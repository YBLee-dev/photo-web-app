<?php


namespace App\Processing\Processes;

use App\Processing\ProcessingStatusesEnum;
use App\Processing\ProcessRecords\ProcessRecordRepo;
use App\Processing\Scenarios\ProcessableScenario;

class ContinueScenarioProcess extends ProcessableProcess
{
    /** @var ProcessableScenario */
    protected $scenarioToContinue;

    /**
     * ContinueScenarioProcess constructor.
     *
     * @param ProcessableScenario $scenarioToContinue
     * @param string|null                       $initialStatus
     * @param ProcessableScenario|null $scenario
     */
    public function __construct(
        ProcessableScenario $scenarioToContinue,
        string $initialStatus = null,
        ProcessableScenario $scenario = null
    ) {
        $this->scenarioToContinue = $scenarioToContinue->getNotInitializedCopy();

        parent::__construct($scenarioToContinue->getProcessableID(), get_class($scenarioToContinue), $initialStatus, $scenario);
    }


    /**
     * Do process logic
     *
     * @return mixed
     * @throws \Exception
     */
    protected function processLogic()
    {
        $this->scenarioToContinue->continue();
    }
}
