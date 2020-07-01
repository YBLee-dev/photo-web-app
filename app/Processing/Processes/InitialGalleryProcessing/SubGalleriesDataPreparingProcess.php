<?php


namespace App\Processing\Processes\InitialGalleryProcessing;


use App\Photos\Galleries\Gallery;
use App\Photos\Galleries\GalleryRepo;
use App\Photos\People\PersonService;
use App\Photos\SubGalleries\SubGalleryService;
use App\Processing\Processes\GalleryProcessingProcess;
use Laracasts\Presenter\Exceptions\PresenterException;

class SubGalleriesDataPreparingProcess extends GalleryProcessingProcess
{
    /**
     * Create all sub galleries for gallery
     *
     * @return mixed
     * @throws PresenterException
     * @throws \Exception
     */
    protected function processLogic()
    {
        // Prepare sub galleries
        /** @var Gallery $gallery */
        $gallery = (new GalleryRepo())->getByID($this->processable_id);
        (new SubGalleryService())->createSubGalleriesFromGalleryProcessingDirectory($gallery);

        // Prepare people
        (new PersonService())->createPeopleFromGalleryProcessingDirectory($gallery);
    }
}
