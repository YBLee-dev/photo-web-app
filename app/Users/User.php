<?php

namespace App\Users;

use App\Photos\Galleries\Gallery;
use App\Users\Roles\Role;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Webmagic\Core\Presenter\PresentableTrait;

/**
 * App\Users\Roles
 *
 * @property int                                                                                                            $id
 * @property string                                                                                                         $name
 * @property string                                                                                                         $password
 * @property string|null                                                                                                    $remember_token
 * @property \Illuminate\Support\Carbon|null                                                                                $created_at
 * @property \Illuminate\Support\Carbon|null                                                                                $updated_at
 * @property-read \Illuminate\Notifications\DatabaseNotificationCollection|\Illuminate\Notifications\DatabaseNotification[] $notifications
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Users\Roles\User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Users\Roles\User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Users\Roles\User query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Users\Roles\User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Users\Roles\User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Users\Roles\User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Users\Roles\User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Users\Roles\User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Users\Roles\User whereUpdatedAt($value)
 * @mixin \Eloquent
 * @property string                                                                                                         $email
 * @property string                                                                                                         $credential_password
 * @property int|null                                                                                                       $status
 * @property int                                                                                                            $role_id
 * @property string                                                                                                         $ftp_login
 * @property string                                                                                                         $ftp_password
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Photos\Galleries\Gallery[]                                  $galleries
 * @property-read \App\Users\Roles\Role                                                                                     $role
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Users\Roles\User whereCredentialPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Users\Roles\User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Users\Roles\User whereFtpLogin($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Users\Roles\User whereFtpPassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Users\Roles\User whereRoleId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Users\Roles\User whereStatus($value)
 * @method UserPresenter present()
 */
class User extends Authenticatable
{
    use Notifiable, PresentableTrait;

    /** @var string Presenter */
    protected $presenter = UserPresenter::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'status',
        'ip_address',
        'credential_password',
        'role_id',
        'ftp_login',
        'ftp_password'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function galleries()
    {
        return $this->hasMany(Gallery::class);
    }


    /**
     * Check one role
     * @param string $role
     */
    public function hasRole($role)
    {
        return null !== $this->role()->where('name', $role)->first();
    }

    /**
     * @return bool
     */
    public function isAdmin(): bool
    {
       return $this->hasRole('Admin');
    }

}
