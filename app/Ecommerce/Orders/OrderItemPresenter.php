<?php


namespace App\Ecommerce\Orders;


use Illuminate\Support\Facades\Storage;
use Webmagic\Core\Presenter\Presenter;

class OrderItemPresenter extends Presenter
{
    protected $entity = OrderItem::class;

    /**
     * Get preview image url
     *
     * @return mixed
     */
    public function previewImage()
    {
        return $this->preparePreviewUrl($this->entity->image);
    }

    /**
     * Get original image url
     *
     * @return mixed
     */
    public function originalImage()
    {
        return $this->originalImageUrl($this->entity->image);
    }


    /**
     * Prepare preview url
     *
     * @param $fileName
     * @return mixed
     */
    public function preparePreviewUrl($fileName)
    {
        return Storage::disk('s3')->url("preview/$fileName");
    }

    /**
     * Prepare original url
     *
     * @param $fileName
     * @return mixed
     */
    public function originalImageUrl($fileName)
    {
        return Storage::disk('s3')->url("original/$fileName");
    }

}