<?php

namespace App\Photos\AdditionalClassrooms;

use Illuminate\Database\Eloquent\Model;


class AdditionalClassrooms extends Model
{
    protected $fillable = [
        'name',
        'person_id',
        'sub_gallery_id'
    ];
}
