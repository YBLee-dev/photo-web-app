<?php

namespace App\Ecommerce\Cart;

use App\Ecommerce\Packages\Package;
use App\Ecommerce\Products\Product;
use App\Ecommerce\Sizes\SizeCombination;
use App\Photos\Photos\Photo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;

/**
 * App\Ecommerce\Cart\CartItem
 *
 * @property int                                            $id
 * @property int|null                                       $product_id
 * @property string                                         $name
 * @property string                                         $price
 * @property int                                            $quantity
 * @property string                                         $sum
 * @property string                          $image
 * @property int|null                        $cart_id
 * @property int|null                        $package_id
 * @property string|null                     $package_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string                          $cart_item_id
 * @property int|null                        $size_combination_id
 * @property string|null                        $retouch
 * @property-read Cart|null                  $cart
 * @property-read Package|null               $package
 * @property-read Product|null               $product
 * @property-read SizeCombination|null       $size
 * @method static Builder|\App\Cart\CartItem newModelQuery()
 * @method static Builder|\App\Cart\CartItem newQuery()
 * @method static Builder|\App\Cart\CartItem query()
 * @method static Builder|\App\Cart\CartItem whereCartId($value)
 * @method static Builder|\App\Cart\CartItem whereCartItemId($value)
 * @method static Builder|\App\Cart\CartItem whereCreatedAt($value)
 * @method static Builder|\App\Cart\CartItem whereId($value)
 * @method static Builder|\App\Cart\CartItem whereImage($value)
 * @method static Builder|\App\Cart\CartItem whereName($value)
 * @method static Builder|\App\Cart\CartItem wherePackageId($value)
 * @method static Builder|\App\Cart\CartItem wherePackageName($value)
 * @method static Builder|\App\Cart\CartItem wherePrice($value)
 * @method static Builder|\App\Cart\CartItem whereProductId($value)
 * @method static Builder|\App\Cart\CartItem whereQuantity($value)
 * @method static Builder|\App\Cart\CartItem whereSizeCombinationId($value)
 * @method static Builder|\App\Cart\CartItem whereSum($value)
 * @method static Builder|\App\Cart\CartItem whereUpdatedAt($value)
 * @mixin Eloquent
 */
class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'package_id',
        'name',
        'price',
        'quantity',
        'sum',
        'image',
        'size',
        'package_name',
        'cart_item_id',
        'size_combination_id',
        'retouch',
        'product_type'
    ];

    /**
     * @return BelongsTo
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * @return BelongsTo
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * @return BelongsTo
     */
    public function package()
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Size
     *
     * @return BelongsTo
     */
    public function size()
    {
        return $this->belongsTo(SizeCombination::class, 'size_combination_id');
    }

    /**
     * @return MorphToMany
     */
    public function photos()
    {
        return $this->morphToMany(Photo::class, 'photo_able', 'photo_able');
    }
}
