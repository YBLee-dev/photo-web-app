<?php

namespace App\Ecommerce\Sizes;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Webmagic\Core\Entity\EntityRepo;

class SizeRepo extends EntityRepo
{
    protected $entity = Size::class;

    /**
     * Query sorting and paginate
     *
     * @param null $per_page
     * @param null $page
     * @param string $order
     * @param string $orderBy
     * @param null $query
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    public function getAllWithSorting($per_page = null, $page = null,  $order = 'asc', $orderBy = 'id', $query = null)
    {
        if(! $query){
            $query = $this->query();
        }

        if ($orderBy) {
            $query->orderBy($orderBy, $order);
        }

        if (is_null($per_page) && is_null($page)) {
            return $query->get();
        }

        return $query->paginate($per_page, ['*'], 'page', $page);
    }

    /**
     * Get array with key is id and value is name with sizes
     *
     * @return array
     * @throws \Exception
     */
    public function getForSelectWithSizesName(): array
    {
        $query = $this->query();
        $query->select('id', DB::raw('CONCAT( name," (", width,"x",height,")" ) as name'));

        return $query->pluck('name', 'id')->toArray();
    }

    /**
     * Get related combination to model by combination id
     *
     * @param int $combination_id
     * @param \Illuminate\Database\Eloquent\Model $size
     * @return mixed
     */
    public function getRelatedCombinationById(int $combination_id, Model $size)
    {
        return $size->combinations->find($combination_id);
    }
}
