<?php


namespace App\Photos\Photos;


use App\Core\PathsManager;

/**
 * Class PhotoPathsManager
 *
 * @package App\Photos\Photos
 *
 * @method string originalsBaseDir()
 * @method string originalsLocalPath(Photo $photo)
 * @method string originalsLocalBasePath(Photo $photo)
 * @method string originalsUrl(Photo $photo)
 * @method string previewsBaseDir()
 * @method string previewsLocalPath(Photo $photo)
 * @method string previewsLocalBasePath(Photo $photo)
 * @method string previewsUrl(Photo $photo)
 * @method string proofsBaseDir()
 * @method string proofsLocalPath(Photo $photo)
 * @method string proofsLocalBasePath(Photo $photo)
 * @method string proofsUrl(Photo $photo)
 * @method string croppedFacesBaseDir()
 * @method string croppedFacesLocalPath(Photo $photo)
 * @method string croppedFacesLocalBasePath(Photo $photo)
 * @method string croppedFacesUrl(Photo $photo)
 * @method string miniWalletCollagesBaseDir()
 * @method string miniWalletCollagesLocalPath(Photo $photo)
 * @method string miniWalletCollagesLocalBasePath(Photo $photo)
 * @method string miniWalletCollagesUrl(Photo $photo)
 * @method string schoolPhotosBaseDir()
 * @method string schoolPhotosLocalPath(Photo $photo)
 * @method string schoolPhotosLocalBasePath(Photo $photo)
 * @method string schoolPhotosUrl(Photo $photo)
 * @method string classCommonPhotosBaseDir()
 * @method string classCommonPhotosLocalPath(Photo $photo)
 * @method string classCommonPhotosLocalBasePath(Photo $photo)
 * @method string classCommonPhotosUrl(Photo $photo)
 * @method string classPersonalPhotosBaseDir()
 * @method string classPersonalPhotosLocalPath(Photo $photo)
 * @method string classPersonalPhotosLocalBasePath(Photo $photo)
 * @method string classPersonalPhotosUrl(Photo $photo)
 * @method string staffPhotosBaseDir()
 * @method string staffPhotosLocalPath(Photo $photo)
 * @method string staffPhotosLocalBasePath(Photo $photo)
 * @method string staffPhotosUrl(Photo $photo)
 * @method string iDCardsPortraitBaseDir()
 * @method string iDCardsPortraitLocalPath(Photo $photo)
 * @method string iDCardsPortraitLocalBasePath(Photo $photo)
 * @method string iDCardsPortraitUrl(Photo $photo)
 * @method string iDCardsLandscapeBaseDir()
 * @method string iDCardsLandscapeLocalPath(Photo $photo)
 * @method string iDCardsLandscapeLocalBasePath(Photo $photo)
 * @method string iDCardsLandscapeUrl(Photo $photo)
 * @method string freeGiftsBaseDir()
 * @method string freeGiftsLocalPath(Photo $photo)
 * @method string freeGiftsLocalBasePath(Photo $photo)
 * @method string freeGiftsUrl(Photo $photo)
 * @method string printableBaseDir()
 * @method string printableLocalPath(Photo $photo)
 * @method string printableLocalBasePath(Photo $photo)
 * @method string printableUrl(Photo $photo) 
 */
class PhotoPathsManager extends PathsManager
{
    /** @var string */
    protected $rootPrefix = 'photos';

    /** @var array  */
    protected $directories = [
        'originals'    => 'originals',
        'previews'     => 'previews',
        'proofs'       => 'proofs',
        'croppedFaces' => 'cropped-faces',
        'miniWalletCollages' => 'mini-wallet-collages',
        'schoolPhotos' => 'school-common',
        'classCommonPhotos' => 'class-common',
        'classPersonalPhotos' => 'class-personal',
        'staffPhotos' => 'staff',
        'iDCardsPortrait' => 'id-cards',
        'iDCardsLandscape' => 'id-cards',
        'freeGifts' => 'free-gifts',
        'printable' => 'printable',
    ];

    /**
     * @param string $dirKey
     * @param Photo  $photo
     *
     * @return mixed
     */
    protected function localPath(string $dirKey, Photo $photo)
    {
        return $this->generateLocalPath($this->baseDir($dirKey), $photo->fileName());
    }

    /**
     * @param string $dirKey
     * @param Photo  $photo
     *
     * @return mixed
     */
    protected function url(string $dirKey, Photo $photo)
    {
        return $this->getRemotePublicPath($this->baseDir($dirKey), $photo->fileName());
    }

    /**
     * @param string $dirKey
     * @param Photo  $photo
     *
     * @return mixed
     */
    protected function localBasePath(string $dirKey, Photo $photo)
    {
        return $this->preparePath([$this->baseDir($dirKey), $photo->fileName()]);
    }

    /**
     * @param string $dirKey
     *
     * @return string
     */
    protected function baseDir(string $dirKey)
    {
        return $this->preparePath([$this->directories[$dirKey]]);
    }

    /**
     * @param $method
     * @param $args
     *
     * @return mixed|string
     * @throws \Exception
     */
    public function __call($method, $args)
    {
        try {
            if (strpos($method, 'LocalPath') !== false) {
                $dirKey = str_replace('LocalPath', '', $method);
                return $this->localPath($dirKey, $args[0]);
            }

            if (strpos($method, 'BaseDir') !== false) {
                $dirKey = str_replace('BaseDir', '', $method);
                return $this->baseDir($dirKey);
            }

            if (strpos($method, 'LocalBasePath') !== false) {
                $dirKey = str_replace('LocalBasePath', '', $method);
                return $this->localBasePath($dirKey, $args[0]);
            }

            if (strpos($method, 'Url') !== false) {
                $dirKey = str_replace('Url', '', $method);
                return $this->url($dirKey, $args[0]);
            }

            throw new \Exception("Method $method not allowed in " . get_class($this));

        } catch (\Exception $e) {
            throw new \Exception("Method $method not allowed in " . get_class($this));
        }
    }
}
