<?php

namespace App\Events;

use App\Settings\SettingsRepo;
use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Broadcasting\InteractsWithSockets;
use Webmagic\Notifier\Contracts\Notifiable;

class BaseEvent implements Notifiable
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Special fields with data for notifier
     *
     * @return array
     * @throws \Throwable
     */
    public function getFieldsData(): array
    {
        return [
            'admin_email' => $this->getAdminEmail(),
            'signature' => $this->getSignature(),
            'signature_image' => $this->getSignatureImage(),
        ];
    }

    /**
     * Fields (names) that can be used in admin panel
     *
     * @return array
     */
    public static function getAvailableFields(): array
    {
        return [
            'admin_email',
            'signature',
            'signature_image',
        ];
    }


    public static function getAvailableFiles(): array
    {
        return [];
    }

    /**
     * Get mail signature
     *
     * @return mixed
     * @throws \Exception
     */
    public function getSignature()
    {
        /** @var SettingsRepo $settingsRepo */
        $settingsRepo = app()->make(SettingsRepo::class);
        $settings = $settingsRepo->getSignature();

        return $settings['email_signature'];
    }

    /**
     * Get mail signature image
     *
     * @return mixed
     * @throws \Exception
     */
    public function getSignatureImage()
    {
        /** @var SettingsRepo $settingsRepo */
        $settingsRepo = app()->make(SettingsRepo::class);
        $settings = $settingsRepo->getSignatureImage();

        return  $settings->email_signature_image ? "<img alt='\' src={$settings->present()->image}>" : '';
    }

    /**
     * Get mail signature
     *
     * @return mixed
     * @throws \Exception
     */
    public function getAdminEmail()
    {
        /** @var SettingsRepo $settingsRepo */
        $settingsRepo = app()->make(SettingsRepo::class);
        $settings = $settingsRepo->getAdminEmail();

        return $settings['admin_email'];
    }
}
