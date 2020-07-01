<?php


namespace App\Photos\SubGalleries;


use App\Core\StorageManager;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\Filesystem;
use SplFileInfo;

class SubGalleryStorageManager extends StorageManager
{
    /**
     * @param SubGallery $subGallery
     *
     * @return \Symfony\Component\Finder\SplFileInfo[]
     */
    public function uploadedFiles(SubGallery $subGallery)
    {
        $path = (new SubGalleryPathsManager())->uploadedDir($subGallery);
        $files = $this->getLocalTMPStorage()->files($path);

        return $files;
    }
}
