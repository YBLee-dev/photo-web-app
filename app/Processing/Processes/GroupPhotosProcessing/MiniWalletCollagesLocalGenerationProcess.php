<?php


namespace App\Processing\Processes\GroupPhotosProcessing;


use App\Photos\GroupPhotosGeneration\GroupPhotoGenerator;
use App\Processing\Processes\GalleryProcessingProcess;
use ImagickException;
use Laracasts\Presenter\Exceptions\PresenterException;

class MiniWalletCollagesLocalGenerationProcess extends GalleryProcessingProcess
{
    /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 1800; // Depends of the children count may need a long time for execution

    /**
     * @return mixed|void
     * @throws ImagickException
     * @throws PresenterException
     */
    protected function processLogic()
    {
       $gallery = $this->getGallery();
       (new GroupPhotoGenerator())->generateMiniWalletPhotos($gallery);
    }


}
