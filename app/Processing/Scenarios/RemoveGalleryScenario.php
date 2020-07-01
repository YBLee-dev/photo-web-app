<?php


namespace App\Processing\Scenarios;


use App\Photos\Galleries\GalleryRepo;
use App\Processing\Processes\RemovingProcessing\RemoveSubGalleryProcess;
use App\Processing\Processes\RemovingProcessing\RemovingGalleryOnlyPhotos;
use App\Processing\ProcessRecords\ProcessRecordRepo;
use Exception;
use Webmagic\Core\Entity\Exceptions\EntityNotExtendsModelException;
use Webmagic\Core\Entity\Exceptions\ModelNotDefinedException;

class RemoveGalleryScenario extends GalleryProcessingScenario
{
    /**
     * Initialize and add all needed processes to processes list
     *
     * @return mixed
     * @throws Exception
     */
    public function initialize()
    {
        $gallery = $this->getGallery();
        $subGalleries = $gallery->subgalleries;

        // Remove sub galleries and people photos
        foreach ($subGalleries as $subGallery) {
            $this->addProcesses(
                0,
                new RemoveSubGalleryProcess($subGallery->id, null, $this)
            );
        }

        // Remove gallery only photos
        $this->addProcesses(
            0,
            new RemovingGalleryOnlyPhotos($gallery->id, null, $this)
        );
    }

    /**
     * @throws EntityNotExtendsModelException
     * @throws ModelNotDefinedException
     */
    protected function finishedSuccessfully()
    {
        // Delete gallery at the end
        (new GalleryRepo())->destroy($this->processable_id);
    }
}
