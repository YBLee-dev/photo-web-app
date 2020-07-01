<?php


namespace App\Core;

abstract class PathsManager
{
    /** @var string  */
    protected $rootPrefix = '';

    /**
     * Generate local uploading path
     *
     * @param string ...$paths
     *
     * @return string
     */
    protected function generateLocalUploadingPath(string ... $paths)
    {
        $path = $this->preparePath($paths);
        $storage = (new StorageManager())->getLocalUploadsStorage();

        return $storage->path($path);
    }

    /**
     * Generate local path
     *
     * @param string ...$paths
     *
     * @return mixed
     */
    protected function generateLocalPath(string ... $paths)
    {
        $path = $this->preparePath($paths);
        $storage = (new StorageManager())->getLocalTMPStorage();

        return $storage->path($path);
    }

    /**
     * Generate local public path
     *
     * @param string ...$paths
     *
     * @return mixed
     */
    protected function generateLocalPublicPath(string ... $paths)
    {
        $path = $this->preparePath($paths);
        $storage = (new StorageManager())->getLocalPublicStorage();

        return $storage->path($path);
    }

    /**
     * Generate local public path
     *
     * @param string ...$paths
     *
     * @return mixed
     */
    protected function generateLocalPublicUrl(string ... $paths)
    {
        $path = $this->preparePath($paths);
        $storage = (new StorageManager())->getLocalPublicStorage();

        return $storage->url($path);
    }

    /**
     * Generate remote storage path
     *
     * @param string ...$paths
     *
     * @return mixed
     */
    protected function generateRemotePath(string ... $paths)
    {
        $path = $this->preparePath($paths);
        $storage = (new StorageManager())->getRemoteStorage();

        return $storage->path($path);
    }

    /**
     * Prepare remote public path
     *
     * @param string ...$paths
     *
     * @return string
     */
    protected function getRemotePublicPath(string ... $paths)
    {
        $path = $this->preparePath($paths);
        $storage = (new StorageManager())->getRemoteStorage();

        return $storage->url($path);
    }

    /**
     * Implode and prepare clear path
     *
     * @param array $paths
     *
     * @return string
     */
    protected function preparePath(array $paths)
    {
        // Implode parts
        $path = implode('/', $paths);
        $path = str_replace('//', '/', $path);

        // Add prefix
        if ($this->rootPrefix) {
            $path = str_start($path, str_finish($this->rootPrefix, '/'));
        }


        return $path;
    }
}
