<?php


namespace App\Processing\Scenarios;

use App\Photos\Galleries\Gallery;
use App\Photos\Galleries\GalleryRepo;
use App\Photos\People\Person;
use App\Processing\Processes\GroupPhotosProcessing\CommonClassPhotosPreparingProcess;
use App\Processing\Processes\GroupPhotosProcessing\GroupPhotosGalleryMoveToRemote;
use App\Processing\Processes\GroupPhotosProcessing\IDCardsForGalleryGenerationProcess;
use App\Processing\Processes\GroupPhotosProcessing\MiniWalletCollagesLocalGenerationProcess;
use App\Processing\Processes\GroupPhotosProcessing\PersonalClassPhotoPreparingProcess;
use App\Processing\Processes\GroupPhotosProcessing\ProofOnePhotoGenerationProcess;
use App\Processing\Processes\GroupPhotosProcessing\SchoolPhotoPreparingProcess;
use App\Processing\Processes\GroupPhotosProcessing\StaffPhotoPreparingProcess;
use App\Processing\Processes\RemovingProcessing\RemoveAllGalleryGroupPhotosProcess;

class GroupPhotosGenerationScenario extends ProcessableScenario
{
    /** @var int */
    protected $galleryId;

    /**
     * GroupPhotosGenerationScenario constructor.
     *
     * @param int                               $galleryId
     * @param string|null                       $initialStatus
     * @param ProcessableScenario|null $scenario
     */
    public function __construct(
        int $galleryId,
        string $initialStatus = null,
        ProcessableScenario $scenario = null
    ) {
        $this->galleryId = $galleryId;

        parent::__construct($galleryId, Gallery::class, $initialStatus, $scenario);
    }

    /**
     * @return mixed|void
     * @throws \Exception
     */
    public function initialize()
    {
        // // Remove old photos
        // $this->addProcesses(
        //     0,
        //     (new RemoveAllGalleryGroupPhotosProcess($this->galleryId, null, $this))
        // );

        // Generate group photos
        $this->addProcesses(
            1,
            (new CommonClassPhotosPreparingProcess($this->galleryId, null, $this))/* ,
            (new StaffPhotoPreparingProcess($this->galleryId, null, $this)),
            (new SchoolPhotoPreparingProcess($this->galleryId, null, $this)),
            (new MiniWalletCollagesLocalGenerationProcess($this->galleryId, null, $this)) */
        );

        // Generate personal class photos and ID cards
        /** @var Person [] $people */
        $people = (new GalleryRepo())->getByID($this->galleryId)->people;
        foreach ($people as $person) {
            /* $this->addProcesses(
                1,
                (new PersonalClassPhotoPreparingProcess($person->id, null, $this))
            ); */

            // Generate ID cards for teachers only
            /* if($person->isStaff()){
                $this->addProcesses(
                    1,
                    (new IDCardsForGalleryGenerationProcess($person->id, null, $this))
                );
            } */

            /* $this->addProcesses(
                1,
                (new ProofOnePhotoGenerationProcess($person->sub_gallery_id, null, $this))
            ); */
        }

        // Move to remote
        /* $this->addProcesses(
            2,
            (new GroupPhotosGalleryMoveToRemote($this->galleryId, null, $this))
        ); */
    }
}
