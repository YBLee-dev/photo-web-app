<?php

namespace App\Jobs;

use App\Core\StorageManager;
use App\Users\User;
use App\Users\UserPathsManger;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class MarkDirectoryAsUploading implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $user;
    protected $ftpPath;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, string $ftpPath)
    {
        $this->user = $user;
        $this->ftpPath = $ftpPath;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new StorageManager())->markDirectoryAsUploading((new UserPathsManger())->uploadedBaseDir($this->user, $this->ftpPath));
        exec('chmod 777 -R ' . '/srv/app/storage/app/ftp/' . $this->user->ftp_login);
    }
}
