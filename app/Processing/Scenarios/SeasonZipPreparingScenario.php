<?php


namespace App\Processing\Scenarios;


use App\Ecommerce\Orders\Order;
use App\Photos\Seasons\Season;
use App\Photos\Seasons\SeasonRepo;
use App\Processing\Processes\OrdersProcessing\FreeGiftPreparingProcess;
use App\Processing\Processes\OrdersProcessing\MoveSeasonZipToRemoteProcess;
use App\Processing\Processes\OrdersProcessing\PrintableOrderPhotosGenerationProcess;
use App\Processing\Processes\OrdersProcessing\ZipForSeasonPreparingProcess;
use App\Processing\Processes\RemovingProcessing\ClearTmpStorageAfterSeasonExport;

class SeasonZipPreparingScenario extends ProcessableScenario
{
    /** @var int $seasonId */
    protected $seasonId;

    /**
     * SeasonZipPreparingScenario constructor.
     *
     * @param int $seasonId
     * @param string|null $initialStatus
     * @param ProcessableScenarioInterface|null $scenario
     */
    public function __construct(
        int $seasonId,
        string $initialStatus = null,
        ProcessableScenarioInterface $scenario = null
    ) {
        $this->seasonId = $seasonId;

        parent::__construct($seasonId, Season::class, $initialStatus, $scenario);
    }

    /**
     * Initialize and add all needed processes to processes list
     *
     * @return mixed
     * @throws \Exception
     */
    public function initialize()
    {
        $season = (new SeasonRepo())->getByID($this->seasonId);
        /** @var Order [] $orders */
        $orders = $season->gallery->orders;

        foreach ($orders as $order){
            // Add free gift if included
            if ($order->isFreeGiftIncluded() && !$order->subGallery->person->isFreeGiftReady()){
                $this->addProcesses(
                    0,
                    new FreeGiftPreparingProcess($order->subGallery->person->id, null, $this)
                );
            }

            // Printable photos generation
            $this->addProcesses(
                0,
                new PrintableOrderPhotosGenerationProcess($order->id, null, $this)
            );
        }

        // Prepare ZIP for season
        $this->addProcesses(
            1,
            new ZipForSeasonPreparingProcess($this->seasonId, null, $this)
        );

        // Move all orders data to remote storage
        $this->addProcesses(
            2,
            new MoveSeasonZipToRemoteProcess($this->seasonId, null, $this)
        );

        // Clear tmp local storage
        $this->addProcesses(
            3,
            new ClearTmpStorageAfterSeasonExport($this->seasonId, null, $this)
        );
    }
}
