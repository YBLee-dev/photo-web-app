<?php


namespace App\Photos\Photos;


use App\Ecommerce\Cart\CartItem;
use App\Ecommerce\Orders\OrderItem;
use App\Photos\Galleries\Gallery;
use App\Photos\People\Person;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphToMany;
use Illuminate\Support\Carbon;
use Laracasts\Presenter\Exceptions\PresenterException;
use MadWeb\Enum\EnumCastable;
use Webmagic\Core\Presenter\PresentableTrait;

/**
 * App\Photos\Photos\Photo
 *
 * @method static Builder|Photo newModelQuery()
 * @method static Builder|Photo newQuery()
 * @method static Builder|Photo query()
 * @method PhotoPresenter present()
 * @property int $id
 * @property mixed $type
 * @property string $original_filename
 * @property string $height
 * @property string $width
 * @property int $size
 * @property int $photo_able_id
 * @property string $photo_able_type
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string|null $extension
 * @property string|null $status
 * @property int|null $local_copy
 * @property int|null $remote_copy
 * @method static Builder|Photo whereCreatedAt($value)
 * @method static Builder|Photo whereExtension($value)
 * @method static Builder|Photo whereHeight($value)
 * @method static Builder|Photo whereId($value)
 * @method static Builder|Photo whereLocalCopy($value)
 * @method static Builder|Photo whereOriginalFilename($value)
 * @method static Builder|Photo wherePhotoAbleId($value)
 * @method static Builder|Photo wherePhotoAbleType($value)
 * @method static Builder|Photo whereRemoteCopy($value)
 * @method static Builder|Photo whereSize($value)
 * @method static Builder|Photo whereStatus($value)
 * @method static Builder|Photo whereType($value)
 * @method static Builder|Photo whereUpdatedAt($value)
 * @method static Builder|Photo whereWidth($value)
 */
class Photo extends Model
{
    use EnumCastable, PresentableTrait;

    protected $presenter = PhotoPresenter::class;

    protected $casts = [
        'type' => PhotoTypeEnum::class
    ];

    protected $fillable = [
        'type',
        'original_filename',
        'extension',
        'height',
        'width',
        'size',
        'status',
        'local_copy',
        'remote_copy',
        'photo_able_id',
        'photo_able_type',
        'crop_x',
        'crop_y',
        'crop_original_width',
        'crop_original_height'
    ];

    /**
     * @return string
     */
    public function fileName()
    {
        return md5($this->id)."_{$this->id}.{$this->extension}";
    }

    /**
     * @param string $key
     *
     * @return string
     */
    public function printableFileName(string $key = '')
    {
        if(PhotoTypeEnum::PROOF()->is($this->type)){
            $fileName = $this->people->first()->id. '_' .str_slug($this->people->first()->present()->name, '_');

            return $this->combineName($fileName, $this->extension, $key);
        }

        if (PhotoTypeEnum::SCHOOL_PHOTO()->is($this->type)){
            $fileName = 'school_photo_'.str_slug($this->galleries->first()->school->name, '_');

            return $this->combineName($fileName, $this->extension, $key);
        }

        if (PhotoTypeEnum::COMMON_CLASS_PHOTO()->is($this->type)){
            $fileName = 'class_photo_'.$this->id;

            return $this->combineName($fileName, $this->extension, $key);
        }

        if (PhotoTypeEnum::STAFF_PHOTO()->is($this->type)){
            $fileName = 'staff_photo_'.str_slug( $this->people->first()->school_name, '_');
            return $this->combineName($fileName, $this->extension, $key);
        }

        if (PhotoTypeEnum::ID_CARD_PORTRAIT()->is($this->type)){
            $fileName = 'ID_card_'.str_slug($this->people->first()->present()->name, '_').'_portrait';

            return $this->combineName($fileName, $this->extension, $key);
        }

        if (PhotoTypeEnum::ID_CARD_LANDSCAPE()->is($this->type)){
            $fileName = 'ID_card_'.str_slug($this->people->first()->present()->name, '_').'_landscape';

            return $this->combineName($fileName, $this->extension, $key);
        }

        if (PhotoTypeEnum::MINI_WALLET_COLLAGE()->is($this->type)){
            $fileName = 'mini_wallet_collage_'.str_slug( $this->galleries->first()->school->name, '_');

            return $this->combineName($fileName, $this->extension, $key);
        }

        return $this->original_filename;
    }

    /**
     * @param string $name
     * @param string $extension
     * @param string $key
     *
     * @return string
     */
    protected function combineName(string $name, string $extension, string $key = '')
    {
        return $key ? "{$name}_$key.$extension" : "$name.$extension";
    }

    /**
     * @return MorphToMany
     */
    public function photoAbles()
    {
        return $this->morphToMany();
    }

    /**
     * @return mixed
     */
    public function hasPreview()
    {
        return PhotoTypeEnum::ORIGINAL()->is($this->type);
    }

    /**
     * Check if remote photo exists
     *
     * @return bool
     * @throws PresenterException
     * @throws PresenterException
     */
    public function isRemoteFileExists()
    {
        return (new PhotoStorageManager())->isRemotePhotoExists($this);
    }

    /**
     * Check if remote photo exists
     *
     * @return bool
     * @throws PresenterException
     * @throws PresenterException
     */
    public function isLocalFileExists()
    {
        return (new PhotoStorageManager())->isLocalPhotoExists($this);
    }

    /**
     * @return bool
     * @throws PresenterException
     */
    public function isReadAble()
    {
        return $this->isLocalFileExists() || $this->isRemoteFileExists();
    }

    /**
     * @return bool|mixed|string
     * @throws PresenterException
     */
    public function bestReadablePath()
    {
        if($this->isLocalFileExists()){
            return $this->present()->originalLocalPath();
        }

        if($this->isRemoteFileExists()){
            return $this->present()->originalUrl();
        }

        return false;
    }

    /**
     * @return bool|false|string
     * @throws PresenterException
     */
    public function getFileContent()
    {
        $bestFilePath = $this->bestReadablePath();

        if($bestFilePath) {
            return file_get_contents($bestFilePath);
        }

        return false;
    }

    /**
     * @return mixed
     */
    public function isOriginal()
    {
        return PhotoTypeEnum::ORIGINAL()->is($this->type);
    }

    /**
     * @return MorphToMany
     */
    public function cartItems()
    {
        return $this->morphedByMany(CartItem::class, 'photo_able','photo_able');
    }

    /**
     * @return MorphToMany
     */
    public function people()
    {
        return $this->morphedByMany(Person::class, 'photo_able','photo_able');
    }

    /**
     * @return MorphToMany
     */
    public function galleries()
    {
        return $this->morphedByMany(Gallery::class, 'photo_able','photo_able');
    }

    /**
     * @return MorphToMany
     */
    public function orderItems()
    {
        return $this->morphedByMany(OrderItem::class, 'photo_able', 'photo_able');
    }

    /**
     * @return bool
     */
    public function canBeDeleted()
    {
        return !$this->cartItems->count() && !$this->orderItems->count();
    }
}
