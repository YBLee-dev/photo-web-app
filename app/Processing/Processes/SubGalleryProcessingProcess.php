<?php


namespace App\Processing\Processes;


use App\Photos\SubGalleries\SubGallery;
use App\Photos\SubGalleries\SubGalleryRepo;
use App\Processing\Scenarios\ProcessableScenarioInterface;
use Exception;

abstract class SubGalleryProcessingProcess extends ProcessableProcess
{
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
        ProcessableScenarioInterface $scenario = null
    ) {
        parent::__construct($subGalleryId, SubGallery::class, $initialStatus, $scenario);
    }

    /**
     * @return SubGallery|null
     * @throws Exception
     */
    protected function getSubGallery()
    {
        return (new SubGalleryRepo())->getByID($this->processable_id);
    }
}
