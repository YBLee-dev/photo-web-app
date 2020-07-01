<?php


namespace App\Photos\Photos;


use App\Core\StorageManager;
use App\Photos\Galleries\Gallery;
use App\Photos\People\Person;
use App\Photos\SubGalleries\SubGallery;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Http\File;
use Illuminate\Http\UploadedFile;
use Laracasts\Presenter\Exceptions\PresenterException;

/**
 * Class PhotoStorageManager
 *
 * @package App\Photos\Photos
 *
 * @method originalsPrepareLocalDir()
 * @method previewsPrepareLocalDir()
 * @method proofsPrepareLocalDir()
 * @method croppedFacesPrepareLocalDir()
 * @method miniWalletCollagesPrepareLocalDir()
 * @method schoolPhotosPrepareLocalDir()
 * @method classCommonPhotosPrepareLocalDir()
 * @method classPersonalPhotosPrepareLocalDir()
 * @method staffPhotosPrepareLocalDir()
 * @method iDCardsPortraitPrepareLocalDir()
 * @method iDCardsLandscapePrepareLocalDir()
 * @method freeGiftsPrepareLocalDir()
 * @method printablePrepareLocalDir()
 */
class PhotoStorageManager extends StorageManager
{
    /** @var string  */
    protected $pathsManager = PhotoPathsManager::class;

    /**
     * @param Gallery $gallery
     */
    public function deleteGalleryPhotos(Gallery $gallery)
    {
        $photos = $gallery->allPhotos();

        call_user_func_array([$this, 'deletePhotos'], $photos);
    }


    /**
     * @param Photo ...$photos
     *
     * @throws PresenterException
     */
    public function deletePhotos(Photo ... $photos)
    {
        foreach ($photos as $photo) {
            $this->deletePhoto($photo);
        }
    }

    /**
     * @param Photo ...$photos
     *
     * @throws PresenterException
     */
    public function deleteLocalPhotos(Photo ... $photos)
    {
        foreach ($photos as $photo) {
            $this->deleteLocalFile($photo->present()->originalBasePath());
        }
    }

    /**
     * Delete all photos
     *
     * @param Photo $photo
     * @throws PresenterException
     */
    public function deletePhoto(Photo $photo)
    {
        // Delete preview for originals
        if($photo->isOriginal()){
            $this->deleteLocalAndRemoteFiles($photo->present()->previewLocalBasePath());
        }

        $this->deleteLocalAndRemoteFiles($photo->present()->originalBasePath());
    }

    /**
     * @param Photo        $photo
     *
     * @param UploadedFile $file
     *
     * @throws FileNotFoundException
     * @throws PresenterException
     */
    public function updatePhotoFile(
        Photo $photo,
        UploadedFile $file
    )
    {
        $baseDirLocalPath = $this->getLocalTMPStorage()->path($photo->present()->baseDir());
        $file->move($baseDirLocalPath, $photo->fileName());

        $this->movePhotosToRemote($photo);
    }

    /**
     * @param Gallery $gallery
     */
    public function moveOnlyGalleryLocalPhotosToRemote(Gallery $gallery)
    {
        // Move gallery photos
        $photos = $gallery->photos;

        call_user_func_array([$this, 'movePhotosToRemote'], $photos->all());
    }


    /**
     * @param Gallery $gallery
     *
     */
    public function moveAllGalleryLocalPhotosToRemote(Gallery $gallery)
    {
        // Move gallery photos
        $photos = $gallery->photos;

        call_user_func_array([$this, 'movePhotosToRemote'], $photos->all());

        // Move sub galleries and persons photos
        foreach ($gallery->subgalleries as $subGallery) {
            $this->moveSubGalleryPhotosToRemote($subGallery);
            $this->movePersonLocalPhotosToRemote($subGallery->person);
        }
    }

    /**
     * @param Gallery $gallery
     */
    public function moveGalleryGroupPhotosToRemote(Gallery $gallery)
    {
        $photos = $gallery->allPhotos();
        call_user_func_array([$this, 'movePhotosToRemote'], $photos);
    }

    /**
     * @param Person $person
     */
    public function movePersonLocalPhotosToRemote(Person $person)
    {
        $photos = $person->photos;

        call_user_func_array([$this, 'movePhotosToRemote'], $photos->all());
    }

    /**
     * @param SubGallery $subGallery
     */
    public function moveSubGalleryPhotosToRemote(SubGallery $subGallery)
    {
        $photos = $subGallery->photos;

        call_user_func_array([$this, 'movePhotosToRemote'], $photos->all());
    }

    /**
     * Move photos to remote
     *
     * @param Photo ...$photos
     *
     * @throws FileNotFoundException
     * @throws PresenterException
     */
    public function movePhotosToRemote(Photo ... $photos)
    {
        foreach ($photos as $photo) {
            // Move file
            $status = $this->moveFileToRemote($photo->present()->originalBasePath());

            if($photo->hasPreview()){
                $status *= $this->moveFileToRemote($photo->present()->previewLocalBasePath());
            }

            // Update  file record
            $photo->update([
                'local_copy' => false,
                'remote_copy' => true,
                'status' => $status ? $photo->status : PhotoStatusEnum::BROKEN
            ]);
        }
    }

    /**
     * Move photos to remote
     *
     * @param Photo ...$photos
     *
     * @throws FileNotFoundException
     * @throws PresenterException
     */
    public function copyPhotosToLocal(Photo ... $photos)
    {
        foreach ($photos as $photo) {
            // Move file
            $status = $this->copyFileToLocal($photo->present()->originalBasePath());

            if($photo->hasPreview()){
                $status *= $this->copyFileToLocal($photo->present()->previewLocalBasePath());
            }

            // Update  file record
            $photo->update([
                'local_copy' => false,
                'remote_copy' => true,
                'status' => $status ? $photo->status : PhotoStatusEnum::BROKEN
            ]);
        }
    }

    /**
     * @param string      $remotePath
     * @param string|null $localPath
     *
     * @return bool
     * @throws FileNotFoundException
     */
    protected function copyFileToLocal(string $remotePath, string $localPath = null)
    {
        $localPath = $localPath ?? $remotePath;

        if(!$this->getRemoteStorage()->exists($remotePath)) {
            return false;
        }

        $file = $this->getRemoteStorage()->get($remotePath);
        $this->getLocalTMPStorage()->put($localPath, $file);
    }

    /**
     * @param Photo $photo
     *
     * @return bool
     * @throws PresenterException
     */
    public function isRemotePhotoExists(Photo $photo)
    {
        return $this->getRemoteStorage()->exists($photo->present()->originalBasePath());
    }

    /**
     * @param Photo $photo
     *
     * @return bool
     * @throws PresenterException
     */
    public function isLocalPhotoExists(Photo $photo)
    {
        return $this->getLocalTMPStorage()->exists($photo->present()->originalBasePath());
    }
}
