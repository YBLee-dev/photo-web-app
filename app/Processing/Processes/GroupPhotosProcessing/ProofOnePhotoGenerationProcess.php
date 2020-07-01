<?php


namespace App\Processing\Processes\GroupPhotosProcessing;


use App\Photos\TemplatedPhotosGeneration\TemplatedPhotoGenerator;
use App\Processing\Processes\SubGalleryProcessingProcess;
use App\Processing\Scenarios\ProcessableScenarioInterface;
use ImagickException;
use Laracasts\Presenter\Exceptions\PresenterException;

class ProofOnePhotoGenerationProcess extends SubGalleryProcessingProcess
{
    protected $newPhotoUrl = null;

    /**
     * OrientPhotosProcess constructor.
     *
     * @param int                               $subGalleryId
     * @param string|null                       $initialStatus
     * @param ProcessableScenarioInterface|null $scenario
     */
    public function __construct(
        int $subGalleryId,
        string $initialStatus = null,
        ProcessableScenarioInterface $scenario = null,
        string $newPhotoUrl = null
    ) {
        $this->newPhotoUrl = $newPhotoUrl;
        parent::__construct($subGalleryId, $initialStatus, $scenario);
    }

    /**
     * Do process logic
     *
     * @return mixed
     * @throws ImagickException
     * @throws PresenterException
     */
    protected function processLogic()
    {
        $subGallery = $this->getSubGallery();
        (new TemplatedPhotoGenerator())->generateProofPhoto($subGallery, $this->newPhotoUrl);
    }
}
