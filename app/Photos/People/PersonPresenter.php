<?php


namespace App\Photos\People;


use App\Photos\Photos\PhotoTypeEnum;
use App\Photos\Photos\PhotoUrlsPresentableTrait;
use Laracasts\Presenter\Exceptions\PresenterException;
use Webmagic\Core\Presenter\Presenter;

class PersonPresenter extends Presenter
{
    use PhotoUrlsPresentableTrait;

    protected $staffPrefixes = ['Mr.','Ms.', 'Mrs.'];

    /**
     * Prepare prefer name
     *
     * @return string
     */
    public function name()
    {
        if (in_array($this->entity->first_name, $this->staffPrefixes)) {
            return ucfirst($this->entity->first_name) .' '. ucfirst($this->entity->last_name);
        }

        return ucfirst($this->entity->first_name);
    }

    /**
     * Clean up first name
     *
     * @return mixed
     */
    public function firstNameClear()
    {
        return trim(str_replace($this->staffPrefixes, '', $this->name()));
    }

    /**
     * @return string
     */
    public function nameWithTitle()
    {
        return $this->name(). ' '. ucfirst($this->entity->title);
    }


    /**
     * Prepare client full name
     *
     * @return string
     */
    public function prepareFullName()
    {
        $lastNameInitial = strlen($this->entity->last_name) > 0 ? $this->entity->last_name[0] : '';

        return ucfirst($this->entity->first_name) .' '. strtoupper($lastNameInitial);
    }

    /**
     * @return string
     * @throws PresenterException
     */
    public function croppedPhotoUrl()
    {
        return $this->getPhotoOriginalUrl($this->entity->croppedPhoto());
    }

    /**
     * @return string
     * @throws PresenterException
     */
    public function classPersonalPhotoUrl()
    {
        return $this->getPhotoOriginalUrl($this->entity->classPersonalPhoto());
    }

    /**
     * @return string
     * @throws PresenterException
     */
    public function proofPhotoUrl()
    {
        return $this->getPhotoOriginalUrl($this->entity->proofPhoto());
    }

    /**
     * @return mixed|string
     * @throws PresenterException
     */
    public function IDCardLandscapeUrl()
    {
        return $this->getPhotoOriginalUrl($this->entity->iDCardsPhotos->where('type', PhotoTypeEnum::ID_CARD_LANDSCAPE)->first());
    }

    /**
     * @return mixed|string
     * @throws PresenterException
     */
    public function IDCardPortraitUrl()
    {
        return $this->getPhotoOriginalUrl($this->entity->iDCardsPhotos->where('type', PhotoTypeEnum::ID_CARD_PORTRAIT)->first());
    }

    /**
     * @return mixed
     */
    public function additionalClassroomsAsString()
    {
        return $this->additionalClassrooms->pluck('name')->implode(', ');
    }
}
