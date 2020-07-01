<?php

namespace App\Ecommerce\PriceLists;

use App\Ecommerce\Cart\Cart;
use App\Ecommerce\Orders\Order;
use App\Ecommerce\Packages\Package;
use App\Ecommerce\Products\Product;
use App\Photos\Galleries\Gallery;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Ecommerce\PriceLists\PriceList
 *
 * @property int                                                                             $id
 * @property string                                                                          $name
 * @property Carbon|null                                                 $created_at
 * @property Carbon|null                                                 $updated_at
 * @property-read Collection|\App\Photos\Galleries\Gallery[]   $galleries
 * @property-read Collection|\App\Ecommerce\Packages\Package[] $packages
 * @property-read Collection|\App\Ecommerce\Products\Product[] $products
 * @method static Builder|\App\Ecommerce\PriceLists\PriceList newModelQuery()
 * @method static Builder|\App\Ecommerce\PriceLists\PriceList newQuery()
 * @method static Builder|\App\Ecommerce\PriceLists\PriceList query()
 * @method static Builder|\App\Ecommerce\PriceLists\PriceList whereCreatedAt($value)
 * @method static Builder|\App\Ecommerce\PriceLists\PriceList whereId($value)
 * @method static Builder|\App\Ecommerce\PriceLists\PriceList whereName($value)
 * @method static Builder|\App\Ecommerce\PriceLists\PriceList whereUpdatedAt($value)
 * @mixin Eloquent
 */
class PriceList extends Model
{
    protected $fillable = [
        'name',
    ];

    /**
     * Product
     *
     * @return BelongsToMany
     */
    public function products()
    {
        return $this->belongsToMany(Product::class)
            ->withPivot('price')->withTimestamps();
    }

    /**
     * Package
     *
     * @return BelongsToMany
     */
    public function packages()
    {
        return $this->belongsToMany(Package::class)
            ->withPivot('price')->withTimestamps();
    }

    /**
     * Galleries
     *
     * @return HasMany
     */
    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }

    /**
     * @return HasMany
     */
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * @return HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}
