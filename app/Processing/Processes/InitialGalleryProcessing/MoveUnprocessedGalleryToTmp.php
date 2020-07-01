<?php


namespace App\Processing\Processes\InitialGalleryProcessing;


use App\Core\StorageManager;
use App\Jobs\MarkDirectoryAsUploading;
use App\Photos\Galleries\Gallery;
use App\Photos\Galleries\GalleryPathsManager;
use App\Photos\Galleries\GalleryRepo;
use App\Photos\Galleries\GalleryStorageManager;
use App\Processing\Processes\GalleryProcessingProcess;
use App\Processing\Scenarios\ProcessableScenarioInterface;
use App\Users\User;
use App\Users\UserPathsManger;
use App\Users\UserRepo;

class MoveUnprocessedGalleryToTmp extends GalleryProcessingProcess
{
    /** @var int */
    protected $userId;
    /**
     * @var string
     */
    private $ftpPath;

    public function __construct(
        int $galleryId,
        int $userId,
        string $ftpPath,
        string $initialStatus = null,
        ProcessableScenarioInterface $scenario = null
    ) {
        parent::__construct($galleryId, $initialStatus, $scenario);

        $this->userId = $userId;
        $this->ftpPath = $ftpPath;
    }

    /**
     * Do process logic
     *
     * @return mixed
     * @throws \Exception
     */
    protected function processLogic()
    {
        /** @var User $user */
        $user = (new UserRepo())->getByID($this->userId);

        /** @var Gallery $gallery */
        $gallery = $this->getGallery();

        (new GalleryStorageManager())->moveUnprocessedGalleryToTmp($gallery, $user, $this->ftpPath);
    }
}
