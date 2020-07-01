<?php

namespace App\Http\Controllers\Dashboard;


use App\Ecommerce\PriceLists\PriceListRepo;
use App\Events\GalleryUpdatedEvent;
use App\Http\Controllers\Controller;
use App\Photos\Export\GalleryClientsExport;
use App\Photos\Export\GalleryPasswordsExport;
use App\Photos\Galleries\Gallery;
use App\Photos\Galleries\GalleryDashboardPresenter;
use App\Photos\Galleries\GalleryRepo;
use App\Photos\Galleries\GalleryService;
use App\Photos\Galleries\GalleryStatusEnum;
use App\Photos\GalleryClientsImport;
use App\Photos\GroupPhotosGeneration\GroupPhotoGenerator;
use App\Photos\Seasons\SeasonRepo;
use App\Photos\SubGalleries\SubGalleryDashboardPresenter;
use App\Photos\TemplatedPhotosGeneration\IDCardsGenerator;
use App\Processing\Processes\OtherProcesses\ProofPhotosZipGenerationProcess;
use App\Processing\Scenarios\GroupPhotosGenerationScenario;
use App\Processing\Scenarios\InitialGalleryProcessingScenario;
use App\Processing\Scenarios\RemoveGalleryScenario;
use App\Users\UserRepo;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use ImagickException;
use Laracasts\Presenter\Exceptions\PresenterException;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Webmagic\Core\Controllers\AjaxRedirectTrait;
use Webmagic\Dashboard\Components\FormGenerator;
use Webmagic\Dashboard\Components\FormPageGenerator;
use Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable;
use Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined;
use Webmagic\Dashboard\Dashboard;


class GalleryDashboardController extends Controller
{
    use AjaxRedirectTrait;

    /**
     * Show create gallery form
     *
     * @param string                    $galleryName
     * @param int                       $uploadedGalleryOwnerId
     * @param UserRepo                  $userRepo
     * @param PriceListRepo             $priceListRepo
     * @param SeasonRepo                $seasonRepo
     * @param GalleryRepo               $galleryRepo
     * @param GalleryService            $galleryService
     * @param GalleryDashboardPresenter $dashboardPresenter
     *
     * @return FormPageGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     * @throws Exception
     */
    public function create(
        string $galleryName,
        int $uploadedGalleryOwnerId,
        UserRepo $userRepo,
        PriceListRepo $priceListRepo,
        SeasonRepo $seasonRepo,
        GalleryRepo $galleryRepo,
        GalleryService $galleryService,
        GalleryDashboardPresenter $dashboardPresenter
    ) {
        $user = Auth::user();

        if ($user->isAdmin()) {
            $photographers = $userRepo->getAll()->pluck('email', 'id')->toArray();
        } else {
            $photographers = null;
        }

        $uploadedGalleryOwner = $userRepo->getByID($uploadedGalleryOwnerId);
        $price_lists = $priceListRepo->getForSelect('name', 'id');
        //$seasons = $galleryService->prepareArrayOfSeasonsForSelect($seasonRepo, $galleryRepo);
        $seasons = $galleryService->getArrayOfSeasonsWithoutGalleries($seasonRepo, $galleryRepo);

        return $dashboardPresenter->getCreateFormPage(
            $galleryName,
            $uploadedGalleryOwner,
            $user,
            $price_lists,
            $seasons,
            $photographers
        );
    }

    /**
     * Show edit gallery form
     *
     * @param int                       $gallery_id
     * @param UserRepo                  $userRepo
     * @param GalleryRepo               $galleryRepo
     * @param PriceListRepo             $priceListRepo
     * @param SeasonRepo                $seasonRepo
     * @param GalleryDashboardPresenter $dashboardPresenter
     *
     * @return FormPageGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     * @throws Exception
     */
    public function edit(
        int $gallery_id,
        UserRepo $userRepo,
        GalleryRepo $galleryRepo,
        PriceListRepo $priceListRepo,
        SeasonRepo $seasonRepo,
        GalleryDashboardPresenter $dashboardPresenter
    ) {
        if (! $gallery = $galleryRepo->getByID($gallery_id)) {
            abort(404, 'Gallery not found');
        };

        $user = Auth::user();
        $photographers = null;
        if ($user->isAdmin()) {
            $photographers = $userRepo->getAll()->pluck('email', 'id')->toArray();
        }
        $price_lists = $priceListRepo->getForSelect('name', 'id');
        $seasons = $seasonRepo->getForSelectWithSchoolName();

        return $dashboardPresenter->getEditGalleryFormPage($gallery, $user, $price_lists, $seasons, $photographers);
    }

    /**
     * Update gallery info in db and move files on s3 if owner was changed
     *
     * @param                     $galleryId
     * @param Request $request
     * @param GalleryRepo $galleryRepo
     * @param SeasonRepo $seasonRepo
     *
     * @return void
     * @throws Exception
     */
    public function update(
        $galleryId,
        Request $request,
        GalleryRepo $galleryRepo,
        SeasonRepo $seasonRepo
    ) {
        $validData = $request->validate([
            'deadline' => 'required',
            'photographer_id' => 'exists:users,id',
            'price_list_id' => 'exists:price_lists,id',
            'staff_price_list_id' => 'exists:price_lists,id',
            'season_id' => 'exists:seasons,id',
        ]);

        $season = $seasonRepo->getByID($request->get('season_id'));

        /** @var Gallery $gallery */
        $gallery = $gallery = $galleryRepo->getByID($galleryId);
        if (! $gallery) {
            abort(404, 'Gallery not found');
        };

        $updatedGallery = $gallery;

        $updatedGallery->update([
                'user_id' => $validData['photographer_id'],
                'price_list_id' => $validData['price_list_id'],
                'staff_price_list_id' => $validData['staff_price_list_id'],
                'school_id' => $season->school_id,
                'season_id' => $season->id,
                'deadline' => $validData['deadline'],
            ]);

        event(new GalleryUpdatedEvent($updatedGallery, $gallery));
    }

    /**
     * Delete gallery from db and its files from s3
     *
     * @param                $galleryId
     * @param GalleryRepo    $galleryRepo
     *
     * @return JsonResponse|RedirectResponse|Redirector
     * @throws Exception
     */
    public function destroy($galleryId, GalleryRepo $galleryRepo)
    {
        if (! $gallery = $galleryRepo->getByID($galleryId)) {
            abort(404, 'Gallery not found');
        };

        $gallery['status'] = GalleryStatusEnum::DELETING;
        $gallery->save();

        // Start gallery removing scenario
        (new RemoveGalleryScenario($galleryId))->start();

        return $this->redirect(route('dashboard::gallery.index'));
    }

    /**
     * Show gallery page with its sub galleries in table list
     *
     * @param int $gallery_id
     * @param GalleryRepo $galleryRepo
     * @param Dashboard $dashboard
     * @param GalleryDashboardPresenter $dashboardPresenter
     * @param SubGalleryDashboardPresenter $subGalleryDashboardPresenter
     *
     * @param Request $request
     * @return Dashboard
     * @throws AuthorizationException
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     * @throws PresenterException
     * @throws Exception
     * @throws Exception
     * @throws Exception
     * @throws Exception
     */
    public function show(
        int $gallery_id,
        GalleryRepo $galleryRepo,
        Dashboard $dashboard,
        GalleryDashboardPresenter $dashboardPresenter,
        SubGalleryDashboardPresenter $subGalleryDashboardPresenter,
        Request $request
    ) {
        $user = Auth::user();
        $this->authorize('edit', $user);

        /** @var Gallery $gallery */
        $gallery = $gallery = $galleryRepo->getByID($gallery_id);
        if (!$gallery) {
            abort(404, 'Gallery not found');
        };

        $sort = $request->get('sort', 'asc');
        $sortBy = $request->get('sortBy', 'id');

        if(!$gallery->isAvailableForView()){
            $dashboard->page()->addContent($dashboardPresenter->getConvertingStatuses($gallery));
            return $dashboard;
        }

        $dashboardPresenter->getGalleriesDescriptionList($gallery, $dashboard);
        $subGalleries = $gallery->subGalleriesWithTheirRelations()->paginate(config('project.pagination_size'));

        $classrooms = $gallery->getClassroomsList(true);
        $subGalleriesTable = $subGalleryDashboardPresenter->getTable(
            $gallery,
            $subGalleries,
            $classrooms,
            $request,
            $sort,
            $sortBy
        );

        $groupPhotoInProgress = $gallery->isGroupPhotoGenerationInProgress();

        $croppedPhotosListPage = $subGalleryDashboardPresenter->getCroppedPhotosTiles($subGalleries, $classrooms, $request, $gallery_id);

        $proofPhotosTiles = $subGalleryDashboardPresenter->getProofPhotosTiles($subGalleries,$classrooms, $request, $gallery, $groupPhotoInProgress);

        $miniWalletCollagesListPage = $dashboardPresenter->getMiniWalletCollagesTiles($gallery, $groupPhotoInProgress);

        $classPhotosTiles = $dashboardPresenter->prepareClassPhotosTiles($gallery, $groupPhotoInProgress);

        $staffCommonPhotoTiles = $dashboardPresenter->prepareStaffCommonPhotoTiles($gallery, $groupPhotoInProgress);

        $schoolPhotoTiles = $dashboardPresenter->prepareSchoolPhotoTiles($gallery, $groupPhotoInProgress);

        $iDCardsTiles = $dashboardPresenter->prepareIDCardsTiles($gallery, $groupPhotoInProgress);

        return $dashboardPresenter->prepareTabsPage(
            $dashboard,
            $subGalleriesTable,
            $croppedPhotosListPage,
            $miniWalletCollagesListPage,
            $classPhotosTiles,
            $staffCommonPhotoTiles,
            $schoolPhotoTiles,
            $proofPhotosTiles,
            $iDCardsTiles
        );
    }

    /**
     * Show list of all galleries for admin and related to photographer
     *
     * @param GalleryRepo $galleryRepo
     * @param Dashboard $dashboard
     * @param Request $request
     * @param GalleryDashboardPresenter $dashboardPresenter
     *
     * @return \Webmagic\Dashboard\Components\TablePageGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     * @throws Exception
     */
    public function index(Request $request, Dashboard $dashboard, GalleryDashboardPresenter $dashboardPresenter)
    {
        $user = Auth::user();

        $galleries = (new GalleryRepo())->getByFilter(
            $request->get('schools'),
            $request->get('seasons'),
            $request->get('per_page', 10),
            $request->get('page', 1),
            $user->isAdmin() ? null : $user->id
        );

        return $dashboardPresenter->getGalleriesTablePage($galleries, $request, $dashboard);
    }

    /**
     * Prepared array and export subgalleries passwords
     *
     * @param int $gallery_id
     * @param GalleryRepo $galleryRepo
     *
     * @return BinaryFileResponse
     * @throws Exception
     */
    public function downloadPasswords(int $gallery_id, GalleryRepo $galleryRepo)
    {
        if (! $gallery = $galleryRepo->getByID($gallery_id)) {
            abort(404);
        };

        return Excel::download(new GalleryPasswordsExport($gallery), $gallery_id."_password.csv");
    }

    /**
     * Export clients metadata in csv
     *
     * @param int         $gallery_id
     * @param GalleryRepo $galleryRepo
     *
     * @return BinaryFileResponse
     * @throws Exception
     */
    public function exportClients(int $gallery_id, GalleryRepo $galleryRepo)
    {
        if (! $gallery = $galleryRepo->getByID($gallery_id)) {
            abort(404, 'Gallery not found');
        };

        return Excel::download(new GalleryClientsExport($gallery), $gallery_id."_clients_metadata.csv");
    }

    /**
     * Import csv file and update clients data
     *
     * @param int         $gallery_id
     * @param GalleryRepo $galleryRepo
     * @param Request     $request
     *
     * @return RedirectResponse
     * @throws Exception
     */
    public function importClients(int $gallery_id, GalleryRepo $galleryRepo, Request $request)
    {
        $request->validate([
            'file'   => 'required|mimetypes:text/plain,csv',
        ]);

        if (! $gallery = $galleryRepo->getByID($gallery_id)) {
            abort(404, 'Gallery not found');
        };

         Excel::import(new GalleryClientsImport(), request()->file('file'));

        return back();
    }

    /**
     * Get popup with form for choose import file
     *
     * @param int $gallery_id
     * @param GalleryRepo $galleryRepo
     * @param GalleryDashboardPresenter $dashboardPresenter
     *
     * @return FormGenerator
     * @throws FieldUnavailable
     * @throws NoOneFieldsWereDefined
     * @throws Exception
     */
    public function getImportFile(int $gallery_id, GalleryRepo $galleryRepo, GalleryDashboardPresenter $dashboardPresenter)
    {
        if (! $gallery = $galleryRepo->getByID($gallery_id)) {
            abort(404, 'Gallery not found');
        };

        return $dashboardPresenter->getFormForImportFile($gallery);
    }

    public function generationTestPage(Dashboard $dashboard)
    {
        $page = $dashboard->page();
        $page->setPageTitle('Generation functionality testing')
        ->addElement()
            ->box()->boxTitle('Test photo generation')
            ->addElement()->form()
            ->action(route('dashboard::gallery.generation-test'))
            ->addElement()->select()->name('type')
            ->options([
                'common_class_photo' => 'Common class photo generation test',
                'personal_class_photo' => 'Personal class photo generation test',
                'common_school_photo' => 'Common school photo generation test',
                'staff_photo' => 'Staff photo generation test',
                'id_card_landscape' => 'ID card landscape generation test',
                'id_card_portrait' => 'ID card portrait generation test',
            ])
            ->parent('form')->addElement()->button()->type('submit')->content('Test');


        return $dashboard;
    }

    /**
     * @param Request $request
     *
     * @return string|BinaryFileResponse
     * @throws ImagickException
     * @throws PresenterException
     */
    public function generationTest(Request $request)
    {
        $type = $request->get('type');
        $gallery = Gallery::first();

        if ($type == 'common_class_photo') {
            $generator = new GroupPhotoGenerator();

            $classPhotoPath = $generator->commonClassPhotoGenerate($gallery, array_first($gallery->getClassroomsList()));

            return response()->download($classPhotoPath);
        }

        if ($type == 'common_school_photo') {
            $generator = new GroupPhotoGenerator();

            $classPhotoPath = $generator->schoolPhotoGenerate($gallery);

            return response()->download($classPhotoPath);
        }

        if ($type == 'staff_photo') {
            $generator = new GroupPhotoGenerator();

            $classPhotoPath = $generator->staffPhotoGenerate($gallery);

            return response()->download($classPhotoPath);
        }

        if ($type == 'personal_class_photo') {
            $generator = new GroupPhotoGenerator();

            $personalClassPhotoPath = $generator->personalClassPhotoGenerate(
                $gallery,
                $gallery->subgalleries->first()->client
            );

            return response()->download($personalClassPhotoPath);
        }

        if ($type == 'id_card_landscape') {
            $generator = new IDCardsGenerator($gallery->subgalleries->first());

            $idCardLandscapePhotoPath = $generator->getLandscapeImage();

            return response()->download($idCardLandscapePhotoPath);
        }

        if ($type == 'id_card_portrait') {
            $generator = new IDCardsGenerator($gallery->subgalleries->first());

            $idCardLandscapePhotoPath = $generator->getPortraitImage();

            return response()->download($idCardLandscapePhotoPath);
        }

        return 'Action not set';
    }

    /**
     * Start gallery group photos generation process
     *
     * @param                           $gallery_id
     * @param GalleryDashboardPresenter $presenter
     *
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws Exception
     */
    public function groupPhotoGenerationStart($gallery_id)
    {
        /** @var Gallery $gallery */
        if(!$gallery = (new GalleryRepo())->getByID($gallery_id)){
            abort(404);
        }

        $gallery->groupPhotoGenerationStart();

        return $this->redirect(route('dashboard::gallery.show', $gallery->id));
    }

    /**
     * Return gallery group photo generation process button
     *
     * @param GalleryDashboardPresenter $presenter
     *
     * @param $gallery_id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Webmagic\Dashboard\Elements\Buttons\DefaultButton
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function getStatusBtn(GalleryDashboardPresenter $presenter,  $gallery_id)
    {
        if(!$gallery = (new GalleryRepo())->getByID($gallery_id)){
            abort(404);
        }

        if($gallery->isGroupPhotoGenerationInProgress()){
            return $presenter->getGroupPhotoGenerationButton($gallery);
        }

        return $this->redirect(route('dashboard::gallery.show', $gallery->id))->header('X-Update-Action', 'update-stop');
    }

    /**
     * Return processes by gallery with statuses
     *
     * @param \App\Photos\Galleries\GalleryDashboardPresenter $presenter
     * @param $gallery_id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     * @throws \Exception
     */
    public function getConvertingStatuses(GalleryDashboardPresenter $presenter,  $gallery_id)
    {
        if(!$gallery = (new GalleryRepo())->getByID($gallery_id)){
            abort(404);
        }

        if(!$gallery->isAvailableForView()){
            return $presenter->getConvertingStatuses($gallery);
        }

        return $this->redirect(route('dashboard::gallery.show', $gallery->id));
    }

    /**
     * Return group photos processes by gallery with statuses
     *
     * @param \App\Photos\Galleries\GalleryDashboardPresenter $presenter
     * @param $gallery_id
     * @return mixed
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     * @throws \Exception
     */
    public function getGroupPhotosConvertingStatuses(GalleryDashboardPresenter $presenter,  $gallery_id)
    {
        if(!$gallery = (new GalleryRepo())->getByID($gallery_id)){
            abort(404);
        }

        if($gallery->isGroupPhotoGenerationInProgress()){
            return $presenter->getGroupPhotosConvertingStatuses($gallery);
        }
    }

    /**
     * Get status block for index gallery page
     *
     * @param \App\Photos\Galleries\GalleryDashboardPresenter $presenter
     * @param int $gallery_id
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|string
     * @throws \Exception
     */
    public function getConvertingStatus(GalleryDashboardPresenter $presenter, int $gallery_id)
    {
        if( !$gallery = (new GalleryRepo())->getByID($gallery_id)){
            return $this->redirect(route('dashboard::gallery.index'));
        }

        return $presenter->getGalleryShortStatusBlock($gallery);
    }

    /**
     * @param int $orderId
     * @param GalleryRepo $galleryRepo
     *
     * @return mixed|JsActionsApplicable|LinkButton
     * @throws NoOneFieldsWereDefined
     * @throws PresenterException
     * @throws Exception
     */
    public function proofPhotosExportZipPreparingStatus(int $orderId, GalleryRepo $galleryRepo)
    {
        /** @var Gallery $gallery */
        $gallery = $galleryRepo->getByID($orderId);

        if(!$gallery) {
            abort(404);
        }

        return $gallery->dashboardElements()->proofPhotosZipProcessingButton();
    }

    /**
     * @param int $galleryId
     * @param GalleryRepo $galleryRepo
     *
     * @return mixed|JsActionsApplicable|LinkButton
     * @throws NoOneFieldsWereDefined
     * @throws PresenterException
     * @throws Exception
     */
    public function proofPhotosExportZipPreparingStart(int $galleryId, GalleryRepo $galleryRepo)
    {
        /** @var Gallery $gallery */
        $gallery = $galleryRepo->getByID($galleryId);

        if (!$gallery) {
            abort(404);
        }

        (new ProofPhotosZipGenerationProcess($galleryId))->start();

        return $gallery->dashboardElements()->proofPhotosZipProcessingButton();
    }

    /**
     * Download zip with proofing photos for gallery
     *
     * @param int $gallery_id
     * @param GalleryRepo $galleryRepo
     *
     * @return JsonResponse|RedirectResponse|Redirector
     * @throws Exception
     */
    public function downloadProofingPhotos(int $gallery_id, GalleryRepo $galleryRepo)
    {
        /** @var Gallery $gallery */
        $gallery = $gallery = $galleryRepo->getByID($gallery_id);
        if (!$gallery) {
            abort(404, 'Gallery not found');
        };

        if($gallery->isProofPhotosZipReady()){
            return response()->download($gallery->present()->proofExportFullPath())->deleteFileAfterSend(true);
        }

        return redirect()->back();
    }

    public function manualContinueInitialGalleryProcessingScenario(int $gallery_id, GalleryRepo $galleryRepo)
    {
        if (!$gallery = $gallery = $galleryRepo->getByID($gallery_id)) {
            abort(404, 'Gallery not found');
        };

        (new InitialGalleryProcessingScenario($gallery->id, $gallery->user->ftp_login, $gallery->user->id))->continue();

        return redirect()->back();
    }

    public function manualContinueGroupGalleryProcessingScenario(int $gallery_id, GalleryRepo $galleryRepo)
    {
        if (!$gallery = $gallery = $galleryRepo->getByID($gallery_id)) {
            abort(404, 'Gallery not found');
        };

        (new GroupPhotosGenerationScenario($gallery_id))->continue();

        return redirect()->back();
    }

}
