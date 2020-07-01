<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Events list
    |--------------------------------------------------------------------------
    |
    | Registration events and association mails listeners for this events
    |
    */

    'events' => [
        App\Events\NewPaymentEvent::class,
        App\Events\ConfirmationPaymentEvent::class,
        App\Events\RemindAboutUnpaidOrderEvent::class,
        App\Events\ReminderForPotentialCustomersEvent::class
    ],

    /*
    |--------------------------------------------------------------------------
    | Email template
    |--------------------------------------------------------------------------
    |
    | On/Off using templates for emails
    | Defining list of available templates
    |
    */

    'use_email_templates' => true,

    'email_templates' => [
        'default' => 'notifier::mail.template',
    ],


    /*
    |--------------------------------------------------------------------------
    | Types list
    |--------------------------------------------------------------------------
    |
    | Registration types alias and association with their class
    |
    */

    'types' => [
        'mail' => 'Webmagic\Notifier\Mail\Mail',
    ],


    /*
    |--------------------------------------------------------------------------
    | Using queue in sending mail
    |--------------------------------------------------------------------------
    */
    'mail_queue_use' => false,


    /*
    |--------------------------------------------------------------------------
    | Additional fields for notifications and different types
    |--------------------------------------------------------------------------
    */
    'notifications_additional_fields' => [],

    /*
     * Types
     */
    'mails_additional_fields' => [],

    /*
    |--------------------------------------------------------------------------
    | Additional templates
    |--------------------------------------------------------------------------
    |
    | Array with additional templates which will be available for notify by email
    | All variables will be available in selected template
    |
    */

    'additional_templates' => [
    ]

];
