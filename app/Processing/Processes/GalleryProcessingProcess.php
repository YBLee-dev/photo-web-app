<?php


namespace App\Processing\Processes;


use App\Photos\Galleries\Gallery;
use App\Photos\Galleries\GalleryRepo;
use App\Processing\Scenarios\ProcessableScenarioInterface;
use Exception;
use Illuminate\Database\Eloquent\Model;

abstract class GalleryProcessingProcess extends ProcessableProcess
{
    /**
     * SchoolPhotoPreparingProcess constructor.
     *
     * @param int                               $galleryId
     * @param string                            $initialStatus
     * @param ProcessableScenarioInterface|null $scenario
     */
    public function __construct(int $galleryId, string $initialStatus = null, ProcessableScenarioInterface $scenario = null)
    {
        parent::__construct($galleryId, Gallery::class, $initialStatus, $scenario);
    }

    /**
     * Get gallery
     *
     * @return Gallery|null
     * @throws Exception
     */
    protected function getGallery()
    {
        return (new GalleryRepo())->getByID($this->processable_id);
    }
}
