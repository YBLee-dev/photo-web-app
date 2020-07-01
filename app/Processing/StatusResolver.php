<?php


namespace App\Processing;


use App\Photos\Galleries\Gallery;
use App\Photos\Galleries\GalleryRepo;
use App\Processing\Processes\GroupPhotosProcessing\CommonClassPhotosPreparingProcess;
use App\Processing\Processes\GroupPhotosProcessing\GroupPhotosGalleryMoveToRemote;
use App\Processing\Processes\GroupPhotosProcessing\IDCardsForGalleryGenerationProcess;
use App\Processing\Processes\GroupPhotosProcessing\MiniWalletCollagesLocalGenerationProcess;
use App\Processing\Processes\GroupPhotosProcessing\PersonalClassPhotoPreparingProcess;
use App\Processing\Processes\GroupPhotosProcessing\ProofOnePhotoGenerationProcess;
use App\Processing\Processes\GroupPhotosProcessing\SchoolPhotoPreparingProcess;
use App\Processing\Processes\GroupPhotosProcessing\StaffPhotoPreparingProcess;
use App\Processing\Processes\InitialGalleryProcessing\CroppingSubGalleryPhotoProcess;
use App\Processing\Processes\InitialGalleryProcessing\LoadPhotosProcess;
use App\Processing\Processes\InitialGalleryProcessing\MoveGalleryPhotosToRemote;
use App\Processing\Processes\InitialGalleryProcessing\MoveSubGalleryPhotosToRemote;
use App\Processing\Processes\InitialGalleryProcessing\MoveUnprocessedGalleryToTmp;
use App\Processing\Processes\InitialGalleryProcessing\OrientSubGalleryOriginalLocalPhotosProcess;
use App\Processing\Processes\InitialGalleryProcessing\PreviewsOneLocalSubGalleryGenerateProcess;
use App\Processing\Processes\InitialGalleryProcessing\RemoveGalleyUploadDirectoryProcess;
use App\Processing\Processes\InitialGalleryProcessing\SubGalleriesDataPreparingProcess;
use App\Processing\Processes\RemovingProcessing\RemoveAllGalleryGroupPhotosProcess;
use App\Processing\ProcessRecords\ProcessRecord;
use App\Processing\ProcessRecords\ProcessRecordRepo;
use App\Processing\Scenarios\InitialGalleryProcessingScenario;
use App\Processing\Scenarios\ProcessableScenario;

class StatusResolver
{
    /**
     * @param int    $processableId
     * @param string $processableClass
     * @param string $processClass
     *
     * @return mixed
     * @throws \Exception
     */
    public function shortStatusForProcessableByProcess(int $processableId, string $processableClass, string $processClass)
    {
        $processRecords = (new ProcessRecordRepo())->getForProcessAndProcessable($processableId, $processableClass,$processClass);

        $status = call_user_func_array([$this, 'resolveShortStatusForProcRecords'], $processRecords->all());

        return $status;
    }

    /**
     * @param int $galleryId
     *
     * @return string
     * @throws \Exception
     */
    public function getGalleryShortStatus(int $galleryId)
    {
        $records = (new ProcessRecordRepo())->getForProcessable($galleryId, Gallery::class);
        // var_dump($galleryId);
        // if ($galleryId == 75) dd($records->all());

        $status = call_user_func_array([$this, 'resolveShortStatusForProcRecords'], $records->all());

        return $status;
    }

    /**
     * @param int    $processableId
     * @param string $processableClass
     * @param string $scenarioClass
     *
     * @return mixed
     * @throws \Exception
     */
    public function getProcessableByScenarioShortStatus(int $processableId, string $processableClass, string $scenarioClass)
    {
        $records = (new ProcessRecordRepo())->getForProcessableByScenario($scenarioClass, $processableId, $processableClass);

        $status = call_user_func_array([$this, 'resolveShortStatusForProcRecords'], $records->all());

        return $status;
    }


    /**
     * Resolve one status for set of processes
     *
     * @param ProcessRecord ...$records
     *
     * @return string
     */
    public function resolveShortStatusForProcRecords(ProcessRecord ... $records)
    {
        if(!count($records)) {
            return ProcessingStatusesEnum::NEWER_STARTED;
        }

       return call_user_func_array([$this, 'resolveShortStatus'], $records);
    }

    /**
     * Resolve short status for group of processes
     *
     * @param StatusResolvable ...$statusResolvables
     *
     * @return string
     */
    public function resolveShortStatus(StatusResolvable ... $statusResolvables)
    {
        $finishedCount = 0;
        $inProgressCount = 0;
        $inQueueCount = 0;
        $waitCount = 0;

        // Resolve and count statuses
        foreach ($statusResolvables as $statusResolvable) {
            // Mark all group failed if one of the processes is failed
            if (ProcessingStatusesEnum::FAILED()->is($statusResolvable->getStatus())){
                return ProcessingStatusesEnum::FAILED;
            }

            if (ProcessingStatusesEnum::IN_PROGRESS()->is($statusResolvable->getStatus())){
                $inProgressCount++;
            }

            if (ProcessingStatusesEnum::IN_QUEUE()->is($statusResolvable->getStatus())){
                $inQueueCount++;
            }

            if (ProcessingStatusesEnum::WAIT()->is($statusResolvable->getStatus())){
                $waitCount++;
            }

            if (ProcessingStatusesEnum::FINISHED()->is($statusResolvable->getStatus())) {
                $finishedCount++;
            }
        }

        // Resolve group statuses
        // Mark all as in progress if at least one is in progress and no failed processes
        if($inProgressCount > 0) {
            return ProcessingStatusesEnum::IN_PROGRESS;
        }

        //Mark also as in progress if part of tasks finished and the other are in queue
        if($inQueueCount > 0 && $finishedCount > 0) {
            return ProcessingStatusesEnum::IN_PROGRESS;
        }

        // Mark all as in queue if at least one is in queue and no in progress and failed processes
        if($inQueueCount > 0) {
            return ProcessingStatusesEnum::IN_QUEUE;
        }

        // Mark all as wait if at least one is wait and no in progress, failed or in queue processes
        if($waitCount > 0) {
            return ProcessingStatusesEnum::WAIT;
        }

        // Resolve as finished or never started depends of the processes count
        return count($statusResolvables) == $finishedCount ? ProcessingStatusesEnum::FINISHED : ProcessingStatusesEnum::NEWER_STARTED;
    }


    /**
     * @param ProcessableScenario $scenario
     *
     * @return array
     */
    public function getProcessesWithShortStatusesForScenario(ProcessableScenario $scenario)
    {
        $statuses = $scenario->getUniqueProcessesListWithShortStatuses();

        foreach ($statuses as $processClass => $status){
            $name = $this->getProcessShortName($processClass);

            if($name){
                $prepared_data[] = [
                    'short_name' => $name,
                    'status' => ProcessingStatusesEnum::NEWER_STARTED()->is($status) ? ProcessingStatusesEnum::WAIT : $status
                ];
            }
        }

        return $prepared_data ?? [];
    }

    /**
     * Get current gallery processes from db with short name
     *
     * @param int $galleryId
     * @return array
     * @throws \Exception
     */
    public function getGalleryCurrentProcessesWithShortStatus(int $galleryId)
    {
        /** @var Gallery $gallery */
        $gallery = (new GalleryRepo())->getByID($galleryId);
        $statuses = (new InitialGalleryProcessingScenario($galleryId, $gallery->user->ftp_login, $gallery->user->id))->getUniqueProcessesListWithShortStatuses();

        foreach ($statuses as $processClass => $status){
            $name = $this->getProcessShortName($processClass);

            if($name){
                $prepared_data[] = [
                    'short_name' => $name,
                    'status' => ProcessingStatusesEnum::NEWER_STARTED()->is($status) ? ProcessingStatusesEnum::WAIT : $status
                ];
            }
        }

        return $prepared_data ?? [];
    }

    /**
     * @param string $process
     * @return bool|string
     */
    protected function getProcessShortName(string $process)
    {
        switch ($process){
            case MoveUnprocessedGalleryToTmp::class :
                $name = 'Moving to tmp directory';
                break;
            case SubGalleriesDataPreparingProcess::class :
                $name = 'Prepare sub-galleries data';
                break;
            case LoadPhotosProcess::class :
                $name = 'Reading and loading photos data';
                break;
            case OrientSubGalleryOriginalLocalPhotosProcess::class :
                $name = 'Detecting and updating photos orientation';
                break;
            case PreviewsOneLocalSubGalleryGenerateProcess::class :
                $name = 'Previews generation';
                break;
            case CroppingSubGalleryPhotoProcess::class :
                $name = 'Cropping photos';
                break;
            case ProofOnePhotoGenerationProcess::class :
                $name = 'Proof photos generation';
                break;
            case MoveSubGalleryPhotosToRemote::class :
                $name = 'Saving sub galleries on S3';
                break;
            case MoveGalleryPhotosToRemote::class :
                $name = 'Saving gallery on S3';
                break;
            case RemoveGalleyUploadDirectoryProcess::class :
                $name = 'Removing uploaded directories';
                break;
            case InitialGalleryProcessingScenario::class :

            // Group photo generation scenario
            case RemoveAllGalleryGroupPhotosProcess::class :
                $name = 'Remove old gallery group photos';
                break;
            case CommonClassPhotosPreparingProcess::class :
                $name = 'Generate common class photos';
                break;
            case StaffPhotoPreparingProcess::class :
                $name = 'Generate stuff photos';
                break;
            case SchoolPhotoPreparingProcess::class :
                $name = 'Generate school photos';
                break;
            case PersonalClassPhotoPreparingProcess::class :
                $name = 'Generate personal class photos';
                break;
            case IDCardsForGalleryGenerationProcess::class :
                $name = 'Generate ID cards';
                break;
            case MiniWalletCollagesLocalGenerationProcess::class :
                $name = 'Generate mini wallet collages';
                break;
            case GroupPhotosGalleryMoveToRemote::class :
                $name = 'Saving group photos on S3';
                break;
            default:
                $name = $process;
                break;
        }

        return $name;
    }
}
