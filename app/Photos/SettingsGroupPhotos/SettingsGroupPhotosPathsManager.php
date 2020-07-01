<?php


namespace App\Photos\SettingsGroupPhotos;


use App\Core\PathsManager;

class SettingsGroupPhotosPathsManager extends PathsManager
{
    /** @var string  */
    protected $rootPrefix = 'group-settings';

    /** @var string  */
    protected $templatesDir = 'templates';

    /**
     * @return mixed
     */
    public function templatesDirPath()
    {
        return $this->generateLocalPublicPath($this->templatesDir);
    }

    /**
     * @param string $fileName
     *
     * @return mixed
     */
    public function templateTmpPublicUrl(string $fileName)
    {
        return $this->generateLocalPublicUrl($this->templatesDir, $fileName);
    }

    /**
     * @param string $fileName
     *
     * @return mixed
     */
    public function templateTmpPublicPath(string $fileName)
    {
        return $this->generateLocalPublicPath($this->templatesDir, $fileName);
    }
}
