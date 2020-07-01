<?php


namespace App\Processing\Processes\OrdersProcessing;


use App\Ecommerce\Orders\Order;
use App\Ecommerce\Orders\OrderExportService;

class ZipForDigitalOrderPreparingProcess extends OrderProcessingProcess
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
        (new OrderExportService())->prepareDigitalOrderZip($order);
    }
}
