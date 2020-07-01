<?php

namespace App\Events;


class NewPaymentEvent extends BasePaymentEvent
{
    /**
     *
     * @return array
     * @throws \Throwable
     */
    public function getFieldsData(): array
    {
        $data = [
            'customer_email' => $this->order->customer['email']
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
            'customer_email'
        ];

        return array_merge(parent::getAvailableFields(), $data);
    }
}
