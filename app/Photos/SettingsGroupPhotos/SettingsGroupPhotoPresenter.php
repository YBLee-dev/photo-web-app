<?php


namespace App\Photos\SettingsGroupPhotos;


use Illuminate\Support\Facades\Storage;
use Webmagic\Core\Presenter\Presenter;

class SettingsGroupPhotoPresenter extends Presenter
{
    protected $entity = SettingsGroupPhotos::class;

    /**
     * School Background
     *
     * @return string
     */
    public function schoolBackground()
    {
        return (new SettingsGroupPhotosPathsManager())->templateTmpPublicUrl($this->entity->school_background);
    }

    /**
     * Class Background
     *
     * @return string
     */
    public function classBackground()
    {
        return (new SettingsGroupPhotosPathsManager())->templateTmpPublicUrl($this->entity->class_background);
    }

    /**
     * Id Card Portrait Background
     *
     * @return string
     */
    public function idCardPortraitBackground()
    {
        return (new SettingsGroupPhotosPathsManager())->templateTmpPublicUrl($this->entity->id_cards_background_portrait);
    }

    /**
     * Id Card Landscape Background
     *
     * @return string
     */
    public function idCardLandscapeBackground()
    {
        return (new SettingsGroupPhotosPathsManager())->templateTmpPublicUrl($this->entity->id_cards_background_landscape);
    }


    /**
     * Path to School logo
     *
     * @return string
     */
    public function schoolLogoPath()
    {
        return (new SettingsGroupPhotosPathsManager())->templateTmpPublicPath($this->entity->school_logo);
    }

    /**
     * School logo url
     *
     * @return string
     */
    public function schoolLogoUrl()
    {
        return (new SettingsGroupPhotosPathsManager())->templateTmpPublicUrl($this->entity->school_logo);
    }

    /**
     * Font path
     *
     * @return string
     */
    public function fontPath()
    {
        return $this->prepareFontPath($this->entity->font_file);
    }

    /**
     * Path to school background
     *
     * @return string
     */
    public function schoolBackgroundPath()
    {
        return (new SettingsGroupPhotosPathsManager())->templateTmpPublicPath($this->entity->school_background);
    }

    /**
     * Path to class background
     *
     * @return string
     */
    public function classBackgroundPath()
    {
        return (new SettingsGroupPhotosPathsManager())->templateTmpPublicPath($this->entity->class_background);
    }

    /**
     * Path to ID card portrait background
     *
     * @return string
     */
    public function idCardPortraitBackgroundPath()
    {
        return (new SettingsGroupPhotosPathsManager())->templateTmpPublicPath($this->entity->id_cards_background_portrait);
    }

    /**
     * Path to ID card landscape background
     *
     * @return string
     */
    public function idCardLandscapeBackgroundPath()
    {
        return (new SettingsGroupPhotosPathsManager())->templateTmpPublicPath($this->entity->id_cards_background_landscape);
    }

    /**
     * Prepare font path
     *
     * @param $fontName
     * @return string
     */
    protected function prepareFontPath(string $fontName):string
    {
        return public_path(config('project.fonts_path') . "/$fontName");
    }

}
