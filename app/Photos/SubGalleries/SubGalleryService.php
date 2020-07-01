<?php

namespace App\Photos\SubGalleries;

use App\Core\StorageManager;
use App\Photos\Galleries\Gallery;
use App\Photos\Photos\PhotosFactory;
use App\Photos\Photos\PhotoStatusEnum;
use App\Photos\Photos\PhotoTypeEnum;
use App\Utils;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Laracasts\Presenter\Exceptions\PresenterException;

class SubGalleryService
{
    /**
     * Create sub galleries list from processing directory
     *
     * @param Gallery $gallery
     *
     * @throws PresenterException
     * @throws \Exception
     */
    public function createSubGalleriesFromGalleryProcessingDirectory(Gallery $gallery)
    {
        $galleryPath = $gallery->present()->processingGalleryPath();
        $directories = (new StorageManager())->dirListInLocalTMPPath($galleryPath);

        $repo = (new SubGalleryRepo());

        foreach ($directories as $directory) {
            // Firs or create for correct work when you upload new photos to current gallery
            $repo->firstOrCreate($gallery->id, basename($directory));
        }
    }

    /**
     * Return concat full path$subGalleryRepo
     *
     * @param SubGallery $subGallery
     * @deprecated
     * @return string
     */
    public function getStoragePath(SubGallery $subGallery)
    {
        return $subGallery->gallery->path . '/' . $subGallery->name;
    }

    /**
     * @param SubGallery $subGallery
     * @deprecated
     */
    public function deleteSubGalleryOnS3(SubGallery $subGallery)
    {
        $storage_path = $this->getStoragePath($subGallery);
        $this->deleteDirectoryFromS3('original/' . $storage_path);
        $this->deleteDirectoryFromS3('preview/' . $storage_path);
    }

    /**
     * @param string $path
     *
     * @deprecated
     */
    protected function deleteDirectoryFromS3(string $path)
    {
        $exists = Storage::disk('s3')->exists($path);

        if ($exists) {
            Storage::disk('s3')->deleteDirectory($path);
        }
    }

    /**
     * @param string $oldGalleryPath
     * @param string $newGalleryPath
     * @deprecated
     */
    public function moveSubgalleryOnS3ToAnotherGallery(string $oldGalleryPath, string $newGalleryPath)
    {
        $original_images = Storage::disk('s3')->allFiles('original/' . $oldGalleryPath);
        $this->moveToNewPathOnS3($original_images, $oldGalleryPath, $newGalleryPath);

        $preview_images = Storage::disk('s3')->allFiles('preview/' . $oldGalleryPath);
        $this->moveToNewPathOnS3($preview_images, $oldGalleryPath, $newGalleryPath);
    }

    /**
     * @param array  $images
     * @param string $old_gallery_path
     * @param string $new_gallery_path
     * @deprecated
     */
    protected function moveToNewPathOnS3(array $images, string $old_gallery_path, string $new_gallery_path)
    {
        foreach ($images as $image) {
            $moveTo = str_replace($old_gallery_path, $new_gallery_path, $image);
            Storage::disk('s3')->move($image, $moveTo);
        }
    }

    /**
     * Return base sub gallery image
     *
     * @param $subGalleryPath
     * @deprecated
     * @return mixed|null
     */
    public function getBasicPhoto($subGalleryPath)
    {
        $files = Storage::disk('s3')->files('original/' . $subGalleryPath);

        if (!is_array($files)) {
            return null;
        }

        return Arr::first($files);
    }

    /**
     * Save uploading image to original local storage and attach it to subgallery
     *
     * @param \Illuminate\Http\UploadedFile $image
     * @param \App\Photos\SubGalleries\SubGallery $subGallery
     * @param bool $mainPhoto
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function createAndAttachOriginalPhoto(UploadedFile $image, SubGallery $subGallery, $mainPhoto = false)
    {
        /** @var PhotosFactory $photoFactory */
        $photoFactory = new PhotosFactory();
        $photo = $photoFactory->createEmptyPhoto(
            PhotoTypeEnum::ORIGINAL,
            $image->getClientOriginalExtension(),
            $image->getClientOriginalName(),
            PhotoStatusEnum::ADDED_MANUALLY);
        // Store photo
        $path = $photo->present()->originalLocalPath();
        file_put_contents($path, file_get_contents($image));

        // Update photo info
        $photo = $photoFactory->updatePhotoFromFile($photo, $path);

        // Attache to gallery
        $subGallery->photos()->attach($photo->id);

        // Attache photo to person
        $subGallery->person->photos()->attach($photo->id);

        if($mainPhoto){
            $subGallery['main_photo_id'] = $photo->id;
            $subGallery->save();
        }
    }
}
