<?php


namespace App\Ecommerce\Orders;


use App\Core\PathsManager;

/**
 * Class OrderPathsManager
 *
 * @package App\Ecommerce\Orders
 *
// * @method string commonZipsBaseDir()
// * @method string commonZipsLocalPath(Order $order)
// * @method string commonZipsLocalBasePath(Order $order)
// * @method string commonZipsUrl(Order $order)
 */
class OrderPathsManager extends PathsManager
{
    /** @var string  */
    protected $rootPrefix = 'orders';

    protected $directories = [
       'oneOrderZips' => 'one-order-zips',
       'commonZips' => 'common-zips',
       'orderDetailsExport' => 'order-details',
       'oneOrderDigitalZips' => 'digital-zips'
    ];


    /**
     * @return string
     */
    public function oneOrderDetailsCsvBaseDir()
    {
        return $this->preparePath([$this->directories['orderDetailsExport']]);
    }

    /**
     * @param Order $order
     *
     * @return string
     */
    public function oneOrderDetailsCsvLocalPath(Order $order)
    {
        return $this->generateLocalPath($this->oneOrderDetailsCsvBasePath($order));
    }

    /**
     * @param Order $order
     *
     * @return string
     */
    public function oneOrderDetailsCsvBasePath(Order $order)
    {
        $fileName = "Order_{$order->id}_export ".today()->format('Y-m-d').'.csv';

        return $this->preparePath([$this->oneOrderDetailsCsvBaseDir(), $fileName]);
    }

    /**
     * @return string
     */
    public function oneOrderZipsBaseDir()
    {
        return $this->preparePath([$this->directories['oneOrderZips']]);
    }

    /**
     * @param Order $order
     *
     * @return string
     */
    public function oneOrderZipsLocalPath(Order $order)
    {
        $fileName = $this->prepareOneOrderZipFileName($order);

        return $this->generateLocalPath($this->oneOrderZipsBaseDir(), $fileName);
    }

    /**
     * @param Order $order
     *
     * @return string
     */
    protected function prepareOneOrderZipFileName(Order $order)
    {
        return "order_export_{$order->updated_at->format('Y-m-d_h-m-s')}_{$order->id}.zip";
    }

    /**
     * @param Order $order
     *
     * @return string
     */
    public function oneOrderZipsBasePath(Order $order)
    {
        $fileName = $this->prepareOneOrderZipFileName($order);

        return $this->preparePath([$this->oneOrderZipsBaseDir(), $fileName]);
    }

    /**
     * @param Order $order
     *
     * @return string
     */
    public function oneOrderZipsUrl(Order $order)
    {
        $fileName = $this->prepareOneOrderZipFileName($order);

        return $this->getRemotePublicPath($this->oneOrderZipsBaseDir(), $fileName);
    }

    /**
     * @param Order $order
     *
     * @return string
     */
    public function oneOrderDigitalZipsUrl(Order $order)
    {
        $fileName = $this->prepareOneOrderDigitalZipFileName($order);

        return $this->getRemotePublicPath($this->oneOrderDigitalZipsBaseDir(), $fileName);
    }

    /**
     * @param Order $order
     *
     * @return string
     */
    protected function prepareOneOrderDigitalZipFileName(Order $order)
    {
        return "digital_{$order->updated_at->format('Y-m-d_h-m-s')}_{$order->id}.zip";
    }

    /**
     * @return string
     */
    public function oneOrderDigitalZipsBaseDir()
    {
        return $this->preparePath([$this->directories['oneOrderDigitalZips']]);
    }

    /**
     * @param Order $order
     *
     * @return string
     */
    public function oneOrderDigitalZipsLocalPath(Order $order)
    {
        $fileName = $this->prepareOneOrderDigitalZipFileName($order);

        return $this->generateLocalPath($this->oneOrderDigitalZipsBaseDir(), $fileName);
    }

    /**
     * @param Order $order
     *
     * @return string
     */
    public function oneOrderDigitalZipsBasePath(Order $order)
    {
        $fileName = $this->prepareOneOrderDigitalZipFileName($order);

        return $this->preparePath([$this->oneOrderDigitalZipsBaseDir(), $fileName]);
    }
}
