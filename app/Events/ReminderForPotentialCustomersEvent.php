<?php

namespace App\Events;


class ReminderForPotentialCustomersEvent extends BaseEvent
{
    public $subgallery_password;
    public $emails;

    /**
     * BasePaymentEvent constructor.
     *
     * @param $order
     * @param $packages
     * @param $addons
     * @param $admInformation
     */
    public function __construct(array $emails, $subgallery_password)
    {
        $this->emails = implode(',', $emails);
        $this->subgallery_password = $subgallery_password;
    }

    /**
     *
     * @return array
     * @throws \Throwable
     */
    public function getFieldsData(): array
    {
        $data = [
            'subgallery_password' => $this->subgallery_password,
            'emails' => $this->emails,
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
            'subgallery_password',
            'emails'
        ];

        return array_merge(parent::getAvailableFields(), $data);
    }
}
