<?php


namespace App\Processing\Processes\GroupPhotosProcessing;


use App\Photos\Galleries\GalleryRepo;
use App\Photos\GroupPhotosGeneration\GroupPhotoGenerator;
use App\Processing\Processes\GalleryProcessingProcess;

class CommonClassPhotosPreparingProcess extends GalleryProcessingProcess
{
    /**
     * Job handle function
     *
     * @return mixed
     * @throws \ImagickException
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function processLogic()
    {
        $gallery = (new GalleryRepo())->getByID($this->processable_id);
        (new GroupPhotoGenerator())->allCommonClassPhotosGenerate($gallery);
    }
}
