<?php


namespace App\Processing\Scenarios;


interface ProcessableScenarioInterface
{
    public function initialize();

    public function start();

    public function continue();
}
