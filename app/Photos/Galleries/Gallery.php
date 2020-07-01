<?php

namespace App\Photos\Galleries;

use App\Ecommerce\Cart\Cart;
use App\Ecommerce\Orders\Order;
use App\Ecommerce\PriceLists\PriceList;
use App\Photos\People\Person;
use App\Photos\Photos\Photo;
use App\Photos\Photos\PhotoTypeEnum;
use App\Photos\Schools\School;
use App\Photos\Seasons\Season;
use App\Photos\SubGalleries\SubGallery;
use App\Processing\Processes\ContinueScenarioProcess;
use App\Processing\Processes\OtherProcesses\ProofPhotosZipGenerationProcess;
use App\Processing\ProcessingStatusesEnum;
use App\Processing\Scenarios\GroupPhotosGenerationScenario;
use App\Processing\StatusResolver;
use App\Users\User;
use Eloquent;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;
use Illuminate\Database\Eloquent\Relations\MorphToMany as MorphToManyAlias;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Laracasts\Presenter\Exceptions\PresenterException;
use Webmagic\Core\Presenter\PresentableTrait;

/**
 * App\Photos\Galleries\Gallery
 *
 * @property int                                                                      $id
 * @property string                                                                   $name
 * @property string                                                                   $status
 * @property string                                                                   $path
 * @property int                                                                      $user_id
 * @property Carbon|null                                                              $created_at
 * @property Carbon|null                                                $updated_at
 * @property string                                                     $password
 * @property int|null                                                   $price_list_id
 * @property int|null                                                   $school_id
 * @property int|null                                                   $season_id
 * @property-read \Illuminate\Database\Eloquent\Collection|Cart[]       $carts
 * @property-read \Illuminate\Database\Eloquent\Collection|Order[]      $orders
 * @property-read PriceList|null                                        $priceList
 * @property-read School|null                                           $school
 * @property-read Season|null                                           $season
 * @property-read \Illuminate\Database\Eloquent\Collection|SubGallery[] $subgalleries
 * @property-read \Illuminate\Database\Eloquent\Collection|Person[]     $teachers
 * @property-read User                                                                $user
 * @method static Builder|Gallery newModelQuery()
 * @method static Builder|Gallery newQuery()
 * @method static Builder|Gallery query()
 * @method static Builder|Gallery whereCreatedAt($value)
 * @method static Builder|Gallery whereId($value)
 * @method static Builder|Gallery whereName($value)
 * @method static Builder|Gallery wherePassword($value)
 * @method static Builder|Gallery wherePath($value)
 * @method static Builder|Gallery wherePriceListId($value)
 * @method static Builder|Gallery whereSchoolId($value)
 * @method static Builder|Gallery whereSeasonId($value)
 * @method static Builder|Gallery whereStatus($value)
 * @method static Builder|Gallery whereUpdatedAt($value)
 * @method static Builder|Gallery whereUserId($value)
 * @mixin Eloquent
 * @method GalleryPresenter present()
 * @property-read \Illuminate\Database\Eloquent\Collection|SubGallery[] $subgalleriesForClassPhoto
 * @property-read \Illuminate\Database\Eloquent\Collection|SubGallery[] $subgalleriesForGeneralPhoto
 * @property string|null                                                $deadline
 * @property-read \Illuminate\Database\Eloquent\Collection|Person[]     $children
 * @property-read \Illuminate\Database\Eloquent\Collection|Person[]     $clients
 * @property-read \Illuminate\Database\Eloquent\Collection|SubGallery[] $subGalleries
 * @property-read \Illuminate\Database\Eloquent\Collection|SubGallery[] $subGalleriesForClassPhoto
 * @property-read \Illuminate\Database\Eloquent\Collection|SubGallery[] $subGalleriesForGeneralPhoto
 * @method static Builder|Gallery whereDeadline($value)
 */
class Gallery extends Model
{
    use PresentableTrait;

    /** @var string Presenter */
    protected $presenter = GalleryPresenter::class;

    protected $fillable = [
        'name',
        'status',
        'user_id',
        'path',
        'password',
        'price_list_id',
        'staff_price_list_id',
        'school_id',
        'season_id',
        'deadline',
    ];

    /**
     * User
     *
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * SubGallery
     *
     * @return HasMany
     */
    public function subGalleries()
    {
        return $this->hasMany(SubGallery::class)->with('person');
    }

    /**
     * SubGallery
     *
     * @return HasMany
     */
    public function subGalleriesWithTheirRelations()
    {
        return $this->hasMany(SubGallery::class)
            ->with('person')
            ->with('orders')
            ->with('carts')
            ;
    }

    /**
     * Sub galleries, who will presented on Class photo
     *
     * @return HasMany
     */
    public function subGalleriesForClassPhoto()
    {
        return $this->subGalleries()->where('available_on_class_photo', '=', true);
    }

    /**
     * Sub galleries, who will presented on General photo
     *
     * @return HasMany
     */
    public function subGalleriesForGeneralPhoto()
    {
        return $this->subGalleries()->where('available_on_general_photo', '=', true);
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
     * Staff price List
     *
     * @return BelongsTo
     */
    public function staffPriceList()
    {
        return $this->belongsTo(PriceList::class, 'staff_price_list_id');
    }

    /**
     * School
     *
     * @return BelongsTo
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    /**
     * @return MorphToManyAlias
     */
    public function staffCommonPhotos()
    {
        return $this->photos()->where('type', PhotoTypeEnum::STAFF_PHOTO);
    }

    /**
     * Return all gallery photos
     *
     * @return Photo [] | array
     */
    public function allPhotos()
    {
        // Add group photos
        $allPhotos = $this->allGroupPhotos();

        // Add ID cards photos
        $people = $this->people;
        foreach ($people as $person){
            $allPhotos = array_merge($allPhotos, $person->iDCardsPhotos->all());
        }

        return $allPhotos;
    }

    /**
     * @return Photo [] | array
     */
    public function allGroupPhotos()
    {
        $allPhotos = $this->photos->all();

        // Add personal class photos
        $people = $this->people;
        foreach ($people as $person){
            $allPhotos = array_merge($allPhotos, $person->classPersonalPhotos->all());
            $allPhotos = array_merge($allPhotos, $person->proofPhotos->all());
        }

        //Add staff common photo
        $teachers = $this->teachers;
        foreach ($teachers as $teacher) {
            $allPhotos = array_merge($allPhotos, $teacher->staffCommonPhotos->all());
        }

        // Add classes common photos
        $groupedPeople = $people->groupBy(function(Person $person){
            return $person->classroom;
        });

        foreach ($groupedPeople as $classRoom) {
            $allPhotos = array_merge($allPhotos, $classRoom->first()->classCommonPhotos->all());
        }

        return $allPhotos;
    }

    /**
     * Season
     *
     * @return BelongsTo
     */
    public function season()
    {
        return $this->belongsTo(Season::class);
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
     * Client
     *
     * @return HasMany
     */
    public function teachers()
    {
        return $this->people()->where('teacher', 1);
    }

    /**
     * Client
     *
     * @return HasMany
     */
    public function children()
    {
        return $this->people()->where('teacher', 0);
    }

    /**
     * Clients relation
     *
     * @return HasManyThrough
     */
    public function people()
    {
        return $this->hasManyThrough(Person::class, SubGallery::class);
    }

    /**
     * @param $classroomName
     *
     * @return mixed
     */
    public function classRoomPeople($classroomName)
    {
        return $this->people->filter(function($person) use ($classroomName) {
            return ($person->classroom == $classroomName) || $person->all_class_photos || $person->additionalClassrooms->contains('name', $classroomName);
        });
    }

    /**
     * Photos
     *
     * @return MorphToManyAlias
     */
    public function photos()
    {
        return $this->morphToMany(Photo::class, 'photo_able', 'photo_able');
    }

    /**
     * @return MorphToManyAlias
     */
    public function schoolCommonPhotos()
    {
        return $this->photos()->where('type', PhotoTypeEnum::SCHOOL_PHOTO);
    }

    /**
     * @return MorphToManyAlias
     */
    public function classesCommonPhotos()
    {
        return $this->photos()
            ->with('people')
            ->where('type', PhotoTypeEnum::COMMON_CLASS_PHOTO);
    }

    /**
     * @return Photo
     */
    public function schoolCommonPhoto()
    {
        return $this->schoolCommonPhotos->first();
    }

    /**
     * @return MorphToManyAlias
     */
    public function miniWalletCollages()
    {
        return $this->photos()->where('type', PhotoTypeEnum::MINI_WALLET_COLLAGE);
    }

    /**
     * Return sub galleries filtered by classroom
     *
     * @param string $classroomName
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getClassroomSubGalleries(string $classroomName): Collection
    {
        return $this->subgalleries->filter(function (SubGallery $item) use ($classroomName) {
            return $item->person->classroom == $classroomName;
        });
    }

    /**
     * Prepare staff subgalleries
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStaffSubGalleries(): Collection
    {
        return $this->subgalleries->filter(function (SubGallery $item) {
            return $item->person->isStaff();
        });
    }

    /**
     * Prepare class rooms list
     *
     * @param bool $withValues
     *
     * @return array
     */
    public function getClassroomsList(bool $withValues = false): array
    {
        $classrooms = [];

        foreach ($this->subgalleries as $subGallery) {
            $classroom = is_null($subGallery->person) ? : $subGallery->person->classroom;
            $classrooms[$classroom ? : 'None'] = $classroom ? : 'None';
        }

        // Keys uses to get uniq only
        return $withValues ? $classrooms : array_keys($classrooms);
    }

    /**
     * @return bool
     */
    public function isAvailableForView()
    {
        return GalleryStatusEnum::READY()->is($this->status);
    }

    /**
     * Check if gallery processing status in progress
     *
     * @return bool
     * @throws Exception
     */
    public function isGroupPhotoGenerationInProgress(): bool
    {
        $status = (new GroupPhotosGenerationScenario($this->id))->getStatus();

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

    public function istGroupPhotoGenerationInWait()
    {
        $status = (new GroupPhotosGenerationScenario($this->id))->getStatus();

        if(ProcessingStatusesEnum::WAIT()->is($status)){
            return true;
        }

        return false;
    }

    /*
     * Start groups photos generation for gallery
     */
    public function groupPhotoGenerationStart()
    {
        (new GroupPhotosGenerationScenario($this->id))->start();
    }

    /**
     * Check, is time to deadline finish
     *
     * @return bool
     */
    public function isDeadlineCame()
    {
        $deadline = Carbon::parse($this->deadline)->format('Y-m-d');
        $now = Carbon::now()->format('Y-m-d');

        return $now > $deadline;
    }

    /**
     * @return GalleriesDashboardControlsGenerator
     */
    public function controls()
    {
        return (new GalleriesDashboardControlsGenerator($this));
    }

    /**
     * @return GalleryRouts
     */
    public function routs()
    {
        return (new GalleryRouts($this));
    }

    /**
     * @return GalleriesDashboardControlsGenerator
     */
    public function dashboardElements()
    {
        return (new GalleriesDashboardControlsGenerator($this));
    }

    /**
     * @return bool
     * @throws PresenterException
     */
    public function isProofPhotosZipReady()
    {
        return file_exists($this->present()->proofExportFullPath());
    }

    /**
     * @return bool
     * @throws Exception
     */
    public function isProofPhotosZipInProgress()
    {
        $status = (new StatusResolver())->shortStatusForProcessableByProcess($this->id, get_class($this), ProofPhotosZipGenerationProcess::class);

        return !ProcessingStatusesEnum::FINISHED()->is($status) && !ProcessingStatusesEnum::NEWER_STARTED()->is($status);
    }

    /**
     * @return mixed
     * @throws \Exception
     */
    public function isGroupPhotosWasGenerated()
    {
        $status = (new StatusResolver())->shortStatusForProcessableByProcess($this->id, GroupPhotosGenerationScenario::class, ContinueScenarioProcess::class);

        return ProcessingStatusesEnum::FINISHED()->is($status);
    }

    /**
     * @return int
     */
    public function isRetouchProductsCountPresent()
    {
        return $this->orders()
                ->whereHas('items', function ($query) {
                    $query->whereNotNull('retouch');
                })->count();
    }
}
