<?php

namespace App\Photos\People;

use App\Photos\AdditionalClassrooms\AdditionalClassrooms;
use App\Photos\Photos\Photo;
use App\Photos\Photos\PhotoTypeEnum;
use App\Photos\SubGalleries\SubGallery;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphToMany as MorphToManyAlias;
use Illuminate\Support\Carbon;
use Webmagic\Core\Presenter\PresentableTrait;

/**
 * App\Photos\People\Client
 *
 * @property int                                      $id
 * @property string|null                              $first_name
 * @property string|null                              $last_name
 * @property string|null                              $classroom
 * @property int|null                                 $graduate
 * @property int|null                                 $teacher
 * @property int                             $subgallery_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property string                          $school_name
 * @property string                          $contact_email
 * @property string                          $proof_photo_path
 * @property int                             $gallery_id
 * @property string|null                     $title
 * @property-read SubGallery                 $subgallery
 * @method static Builder|Person newModelQuery()
 * @method static Builder|Person newQuery()
 * @method static Builder|Person query()
 * @method static Buipresentlder|Person whereClassroom($value)
 * @method static Builder|Person whereContactEmail($value)
 * @method static Builder|Person whereCreatedAt($value)
 * @method static Builder|Person whereFirstName($value)
 * @method static Builder|Person whereGalleryId($value)
 * @method static Builder|Person whereGraduate($value)
 * @method static Builder|Person whereId($value)
 * @method static Builder|Person whereLastName($value)
 * @method static Builder|Person whereProofPhotoPath($value)
 * @method static Builder|Person whereSchoolName($value)
 * @method static Builder|Person whereSubgalleryId($value)
 * @method static Builder|Person whereTeacher($value)
 * @method static Builder|Person whereTitle($value)
 * @method static Builder|Person whereUpdatedAt($value)
 * @method PersonPresenter present()
 * @property int $sub_gallery_id
 */
class Person extends Model
{
    use PresentableTrait;

    /** @var string Presenter */
    protected $presenter = PersonPresenter::class;

    protected $fillable = [
        'first_name',
        'last_name',
        'graduate',
        'teacher',
        'classroom',
        'school_name',
        'contact_email',
        'proof_photo_path',
        'sub_gallery_id',
        'title',
        'all_class_photos',
        'position'
    ];

    /**
     * Relation to Sub gallery
     *
     * @return BelongsTo
     */
    public function subgallery()
    {
        return $this->belongsTo(SubGallery::class, 'sub_gallery_id', 'id');
    }

    /**
     * Check if person is staff
     *
     * @return bool
     */
    public function isStaff(): bool
    {
        if(!is_null($this->teacher)){
            return $this->teacher == 1;
        }

        return (new PersonService())->isTeacher($this->first_name);
    }

    /**
     * @return bool
     */
    public function shouldBeOnClassPhoto(): bool
    {
        return $this->subgallery->available_on_class_photo == 1;
    }

    /**
     * @return bool
     */
    public function shouldBeOnSchoolPhoto(): bool
    {
        return $this->subgallery->available_on_general_photo == 1;
    }

    /**
     * @return MorphToManyAlias
     */
    public function photos()
    {
        return $this->morphToMany(Photo::class, 'photo_able', 'photo_able', 'photo_able_id');
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function additionalClassrooms()
    {
        return $this->hasMany(AdditionalClassrooms::class);
    }

    /**
     * @return MorphToManyAlias
     */
    public function proofPhotos()
    {
        return $this->photos()->where('type', PhotoTypeEnum::PROOF);
    }

    /**
     * @return Photo|null
     */
    public function proofPhoto()
    {
        return $this->proofPhotos->first();
    }

    /**
     * @return MorphToManyAlias
     */
    public function classCommonPhotos()
    {
        return $this->photos()->where('type', PhotoTypeEnum::COMMON_CLASS_PHOTO);
    }

    /**
     * @return MorphToManyAlias
     */
    public function classCommonPhoto()
    {
        return $this->classCommonPhotos->first();
    }

    /**
     * @return MorphToManyAlias
     */
    public function classPersonalPhotos()
    {
        return $this->photos()->where('type', PhotoTypeEnum::PERSONAL_CLASS_PHOTO);
    }

    /**
     * @return MorphToManyAlias
     */
    public function freeGifts()
    {
        return $this->photos()->where('type', PhotoTypeEnum::FREE_GIFT);
    }

    /**
     * @return Photo
     */
    public function freeGift()
    {
        return $this->freeGifts->first();
    }

    /**
     * @return MorphToManyAlias
     */
    public function classPersonalPhoto()
    {
        return $this->classPersonalPhotos->first();
    }

    /**
     * @return MorphToManyAlias
     */
    public function staffCommonPhotos()
    {
        return $this->photos()->where('type', PhotoTypeEnum::STAFF_PHOTO);
    }

    /**
     * @return MorphToManyAlias
     */
    public function staffCommonPhoto()
    {
        return $this->staffCommonPhotos->first();
    }

    /**
     * @return MorphToManyAlias
     */
    public function croppedPhotos()
    {
        return $this->photos()
            ->where('photos.type', PhotoTypeEnum::CROPPED_FACE);
    }

    /**
     * @return Photo|null
     */
    public function croppedPhoto()
    {
        return $this->croppedPhotos->first();
    }

    /**
     * @return MorphToManyAlias
     */
    public function iDCardsPhotos()
    {
        return $this->photos()
            ->where(function(Builder $query){
                $query->where('photos.type', PhotoTypeEnum::ID_CARD_PORTRAIT)
                    ->orWhere('photos.type', PhotoTypeEnum::ID_CARD_LANDSCAPE);
            });
    }

    /**
     * @return bool
     */
    public function isFreeGiftReady()
    {
        return count($this->freeGifts) > 0;
    }
}
