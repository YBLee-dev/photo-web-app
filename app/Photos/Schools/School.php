<?php

namespace App\Photos\Schools;

use App\Photos\Galleries\Gallery;
use App\Photos\Seasons\Season;
use Illuminate\Database\Eloquent\Model;
use Webmagic\Core\Presenter\PresentableTrait;

/**
 * App\Photos\Schools\School
 *
 * @property int                                                                           $id
 * @property string                                                                        $name
 * @property \Illuminate\Support\Carbon|null                                               $created_at
 * @property \Illuminate\Support\Carbon|null                                               $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Photos\Galleries\Gallery[] $galleries
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Photos\Seasons\Season[]    $seasons
 * @method static \Illuminate\Database\Eloquent\Builder|\App\School\School newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\School\School newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\School\School query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\School\School whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\School\School whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\School\School whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\School\School whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class School extends Model
{
    use PresentableTrait;
    protected $presenter = SchoolPresenter::class;

    protected $fillable = [
        'name',
        'school_logo'
    ];

    /**
     * Gallery
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }

    /**
     * Gallery
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function seasons()
    {
        return $this->hasMany(Season::class);
    }
}
