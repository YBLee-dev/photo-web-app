<?php


namespace App\Processing\Processes\InitialGalleryProcessing;


use App\Photos\Galleries\Gallery;
use App\Photos\Galleries\GalleryRepo;
use App\Photos\Photos\PhotosFactory;
use App\Photos\SubGalleries\SubGalleryService;
use App\Processing\Processes\GalleryProcessingProcess;

class LoadPhotosProcess extends GalleryProcessingProcess
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
        $gallery = (new GalleryRepo())->getByID($this->processable_id);
        (new PhotosFactory())->loadPhotosDataForGallery($gallery);
    }
}
