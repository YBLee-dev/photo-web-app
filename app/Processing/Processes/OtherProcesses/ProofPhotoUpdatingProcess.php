<?php


namespace App\Processing\Processes\OtherProcesses;


use App\Photos\Photos\PhotoRepo;
use App\Photos\Photos\PhotoStorageManager;
use App\Photos\SubGalleries\SubGallery;
use App\Photos\TemplatedPhotosGeneration\TemplatedPhotoGenerator;
use App\Processing\Processes\SubGalleryProcessingProcess;
use App\Processing\Scenarios\ProcessableScenarioInterface;

class ProofPhotoUpdatingProcess extends SubGalleryProcessingProcess
{
    protected $newPhotoUrl;

    /**
     * OrientPhotosProcess constructor.
     *
     * @param int                               $subGalleryId
     * @param string|null                       $initialStatus
     * @param ProcessableScenarioInterface|null $scenario
     */
    public function __construct(
        int $subGalleryId,
        string $newPhotoUrl,
        string $initialStatus = null,
        ProcessableScenarioInterface $scenario = null
    ) {
        $this->newPhotoUrl = $newPhotoUrl;
        parent::__construct($subGalleryId, SubGallery::class, $initialStatus, $scenario);
    }


    /**
     * Do process logic
     *
     * @return mixed
     * @throws \Exception
     */
    protected function processLogic()
    {
        $subGallery = $this->getSubGallery();
        $photo = (new TemplatedPhotoGenerator())->generateProofPhoto($subGallery, $this->newPhotoUrl);

        //Move new proof photo on s3 from local
        (new PhotoStorageManager())->movePhotosToRemote($photo);

        //Delete previous proof photo
        (new PhotoRepo())->deletePhoto($subGallery->person->proofPhoto()->id);
    }
}
