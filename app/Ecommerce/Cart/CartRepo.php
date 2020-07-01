<?php

namespace App\Ecommerce\Cart;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Webmagic\Core\Entity\EntityRepo;

class CartRepo extends EntityRepo
{
    protected $entity = Cart::class;

    /**
     * Filter with pagination and sorting
     *
     * @param bool $abandoned
     * @param array $galleries
     * @param array $subgalleries
     * @param array $price_lists
     * @param null $from
     * @param null $to
     * @param null $per_page
     * @param null $page
     * @param string $order
     * @param string $orderBy
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    public function getByFilter(
        $abandoned = false,
        $galleries = [],
        $subgalleries = [],
        $price_lists = [],
        $from = null,
        $to = null,
        $per_page = null,
        $page = null,
        $order = 'desc',
        $orderBy = 'updated_at'
    ) {
        $query = $this->query();

        if ($abandoned) {
            $query->where('abandoned', 1);
        }

        if (count($galleries)) {
            $query->whereIn('gallery_id', $galleries);
        } else {
            $query->whereNotNull('gallery_id');
        }

        if (count($subgalleries)) {
            $query->whereIn('sub_gallery_id', $subgalleries);
        } else {
            $query->whereNotNull('sub_gallery_id');
        }

        if (count($price_lists)) {
            $query->whereIn('price_list_id', $price_lists);
        } else {
            $query->whereNotNull('price_list_id');
        }

        if ($from || $to) {
            $this->addFilterByDates($query, $from, $to);
        }

        if ($orderBy) {
            if ($orderBy == 'gallery_name') {
                $query->select('galleries.name as gallery_name', 'carts.*')
                    ->leftJoin('galleries', 'carts.gallery_id', '=', 'galleries.id');
            }

            $query->orderBy($orderBy, $order);
        }

        if (is_null($per_page) && is_null($page)) {
            return $query->get();
        }

        return $query->paginate($per_page, ['*'], 'page', $page);
    }

    /**
     * Get all related galleries by unique name
     *
     * @return array
     * @throws \Exception
     */
    public function getGalleriesForSelect()
    {
        $query = $this->query();
        $query->whereNotNull('gallery_id')
            ->select('galleries.*')->leftJoin('galleries', 'carts.gallery_id', '=', 'galleries.id')->groupBy('carts.gallery_id');

        return $query->pluck('name', 'id')->toArray();
    }

    /**
     * Get all related subgalleries by unique name
     *
     * @return array
     * @throws \Exception
     */
    public function getSubgalleriesForSelect()
    {
        $query = $this->query();
        $query->whereNotNull('sub_gallery_id')
            ->select('sub_galleries.*')->leftJoin('sub_galleries', 'carts.sub_gallery_id', '=', 'sub_galleries.id')->groupBy('name');

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
        $query->whereNotNull('price_list_id')
            ->select('price_lists.*')->leftJoin('price_lists', 'carts.price_list_id', '=', 'price_lists.id')->groupBy('name');

        return $query->pluck('name', 'id')->toArray();
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
            $query->where('updated_at', '>=', Carbon::parse($from));
        }

        if (! is_null($to)) {
            $query->where('updated_at', '<=', Carbon::parse($to));
        }

        return $query;
    }

    /**
     * Get Eloquent or Create Model by session cart key
     *
     * @param string $session_key
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model
     * @throws \Exception
     */
    public function getOrCreateBySessionKey(string $session_key)
    {
        $query = $this->query();

        $query->where('session_key', $session_key);
        $cart = $this->realGetOne($query);

        return $cart ?: $query->create(['session_key' => $session_key]);
    }

    /**
     * Destroy cart by session key
     *
     * @param $session_key
     * @return mixed
     * @throws \Exception
     */
    public function destroyBySessionKey($session_key)
    {
        $query = $this->query();
        $query->where('session_key', $session_key);

        return $query->delete();
    }

}
