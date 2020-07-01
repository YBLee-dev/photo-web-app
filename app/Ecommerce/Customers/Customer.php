<?php

namespace App\Ecommerce\Customers;

use App\Ecommerce\PromoCodes\PromoCode;
use App\Photos\SubGalleries\SubGallery;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Ecommerce\Customers\Customer
 *
 * @property int                                                                                 $id
 * @property string                                                                              $email
 * @property \Illuminate\Support\Carbon|null                                                     $created_at
 * @property \Illuminate\Support\Carbon|null                                                     $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Ecommerce\PromoCodes\PromoCode[] $promoCodes
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Customers\Customer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Customers\Customer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Customers\Customer query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Customers\Customer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Customers\Customer whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Customers\Customer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Customers\Customer whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Customer extends Model
{
    protected $fillable = [
        'email'
    ];

    /**
     * Promo Codes
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function promoCodes()
    {
        return $this->belongsToMany(PromoCode::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function subGalleries()
    {
        return $this->belongsToMany(SubGallery::class)->withTimestamps();
    }
}
