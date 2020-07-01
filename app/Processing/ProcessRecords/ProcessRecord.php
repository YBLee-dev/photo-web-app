<?php


namespace App\Processing\ProcessRecords;


use App\Processing\Processes\GroupPhotosProcessing\ProcessesTypesEnum;
use App\Processing\ProcessingStatusesEnum;
use App\Processing\StatusResolvable;
use Eloquent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Carbon;
use MadWeb\Enum\EnumCastable;

/**
 * App\Processing\ProcessRecords\ProcessRecord
 *
 * @property-read Collection|ProcessRecord[] $processable
 * @method static Builder|ProcessRecord newModelQuery()
 * @method static Builder|ProcessRecord newQuery()
 * @method static Builder|ProcessRecord query()
 * @mixin Eloquent
 * @property int                                                           $id
 * @property mixed                                                         $status
 * @property int|null                                                      $job_id
 * @property string                                                        $process
 * @property string|null                                                   $scenario
 * @property int                                                           $processable_id
 * @property string                                                        $processable_type
 * @property Carbon|null                               $created_at
 * @property Carbon|null                               $updated_at
 * @method static Builder|ProcessRecord whereCreatedAt($value)
 * @method static Builder|ProcessRecord whereId($value)
 * @method static Builder|ProcessRecord whereJobId($value)
 * @method static Builder|ProcessRecord whereProcess($value)
 * @method static Builder|ProcessRecord whereProcessableId($value)
 * @method static Builder|ProcessRecord whereProcessableType($value)
 * @method static Builder|ProcessRecord whereScenario($value)
 * @method static Builder|ProcessRecord whereStatus($value)
 * @method static Builder|ProcessRecord whereUpdatedAt($value)
 */
class ProcessRecord extends Model implements StatusResolvable
{
    use EnumCastable;

    protected $casts = [
        'status' => ProcessingStatusesEnum::class,
    ];

    protected $fillable = [
        'status',
        'job_id',
        'process',
        'scenario',
        'processable_id',
        'processable_type'
    ];

    /**
     * Polymorphic relation
     *
     * @return MorphTo
     */
    public function processable()
    {
        return $this->morphTo();
    }

    /**
     * @return mixed
     */
    public function getStatus(): string
    {
        return $this->status;
    }
}
