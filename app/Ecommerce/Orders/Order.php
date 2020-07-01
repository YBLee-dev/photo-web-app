<?php

namespace App\Ecommerce\Orders;

use App\Ecommerce\Customers\Customer;
use App\Ecommerce\PriceLists\PriceList;
use App\Ecommerce\Products\ProductTypesEnum;
use App\Ecommerce\PromoCodes\PromoCode;
use App\Photos\Galleries\Gallery;
use App\Photos\Photos\Photo;
use App\Photos\Photos\PhotoTypeEnum;
use App\Photos\SubGalleries\SubGallery;
use App\Processing\ProcessingStatusesEnum;
use App\Processing\Scenarios\OrderZipPreparingScenario;
use App\Processing\StatusResolver;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;
use Webmagic\Core\Presenter\PresentableTrait;

/**
 * App\Ecommerce\Orders\Order
 *
 * @property int                                                                             $id
 * @property string                                                                          $status
 * @property string                                                                          $payment_status
 * @property string                                                                          $customer_first_name
 * @property string                                                                          $customer_last_name
 * @property string                                                               $address
 * @property string                                                               $city
 * @property string                                                               $state
 * @property string                                                               $postal
 * @property string                                                               $country
 * @property string|null                                                          $message
 * @property int                                                                  $receive_promotions_by_email
 * @property int|null                                                             $customer_id
 * @property int|null                                                             $subgallery_id
 * @property int|null                                                             $gallery_id
 * @property int|null                                                             $price_list_id
 * @property Carbon|null                                      $created_at
 * @property Carbon|null                                      $updated_at
 * @property string                                                               $total
 * @property string                      $subtotal
 * @property string|null                 $discount
 * @property int                         $items_count
 * @property string|null                 $discount_type
 * @property string|null                 $discount_name
 * @property string|null                 $total_coupon
 * @property string|null                 $transaction_id
 * @property-read Customer|null          $customer
 * @property-read Gallery|null           $gallery
 * @property-read Collection|OrderItem[] $items
 * @property-read PriceList|null         $priceList
 * @property-read SubGallery|null        $subgallery
 * @method static Builder|Order newModelQuery()
 * @method static Builder|Order newQuery()
 * @method static Builder|Order query()
 * @method static Builder|Order whereAddress($value)
 * @method static Builder|Order whereCity($value)
 * @method static Builder|Order whereCountry($value)
 * @method static Builder|Order whereCreatedAt($value)
 * @method static Builder|Order whereCustomerFirstName($value)
 * @method static Builder|Order whereCustomerId($value)
 * @method static Builder|Order whereCustomerLastName($value)
 * @method static Builder|Order whereDiscount($value)
 * @method static Builder|Order whereDiscountName($value)
 * @method static Builder|Order whereDiscountType($value)
 * @method static Builder|Order whereGalleryId($value)
 * @method static Builder|Order whereId($value)
 * @method static Builder|Order whereItemsCount($value)
 * @method static Builder|Order whereMessage($value)
 * @method static Builder|Order wherePaymentStatus($value)
 * @method static Builder|Order wherePostal($value)
 * @method static Builder|Order wherePriceListId($value)
 * @method static Builder|Order whereReceivePromotionsByEmail($value)
 * @method static Builder|Order whereState($value)
 * @method static Builder|Order whereStatus($value)
 * @method static Builder|Order whereSubgalleryId($value)
 * @method static Builder|Order whereSubtotal($value)
 * @method static Builder|Order whereTotal($value)
 * @method static Builder|Order whereTotalCoupon($value)
 * @method static Builder|Order whereTransactionId($value)
 * @method static Builder|Order whereUpdatedAt($value)
 * @mixin Eloquent
 * @property int                  $free_gift
 * @method static Builder|Order whereFreeGift($value)
 * @method OrderPresenter present()
 * @property int|null             $sub_gallery_id
 * @property-read SubGallery|null $subGallery
 */
class Order extends Model
{
    use PresentableTrait;


    /*
     * Do not update timestamp update at because of digital products save on amazon with order timestamps
     */
    protected static function boot()
    {
        parent::boot();

        static::updating(function ($model) {
            $model->timestamps = false;
        });
        static::updated(function ($model) {
            $model->timestamps = true;
        });
    }

    protected $presenter = OrderPresenter::class;

    protected $fillable = [
        'customer_first_name',
        'customer_last_name',
        'address',
        'city',
        'state',
        'postal',
        'country',
        'message',
        'receive_promotions_by_email',

        'status',
        'payment_status',
        'transaction_id',

        'sub_gallery_id',
        'gallery_id',
        'price_list_id',
        'customer_id',
        'cart_id',
        'total',
        'subtotal',
        'total_coupon',
        'discount',
        'discount_type',
        'discount_name',
        'items_count',
        'free_gift',
        'tax',
        'promo_code_id',
        'hash'
    ];

    /**
     * @return MorphToMany
     */
    public function photos()
    {
        return $this->morphToMany(Photo::class, 'photo_able', 'photo_able');
    }

    /**
     * @return MorphToMany
     */
    public function printablePhotos()
    {
        return $this->photos()->where('type', PhotoTypeEnum::PRINTABLE);
    }

    /**
     * @return MorphToMany
     */
    public function originalPhotos()
    {
        return $this->photos()->where('type', PhotoTypeEnum::ORIGINAL);
    }

    /**
     * Customer
     *
     * @return BelongsTo
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Promo code
     *
     * @return BelongsTo
     */
    public function promoCode()
    {
        return $this->belongsTo(PromoCode::class);
    }

    /**
     * Order items
     *
     * @return HasMany
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * @return Builder|HasMany
     */
    public function printableItems()
    {
        return $this->items()
            ->whereHas('product', function ($query) {
                $query->where('type', '=', ProductTypesEnum::PRINTABLE);
                $query->whereNotNull('size_combination_id');
            });
    }

    /**
     * @return bool
     */
    public function isIdCardsIncluded()
    {
        return $this->subGallery->person->isStaff();
    }

    /**
     * Subgallery
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
     * @return bool
     * @throws Exception
     */
    public function isZipPreparingInProgress()
    {
        $orderZipPreparingScenarioStatus = (new StatusResolver())->getProcessableByScenarioShortStatus($this->id,  get_class($this), OrderZipPreparingScenario::class);

        return !ProcessingStatusesEnum::FINISHED()->is($orderZipPreparingScenarioStatus) && !ProcessingStatusesEnum::NEWER_STARTED()->is($orderZipPreparingScenarioStatus);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isZipPrepared()
    {
        return (new OrderStorageManager())->isOrderZipFileExists($this);
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isDigitalZipPrepared()
    {
        return (new OrderStorageManager())->isOrderDigitalZipFileExists($this);
    }

    /**
     * @return OrderRoutesGenerator
     */
    public function routs()
    {
        return new OrderRoutesGenerator($this);
    }

    /**
     * @return OrderDashboardControlsGenerator
     */
    public function dashboardElements()
    {
        return new OrderDashboardControlsGenerator($this);
    }

    /**
     * @return bool
     */
    public function isFreeGiftIncluded()
    {
        return $this->free_gift == 1;
    }

    /**
     * @return bool
     */
    public function isFreeGiftReady()
    {
        return $this->subGallery->person->isFreeGiftReady();
    }

    /**
     * @return HasMany|\Illuminate\Database\Query\Builder
     */
    public function packages()
    {
        return $this->items()->whereNotNull('package_id')->groupBy('item_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function productsInPackages()
    {
        return $this->items()->whereNotNull('package_id')->get()->groupBy('package_id');
    }

    /**
     * @return HasMany|\Illuminate\Database\Query\Builder
     */
    public function addons()
    {
        return $this->items()->whereNull('package_id');
    }

    /**
     * @return bool
     */
    public function isDigitalFull()
    {
        return $this->items()
                ->whereHas('product', function ($query) {
                    $query->where('type', '=', ProductTypesEnum::DIGITAL_FULL);
                })
                ->count() > 0;
    }

    /**
     * @return bool
     */
    public function isDigital()
    {
        return $this->items()
                ->whereHas('product', function ($query) {
                    $query->where('type', '=', ProductTypesEnum::DIGITAL);
                })
                ->count() > 0;
    }

    public function isDownloadable()
    {
        return $this->items()
                ->whereHas('product', function ($query) {
                    $query->where('type', '=', ProductTypesEnum::DIGITAL_FULL)
                        ->orWhere('type', '=', ProductTypesEnum::DIGITAL)
                        ->orWhere('type', '=', ProductTypesEnum::SINGLE_DIGITAL);
                })
                ->count() > 0;
    }

    /**
     * @return Photo[]|Collection
     */
    public function personImages()
    {
        return $this->subGallery->photos;
    }
}
