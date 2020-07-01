<?php


namespace App\Processing\Processes\GroupPhotosProcessing;


use App\Photos\Galleries\GalleryRepo;
use App\Photos\GroupPhotosGeneration\GroupPhotoGenerator;
use App\Processing\Processes\GalleryProcessingProcess;

class StaffPhotoPreparingProcess extends GalleryProcessingProcess
{
    /**
     * Do process logic
     *
     * @return mixed
     * @throws \ImagickException
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    protected function processLogic()
    {
        $gallery = (new GalleryRepo())->getByID($this->processable_id);
        (new GroupPhotoGenerator())->staffPhotoGenerate($gallery);
    }
}
