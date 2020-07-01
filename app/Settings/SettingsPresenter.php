<?php


namespace App\Settings;


use Webmagic\Core\Presenter\Presenter;

class SettingsPresenter extends Presenter
{
    protected $entity = Settings::class;

    /**
     * Image
     *
     * @return string
     */
    public function image()
    {
        return $this->prepareImageURL($this->entity->email_signature_image);
    }

    /**
     * Prepare url for images
     *
     * @param $file_name
     *
     * @return string
     */
    protected function prepareImageURL($file_name)
    {
        return asset(config('project.settings_img_path') . '/' . $file_name);
    }
}
