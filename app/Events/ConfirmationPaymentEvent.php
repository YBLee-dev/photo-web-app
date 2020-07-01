<?php

namespace App\Events;


class ConfirmationPaymentEvent extends BasePaymentEvent
{
    /**
     *
     * @return array
     * @throws \Throwable
     */
    public function getFieldsData(): array
    {
        $data = [
            'customer_first_name' => $this->order['customer_first_name'],
            'customer_last_name' => $this->order['customer_last_name'],
            'customer_email' => $this->order->customer['email'],
            'order_id' => $this->order['id']
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
            'customer_first_name',
            'customer_last_name',
            'customer_email',
            'order_id'
        ];

        return array_merge(parent::getAvailableFields(), $data);
    }
}
