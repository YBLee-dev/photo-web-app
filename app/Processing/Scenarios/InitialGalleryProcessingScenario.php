<?php


namespace App\Processing\Scenarios;


use App\Photos\Galleries\Gallery;
use App\Photos\Galleries\GalleryRepo;
use App\Photos\Galleries\GalleryStatusEnum;
use App\Photos\SubGalleries\SubGalleryRepo;
use App\Processing\Processes\GroupPhotosProcessing\MiniWalletCollagesLocalGenerationProcess;
use App\Processing\Processes\InitialGalleryProcessing\CroppingLocalGalleryPhotosProcess;
use App\Processing\Processes\InitialGalleryProcessing\CroppingSubGalleryPhotoProcess;
use App\Processing\Processes\InitialGalleryProcessing\LoadPhotosProcess;
use App\Processing\Processes\InitialGalleryProcessing\MoveGalleryPhotosToRemote;
use App\Processing\Processes\InitialGalleryProcessing\MoveSubGalleryPhotosToRemote;
use App\Processing\Processes\InitialGalleryProcessing\MoveUnprocessedGalleryToTmp;
use App\Processing\Processes\InitialGalleryProcessing\OrientSubGalleryOriginalLocalPhotosProcess;
use App\Processing\Processes\InitialGalleryProcessing\PreviewsOneLocalSubGalleryGenerateProcess;
use App\Processing\Processes\InitialGalleryProcessing\RemoveGalleyUploadDirectoryProcess;
use App\Processing\Processes\InitialGalleryProcessing\SubGalleriesDataPreparingProcess;

class InitialGalleryProcessingScenario extends ProcessableScenario
{
    /** @var Gallery */
    protected $galleryId;
    /**
     * @var string
     */
    private $ftpPath;
    /**
     * @var int
     */
    private $userId;

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
        int $galleryId,
        string $ftpPath,
        int $userId,
        string $initialStatus = null,
        ProcessableScenarioInterface $scenario = null
    ) {

        $this->galleryId = $galleryId;
        $this->ftpPath = $ftpPath;
        $this->userId = $userId;

        parent::__construct($galleryId, Gallery::class, $initialStatus, $scenario);
    }

    /**
     * @return mixed|void
     * @throws \Exception
     */
    public function initialize()
    {
        $subGalleries = (new SubGalleryRepo())->getByGalleryID($this->galleryId);

        $this->addProcesses(
            0,
            (new MoveUnprocessedGalleryToTmp($this->galleryId, $this->userId, $this->ftpPath, null, $this))
        );

        $this->addProcesses(
            1,
            (new SubGalleriesDataPreparingProcess($this->galleryId, null, $this))
        );

        $this->addProcesses(
            2,
            (new LoadPhotosProcess($this->galleryId, null, $this))
        );

        foreach ($subGalleries as $subGallery) {
            $this->addProcesses(
                3,
                (new OrientSubGalleryOriginalLocalPhotosProcess($subGallery->id, null, $this))
            );
        }

        foreach ($subGalleries as $subGallery) {
            $this->addProcesses(
                4,
                (new PreviewsOneLocalSubGalleryGenerateProcess($subGallery->id, null, $this))
            );
        }

        // Crop photos
        foreach ($subGalleries as $subGallery) {
            $this->addProcesses(
                4,
                (new CroppingSubGalleryPhotoProcess($subGallery->id, null, $this))
            );
        }

        // Move sub galleries and people photos to remote
        foreach ($subGalleries as $subGallery) {
            $this->addProcesses(
                5,
                (new MoveSubGalleryPhotosToRemote($subGallery->id, null, $this))
            );
        }

        //Move only gallery photos to remote
        $this->addProcesses(
            5,
            (new MoveGalleryPhotosToRemote($this->galleryId, null, $this))
        );

        //Remove gallery upload directory
        $this->addProcesses(
            6,
            (new RemoveGalleyUploadDirectoryProcess($this->galleryId, null, $this))
        );
    }

    /**
     * @throws \Exception
     */
    protected function finishedSuccessfully()
    {
        (new GalleryRepo())->getByID($this->galleryId)->update(['status' => GalleryStatusEnum::READY]);
    }


}
