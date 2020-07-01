<?php

namespace App\Console\Commands;

use App\Users\FtpUsers\FtpUserManager;
use Illuminate\Console\Command;

class CreateFtpUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ftp-users:create {login} {password}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create ftp client';

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
        $ftpUserManager->addUser($this->argument('login'), $this->argument('password'));
        $this->info('Create ftp client for: '.$this->argument('login').' - '.$this->argument('password'));
    }
}
