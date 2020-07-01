<?php


namespace App\Processing\Processes\GroupPhotosProcessing;


use App\Photos\Galleries\GalleryRepo;
use App\Photos\GroupPhotosGeneration\GroupPhotosStorageManager;
use App\Photos\Photos\PhotoStorageManager;
use App\Processing\Processes\GalleryProcessingProcess;
use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class GroupPhotosGalleryMoveToRemote extends GalleryProcessingProcess
{
    /**
     * Job handle function
     *
     * @return mixed
     * @throws Exception
     */
    public function processLogic()
    {
        $gallery = (new GalleryRepo())->getByID($this->processable_id);
        (new PhotoStorageManager())->moveGalleryGroupPhotosToRemote($gallery);
    }
}
