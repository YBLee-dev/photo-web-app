<?php

namespace App\Ecommerce\Cart;

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
 * App\Ecommerce\Cart\Cart
 *
 * @property int                                                                          $id
 * @property string                                                                       $total
 * @property int                                                                          $items_count
 * @property int|null                                                                     $sub_gallery_id
 * @property int|null                        $gallery_id
 * @property int|null                        $price_list_id
 * @property int|null                        $abandoned
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string                          $session_key
 * @property-read Gallery|null               $gallery
 * @property-read Collection|CartItem[]      $items
 * @property-read PriceList|null             $priceList
 * @property-read SubGallery|null            $subgallery
 * @method static Builder|\App\Cart\Cart newModelQuery()
 * @method static Builder|\App\Cart\Cart newQuery()
 * @method static Builder|\App\Cart\Cart query()
 * @method static Builder|\App\Cart\Cart whereAbandoned($value)
 * @method static Builder|\App\Cart\Cart whereCreatedAt($value)
 * @method static Builder|\App\Cart\Cart whereGalleryId($value)
 * @method static Builder|\App\Cart\Cart whereId($value)
 * @method static Builder|\App\Cart\Cart whereItemsCount($value)
 * @method static Builder|\App\Cart\Cart wherePriceListId($value)
 * @method static Builder|\App\Cart\Cart whereSessionKey($value)
 * @method static Builder|\App\Cart\Cart whereSubGalleryId($value)
 * @method static Builder|\App\Cart\Cart whereTotal($value)
 * @method static Builder|\App\Cart\Cart whereUpdatedAt($value)
 * @mixin Eloquent
 */
class Cart extends Model
{
    protected $fillable = [
        'total',
        'items_count',
        'sub_gallery_id',
        'gallery_id',
        'price_list_id',
        'abandoned',
        'session_key',
        'free_gift'
    ];

    /**
     * SubGallery
     *
     * @return BelongsTo
     */
    public function subGallery()
    {
        return $this->belongsTo(SubGallery::class);
    }

    /**
     * Gallery
     *
     * @return BelongsTo
     */
    public function gallery()
    {
        return $this->belongsTo(Gallery::class);
    }

    /**
     * Price List
     *
     * @return BelongsTo
     */
    public function priceList()
    {
        return $this->belongsTo(PriceList::class);
    }

    /**
     * Cart items
     *
     * @return HasMany
     */
    public function items()
    {
        return $this->hasMany(CartItem::class);
    }
}
