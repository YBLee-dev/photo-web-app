<?php

namespace App\Ecommerce\Products;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Webmagic\Core\Entity\EntityRepo;

class ProductRepo extends EntityRepo
{
    protected $entity = Product::class;

    /**
     * Get products with paginate and filter by type
     *
     * @param null $type
     * @param null $per_page
     * @param null $page
     * @param string $order
     * @param string $orderBy
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    public function getByFilter($type = null, $name = null, $per_page = null, $page = null,  $order = 'desc', $orderBy = 'id', Builder $query = null)
    {
        if(!$query){
            $query = $this->query();
        }

        if(is_array($type) && count($type)){
            $query->whereIn('type', $type);
        } elseif($type) {
            $query->where('type', $type);
        }

        if($name){
            $query->where('name', 'LIKE', '%'.$name.'%');
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
     * Get related price list by price list id from pivot table
     * for current model
     *
     * @param $price_list_id
     * @param \Illuminate\Database\Eloquent\Model $product
     * @return mixed
     */
    public function getRelatedPriceListById($price_list_id, Model $product)
    {
        return $product->priceLists->find($price_list_id);
    }

    /**
     * Get related package from pivot table by package id
     * with sorting and pagination
     *
     * @param int $package_id
     * @param null $per_page
     * @param null $page
     * @param string $order
     * @param string $orderBy
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator|\Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     * @throws \Exception
     */
    public function getByPackageIdWithFilter(int $package_id, $per_page = null, $page = null, $order = 'desc', $orderBy = 'id')
    {
        $query = $this->query();
        $query->join('package_product', 'products.id', 'package_product.product_id')
            ->select('package_product.package_id', 'products.*')
            ->where('package_product.package_id', $package_id);

        return $this->getByFilter(null, null, $per_page, $page, $order, $orderBy, $query);
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
    public function getByPriceListIdWithFilter(int $price_list_id, $per_page = null, $page = null, $order = 'desc', $orderBy = 'id')
    {
        $query = $this->query();
        $query->join('price_list_product', 'products.id', 'price_list_product.product_id')
            ->select('price_list_product.price_list_id', 'price_list_product.price','products.*')
            ->where('price_list_product.price_list_id', $price_list_id);

        return $this->getByFilter(null, null, $per_page, $page, $order, $orderBy, $query);
    }

    /**
     * Create copy of model with products relations
     *
     * @param \Illuminate\Database\Eloquent\Model $package
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function createCopyWithSizesRelations(Model $product)
    {
        $new_product = $product->replicate();
        $new_product->name = 'COPIED ---'.$product->name;
        $new_product->save();

        $new_product->sizes()->attach($product->sizes()->allRelatedIds());

        return $new_product;
    }

    public function getAvailableSizesForProduct(int $product_id)
    {
        $product = $this->getByID($product_id);
        $query = $product->sizes()->select('size_id','name');

        return $query->pluck('name', 'size_id')->toArray();
    }
}
