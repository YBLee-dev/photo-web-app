<?php

namespace App\Mail;

use App\Users\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendReminderForPotentialCustomers extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The photographer instance.
     *
     * @var User
     */
    public $send_to;
    public $sub_gallery_password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($send_to, $sub_gallery_password)
    {
        $this->send_to = $send_to;
        $this->sub_gallery_password = $sub_gallery_password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.reminder_for_customers')
            ->with([
                'sub_gallery_password' => $this->sub_gallery_password
            ])
            ->cc($this->send_to)
            ->subject('Notification about your order');
    }
}
