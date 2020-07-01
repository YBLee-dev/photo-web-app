<?php


namespace App\Photos\Photos;


use App\Photos\Galleries\Gallery;
use App\Photos\People\Person;
use App\Photos\SubGalleries\SubGallery;
use Exception;
use Illuminate\Database\Eloquent\Collection;
use Laracasts\Presenter\Exceptions\PresenterException;
use Webmagic\Core\Entity\EntityRepo;
use Webmagic\Core\Entity\Exceptions\EntityNotExtendsModelException;
use Webmagic\Core\Entity\Exceptions\ModelNotDefinedException;

class PhotoRepo extends EntityRepo
{
    protected $entity = Photo::class;

    /**
     * @param Gallery $gallery
     */
    public function deleteAllGalleryPhotos(Gallery $gallery)
    {
        // Delete photo files
        (new PhotoStorageManager())->deleteGalleryPhotos($gallery);

        // Delete photo record
        $photos = $gallery->allPhotos();
        $photosIds = array_only($photos, 'id');

        Photo::whereIn('id', $photosIds)->delete();
    }

    /**
     * Delete photos
     *
     * @param Photo ...$photos
     *
     * @throws Exception
     */
    public function deletePhotos(Photo ... $photos)
    {
        // Delete photo files
        call_user_func_array([(new PhotoStorageManager()), 'deletePhotos'], $photos);


        foreach ($photos as $photo){
            $photo->delete();
        }
    }

    /**
     * @param int $galleryId
     * @param int $perPage
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|Collection
     */
    public function allProofPhotosForGallery(int $galleryId, int $perPage = null)
    {
        $query = Photo::where('type', PhotoTypeEnum::PROOF)
            ->leftJoin('photo_able', 'photo_able.photo_id', '=', 'photos.id')
            ->leftJoin('people', 'photo_able.photo_able_id', '=', 'people.id')
            ->leftJoin('sub_galleries', 'people.sub_gallery_id', '=', 'sub_galleries.id')
            ->where('photo_able.photo_able_type', Person::class)
            ->where('sub_galleries.gallery_id', $galleryId)
            ->orderBy('photos.updated_at', 'desc')
            ->select('photos.*');

        return $this->realGetMany($query, $perPage);
    }

    /**
     * @return Photo [] | Collection
     * @throws Exception
     */
    public function allInitialProcessingPhotos()
    {
        $query = $this->query()->where('status', PhotoStatusEnum::INITIAL_PROCESSING);

        return $this->realGetMany($query);
    }

    /**
     * @param int $photoId
     *
     * @throws PresenterException
     * @throws EntityNotExtendsModelException
     * @throws ModelNotDefinedException
     */
    public function deletePhoto(int $photoId)
    {
        $photo = $this->getByID($photoId);

        $this->destroy($photoId);

        (new PhotoStorageManager())->deletePhoto($photo);
    }
}
