<?php

namespace App\Photos\Galleries;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Webmagic\Core\Entity\EntityRepo;

class GalleryRepo extends EntityRepo
{
    protected $entity = Gallery::class;

    /**
     * Find by password code
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
     * Find by name and status
     *
     * @param string $name
     * @param string $statusEnum
     * @return Model|null
     * @throws Exception
     */
    public function getByNameAndStatus(string $name, string $statusEnum)
    {
        $query = $this->query();
        $query->where('name', $name);
        $query->where('status', $statusEnum);

        return $this->realGetOne($query);
    }

    /**
     * Find by season id and school id
     *
     * @param int $season_id
     * @param int $school_id
     * @return Model|null
     * @throws Exception
     */
    public function getBySchoolAndSeasonID(int $season_id, int $school_id)
    {
        $query = $this->query();
        $query->where('season_id', $season_id);
        $query->where('school_id', $school_id);

        return $this->realGetOne($query);
    }

    /**
     * Find by season id
     *
     * @param int $season_id
     * @return Model|null
     * @throws Exception
     */
    public function getBySeasonID(int $season_id)
    {
        $query = $this->query();
        $query->where('season_id', $season_id);

        return $this->realGetOne($query);
    }

    /**
     * @param array $ids
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     * @throws Exception
     */
    public function getByIds(array $ids)
    {
        $query = $this->query();
        $query->whereIn('id', $ids);

        return $this->realGetMany($query);
    }

    /**
     * @param array $schools
     * @param array $seasons
     * @param null $userId
     * @param null $per_page
     * @param null $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws Exception
     */
    public function getByFilter(
        $schools = [],
        $seasons = [],
        $per_page = null,
        $page = null,
        $userId = null
    ) {
        $query = $this->query();

        if (count($schools)) {
            $query->whereHas('school', function ($query) use ($schools) {
                $query->whereIn('name', $schools);
            });
        }

        if (count($seasons)) {
            $query->whereHas('season', function ($query) use ($seasons) {
                $query->whereIn('name', $seasons);
            });
        }

        if(!is_null($userId)){
            $query->where('user_id', $userId);
        }

        if (is_null($per_page) && is_null($page)) {
            return $query->get();
        }

        $query->orderBy('created_at', 'desc');

        return $query->paginate($per_page, ['*'], 'page', $page);
    }

    /**
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    public function getAllBeforeDeadline()
    {
        $query = $this->query();
        $query->whereDate('deadline', '>=', Carbon::now()->toDateString());

        return $this->realGetMany($query);
    }
}
