<?php


namespace App\Processing\Processes\GroupPhotosProcessing;

use App\Photos\GroupPhotosGeneration\GroupPhotoGenerator;
use App\Photos\People\Person;
use App\Photos\People\PersonRepo;
use App\Processing\Processes\ProcessableProcess;
use App\Processing\Scenarios\ProcessableScenario;
use ImagickException;
use Laracasts\Presenter\Exceptions\PresenterException;

class PersonalClassPhotoPreparingProcess extends ProcessableProcess
{
    /**
     * PersonalClassPhotoPreparingProcess constructor.
     *
     * @param int                 $processable_id
     * @param string|null         $initialStatus
     * @param ProcessableScenario $scenario
     */
    public function __construct(
        int $processable_id,
        string $initialStatus = null,
        ProcessableScenario $scenario = null
    ) {
        parent::__construct($processable_id, Person::class, $initialStatus, $scenario);
    }


    /**
     * Do process logic
     *
     * @return mixed
     * @throws ImagickException
     * @throws PresenterException
     */
    protected function processLogic()
    {
        /** @var Person $person */
        $person = (new PersonRepo())->getByID($this->processable_id);
        (new GroupPhotoGenerator())->personalClassPhotoGenerateIfNeeded($person->subgallery->gallery, $person);
    }
}
