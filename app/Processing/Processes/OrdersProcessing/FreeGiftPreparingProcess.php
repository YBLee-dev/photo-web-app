<?php


namespace App\Processing\Processes\OrdersProcessing;

use App\Photos\People\Person;
use App\Photos\People\PersonRepo;
use App\Photos\TemplatedPhotosGeneration\TemplatedPhotoGenerator;
use App\Processing\Processes\ProcessableProcess;
use App\Processing\Scenarios\ProcessableScenarioInterface;
use Laracasts\Presenter\Exceptions\PresenterException;

class FreeGiftPreparingProcess extends ProcessableProcess
{
    /**
     * FreeGiftPreparingProcess constructor.
     *
     * @param int                               $personId
     * @param string|null                       $initialStatus
     * @param ProcessableScenarioInterface|null $scenario
     */
    public function __construct(
        int $personId,
        string $initialStatus = null,
        ProcessableScenarioInterface $scenario = null
    ) {
        parent::__construct($personId, Person::class, $initialStatus, $scenario);
    }


    /**
     * Do process logic
     *
     * @return mixed
     * @throws PresenterException
     */
    protected function processLogic()
    {
        /** @var Person $person */
        $person = (new PersonRepo())->getByID($this->processable_id);

        (new TemplatedPhotoGenerator())->freeGiftGenerate($person);
    }
}
