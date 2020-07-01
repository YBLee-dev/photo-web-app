<?php


namespace App\Processing\Processes\InitialGalleryProcessing;


use App\Photos\Photos\PhotoProcessingService;
use App\Photos\SubGalleries\SubGallery;
use App\Processing\Processes\SubGalleryProcessingProcess;
use ImagickException;
use Laracasts\Presenter\Exceptions\PresenterException;

class CroppingSubGalleryPhotoProcess extends SubGalleryProcessingProcess
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
        /** @var SubGallery $subGallery */
        $subGallery = $this->getSubGallery();
        (new PhotoProcessingService())->cropSubGalleryPhoto($subGallery);
    }
}
