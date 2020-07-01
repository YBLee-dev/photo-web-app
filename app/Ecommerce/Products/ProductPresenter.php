<?php


namespace App\Ecommerce\Products;


use Webmagic\Core\Presenter\Presenter;

class ProductPresenter extends Presenter
{
    protected $entity = Product::class;

    /**
     * Image
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
        return asset(config('webmagic.dashboard.image_config.products_img_path') . '/' . $file_name);
    }
}