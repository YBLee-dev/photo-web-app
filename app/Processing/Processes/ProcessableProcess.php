<?php

namespace App\Processing\Processes;

use App\Processing\Core\Processable;
use App\Processing\ProcessFinishedEvent;
use App\Processing\ProcessingStatusesEnum;
use App\Processing\ProcessRecords\Recordable;
use App\Processing\Scenarios\DefaultScenario;
use App\Processing\Scenarios\ProcessableScenario;
use App\Processing\Scenarios\ProcessableScenarioInterface;
use App\Processing\Scenarios\ScenarioProcess;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

abstract class ProcessableProcess implements Processable, ShouldQueue, Recordable, ScenarioProcess
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels, RecordableTrait { __construct as protected construct; }

    /**
     * ProcessableProcess constructor.
     *
     * @param int                               $processable_id
     * @param string                            $processable_class
     * @param string|null                       $initialStatus
     * @param ProcessableScenario|null $scenario
     */
    public function __construct(
        int $processable_id,
        string $processable_class,
        string $initialStatus = null,
        ProcessableScenario $scenario = null
    ) {

        $this->construct($processable_id, $processable_class, $initialStatus, $scenario);

        $this->prepareScenario($scenario);
    }

    /**
     * Prepare scenario
     *
     * @param ProcessableScenario|null $scenario
     *
     * @return DefaultScenario|ProcessableScenario|null
     */
    protected function prepareScenario(ProcessableScenario $scenario = null)
    {
        if(is_null($scenario)) {
            $this->scenario = new DefaultScenario();

            return $this->scenario;
        }

        $this->scenario = $scenario->getNotInitializedCopy();

        return $this->scenario;
    }


    /**
     * Job handle function
     *
     * @return mixed
     */
    public function handle()
    {
        // Don not start the process if scenario is finished
        if($this->isScenarioFinishedOrFailed()){
            $this->updateStatus(ProcessingStatusesEnum::WAIT);
            return false;
        }

        $this->updateStatus(ProcessingStatusesEnum::IN_PROGRESS);

        $this->processLogic();

        $this->successFinish();
    }

    /**
     * Update process status
     *
     * @param $status
     */
    protected function updateStatus($status)
    {
        $this->status = ProcessingStatusesEnum::IN_PROGRESS;
        $this->updateProcessRecord();
    }

    /**
     * @return bool
     */
    protected function isScenarioFinishedOrFailed()
    {
        if(empty($this->scenario) || $this->scenario instanceof DefaultScenario){
            return false;
        }

        $scenarioActualStatus = $this->scenario->getActualStatus();

        if(ProcessingStatusesEnum::FINISHED()->is($scenarioActualStatus)) {
            return true;
        }

        return ProcessingStatusesEnum::FAILED()->is($scenarioActualStatus);
    }

    /**
     * Return active scenario
     *
     * @return ProcessableScenarioInterface
     */
    public function getActiveScenario(): ProcessableScenarioInterface
    {
        return $this->scenario;
    }

    /**
     * Start process
     *
     * @return bool
     */
    public function start()
    {
        // Do not start processes which are at the queue or in progress already
        if($this->isAlreadyStarted())
        {
            return false;
        }

        dispatch($this);

        $this->status = ProcessingStatusesEnum::IN_QUEUE;

        $this->updateProcessRecord();

        return true;
    }

    /**
     * Check if current process was already started
     *
     * @return bool
     */
    protected function isAlreadyStarted()
    {
        //Process is started when in the queue
        if (ProcessingStatusesEnum::IN_QUEUE()->is($this->getStatus())) {
            return true;
        }

        return  ProcessingStatusesEnum::IN_PROGRESS()->is($this->getStatus());
    }

    /**
     * Finish process with success
     *
     * @return mixed
     */
    public function successFinish()
    {
        $this->status = ProcessingStatusesEnum::FINISHED;

        $this->updateProcessRecord();

        event(new ProcessFinishedEvent($this));
    }

    /**
     * Save process but don't start it
     *
     * @return mixed
     */
    public function wait()
    {
        $this->status = ProcessingStatusesEnum::WAIT;

        $this->updateProcessRecord();
    }

    /**
     * Do process logic
     *
     * @return mixed
     */
    abstract protected function processLogic();

    /**
     * The job failed to process.
     *
     * @return void
     */
    public function failed()
    {
        $this->status = ProcessingStatusesEnum::FAILED;

        $this->updateProcessRecord();

        // Update scenario status
        if($this->hasScenario()){
            $this->scenario->setFailed();
        }
    }

    /**
     * @return bool
     */
    protected function hasScenario()
    {
        return isset($this->scenario);
    }
}
