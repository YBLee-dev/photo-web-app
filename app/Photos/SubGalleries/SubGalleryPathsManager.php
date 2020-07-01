<?php


namespace App\Photos\SubGalleries;


use App\Core\PathsManager;
use App\Photos\Galleries\GalleryPathsManager;

class SubGalleryPathsManager extends PathsManager
{
    /** @var GalleryPathsManager */
    protected $galleryPathManager;

    /**
     * SubPathsManager constructor.
     *
     */
    public function __construct()
    {
        $this->galleryPathManager = (new GalleryPathsManager());
    }

    /**
     * Return sub gallery uploaded local dir
     *
     * @param SubGallery $subGallery
     *
     * @return string
     */
    public function uploadedDir(SubGallery $subGallery)
    {
        $galleryUploadedPath = $this->galleryPathManager->uploadedDir($subGallery->gallery);

        return $this->preparePath([$galleryUploadedPath, $subGallery->name]);
    }

    /**
     * Return sub gallery uploaded local dir
     *
     * @param SubGallery $subGallery
     *
     * @return string
     */
    public function uploadedLocalPath(SubGallery $subGallery)
    {
        $baseDir = $this->uploadedDir($subGallery);

        return $this->generateLocalPath($baseDir);
    }

    /**
     * @param SubGallery $subGallery
     *
     * @return array
     */
    public function uploadedLocalFiles(SubGallery $subGallery)
    {
        $files = (new SubGalleryStorageManager())->uploadedFiles($subGallery);

        $paths = [];

        foreach ($files as $file) {
            $paths[] = $this->generateLocalPath($file);
        }

        return $paths;
    }
}
