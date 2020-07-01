<?php


namespace App\Photos\Galleries;


use App\Core\PathsManager;


class GalleryPathsManager extends PathsManager
{
    /** @var string  */
    protected $rootPrefix = 'galleries';

    /** @var string Uploaded photos dir */
    protected $uploaded = 'uploaded';

    /** @var string Export dir */
    protected $export = 'export';

    /**
     * Prepare uploaded base dir
     *
     * @param Gallery $gallery
     *
     * @return string
     */
    public function uploadedDir(Gallery $gallery)
    {
        return $this->preparePath([$this->uploaded, $gallery->id]);
    }


    /**
     * Fool path to local export dir
     */
    public function exportLocalDirPath()
    {
        return $this->generateLocalPath($this->export);
    }

    /**
     * Base path to local export dir
     */
    public function exportLocalDirBasePath()
    {
        return $this->preparePath([$this->export]);
    }

    /**
     * Export file path for proof photos
     *
     * @param Gallery $gallery
     * @return string
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function proofExportFoolPathName(Gallery $gallery)
    {
        $fileName = 'proofs_'.$gallery->id .'_'. $gallery->present()->name() .'.zip';

        return $this->generateLocalPath($this->exportLocalDirBasePath(), $fileName);
    }

    /**
     * Export file path for passwords file csv
     *
     * @param Gallery $gallery
     * @return string
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function csvPasswordsExportFoolPathName(Gallery $gallery)
    {
        $fileName = $this->passwordsCsvFileName($gallery);
        return $this->generateLocalPath($this->exportLocalDirBasePath(), $fileName);
    }

    /**
     * File name for export passwords in csv
     *
     * @param \App\Photos\Galleries\Gallery $gallery
     * @return string
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function passwordsCsvFileName(Gallery $gallery)
    {
        return 'passwords_'.$gallery->id .'_'. $gallery->present()->name() .'.csv';
    }
}
