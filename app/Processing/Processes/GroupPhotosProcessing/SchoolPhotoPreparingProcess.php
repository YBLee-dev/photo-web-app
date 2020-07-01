<?php


namespace App\Processing\Processes\GroupPhotosProcessing;


use App\Photos\Galleries\GalleryRepo;
use App\Photos\GroupPhotosGeneration\GroupPhotoGenerator;
use App\Processing\Processes\GalleryProcessingProcess;
use ImagickException;
use Laracasts\Presenter\Exceptions\PresenterException;

class SchoolPhotoPreparingProcess extends GalleryProcessingProcess
{
    /**
     * Do process logic
     *
     * @return mixed
     * @throws ImagickException
     * @throws PresenterException
     */
    protected function processLogic()
    {
        $gallery = (new GalleryRepo())->getByID($this->processable_id);
        (new GroupPhotoGenerator())->schoolPhotoGenerate($gallery);
    }
}
