<?php


namespace App\Processing\Processes\OrdersProcessing;


use App\Ecommerce\Orders\Order;
use App\Photos\Seasons\Season;
use App\Photos\Seasons\SeasonExportService;
use App\Photos\Seasons\SeasonRepo;
use App\Processing\Processes\ProcessableProcess;
use App\Processing\Scenarios\ProcessableScenarioInterface;

class ZipForSeasonPreparingProcess extends ProcessableProcess
{
    /**
     * ZipForSeasonPreparingProcess constructor.
     * @param int $processable_id
     * @param string|null $initialStatus
     * @param ProcessableScenarioInterface|null $scenario
     */
    public function __construct(
        int $processable_id,
        string $initialStatus = null,
        ProcessableScenarioInterface $scenario = null
    ) {
        parent::__construct($processable_id, Season::class, $initialStatus, $scenario);
    }

    /**
     * Do process logic
     *
     * @return mixed
     * @throws \Exception
     */
    protected function processLogic()
    {
        $season = (new SeasonRepo())->getByID($this->processable_id);
        /** @var Season $season */
        (new SeasonExportService())->prepareSeasonZip($season);
    }
}
