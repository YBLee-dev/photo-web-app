<?php


namespace App\Photos\Galleries;

use App\Photos\Export\GalleryPasswordsExport;
use App\Photos\People\Person;
use App\Photos\Photos\Photo;
use App\Photos\Photos\PhotosFactory;
use Laracasts\Presenter\Exceptions\PresenterException;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class GalleryExportService
{
    /**
     * Export proof & class photos
     *
     * @param Gallery $gallery
     * @return mixed
     * @throws PresenterException
     */
    public function proofPhotosExport(Gallery $gallery)
    {
        (new GalleryStorageManager())->prepareLocalExportDir();

        $path = $gallery->present()->proofExportFullPath();

        $zipArchive = new ZipArchive();
        $zipArchive->open($path, ZipArchive::CREATE | ZipArchive::OVERWRITE);

        // Proof photos
        foreach ($gallery->subGalleries as $subGallery){
            $file_content = $subGallery->person->proofPhoto()->getFileContent();
            $file_name = $this->prepareFileName($subGallery->person->proofPhoto(), $subGallery->person);

            $zipArchive->addFromString($file_name, $file_content);
        }

        // Add passwords CSV
        $zipArchive = $this->generateAndAddGalleryPasswordsCsv($zipArchive, $gallery);

        $zipArchive->close();

        return $path;
    }

    /**
     * Generate, add to zip and delete csv file with passwords info
     *
     * @param \ZipArchive $zipArchive
     * @param \App\Photos\Galleries\Gallery $gallery
     * @return \ZipArchive
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    protected function generateAndAddGalleryPasswordsCsv(ZipArchive $zipArchive, Gallery $gallery)
    {
        $full_path = $gallery->present()->csvPasswordsFullPath();
        $file_name = $gallery->present()->csvPasswordsFileName();

        $storageManager = new GalleryPathsManager();
        $path =  'tmp/' . $storageManager->exportLocalDirBasePath();

        Excel::store(new GalleryPasswordsExport($gallery), $path.'/'.$file_name);

        $zipArchive->addFromString($file_name, file_get_contents($full_path));

        unlink($full_path);

        return $zipArchive;
    }

    /**
     * @param Photo $photo
     * @param \App\Photos\People\Person $person
     * @return string
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    protected function prepareFileName(Photo $photo, Person $person)
    {
        $photoFactory = new PhotosFactory();

        return $photoFactory->prepareProofPhotoFileName(
            $photo,
            $person->id,
            $person->present()->name(),
            $person->classroom
        );
    }
}
