<?php


namespace App\Photos\Photos;


use Laracasts\Presenter\Exceptions\PresenterException;

trait PhotoUrlsPresentableTrait
{
    /**
     * @param Photo $photo
     *
     * @return string
     * @throws PresenterException
     */
    protected function getPhotoOriginalUrl(Photo $photo = null)
    {
        return is_null($photo) ? '' : $photo->present()->originalUrl();
    }

    /**
     * @param Photo $photo
     *
     * @return string
     * @throws PresenterException
     */
    protected function getPhotoPreviewUrl(Photo $photo = null)
    {
        return is_null($photo) ? '' : $photo->present()->previewUrl();
    }
}
