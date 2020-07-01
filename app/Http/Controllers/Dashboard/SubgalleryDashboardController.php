<?php

namespace App\Http\Controllers\Dashboard;

use App\Events\ClassPhotoAvailabilityUpdatedEvent;
use App\Events\CroppedFaceUpdatedEvent;
use App\Events\PersonDataUpdatedEvent;
use App\Events\SchoolCommonPhotoAvailabilityUpdatedEvent;
use App\Events\SubGalleryAndPersonDeletedEvent;
use App\Events\SubGalleryMovedEvent;
use App\Http\Controllers\Controller;
use App\Photos\Galleries\Gallery;
use App\Photos\Galleries\GalleryRepo;
use App\Photos\People\PersonRepo;
use App\Photos\People\PersonService;
use App\Photos\Photos\PhotoRepo;
use App\Photos\Photos\PhotoStorageManager;
use App\Photos\SubGalleries\SubGallery;
use App\Photos\SubGalleries\SubGalleryDashboardPresenter;
use App\Photos\SubGalleries\SubGalleryRepo;
use App\Photos\SubGalleries\SubGalleryService;
use App\Processing\Processes\OtherProcesses\ProofPhotoUpdatingProcess;
use App\Processing\Processes\RemovingProcessing\RemoveSubGalleryProcess;
use App\Processing\Scenarios\AddPhotoToSubgalleryScenario;
use App\Utils;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Contracts\Routing\ResponseFactory;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Laracasts\Presenter\Exceptions\PresenterException;
use Symfony\Component\HttpFoundation\Response;
use Webmagic\Core\Controllers\AjaxRedirectTrait;
use Webmagic\Core\Entity\Exceptions\EntityNotExtendsModelException;
use Webmagic\Core\Entity\Exceptions\ModelNotDefinedException;
use Webmagic\Dashboard\Components\FormGenerator;
use Webmagic\Dashboard\Components\FormPageGenerator;
use Webmagic\Dashboard\Components\TableGenerator;
use Webmagic\Dashboard\Components\TilesListGenerator;
use Webmagic\Dashboard\Components\TilesListPageGenerator;
use Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable;
use Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined;
use Webmagic\Dashboard\Dashboard;


class   SubgalleryDashboardController extends Controller
{
    use AjaxRedirectTrait;

    /**
     * Show sub gallery page with all files from s3
     *
     * @param                              $subGalleryId
     * @param SubGalleryRepo $subGalleryRepo
     * @param Dashboard $dashboard
     * @param SubGalleryDashboardPresenter $dashboardPresenter
     *
     * @return Dashboard
     * @throws AuthorizationException
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     * @throws PresenterException
     */
    public function show(
        $subGalleryId,
        SubGalleryRepo $subGalleryRepo,
        Dashboard $dashboard,
        SubGalleryDashboardPresenter $dashboardPresenter
    ) {
        $user = Auth::user();
        $this->authorize('edit', $user);

        /** @var SubGallery $subGallery */
        $subGallery = $subGallery = $subGalleryRepo->getByID($subGalleryId);
        if (!$subGallery) {
            abort(404, 'Subgallery not found');
        };

        $page = $dashboardPresenter->getDescriptionList($subGallery, $dashboard);

        if($subGallery->isSubGalleryPhotoGenerationInProgress()){
            $dashboard->page()->addContent($dashboardPresenter->getPhotoUpdatingStatuses($subGallery));
            return $dashboard;
        }
        return $dashboardPresenter->getImageComponents($subGallery, $page);
    }

    /**
     * Create form for sub gallery
     *
     * @param $galleryId
     * @param GalleryRepo $galleryRepo
     * @param SubGalleryDashboardPresenter $dashboardPresenter
     *
     * @return FormPageGenerator
     *
     * @throws AuthorizationException
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     */
    public function create($galleryId, GalleryRepo $galleryRepo, SubGalleryDashboardPresenter $dashboardPresenter)
    {
        $user = Auth::user();
        $this->authorize('edit', $user);

        if(! $gallery = $galleryRepo->getByID($galleryId)){
            abort(404, 'Gallery not found');
        }

        return $dashboardPresenter->getFormForCreateSubGallery($gallery);
    }

    /**
     * Store data of person & sub gallery
     *
     * @param $galleryId
     * @param Request $request
     * @param GalleryRepo $galleryRepo
     * @param PersonRepo $personRepo
     * @throws AuthorizationException
     */
    public function store(
        $galleryId,
        Request $request,
        GalleryRepo $galleryRepo,
        PersonRepo $personRepo,
        SubGalleryRepo $subGalleryRepo,
        SubGalleryService $subGalleryService
    ) {
        $user = Auth::user();
        $this->authorize('edit', $user);

        if(! $gallery = $galleryRepo->getByID($galleryId)){
            abort(404, 'Gallery not found');
        }

        $data = $request->validate([
            'image'                         => 'required|image',
            'first_name'                    => 'required|string',
            'last_name'                     => 'required|string',
            'classroom'                     => 'required|string',
            'graduate'                      => 'nullable|boolean',
            'teacher'                       => 'nullable|boolean',
            'title'                         => 'nullable|string',
            'available_on_class_photo'      => 'nullable|boolean',
            'available_on_general_photo'    => 'nullable|boolean',
        ]);

        $subGallery = $subGalleryRepo->create([
            'name'                          => $data['first_name'],
            'password'                      => Utils::generateSimpleNumberPassword(),
            'available_on_class_photo'      => data_get($data, 'available_on_class_photo', false),
            'available_on_general_photo'    => data_get($data, 'available_on_general_photo', false),
            'gallery_id'                    => $galleryId,
        ]);

        $personRepo->create([
            'first_name'        => $data['first_name'],
            'last_name'         => $data['last_name'],
            'classroom'         => $data['classroom'],
            'graduate'          => data_get($data, 'graduate', false),
            'teacher'           => data_get($data, 'teacher', false),
            'title'             => $data['title'],
            'gallery_id'        => $galleryId,
            'sub_gallery_id'    => $subGallery->id,
            'school_name'       => $gallery->season->school->name
        ]);

        $subGalleryService->createAndAttachOriginalPhoto($data['image'], $subGallery, true);

        (new AddPhotoToSubgalleryScenario($subGallery->id))->start();

        return $this->redirect(route('dashboard::gallery.show', $subGallery->gallery_id));
    }

    /**
     * Delete subgallery from db and its files from s3
     *
     * @param $subGalleryId
     * @param SubGalleryRepo $subGalleryRepo
     *
     * @throws AuthorizationException
     * @throws EntityNotExtendsModelException
     * @throws ModelNotDefinedException
     */
    public function delete($subGalleryId, SubGalleryRepo $subGalleryRepo)
    {
        $user = Auth::user();
        $this->authorize('edit', $user);

        if (! $subGallery = $subGalleryRepo->getByID($subGalleryId)) {
            abort(404, 'Sub gallery not found');
        };

        // Start sub gallery deleting process
        (new RemoveSubGalleryProcess($subGalleryId))->start();

        event(new SubGalleryAndPersonDeletedEvent($subGallery, $subGallery->person));

        $subGalleryRepo->destroy($subGalleryId);
    }

    /**
     * Form for manually adding photo
     *
     * @param $subGalleryId
     * @param SubGalleryRepo $subGalleryRepo
     * @param SubGalleryDashboardPresenter $subGalleryDashboardPresenter
     * @return FormPageGenerator
     * @throws AuthorizationException
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     */
    public function addPhoto(
        $subGalleryId,
        SubGalleryRepo $subGalleryRepo,
        SubGalleryDashboardPresenter $subGalleryDashboardPresenter
    ) {
        $user = Auth::user();
        $this->authorize('edit', $user);

        if (! $subGallery = $subGalleryRepo->getByID($subGalleryId)) {
            abort(404, 'Sub gallery not found');
        };

        return $subGalleryDashboardPresenter->getFormForAddPhoto($subGallery);
    }

    /**
     * Store manually added photo
     *
     * @param $subGalleryId
     * @param Request $request
     * @param SubGalleryRepo $subGalleryRepo
     * @param \App\Photos\SubGalleries\SubGalleryService $subGalleryService
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function storePhoto(
        $subGalleryId,
        Request $request,
        SubGalleryRepo $subGalleryRepo,
        SubGalleryService $subGalleryService
    ) {
        $user = Auth::user();
        $this->authorize('edit', $user);

        if (! $subGallery = $subGalleryRepo->getByID($subGalleryId)) {
            abort(404, 'Sub gallery not found');
        };

        $data = $request->validate([
            'photo' => 'required|image'
        ]);

        $subGalleryService->createAndAttachOriginalPhoto($data['photo'], $subGallery);

        (new AddPhotoToSubgalleryScenario($subGallery->id))->start();
    }

    /**
     * Get status about adding photo to subgallery
     *
     * @param \App\Photos\SubGalleries\SubGalleryDashboardPresenter $presenter
     * @param $subGalleryId
     * @param \App\Photos\SubGalleries\SubGalleryRepo $galleryRepo
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function getSubgalleryPhotosConvertingStatuses(
        SubGalleryDashboardPresenter $presenter,
        $subGalleryId,
        SubGalleryRepo $galleryRepo
    ) {
        if (! $subGallery = $galleryRepo->getByID($subGalleryId)) {
            abort(404);
        }

        if ($subGallery->isSubGalleryPhotoGenerationInProgress()) {
            return $presenter->getPhotoUpdatingStatuses($subGallery);
        }

        return $this->redirect(route('dashboard::gallery.subgallery.show', $subGallery->id));
    }

    /**
     * Delete sub gallery photo from s3 (preview and original)
     * update sub gallery preview image if it was delete
     *
     * @param                     $photoId
     * @param PhotoRepo           $photoRepo
     *
     * @throws AuthorizationException
     * @throws EntityNotExtendsModelException
     * @throws ModelNotDefinedException
     * @throws PresenterException
     */
    public function deletePhotoFromSubgallery($photoId, PhotoRepo $photoRepo)
    {
        $user = Auth::user();
        $this->authorize('edit', $user);

        if (! $photo = $photoRepo->getByID($photoId)) {
            abort(404);
        };

        $photoRepo->deletePhoto($photoId);
    }

    /**
     * Get form in popup for moving subgallery from one to another owners gallery or
     * admin can choose any gallery and change owner
     *
     * @param $subGalleryId
     * @param SubGalleryRepo $subGalleryRepo
     * @param GalleryRepo $galleryRepo
     * @param SubGalleryDashboardPresenter $dashboardPresenter
     *
     * @return FormGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     */
    public function getPopupForMovingSubgallery(
        $subGalleryId,
        SubGalleryRepo $subGalleryRepo,
        GalleryRepo $galleryRepo,
        SubGalleryDashboardPresenter $dashboardPresenter
    ) {
        $user = Auth::user();

        if (! $subGallery = $subGalleryRepo->getByID($subGalleryId)) {
            abort(404);
        };

        $galleries = [];
        if ($user->isAdmin()) {
            $all_galleries = $galleryRepo->getAll();
            foreach ($all_galleries as $gallery) {
                if ($gallery->id != $subGallery->gallery_id) {
                    $galleries[$gallery->id] = $gallery->present()->name;
                }
            }
        } else {
            foreach ($galleries as $id => $gallery) {
                if ($gallery->id != $subGallery->gallery_id) {
                    $galleries[$gallery->id] = $gallery->present()->name;
                }
            }
        }

        return $dashboardPresenter->getFormForMoveSubgallery($subGallery, $galleries);
    }

    /**
     * Move sub gallery to another gallery
     * Update relations in db and move files on s3
     *
     * @param $subGallery_id
     * @param Request $request
     * @param SubGalleryRepo $subGalleryRepo
     * @param GalleryRepo $galleryRepo
     * @param SubGalleryService $subGalleryService
     *
     * @return JsonResponse|RedirectResponse|Redirector
     * @throws Exception
     */
    public function move(
        $subGallery_id,
        Request $request,
        SubGalleryRepo $subGalleryRepo,
        GalleryRepo $galleryRepo
    ) {
        if (! $subGallery = $subGalleryRepo->getByID($subGallery_id)) {
            abort(404, 'Subgallery not found');
        };

        if (! $newGallery = $galleryRepo->getByID($request->get('gallery_id'))) {
            abort(404, 'Gallery not found');
        };

        $old_gallery_id = $subGallery->gallery_id;

        event(new SubGalleryMovedEvent($subGallery, $old_gallery_id));

        $subGallery->update(['gallery_id' => $newGallery->id]);

        return $this->redirect(route('dashboard::gallery.show', $old_gallery_id));
    }

    /**
     * Download proofing photos from s3
     *
     * @param int            $subgallery_id
     * @param SubGalleryRepo $subgalleryRepo
     *
     * @return mixed
     * @throws Exception
     */
    public function downloadProofingPhotos(int $subgallery_id, SubGalleryRepo $subgalleryRepo)
    {
        if (! $subgallery = $subgalleryRepo->getByID($subgallery_id)) {
            abort(404);
        };
        if(isset($subgallery->client->proof_photo_path)){
            return  Storage::disk('s3')->download($subgallery->client->proof_photo_path);
        }

        return $this->redirect(route('dashboard::gallery.subgallery.show', $subgallery_id));
    }

    /**
     * Show form in popup for edit client info
     *
     * @param int                          $client_id
     * @param PersonRepo                   $clientRepo
     *
     * @return FormGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     */
    public function editClient(int $client_id, PersonRepo $clientRepo)
    {
        if (! $client = $clientRepo->getByID($client_id)) {
            abort(404, 'Client no found');
        };

        $classrooms = $client->subgallery->gallery->getClassroomsList(true);

        return view('dashboard.client_edit_form', compact('classrooms', 'client'));
    }

    /**
     * Update person info
     *
     * @param int $person_id
     * @param PersonRepo $personRepo
     * @param Request $request
     *
     * @param \App\Photos\People\PersonService $personService
     * @return void
     * @throws \Exception
     */
    public function updateClient(int $person_id, PersonRepo $personRepo, Request $request, PersonService $personService)
    {
        if (! $person = $personRepo->getByID($person_id)) {
            abort(404, 'Client no found');
        };

        $updatedPerson = $person;

        if(!$updatedPerson->update([
            'first_name' => $request->get('first_name'),
            'last_name' => $request->get('last_name'),
            'classroom' => $request->get('classroom'),
            'school_name' => $request->get('school_name'),
            'title' =>  $request->get('teacher') ? $request->get('title')  ?: null : null,
            'graduate' => $request->get('graduate', false),
            'teacher' => $request->get('teacher', false),
        ])){
            abort(500, 'Error on client updating');
        };

        $personService->updateAdditionalClassrooms($request->get('add_classrooms', []), $person);

        event(new PersonDataUpdatedEvent($updatedPerson, $person));
    }

    /**
     * @param int $person_id
     * @param \App\Photos\People\PersonRepo $personRepo
     * @param \Illuminate\Http\Request $request
     * @throws \Exception
     */
    public function updatePositionForTeacher(int $person_id, PersonRepo $personRepo, Request $request)
    {
        if (! $person = $personRepo->getByID($person_id)) {
            abort(404);
        };

        if($person->isStaff()){
            $personRepo->update($person_id, ['position' => $request->get('position')]);
        }
    }


    /**
     * Update availability for class & general photo
     *
     * @param $subGallery_id
     * @param Request $request
     * @param SubGalleryRepo $subGalleryRepo
     * @return ResponseFactory|Response
     * @throws Exception
     */
    public function updateGroupPhotoAvailability($subGallery_id, Request $request, SubGalleryRepo $subGalleryRepo)
    {
        if(! $subGallery = $subGalleryRepo->getByID($subGallery_id)){
            abort(404, 'Sub gallery not found!');
        }

        $data = [
          'available_on_class_photo' => $request->get('available_on_class_photo', $subGallery['available_on_class_photo']),
          'available_on_general_photo' => $request->get('available_on_general_photo', $subGallery['available_on_general_photo'])
        ];

        if(! $subGalleryRepo->update($subGallery['id'], $data)){
            abort(500, 'Can not update!');
        }

        if($request->has('available_on_general_photo')){
            event(new SchoolCommonPhotoAvailabilityUpdatedEvent($subGallery->person));
        }
        elseif ($request->has('available_on_class_photo')){
            event(new ClassPhotoAvailabilityUpdatedEvent($subGallery->person));
        }

        return response('Updated', 200);
    }

    /**
     * @param int $person_id
     * @param \App\Photos\People\PersonRepo $personRepo
     * @param \Illuminate\Http\Request $request
     * @throws \Exception
     */
    public function updateAllClassPhotosForClient(int $person_id, PersonRepo $personRepo, Request $request)
    {
        if (! $person = $personRepo->getByID($person_id)) {
            abort(404);
        };

        if (! $personRepo->update($person->id, [
            'all_class_photos' => $request->get('all_class_photos', false),
        ])) {
            abort(500, 'Error on client updating');
        };

        $updatedPerson = $personRepo->getByID($person_id);

        event(new PersonDataUpdatedEvent($updatedPerson, $person));
    }

    /**
     * @param int $subgallery_id
     * @param \App\Photos\SubGalleries\SubGalleryRepo $subgalleryRepo
     * @param \App\Photos\SubGalleries\SubGalleryDashboardPresenter $dashboardPresenter
     * @return \App\Photos\SubGalleries\SubGalleryDashboardPresenter
     * @throws \Illuminate\Auth\Access\AuthorizationException
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function getPopupForChangingProofPhoto(
        int $subgallery_id,
        SubGalleryRepo $subgalleryRepo,
        SubGalleryDashboardPresenter $dashboardPresenter
    ) {
        /** @var SubGallery $subGallery */
        $subGallery = $subgalleryRepo->getByID($subgallery_id);
        if (! $subGallery) {
            abort(404, 'Sub gallery not found');
        }

        $photos = $subGallery->photos;

        foreach ($photos as $key => $photo) {
            $imageData[] = [
                'id' => $photo->id,
                'image' => $photo->present()->previewUrl(),
            ];
        }

        return $dashboardPresenter->getFormForUpdateProofPhoto($subGallery, $imageData);
    }

    /**
     * Start updating proof photo process
     *
     * @param int $subgallery_id
     * @param \Illuminate\Http\Request $request
     * @param \App\Photos\SubGalleries\SubGalleryRepo $subgalleryRepo
     * @param \App\Photos\Photos\PhotoRepo $photoRepo
     * @param \App\Photos\SubGalleries\SubGalleryDashboardPresenter $dashboardPresenter
     * @return mixed
     * @throws \Illuminate\Auth\Access\AuthorizationException
     */
    public function updateProofPhoto(
        int $subgallery_id,
        Request $request,
        SubGalleryRepo $subgalleryRepo,
        PhotoRepo $photoRepo,
        SubGalleryDashboardPresenter $dashboardPresenter
    ) {
        /** @var SubGallery $subGallery */
        if (! $subGallery = $subgalleryRepo->getByID($subgallery_id)) {
            abort(404);
        }

        if (! $photo = $photoRepo->getByID($request->get('photo_id'))) {
            abort(404);
        }
        (new ProofPhotoUpdatingProcess($subGallery->id, $photo->present()->previewUrl))->start();

        return $dashboardPresenter->proofPhotoUpdatingProcessingBox($subGallery);
    }

    /**
     * Get status of proof updating photo
     *
     * @param int $subgallery_id
     * @param \App\Photos\SubGalleries\SubGalleryRepo $subgalleryRepo
     * @param \App\Photos\SubGalleries\SubGalleryDashboardPresenter $dashboardPresenter
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function proofPhotoUpdatingStatus(
        int $subgallery_id,
        SubGalleryRepo $subgalleryRepo,
        SubGalleryDashboardPresenter $dashboardPresenter
    ) {
        if (! $subGallery = $subgalleryRepo->getByID($subgallery_id)) {
            abort(404);
        }
        if ($subGallery->isProofPhotosUpdatingInProgress()) {
            return response('', 200)
                ->header('X-Update-Action', 'update-false');
        }

        return response($dashboardPresenter->getProofPhotoComponent($subGallery))->header('X-Update-Action', 'update-stop');
    }

    /**
     * @param int                          $subgallery_id
     * @param SubGalleryRepo               $subgalleryRepo
     * @param SubGalleryDashboardPresenter $dashboardPresenter
     *
     * @return FormGenerator
     * @throws AuthorizationException
     * @throws PresenterException
     */
    public function getPopupForChangingCroppedFaceInfo(
        int $subgallery_id,
        SubGalleryRepo $subgalleryRepo,
        SubGalleryDashboardPresenter $dashboardPresenter
    ) {
        /** @var SubGallery $subGallery */
        $subGallery = $subgalleryRepo->getByID($subgallery_id);
        if (! $subGallery) {
            abort(404, 'Sub gallery not found');
        }

        return $dashboardPresenter->getFormForUpdateCroppedInfo($subGallery);
    }

    /**
     * @param int            $subGalleryId
     * @param Request        $request
     * @param SubGalleryRepo $subgalleryRepo
     *
     * @return mixed
     * @throws AuthorizationException
     * @throws PresenterException
     * @throws FileNotFoundException
     */
    public function updateCroppedFaceInfo(int $subGalleryId, Request $request, SubGalleryRepo $subgalleryRepo)
    {
        /** @var SubGallery $subGallery */
        $subGallery = $subgallery = $subgalleryRepo->getByID($subGalleryId);
        if (!$subGallery) {
            abort(404, 'Sub gallery not found');
        }

        $validData = $request->validate([
            'crop_x' => 'required|integer',
            'crop_y' => 'required|integer',
            'crop_original_width' => 'required|integer',
            'crop_original_height' => 'required|integer',
            'cropped_image' => 'required'
        ]);

        // Update data
        $currentPhoto = $subGallery->person->croppedPhoto();
        if($request->exists('cropped_image')){
            (new PhotoStorageManager())->updatePhotoFile($currentPhoto, $validData['cropped_image']);
            (new PhotoRepo())->update($currentPhoto->id, array_except($validData, ['cropped_image']));
        }

        event(new CroppedFaceUpdatedEvent($subGallery->person, $currentPhoto));

        return $subGallery->person->present()->croppedPhotoUrl().'?v='.uniqid();
    }

    /**
     * Delete file from s3 and path from subgallery
     *
     * @param int            $subGalleryId
     * @param SubGalleryRepo $subgalleryRepo
     *
     * @return JsonResponse|RedirectResponse|Redirector
     * @throws AuthorizationException
     */
    public function deleteCroppedPhoto(int $subGalleryId, SubGalleryRepo $subgalleryRepo)
    {
        if (! $subGallery = $subgalleryRepo->getByID($subGalleryId)) {
            abort(404, 'Sub gallery not found');
        }

        (new PersonStorageManager())->deleteRemoteCroppedPhoto($subGallery->person);

        return $this->redirect(route('dashboard::gallery.show', $subGallery->gallery_id));
    }

    /**
     * Filter subgallery by school and gallery id
     * return tiles for cropped photos or table
     *
     * @param                                                        $gallery_id
     * @param SubGalleryRepo                                         $subgalleryRepo
     * @param GalleryRepo                                            $galleryRepo
     * @param SubGalleryDashboardPresenter                           $dashboardPresenter
     * @param Request                                                $request
     * @param string|null                                            $return_type
     *
     * @return TableGenerator|TilesListGenerator|TilesListPageGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     * @throws Exception
     */
    public function filter(
        $gallery_id,
        SubGalleryRepo $subgalleryRepo,
        GalleryRepo $galleryRepo,
        SubGalleryDashboardPresenter $dashboardPresenter,
        Request $request
    ) {
        /** @var Gallery $gallery */
        $gallery = $galleryRepo->getByID($gallery_id);
        if (! $gallery) {
            abort(404, 'Gallery not found');
        };
;
        $classrooms = $request->get('classrooms', null);
        $onlyStaff = $request->get('staff', false);

        $sort = $request->get('sort', 'asc');
        $sortBy = $request->get('sortBy', 'id');

        if ($classrooms || $onlyStaff) {
            $subgalleries = $subgalleryRepo->getByFilter($classrooms, $onlyStaff, $gallery_id, config('project.pagination_size'), $sort, $sortBy);
        } else {
            $subgalleries = $subgalleryRepo->getByGalleryId($gallery_id, config('project.pagination_size'), $sort, $sortBy);
        }

        // Tiles for proof photos
        if($request->get('proofPhotos')){
            return $dashboardPresenter->getProofPhotosTiles($subgalleries, $gallery->getClassroomsList(true),$request, $gallery);
        }

        // Tiles for cropping
        if($request->get('croppedPhotos')){
            return $dashboardPresenter->getCroppedPhotosTiles($subgalleries, $gallery->getClassroomsList(true), $request, $gallery_id);
        }

        $classrooms = $gallery->getClassroomsList(true);


        return $dashboardPresenter->getTable(
            $gallery,
            $subgalleries,
            $classrooms,
            $request,
            $sort,
            $sortBy
        );
    }
}
