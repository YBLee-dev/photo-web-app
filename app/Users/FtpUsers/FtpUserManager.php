<?php


namespace App\Users\FtpUsers;


class FtpUserManager
{
    protected $logFilePath = 'storage/logs/ftp_user_management.log';

    /**
     * Add new system user and FTP account
     * Create directories
     *
     * @param string $userName
     * @param string $password
     *
     * @return FtpUser
     * @throws \Throwable
     */
    public function addUser(string $userName, string $password)
    {
        $userName = $this->prepareFtpLogin($userName);
        $password = $this->prepareFtpLogin($password);

        $addUserScript = $this->prepareCommandPath('adduser');

        # Create FTP USER
        exec("$addUserScript -u $userName -p $password", $output, $return_var );

        # Check that no errors were happened
        throw_if(
            $return_var != 0,
            "User adding exception. Check $this->logFilePath for more details"
        );

        return (new FtpUser($userName, $password));
    }

    /**
     * Prepare username
     *
     * @param string $userName
     *
     * @return mixed
     */
    public function prepareFtpLogin(string $userName)
    {
        return str_replace(['@', '.', ' ', '\\', '/'], '_', $userName);
    }

    /**
     * Remove system user, FTP account and user directories and files
     *
     * @param string $userName
     */
    public function deleteUser(string $userName)
    {
        $deleteUserScript = $this->prepareCommandPath('deluser');

        exec("$deleteUserScript -u $userName", $result);
    }


    /**
     * Prepare command file path
     *
     * @param string $command
     *
     * @return string
     */
    protected function prepareCommandPath(string $command): string
    {
        return base_path(".ftp-service/$command.sh");
    }
}
