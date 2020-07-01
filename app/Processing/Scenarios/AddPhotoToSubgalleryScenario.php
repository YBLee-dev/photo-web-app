<?php


namespace App\Processing\Scenarios;


use App\Photos\Galleries\Gallery;
use App\Photos\SubGalleries\SubGallery;
use App\Processing\Processes\InitialGalleryProcessing\CroppingSubGalleryPhotoProcess;
use App\Processing\Processes\InitialGalleryProcessing\MoveSubGalleryPhotosToRemote;
use App\Processing\Processes\InitialGalleryProcessing\OrientSubGalleryOriginalLocalPhotosProcess;
use App\Processing\Processes\InitialGalleryProcessing\PreviewsOneLocalSubGalleryGenerateProcess;


class AddPhotoToSubgalleryScenario extends ProcessableScenario
{
    /** @var Gallery */
    protected $subGalleryId;


    /**
     * InitialGalleryProcessingScenario constructor.
     *
     * @param int                               $galleryId
     * @param string                            $ftpPath
     * @param int                               $userId
     * @param string|null                       $initialStatus
     * @param ProcessableScenarioInterface|null $scenario
     */
    public function __construct(
        int $subGalleryId,
        string $initialStatus = null,
        ProcessableScenarioInterface $scenario = null
    ) {

        $this->subGalleryId = $subGalleryId;

        parent::__construct($subGalleryId, SubGallery::class, $initialStatus, $scenario);
    }

    /**
     * @return mixed|void
     * @throws \Exception
     */
    public function initialize()
    {
        $this->addProcesses(
            0,
            (new OrientSubGalleryOriginalLocalPhotosProcess($this->subGalleryId, null, $this))
        );

        $this->addProcesses(
                1,
            (new PreviewsOneLocalSubGalleryGenerateProcess($this->subGalleryId, null, $this))
        );


        // Crop photos
        $this->addProcesses(
            2,
            (new CroppingSubGalleryPhotoProcess($this->subGalleryId, null, $this))
        );


        //Move sub galleries and people photos to remote
        $this->addProcesses(
            3,
            (new MoveSubGalleryPhotosToRemote($this->subGalleryId, null, $this))
        );
    }
}
