<?php


namespace App\Photos\PrintablePhotosGeneration;


use App\Ecommerce\Orders\Order;
use App\Ecommerce\Orders\OrderItem;
use App\Ecommerce\Orders\OrderService;
use App\Photos\People\Person;
use App\Photos\Photos\PhotosFactory;
use App\Photos\Photos\PhotoStorageManager;
use App\Photos\Photos\PhotoTypeEnum;
use Intervention\Image\Facades\Image;
use Intervention\Image\Image as InterventionImage;
use Laracasts\Presenter\Exceptions\PresenterException;

class PrintablePhotosGenerator
{
    protected $dpi = 300;

    protected $background_width = 10;

    protected $background_height = 8;

    protected $text_y = 7.5;

    protected $text_x = 5;

    /**
     * @param Order $order
     *
     * @throws PresenterException
     */
    public function generatePrintablePhotosForOrder(Order $order)
    {
        $person = $order->subGallery->person;
        $shoolId = $order->gallery->school_id;

        $photoFactory = new PhotosFactory();
        $printableItems = (new OrderService())->getPrintableItems($order);

        // Text
        $text = $this->prepareTextForPhoto($order, $person);

        // Prepare directory
        (new PhotoStorageManager())->printablePrepareLocalDir();

        // Generate images
        $photoIds = [];
        foreach ($printableItems as $item) {
            /** @var OrderItem $orderItem */
            $orderItem = $item['order_item'];

            $selectedPhoto = $orderItem->photo()->present()->originalUrl();

            // Generate image
            $image = $this->prepareImage(
                $selectedPhoto,
                $item['size'],
                $item['height'],
                $item['width'],
                $item['x'],
                $item['y'],
                $text
                );

            // Prepare photos count depends on selected count
            for ($i = $item['quantity']; $i > 0; $i--) {
                // Create new photo for all quantity
                $printablePhoto = $photoFactory->createEmptyPrintablePhoto(
                    PhotoTypeEnum::PRINTABLE,
                    $shoolId,
                    $order->id,
                    $person->present()->prepareFullName(),
                    $person->classroom,
                    $item['size'],
                    (int)$orderItem->id.$i, //Fast fix to not override photos with the same names
                    'jpg'
                );

                $path = $printablePhoto->present()->originalLocalPath();

                $image->save($path);
                $photoFactory->updatePhotoFromFile($printablePhoto, $path, false);

                $photoIds[] = $printablePhoto->id;
            }

            // Sync new photos with order to update the all relations
            $order->photos()->sync($photoIds);

            // Clean up resources
            $image->destroy();
        }
    }

    /**
     * @param Order  $order
     * @param Person $person
     *
     * @return string
     * @throws PresenterException
     */
    protected function prepareTextForPhoto(Order $order, Person $person)
    {
        return "#{$order->id}/{$person->name}({$order->gallery->present()->name()})";
    }

    /**
     * @param string $photo
     * @param string $sizeCombination
     * @param        $height
     * @param        $width
     * @param        $x
     * @param        $y
     * @param string $text
     *
     * @return InterventionImage
     */
    protected function prepareImage(string $photo, string $sizeCombination, int $height, int $width, int $x, int $y, string $text)
    {
        $image = Image::make($photo);

        if (PrintablePhotosSizesEnum::TWO_5x7()->is($sizeCombination)){
            return $this->two5x7($image, $width, $height, $x, $y, $text);
        }

        if (PrintablePhotosSizesEnum::FOUR_3_5x5()->is($sizeCombination)){
            return $this->four3_5x5($image, $width, $height, $x, $y, $text);
        }

        if (PrintablePhotosSizesEnum::SET_OF_8_WALLETS()->is($sizeCombination)){
            return $this->setOf8Wallets($image, $width, $height, $x, $y, $text);
        }

        if (PrintablePhotosSizesEnum::TWO_3_5x5_AND_FOUR_WALLETS()->is($sizeCombination)){
            $second_image = Image::make($photo);
            return $this->two3_5x5AndFourWallets($image, $second_image, $width, $height, $x, $y, $text);
        }

        if (PrintablePhotosSizesEnum::ONE_5x7_AND_4_WALLETS()->is($sizeCombination)){
            return $this->one5x7And4Wallets($image, $width, $height, $x, $y, $text);
        }

        if (PrintablePhotosSizesEnum::ONE_5x7_AND_TWO_3_5x5()->is($sizeCombination)){
            return $this->one5x7AndTwo3_5x5($image, $width, $height, $x, $y, $text);
        }

        if (PrintablePhotosSizesEnum::ONE_8x10()->is($sizeCombination)){
            return $this->one8x10($image, $width, $height, $x, $y, $text);
        }
    }

    /**
     * @param InterventionImage $image
     * @param int               $width
     * @param int               $height
     * @param int               $x
     * @param int               $y
     * @param string            $text
     *
     * @return InterventionImage
     */
    protected function one8x10(InterventionImage $image, int $width, int $height, int $x, int $y, string $text)
    {
        $img = $this->resizeImageBySize($image, 8, 10, $width, $height, $x, $y);
        $img->rotate(-90);

        return $img;
    }

    /**
     * @param InterventionImage $image
     * @param int               $width
     * @param int               $height
     * @param int               $x
     * @param int               $y
     * @param string            $text
     *
     * @return InterventionImage
     */
    protected function one5x7AndTwo3_5x5(InterventionImage $image, int $width, int $height, int $x, int $y, string $text)
    {
        $img5x7 = $this->resizeImageBySize($image, 5, 7, $width, $height, $x, $y);
        $canvas = Image::canvas($this->countPixels($this->background_width), $this->countPixels($this->background_height), '#fff');
        $canvas->insert($img5x7, 'top-left');

        $img3_5x5 = $this->resizeImageBySize($image, 3.5, 5);
        $img3_5x5->rotate(-90);
        $canvas->insert($img3_5x5, 'top-right');
        $canvas->insert($img3_5x5, 'top-right', 0, $img3_5x5->height());

        $this->setText($canvas, $text);

        return $canvas;
    }

    /**
     * @param InterventionImage $image
     * @param int               $width
     * @param int               $height
     * @param int               $x
     * @param int               $y
     * @param string            $text
     *
     * @return InterventionImage
     */
    protected function one5x7And4Wallets(InterventionImage $image, int $width, int $height, int $x, int $y, string $text)
    {
        $img5x7 = $this->resizeImageBySize($image, 5, 7, $width, $height, $x, $y);
        $canvas = Image::canvas($this->countPixels($this->background_width), $this->countPixels($this->background_height), '#fff');
        $canvas->insert($img5x7, 'top-left');

        $wallet = $this->resizeImageBySize($image, 2.5, 3.5);

        $canvas->insert($wallet, 'top-right');
        $canvas->insert($wallet, 'top-right', 0, $wallet->height());
        $canvas->insert($wallet, 'top-right', $wallet->width());
        $canvas->insert($wallet, 'top-right', $wallet->width(), $wallet->height());

        $this->setText($canvas, $text);

        return $canvas;
    }

    /**
     * @param InterventionImage $image
     * @param int               $width
     * @param int               $height
     * @param int               $x
     * @param int               $y
     * @param string            $text
     *
     * @return InterventionImage
     */
    protected function two3_5x5AndFourWallets(InterventionImage $image, InterventionImage $second_image, int $width, int $height, int $x, int $y, string $text)
    {
        $img3_5x5 = $this->resizeImageBySize($image, 3.5, 5, $width, $height, $x, $y);
        $img3_5x5->rotate(-90);
        $canvas = Image::canvas($this->countPixels($this->background_width), $this->countPixels($this->background_height), '#fff');
        $canvas->insert($img3_5x5, 'top-left');
        $canvas->insert($img3_5x5, 'top-left', 0, $img3_5x5->height());

        $wallet = $this->resizeImageBySize($second_image, 2.5, 3.5);
        $canvas->insert($wallet, 'top-right');
        $canvas->insert($wallet, 'top-right', 0, $wallet->height());
        $canvas->insert($wallet, 'top-right', $wallet->width());
        $canvas->insert($wallet, 'top-right', $wallet->width(), $wallet->height());

        $this->setText($canvas, $text);

        return $canvas;
    }

    /**
     * @param InterventionImage $image
     * @param int               $width
     * @param int               $height
     * @param int               $x
     * @param int               $y
     * @param string            $text
     *
     * @return InterventionImage
     */
    protected function setOf8Wallets(InterventionImage $image, int $width, int $height, int $x, int $y, string $text)
    {
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

        return $canvas;
    }

    /**
     * @param InterventionImage $image
     * @param int               $width
     * @param int               $height
     * @param int               $x
     * @param int               $y
     * @param string            $text
     *
     * @return InterventionImage
     */
    protected function four3_5x5(InterventionImage $image, int $width, int $height, int $x, int $y, string $text)
    {
        $img3_5x5 = $this->resizeImageBySize($image, 3.5, 5, $width, $height, $x, $y);

        $img3_5x5->rotate(-90);
        $canvas = Image::canvas($this->countPixels($this->background_width), $this->countPixels($this->background_height), '#fff');
        $canvas->insert($img3_5x5, 'top-left');
        $canvas->insert($img3_5x5, 'top-right');
        $canvas->insert($img3_5x5, 'top-left', 0, $img3_5x5->height());
        $canvas->insert($img3_5x5, 'top-right', 0, $img3_5x5->height());

        $this->setText($canvas, $text);

        return $canvas;
    }

    /**
     * @param InterventionImage $image
     * @param int               $width
     * @param int               $height
     * @param int               $x
     * @param int               $y
     * @param string            $text
     *
     * @return InterventionImage
     */
    protected function two5x7(InterventionImage $image, int $width, int $height, int $x, int $y, string $text)
    {
        $image = $this->resizeImageBySize($image, 5, 7, $width, $height, $x, $y);

        $canvas = Image::canvas($this->countPixels($this->background_width), $this->countPixels($this->background_height), '#fff');
        $canvas->insert($image, 'top-left');
        $canvas->insert($image, 'top-right');

        $this->setText($canvas, $text);

        return $canvas;
    }

    /**
     * Create intervention image and
     * resize image by width and height
     *
     * @param InterventionImage $image
     * @param int               $width
     * @param int               $height
     * @param int|null          $widthPixels
     * @param int|null          $heightPixels
     * @param int               $x
     * @param int               $y
     *
     * @return InterventionImage
     */
    protected function resizeImageBySize(
        InterventionImage $image,
        float $width,
        float $height,
        int $widthPixels = null,
        int $heightPixels = null,
        int $x = 0,
        int $y = 0
    ) {
        if($widthPixels && $heightPixels){
            $image->crop($widthPixels, $heightPixels, $x, $y);
            $image->resize($this->countPixels($width), $this->countPixels($height));
        } else {
            $image->fit($this->countPixels($width), $this->countPixels($height), function ($constraint) {
                $constraint->upsize();
            });
        }

        return $image;
    }

    /**
     * Set text on image
     *
     * @param $canvas
     * @param $text
     */
    protected function setText($canvas, string $text)
    {
        $canvas->text($text, $this->countPixels($this->text_x), $this->countPixels($this->text_y), function ($font) {
            $font->file(public_path("fonts/Poppins-Regular.ttf"));
            $font->size(70);
            $font->align('center');
            $font->valign('middle');
        });
    }

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
}
