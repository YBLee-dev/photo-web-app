<?php


namespace App\Processing\Processes\RemovingProcessing;


use App\Photos\Galleries\Gallery;
use App\Photos\Photos\PhotoRepo;
use App\Processing\Processes\GalleryProcessingProcess;

class RemoveAllGalleryGroupPhotosProcess extends GalleryProcessingProcess
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
        $gallery = $this->getGallery();
        call_user_func_array([new PhotoRepo(), 'deletePhotos'], $gallery->allPhotos());
    }
}
