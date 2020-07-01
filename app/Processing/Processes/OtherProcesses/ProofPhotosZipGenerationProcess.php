<?php


namespace App\Processing\Processes\OtherProcesses;


use App\Photos\Galleries\Gallery;
use App\Photos\Galleries\GalleryExportService;
use App\Processing\Processes\GalleryProcessingProcess;

class ProofPhotosZipGenerationProcess extends GalleryProcessingProcess
{
    /**
     * Do process logic
     *
     * @return mixed
     * @throws \Exception
     */
    protected function processLogic()
    {
        /** @var Gallery $gallery */
        $gallery = $this->getGallery();
        (new GalleryExportService())->proofPhotosExport($gallery);
    }
}
