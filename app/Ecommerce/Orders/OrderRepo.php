<?php

namespace App\Ecommerce\Orders;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Webmagic\Core\Entity\EntityRepo;

class OrderRepo extends EntityRepo
{
    protected $entity = Order::class;

    public function getByFilter(
        $galleries = [],
        $subgalleries = [],
        $price_lists = [],
        $clients = [],
        $statuses = [],
        $payments = [],
        $from = null,
        $to = null,
        $per_page = null,
        $page = null
    ) {
        $query = $this->query();

        if (count($galleries)) {
            $query->whereIn('gallery_id', $galleries);
        }

        if (count($subgalleries)) {
            $query->whereIn('sub_gallery_id', $subgalleries);
        }

        if (count($price_lists)) {
            $query->whereIn('price_list_id', $price_lists);
        }

        if (count($clients)) {
            $query->whereIn(DB::raw('CONCAT( customer_first_name," ",customer_last_name )'), $clients);
        }

        if (count($statuses)) {
            $query->whereIn('status', $statuses);
        }

        if (count($payments)) {
            $query->whereIn('payment_status', $payments);
        }

        if ($from || $to) {
            $this->addFilterByDates($query, $from, $to);
        }

        if (is_null($per_page) && is_null($page)) {
            return $query->get();
        }

        $query->orderBy('created_at', 'desc');

        $query->with('gallery', 'subGallery', 'priceList');

        return $query->paginate($per_page, ['*'], 'page', $page);
    }

    /**
     * Get all related subgalleries by unique name
     *
     * @return array
     * @throws \Exception
     */
    public function getSubGalleriesForSelect()
    {
        $query = $this->query();
        $query->select('sub_galleries.*')
            ->leftJoin('sub_galleries', 'orders.sub_gallery_id', '=', 'sub_galleries.id')
            ->groupBy('name');

        return $query->pluck('name', 'id')->toArray();
    }

    /**
     * Get all related price lists by unique name
     *
     * @return array
     * @throws \Exception
     */
    public function getPriceListsForSelect()
    {
        $query = $this->query();
        $query->select('price_lists.*')
            ->leftJoin('price_lists', 'orders.price_list_id', '=', 'price_lists.id')
            ->groupBy('name');

        return $query->pluck('name', 'id')->toArray();
    }

    /**
     * Get all related clients by unique name
     *
     * @return array
     * @throws \Exception
     */
    public function getClientsForSelect()
    {
        $query = $this->query();
        $query->selectRaw('CONCAT( customer_first_name," ",customer_last_name ) as full_name');
        return $query->pluck('full_name', 'full_name')->toArray();
    }

    /**
     * Add filter by updated_at field
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param null $from
     * @param null $to
     * @return \Illuminate\Database\Eloquent\Builder
     */
    protected function addFilterByDates(Builder $query, $from = null, $to = null)
    {
        if (! is_null($from)) {
            $query->where('created_at', '>=', Carbon::parse($from));
        }

        if (! is_null($to)) {
            $query->where('created_at', '<=', Carbon::parse($to));
        }

        return $query;
    }

    /**
     * Get entity by Hash
     *
     * @param $id
     *
     * @return \Illuminate\Database\Eloquent\Model
     * @throws Exception
     */
    public function getByHash(string $hash)
    {
        $query = $this->query();
        $query->where('hash', $hash);

        return $this->realGetOne($query);
    }

    public function getAllPaid()
    {
        $query = $this->query();
        $query->where('payment_status', OrderPaymentStatusEnum::PAID);

        return $this->realGetMany($query);
    }

    public function getAllUnpaid()
    {
        $query = $this->query();
        $query->where('payment_status', OrderPaymentStatusEnum::NOT_PAID);

        return $this->realGetMany($query);
    }
}
