<?php

namespace App\Ecommerce\PromoCodes;

use App\Ecommerce\Customers\Customer;
use App\Ecommerce\Orders\Order;
use Illuminate\Database\Eloquent\Model;


/**
 * App\Ecommerce\PromoCodes\PromoCode
 *
 * @property int                                                                               $id
 * @property string                                                                            $name
 * @property string                                                                            $redeem_code
 * @property string                                                                            $type
 * @property float                                                                             $discount_amount
 * @property string|null                                                                   $active_from
 * @property string|null                                                                   $expires_at
 * @property string                                                                        $may_be_used
 * @property float|null                                                                    $cart_total_from
 * @property float|null                                                                        $cart_total_to
 * @property string|null                                                                       $description
 * @property string                                                                            $status
 * @property \Illuminate\Support\Carbon|null                                                   $created_at
 * @property \Illuminate\Support\Carbon|null                                                   $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Ecommerce\Customers\Customer[] $customers
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\PromoCodes\PromoCode newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\PromoCodes\PromoCode newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\PromoCodes\PromoCode query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\PromoCodes\PromoCode whereActiveFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\PromoCodes\PromoCode whereCartTotalFrom($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\PromoCodes\PromoCode whereCartTotalTo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\PromoCodes\PromoCode whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\PromoCodes\PromoCode whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\PromoCodes\PromoCode whereDiscountAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\PromoCodes\PromoCode whereExpiresAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\PromoCodes\PromoCode whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\PromoCodes\PromoCode whereMayBeUsed($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\PromoCodes\PromoCode whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\PromoCodes\PromoCode whereRedeemCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\PromoCodes\PromoCode whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\PromoCodes\PromoCode whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\PromoCodes\PromoCode whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class PromoCode extends Model
{
    protected $fillable = [
        'name',
        'redeem_code',
        'type',
        'discount_amount',
        'active_from',
        'expires_at',
        'may_be_used',
        'cart_total_from',
        'cart_total_to',
        'description',
        'status',
    ];


    /**
     * Customers
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function customers()
    {
        return $this->belongsToMany(Customer::class);
    }

    /**
     * Orders
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return bool
     */
    public function isOneTimeCode()
    {
        return $this->may_be_used === PromoCodeUsedTypesEnum::ONCE;
    }

    /**
     * @return bool
     */
    public function isOneTimeCodePerPerson()
    {
        return $this->may_be_used === PromoCodeUsedTypesEnum::ONCE_PERSON;
    }
}
