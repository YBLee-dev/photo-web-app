<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class UpdatePermissionsOnStorage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $ftp_user;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($ftp_user)
    {
        $this->ftp_user = $ftp_user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        exec('chmod 777 -R ' . '/srv/app/storage/app/ftp/' . $this->ftp_user);
        logger('set permisiions for user: '.$this->ftp_user . decoct(fileperms('/srv/app/storage/app/ftp/' . $this->ftp_user) & 0777));
    }
}
