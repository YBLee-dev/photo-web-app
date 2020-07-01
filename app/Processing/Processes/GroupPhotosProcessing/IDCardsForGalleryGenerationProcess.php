<?php


namespace App\Processing\Processes\GroupPhotosProcessing;


use App\Photos\TemplatedPhotosGeneration\TemplatedPhotoGenerator;
use App\Processing\PersonProcessableProcess;
use ImagickException;
use Laracasts\Presenter\Exceptions\PresenterException;

class IDCardsForGalleryGenerationProcess extends PersonProcessableProcess
{
    /**
     * @return mixed|void
     * @throws ImagickException
     * @throws PresenterException
     * @throws \Exception
     */
    protected function processLogic()
    {
        $person = $this->getPerson();
        (new TemplatedPhotoGenerator())->generateIDCards($person);
    }

}
