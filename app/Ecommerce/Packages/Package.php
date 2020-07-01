<?php

namespace App\Ecommerce\Packages;

use App\Ecommerce\Cart\CartItem;
use App\Ecommerce\Orders\OrderItem;
use App\Ecommerce\PriceLists\PriceList;
use App\Ecommerce\Products\Product;
use Illuminate\Database\Eloquent\Model;
use Webmagic\Core\Presenter\PresentableTrait;
use Webmagic\Core\Presenter\Presenter;

/**
 * App\Ecommerce\Packages\Package
 *
 * @property int                                                                                 $id
 * @property string|null                                                                         $image
 * @property string                                                                              $name
 * @property string|null                                                                         $reference_name
 * @property float                                                                               $price
* @property int                                                                                  $taxable
 * @property int                                                                                 $limit_poses
 * @property boolean                                                                             $available_after_deadline
 * @property string|null                                                                         $description
 * @property \Illuminate\Support\Carbon|null                                                     $created_at
 * @property \Illuminate\Support\Carbon|null                                                     $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Ecommerce\Cart\CartItem[]        $cart_items
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Ecommerce\Orders\OrderItem[]     $order_items
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Ecommerce\PriceLists\PriceList[] $priceLists
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Ecommerce\Products\Product[]     $products
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Packages\Package newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Packages\Package newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Packages\Package query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Packages\Package whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Packages\Package whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Packages\Package whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Packages\Package whereImage($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Packages\Package whereLimitPoses($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Packages\Package whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Packages\Package wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Packages\Package whereReferenceName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Packages\Package whereTaxable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Packages\Package whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Package extends Model
{
    use PresentableTrait;

    /** @var  Presenter class that using for present model */
    protected $presenter = PackagePresenter::class;

    protected $fillable = [
        'image',
        'name',
        'reference_name',
        'price',
        'taxable',
        'limit_poses',
        'description',
        'available_after_deadline'
    ];

    /**
     * Product
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(Product::class)->withTimestamps();
    }

    /**
     * Price List
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function priceLists()
    {
        return $this->belongsToMany(PriceList::class)->withPivot('price');
    }

    /**
     * Order items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function order_items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Cart items
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cart_items()
    {
        return $this->hasMany(CartItem::class);
    }
}
