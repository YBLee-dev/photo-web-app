<?php


namespace App\Processing\ProcessRecords;


use App\Processing\ProcessingStatusesEnum;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Webmagic\Core\Entity\EntityRepo;

class ProcessRecordRepo extends EntityRepo
{
    /** @var string Entity */
    protected $entity = ProcessRecord::class;

    /**
     * Update process record or create new
     *
     * @param Recordable $recordable
     */
    public function updateProcessRecord(Recordable $recordable)
    {
        ProcessRecord::updateOrCreate(
            [
                'process' => $recordable->getProcessClass(),
                'processable_id' => $recordable->getProcessableID(),
                'processable_type' => $recordable->getProcessableClass(),
            ],
            [
                'status' => $recordable->getStatus(),
                'job_id' => $recordable->getJobID(),
                'scenario' => $recordable->getScenario()
            ]
        );
    }

    /**
     * @param string $scenarioClass
     *
     * @throws Exception
     */
    public function deleteAllProcessesByScenario(string $scenarioClass)
    {
        $query = $this->query();
        $query = $this->addScenarioFilter($query, $scenarioClass);

        $query->delete();
    }

    /**
     * Return process record for process
     *
     * @param Recordable $recordable
     *
     * @return mixed
     */
    public function getForProcess(Recordable $recordable)
    {
        $query = ProcessRecord::where('process', $recordable->getProcessClass());

        $query = $this->addProcessableFilter($query, $recordable->getProcessableID(), $recordable->getProcessableClass());

        return $this->realGetOne($query);
    }

    /**
     * Return all records fro processable
     *
     * @param int    $processableId
     * @param string $processableClass
     *
     * @return LengthAwarePaginator|Collection
     * @throws Exception
     */
    public function getForProcessable(int $processableId, string $processableClass)
    {
        $query = $this->query();

        $query = $this->addProcessableFilter($query, $processableId, $processableClass);

        return $this->realGetMany($query);
    }

    /**
     * Count process records
     *
     * @param string $scenarioClass
     *
     * @return mixed
     */
    public function countScenarioActiveProcesses(string $scenarioClass)
    {
        $query = ProcessRecord::where('status', ProcessingStatusesEnum::IN_PROGRESS()->label());

        $query = $this->addScenarioFilter($query, $scenarioClass);

        return $query->count();
    }

    /**
     * Return all processes associated with scenario and processable entity
     *
     * @param string $scenarioClass
     *
     * @return LengthAwarePaginator|Collection
     * @throws Exception
     */
    public function getForProcessableByScenario(string $scenarioClass, int $processedId, string $processedClass)
    {
        $query = $this->addScenarioFilter($this->query(), $scenarioClass);
        $query = $this->addProcessableFilter($query, $processedId, $processedClass);

        return $this->realGetMany($query);
    }

    /**
     * @param int    $processableId
     * @param string $processableClass
     * @param string $processClass
     *
     * @return LengthAwarePaginator|Collection
     * @throws Exception
     */
    public function getForProcessAndProcessable(int $processableId, string $processableClass, string $processClass)
    {
        $query = $this->addProcessableFilter($this->query(), $processableId, $processableClass);
        $query = $query->where('process', $processClass);

        return $this->realGetMany($query);
    }

    /**
     * Return all processes associated with scenario and processable entity
     *
     * @param string $scenarioClass
     *
     * @return LengthAwarePaginator|Collection
     * @throws Exception
     */
    public function getForScenario(string $scenarioClass)
    {
        $query = $this->addScenarioFilter($this->query(), $scenarioClass);

        return $this->realGetMany($query);
    }

    /**
     * @param Builder $query
     * @param string  $scenarioClass
     *
     * @return Builder
     */
    protected function addScenarioFilter(Builder $query, string $scenarioClass)
    {
        return $query->where('scenario', $scenarioClass);
    }

    /**
     * @param Builder $query
     * @param int     $processableId
     * @param string  $processableType
     *
     * @return Builder
     */
    protected function addProcessableFilter(Builder $query, int $processableId, string $processableType)
    {
        return $query
            ->where('processable_id', $processableId)
            ->where('processable_type', $processableType);
    }

    /**
     * Remove all records by recordables
     *
     * @param Recordable ...$recordables
     *
     * @throws Exception
     */
    public function deleteProcesses(Recordable ...$recordables)
    {
        foreach ($recordables as $recordable){
            $record = $this->addProcessableFilter($this->query(), $recordable->getProcessableID(), $recordable->getProcessableClass())->first();

            if($record){
                $record->delete();
            }
        }
    }

    /**
     * @param ProcessRecord ...$records
     *
     * @throws Exception
     */
    public function deleteRecords(ProcessRecord ... $records)
    {
        foreach ($records as $record){
            $record->delete();
        }
    }
}
