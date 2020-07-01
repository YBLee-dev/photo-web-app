<?php


namespace App\Photos\TemplatedPhotosGeneration;

use Intervention\Image\Facades\Image;

class FreeGiftGenerator
{
    /**
     * @var string
     */
    private $template;

    /** @var int Quality */
    protected $dpi = 300;

    /** @var int  */
    protected $backgroundWidth = 10;

    /**
     * @var int
     */
    protected $backgroundHeight = 8;
    /**
     * @var string
     */
    private $personPhotoPath;

    /**
     * FreeGiftGenerator constructor.
     *
     * @param string $personPhotoPath
     */
    public function __construct(string $personPhotoPath)
    {
        // todo move to dashboard later
        $this->template = storage_path('app/public/group-settings/templates/default/base_free_gift.png');
        $this->personPhotoPath = $personPhotoPath;
    }

    /**
     * @return \Intervention\Image\Image
     */
    protected function generateImage()
    {
        $img = $this->resizeImageBySize($this->personPhotoPath, 1.93, 3.04);
        $img->rotate(-90);

        $giftTemplate = $this->resizeImageBySize($this->template,  8, 10);
        $giftTemplate->rotate(-90);

        $canvas = Image::canvas($this->countPixels($this->backgroundWidth), $this->countPixels($this->backgroundHeight), '#fff');
        $canvas->insert($img, 'top-right', 637, 947);
        $canvas->insert($giftTemplate, 'top_left');

        return $canvas;
    }

    /**
     * @param string $fullLocalPath
     *
     * @return string
     */
    public function generateAndSave(string $fullLocalPath)
    {
        $image = $this->generateImage();
        $image->save($fullLocalPath, 9);

        // Clear memory
        $image->destroy();

        return  $fullLocalPath;
    }

    /**
     * Create intervention image and
     * resize image by width and height
     *
     * @param string $image
     * @param        $width
     * @param        $height
     * @param null   $width_pixels
     * @param null   $height_pixels
     * @param int    $x
     * @param int    $y
     *
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
