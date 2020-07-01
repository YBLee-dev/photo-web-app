<?php

namespace App\Ecommerce\Sizes;

use Illuminate\Database\Eloquent\Model;
use Webmagic\Core\Presenter\PresentableTrait;
use Webmagic\Core\Presenter\Presenter;

/**
 * App\Ecommerce\Sizes\Size
 *
 * @property int                                                                                  $id
 * @property string                                                                               $name
 * @property float                                                                                $width
 * @property float                                                                                $height
 * @property \Illuminate\Support\Carbon|null                                                      $created_at
 * @property \Illuminate\Support\Carbon|null                                                      $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Ecommerce\Sizes\SizeCombination[] $combinations
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Sizes\Size newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Sizes\Size newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Sizes\Size query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Sizes\Size whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Sizes\Size whereHeight($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Sizes\Size whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Sizes\Size whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Sizes\Size whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Ecommerce\Sizes\Size whereWidth($value)
 * @mixin \Eloquent
 */
class Size extends Model
{
    use PresentableTrait;

    /** @var  Presenter class that using for present model */
    protected $presenter = SizePresenter::class;


    protected $fillable = [
        'name',
        'width',
        'height',
    ];

    /**
     * Size Combination
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function combinations()
    {
        return $this->belongsToMany(SizeCombination::class)->withTimestamps()->withPivot('quantity');
    }
}
