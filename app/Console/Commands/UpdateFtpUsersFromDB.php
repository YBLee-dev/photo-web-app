<?php

namespace App\Console\Commands;

use App\Users\FtpUsers\FtpUserManager;
use App\Users\UserRepo;
use Illuminate\Console\Command;

class UpdateFtpUsersFromDB extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ftp-users:update-all';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get all users from db and create for them ftp clients';

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
    public function handle(FtpUserManager $ftpUserManager, UserRepo $userRepo)
    {
        $users = $userRepo->getAll();

        foreach ($users as $user)
        {
            if($user->ftp_login && $user->ftp_password){
                # Try to delete user first to resolve the user exists issue
                $ftpUserManager->deleteUser($user->ftp_login);
            }
        }

        foreach ($users as $user)
        {
            if($user->ftp_login && $user->ftp_password) {
                # Add user
                $ftpUserManager->addUser($user->ftp_login, $user->ftp_password);

                $this->info('Create ftp client for: '.$user->ftp_login.' - '.$user->ftp_password);
            }
        }
    }
}
