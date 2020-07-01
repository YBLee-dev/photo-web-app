<?php

namespace App\Ecommerce\Orders;

use App\Ecommerce\Packages\Package;
use App\Ecommerce\Products\Product;
use App\Photos\SubGalleries\SubGallery;
use App\Ecommerce\Sizes\SizeCombination;
use App\Photos\Photos\Photo;
use App\Photos\Photos\PhotoTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;
use Webmagic\Core\Presenter\PresentableTrait;

/**
 * App\Ecommerce\Orders\OrderItem
 *
 * @property int                                            $id
 * @property int|null                                       $product_id
 * @property string                                         $item_id
 * @property string                                         $name
 * @property string                                         $price
 * @property int                                            $quantity
 * @property string                                          $sum
 * @property string                                        $image
 * @property int|null                                 $order_id
 * @property int|null                                 $package_id
 * @property string|null                     $package_name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null                        $size_combination_id
 * @property string|null                     $crop_info_width
 * @property string|null                     $crop_info_height
 * @property string|null                     $crop_info_x
 * @property string|null                     $crop_info_y
 * @property string|null                     $retouch
 * @property-read Order|null                 $order
 * @property-read Package|null               $package
 * @property-read Product|null               $product
 * @property-read SizeCombination|null       $size
 * @method static Builder|OrderItem newModelQuery()
 * @method static Builder|OrderItem newQuery()
 * @method static Builder|OrderItem query()
 * @method static Builder|OrderItem whereCreatedAt($value)
 * @method static Builder|OrderItem whereCropInfoHeight($value)
 * @method static Builder|OrderItem whereCropInfoWidth($value)
 * @method static Builder|OrderItem whereCropInfoX($value)
 * @method static Builder|OrderItem whereCropInfoY($value)
 * @method static Builder|OrderItem whereId($value)
 * @method static Builder|OrderItem whereImage($value)
 * @method static Builder|OrderItem whereItemId($value)
 * @method static Builder|OrderItem whereName($value)
 * @method static Builder|OrderItem whereOrderId($value)
 * @method static Builder|OrderItem wherePackageId($value)
 * @method static Builder|OrderItem wherePackageName($value)
 * @method static Builder|OrderItem wherePrice($value)
 * @method static Builder|OrderItem whereProductId($value)
 * @method static Builder|OrderItem whereQuantity($value)
 * @method static Builder|OrderItem whereSizeCombinationId($value)
 * @method static Builder|OrderItem whereSum($value)
 * @method static Builder|OrderItem whereUpdatedAt($value)
 * @mixin Eloquent
 *
 * @method OrderItemPresenter present()
 */
class OrderItem extends Model
{
    use PresentableTrait;

    protected $presenter = OrderItemPresenter::class;

    protected $fillable = [
        'order_id',
        'product_id',
        'sub_gallery_id',
        'item_id',
        'name',
        'price',
        'quantity',
        'sum',
        'image',
        'package_id',
        'package_name',
        'size_combination_id',
        'crop_info_width',
        'crop_info_height',
        'crop_info_x',
        'crop_info_y',
        'retouch'
    ];

    /**
     * @return BelongsTo
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
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

    /**
     * @return Photo|null
     */
    public function originalPhoto()
    {
        return $this->photos->where('type', PhotoTypeEnum::ORIGINAL)->first();
    }

    /**
     * @return MorphToMany
     */
    public function originalPhotos()
    {
        return $this->photos()->where('type', PhotoTypeEnum::ORIGINAL);
    }

    /**
     * @return Photo | null
     */
    public function photo()
    {
        return $this->photos->first();
    }

    /**
     * @return bool
     */
    public function isDownloadable()
    {
        return $this->product->isDownloadable();
    }

    /**
     * @return mixed
     */
    public function isDigitalFull()
    {
        return $this->product->isDigitalFull();
    }

    /**
     * @return mixed
     */
    public function isDigital()
    {
        return $this->product->isDigital();
    }

    /**
     * @return bool
     */
    public function isSingleDigital()
    {
        return  $this->product->isSingleDigital();
    }

    /**
     * @return Photo[]|\Illuminate\Database\Eloquent\Collection
     */
    public function personImages()
    {
        return $this->order->personImages();
    }

    public function subGallery()
    {
        return $this->belongsTo(SubGallery::class);
    }
}
