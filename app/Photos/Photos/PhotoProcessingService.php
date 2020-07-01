<?php

namespace App\Photos\Photos;


use App\Photos\Galleries\Gallery;
use App\Photos\PhotoProcessing\FaceDetectService;
use App\Photos\SubGalleries\SubGallery;
use ImagickException;
use Intervention\Image\Facades\Image;
use Laracasts\Presenter\Exceptions\PresenterException;

class PhotoProcessingService
{
    protected $previewWidth = 800;
    protected $previewHeight = 800;

    /**
     * @param Gallery $gallery
     *
     * @throws ImagickException
     * @throws PresenterException
     */
    public function cropGalleryPhotosLocally(Gallery $gallery)
    {
        $subGalleries = $gallery->subgalleries;
        foreach ($subGalleries as $subGallery) {
            $this->cropSubGalleryPhoto($subGallery);
        }
    }

    /**
     * @param SubGallery $subGallery
     *
     * @throws ImagickException
     * @throws PresenterException
     */
    public function cropSubGalleryPhoto(SubGallery $subGallery)
    {
        // Prepare dir
        (new PhotoStorageManager())->croppedFacesPrepareLocalDir();
        $photoFactory = new PhotosFactory();

        $width = config('project.cropped_face_size.width');
        $height = config('project.cropped_face_size.height');

        // Do nothing if Person has already has cropped photo
        if($subGallery->person->croppedPhoto()){
            return;
        }

        $originalPhotoPath = $subGallery->mainPhoto()->present()->originalLocalPath();

        //Save new photo
        $photo = $photoFactory->createEmptyPhoto(PhotoTypeEnum::CROPPED_FACE);
        $croppingFilePath = $photo->present()->originalLocalPath();

        // Crop file
        $croppedFile = (new FaceDetectService())->getPortrait($originalPhotoPath, $width, $height, $photo);

        //todo check why failed
        if(!$croppedFile) {
            return;
        }

        $croppedFile->writeImage($croppingFilePath);

        $photo = $photoFactory->updatePhotoFromFile($photo, $croppingFilePath);

        // Associate with Person
        $subGallery->person->photos()->attach($photo->id);
    }

    /**
     * Orient all original uploaded photos
     *
     * @param SubGallery $subGallery
     *
     * @throws PresenterException
     */
    public function orientSubGalleryOriginalLocalPhotos(SubGallery $subGallery)
    {
        $photos = $subGallery->originalPhotos;

        foreach ($photos as $photo) {
            // Do nothing if local file doesn't exist
            if(!$photo->isLocalFileExists()) {
                continue;
            }

            $filePath = $photo->present()->originalLocalPath();

            Image::make($filePath)->orientate()->save($filePath)->destroy();
        }
    }

    /**
     * Orient all photos in sub gallery
     *
     * @param SubGallery $subGallery
     *
     * @throws PresenterException
     */
    public function generateLocalSubGalleryPreviews(SubGallery $subGallery)
    {
        $previewWidth = config('project.preview_size.width');
        $previewHeight = config('project.preview_size.height');
        $watermarkPath = public_path('img/watermark.png');

        // Prepare directory
        (new PhotoStorageManager())->previewsPrepareLocalDir();

        // Create previews
        $photos = $subGallery->originalPhotos;

        foreach ($photos as $photo) {
            // Do nothing if local file doesn't exist
            if(!$photo->isLocalFileExists()){
                continue;
            }

            // copy file
            $resultFilePath = $photo->present()->previewLocalPath();
            file_put_contents($resultFilePath, file_get_contents($photo->present()->originalLocalPath()));

            Image::make($resultFilePath)
                ->resize($previewWidth, $previewHeight, function ($constraint) {
                        $constraint->aspectRatio();
                    })
                ->insert($watermarkPath)
                ->save($resultFilePath);
        }
    }
}
