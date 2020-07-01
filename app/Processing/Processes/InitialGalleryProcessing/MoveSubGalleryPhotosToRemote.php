<?php


namespace App\Processing\Processes\InitialGalleryProcessing;


use App\Photos\Photos\PhotoStorageManager;
use App\Processing\Processes\SubGalleryProcessingProcess;

class MoveSubGalleryPhotosToRemote extends SubGalleryProcessingProcess
{

    /**
     * Do process logic
     *
     * @return mixed
     * @throws \Exception
     */
    protected function processLogic()
    {
        $subGallery = $this->getSubGallery();

        $photoStorageManager = new PhotoStorageManager();
        $photoStorageManager->moveSubGalleryPhotosToRemote($subGallery);
        $photoStorageManager->movePersonLocalPhotosToRemote($subGallery->person);
    }
}
