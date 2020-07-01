<?php


namespace App\Processing\Processes;


use App\Processing\ProcessingStatusesEnum;
use App\Processing\ProcessRecords\ProcessRecord;
use App\Processing\ProcessRecords\ProcessRecordRepo;
use App\Processing\Scenarios\ProcessableScenario;
use App\Processing\Scenarios\ProcessableScenarioInterface;

trait RecordableTrait
{
    /**
     * @var ProcessableScenario
     */
    protected $scenario;
    /**
     * @var int
     */
    protected $processable_id;
    /**
     * @var string
     */
    protected $processable_class;
    /**
     * @var int
     */
    protected $job_id = null;
    /**
     * @var ProcessingStatusesEnum
     */
    protected $status = ProcessingStatusesEnum::IN_PROGRESS;

    /** @var ProcessRecord|null */
    protected $processRecord;

    /**
     * ProcessableProcess constructor.
     *
     * @param int                               $processable_id
     * @param string|null                       $initialStatus
     * @param ProcessableScenario|null $scenario
     * @param string                            $processable_class
     */
    public function __construct(
        int $processable_id,
        string $processable_class,
        string $initialStatus = null,
        ProcessableScenario $scenario = null
    ) {
        $this->processable_id = $processable_id;
        $this->processable_class = $processable_class;
        $this->scenario = $scenario;

        $this->loadRecord();
        $this->setStatus($initialStatus);
    }

    /**
     * Return process record
     *
     * @return ProcessRecord|null
     */
    public function getRecord()
    {
        return $this->processRecord;
    }

    /**
     * Prepare process status
     *
     * @param string|null $initialStatus
     */
    protected function setStatus(string $initialStatus = null)
    {
        // Set initial
        if(!is_null($initialStatus)){
            $this->status = $initialStatus;
            return;
        }

        // Load from record
        if(!is_null($this->processRecord)){
            $this->status = $this->processRecord->status;
            return;
        }

        $this->status = ProcessingStatusesEnum::NEWER_STARTED;
    }

    /**
     * Load Process Record for process
     */
    protected function loadRecord()
    {
        $this->processRecord = (new ProcessRecordRepo())->getForProcess($this);
    }

    /**
     * Return process class
     *
     * @return string
     */
    public function getProcessClass(): string
    {
        return get_class($this);
    }

    /**
     * Return scenario
     *
     * @return string
     */
    public function getScenario(): string
    {
        return get_class($this->scenario);
    }

    /**
     * @return int
     */
    public function getJobId(): int
    {
        return (int)$this->job_id;
    }

    /**
     * Return processable type
     *
     * @return string
     */
    public function getProcessableClass(): string
    {
        return $this->processable_class;
    }

    /**
     * Return processable ID
     *
     * @return int
     */
    public function getProcessableID(): int
    {
        return $this->processable_id;
    }

    /**
     * Update process record
     */
    protected function updateProcessRecord()
    {
        (new ProcessRecordRepo())->updateProcessRecord($this);
    }

    /**
     * Return process status
     *
     * @return ProcessingStatusesEnum
     */
    public function getStatus(): string
    {
        return $this->status;
    }
}
