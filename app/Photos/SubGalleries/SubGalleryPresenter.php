<?php


namespace App\Photos\SubGalleries;


use App\Photos\Photos\PhotoUrlsPresentableTrait;
use App\Processing\Scenarios\AddPhotoToSubgalleryScenario;
use App\Processing\StatusResolver;
use Illuminate\Filesystem\Filesystem;
use Laracasts\Presenter\Exceptions\PresenterException;
use Symfony\Component\Finder\SplFileInfo;
use Webmagic\Core\Presenter\Presenter;

class SubGalleryPresenter extends Presenter
{
    use PhotoUrlsPresentableTrait;

    /**
     * @return SplFileInfo[]
     */
    public function uploadedLocalFiles()
    {
        return (new SubGalleryPathsManager())->uploadedLocalFiles($this->entity);
    }

    /**
     * @return string
     * @throws PresenterException
     */
    public function mainPhotoOriginalURL()
    {
        return $this->getPhotoOriginalUrl($this->entity->mainPhoto());
    }

    /**
     * @return string
     * @throws PresenterException
     */
    public function mainPhotoPreviewUrl()
    {
        return $this->getPhotoPreviewUrl($this->entity->mainPhoto());
    }

    /**
     * Return path to sub gallery uploaded main photo
     *
     * @return mixed|SplFileInfo
     */
    public function mainPhotoUploadedLocalPath()
    {
        $subGalleryProcessingLocalPath = (new SubGalleryPathsManager())->uploadedLocalPath($this->entity);

        $files = (new Filesystem())->files($subGalleryProcessingLocalPath);

        return array_first($files);
    }

    /**
     * Get array of current sub gallery process with statuses
     *
     * @return array
     * @throws \Exception
     */
    public function currentStatuses()
    {
        return (new StatusResolver())->getProcessesWithShortStatusesForScenario( new AddPhotoToSubgalleryScenario($this->id));
    }
}
