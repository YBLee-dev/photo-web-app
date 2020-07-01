<?php


namespace App\Ecommerce\Orders;


use Webmagic\Core\Presenter\Presenter;

class OrderPresenter extends Presenter
{
    protected $entity = Order::class;

    /**
     * @return string
     */
    public function zipBasePath()
    {
        return (new OrderPathsManager())->oneOrderZipsBasePath($this->entity);
    }

    /**
     * @return string
     */
    public function zipLocalPath()
    {
        return (new OrderPathsManager())->oneOrderZipsLocalPath($this->entity);
    }

    /**
     * @return string
     */
    public function zipUrl()
    {
        return (new OrderPathsManager())->oneOrderZipsUrl($this->entity);
    }

    /**
     * @return string
     */
    public function zipDigitalUrl()
    {
        return (new OrderPathsManager())->oneOrderDigitalZipsUrl($this->entity);
    }

    /**
     * @return string
     */
    public function zipDigitalLocalPath()
    {
        return (new OrderPathsManager())->oneOrderDigitalZipsLocalPath($this->entity);
    }

    /**
     * @return string
     */
    public function zipDigitalBasePath()
    {
        return (new OrderPathsManager())->oneOrderDigitalZipsBasePath($this->entity);
    }

    /**
     * @return string
     */
    public function csvDetailsLocalPath()
    {
        return (new OrderPathsManager())->oneOrderDetailsCsvLocalPath($this->entity);
    }

    /**
     * @return string
     */
    public function csvDetailsBasePath()
    {
        return (new OrderPathsManager())->oneOrderDetailsCsvBasePath($this->entity);
    }
}
