<?php


namespace App\Photos\TemplatedPhotosGeneration;


use App\Core\StorageManager;
use App\Photos\Galleries\Gallery;
use App\Photos\GroupPhotosGeneration\GroupPhotosPathManager;
use Illuminate\Contracts\Filesystem\FileNotFoundException as FileNotFoundExceptionAlias;
use Illuminate\Contracts\Filesystem\FileNotFoundException;

class TemplatedPhotosStorageManager extends StorageManager
{
    /**
     * Move ID cards to remote storage
     *
     * @param Gallery $gallery
     *
     * @throws FileNotFoundException
     */
    public function moveIdCardsToRemote(Gallery $gallery)
    {
        $localDirectory = (new TemplatedPhotosPathsManager())->IdCardsDir($gallery);

        $this->moveDirToRemote($localDirectory);
    }

    /**
     * Prepare proofing photos dir
     *
     * @param Gallery $gallery
     *
     * @return bool
     */
    public function prepareProofingPhotosDirectory(Gallery $gallery)
    {
        $proofingPhotosDir = (new TemplatedPhotosPathsManager())->proofingPhotosDir($gallery);

        return $this->prepareLocalDir($proofingPhotosDir);
    }

    /**
     * @param Gallery $gallery
     *
     * @throws FileNotFoundExceptionAlias
     */
    public function moveProofingToRemoteForAllGallery(Gallery $gallery)
    {
        $proofingPhotosDir = (new TemplatedPhotosPathsManager())->proofingPhotosDir($gallery);

        $this->moveDirToRemote($proofingPhotosDir);
    }
}
