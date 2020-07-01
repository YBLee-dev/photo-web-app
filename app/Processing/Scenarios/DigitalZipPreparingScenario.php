<?php


namespace App\Processing\Scenarios;


use App\Ecommerce\Orders\Order;
use App\Ecommerce\Orders\OrderRepo;
use App\Processing\Processes\OrdersProcessing\FreeGiftPreparingProcess;
use App\Processing\Processes\OrdersProcessing\MoveDigitalOrderFilesToRemoteProcess;
use App\Processing\Processes\OrdersProcessing\ZipForDigitalOrderPreparingProcess;


class DigitalZipPreparingScenario extends ProcessableScenario
{
    /** @var int */
    protected $orderId;

    /**
     * OrderZipPreparingScenario constructor.
     *
     * @param int                               $orderId
     * @param string|null                       $initialStatus
     * @param ProcessableScenarioInterface|null $scenario
     */
    public function __construct(
        int $orderId,
        string $initialStatus = null,
        ProcessableScenarioInterface $scenario = null
    ) {
        $this->orderId = $orderId;

        parent::__construct($orderId, Order::class, $initialStatus, $scenario);
    }

    /**
     * Initialize and add all needed processes to processes list
     *
     * @return mixed
     * @throws \Exception
     */
    public function initialize()
    {
        /** @var Order $order */
        $order = (new OrderRepo())->getByID($this->orderId);

        // Add free gift if included
        if ($order->isFreeGiftIncluded() && !$order->subGallery->person->isFreeGiftReady()){
            $this->addProcesses(
                0,
                new FreeGiftPreparingProcess($order->subGallery->person->id, null, $this)
            );
        }

        // Prepare ZIP for order
        $this->addProcesses(
            1,
            new ZipForDigitalOrderPreparingProcess($this->orderId, null, $this)
        );

        // Move all order data to remote storage
        $this->addProcesses(
            2,
            new MoveDigitalOrderFilesToRemoteProcess($this->orderId, null, $this)
        );
    }
}
