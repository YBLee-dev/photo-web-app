<?php


namespace App\Processing\Processes\RemovingProcessing;


use App\Ecommerce\Orders\Order;
use App\Photos\Galleries\Gallery;
use App\Photos\Photos\PhotoRepo;
use App\Photos\Photos\PhotoStorageManager;
use App\Photos\Seasons\Season;
use App\Photos\Seasons\SeasonRepo;
use App\Photos\Seasons\SeasonStorageManager;
use App\Processing\Processes\ProcessableProcess;
use App\Processing\Scenarios\ProcessableScenarioInterface;

class ClearTmpStorageAfterSeasonExport extends ProcessableProcess
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
        /** @var Season $season */
        $season = (new SeasonRepo())->getByID($this->processable_id);

        /** @var Order [] $orders */
        $orders = $season->gallery->orders;

        foreach ($orders as $order) {
            call_user_func_array([(new PhotoStorageManager()), 'deleteLocalPhotos'], $order->printablePhotos->all());
        }

        $storageManager = new SeasonStorageManager();
        $storageManager->deleteSeasonDetailsLocalFiles($season);
    }
}