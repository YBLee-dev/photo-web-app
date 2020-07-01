<?php

namespace App\Ecommerce\Products;


use App\Ecommerce\Cart\CartItem;
use App\Ecommerce\Orders\OrderItem;
use App\Ecommerce\Packages\Package;
use App\Ecommerce\PriceLists\PriceList;
use App\Ecommerce\Sizes\SizeCombination;
use Illuminate\Database\Eloquent\Model;
use Webmagic\Core\Presenter\PresentableTrait;

/**
 * App\Ecommerce\Products\Product
 *
 * @property int                                                                                  $id
 * @property string                                                                               $type
 * @property string                                                                               $name
 * @property string|null                                                                          $reference
 * @property float                                                                                $default_price
 * @property int                                                                                 $taxable
 * @property string|null                                                                         $description
 * @property \Illuminate\Support\Carbon|null                                                     $created_at
 * @property \Illuminate\Support\Carbon|null                                                      $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Ecommerce\Cart\CartItem[]         $cart_items
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Ecommerce\Orders\OrderItem[]      $order_items
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Ecommerce\Packages\Package[]      $packages
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Ecommerce\PriceLists\PriceList[]  $priceLists
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Ecommerce\Sizes\SizeCombination[] $sizes
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Products\Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Products\Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Products\Product query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Products\Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Products\Product whereDefaultPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Products\Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Products\Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Products\Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Products\Product whereReference($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Products\Product whereTaxable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Products\Product whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Products\Product whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Product extends Model
{
    use PresentableTrait;

    protected $presenter = ProductPresenter::class;

    protected $fillable = [
        'name',
        'type',
        'reference',
        'default_price',
        'taxable',
        'description',
        'size',
        'image',
    ];

    /**
     * Size
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sizes()
    {
        return $this->belongsToMany(SizeCombination::class, 'product_size', 'product_id', 'size_id', 'id', 'id');
    }

    /**
     * Price List
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function priceLists()
    {
        return $this->belongsToMany(PriceList::class)
            ->withPivot('price')->withTimestamps();
    }

    /**
     * Package
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function packages()
    {
        return $this->belongsToMany(Package::class)->withTimestamps();
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

    /**
     * @return bool
     */
    public function isDownloadable()
    {
        return ProductTypesEnum::DIGITAL_FULL()->is($this->type) || ProductTypesEnum::DIGITAL()->is($this->type) ||  ProductTypesEnum::SINGLE_DIGITAL()->is($this->type);
    }

    /**
     * @return bool
     */
    public function isDigitalFull()
    {
        return ProductTypesEnum::DIGITAL_FULL()->is($this->type);
    }

    /**
     * @return bool
     */
    public function isDigital()
    {
        return ProductTypesEnum::DIGITAL()->is($this->type);
    }

    /**
     * @return bool
     */
    public function isSingleDigital()
    {
        return ProductTypesEnum::SINGLE_DIGITAL()->is($this->type);
    }

    /**
     * @return bool
     */
    public function isRetouch()
    {
        return ProductTypesEnum::RETOUCH()->is($this->type);
    }
}
