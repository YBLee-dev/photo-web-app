<?php


namespace App\Ecommerce\Packages;

use Webmagic\Core\Presenter\Presenter as CorePresenter;

class PackagePresenter extends CorePresenter
{

    /**
     * Main image
     *
     * @return string
     */
    public function image()
    {
        return $this->prepareImageURL($this->entity->image);
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
        return asset(config('webmagic.dashboard.image_config.packages_img_path') . '/' . $file_name);
    }

    public function fullImagePath()
    {
        return config('webmagic.dashboard.image_config.packages_img_path') . '/' . $this->entity->image;
    }
}
