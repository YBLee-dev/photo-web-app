<?php


namespace App\Processing\Processes\RemovingProcessing;

use App\Photos\SubGalleries\SubGalleryRepo;
use App\Processing\Processes\SubGalleryProcessingProcess;

class RemoveSubGalleryProcess extends SubGalleryProcessingProcess
{
    /**
     * Do process logic
     *
     * @return mixed
     * @throws \Exception
     */
    protected function processLogic()
    {
        // Delete sub gallery, person and their photos photos
        (new SubGalleryRepo())->destroy($this->processable_id);
    }
}
