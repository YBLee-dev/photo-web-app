<?php


namespace App\Processing\Scenarios;


interface ScenarioProcess
{
    /**
     * Return active scenario
     *
     * @return ProcessableScenarioInterface
     */
    public function getActiveScenario(): ProcessableScenarioInterface;
}
