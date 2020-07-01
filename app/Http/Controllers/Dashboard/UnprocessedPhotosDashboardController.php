<?php

namespace App\Http\Controllers\Dashboard;

use App\Core\StorageManager;
use App\Http\Controllers\Controller;
use App\Jobs\DeleteUnprocessedGallery;
use App\Jobs\MarkDirectoryAsUploading;
use App\Jobs\UpdatePermissionsOnStorage;
use App\Photos\Galleries\GalleryDashboardPresenter;
use App\Photos\Galleries\GalleryRepo;
use App\Photos\Galleries\GalleryStatusEnum;
use App\Photos\Seasons\Season;
use App\Photos\Seasons\SeasonRepo;
use App\Processing\Scenarios\InitialGalleryProcessingScenario;
use App\Users\PhotographerService;
use App\Users\User;
use App\Users\UserRepo;
use App\Users\UserStorageManger;
use App\Utils;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Redirector;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Webmagic\Core\Controllers\AjaxRedirectTrait;
use Webmagic\Dashboard\Dashboard;

class UnprocessedPhotosDashboardController extends Controller
{
    use AjaxRedirectTrait;

    /**
     * Delete unprocessed gallery
     *
     * @param string   $gallery_name
     * @param int      $user_id
     * @param UserRepo $userRepo
     *
     * @return JsonResponse|RedirectResponse|Redirector
     * @throws Exception
     */
    public function destroy(string $gallery_name, int $user_id, UserRepo $userRepo)
    {
        if (! $user = $userRepo->getByID($user_id)) {
            abort(404);
        };

        $currentPath = $user->ftp_login . '/' . $gallery_name;
        $storageManager = new StorageManager();
        $exists = $storageManager->isFtpUploadsDirExists($currentPath);

        if ($exists){
            DeleteUnprocessedGallery::dispatch($currentPath)->onQueue('delete_unprocessed_gallery');
        }  else{
            abort(404);
        }

        return $this->redirect(route('dashboard::unprocessed-photos.index'));
    }


    /**
     * Create gallery and add job for moving photos from upload to local storage tmp
     *
     * @param Request     $request
     * @param UserRepo    $userRepo
     * @param GalleryRepo $galleryRepo
     *
     * @param SeasonRepo  $seasonRepo
     *
     * @return JsonResponse|RedirectResponse|Redirector
     * @throws Exception
     */
    public function convert(Request $request, UserRepo $userRepo, GalleryRepo $galleryRepo, SeasonRepo $seasonRepo, UserStorageManger $storageManger)
    {
        $validData = $request->validate([
            'ftp_path' => 'required|string',
            'photographer_id' => 'required|exists:users,id',
            'ftp_user_id' => 'required|exists:users,id',
            'price_list_id' => 'required|exists:price_lists,id',
            'staff_price_list_id' => 'required|exists:price_lists,id',
            'season_id' => 'required|exists:seasons,id',
            'deadline' => 'required',
        ]);

        // Check if gallery path exit
        /** @var User $uploadedGalleryOwner */
        $uploadedGalleryOwner = $userRepo->getByID($request->get('ftp_user_id'));

        if(!$storageManger->isDirectoryUploaded($uploadedGalleryOwner, $validData['ftp_path'])){
            abort(500, "Gallery directory doesn't exist");
        }

        $photographer = $userRepo->getByID($request->get('photographer_id'));

        // Prepare gallery
        /** @var Season $season */
        $season = $seasonRepo->getByID($validData['season_id']);
        $gallery = $season->gallery;
        if(is_null($gallery)) {
            $gallery = $galleryRepo->create([
                'user_id' => $photographer->id,
                'status' => GalleryStatusEnum::PREPARING,
                'password' => Utils::generateSimpleNumberPassword(),
                'price_list_id' => $validData['price_list_id'],
                'staff_price_list_id' => $validData['staff_price_list_id'],
                'school_id' => $season->school_id,
                'season_id' => $season->id,
                'deadline' => $validData['deadline'],
            ]);
        } else {
            //todo update with new galleries status getting functionality
            $galleryRepo->update($gallery->id, [
                'status' => GalleryStatusEnum::PREPARING
            ]);
        }

        // Start gallery initial processing scenario
        MarkDirectoryAsUploading::dispatch($uploadedGalleryOwner, $validData['ftp_path'])->onQueue('mark_directory');;
        (new InitialGalleryProcessingScenario($gallery->id, $validData['ftp_path'], $uploadedGalleryOwner->id))->start();

        return $this->redirect(route('dashboard::gallery.index'));
    }

    /**
     * Show list of unprocessed photos that locate in uploads ftp users
     *
     * @param PhotographerService       $photographerService
     * @param Dashboard                 $dashboard
     * @param GalleryDashboardPresenter $dashboardPresenter
     *
     * @return Dashboard
     * @throws Exception
     */
    public function index(
        PhotographerService $photographerService,
        Dashboard $dashboard,
        GalleryDashboardPresenter $dashboardPresenter
    ) {
        $user = Auth::user();
        if ($user->isAdmin()) {
            $files = $photographerService->getAllUnprocessedGalleries();
        } else {
            $files = $photographerService->getUnprocessedGallery($user);
        }

        return $dashboardPresenter->getUnprocessedPhotosTablePage($files, $dashboard);
    }

    /**
     * Get popup for checking dir structures before gallery converting
     *
     * @param string $directory_name
     * @param int $user_id
     * @param \App\Users\UserRepo $userRepo
     * @param \App\Photos\Galleries\GalleryDashboardPresenter $dashboardPresenter
     * @return \Webmagic\Dashboard\Elements\Boxes\Box
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function getPopupForStartingConvertingProcess(
        string $directory_name,
        int $user_id,
        UserRepo $userRepo,
        GalleryDashboardPresenter $dashboardPresenter
    ) {
        if (! $photographer = $userRepo->getByID($user_id)) {
            abort(404);
        }

        UpdatePermissionsOnStorage::dispatch($photographer->ftp_login)->onQueue('set_permissions');

        return $dashboardPresenter->getPopupForStartingConvertingProcess($directory_name, $photographer);
    }

    /**
     * Check directory structure by path
     * If it has errors return them in popup
     * If it's all correct go to create gallery page
     *
     * @param string $directory_name
     * @param int $user_id
     * @param \App\Users\UserRepo $userRepo
     * @param \App\Users\PhotographerService $photographerService
     * @param \App\Photos\Galleries\GalleryDashboardPresenter $dashboardPresenter
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Webmagic\Dashboard\Elements\Boxes\Box
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\FieldUnavailable
     * @throws \Webmagic\Dashboard\Core\Content\Exceptions\NoOneFieldsWereDefined
     */
    public function checkDirectoryStructure(
        string $directory_name,
        int $user_id,
        UserRepo $userRepo,
        PhotographerService $photographerService,
        GalleryDashboardPresenter $dashboardPresenter
    ) {
        if (! $photographer = $userRepo->getByID($user_id)) {
            abort(404);
        }

        $directory_path = $photographer->ftp_login.'/'.$directory_name;
        $exist = Storage::disk('uploads')->exists($directory_path);

        if (! $exist) {
            abort(404);
        }

        $errors = $photographerService->checkDirectoryStructure($directory_path, $photographer->ftp_login);

        if (count($errors) == 0) {
            return $this->redirect(route('dashboard::gallery.create', [$directory_name, $photographer->id]));
        }

        return $dashboardPresenter->getErrorsOfDirectoryStructure($errors);
    }
}
