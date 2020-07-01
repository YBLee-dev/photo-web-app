<?php

namespace App\Photos\Seasons;

use App\Ecommerce\Orders\OrderDashboardControlsGenerator;
use App\Photos\Galleries\Gallery;
use App\Photos\Schools\School;
use App\Photos\SettingsGroupPhotos\SettingsGroupPhotos;
use App\Processing\ProcessingStatusesEnum;
use App\Processing\Scenarios\SeasonZipPreparingScenario;
use App\Processing\StatusResolver;
use Barryvdh\LaravelIdeHelper\Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Carbon;
use Webmagic\Core\Presenter\PresentableTrait;

/**
 * App\Photos\Seasons\Season
 *
 * @property int                             $id
 * @property string                          $name
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int                             $school_id
 * @property-read Gallery                    $gallery
 * @property-read School                     $school
 * @method static Builder|\App\Season\Season newModelQuery()
 * @method static Builder|\App\Season\Season newQuery()
 * @method static Builder|\App\Season\Season query()
 * @method static Builder|\App\Season\Season whereCreatedAt($value)
 * @method static Builder|\App\Season\Season whereId($value)
 * @method static Builder|\App\Season\Season whereName($value)
 * @method static Builder|\App\Season\Season whereSchoolId($value)
 * @method static Builder|\App\Season\Season whereUpdatedAt($value)
 * @mixin Eloquent
 * @property-read \App\Photos\SettingsGroupPhotos\SettingsGroupPhotos $groupSettings
 *
 * @method SeasonPresenter present()
 */
class Season extends Model
{
    use PresentableTrait;

    protected $fillable = [
        'name',
        'school_id'
    ];

    protected $presenter = SeasonPresenter::class;

    /**
     * Gallery
     *
     * @return HasOne
     */
    public function gallery()
    {
        return $this->hasOne(Gallery::class);
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
     * Group settings photo
     *
     * @return HasOne
     */
    public function groupSettings()
    {
        return $this->hasOne(SettingsGroupPhotos::class);
    }

    /**
     * @return SeasonDashboardControlsGenerator
     */
    public function dashboardElements()
    {
        return new SeasonDashboardControlsGenerator($this);
    }

    /**
     * @return bool
     * @throws \Exception
     */
    public function isZipPreparingInProgress()
    {
        $seasonZipPreparingScenarioStatus = (new StatusResolver())->getProcessableByScenarioShortStatus($this->id,  get_class($this), SeasonZipPreparingScenario::class);

        return !ProcessingStatusesEnum::FINISHED()->is($seasonZipPreparingScenarioStatus) && !ProcessingStatusesEnum::NEWER_STARTED()->is($seasonZipPreparingScenarioStatus);
    }

    /**
     * @return bool
     * @throws \Laracasts\Presenter\Exceptions\PresenterException
     */
    public function isZipPrepared()
    {
        return (new SeasonStorageManager())->isSeasonZipFileExists($this);
    }

    /**
     * @return SeasonRoutesGenerator
     */
    public function routs()
    {
        return new SeasonRoutesGenerator($this);
    }

    /**
     * @return int
     */
    public function isRetouchProductsCountPresent()
    {
        return $this->gallery->isRetouchProductsCountPresent();
    }
}
