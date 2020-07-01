<?php

namespace App\Services;

use App\Ecommerce\Export\AllOrdersInvoiceDetailsExport;
use App\Ecommerce\Export\AllOrdersPrintDetailsExport;
use App\Ecommerce\Export\OrderDetailsExport;
use App\Photos\SubGalleries\SubGalleryService;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
use Maatwebsite\Excel\Facades\Excel;
use ZipArchive;

class GenerateZipWithPrintablePhotos
{
    protected $dpi = 300;

    protected $background_width = 10;

    protected $background_height = 8;

    protected $text_y = 7.5;

    protected $text_x = 5;

    /**
     * Count inches to pixels
     *
     * @param $inches
     * @return float|int
     */
    protected function countPixels($inches)
    {
        return $inches * $this->dpi;
    }

    /**
     * Start to crop and resize image by parameters,
     * generate zip and save on s3
     *
     * @param Model $order
     * @param array $items
     */
    public function generateForOrder(Model $order, array $items)
    {
        logger('Preparing zip with printable photos for order: '.$order->id);

        $local_zips_path = Storage::disk('public')->path('zips/orders_printable');
        $text = '#'.$order->id.' / '.$order->subgallery->name.' ('.$order->gallery->name.')';

        exec('chmod 775 -R '.storage_path());

        if (! file_exists($local_zips_path)) {
            mkdir($local_zips_path, '0777', true);
        }

        $hash_path = hash('adler32', time().$order->id);
        logger('hash: '.$hash_path);

        foreach ($items as $item) {
            $this->createCompositions(
                Storage::disk('s3')->url('original/' . $item['image']),
                $item['quantity'],
                $item['size'],
                $item['height'],
                $item['width'],
                $item['x'],
                $item['y'],
                $text,
                $hash_path
            );
        }

        if($order['free_gift']){
            /** @var SubGalleryService $service */
            $service = app()->make(SubGalleryService::class);
            $subPath = $service->getStoragePath($order->subgallery);

            $baseImagePath = $service->getBasicPhoto($subPath);

            if(! is_null($baseImagePath)){
                $this->createFreeGift($baseImagePath, $hash_path);
            }
        }

        $this->createExportOrderDetails($order, $hash_path);

        $this->generateZip($order, $hash_path);
        $this->saveImagesOnS3($order, $hash_path);
        $this->deleteLocalFiles($hash_path);
    }

    /**
     * Generate free gift photo
     *
     * @param $imagePath
     * @param $hashPath
     */
    protected function createFreeGift($imagePath, $hashPath)
    {
        $image = Storage::disk('s3')->url($imagePath);
        $name = last(explode('/', $image));
        $template = public_path('img/base_free_gift.png'); // todo: move to dashboard later

        $img = $this->resizeImageBySize($image, 1.93, 3.04);
        $img->rotate(-90);

        $giftTemplate = $this->resizeImageBySize($template,  8, 10);
        $giftTemplate->rotate(-90);

        $canvas = Image::canvas($this->countPixels($this->background_width), $this->countPixels($this->background_height), '#fff');
        $canvas->insert($img, 'top-right', 637, 947);
        $canvas->insert($giftTemplate, 'top_left');

        Storage::disk('local-printable')->put("$hashPath/free-gift-$name", $canvas->encode());
    }

    /**
     * Create csv file with order details
     *
     * @param Model $order
     * @param $hash_path
     */
    public function createExportOrderDetails(Model $order, $hash_path)
    {
        $packages = $order->items()->whereNotNull('package_id')->get()->groupBy('item_id');
        $addons = $order->items()->whereNull('package_id')->get();


        Excel::store(
            new OrderDetailsExport($order, $packages, $addons),
            $hash_path."/Order export ".today()->format('Y-m-d').".csv",
            'local-printable'
        );
    }

    /**
     * Resize image by parameters
     *
     * @param $image
     * @param $height
     * @param $width
     * @param $hash_path
     */
    public function createCompositions(string $image, $quantity, string $size, $height, $width, $x, $y, string $text, string $hash_path)
    {
        $name = last(explode('/', $image));
        $full_path = $hash_path.'/'.$size.'_'.$name;
        $pos = strpos($full_path, '.jpg');

        if ($size === '2 - 5x7') {
            $img = $this->resizeImageBySize($image, 5, 7, $width, $height, $x, $y);
            $canvas = Image::canvas($this->countPixels($this->background_width), $this->countPixels($this->background_height), '#fff');
            $canvas->insert($img, 'top-left');
            $canvas->insert($img, 'top-right');

            $this->setText($canvas, $text);

            Storage::disk('local-printable')->put($full_path, (string) $canvas->encode());
        }

        if ($size === '4 - 3.5x5') {
            $img3_5x5 = $this->resizeImageBySize($image, 3.5, 5, $width, $height, $x, $y);
            $img3_5x5->rotate(-90);
            $canvas = Image::canvas($this->countPixels($this->background_width), $this->countPixels($this->background_height), '#fff');
            $canvas->insert($img3_5x5, 'top-left');
            $canvas->insert($img3_5x5, 'top-right');
            $canvas->insert($img3_5x5, 'top-left', 0, $img3_5x5->height());
            $canvas->insert($img3_5x5, 'top-right', 0, $img3_5x5->height());

            $this->setText($canvas, $text);

            Storage::disk('local-printable')->put($full_path, (string) $canvas->encode());
        }

        if ($size === 'Set of 8 wallets') {
            $img = $this->resizeImageBySize($image, 2.5, 3.5, $width, $height, $x, $y);
            $canvas = Image::canvas($this->countPixels($this->background_width), $this->countPixels($this->background_height), '#fff');
            $canvas->insert($img, 'top-left');
            $canvas->insert($img, 'top-left', 0, $img->height());
            $canvas->insert($img, 'top-left', $img->width());
            $canvas->insert($img, 'top-left', $img->width(), $img->height());
            $canvas->insert($img, 'top-right');
            $canvas->insert($img, 'top-right', 0, $img->height());
            $canvas->insert($img, 'top-right', $img->width());
            $canvas->insert($img, 'top-right', $img->width(), $img->height());

            $this->setText($canvas, $text);

            Storage::disk('local-printable')->put($full_path, (string) $canvas->encode());
        }

        if ($size === '2 - 3.5x5 and 4 - Wallets') {
            $img3_5x5 = $this->resizeImageBySize($image, 3.5, 5, $width, $height, $x, $y);
            $img3_5x5->rotate(-90);
            $canvas = Image::canvas($this->countPixels($this->background_width), $this->countPixels($this->background_height), '#fff');
            $canvas->insert($img3_5x5, 'top-left');
            $canvas->insert($img3_5x5, 'top-left', 0, $img3_5x5->height());

            $wallet = $this->resizeImageBySize($image, 2.5, 3.5);
            $canvas->insert($wallet, 'top-right');
            $canvas->insert($wallet, 'top-right', 0, $wallet->height());
            $canvas->insert($wallet, 'top-right', $wallet->width());
            $canvas->insert($wallet, 'top-right', $wallet->width(), $wallet->height());

            $this->setText($canvas, $text);

            Storage::disk('local-printable')->put($full_path, (string) $canvas->encode());
        }

        if ($size === '1 - 5x7 and 4 - Wallets') {
            $img5x7 = $this->resizeImageBySize($image, 5, 7, $width, $height, $x, $y);
            $canvas = Image::canvas($this->countPixels($this->background_width), $this->countPixels($this->background_height), '#fff');
            $canvas->insert($img5x7, 'top-left');

            $wallet = $this->resizeImageBySize($image, 2.5, 3.5);

            $canvas->insert($wallet, 'top-right');
            $canvas->insert($wallet, 'top-right', 0, $wallet->height());
            $canvas->insert($wallet, 'top-right', $wallet->width());
            $canvas->insert($wallet, 'top-right', $wallet->width(), $wallet->height());

            $this->setText($canvas, $text);

            Storage::disk('local-printable')->put($full_path, (string) $canvas->encode());
        }

        if ($size === '1 - 5x7 and 2 - 3.5x5') {
            $img5x7 = $this->resizeImageBySize($image, 5, 7, $width, $height, $x, $y);
            $canvas = Image::canvas($this->countPixels($this->background_width), $this->countPixels($this->background_height), '#fff');
            $canvas->insert($img5x7, 'top-left');

            $img3_5x5 = $this->resizeImageBySize($image, 3.5, 5);
            $img3_5x5->rotate(-90);
            $canvas->insert($img3_5x5, 'top-right');
            $canvas->insert($img3_5x5, 'top-right', 0, $img3_5x5->height());

            $this->setText($canvas, $text);

            Storage::disk('local-printable')->put($full_path, (string) $canvas->encode());
        }

        if ($size === '1 - 8x10') {
            $img = $this->resizeImageBySize($image, 8, 10, $width, $height, $x, $y);
            $img->rotate(-90);

            Storage::disk('local-printable')->put($full_path, (string) $img->encode());
        }

        if ($quantity > 1 && Storage::disk('local-printable')->exists($full_path)) {
            for ($i = 1; $i < $quantity; $i++) {
                $new_path = substr_replace($full_path, "_($i)", $pos, 0);
                Storage::disk('local-printable')->copy($full_path, $new_path);
            }
        }
    }

    /**
     * Create intervention image and
     * resize image by width and height
     *
     * @param string $image
     * @param $width
     * @param $height
     * @return mixed
     */
    protected function resizeImageBySize(string $image, $width, $height, $width_pixels = null, $height_pixels = null, $x = 0, $y = 0)
    {
        $img = Image::make($image);

        if($width_pixels && $height_pixels){
            $img->crop($width_pixels, $height_pixels, $x, $y);
            $img->resize($this->countPixels($width), $this->countPixels($height));
        } else {
            $img->fit($this->countPixels($width), $this->countPixels($height), function ($constraint) {
                $constraint->upsize();
            });
        }

        return $img;
    }

    /**
     * Set text on image
     *
     * @param $canvas
     * @param $text
     */
    public function setText($canvas, string $text)
    {
        $canvas->text($text, $this->countPixels($this->text_x), $this->countPixels($this->text_y), function ($font) {
            $font->file(public_path("fonts/Poppins-Regular.ttf"));
            $font->size(70);
            $font->align('center');
            $font->valign('middle');
        });
    }

    /**
     * Generate zip with resizing images and save on s3
     *
     * @param Model  $order
     * @param string $hash_path
     */
    public function generateZip(Model $order, string $hash_path)
    {
        $zip_name = 'Order_export_'.$order->created_at->format('Y-m-d').'_'.$order->id.'.zip';


        $local_zip_path = 'zips/orders_printable/'.$hash_path.'.zip';


        $full_local_zip_path = Storage::disk('public')->path($local_zip_path);

        $zip_path = 'zips/orders_printable/'.$zip_name;

        $file_names = Storage::disk('local-printable')->files($hash_path);

        if (! empty($file_names)) {

            $zip = new ZipArchive();
            $zip->open($full_local_zip_path, ZipArchive::CREATE);

            foreach ($file_names as $full_path) {
                $file_content = Storage::disk('local-printable')->get($full_path);
                $file_name = last(explode('/', $full_path));

                $zip->addFromString($file_name, $file_content);
            }

            $zip->close();

            $zip_file = Storage::disk('public')->get($local_zip_path);
            Storage::disk('s3')->put($zip_path, $zip_file);
        }
    }

    /**
     * Save image compositions on s3
     *
     * @param $order
     * @param $hash
     */
    protected function saveImagesOnS3($order, $hash)
    {
        $directory_path = 'orders/'.$order->id;

        if( Storage::disk('s3')->exists($directory_path)){
            Storage::disk('s3')->deleteDirectory($directory_path);
        }

        $files = Storage::disk('local-printable')->files($hash);

        foreach ($files as $file) {
            $extensions = last(explode('.', $file));
            if($extensions == 'jpg'){
                $new_path = $directory_path . '/'.last(explode('/', $file));
                $file = Storage::disk('local-printable')->get($file);
                Storage::disk('s3')->put($new_path, $file);
            }
        }
    }

    /**
     * Delete local zip and resizing images
     *
     * @param string $hash_path
     */
    protected function deleteLocalFiles(string $hash_path)
    {
        Storage::disk('public')->delete('zips/orders_printable/'.$hash_path.'.zip');
        Storage::disk('local-printable')->deleteDirectory($hash_path);
    }

    /**
     * Generate zip with all orders printable files,
     * add csv with order details
     *
     * @param $orders
     * @return string
     * @throws Exception
     */
    public function generateForAllOrders($orders)
    {
        $csv_path = "All orders print details export ".today()->format('Y-m-d').".csv";
        Excel::store(
            new AllOrdersPrintDetailsExport($orders),
            $csv_path,
            'public'
        );

        $xls_path = "All orders invoice details export ".today()->format('Y-m-d').".xls";
        Excel::store(
            new AllOrdersInvoiceDetailsExport($orders),
            $xls_path,
            'public'
        );

        $zip_name = "Order export ".today()->format('Y-m-d').'.zip';
        $zip_path = 'zips/'. $zip_name;
        $local_zip_path =  Storage::disk('public')->path($zip_path);

        if( Storage::disk('s3')->exists('orders')){
            $file_names = Storage::disk('s3')->allFiles('orders');

            if(!empty($file_names)) {

                $zip = new ZipArchive();
                $zip->open($local_zip_path, ZipArchive::CREATE);

                foreach ($file_names as $full_path) {
                    $file_content = Storage::disk('s3')->get($full_path);
                    $file_name = last(explode('/', $full_path));
                    $zip->addFromString($file_name, $file_content);
                }

                if( Storage::disk('public')->exists($csv_path)){
                    $file_content = Storage::disk('public')->get($csv_path);
                    $zip->addFromString($csv_path, $file_content);

                    Storage::disk('public')->delete($csv_path);
                }

                if( Storage::disk('public')->exists($xls_path)){
                    $file_content = Storage::disk('public')->get($xls_path);
                    $zip->addFromString($xls_path, $file_content);

                    Storage::disk('public')->delete($xls_path);
                }

                $zip->close();

                return $zip_path;
            }
        }
    }
}
