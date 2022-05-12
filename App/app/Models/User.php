<?php

namespace App\Models;

use App\Eloquent\Model;

class User extends Model
{
    protected $casts = [
        Model::CREATED_AT => 'datetime',
        Model::UPDATED_AT => 'datetime',
    ];
}
