<?php


namespace App\Processing;


use App\Processing\Core\Processable;

class ProcessingManager
{
    public function startProcess(Processable $process)
    {
        $process->start();
    }
}
