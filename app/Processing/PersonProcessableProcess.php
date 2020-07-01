<?php


namespace App\Processing;


use App\Photos\People\Person;
use App\Photos\People\PersonRepo;
use App\Processing\Processes\ProcessableProcess;
use App\Processing\Scenarios\ProcessableScenario;

abstract class PersonProcessableProcess extends ProcessableProcess
{
    /**
     * PersonProcessableProcess constructor.
     *
     * @param int                      $processable_id
     * @param string|null              $initialStatus
     * @param ProcessableScenario|null $scenario
     */
    public function __construct(
        int $processable_id,
        string $initialStatus = null,
        ProcessableScenario $scenario = null
    ) {
        parent::__construct($processable_id, Person::class, $initialStatus, $scenario);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Model|null|Person
     * @throws \Exception
     */
    protected function getPerson()
    {
        return (new PersonRepo())->getByID($this->processable_id);
    }

}
