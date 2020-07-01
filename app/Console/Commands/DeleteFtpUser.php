<?php

namespace App\Console\Commands;

use App\Users\FtpUsers\FtpUserManager;
use Illuminate\Console\Command;

class DeleteFtpUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ftp-users:delete {login}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete ftp client';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(FtpUserManager $ftpUserManager)
    {
        $ftpUserManager->deleteUser($this->argument('login'));
        $this->info('Delete ftp client: '.$this->argument('login'));
    }
}
