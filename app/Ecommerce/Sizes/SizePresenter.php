<?php


namespace App\Ecommerce\Sizes;

use Webmagic\Core\Presenter\Presenter as CorePresenter;

class SizePresenter extends CorePresenter
{

    /**
     * Full size for viewing
     *
     * @return string
     */
    public function prepareViewSize()
    {
        return $this->entity->width .' x '.$this->entity->height;
    }

}
