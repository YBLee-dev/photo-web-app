<?php


namespace App\Processing\Processes\InitialGalleryProcessing;


use App\Photos\Galleries\Gallery;
use App\Photos\Galleries\GalleryStorageManager;
use App\Processing\Processes\GalleryProcessingProcess;

class RemoveGalleyUploadDirectoryProcess extends GalleryProcessingProcess
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
        (new GalleryStorageManager())->removeUploadDirectory($gallery);
    }
}
