<?php

namespace App\Jobs;

use App\Mail\SendCredentialsForPhotographers;
use App\Users\FtpUsers\FtpUserManager;
use App\Users\User;
use App\Users\UserRepo;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;
use Throwable;

class CreateFtpUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $send_credentials;

    /**
     * Create a new job instance.
     *
     * @param User $user
     * @param bool $send_credentials
     */
    public function __construct(User $user, $send_credentials = false)
    {
        $this->user = $user;
        $this->send_credentials = $send_credentials;
    }

    /**
     * Execute the job.
     *
     * @param FtpUserManager $ftpUserManager
     * @param UserRepo       $userRepo
     *
     * @return void
     * @throws Throwable
     */
    public function handle(FtpUserManager $ftpUserManager, UserRepo $userRepo)
    {
        logger('start to creating ftp user');

        # Try to delete user first to resolve the user exists issue
        $login = $ftpUserManager->prepareFtpLogin($this->user->email);
        $ftpUserManager->deleteUser($login);

        # Add new user
        $ftp_user = $ftpUserManager->addUser(
            $this->user->email,
            $this->user->credential_password
        );

        # Send credentials
        if($this->send_credentials){
            Mail::send(new SendCredentialsForPhotographers(
                $this->user->email,
                $this->user->email,
                $ftp_user->getUserName(),
                $ftp_user->getPassword()
            ));
        }

        # Save new user
        $userRepo->update($this->user->id, [
            'ftp_login' => $ftp_user->getUserName(),
            'ftp_password' => $ftp_user->getPassword(),
        ]);

        logger('Send credentials to: ' . $this->user->email);
    }
}
