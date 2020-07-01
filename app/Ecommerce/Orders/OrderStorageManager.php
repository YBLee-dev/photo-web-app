<?php


namespace App\Ecommerce\Orders;


use App\Core\StorageManager;
use App\Photos\Photos\PhotoStorageManager;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Laracasts\Presenter\Exceptions\PresenterException;

/**
 * Class OrderStorageManager
 *
 * @package App\Ecommerce\Orders
 * @method oneOrderZipsPrepareLocalDir()
 * @method oneOrderDigitalZipsPrepareLocalDir()
 * @method oneOrderDetailsCsvPrepareLocalDir()
 */
class OrderStorageManager extends StorageManager
{
    /** @var string  */
    protected $pathsManager = OrderPathsManager::class;

    /**
     * @param Order $order
     *
     * @throws PresenterException
     */
    public function deleteOrderDetailsLocalCsvFile(Order $order)
    {
        $this->getLocalTMPStorage()->delete($order->present()->csvDetailsBasePath());
    }

    /**
     * @param Order $order
     *
     * @throws PresenterException
     * @throws FileNotFoundException
     */
    public function moveAllOrderDataToRemote(Order $order)
    {
        // Move gift to remote
        $freeGift = $order->subGallery->person->freeGift();
        if($freeGift){
            $this->moveFileToRemote($freeGift->present()->originalBasePath());
        }

        // Move printable photos to remote
        $orderPhotos = $order->printablePhotos;
        call_user_func_array([new PhotoStorageManager(), 'movePhotosToRemote'], $orderPhotos->all());

        // Move ZIP file to remote
        $this->moveFileToRemote($order->present()->zipBasePath());
    }

    /**
     * @param \App\Ecommerce\Orders\Order $order
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function moveDigitalOrderDataToRemote(Order $order)
    {
        if($order->isDigitalFull() || $order->isDigital()) {
            // Move ZIP file to remote
            $this->moveFileToRemote($order->present()->zipDigitalBasePath());
        }
    }

    /**
     * @param Order $order
     */
    public function copyAllPhotoForOrderGenerationOnLocal(Order $order)
    {
        /** @var OrderItem [] $orderItems */
        $orderItems = $order->items;

        foreach ($orderItems as $item){
            $photos = $item->originalPhotos;
            call_user_func_array([new PhotoStorageManager(), 'copyPhotosToLocal'], $photos->all());
        }
    }

    /**
     * @param Order $order
     *
     * @return bool
     * @throws PresenterException
     */
    public function isOrderZipFileExists(Order $order)
    {
        return $this->getRemoteStorage()->exists($order->present()->zipBasePath());
    }

    /**
     * @param Order $order
     *
     * @return bool
     * @throws PresenterException
     */
    public function isOrderDigitalZipFileExists(Order $order)
    {
        return $this->getRemoteStorage()->exists($order->present()->zipDigitalBasePath());
    }
}
