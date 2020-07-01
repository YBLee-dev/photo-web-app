<?php

namespace App\Photos\Seasons;

use Illuminate\Support\Facades\DB;
use Webmagic\Core\Entity\EntityRepo;

class SeasonRepo extends EntityRepo
{
    protected $entity = Season::class;

    /**
     * Get array with key is id and value is name with school name and season name
     *
     * @return array
     * @throws \Exception
     */
    public function getForSelectWithSchoolName(): array
    {
        $query = $this->query();
        $query->join('schools', 'seasons.school_id', 'schools.id');

        $query->select('seasons.id', DB::raw('CONCAT( schools.name," - ", seasons.name ) as full_name'));

        return $query->pluck('full_name', 'id')->toArray();
    }

    /**
     * @param array $seasons
     * @param array $schools
     * @param null $per_page
     * @param null $page
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    public function getByFilter($seasons = [], $schools = [], $per_page = null, $page = null)
    {
        $query = $this->query();

        if (count($seasons)) {
            $query->whereIn('id', $seasons);
        }

        if (count($schools)) {
            $query->whereIn('school_id', $schools);
        }

        if (is_null($per_page) && is_null($page)) {
            return $query->get();
        }

        $query->orderBy('created_at', 'desc');

        $query->with('school');

        return $query->paginate($per_page, ['*'], 'page', $page);
    }
}
