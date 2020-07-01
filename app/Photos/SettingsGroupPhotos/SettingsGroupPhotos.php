<?php

namespace App\Photos\SettingsGroupPhotos;

use App\Photos\Seasons\Season;
use Illuminate\Database\Eloquent\Model;
use Webmagic\Core\Presenter\PresentableTrait;

/**
 * App\Photos\SettingsGroupPhotos\SettingsGroupPhotos
 *
 * @property int                             $id
 * @property string|null                     $naming_structure
 * @property int|null                        $use_teacher_prefix
 * @property string|null                     $font_file
 * @property int|null                        $school_name_font_size
 * @property int|null                        $class_name_font_size
 * @property int|null                        $year_font_size
 * @property int|null                          $name_font_size
 * @property string|null                       $school_background
 * @property string|null                       $class_background
 * @property string|null                       $id_cards_background_portrait
 * @property string|null                       $id_cards_background_landscape
 * @property int|null                          $id_cards_portrait_name_size
 * @property int|null                        $id_cards_portrait_title_size
 * @property int|null                        $id_cards_portrait_year_size
 * @property int|null                        $id_cards_landscape_name_size
 * @property int|null                        $id_cards_landscape_title_size
 * @property int|null                        $id_cards_landscape_year_size
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Photos\Seasons\Season $season
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Photos\SettingsGroupPhotos\SettingsGroupPhotos newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Photos\SettingsGroupPhotos\SettingsGroupPhotos newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Photos\SettingsGroupPhotos\SettingsGroupPhotos query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Photos\SettingsGroupPhotos\SettingsGroupPhotos whereClassBackground($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Photos\SettingsGroupPhotos\SettingsGroupPhotos whereClassNameFontSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Photos\SettingsGroupPhotos\SettingsGroupPhotos whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Photos\SettingsGroupPhotos\SettingsGroupPhotos whereFontFile($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Photos\SettingsGroupPhotos\SettingsGroupPhotos whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Photos\SettingsGroupPhotos\SettingsGroupPhotos whereIdCardsBackgroundLandscape($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Photos\SettingsGroupPhotos\SettingsGroupPhotos whereIdCardsBackgroundPortrait($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Photos\SettingsGroupPhotos\SettingsGroupPhotos whereIdCardsLandscapeNameSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Photos\SettingsGroupPhotos\SettingsGroupPhotos whereIdCardsLandscapeTitleSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Photos\SettingsGroupPhotos\SettingsGroupPhotos whereIdCardsLandscapeYearSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Photos\SettingsGroupPhotos\SettingsGroupPhotos whereIdCardsPortraitNameSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Photos\SettingsGroupPhotos\SettingsGroupPhotos whereIdCardsPortraitTitleSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Photos\SettingsGroupPhotos\SettingsGroupPhotos whereIdCardsPortraitYearSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Photos\SettingsGroupPhotos\SettingsGroupPhotos whereNameFontSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Photos\SettingsGroupPhotos\SettingsGroupPhotos whereNamingStructure($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Photos\SettingsGroupPhotos\SettingsGroupPhotos whereSchoolBackground($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Photos\SettingsGroupPhotos\SettingsGroupPhotos whereSchoolNameFontSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Photos\SettingsGroupPhotos\SettingsGroupPhotos whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Photos\SettingsGroupPhotos\SettingsGroupPhotos whereUseTeacherPrefix($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Photos\SettingsGroupPhotos\SettingsGroupPhotos whereYearFontSize($value)
 * @mixin \Eloquent
 * @method SettingsGroupPhotoPresenter present()
 * @property string|null $school_name
 * @property string|null $year
 * @property string|null $class_name
 * @property string|null $school_logo
 * @property int $season_id
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Photos\SettingsGroupPhotos\SettingsGroupPhotos whereClassName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Photos\SettingsGroupPhotos\SettingsGroupPhotos whereSchoolLogo($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Photos\SettingsGroupPhotos\SettingsGroupPhotos whereSchoolName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Photos\SettingsGroupPhotos\SettingsGroupPhotos whereSeasonId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Photos\SettingsGroupPhotos\SettingsGroupPhotos whereYear($value)
 */
class SettingsGroupPhotos extends Model
{
    use PresentableTrait;


    protected $presenter = SettingsGroupPhotoPresenter::class;

    protected $fillable = [
        'naming_structure',

        'use_teacher_prefix',
        'font_file',
        'school_name_font_size',
        'class_name_font_size',
        'year_font_size',
        'name_font_size',

        'school_name_font_size_school_photo',
        'year_font_size_school_photo',
        'name_font_size_school_photo',

        'school_background',
        'class_background',

        'id_cards_background_portrait',
        'id_cards_background_landscape',
        'id_cards_portrait_name_size',
        'id_cards_portrait_title_size',
        'id_cards_portrait_year_size',
        'id_cards_landscape_name_size',
        'id_cards_landscape_title_size',
        'id_cards_landscape_year_size',

        'personal_name_font_size',

        'season_id',

        'school_name',
        'year',
        'school_logo',
        'use_school_logo'
    ];


    /**
     * Season
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function season()
    {
        return $this->belongsTo(Season::class);
    }
}
