<?php


namespace App\Photos\Seasons;


use App\Ecommerce\Export\AllOrdersInvoiceDetailsExport;
use App\Ecommerce\Export\AllOrdersPrintDetailsExport;
use App\Ecommerce\Orders\OrderExportService;
use App\Ecommerce\Orders\OrderPaymentStatusEnum;
use App\Photos\Galleries\Gallery;
use App\Photos\Photos\Photo;
use Illuminate\Support\Collection;
use Laracasts\Presenter\Exceptions\PresenterException;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class SeasonExportService extends OrderExportService
{
    /**
     * @param Season $season
     *
     * @return string
     * @throws PresenterException
     */
    public function prepareSeasonZip(Season $season)
    {
        $storageManager = new SeasonStorageManager();
        $storageManager->seasonZipsPrepareLocalDir();
        $zipPath = $season->present()->zipLocalPath();
        $zipArchive = $this->prepareZipFile($zipPath);

        // Add personal photos for orders
        $orders = $season->gallery->orders->where('payment_status', OrderPaymentStatusEnum::PAID);
        foreach ($orders as $order)
        {
            // Add printable photos
            if (! $order->isDigitalFull()) {
                $zipArchive = $this->addOrderPersonalPhotos($zipArchive, $order);
            }
        }

        // Add ID cards
        $zipArchive = $this->addAllIdCards($zipArchive, $season);

        // Add school photo
        $zipArchive = $this->addSchoolPhoto($zipArchive, $season->gallery);

        // Add common class photos
        $zipArchive = $this->addCommonClassPhotos($zipArchive, $season, 2);

        // Add staff common photos
        $zipArchive = $this->addStaffCommonPhoto($zipArchive, $season, 2);

        // Add mini wallet collage
        $zipArchive = $this->addMiniWalletPhotos($zipArchive, $season->gallery);

        // Add details CSV
        $zipArchive = $this->generateAndAddSeasonDetailsCsv($zipArchive, $season);

        // Add invoice
        $zipArchive = $this->generateAndAddSeasonInvoiceXsl($zipArchive, $season);

        $zipArchive->close();

        return $zipPath;
    }

    /**
     * @param ZipArchive $zipArchive
     * @param Gallery    $gallery
     *
     * @return ZipArchive
     * @throws PresenterException
     */
    protected function addMiniWalletPhotos(ZipArchive $zipArchive, Gallery $gallery)
    {
        /** @var Photo [] $miniWalletPhotos */
        $miniWalletPhotos = $gallery->miniWalletCollages;

        foreach ($miniWalletPhotos as $key => $miniWalletPhoto){
            $photoContent = $miniWalletPhoto->getFileContent();

            if(!$photoContent){
                continue;
            }

            $zipArchive->addFromString($miniWalletPhoto->printableFileName($key), $photoContent);
        }

        return $zipArchive;
    }

    /**
     * @param ZipArchive $zipArchive
     * @param Season     $season
     * @param int        $count
     *
     * @return ZipArchive
     * @throws PresenterException
     */
    protected function addCommonClassPhotos(ZipArchive $zipArchive, Season $season, int $count = 2)
    {
        /** @var Photo [] $commonClassPhotos */
        $commonClassPhotos = $season->gallery->classesCommonPhotos;

        foreach ($commonClassPhotos as $commonClassPhoto){
            $commonClassPhotoContent = $commonClassPhoto->getFileContent();

            if(!$commonClassPhotoContent){
                continue;
            }

            for($i = $count; $i > 0; $i--){
                $zipArchive->addFromString("{$commonClassPhoto->printableFileName($i)}", $commonClassPhotoContent);
            }
        }

        return $zipArchive;
    }

    /**
     * @param ZipArchive $zipArchive
     * @param Season     $season
     * @param int        $count
     *
     * @return ZipArchive
     * @throws PresenterException
     */
    protected function addStaffCommonPhoto(ZipArchive $zipArchive, Season $season, int $count = 2)
    {
        /** @var Photo [] $staffCommonPhotos */
        $staffCommonPhotos = $season->gallery->staffCommonPhotos;

        foreach ($staffCommonPhotos as $staffCommonPhoto){
            $photoContent = $staffCommonPhoto->getFileContent();

            if(!$photoContent){
                continue;
            }

            for($i = $count; $i > 0; $i--){
                $zipArchive->addFromString($staffCommonPhoto->printableFileName($i), $photoContent);
            }
        }

        return $zipArchive;
    }

    /**
     * @param ZipArchive $zipArchive
     * @param Season $season
     *
     * @return ZipArchive
     *
     * @throws PresenterException
     * @throws \Exception
     */
    protected function generateAndAddSeasonDetailsCsv(ZipArchive $zipArchive, Season $season)
    {
        $storageManager = new SeasonStorageManager();
        $storageManager->seasonDetailsPrepareLocalDir();

        $path = $season->present()->csvDetailsBasePath();
        $localDiskName = $storageManager->getLocalDiskName();

        $orders = $season->gallery->orders->where('payment_status', OrderPaymentStatusEnum::PAID);
        Excel::store(new AllOrdersPrintDetailsExport($orders), $path, $localDiskName);

        $fileName = $season->present()->csvDetailsFileName();
        $zipArchive->addFromString("{$this->ordersDetailsDir}/$fileName", file_get_contents($season->present()->csvDetailsLocalPath()));

        return $zipArchive;
    }

    /**
     * @param ZipArchive $zipArchive
     * @param Season $season
     *
     * @return ZipArchive
     *
     * @throws PresenterException
     * @throws \Exception
     */
    protected function generateAndAddSeasonInvoiceXsl(ZipArchive $zipArchive, Season $season)
    {
        $storageManager = new SeasonStorageManager();
        $storageManager->seasonDetailsPrepareLocalDir();

        $path = $season->present()->xlsInvoiceBasePath();
        $localDiskName = $storageManager->getLocalDiskName();

        $orders = $season->gallery->orders->where('payment_status', OrderPaymentStatusEnum::PAID);
        Excel::store(new AllOrdersInvoiceDetailsExport($orders), $path, $localDiskName);

        $fileName = $season->present()->xlsInvoiceFileName();
        $zipArchive->addFromString("{$this->ordersDetailsDir}/$fileName", file_get_contents($season->present()->xlsInvoiceLocalPath()));

        return $zipArchive;
    }
}
