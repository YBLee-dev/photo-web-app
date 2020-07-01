<?php


namespace App\Photos\SubGalleries;


use App\Utils;

class SubGalleryFactory
{
    public function createFromPhoto(int $gallery_id, string $subGalleryDirectoryName)
    {
        // Create gallery
        $subGallery = new SubGallery([
            'gallery_id' => $gallery_id,
            'name' => $subGalleryDirectoryName,
            'password' => Utils::generateSimpleNumberPassword()
        ]);
        $subGallery->save();

        return $subGallery;
    }


}
