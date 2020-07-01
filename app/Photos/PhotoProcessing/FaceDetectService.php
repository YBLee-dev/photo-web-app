<?php
namespace App\Photos\PhotoProcessing;

use App\Photos\Photos\Photo;
use Imagick;
use Softon\LaravelFaceDetect\Facades\FaceDetect;

class FaceDetectService
{
    /**
     * Get coords of face and then crop and resize for mini wallet size
     *
     * @param string     $originalFilePath
     *
     * @param int        $neededWidth
     * @param int        $neededHeight
     *
     * @param Photo|null $photoForSaveInfo
     *
     * @return bool|\Imagick
     * @throws \ImagickException
     */
    public function getPortrait(string $originalFilePath, int $neededWidth, int $neededHeight, Photo $photoForSaveInfo = null)
    {
        $coords = FaceDetect::extract($originalFilePath)->face;

        if ($coords){
            $x = $coords['x'];
            $y = $coords['y'];
            $recognizedWidth = $coords['w'];
            $x = $x - ($recognizedWidth*0.25);
            $y = $y - ($recognizedWidth*0.5);
            $recognizedWidth = $recognizedWidth * 1.5;
            $recognizedHeight = $recognizedWidth * 1.25;

//            $x = $x - ($recognizedWidth*0.5);
//            $y = $y - ($recognizedWidth*0.5);
//            $recognizedHeight = $recognizedWidth * $height/$width;

            $portrait = array(
                'x' => $x,
                'y' => $y,
                'w' => $recognizedWidth,
                'h' => $recognizedHeight,
            );
        } else {
            // Crop something to not broke the process
            // todo improve this part
            $portrait = array(
                'x' => 0,
                'y' => 0,
                'w' => $neededWidth,
                'h' => $neededHeight,
            );
        }

        $imPhoto = new Imagick($originalFilePath);
        $imPhoto->setResourceLimit(Imagick::RESOURCETYPE_MEMORY, 256);
        $imPhoto->setResourceLimit(Imagick::RESOURCETYPE_MAP, 256);
        $imPhoto->cropImage($portrait['w'],$portrait['h'],$portrait['x'],$portrait['y']);

        //Make thumbnail by biggest size
        $fitbyWidth = (($neededWidth/$imPhoto->getImageWidth())<($neededHeight/$imPhoto->getImageHeight())) ?true:false;
        if($fitbyWidth){
            $imPhoto->thumbnailImage(0, $neededHeight, false);
        }else{
            $imPhoto->thumbnailImage($neededWidth, 0, false);
        }

        //Crop in center image to needed size
        $imPhoto->cropImage($neededWidth, $neededHeight, ($imPhoto->getImageWidth() - $neededWidth) / 2, ($imPhoto->getImageHeight() - $neededHeight) / 2);

        //Save data about cropping if needed
        if($photoForSaveInfo){
            $photoForSaveInfo['crop_x'] = $portrait['x'];
            $photoForSaveInfo['crop_y'] = $portrait['y'];
            $photoForSaveInfo['crop_original_width'] = $portrait['w'];
            $photoForSaveInfo['crop_original_height'] = $portrait['h'];
            $photoForSaveInfo->save();
        }

        return $imPhoto->getImage();
    }
}
