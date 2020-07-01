<?php


namespace App\Photos\Schools;


use App\Photos\SettingsGroupPhotos\SettingsGroupPhotosPathsManager;
use Webmagic\Core\Presenter\Presenter;

class SchoolPresenter extends Presenter
{
    protected $entity = School::class;

    /**
     * Path to School logo
     *
     * @return string
     */
    public function schoolLogoUrl()
    {
        return (new SettingsGroupPhotosPathsManager())->templateTmpPublicUrl($this->entity->school_logo);
    }
}
