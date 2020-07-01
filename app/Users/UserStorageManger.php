<?php


namespace App\Users;


use App\Core\StorageManager;

class UserStorageManger extends StorageManager
{
    /**
     * Check if uploaded directory exits
     *
     * @param User   $user
     * @param string $dirName
     *
     * @return bool
     */
    public function isDirectoryUploaded(User $user, string $dirName)
    {
        $basePath = (new UserPathsManger())->uploadedBaseDir($user, $dirName);

        return $this->getLocalUploadsStorage()->exists($basePath);
    }
}
