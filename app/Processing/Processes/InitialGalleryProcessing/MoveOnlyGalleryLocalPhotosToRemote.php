<?php


namespace App\Processing\Processes\InitialGalleryProcessing;


use App\Photos\Galleries\Gallery;
use App\Photos\Photos\PhotoStorageManager;
use App\Processing\Processes\GalleryProcessingProcess;

class MoveOnlyGalleryLocalPhotosToRemote extends GalleryProcessingProcess
{

    /**
     * Do process logic
     *
     * @return mixed
     * @throws \Exception
     */
    protected function processLogic()
    {
        /** @var Gallery $gallery */
        $gallery = $this->getGallery();
        (new PhotoStorageManager())->moveOnlyGalleryLocalPhotosToRemote($gallery);
    }
}
