<?php


namespace App\Photos\Seasons;


use App\Core\PathsManager;
use Carbon\Carbon;

class SeasonPathsManager extends PathsManager
{
    /** @var string  */
    protected $rootPrefix = 'orders';

    protected $directories = [
       'seasonOrdersZips' => 'season-zips',
       'seasonOrdersDetails' => 'season-details'
    ];

    /**
     * @return string
     */
    public function seasonZipsBaseDir()
    {
        return $this->preparePath([$this->directories['seasonOrdersZips']]);
    }

    /**
     * @param Season $season
     * @return string
     */
    public function seasonZipsLocalPath(Season $season)
    {
        $fileName = $this->prepareSeasonZipFileName($season);

        return $this->generateLocalPath($this->seasonZipsBaseDir(), $fileName);
    }

    /**
     * @param Season $season
     *
     * @return string
     */
    protected function prepareSeasonZipFileName(Season $season)
    {
        return 'season_export_'.Carbon::now()->format('Y-m').'_'.$season->gallery->present()->name.'.zip';
    }

    /**
     * @param Season $season
     *
     * @return string
     */
    public function seasonZipsBasePath(Season $season)
    {
        $fileName = $this->prepareSeasonZipFileName($season);

        return $this->preparePath([$this->seasonZipsBaseDir(), $fileName]);
    }

    /**
     * @param Season $season
     * @return string
     */
    public function seasonZipsUrl(Season $season)
    {
        $fileName = $this->prepareSeasonZipFileName($season);

        return $this->getRemotePublicPath($this->seasonZipsBaseDir(), $fileName);
    }

    /**
     * @param Season $season
     * @return string
     */
    public function seasonDetailsCsvFileName(Season $season)
    {
        return "Season_{$season->gallery->present()->namee}_export_".Carbon::now()->format('Y-m').'.csv';
    }

    /**
     * @param Season $season
     *
     * @return string
     */
    public function seasonDetailsCsvBasePath(Season $season)
    {
        $fileName = $this->seasonDetailsCsvFileName($season);

        return $this->preparePath([$this->seasonDetailsBaseDir(), $fileName]);
    }

    /**
     * @return string
     */
    public function seasonDetailsBaseDir()
    {
        return $this->preparePath([$this->directories['seasonOrdersDetails']]);
    }

    /**
     * @param Season $season
     * @return string
     */
    public function seasonDetailsCsvLocalPath(Season $season)
    {
        return $this->generateLocalPath($this->seasonDetailsCsvBasePath($season));
    }

    /**
     * @param Season $season
     *
     * @return string
     */
    public function seasonInvoiceXlsBasePath(Season $season)
    {
        $fileName = $this->seasonInvoiceXlsFileName($season);

        return $this->preparePath([$this->seasonDetailsBaseDir(), $fileName]);
    }

    /**
     * @param Season $season
     * @return string
     */
    public function seasonInvoiceXlsFileName(Season $season)
    {
        return "Season_{$season->gallery->present()->name}_invoice_".Carbon::now()->format('Y-m').'.xls';
    }

    /**
     * @param Season $season
     * @return string
     */
    public function seasonInvoiceXlsLocalPath(Season $season)
    {
        return $this->generateLocalPath($this->seasonInvoiceXlsBasePath($season));
    }
}
