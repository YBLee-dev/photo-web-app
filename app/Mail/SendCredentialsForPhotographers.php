<?php

namespace App\Mail;

use App\Users\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendCredentialsForPhotographers extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * The photographer instance.
     *
     * @var User
     */
    public $ftp_login;
    public $password;
    public $send_to;
    public $admin_panel_login;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($send_to, $admin_panel_login, $ftp_login, $password)
    {
        $this->send_to = $send_to;
        $this->ftp_login = $ftp_login;
        $this->admin_panel_login = $admin_panel_login;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->view('emails.credentials')
            ->with([
                'ftp_login' => $this->ftp_login,
                'admin_panel_login' => $this->admin_panel_login,
                'password' => $this->password,
            ])
            ->to($this->send_to);
    }
}
