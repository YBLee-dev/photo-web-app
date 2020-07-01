<?php


namespace App\Photos\Photos;


use Webmagic\Core\Presenter\Presenter;

class PhotoPresenter extends Presenter
{
    /**
     * @return mixed
     */
    public function originalLocalPath()
    {
        if(PhotoTypeEnum::ORIGINAL()->is($this->entity->type)) {
            return (new PhotoPathsManager())->originalsLocalPath($this->entity);
        }

        if(PhotoTypeEnum::PROOF()->is($this->entity->type)) {
            return (new PhotoPathsManager())->proofsLocalPath($this->entity);
        }

        if(PhotoTypeEnum::CROPPED_FACE()->is($this->entity->type)) {
            return (new PhotoPathsManager())->croppedFacesLocalPath($this->entity);
        }

        if(PhotoTypeEnum::MINI_WALLET_COLLAGE()->is($this->entity->type)) {
            return (new PhotoPathsManager())->miniWalletCollagesLocalPath($this->entity);
        }

        if(PhotoTypeEnum::COMMON_CLASS_PHOTO()->is($this->entity->type)) {
            return (new PhotoPathsManager())->classCommonPhotosLocalPath($this->entity);
        }

        if(PhotoTypeEnum::PERSONAL_CLASS_PHOTO()->is($this->entity->type)) {
            return (new PhotoPathsManager())->classPersonalPhotosLocalPath($this->entity);
        }

        if(PhotoTypeEnum::STAFF_PHOTO()->is($this->entity->type)) {
            return (new PhotoPathsManager())->staffPhotosLocalPath($this->entity);
        }

        if(PhotoTypeEnum::SCHOOL_PHOTO()->is($this->entity->type)) {
            return (new PhotoPathsManager())->schoolPhotosLocalPath($this->entity);
        }

        if(PhotoTypeEnum::ID_CARD_PORTRAIT()->is($this->entity->type)) {
            return (new PhotoPathsManager())->iDCardsPortraitLocalPath($this->entity);
        }

        if(PhotoTypeEnum::ID_CARD_LANDSCAPE()->is($this->entity->type)) {
            return (new PhotoPathsManager())->iDCardsLandscapeLocalPath($this->entity);
        }

        if(PhotoTypeEnum::FREE_GIFT()->is($this->entity->type)) {
            return (new PhotoPathsManager())->freeGiftsLocalPath($this->entity);
        }

        if(PhotoTypeEnum::PRINTABLE()->is($this->entity->type)) {
            return (new PhotoPathsManager())->printableLocalPath($this->entity);
        }
    }

    /**
     * @return mixed
     */
    public function previewLocalPath()
    {
        if(PhotoTypeEnum::ORIGINAL()->is($this->entity->type)) {
            return (new PhotoPathsManager())->previewsLocalPath($this->entity);
        }

        return $this->originalLocalPath();
    }

    /**
     * @return string
     */
    public function originalBasePath()
    {
        if(PhotoTypeEnum::ORIGINAL()->is($this->entity->type)) {
            return (new PhotoPathsManager())->originalsLocalBasePath($this->entity);
        }

        if(PhotoTypeEnum::PROOF()->is($this->entity->type)) {
            return (new PhotoPathsManager())->proofsLocalBasePath($this->entity);
        }

        if(PhotoTypeEnum::CROPPED_FACE()->is($this->entity->type)) {
            return (new PhotoPathsManager())->croppedFacesLocalBasePath($this->entity);
        }

        if(PhotoTypeEnum::MINI_WALLET_COLLAGE()->is($this->entity->type)) {
            return (new PhotoPathsManager())->miniWalletCollagesLocalBasePath($this->entity);
        }

        if(PhotoTypeEnum::COMMON_CLASS_PHOTO()->is($this->entity->type)) {
            return (new PhotoPathsManager())->classCommonPhotosLocalBasePath($this->entity);
        }

        if(PhotoTypeEnum::PERSONAL_CLASS_PHOTO()->is($this->entity->type)) {
            return (new PhotoPathsManager())->classPersonalPhotosLocalBasePath($this->entity);
        }

        if(PhotoTypeEnum::STAFF_PHOTO()->is($this->entity->type)) {
            return (new PhotoPathsManager())->staffPhotosLocalBasePath($this->entity);
        }

        if(PhotoTypeEnum::SCHOOL_PHOTO()->is($this->entity->type)) {
            return (new PhotoPathsManager())->schoolPhotosLocalBasePath($this->entity);
        }

        if(PhotoTypeEnum::ID_CARD_PORTRAIT()->is($this->entity->type)) {
            return (new PhotoPathsManager())->iDCardsPortraitLocalBasePath($this->entity);
        }

        if(PhotoTypeEnum::ID_CARD_LANDSCAPE()->is($this->entity->type)) {
            return (new PhotoPathsManager())->iDCardsLandscapeLocalBasePath($this->entity);
        }

        if(PhotoTypeEnum::FREE_GIFT()->is($this->entity->type)) {
            return (new PhotoPathsManager())->freeGiftsLocalBasePath($this->entity);
        }

        if(PhotoTypeEnum::PRINTABLE()->is($this->entity->type)) {
            return (new PhotoPathsManager())->printableLocalBasePath($this->entity);
        }
    }

    /**
     * @return string
     */
    public function previewLocalBasePath()
    {
        if(PhotoTypeEnum::ORIGINAL()->is($this->entity->type)) {
            return (new PhotoPathsManager())->previewsLocalBasePath($this->entity);
        }

        return $this->originalBasePath();
    }

    /**
     * @return string
     */
    public function originalUrl()
    {
        if(PhotoTypeEnum::ORIGINAL()->is($this->entity->type)) {
            return (new PhotoPathsManager())->originalsUrl($this->entity);
        }

        if(PhotoTypeEnum::PROOF()->is($this->entity->type)) {
            return (new PhotoPathsManager())->proofsUrl($this->entity);
        }

        if(PhotoTypeEnum::CROPPED_FACE()->is($this->entity->type)) {
            return (new PhotoPathsManager())->croppedFacesUrl($this->entity);
        }

        if(PhotoTypeEnum::MINI_WALLET_COLLAGE()->is($this->entity->type)) {
            return (new PhotoPathsManager())->miniWalletCollagesUrl($this->entity);
        }

        if(PhotoTypeEnum::COMMON_CLASS_PHOTO()->is($this->entity->type)) {
            return (new PhotoPathsManager())->classCommonPhotosUrl($this->entity);
        }

        if(PhotoTypeEnum::PERSONAL_CLASS_PHOTO()->is($this->entity->type)) {
            return (new PhotoPathsManager())->classPersonalPhotosUrl($this->entity);
        }

        if(PhotoTypeEnum::STAFF_PHOTO()->is($this->entity->type)) {
            return (new PhotoPathsManager())->staffPhotosUrl($this->entity);
        }

        if(PhotoTypeEnum::SCHOOL_PHOTO()->is($this->entity->type)) {
            return (new PhotoPathsManager())->schoolPhotosUrl($this->entity);
        }

        if(PhotoTypeEnum::ID_CARD_PORTRAIT()->is($this->entity->type)) {
            return (new PhotoPathsManager())->iDCardsPortraitUrl($this->entity);
        }

        if(PhotoTypeEnum::ID_CARD_LANDSCAPE()->is($this->entity->type)) {
            return (new PhotoPathsManager())->iDCardsLandscapeUrl($this->entity);
        }

        if(PhotoTypeEnum::FREE_GIFT()->is($this->entity->type)) {
            return (new PhotoPathsManager())->freeGiftsUrl($this->entity);
        }

        if(PhotoTypeEnum::PRINTABLE()->is($this->entity->type)) {
            return (new PhotoPathsManager())->printableUrl($this->entity);
        }
    }

    /**
     * @return string
     */
    public function baseDir()
    {
        if(PhotoTypeEnum::ORIGINAL()->is($this->entity->type)) {
            return (new PhotoPathsManager())->originalsBaseDir();
        }

        if(PhotoTypeEnum::PROOF()->is($this->entity->type)) {
            return (new PhotoPathsManager())->previewsBaseDir();
        }

        if(PhotoTypeEnum::CROPPED_FACE()->is($this->entity->type)) {
            return (new PhotoPathsManager())->croppedFacesBaseDir();
        }

        if(PhotoTypeEnum::MINI_WALLET_COLLAGE()->is($this->entity->type)) {
            return (new PhotoPathsManager())->miniWalletCollagesBaseDir();
        }

        if(PhotoTypeEnum::COMMON_CLASS_PHOTO()->is($this->entity->type)) {
            return (new PhotoPathsManager())->classCommonPhotosBaseDir();
        }

        if(PhotoTypeEnum::PERSONAL_CLASS_PHOTO()->is($this->entity->type)) {
            return (new PhotoPathsManager())->classPersonalPhotosBaseDir();
        }

        if(PhotoTypeEnum::STAFF_PHOTO()->is($this->entity->type)) {
            return (new PhotoPathsManager())->staffPhotosBaseDir();
        }

        if(PhotoTypeEnum::SCHOOL_PHOTO()->is($this->entity->type)) {
            return (new PhotoPathsManager())->schoolPhotosBaseDir();
        }

        if(PhotoTypeEnum::ID_CARD_PORTRAIT()->is($this->entity->type)) {
            return (new PhotoPathsManager())->iDCardsLandscapeBaseDir();
        }

        if(PhotoTypeEnum::ID_CARD_LANDSCAPE()->is($this->entity->type)) {
            return (new PhotoPathsManager())->iDCardsLandscapeBaseDir();
        }

        if(PhotoTypeEnum::FREE_GIFT()->is($this->entity->type)) {
            return (new PhotoPathsManager())->freeGiftsBaseDir();
        }

        if(PhotoTypeEnum::PRINTABLE()->is($this->entity->type)) {
            return (new PhotoPathsManager())->previewsBaseDir();
        }
    }

    /**
     * @return string
     */
    public function previewUrl()
    {
        if(PhotoTypeEnum::ORIGINAL()->is($this->entity->type)) {
            return (new PhotoPathsManager())->previewsUrl($this->entity);
        }

        return $this->originalUrl();
    }
}
