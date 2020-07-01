<?php


namespace App\Processing\Scenarios;


use App\Processing\ProcessRecords\ProcessRecordRepo;

class DefaultScenario extends ProcessableScenario
{
    /**
     * DefaultScenario constructor.
     */
    public function __construct() {
        parent::__construct(0, '', null);
    }


    /**
     * Initialize and add all needed processes to processes list
     *
     * @return mixed
     */
    public function initialize()
    {
        return true;
    }

    /**
     * @throws \Exception
     */
    protected function cleanUp()
    {
        parent::cleanUp();

        // Delete not needed record to be clean
        (new ProcessRecordRepo())->deleteProcesses($this);
    }


}
