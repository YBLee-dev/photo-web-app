<?php

namespace App\Photos\Galleries;

use App\Photos\Seasons\SeasonRepo;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

/**
 * Class GalleryService
 *
 * @package App\Photos\Galleries
 * @deprecated
 */
class GalleryService
{
    /**
     * Find all files on s3 by previous gallery owner
     * and move them to new path for new owner
     *
     * @param Model  $gallery
     * @param string $new_gallery_path
     */
    public function moveGalleryOnS3ByForNewOwner(Model $gallery, string $new_gallery_path)
    {
        $original_images = Storage::disk('s3')->allFiles('original/' . $gallery->path);
        $this->moveToNewPathOnS3($original_images, $gallery, $new_gallery_path);

        $preview_images = Storage::disk('s3')->allFiles('preview/' . $gallery->path);
        $this->moveToNewPathOnS3($preview_images, $gallery, $new_gallery_path);

        $proofs_images = Storage::disk('s3')->allFiles('proofs/' . $gallery->path);
        $this->moveToNewPathOnS3($proofs_images, $gallery, $new_gallery_path);
    }

    /**
     * Moving files on s3
     *
     * @param array   $images
     * @param Gallery $gallery
     * @param         $new_gallery_path
     */
    protected function moveToNewPathOnS3(array $images, Gallery $gallery, $new_gallery_path)
    {
        foreach ($images as $image) {
            $moveTo = str_replace($gallery->path, $new_gallery_path, $image);
            Storage::disk('s3')->move($image, $moveTo);
        }
    }

    /**
     * Delete galleries from original and preview path on s3
     *
     * @param Model $gallery
     */
    public function deleteGalleryOnS3(Model $gallery)
    {
        $this->deleteDirectoryFromS3('original/' . $gallery->path);
        $this->deleteDirectoryFromS3('preview/' . $gallery->path);
        $this->deleteDirectoryFromS3('proofs/' . $gallery->path);
        $this->deleteDirectoryFromS3('cropped_faces/' . $gallery->path);
        $this->deleteDirectoryFromS3('mini_wallet_collages/' . $gallery->path);
    }

    /**
     * Delete directory on s3 if exist
     *
     * @param string $path
     */
    protected function deleteDirectoryFromS3(string $path)
    {
        $exists = Storage::disk('s3')->exists($path);

        if ($exists){
            Storage::disk('s3')->deleteDirectory($path);
        }
    }

    /**
     * Prepare array for select with additional info
     *
     * @param SeasonRepo  $seasonRepo
     * @param GalleryRepo $galleryRepo
     *
     * @return array
     * @throws Exception
     */
    public function prepareArrayOfSeasonsForSelect(SeasonRepo $seasonRepo, GalleryRepo $galleryRepo)
    {
        $seasons = $seasonRepo->getForSelectWithSchoolName();
        foreach ($seasons as $season_id => &$season){
            if( $gallery = $galleryRepo->getBySeasonID($season_id)){
                $season .= ' ('. count($gallery->subgalleries) .' sub-galleries)';
            }
        }
        return $seasons;
    }

    public function getArrayOfSeasonsWithoutGalleries(SeasonRepo $seasonRepo, GalleryRepo $galleryRepo)
    {
        $seasons = $seasonRepo->getForSelectWithSchoolName();
        foreach ($seasons as $season_id => $season){
            if( $gallery = $galleryRepo->getBySeasonID($season_id)){
                if(count($gallery->subgalleries) > 0){
                    unset($seasons[$season_id]);
                }
            }
        }
        return $seasons;
    }

    /**
     * Prepare array with path to s3 for all mini wallet collages by gallery
     *
     * @param Model $gallery
     *
     * @return array
     */
    public function getMiniWalletCollages(Model $gallery)
    {
        $mini_wallet_collages = Storage::disk('s3')->files('mini_wallet_collages/'.$gallery->path);
        foreach ($mini_wallet_collages as $collage) {
            $prepared_data[] = ['path' => $collage];
        }

        return $prepared_data ?? [[]];
    }
}
