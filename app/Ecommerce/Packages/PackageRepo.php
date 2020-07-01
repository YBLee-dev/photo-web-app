<?php

namespace App\Ecommerce\Packages;

use Illuminate\Database\Eloquent\Model;
use Webmagic\Core\Entity\EntityRepo;

class PackageRepo extends EntityRepo
{
    protected $entity = Package::class;

    /**
     * Get all with sorting and paginate
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
     * Search entities by part or full name
     *
     * @param string $name
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    public function getByName(string $name)
    {
        $query = $this->query();

        if($name){
            $query->where('name', 'LIKE', '%'.$name.'%');
        }

        return $this->realGetMany($query);
    }

    /**
     * Get related price list by price list id from pivot table
     * for current model
     *
     * @param $price_list_id
     * @param \Illuminate\Database\Eloquent\Model $package
     * @return mixed
     */
    public function getRelatedPriceListById($price_list_id, Model $package)
    {
        return $package->priceLists->find($price_list_id);
    }

    /**
     * Get related price list from pivot table by price list id
     * with sorting and pagination
     *
     * @param int $price_list_id
     * @param null $per_page
     * @param null $page
     * @param string $order
     * @param string $orderBy
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    public function getByPriceListIdWithFilter(int $price_list_id, $per_page = null, $page = null, $order = 'asc', $orderBy = 'id')
    {
        $query = $this->query();
        $query->join('package_price_list', 'packages.id', 'package_price_list.package_id')
            ->select('package_price_list.price_list_id', 'packages.*', 'package_price_list.price')
            ->where('package_price_list.price_list_id', $price_list_id);

        return $this->getAllWithSorting($per_page, $page, $order, $orderBy, $query);
    }

    /**
     * Create copy of model with products relations
     *
     * @param \Illuminate\Database\Eloquent\Model $package
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createCopyWithProductsRelations(Model $package)
    {
        $new_package = $package->replicate();
        $new_package->name = 'COPIED ---'.$package->name;
        $new_package->save();

        $new_package->products()->attach($package->products()->allRelatedIds());

        return $new_package;
    }
}
