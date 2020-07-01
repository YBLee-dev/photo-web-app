<?php

namespace App\Jobs;

use App\Users\FtpUsers\FtpUserManager;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DeleteFtpUser implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ftp_login;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($ftp_login)
    {
        $this->ftp_login = $ftp_login;
    }

    /**
     * Execute the job.
     *
     * @param FtpUserManager $ftpUserManager
     *
     * @return void
     */
    public function handle(FtpUserManager $ftpUserManager)
    {
        logger('start to delete ftp user: ' . $this->ftp_login);

        $ftpUserManager->deleteUser( $this->ftp_login);

        logger('successfully delete');
    }
}
