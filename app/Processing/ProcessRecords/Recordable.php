<?php


namespace App\Processing\ProcessRecords;


use App\Processing\StatusResolvable;

interface Recordable extends StatusResolvable
{
    public function getStatus(): string;
    public function getJobId(): int;
    public function getProcessClass(): string;
    public function getScenario(): string;
    public function getProcessableID(): int;
    public function getProcessableClass(): string ;
}
