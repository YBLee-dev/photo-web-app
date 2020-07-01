<?php

namespace App\Photos\GroupPhotosGeneration;

use Exception;
use Illuminate\Support\Facades\Storage;
use Imagick;
use ImagickDraw;

// todo Check and add comments
class MiniWalletSizedCollageGenerator
{

    /**
     * @param array $items
     *
     * @return mixed
     * @throws \ImagickException
     */
    public function create(array $items)
    {
        $g = [
            'w' => 2400,
            'h' => 3000,
            'wn' => 6,
            'margin-w' => 20,
        ];

        //$portraitRatio = 2.5/1.75;
        //$portraitRatio = 127 / 109;
        //$portraitRatio = 1;
        $portraitRatio = 1.3;
        $g['p']['w'] = floor(($g['w'] - $g['margin-w'] * 2) / 6);
        $g['p']['h'] = floor($g['p']['w'] * $portraitRatio);
        $g['hn'] = floor($g['h'] / $g['p']['h']);
        $g['margin-h'] = floor(($g['h'] - $g['hn'] * $g['p']['h']) / 2);


        $canvas = imagecreatetruecolor($g['w'], $g['h']);
        imagesavealpha($canvas, true);
        $trans_color = imagecolorallocatealpha($canvas, 255, 255, 255, 127);
        imagefill($canvas, 0, 0, $trans_color);


        //$classnames = 1;
        //$text = "This is a sunset!";
        $font_path = public_path("fonts/Poppins-Regular.ttf");
        $white = imagecolorallocate($canvas, 0, 0, 0);
        $text_size = $g['margin-h']/5;
        $text_height = $g['margin-h']/7;
        $offsetAfterText = 20;

        $countClasses = 1;
        $countPhotos = 0;

        foreach ($items as $classname => $photos) {
            $text = $classname;

            $bbox = imagettfbbox($text_height, 0, $font_path, $text);
            $center_for_text = (imagesx($canvas) / 2) - (($bbox[2] - $bbox[0]) / 2);

            $textsOffset = ($text_size + 10) * $countClasses;

            imagettftext($canvas, $text_height, 0, $center_for_text, $textWithImagesTopOffset ?? $textsOffset, $white, $font_path, $text);


            foreach ($photos as $key => $photo) {
                if(! $pic = $this->createImage($photo)) {
                    continue;
                }
                $pic = $this->stretchImage($pic,$g['p']['w'],$g['p']['h']);
                $pic = $this->coverImage($pic,$g['p']['w'],$g['p']['h']);

                $row = floor($countPhotos/$g['wn']);
                $col = $countPhotos - $row*$g['wn'];

                if($countPhotos == 0){
                    $leftOffset = $g['margin-w'];
                    $topOffset = $textsOffset + $offsetAfterText;
                } else {
                    $leftOffset = $col*$g['p']['w'] + $g['margin-w'];
                    $topOffset = $row*$g['p']['h'] + $textsOffset + $offsetAfterText;
                }

                imagecopymerge($canvas,$pic,$leftOffset,$topOffset,0,0,imagesx($pic),imagesy($pic),100);
                $countPhotos++;
            }
            while($countPhotos%$g['wn'] !== 0){
                $countPhotos++;
            }
            $textWithImagesTopOffset = $row*$g['p']['h'] + $textsOffset + imagesy($pic) + $text_size + 10;
            $countClasses++;
        }

        $return['success'] = true;
        $return['image'] = $canvas;


        return $return;
    }

    /**
     * @param $image_url
     *
     * @return bool|false|Imagick|resource
     * @throws \ImagickException
     */
    public function createImage($image_url)
    {
        if ($image_url == null OR $image_url == '') {
            return false;
        }

        if (! file($image_url)) {
            logger('Cannot add to mini wallet sized collage photo (is not a file): ' .$image_url);
            return false;
        }

        $file = $image_url;

        try {
            list($width, $height, $type) = getimagesize($file);
        }
        catch(Exception $e){
            logger('Cannot add to mini wallet sized collage photo (cannot read image sizes): ' .$image_url);
            return false;
        }

        if ($type == false) {
            return false;
        }
        switch ($type){
            case 1:
                $img = imagecreatefromgif($file);
                return $img;
            case 2:
                return imagecreatefromjpeg($file);
            case 3:
                $tempFileNameBase = uniqid();
                $tempFileName = "{$tempFileNameBase}.png";
                $count = 0;
                while (file_exists("tempFiles/{$tempFileName}")){
                    $count++;
                    $tempFileName = "{$tempFileNameBase}_{$count}.png";
                }
                $handle = fopen($file,'rb');
                $img = new Imagick();
                $img->readImageFile($handle);
                $img->transformImageColorspace(Imagick::COLORSPACE_SRGB);

                $white = new Imagick();
                $white->newImage($width,$height,"transparent");
                $white->compositeimage($img,Imagick::COMPOSITE_OVER,0,0);
                $white->setImageFormat('png');
                $white->setImageCompressionQuality(1000);
                $white->writeImage($tempFileName);
                $img = imagecreatefrompng($tempFileName);
                unlink($tempFileName);
                imagealphablending($img, false);
                imagesavealpha($img, true);
                return $img;
            default:
                return false;
        }
    }

    /**
     * @param string $text
     *
     * @return ImagickDraw
     */
    protected function createClassName(string $text)
    {
        $draw = new ImagickDraw();
        $draw->setStrokeWidth(1.2);
        $draw->setFillColor('#000000');
        $draw->setTextAlignment(Imagick::ALIGN_CENTER);

        $draw->setFontSize(24);

        $draw->annotation(0, 0, $text);

        return $draw;
    }

    /**
     * @param $img
     * @param $maxWidth
     * @param $maxHeight
     *
     * @return false|resource
     */
    public function stretchImage($img, $maxWidth, $maxHeight)
    {
        $imgWidth = imagesx($img);
        $imgHeight = imagesy($img);
        $destImage = imagecreatetruecolor($maxWidth, $maxHeight);
        imagefill($destImage, 0, 0, imagecolorallocatealpha($destImage, 255, 255, 255, 127));
        imagealphablending($destImage, false);
        imagesavealpha($destImage, true);
        imagecopyresampled($destImage, $img, 0, 0, 0, 0, $maxWidth, $maxHeight, $imgWidth, $imgHeight);

        return $destImage;
    }

    /**
     * @param $img
     * @param $destWidth
     * @param $destHeight
     *
     * @return false|resource
     */
    public function coverImage($img, $destWidth, $destHeight)
    {
        imagealphablending($img, false);
        imagesavealpha($img, true);
        $destImage = imagecreatetruecolor($destWidth, $destHeight);
        imagealphablending($destImage, false);
        imagesavealpha($destImage, true);
        imagefill($destImage, 0, 0, imagecolorallocatealpha($destImage, 255, 255, 255, 127));
        //Determine Coeficient used to 'resize' source image
        $imgWidth = imagesx($img);
        $imgHeight = imagesy($img);
        $widthRatio = $destWidth / $imgWidth;
        $heightRatio = $destHeight / $imgHeight;
        $coef = $widthRatio > $heightRatio ? $widthRatio : $heightRatio;
        $imgTargetWidth = $imgWidth * $coef;
        $imgTargetHeight = $imgHeight * $coef;
        $xOffset = ($imgTargetWidth - $destWidth) / 2 / $coef;
        $yOffset = ($imgTargetHeight - $destHeight) / 2 / $coef;
        imagecopyresampled($destImage, $img, 0, 0, $xOffset, $yOffset, $destWidth, $destHeight, $destWidth / $coef, $destHeight / $coef);

        return $destImage;
    }
}
