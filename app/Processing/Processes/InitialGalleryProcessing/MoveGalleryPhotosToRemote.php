<?php


namespace App\Processing\Processes\InitialGalleryProcessing;


use App\Photos\Galleries\GalleryStatusEnum;
use App\Photos\Galleries\GalleryStorageManager;
use App\Photos\People\PersonStorageManager;
use App\Photos\Photos\PhotoStorageManager;
use App\Photos\TemplatedPhotosGeneration\TemplatedPhotosStorageManager;
use App\Processing\Processes\GalleryProcessingProcess;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class MoveGalleryPhotosToRemote extends GalleryProcessingProcess
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

        (new PhotoStorageManager())->moveAllGalleryLocalPhotosToRemote($gallery);
        (new GalleryStorageManager())->removeUploadDirectory($gallery);
    }
}
