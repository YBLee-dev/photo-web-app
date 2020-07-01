<?php


namespace App\Photos\Photos;


use App\Photos\Galleries\Gallery;
use App\Photos\PrintablePhotosGeneration\PrintablePhotosSizesEnum;
use Intervention\Image\File;
use Laracasts\Presenter\Exceptions\PresenterException;
use Exception;


class PhotosFactory
{
    /**
     * @param Gallery $gallery
     *
     * @throws PresenterException
     */
    public function loadPhotosDataForGallery(Gallery $gallery)
    {
        $subGalleries = $gallery->subGalleries;

        (new PhotoStorageManager())->originalsPrepareLocalDir();

        foreach ($subGalleries as $subGallery) {
            $subGalleryUploadedFiles = $subGallery->present()->uploadedLocalFiles();

            // Do nothing for empty directories
            if(!count($subGalleryUploadedFiles)) {
                continue;
            }

            foreach ($subGalleryUploadedFiles as $key => $file) {
                // Create photo record
                $photo = $this->createPhotoFromFile($file, PhotoTypeEnum::ORIGINAL);

                if(is_null($photo)){
                    continue;
                }
                // Copy file
                $newFilePath = $photo->present()->originalLocalPath();
                file_put_contents($newFilePath, file_get_contents($file));

                // Save first file as main fro sub gallery
                if($key === 0) {
                    $subGallery['main_photo_id'] = $photo->id;
                    $subGallery->save();
                }

                $subGallery->photos()->attach($photo->id);
            }
        }
    }

    /**
     * @param Photo  $photo
     * @param string $fullLocalPath
     *
     * @param bool   $updateOriginalFileName
     *
     * @return Photo
     */
    public function updatePhotoFromFile(Photo $photo, string $fullLocalPath, bool $updateOriginalFileName = true)
    {
        $file = (new File())->setFileInfoFromPath($fullLocalPath);
        try {
            $sizes = getimagesize($fullLocalPath);

            $photo->update([
                'extension' => $file->extension,
                'width' => $sizes[0],
                'height' => $sizes[1],
                'size' => $file->filesize(),
                'remote_copy' => false,
                'local_copy' => true,
            ]);
        } catch (Exception $e) {
            logger($e);
        }

        // Update original file name if needed
        if ($updateOriginalFileName) {
            $photo->update([
                'original_filename' => $file->basename,
            ]);
        }

        return $photo;
    }

    /**
     * @param string $fullLocalPath
     * @param string $type
     *
     * @return Photo|null
     */
    public function createPhotoFromFile(string $fullLocalPath, string $type)
    {
        // Create photo
        $file = (new File())->setFileInfoFromPath($fullLocalPath);

        try{
            $sizes = getimagesize($fullLocalPath);
        } catch (\Exception $e){
            logger($e);
            return null;
        }

        $photo = new Photo([
           'type' => $type,
           'original_filename' => $file->basename,
           'extension' => $file->extension,
           'width' =>$sizes[0],
           'height' => $sizes[1],
           'size' => $file->filesize(),
           'remote_copy' => false,
           'local_copy' => true,
           'status' => PhotoStatusEnum::INITIAL_PROCESSING
        ]);

        $photo->save();

        return $photo;
    }

    /**
     * @param string|null $type
     *
     * @param string $extension
     *
     * @param string|null $originalFileName
     * @param string|null $status
     * @return Photo
     */
    public function createEmptyPhoto(
        string $type = null,
        string $extension = 'jpg',
        string $originalFileName = null,
        string $status = null
    ) {
        $photo = new Photo([
            'type' => $type ?? PhotoTypeEnum::EMPTY_TEMPLATE,
            'extension' => $extension,
            'original_filename' => $originalFileName ?? '',
            'status' => $status ?? PhotoStatusEnum::TEMPLATE

        ]);
        $photo->save();

        return $photo;
    }


    /**
     * @param string|null $type
     *
     * @param int         $schoolId
     * @param int         $orderId
     * @param string      $personName
     * @param string|null $classroom
     * @param string|null $size
     * @param int         $number
     * @param string      $extension
     *
     * @param string|null $status
     *
     * @return Photo
     */
    public function createEmptyPrintablePhoto(
        string $type,
        int $schoolId,
        int $orderId,
        string $personName,
        string $classroom = null,
        string $size = null,
        int $number = 0,
        string $extension = 'jpg',
        string $status = null
    ) {
        $photo = $this->createEmptyPhoto($type, $extension, null, $status);

        $fileName = $this->preparePrintablePhotoFileName(
            $photo,
            $schoolId,
            $orderId,
            $personName,
            $classroom,
            $size,
            $number
        );

        $photo['original_filename'] = $fileName;

        $photo->save();

        return $photo;
    }

    /**
     * @param Photo       $photo
     * @param int         $schoolId
     * @param int         $orderId
     * @param string      $personName
     * @param string      $classroom
     * @param string|null $size
     * @param int         $number
     *
     * @return string
     */
    public function preparePrintablePhotoFileName(
        Photo $photo,
        int $schoolId,
        int $orderId,
        string $personName,
        string $classroom = null,
        string $size = null,
        int $number = 0
    ) {
        // Prepare photo list number
        $photoListNumber = $this->getPhotoListNumber($photo, $size);

        // Prepare other data
        $personName = str_slug($personName, '_');
        $classroom = str_slug($classroom, '_');
        $extension = $photo->extension;

        // Prepare additional mark
        if($size){
            $sizeMark = str_slug($size);
        } else {
            $sizeMark = str_slug($photo->type);
        }

        return "{$schoolId}_{$classroom}_{$orderId}_{$personName}_{$photoListNumber}_{$sizeMark}_{$number}.{$extension}";
    }

    /**
     * @param \App\Photos\Photos\Photo $photo
     * @param int $schoolId
     * @param string $personName
     * @param string|null $classroom
     * @return string
     */
    public function prepareProofPhotoFileName(
        Photo $photo,
        int $personId,
        string $personName,
        string $classroom
    ) {
        // Prepare other data
        $personName = str_slug($personName, '_');
        $classroom = str_slug($classroom, '_');
        $extension = $photo->extension;

        return "{$classroom}-{$personId}-{$personName}.{$extension}";
    }

    /**
     * @param Photo       $photo
     * @param string|null $size
     *
     * @return false|int|string|void
     */
    public function getPhotoListNumber(Photo $photo, string $size = null)
    {
        $availableSizes = array_keys(PrintablePhotosSizesEnum::labels());

        // Try to get order number by size
        if ($size) {
            $orderNumber = array_search($size, $availableSizes);
            if ($orderNumber !== false){
                return $orderNumber;
            }
        }

        $availableSizesCount = count($availableSizes);

        // Try to get order number by photo type
        if(PhotoTypeEnum::FREE_GIFT()->is($photo->type)){
            return $availableSizesCount + 1;
        }

        // Try to get order number by photo type
        if(PhotoTypeEnum::SCHOOL_PHOTO()->is($photo->type) || PhotoTypeEnum::PERSONAL_CLASS_PHOTO()->is($photo->type)) {
            return $availableSizesCount + 2;
        }

        // Return default order number
        return $availableSizesCount + 3;
    }
}
