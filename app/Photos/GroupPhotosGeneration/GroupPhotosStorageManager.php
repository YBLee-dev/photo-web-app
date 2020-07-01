<?php

namespace App\Photos\GroupPhotosGeneration;


use App\Core\StorageManager;
use App\Photos\Galleries\Gallery;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class GroupPhotosStorageManager extends StorageManager
{
    /**
     * Move common class photos to remove storage
     *
     * @param Gallery $gallery
     *
     * @throws FileNotFoundException
     */
    public function moveMiniWalletCollagePhotosToRemote(Gallery $gallery)
    {
        $pathManager = new GroupPhotosPathManager();
        $localDirectory = $pathManager->miniWalletCollageDir($gallery);

        $this->moveDirToRemote($localDirectory);
    }

    /**
     * Move common class photos to remove storage
     *
     * @param Gallery $gallery
     *
     * @throws FileNotFoundException
     */
    public function moveCommonClassPhotosToRemote(Gallery $gallery)
    {
        $pathManager = new GroupPhotosPathManager();
        $localDirectory = $pathManager->commonClassPhotoDir($gallery);

        $this->moveDirToRemote($localDirectory);
    }

    /**
     * Move common class photos to remove storage
     *
     * @param Gallery $gallery
     *
     * @throws FileNotFoundException
     */
    public function personalClassPhotosMoveToRemote(Gallery $gallery)
    {
        $pathManager = new GroupPhotosPathManager();
        $baseDir = $pathManager->personalClassPhotoDir($gallery);

        $this->moveDirToRemote($baseDir);
    }

    /**
     * Move staff photo to remote
     *
     * @param Gallery $gallery
     *
     * @throws FileNotFoundException
     */
    public function moveStaffPhotoToRemote(Gallery $gallery)
    {
        $pathManager = new GroupPhotosPathManager();
        $staffPhotoPath = $pathManager->staffPhotoBasePath($gallery);

        $this->moveFileToRemote($staffPhotoPath, $staffPhotoPath);
    }

    /**
     * Move staff photo to remote
     *
     * @param Gallery $gallery
     *
     * @throws FileNotFoundException
     */
    public function moveSchoolPhotoToRemote(Gallery $gallery)
    {
        $pathManager = new GroupPhotosPathManager();
        $staffPhotoPath = $pathManager->schoolPhotoBasePath($gallery);

        $this->moveFileToRemote($staffPhotoPath, $staffPhotoPath);
    }

    /**
     * @param Gallery $gallery
     *
     * @return array
     */
    public function miniWalletsPhotos(Gallery $gallery)
    {
        $dirPath = (new GroupPhotosPathManager())->miniWalletCollageDir($gallery);
        $files = $this->getRemoteStorage()->files($dirPath);

        return $files;
    }
}
