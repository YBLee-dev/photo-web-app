<?php


namespace App\Photos\Seasons;


use Webmagic\Core\Presenter\Presenter;

class SeasonPresenter extends Presenter
{
    protected $entity = Season::class;

    /**
     * @return string
     */
    public function zipBasePath()
    {
        return (new SeasonPathsManager())->seasonZipsBasePath($this->entity);
    }

    /**
     * @return string
     */
    public function zipLocalPath()
    {
        return (new SeasonPathsManager())->seasonZipsLocalPath($this->entity);
    }

    /**
     * @return string
     */
    public function zipUrl()
    {
        return (new SeasonPathsManager())->seasonZipsUrl($this->entity);
    }

    /**
     * @return string
     */
    public function csvDetailsLocalPath()
    {
        return (new SeasonPathsManager())->seasonDetailsCsvLocalPath($this->entity);
    }

    /**
     * @return string
     */
    public function csvDetailsBasePath()
    {
        return (new SeasonPathsManager())->seasonDetailsCsvBasePath($this->entity);
    }

    /**
     * @return string
     */
    public function csvDetailsFileName()
    {
        return (new SeasonPathsManager())->seasonDetailsCsvFileName($this->entity);
    }

    /**
     * @return string
     */
    public function xlsInvoiceBasePath()
    {
        return (new SeasonPathsManager())->seasonInvoiceXlsBasePath($this->entity);
    }

    /**
     * @return string
     */
    public function xlsInvoiceFileName()
    {
        return (new SeasonPathsManager())->seasonInvoiceXlsFileName($this->entity);
    }

    /**
     * @return string
     */
    public function xlsInvoiceLocalPath()
    {
        return (new SeasonPathsManager())->seasonInvoiceXlsLocalPath($this->entity);
    }
}
