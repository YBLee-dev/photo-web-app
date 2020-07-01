<?php


namespace App\Users;


use App\Core\PathsManager;

class UserPathsManger extends PathsManager
{
    /**
     * Generate local uploading path
     *
     * @param User   $user
     * @param string $dirName
     *
     * @return string
     */
    public function uploadedDirectoryPath(User $user, string $dirName)
    {
        return $this->generateLocalUploadingPath($this->uploadedBaseDir($user, $dirName));
    }

    /**
     * Prepare base path for user uploaded directory
     *
     * @param User   $user
     * @param string $dirName
     *
     * @return string
     */
    public function uploadedBaseDir(User $user, string $dirName)
    {
        return $this->preparePath([$user->ftp_login, $dirName]);
    }
}
