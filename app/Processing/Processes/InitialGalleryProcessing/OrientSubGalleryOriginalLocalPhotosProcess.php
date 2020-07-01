<?php


namespace App\Processing\Processes\InitialGalleryProcessing;


use App\Photos\Photos\PhotoProcessingService;
use App\Photos\SubGalleries\SubGallery;
use App\Processing\Processes\SubGalleryProcessingProcess;

class OrientSubGalleryOriginalLocalPhotosProcess extends SubGalleryProcessingProcess
{
    /**
     * Orient all photos in gallery
     *
     * @return mixed
     * @throws \Exception
     */
    protected function processLogic()
    {
        /** @var SubGallery $subGallery */
        $subGallery = $this->getSubGallery();
        (new PhotoProcessingService())->orientSubGalleryOriginalLocalPhotos($subGallery);
    }
}
