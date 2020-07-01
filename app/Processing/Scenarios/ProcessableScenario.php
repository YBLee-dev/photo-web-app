<?php


namespace App\Processing\Scenarios;

use App\Processing\Processes\ProcessableProcess;
use App\Processing\Processes\RecordableTrait;
use App\Processing\ProcessingStatusesEnum;
use App\Processing\ProcessRecords\ProcessRecordRepo;
use App\Processing\ProcessRecords\Recordable;
use App\Processing\StatusResolver;

abstract class ProcessableScenario implements ProcessableScenarioInterface, Recordable
{
    use RecordableTrait;

    /** @var array Defined processes list */
    protected $processesList = [];

    /**
     * Initialize and add all needed processes to processes list
     *
     * @return mixed
     */
    abstract public function initialize();

    /**
     * Empty processes list
     */
    protected function resetProcessesList()
    {
        $this->processesList = [];
    }

    /**
     * Actualize status based on record and return it
     *
     * @return ProcessingStatusesEnum|string
     */
    public function getActualStatus()
    {
        $this->loadRecord();
        $this->setStatus();

        return $this->getStatus();
    }

    /**
     * Return list of processes grouped by processes class with short statuses
     *
     * @return array
     */
    public function getUniqueProcessesListWithShortStatuses(bool $withProgress = false)
    {
        $this->initialize();

        if(!count($this->processesList)){
            return [];
        }

        ksort($this->processesList);
        $processesList = collect($this->processesList);

        $grouped = $processesList->collapse()->groupBy(function (ProcessableProcess $process){
            return get_class($process);
        });

        $resultList = [];
        foreach ($grouped as $processClass => $group) {
            $groupStatus = call_user_func_array([$this, 'resolveStatusForProcesses'], $group->all());

            $resultList[$processClass] = $groupStatus;
        }

        return $resultList;
    }

    /**
     * Add process
     *
     * @param int                $turnPackPosition
     * @param ProcessableProcess ...$processes
     */
    protected function addProcesses(int $turnPackPosition, ProcessableProcess ... $processes)
    {
        foreach ($processes as $process) {
            if(empty($this->processesList[$turnPackPosition])) {
                $this->processesList[$turnPackPosition] = [$process];
            } else {
                $this->processesList[$turnPackPosition][] = $process;
            }
        }
    }

    /**
     * Start scenario processing
     *
     * @return mixed|void
     */
    public function start()
    {
        // Update record
        $this->updateStatus(ProcessingStatusesEnum::IN_PROGRESS);
        $this->initialize();

        // Do nothing if no tasks
        if(count($this->processesList) == 0){
            logger('No processes added. '.get_class($this));
            $this->updateStatus(ProcessingStatusesEnum::FINISHED);
            return;
        }

        $this->setWaitAllProcesses();

        // Start processing
        $firstPackPosition = $this->getFirstProcessPackPosition();
        $this->startProcessesPack($firstPackPosition);
    }

    /**
     * @param string $status
     */
    public function updateStatus(string $status)
    {
        $this->status = $status;
        $this->updateProcessRecord();
    }

    /**
     * Return first processes pack
     *
     * @return mixed
     */
    protected function getFirstProcessPackPosition(): int
    {
        // Sort to be sure that we start from first
        $processesList = $this->processesList;
        ksort($processesList);
        $keys = array_keys($processesList);

        return array_first($keys);
    }

    /**
     * Continue scenario
     *
     * @throws \Exception
     */
    public function continue()
    {
        // Continue scenario
        $this->resetProcessesList();
        $this->initialize();

        // Don not continue scenario if we can't
        if(!$this->mayBeContinued()){
            return false;
        }

        //Update record
        $this->updateStatus(ProcessingStatusesEnum::IN_PROGRESS);

        $finishedGroupsCounter = 0;
        foreach ($this->processesList as $turnPackPosition => $processesPack) {
            $processesPackStatus = $this->resolveProcessesTurnStatus($turnPackPosition);

            // Mark as failed if one of processes failed
            if(ProcessingStatusesEnum::FAILED()->is($processesPackStatus)){
                $this->setFailed();
                return false;
            }

            // Looking for first not started pack
            if(ProcessingStatusesEnum::WAIT()->is($processesPackStatus) || ProcessingStatusesEnum::NEWER_STARTED()->is($processesPackStatus)) {

                // Start current not started pack if previous is finished
                $previousPackStatus = $this->resolveProcessesTurnStatus($turnPackPosition - 1);
                if (ProcessingStatusesEnum::FINISHED()->is($previousPackStatus)) {
                    $this->startProcessesPack($turnPackPosition);

                    return true;
                }
            }

            $finishedGroupsCounter += ProcessingStatusesEnum::FINISHED()->is($processesPackStatus);
        }

        // Update status to finished when all processes are done
        if($finishedGroupsCounter >= count($this->processesList))
        {
            $this->finish();

            $this->finishedSuccessfully();
        }

        return true;
    }

    /**
     * Restart failed scenario
     * All failed processes will be started again
     */
    public function retry()
    {
        // Continue scenario
        $this->resetProcessesList();
        $this->initialize();

        $this->updateStatus(ProcessingStatusesEnum::IN_PROGRESS);

        /** @var ProcessableProcess [] $processes */
        $processes = array_collapse($this->processesList);

        foreach ($processes as $process){
            if(ProcessingStatusesEnum::FAILED()->is($process->getStatus())){
                $process->start();
            }
        }
    }

    /**
     * @return ProcessableScenario
     */
    public function getNotInitializedCopy()
    {
        $cloned = clone $this;

        $cloned->resetProcessesList();

        return $cloned;
    }

    /**
     * Check if scenario can be continued
     *
     * @return mixed
     */
    protected function mayBeContinued()
    {
        return ProcessingStatusesEnum::IN_PROGRESS()->is($this->getStatus());
    }

    /**
     * Set scenario as failed
     */
    public function setFailed()
    {
        $this->updateStatus(ProcessingStatusesEnum::FAILED);
    }

    /**
     * @throws \Exception
     */
    public function finishManually()
    {
        $this->initialize();
        $this->finish();
    }

    /**
     * Finish scenario and delete all processes
     *
     * @throws \Exception
     */
    protected function finish()
    {
        $this->updateStatus(ProcessingStatusesEnum::FINISHED);

        $this->cleanUp();
    }

    /**
     * Remove all finished processes after scenario finish
     *
     * @throws \Exception
     */
    protected function cleanUp()
    {
        $processRecordRepo = new ProcessRecordRepo();

        // Remove all processing processes
        $processes = $this->processesList;
        $processes = array_collapse($processes);

        call_user_func_array([$processRecordRepo, 'deleteProcesses'], $processes);

        // Remove continue process
        $processRecords = $processRecordRepo->getForProcessable($this->getProcessableID(), get_class($this));
        if($processRecords){
            call_user_func_array([$processRecordRepo, 'deleteRecords'], $processRecords->all());
        }
    }

    /**
     * Success full finishing
     */
    protected function finishedSuccessfully()
    {
        //
    }

    /**
     * Start all process in processes pack
     *
     * @param int $turnPackPosition
     *
     * @return bool
     */
    protected function startProcessesPack(int $turnPackPosition)
    {
        if (empty($this->processesList[$turnPackPosition])) {
            return false;
        }

        foreach ($this->processesList[$turnPackPosition] as $process) {
            $process->start();
        }

        return true;
    }

    /**
     * Set all processes as WAIT
     */
    protected function setWaitAllProcesses()
    {
        foreach ($this->processesList as $processPack) {
            foreach ($processPack as $process){
                $process->wait();
            }
        }
    }

    /**
     * Return processes turn pack short status
     *
     * @param int $turnPackPosition
     *
     * @return string
     */
    protected function resolveProcessesTurnStatus(int $turnPackPosition)
    {
        // Return finished if we have not processes for current turn
        if(empty($this->processesList[$turnPackPosition])) {
            return ProcessingStatusesEnum::FINISHED;
        }

        $processes = $this->processesList[$turnPackPosition];

        return call_user_func_array([$this, 'resolveStatusForProcesses'], $processes);
    }

    /**
     * Resolve short status for group of processes
     *
     * @param ProcessableProcess ...$processes
     *
     * @return string
     */
    protected function resolveStatusForProcesses(ProcessableProcess ... $processes)
    {
        return  call_user_func_array([(new StatusResolver()), 'resolveShortStatus'], $processes);
    }
}
