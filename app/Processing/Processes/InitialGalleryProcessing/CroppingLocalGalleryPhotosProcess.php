<?php


namespace App\Processing\Processes\InitialGalleryProcessing;


use App\Photos\Photos\PhotoProcessingService;
use App\Processing\Processes\GalleryProcessingProcess;

class CroppingLocalGalleryPhotosProcess extends GalleryProcessingProcess
{
    /**
     * Do process logic
     *
     * @return mixed
     * @throws \Exception
     */
    protected function processLogic()
    {
        $gallery = $this->getGallery();
        (new PhotoProcessingService())->cropGalleryPhotosLocally($gallery);
    }
}
