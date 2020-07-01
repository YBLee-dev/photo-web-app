<?php


namespace App\Photos\TemplatedPhotosGeneration;


use App\Core\PathsManager;
use App\Photos\Galleries\Gallery;
use App\Photos\People\Person;

class TemplatedPhotosPathsManager extends PathsManager
{
    /** @var string  */
    protected $rootPrefix = 'templated-photos';

    /** @var string Proofing images directory */
    protected $proofingPhotos = 'proofing-images';

    /** @var string  */
    protected $idCardPhotos = 'id-cards';

    /**
     * Prepare id card photo path
     *
     * @param Person $person
     *
     * @param string $type
     *
     * @return mixed
     */
    public function IdCardFullLocalPath(Person $person, string $type): string
    {
        $basicPath = $this->IdCardPhotoBasePath($person, $type);

        return $this->generateLocalPath($basicPath);
    }

    /**
     * Prepare staff photo path
     *
     * @param Person $person
     *
     * @param string $type
     *
     * @return string
     */
    public function IdCardPhotoBasePath(Person $person, string $type): string
    {
        $fileName = "{$person->id}_$type.png";
        $dir = $this->IdCardsDir($person->subgallery->gallery);

        return $this->preparePath([$dir, $fileName]);
    }

    /**
     * @param Gallery $gallery
     *
     * @return string
     */
    public function IdCardsDir(Gallery $gallery)
    {
        return $this->preparePath([$this->idCardPhotos, $gallery->id]);
    }

    /**
     * @param Gallery $gallery
     *
     * @return string
     */
    public function proofingPhotosDir(Gallery $gallery)
    {
        return $this->preparePath([$this->proofingPhotos, $gallery->id]);
    }

    /**
     * Prepare cropped faces base dir
     *
     * @param Person $person
     *
     * @return string
     */
    public function proofingPhotoPath(Person $person)
    {
        $fileName = "$person->id.jpg";
        $dir = $this->proofingPhotosDir($person->subgallery->gallery);

        return $this->generateLocalPath($dir, $fileName);
    }

    /**
     * Prepare cropped faces base dir
     *
     * @param Person $person
     *
     * @return string
     */
    public function proofingPhotoRemotePublicPath(Person $person)
    {
        $fileName = "$person->id.jpg";
        $dir = $this->proofingPhotosDir($person->subgallery->gallery);

        return $this->getRemotePublicPath($dir, $fileName);
    }
}
