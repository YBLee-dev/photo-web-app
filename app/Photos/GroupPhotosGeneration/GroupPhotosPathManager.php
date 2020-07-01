<?php


namespace App\Photos\GroupPhotosGeneration;

use App\Core\PathsManager;
use App\Photos\Galleries\Gallery;
use App\Photos\People\Person;

class GroupPhotosPathManager extends PathsManager
{
    /** @var string  */
    protected $rootPrefix = 'group-photos';

    /** @var string Common class photos directory path */
    protected $commonClassesPhotosPath = "common-classes-photos";

    /** @var string  */
    protected $personalClassesPhotosPath = "personal-classes-photos";

    /** @var string  */
    protected $staffCommonPhotos = "staff-photo";

    /** @var string  */
    protected $schoolPhotos = "school-photos";

    /** @var string  */
    protected $miniWalletCollage = "mini-wallet-collages";

    /**
     * @param Gallery $gallery
     * @param Person  $person
     *
     * @return string
     */
    public function personalClassPhotoPath(Gallery $gallery, Person $person): string
    {
        $fileName = "{$person->id}_$this->personalClassesPhotosPath.png";
        $dir = $this->personalClassPhotoDir($gallery);

        return $this->preparePath([$dir, $fileName]);
    }

    /**
     * Prepare basic dir
     *
     * @param Gallery $gallery
     *
     * @return string
     */
    public function personalClassPhotoDir(Gallery $gallery): string
    {
        return $this->preparePath([$this->personalClassesPhotosPath, $gallery->id]);
    }

    /**
     * @param Gallery $gallery
     * @param Person  $person
     *
     * @return mixed
     */
    public function personalClassPhotoLocalPath(Gallery $gallery, Person $person): string
    {
        return $this->generateLocalPath($this->personalClassPhotoPath($gallery, $person));
    }

    /**
     * @param Gallery $gallery
     * @param Person  $person
     *
     * @return string
     */
    public function personalClassPhotoUrl(Gallery $gallery, Person $person): string
    {
        return $this->getRemotePublicPath($this->personalClassPhotoPath($gallery, $person));
    }

    /**
     * @param Gallery $gallery
     *
     * @return string
     */
    public function schoolPhotoLocalPath(Gallery $gallery): string
    {
        $basePath = $this->schoolPhotoBasePath($gallery);

        return $this->generateLocalPath($basePath);
    }

    /**
     * @param Gallery $gallery
     *
     * @return string
     */
    public function schoolPhotoBasePath(Gallery $gallery): string
    {
        $fileName = "$this->schoolPhotos.png";

        return $this->preparePath([$this->schoolPhotos, $gallery->id, $fileName]);
    }

    /**
     * Prepare staff photo path
     *
     * @param Gallery $gallery
     *
     * @return string
     */
    public function schoolPhotoUrl(Gallery $gallery): string
    {
        $basePath = $this->schoolPhotoBasePath($gallery);

        return $this->getRemotePublicPath($basePath);
    }


    /**
     * Prepare staff common photo path
     *
     * @param Gallery $gallery
     *
     * @return mixed
     */
    public function staffPhotoFullLocalPath(Gallery $gallery): string
    {
        $basicPath = $this->staffPhotoBasePath($gallery);

        return $this->generateLocalPath($basicPath);
    }

    /**
     * Prepare staff photo path
     *
     * @param Gallery $gallery
     *
     * @return string
     */
    public function staffPhotoUrl(Gallery $gallery): string
    {
        $basicPath = $this->staffPhotoBasePath($gallery);

        return $this->getRemotePublicPath($basicPath);
    }

    /**
     * Prepare staff photo path
     *
     * @param Gallery $gallery
     *
     * @return string
     */
    public function staffPhotoBasePath(Gallery $gallery): string
    {
        $fileName = "$this->staffCommonPhotos.jpg";

        return $this->preparePath([$this->staffCommonPhotos, $gallery->id, $fileName]);
    }

    /**
     * Prepare path for gallery class photos local directory
     *
     * @param Gallery $gallery
     *
     * @return mixed
     */
    public function commonClassPhotoLocalDir(Gallery $gallery): string
    {
        $directory = $this->commonClassPhotoDir($gallery);

        return $this->generateLocalPath($directory);
    }

    /**
     * Prepare path to class photo directory for remote storage
     *
     * @param Gallery $gallery
     *
     * @return string
     */
    public function commonClassPhotoRemoteDir(Gallery $gallery): string
    {
        return $this->commonClassPhotoDir($gallery);
    }

    /**
     * Prepare remote public path for gallery photo
     *
     * @param Gallery $gallery
     * @param string  $classRoom
     *
     * @return string
     */
    public function commonClassPhotoRemoteUrl(Gallery $gallery, string $classRoom): string
    {
        $fileName = $this->commonClassPhotoFilename($classRoom);
        $directory = $this->commonClassPhotoDir($gallery);

        return $this->getRemotePublicPath($directory, $fileName);
    }

    /**
     * Prepare class photo local path
     *
     * @param Gallery $gallery
     * @param string  $classroomName
     *
     * @return mixed
     */
    public function commonClassPhotoLocalPath(Gallery $gallery, string $classroomName): string
    {
        $fileName = $this->commonClassPhotoFilename($classroomName);
        $directory = $this->commonClassPhotoDir($gallery);

        return $this->generateLocalPath($directory, $fileName);
    }

    /**
     * Prepare class photo filename
     *
     * @param string $classroomName
     *
     * @return string
     */
    public function commonClassPhotoFilename(string $classroomName): string
    {
        return strtolower(trim($classroomName)).'.png';
    }

    /**
     * Prepare gallery class photo directory
     *
     * @param Gallery $gallery
     *
     * @return string
     */
    public function commonClassPhotoDir(Gallery $gallery): string
    {
        return $this->preparePath([
            $this->commonClassesPhotosPath,
            $gallery->id
        ]);
    }

    /**
     * @param Gallery $gallery
     *
     * @return string
     */
    public function miniWalletCollageDir(Gallery $gallery)
    {
        return $this->preparePath([$this->miniWalletCollage, $gallery->id]);
    }

    /**
     * @param Gallery $gallery
     *
     * @return array
     */
    public function miniWalletUrls(Gallery $gallery)
    {
        $miniWalletFiles = (new GroupPhotosStorageManager())->miniWalletsPhotos($gallery);

        $urls = [];

        foreach ($miniWalletFiles as $file) {
            $urls[] = $this->getRemotePublicPath($file);
        }

        return $urls;
    }

    /**
     * @param Gallery $gallery
     * @param int     $count
     *
     * @return string
     */
    public function miniWalletCollageBasePath(Gallery $gallery, int $count)
    {
        $fileName = "{$gallery->id}_$count.png";
        $dir = $this->miniWalletCollageDir($gallery);

        return $this->preparePath([$dir, $fileName]);
    }

    /**
     * @param Gallery $gallery
     * @param int     $count
     *
     * @return mixed
     */
    public function miniWalletCollageLocalPath(Gallery $gallery, int $count)
    {
        $fileName = "{$gallery->id}_$count.png";
        $dir = $this->miniWalletCollageDir($gallery);

        return $this->generateLocalPath($dir, $fileName);
    }
}
