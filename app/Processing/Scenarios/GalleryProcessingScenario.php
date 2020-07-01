<?php


namespace App\Processing\Scenarios;


use App\Photos\Galleries\Gallery;
use App\Photos\Galleries\GalleryRepo;

abstract class GalleryProcessingScenario extends ProcessableScenario
{
    /**
     * GalleryProcessingScenario constructor.
     *
     * @param int                               $galleryId
     * @param string|null                       $initialStatus
     * @param ProcessableScenarioInterface|null $scenario
     */
    public function __construct(
        int $galleryId,
        string $initialStatus = null,
        ProcessableScenarioInterface $scenario = null
    ) {
        parent::__construct($galleryId, Gallery::class, $initialStatus, $scenario);
    }

    /**
     * @return Gallery|null
     * @throws \Exception
     */
    protected function getGallery()
    {
        return (new GalleryRepo())->getByID($this->processable_id);
    }

}
