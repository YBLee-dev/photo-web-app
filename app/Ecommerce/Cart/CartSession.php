<?php

namespace App\Ecommerce\CartSessions;

use App\Ecommerce\PriceLists\PriceList;
use App\Photos\Galleries\Gallery;
use App\Photos\SubGalleries\SubGallery;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

/**
 * App\Ecommerce\Cart\CartSessions
 * @mixin Eloquent
 */
class CartSessions extends Model
{
    protected $fillable = [
        'id',
        'user_id',
        'key',
        'value'
    ];
}
