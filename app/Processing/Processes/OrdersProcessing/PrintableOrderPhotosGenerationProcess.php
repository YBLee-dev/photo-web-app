<?php


namespace App\Processing\Processes\OrdersProcessing;


use App\Ecommerce\Orders\Order;
use App\Photos\PrintablePhotosGeneration\PrintablePhotosGenerator;

class PrintableOrderPhotosGenerationProcess extends OrderProcessingProcess
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
        (new PrintablePhotosGenerator())->generatePrintablePhotosForOrder($order);
    }
}
