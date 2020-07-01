<?php


namespace App\Processing\Processes\GroupPhotosProcessing;


use App\Photos\TemplatedPhotosGeneration\TemplatedPhotosStorageManager;
use App\Processing\Processes\GalleryProcessingProcess;
use Illuminate\Contracts\Filesystem\FileNotFoundException as FileNotFoundExceptionAlias;

class IDCardsMovingProcess extends GalleryProcessingProcess
{
    /**
     * @return mixed|void
     * @throws FileNotFoundExceptionAlias
     */
    protected function processLogic()
    {
        $gallery = $this->getGallery();
        (new TemplatedPhotosStorageManager())->moveIdCardsToRemote($gallery);
    }

}
