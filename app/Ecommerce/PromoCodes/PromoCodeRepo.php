<?php

namespace App\Ecommerce\PromoCodes;

use Illuminate\Support\Facades\DB;
use Webmagic\Core\Entity\EntityRepo;

class PromoCodeRepo extends EntityRepo
{
    protected $entity = PromoCode::class;

    /**
     * Get all with sorting and paginate
     *
     * @param null $status_type
     * @param null $per_page
     * @param null $page
     * @param string $order
     * @param string $orderBy
     * @param null $query
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    public function getAllWithSortingAndFilter($status_type = null, $per_page = null, $page = null,  $order = 'asc', $orderBy = 'id', $query = null)
    {
        if(! $query){
            $query = $this->query();
        }

        if($status_type) {
            $query->where('status', $status_type);
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
     * Return only expired codes
     * (where today date more then expires_at column)
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    public function getExpiredCodes()
    {
        $query = $this->query();
        $query->whereDate('expires_at', '<', today());
        return $this->realGetMany($query);
    }

    /**
     * Return only active codes
     * where expired more then today or haven't set
     *
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    public function getActiveCodes()
    {
        $query = $this->query();
        $query->whereDate('expires_at', '>', today());
        $query->orWhereNull('expires_at');
        return $this->realGetMany($query);
    }

    /**
     * @param string $redeem_code
     * @return \Illuminate\Database\Eloquent\Model|null
     * @throws \Exception
     */
    public function getByRedeemCode(string $redeem_code)
    {
        $query = $this->query();
        $query->where('redeem_code', $redeem_code);

        return $this->realGetOne($query);
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getForSelectWithFullName(): array
    {
        $query = $this->query();
        $query->select('id', DB::raw('CONCAT( name," (", discount_amount," ",type,")" ) as name'));

        return $query->pluck('name', 'id')->toArray();
    }
}
