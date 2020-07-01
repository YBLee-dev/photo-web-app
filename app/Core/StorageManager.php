<?php

namespace App\Core;

use Exception;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\Facades\Storage;

class StorageManager
{
    /** @var string */
    protected $localPublicDisk = 'public';

    /** @var string Local disc name */
    protected $localDisk = 'local-tmp';

    /** @var string Local disc name */
    protected $remoteDisk = 's3';

    /** @var string Local uploads disk */
    protected $localUploadingDisk = 'uploads';

    /** @var string  */
    protected $pathsManager;

    /**
     * @return string
     */
    public function getLocalDiskName()
    {
        return $this->localDisk;
    }

    /**
     * @param string $dir
     *
     * @return bool
     */
    public function isFtpUploadsDirExists(string $dir)
    {
        return $this->getLocalUploadsStorage()->exists($dir);
    }

    /**
     * @param string $dir
     */
    public function deleteFtpUploadsDir(string $dir)
    {
        $this->getLocalUploadsStorage()->deleteDirectory($dir);
    }

    /**
     * @param string $localBasePath
     * @return bool
     */
    protected function deleteLocalFile(string $localBasePath)
    {
        if($this->getLocalTMPStorage()->exists($localBasePath)){
            return $this->getLocalTMPStorage()->delete($localBasePath);
        }
    }

    /**
     * @param string $remoteBasePath
     * @return bool
     */
    protected function deleteRemoteFile(string $remoteBasePath)
    {
        if($this->getRemoteStorage()->exists($remoteBasePath)){
            return $this->getRemoteStorage()->delete($remoteBasePath);
        }
    }

    /**
     * @param string $baseFilePath
     */
    protected function deleteLocalAndRemoteFiles(string $baseFilePath)
    {
        $this->deleteLocalFile($baseFilePath);
        $this->deleteRemoteFile($baseFilePath);
    }

    /**
     * @param string $localDir
     *
     * @return bool
     */
    protected function prepareLocalDir(string $localDir)
    {
        if($this->getLocalTMPStorage()->exists($localDir)) {
            return true;
        }

        return $this->getLocalTMPStorage()->makeDirectory($localDir);
    }

    /**
     * @param string $ftpBasePath
     *
     * @return string
     */
    public function getUploadedDirectoryMovingControlFilePath(string $ftpBasePath)
    {
        return "$ftpBasePath/.moving";
    }

    /**
     * @param string $ftpBasePath
     *
     * @return bool
     */
    public function isUploadedDirectoryMoving(string $ftpBasePath)
    {
        $controlFilePath = $this->getUploadedDirectoryMovingControlFilePath($ftpBasePath);
        return $this->getLocalUploadsStorage()->exists($controlFilePath);
    }

    /**
     * @param string $ftpBasePath
     *
     * @return bool
     */
    public function markDirectoryAsUploading(string $ftpBasePath)
    {
        return $this->getLocalUploadsStorage()->put(
            $this->getUploadedDirectoryMovingControlFilePath($ftpBasePath),
            ''
        );
    }

    /**
     * Move uploaded gallery to processing
     *
     * @param string $ftpBasePath
     * @param string $galleryProcessBaseDir
     *
     * @throws FileNotFoundException
     */
    public function moveFtpUploadedDirToGalleryProcessDir(string $ftpBasePath, string $galleryProcessBaseDir)
    {

        $uploadsStorage = $this->getLocalUploadsStorage();
        $ftpDirectories = $uploadsStorage->directories($ftpBasePath);
        foreach ($ftpDirectories as $directory) {
            $files = $uploadsStorage->files($directory);

            foreach ($files as $filePath) {

                // Load images only
                if ($this->isImage($filePath)) {
                    $newPath = str_replace($ftpBasePath,  $galleryProcessBaseDir, $filePath);
                    $file =  $uploadsStorage->get($filePath);
                    $this->getLocalTMPStorage()->put($newPath, $file);
                }
            }
        }

        $uploadsStorage->deleteDirectory($ftpBasePath);
    }

    /**
     * Move all files from one directory to another
     * Local storage only
     *
     *
     * @param string $srcDir - local directory
     * @param string $targetDir - local directory
     */
    public function copyOneLevelLocalDir(string $srcDir, string $targetDir)
    {
        $storage = $this->getLocalTMPStorage();

        $files = $storage->files($srcDir);

        foreach ($files as $filePath) {
            $newPath = str_replace($srcDir,  $targetDir, $filePath);
            $storage->copy($filePath, $newPath);
        }

    }

    /**
     * Check if file is image
     *
     * @param string $image_name
     *
     * @return bool
     */
    public function isImage(string $image_name)
    {
        $pos = strrpos($image_name, '.');
        $ext = substr($image_name, $pos + 1);
        $ext = strtolower($ext);

        return $ext === 'jpg' || $ext === 'png' || $ext === 'jpeg';
    }

    /**
     * Move directory to remote storage
     *
     * @param string $localPath
     * @param string $remotePath
     *
     * @throws FileNotFoundException
     */
    protected function moveDirToRemote(string $localPath, string $remotePath = null)
    {
        $remotePath = $remotePath ?? $localPath;

        if (is_file($localPath)) {
            $this->moveFileToRemote($localPath, $remotePath);
            return;
        }

        $localStorage = $this->getLocalTMPStorage();
        $directories = $localStorage->directories($localPath);
        if (count($directories) > 0) {
            throw new Exception("Some other directories exists into $localStorage->path($localPath)");
        }

        $files = $localStorage->files($localPath);

        foreach ($files as $file) {
            $this->moveFileToRemote($file, $file);
        }

        $localStorage->deleteDir($localPath);
    }

    /**
     * Put file to S3 and remove local
     *
     * @param string $localFilePath
     * @param string $remoteFilePath
     *
     * @return bool
     * @throws FileNotFoundException
     */
    protected function moveFileToRemote(string $localFilePath, string $remoteFilePath = null)
    {
        $localStorage = $this->getLocalTMPStorage();
        $remoteStorage = $this->getRemoteStorage();

        // Do nothing if file is not exists
        if(!$localStorage->exists($localFilePath)){
            return false;
        }

        $remoteFilePath = $remoteFilePath ?? $localFilePath;

        $file = $localStorage->get($localFilePath);

        $remoteStorage->put($remoteFilePath, $file);

        $localStorage->delete($localFilePath);

        return true;
    }

    /**
     * Return remote disk
     *
     * @return FilesystemAdapter
     */
    public function getRemoteStorage(): FilesystemAdapter
    {
        return Storage::disk($this->remoteDisk);
    }

    /**
     * Return local disk
     *
     * @return FilesystemAdapter
     */
    public function getLocalTMPStorage(): FilesystemAdapter
    {
        return Storage::disk($this->localDisk);
    }

    /**
     * Return local public disk
     *
     * @return FilesystemAdapter
     */
    public function getLocalPublicStorage(): FilesystemAdapter
    {
        return Storage::disk($this->localPublicDisk);
    }

    /**
     * Return local disk
     *
     * @return FilesystemAdapter
     */
    public function getLocalUploadsStorage(): FilesystemAdapter
    {
        return Storage::disk($this->localUploadingDisk);
    }

    /**
     * Return directories list in local path
     *
     * @param string $path
     *
     * @return array
     */
    public function dirListInLocalTMPPath(string $path)
    {
        return $this->getLocalTMPStorage()->directories($path);
    }

    /**
     * Real method for preparing local directory
     *
     * @param string $dirKey
     */
    protected function preparePhotosLocalDir(string $dirKey)
    {
        $pathsManagerMethod = "{$dirKey}BaseDir";
        $pathsManager = $this->pathsManager;
        $dirPath = (new $pathsManager())->{$pathsManagerMethod}();

        $this->getLocalTMPStorage()->makeDirectory($dirPath);
    }

    /**
     * @param $method
     * @param $args
     *
     * @throws Exception
     */
    public function __call($method, $args)
    {
        $dirKey = str_replace('PrepareLocalDir', '', $method);

        try{

            $this->preparePhotosLocalDir($dirKey);

        } catch (Exception $e) {
            throw new Exception("Method $method not allowed in ".get_class($this));
        }
    }
}
