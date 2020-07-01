<?php


namespace App\Processing\Processes\OrdersProcessing;


use App\Ecommerce\Orders\Order;
use App\Ecommerce\Orders\OrderRepo;
use App\Processing\Processes\ProcessableProcess;
use App\Processing\Scenarios\ProcessableScenarioInterface;

abstract class OrderProcessingProcess extends ProcessableProcess
{
    /**
     * OrderProcessingProcess constructor.
     *
     * @param int                               $processable_id
     * @param string|null                       $initialStatus
     * @param ProcessableScenarioInterface|null $scenario
     */
    public function __construct(
        int $processable_id,
        string $initialStatus = null,
        ProcessableScenarioInterface $scenario = null
    ) {
        parent::__construct($processable_id, Order::class, $initialStatus, $scenario);
    }

    /**
     * @return Order|null
     * @throws \Exception
     */
    protected function getOrder()
    {
        return (new OrderRepo())->getByID($this->processable_id);
    }


}
