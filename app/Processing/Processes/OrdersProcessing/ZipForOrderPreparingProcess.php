<?php


namespace App\Processing\Processes\OrdersProcessing;


use App\Ecommerce\Orders\Order;
use App\Ecommerce\Orders\OrderExportService;

class ZipForOrderPreparingProcess extends OrderProcessingProcess
{

    /**
     * Do process logic
     *
     * @return mixed
     * @throws \Exception
     */
    protected function processLogic()
    {
        /** @var Order $order */
        $order = $this->getOrder();
        (new OrderExportService())->prepareOrderZip($order);
    }
}
