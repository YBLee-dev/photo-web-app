<?php


namespace App\Processing\Processes\OrdersProcessing;


use App\Ecommerce\Orders\Order;
use App\Ecommerce\Orders\OrderStorageManager;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Laracasts\Presenter\Exceptions\PresenterException;

class MoveDigitalOrderFilesToRemoteProcess extends OrderProcessingProcess
{

    /**
     * Do process logic
     *
     * @return mixed
     * @throws FileNotFoundException
     * @throws PresenterException
     */
    protected function processLogic()
    {
        /** @var Order $order */
        $order = $this->getOrder();
        (new OrderStorageManager())->moveDigitalOrderDataToRemote($order);
    }
}
