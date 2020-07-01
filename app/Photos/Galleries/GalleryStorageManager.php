<?php


namespace App\Photos\Galleries;


use App\Core\StorageManager;
use App\Users\User;
use App\Users\UserPathsManger;
use App\Users\UserRepo;

class GalleryStorageManager extends StorageManager
{
    /**
     * Remove local upload directory
     *
     * @param Gallery $gallery
     */
    public function removeUploadDirectory(Gallery $gallery)
    {
        $uploadedDir = (new GalleryPathsManager())->uploadedDir($gallery);
        $this->getLocalTMPStorage()->deleteDirectory($uploadedDir);
    }

    /**
     * Prepare local export dir
     */
    public function prepareLocalExportDir()
    {
        $path = (new GalleryPathsManager())->exportLocalDirBasePath();

        $this->prepareLocalDir($path);
    }

    /**
     * @param Gallery $gallery
     * @param User    $user
     * @param string  $ftpPath
     *
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function moveUnprocessedGalleryToTmp(Gallery $gallery, User $user, string $ftpPath)
    {
        // Move files to TMP directory
        $uploadedPath = (new UserPathsManger())->uploadedBaseDir($user, $ftpPath);
        $galleryProcessingPath = (new GalleryPathsManager())->uploadedDir($gallery);

        $this->moveFtpUploadedDirToGalleryProcessDir($uploadedPath, $galleryProcessingPath);
    }
}
