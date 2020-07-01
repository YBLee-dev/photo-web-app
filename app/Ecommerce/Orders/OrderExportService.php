<?php


namespace App\Ecommerce\Orders;


use App\Core\StorageManager;
use App\Ecommerce\Export\OrderDetailsExport;
use App\Photos\Galleries\Gallery;
use App\Photos\Photos\Photo;
use App\Photos\Photos\PhotosFactory;
use App\Photos\Seasons\Season;
use Laracasts\Presenter\Exceptions\PresenterException;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class OrderExportService
{
    /** @var string  */
    protected $schoolPhotoDir = 'school-photo';

    /** @var string  */
    protected $ordersDetailsDir = 'details';

    /**
     * @param Order $order
     *
     * @return string
     * @throws PresenterException
     */
    public function prepareOrderZip(Order $order)
    {
        $orderStorageManager = new OrderStorageManager();
        $orderStorageManager->oneOrderZipsPrepareLocalDir();
        $zipPath = $order->present()->zipLocalPath();
        $zipArchive = $this->prepareZipFile($zipPath);

        // Add order details file
        $zipArchive = $this->addOrderDetailsFile($zipArchive, $order);

        // Add all personal photos to order
        $zipArchive = $this->addOrderPersonalPhotos($zipArchive, $order);

        $zipArchive->close();

        // Clean up
        $orderStorageManager->deleteOrderDetailsLocalCsvFile($order);

        return $zipPath;
    }

    /**
     * @param Order $order
     *
     * @return string
     * @throws PresenterException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    public function prepareDigitalOrderZip(Order $order)
    {
        $orderStorageManager = new OrderStorageManager();
        $orderStorageManager->oneOrderDigitalZipsPrepareLocalDir();
        $zipPath = $order->present()->zipDigitalLocalPath();
        $zipArchive = $this->prepareZipFile($zipPath);

        // Add all original photos to order
        $zipArchive = $this->addOrderOriginalPhotos($zipArchive, $order);

        $zipArchive->close();

        return $zipPath;
    }

    /**
     * @param ZipArchive $zipArchive
     * @param Order      $order
     *
     * @return ZipArchive
     * @throws PresenterException
     */
    protected function addOrderPersonalPhotos(ZipArchive $zipArchive, Order $order)
    {
        // Add printable photos
        $zipArchive = $this->addOrderLocalPrintablePhotos($zipArchive, $order);

        // Personal class photo
        $zipArchive = $this->addPersonalClassPhoto($zipArchive, $order);

        // Add free gift
        if($order->isFreeGiftIncluded()){
            $zipArchive = $this->addFreeGift($zipArchive, $order);
        }

        return $zipArchive;
    }

    /**
     * Prepare original photos for Digital zip
     *
     * @param ZipArchive $zipArchive
     * @param Order $order
     *
     * @return ZipArchive
     * @throws PresenterException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function addOrderOriginalPhotos(ZipArchive $zipArchive, Order $order)
    {
        $isDigitalFull = $order->isDigitalFull();

        // Add original photos
        $zipArchive = $this->addOrderRemoteOriginalPhotos($zipArchive, $order);

        // Personal class photo
        if($isDigitalFull) {
            $zipArchive = $this->addPersonalClassPhoto($zipArchive, $order);
        }
        // Add free gift
        if($order->isFreeGiftIncluded() && $isDigitalFull){
            $zipArchive = $this->addFreeGift($zipArchive, $order);
        }

        return $zipArchive;
    }

    /**
     * @param Order $order
     * @param ZipArchive $zipArchive
     *
     * @return ZipArchive
     * @throws PresenterException
     * @throws \Illuminate\Contracts\Filesystem\FileNotFoundException
     */
    protected function addOrderRemoteOriginalPhotos(ZipArchive $zipArchive, Order $order)
    {
        /** @var Photo [] $photos */
        $photos = $order->subGallery->originalPhotos;

        foreach ($photos as $photo) {
            $zipArchive->addFromString($photo->printableFileName(), $photo->getFileContent());
        }

        return $zipArchive;
    }

    /**
     * @param ZipArchive $zipArchive
     * @param Season     $season
     *
     * @return ZipArchive
     * @throws PresenterException
     */
    protected function addAllIdCards(ZipArchive $zipArchive, Season $season)
    {
        /** @var Photo [] $idCardPhotos */
        $subGalleries = $season->gallery->subGalleries;

        foreach ($subGalleries as $subGallery) {
            $idCardPhotos = $subGallery->person->iDCardsPhotos;

            foreach ($idCardPhotos as $idCardPhoto) {
                $idCardPhotoContent = $idCardPhoto->getFileContent();

                if(!$idCardPhotoContent){
                    continue;
                }

                $fileName = $idCardPhoto->printableFileName();
                $zipArchive->addFromString($fileName, $idCardPhotoContent);
            }
        }

        return $zipArchive;
    }

    /**
     * @param ZipArchive $zipArchive
     * @param Order      $order
     *
     * @return ZipArchive
     * @throws PresenterException
     */
    protected function addOrderDetailsFile(ZipArchive $zipArchive, Order $order)
    {
        $orderDetailsFilePath = $this->generateAndSaveOrderDetailsCsv($order);
        $orderDetailsFileName = basename($orderDetailsFilePath);
        $orderDetailsContent = file_get_contents($orderDetailsFilePath);

        $zipArchive->addFromString("{$this->ordersDetailsDir}/$orderDetailsFileName", $orderDetailsContent);

        return $zipArchive;
    }

    /**
     * @param ZipArchive $zipArchive
     * @param Order      $order
     *
     * @return ZipArchive
     * @throws PresenterException
     */
    protected function addFreeGift(ZipArchive $zipArchive, Order $order)
    {
        /** @var Photo $freeGiftPhoto */
        $freeGiftPhoto = $order->subGallery->person->freeGift();

        if(!$freeGiftPhoto) {
            return $zipArchive;
        }

        $freeGiftContent = $freeGiftPhoto->getFileContent();
        if(!$freeGiftContent) {
           return  $zipArchive;
        }

        // Add photo
        $fileName = $this->prepareFileName($freeGiftPhoto, $order);
        $zipArchive->addFromString($fileName, $freeGiftContent);

        return $zipArchive;
    }

    /**
     * @param ZipArchive $zipArchive
     * @param Gallery    $gallery
     *
     * @return ZipArchive
     * @throws PresenterException
     */
    protected function addSchoolPhoto(ZipArchive $zipArchive, Gallery $gallery)
    {
        // Add school photo
        $schoolPhoto = $gallery->schoolCommonPhoto();
        if($schoolPhoto && $schoolPhoto->isReadAble()) {
            $schoolPhotoContent = $schoolPhoto->getFileContent();
            $fileName = $schoolPhoto->printableFileName();

            $zipArchive->addFromString("{$this->schoolPhotoDir}/$fileName", $schoolPhotoContent);
        }


        return $zipArchive;
    }

    /**
     * @param ZipArchive $zipArchive
     * @param Order      $order
     *
     * @return ZipArchive
     * @throws PresenterException
     */
    protected function addPersonalClassPhoto(ZipArchive $zipArchive, Order $order)
    {
        // Add class photo
        /** @var Photo $personalClassPhoto */
        $personalClassPhoto = $order->subGallery->person->classPersonalPhoto();
        if ($personalClassPhoto && $personalClassPhoto->isReadAble()){
            $personalClassPhotoContent = $personalClassPhoto->getFileContent();
            $fileName = $this->prepareFileName($personalClassPhoto, $order);

            $zipArchive->addFromString($fileName, $personalClassPhotoContent);
        }

        return $zipArchive;
    }

    /**
     * @param Order      $order
     * @param ZipArchive $zipArchive
     *
     * @return ZipArchive
     * @throws PresenterException
     */
    protected function addOrderLocalPrintablePhotos(ZipArchive $zipArchive, Order $order)
    {
        /** @var Photo [] $photos */
        $photos = $order->printablePhotos;

        foreach ($photos as $photo) {
            $zipArchive->addFile($photo->present()->originalLocalPath(), $photo->printableFileName());
        }

        return $zipArchive;
    }

    /**
     * @param string $zipFileLocalPath
     *
     * @return ZipArchive
     */
    protected function prepareZipFile(string $zipFileLocalPath)
    {
        $zipArchive = new ZipArchive();
        $zipArchive->open($zipFileLocalPath, ZipArchive::CREATE);

        return $zipArchive;
    }

    /**
     * Generate one order CSV
     *
     * @param Order $order
     *
     * @return string
     * @throws PresenterException
     */
    public function generateAndSaveOrderDetailsCsv(Order $order)
    {
        // Prepare directory
        $orderStorageManager = new OrderStorageManager();
        $orderStorageManager->oneOrderDetailsCsvPrepareLocalDir();
        $path = $order->present()->csvDetailsBasePath();
        $localDiskName = $orderStorageManager->getLocalDiskName();


        Excel::store(new OrderDetailsExport($order), $path, $localDiskName);

        return $order->present()->csvDetailsLocalPath();
    }

    /**
     * @param Photo $photo
     * @param Order $order
     *
     * @return string
     * @throws PresenterException
     */
    protected function prepareFileName(Photo $photo, Order $order)
    {
        $photoFactory = new PhotosFactory();
        $schoolId = $order->gallery->school_id;
        $person = $order->subGallery->person;

        return $photoFactory->preparePrintablePhotoFileName(
            $photo,
            $schoolId,
            $order->id,
            $person->present()->prepareFullName(),
            $person->classroom
        );
    }
}
