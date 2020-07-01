<?php

namespace App\Photos\SubGalleries;

use App\Ecommerce\Cart\Cart;
use App\Ecommerce\Customers\Customer;
use App\Ecommerce\Orders\Order;
use App\Photos\AdditionalClassrooms\AdditionalClassrooms;
use App\Photos\Galleries\Gallery;
use App\Photos\People\Person;
use App\Photos\Photos\Photo;
use App\Photos\Photos\PhotoTypeEnum;
use App\Processing\Processes\OtherProcesses\ProofPhotoUpdatingProcess;
use App\Processing\ProcessingStatusesEnum;
use App\Processing\Scenarios\AddPhotoToSubgalleryScenario;
use App\Processing\StatusResolver;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;
use Webmagic\Core\Presenter\PresentableTrait;

/**
 * App\Photos\SubGalleries\Subgallery
 *
 * @property int                             $id
 * @property string                          $name
 * @property string                          $password
 * @property string                          $preview_image
 * @property int                             $gallery_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null                     $cropped_face_path
 * @property-read Person                     $person
 * @property-read Gallery                    $gallery
 * @method static Builder|SubGallery newModelQuery()
 * @method static Builder|SubGallery newQuery()
 * @method static Builder|SubGallery query()
 * @method static Builder|SubGallery whereCreatedAt($value)
 * @method static Builder|SubGallery whereCroppedFacePath($value)
 * @method static Builder|SubGallery whereGalleryId($value)
 * @method static Builder|SubGallery whereId($value)
 * @method static Builder|SubGallery whereName($value)
 * @method static Builder|SubGallery wherePassword($value)
 * @method static Builder|SubGallery wherePreviewImage($value)
 * @method static Builder|SubGallery whereUpdatedAt($value)
 * @mixin Eloquent
 * @method SubGalleryPresenter present()
 * @property int                                $available_on_class_photo
 * @property int                                $available_on_general_photo
 * @method static Builder|SubGallery whereAvailableOnClassPhoto($value)
 * @method static Builder|SubGallery whereAvailableOnGeneralPhoto($value)
 * @property int $main_photo_id
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Photos\Photos\Photo[] $originalPhotos
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Photos\Photos\Photo[] $photos
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Photos\Photos\Photo[] $previewPhotos
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Photos\SubGalleries\SubGallery whereMainPhotoId($value)
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Photos\Photos\Photo[] $mainPhoto
 */
class SubGallery extends Model
{
    use PresentableTrait;

    /** @var string  */
    protected $presenter = SubGalleryPresenter::class;

    protected $fillable = [
        'name',
        'password',
        'preview_image',
        'gallery_id',
        'main_photo_id',
        'cropped_face_path',
        'available_on_class_photo',
        'available_on_general_photo'
    ];

    /**
     * Gallery
     *
     * @return BelongsTo
     */
    public function gallery()
    {
        return $this->belongsTo(Gallery::class, 'gallery_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function additionalClassrooms()
    {
        return $this->hasMany(AdditionalClassrooms::class);
    }

    /**
     * Special price list for staff and regular for other
     *
     * @return \App\Ecommerce\PriceLists\PriceList|mixed|null
     */
    public function getPriceList()
    {
        return $this->person->isStaff() ? $this->gallery->staffPriceList : $this->gallery->priceList;
    }

    /**
     * Client
     *
     * @return HasOne
     */
    public function person()
    {
        return $this->hasOne(Person::class);
    }

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
    public function mainPhotos()
    {
        return $this->morphToMany(Photo::class, 'photo_able', 'photo_able', 'photo_id', 'photo_id', 'main_photo_id');
    }

    /**
     * @return mixed
     */
    public function mainPhoto()
    {
        return $this->mainPhotos->first();
    }


    /**
     * @return MorphToMany
     */
    public function originalPhotos()
    {
        return $this->photos()->where('type', PhotoTypeEnum::ORIGINAL);
    }

    /**
     * Carts
     *
     * @return HasMany
     */
    public function carts()
    {
        return $this->hasMany(Cart::class);
    }

    /**
     * Orders
     *
     * @return HasMany
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function customers()
    {
        return $this->belongsToMany(Customer::class)->withTimestamps();
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function isProofPhotosUpdatingInProgress()
    {
        $status = (new StatusResolver())->shortStatusForProcessableByProcess($this->id, get_class($this), ProofPhotoUpdatingProcess::class);

        return !ProcessingStatusesEnum::FINISHED()->is($status) && !ProcessingStatusesEnum::NEWER_STARTED()->is($status);
    }

    /**
     * Check if sub gallery processing status in progress
     *
     * @return bool
     * @throws Exception
     */
    public function isSubGalleryPhotoGenerationInProgress(): bool
    {
        $status = (new AddPhotoToSubgalleryScenario($this->id))->getStatus();

        if(ProcessingStatusesEnum::IN_PROGRESS()->is($status)){
            return true;
        }

        if(ProcessingStatusesEnum::FAILED()->is($status)){
            return true;
        }

        if(ProcessingStatusesEnum::WAIT()->is($status)){
            return true;
        }

        if(ProcessingStatusesEnum::IN_QUEUE()->is($status)){
            return true;
        }

        return false;
    }
}
