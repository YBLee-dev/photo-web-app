<?php


namespace App\Processing;


use App\Processing\Processes\ProcessableProcess;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ProcessFinishedEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    /**
     * @var ProcessableProcess
     */
    private $process;

    /**
     * Create a new event instance.
     *
     * @param ProcessableProcess $process
     */
    public function __construct(ProcessableProcess $process)
    {
        $this->process = $process;
    }

    /**
     * @return ProcessableProcess
     */
    public function getProcess(): ProcessableProcess
    {
        return $this->process;
    }
}
