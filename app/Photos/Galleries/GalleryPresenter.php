<?php


namespace App\Photos\Galleries;


use App\Photos\Photos\PhotoUrlsPresentableTrait;
use App\Processing\ProcessingStatusesEnum;
use App\Processing\Scenarios\GroupPhotosGenerationScenario;
use App\Processing\StatusResolver;
use Exception;
use Laracasts\Presenter\Exceptions\PresenterException;
use Webmagic\Core\Presenter\Presenter;

class GalleryPresenter extends Presenter
{
    use PhotoUrlsPresentableTrait;

    /**
     * @return string
     */
    public function name()
    {
        return "{$this->entity->school->name} - {$this->entity->season->name}";
    }

    /**
     * @return string
     */
    public function schoolName()
    {
        return "{$this->entity->school->name}";
    }

    /**
     * Return gallery current short status
     *
     * @return string
     * @throws Exception
     */
    public function shortStatus()
    {
        if(GalleryStatusEnum::DELETING()->is($this->entity->status)){
            return $this->entity->status;
        }

        $status = (new StatusResolver())->getGalleryShortStatus($this->entity->id);

        if(ProcessingStatusesEnum::WAIT()->is($status)) {
            return ProcessingStatusesEnum::IN_PROGRESS;
        }

        return $status;
    }

    /**
     * Get array of current gallery process with statuses
     *
     * @return array
     * @throws Exception
     */
    public function currentStatuses()
    {
       return (new StatusResolver())->getGalleryCurrentProcessesWithShortStatus($this->entity->id);
    }

    /**
     * Get array of current gallery group photos processes with statuses
     *
     * @return array
     * @throws Exception
     */
    public function getGalleryGroupProcesses()
    {
        return (new StatusResolver())->getProcessesWithShortStatusesForScenario( new GroupPhotosGenerationScenario($this->id));
    }

    /**
     * Return staff photo URL
     *
     * @return string
     * @throws PresenterException
     */
    public function staffPhotoUrl()
    {
        $teachers = $this->entity->teachers;
        if(!count($teachers)) {
            return '';
        }

        return $this->getPhotoOriginalUrl($teachers->first()->staffCommonPhoto());
    }

    /**
     * Return staff photo URL
     *
     * @return string
     * @throws PresenterException
     */
    public function schoolPhotoUrl()
    {
        return $this->getPhotoOriginalUrl($this->entity->schoolCommonPhoto());
    }

    /**
     * Prepare path for uploading directory
     *
     * @return mixed
     */
    public function processingGalleryPath()
    {
        return (new GalleryPathsManager())->uploadedDir($this->entity);
    }

    /**
     * Return fool path & file name for export proofs
     *
     * @return string
     * @throws PresenterException
     */
    public function proofExportFullPath()
    {
        return (new GalleryPathsManager())->proofExportFoolPathName($this->entity);
    }

    /**
     * Return fool path & file name for export passwords in csv
     *
     * @return string
     * @throws PresenterException
     */
    public function csvPasswordsFullPath()
    {
        return (new GalleryPathsManager())->csvPasswordsExportFoolPathName($this->entity);
    }

    /**
     * Return name for csv passwords file
     *
     * @return string
     */
    public function csvPasswordsFileName()
    {
        return (new GalleryPathsManager())->passwordsCsvFileName($this->entity);
    }
}
