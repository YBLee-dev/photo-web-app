<?php

namespace App\Photos\SubGalleries;

use App\Photos\Photos\PhotoRepo;
use App\Photos\Photos\PhotoStorageManager;
use App\Utils;
use Exception;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Webmagic\Core\Entity\EntityRepo;
use Webmagic\Core\Entity\Exceptions\EntityNotExtendsModelException;
use Webmagic\Core\Entity\Exceptions\ModelNotDefinedException;

class SubGalleryRepo extends EntityRepo
{
    protected $entity = SubGallery::class;

    /**
     * Get by password
     *
     * @param int $password
     * @return Model|null
     * @throws Exception
     */
    public function getByCode(int $password)
    {
        $query = $this->query();
        $query->where('password', $password);

        return $this->realGetOne($query);
    }

    /**
     * Get by part of sting from preview image
     *
     * @param string $path
     * @return Model|null
     * @throws Exception
     */
    public function getByPartOfImagePreviewPath(string $path)
    {
        $query = $this->query();
        $query->where('preview_image', 'like', "$path%");

        return $this->realGetOne($query);
    }

    /**
     * Get by filter by clients classrooms and gallery id
     *
     * @param array|null $classrooms
     * @param null       $gallery_id
     *
     * @param int|null   $perPage
     *
     * @return LengthAwarePaginator|Collection
     * @throws Exception
     */
    public function getByFilter(array $classrooms = null, $onlyStaff = false, $gallery_id = null, int $perPage = null, $sort = null, $sortBy = null)
    {
        $query = $this->query();

        $query->select('people.id', 'people.classroom', 'people.teacher', 'people.title', 'sub_galleries.*')
            ->leftJoin('people', 'sub_galleries.id', '=', 'people.sub_gallery_id');

        if(count($classrooms) > 0){
            $query->whereIn('classroom', $classrooms);
        }

        if($onlyStaff){
            $query->where('teacher', true);
        }

        if ($sortBy == 'title') {
            $query->orderBy('title', $sort);
        }

        $query->where('sub_galleries.gallery_id', $gallery_id);

        return $this->realGetMany($query, $perPage);
    }

    /**
     * Get by gallery id
     *
     * @param int $gallery_id
     * @param int|null $perPage
     *
     * @param string|null $sort
     * @param string|null $sortBy
     * @return LengthAwarePaginator|Collection
     * @throws \Exception
     */
    public function getByGalleryID(int $gallery_id, int $perPage = null, string $sort = null, string $sortBy = null)
    {
        $query = $this->query();
        $query->where('gallery_id', $gallery_id);

        if ($sortBy == 'title') {
            $query->join('people', 'sub_galleries.id', '=', 'people.sub_gallery_id')
                ->orderBy('people.title', $sort)
                ->select('sub_galleries.*');
        }

        return $this->realGetMany($query, $perPage);
    }

    /**
     * Get all related galleries by unique name
     *
     * @return array
     * @throws Exception
     */
    public function getClassroomsForSelect()
    {
        $query = $this->query();
        $query->select('people.*')
            ->leftJoin('people', 'sub_galleries.id', '=', 'people.sub_gallery_id')
            ->groupBy('classroom');
        $query->where('classroom', '<>', '');

        return $query->pluck('classroom', 'classroom')->toArray();
    }

    /**
     * @param array $data
     *
     * @return SubGallery|Model
     * @throws Exception
     */
    public function create(array $data)
    {
        $data['password'] = empty($data['password']) ?  Utils::generateSimpleNumberPassword() : $data['password'];

        return parent::create($data);
    }


    /**
     * @param int         $galleryId
     * @param string      $subGalleryName
     * @param string|null $password
     *
     * @return Model|SubGallery
     * @throws Exception
     */
    public function firstOrCreate(int $galleryId, string $subGalleryName, string $password = null)
    {
        $subGallery = $this->query()->firstOrNew([
            'gallery_id' => $galleryId,
            'name' => $subGalleryName
        ], [
            'password' => $password ?? Utils::generateSimpleNumberPassword()
        ]);

        $subGallery->save();

        return $subGallery;
    }

    /**
     * @param $id
     *
     * @return int|void
     * @throws EntityNotExtendsModelException
     * @throws ModelNotDefinedException
     */
    public function destroy($id)
    {
        $subGallery = $this->getByID($id);

        // Do nothing if sub gallery doesn't exist
        if(!$subGallery){
            return false;
        }

        // Delete photos first
        $photoRepo = new PhotoRepo();
        // Delete sub gallery photos
        call_user_func_array([$photoRepo, 'deletePhotos'], $subGallery->photos->all());

        // Delete person photos
        $person = $subGallery->person;
        if($person){
            call_user_func_array([$photoRepo, 'deletePhotos'], $person->photos->all());
        }

        $subGallery->customers()->detach();
        $subGallery->additionalClassrooms()->delete();

        //Delete sub gallery
        parent::destroy($id);
    }


}
