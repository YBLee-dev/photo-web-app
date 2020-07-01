<?php

namespace App\Users\Roles;

use App\Users\User;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Users\Roles\Role
 *
 * @property int                                                             $id
 * @property string                                                          $name
 * @property string                                                          $description
 * @property \Illuminate\Support\Carbon|null                                 $created_at
 * @property \Illuminate\Support\Carbon|null                                 $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection|\App\Users\User[] $users
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Users\Roles\Role newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Users\Roles\Role newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Users\Roles\Role query()
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Users\Roles\Role whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Users\Roles\Role whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Users\Roles\Role whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Users\Roles\Role whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|\App\Users\Roles\Role whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Role extends Model
{
    public function users()
    {
        return $this->belongsToMany(User::class);
    }
}
