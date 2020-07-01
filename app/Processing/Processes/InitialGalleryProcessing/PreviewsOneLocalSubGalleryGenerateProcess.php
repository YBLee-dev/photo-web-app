<?php


namespace App\Processing\Processes\InitialGalleryProcessing;


use App\Photos\Photos\PhotoProcessingService;
use App\Processing\Processes\SubGalleryProcessingProcess;
use Laracasts\Presenter\Exceptions\PresenterException;

class PreviewsOneLocalSubGalleryGenerateProcess extends SubGalleryProcessingProcess
{
    /**
     * Do process logic
     *
     * @return mixed
     * @throws PresenterException
     */
    protected function processLogic()
    {
        $subGallery = $this->getSubGallery();
        (new PhotoProcessingService())->generateLocalSubGalleryPreviews($subGallery);
    }
}
