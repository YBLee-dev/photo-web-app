<?php

namespace App\Ecommerce\Sizes;

use App\Ecommerce\Cart\CartItem;
use App\Ecommerce\Orders\OrderItem;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Ecommerce\Sizes\SizeCombination
 *
 * @property int                                                                       $id
 * @property string                                                                    $name
 * @property \Illuminate\Support\Carbon|null                                           $created_at
 * @property \Illuminate\Support\Carbon|null                                           $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Ecommerce\Sizes\Size[] $sizes
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Sizes\SizeCombination newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Sizes\SizeCombination newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Sizes\SizeCombination query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Sizes\SizeCombination whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Sizes\SizeCombination whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Sizes\SizeCombination whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Sizes\SizeCombination whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class SizeCombination extends Model
{

    protected $fillable = [
        'name',
    ];

    /**
     * Size
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function sizes()
    {
        return $this->belongsToMany(Size::class)->withTimestamps()->withPivot('quantity');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
