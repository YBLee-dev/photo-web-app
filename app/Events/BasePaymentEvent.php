<?php

namespace App\Events;


class BasePaymentEvent extends BaseEvent
{
    /** @var array  */
    public $order;

    public $packages;

    public $addons;

    public $admInformation;

    /**
     * BasePaymentEvent constructor.
     *
     * @param $order
     * @param $packages
     * @param $addons
     * @param $admInformation
     */
    public function __construct($order, $packages, $addons, $admInformation = false)
    {
        $this->order = $order;
        $this->packages = $packages;
        $this->addons = $addons;
        $this->admInformation = $admInformation;
    }

    /**
     *
     * @return array
     * @throws \Throwable
     */
    public function getFieldsData(): array
    {
        $data = [
            'order_data' => $this->getOrderMainData(),
            'order_sub_data' => $this->getOrderSubData(),
        ];

        return array_merge(parent::getFieldsData(), $data);
    }

    /**
     * Fields (names) that can be used in admin panel
     *
     * @return array
     */
    public static function getAvailableFields(): array
    {
        $data = [
            'order_data',
            'order_sub_data',
        ];

        return array_merge(parent::getAvailableFields(), $data);
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function getOrderMainData()
    {
        $order = $this->order;
        $admInformation = $this->admInformation;

        return view('emails._parts._order-data', compact('order', 'admInformation'))->render();
    }

    /**
     * @return string
     * @throws \Throwable
     */
    public function getOrderSubData()
    {
        $packages = $this->packages;
        $addons = $this->addons;
        $order = $this->order;

        return view('emails._parts._packages-addons-data', compact('packages', 'addons', 'order'))->render();
    }
}
