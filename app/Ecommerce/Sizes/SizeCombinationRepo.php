<?php

namespace App\Ecommerce\Sizes;

use Webmagic\Core\Entity\EntityRepo;

class SizeCombinationRepo extends EntityRepo
{
    protected $entity = SizeCombination::class;

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
}
