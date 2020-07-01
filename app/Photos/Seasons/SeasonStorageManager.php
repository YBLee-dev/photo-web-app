<?php


namespace App\Photos\Seasons;


use App\Core\StorageManager;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Laracasts\Presenter\Exceptions\PresenterException;

class SeasonStorageManager extends StorageManager
{
    /**
     * Prepare local dir for season export
     */
    public function seasonZipsPrepareLocalDir()
    {
        $path = (new SeasonPathsManager())->seasonZipsBaseDir();

        $this->prepareLocalDir($path);
    }

    /**
     * @param Season $season
     * @throws FileNotFoundException
     * @throws PresenterException
     */
    public function moveSeasonZipToRemote(Season $season)
    {
         // Move ZIP file to remote
        $this->moveFileToRemote($season->present()->zipBasePath());
    }

    /**
     * @param Season $season
     * @return bool
     * @throws PresenterException
     */
    public function isSeasonZipFileExists(Season $season)
    {
        return $this->getRemoteStorage()->exists($season->present()->zipBasePath());
    }

    /**
     * Prepare local dir for season details files
     */
    public function seasonDetailsPrepareLocalDir()
    {
        $path = (new SeasonPathsManager())->seasonDetailsBaseDir();

        $this->prepareLocalDir($path);
    }

    /**
     * @param Season $season
     * @throws PresenterException
     */
    public function deleteSeasonDetailsLocalFiles(Season $season)
    {
        $this->getLocalTMPStorage()->delete($season->present()->csvDetailsBasePath());
        $this->getLocalTMPStorage()->delete($season->present()->xlsInvoiceBasePath());
    }
}
