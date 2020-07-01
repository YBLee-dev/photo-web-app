<?php


namespace App\Processing;


use App\Processing\Processes\ContinueScenarioProcess;
use App\Processing\Scenarios\DefaultScenario;
use App\Processing\Scenarios\ProcessableScenario;
use App\Processing\Scenarios\ScenarioProcess;

class ProcessFinishedListener
{
    /**
     * Continue scenario for process
     *
     * @param ProcessFinishedEvent $event
     *
     * @return bool
     */
    public function handle(ProcessFinishedEvent $event)
    {
        $process = $event->getProcess();

        if ($process instanceof ScenarioProcess) {

            /** @var ProcessableScenario $scenario */
            $scenario = $process->getActiveScenario();

            if($scenario instanceof DefaultScenario){
                return false;
            }

            (new ContinueScenarioProcess($scenario))->start();
        }
    }
}
