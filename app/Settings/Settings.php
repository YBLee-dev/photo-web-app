<?php

namespace App\Settings;

use Illuminate\Database\Eloquent\Model;
use Webmagic\Core\Presenter\PresentableTrait;

/**
 * App\Settings\Settings
 *
 * @property int $id
 * @property string|null $admin_email
 * @property string|null $email_signature
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settings\Settings newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settings\Settings newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settings\Settings query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settings\Settings whereAdminEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settings\Settings whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settings\Settings whereEmailSignature($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settings\Settings whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Settings\Settings whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Settings extends Model
{
    use PresentableTrait;

    protected $presenter = SettingsPresenter::class;

    protected $table = 'settings';

    protected $fillable = [
        'admin_email',
        'email_signature',
        'email_signature_image'
    ];
}
