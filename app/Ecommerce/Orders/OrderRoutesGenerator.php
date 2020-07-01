<?php


namespace App\Ecommerce\Orders;


class OrderRoutesGenerator
{
    /** @var Order */
    protected $order;

    /**
     * OrderRoutesGenerator constructor.
     *
     * @param Order $order
     */
    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    /**
     * @return string
     */
    public function zipPreparingStart()
    {
        return route('dashboard::orders.zip.preparing-start', $this->order);
    }

    /**
     * @return string
     */
    public function zipPreparingStatus()
    {
        return route('dashboard::orders.zip.preparing-status', $this->order);
    }

    /**
     * @return string
     */
    public function zipDownload()
    {
        return route('dashboard::orders.zip.download', $this->order);
    }

    /**
     * @return string
     */
    public function zipDigitalPreparingStatus()
    {
        return route('dashboard::orders.zip.digital.preparing-status', $this->order);
    }

}
