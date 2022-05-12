<?php

namespace App\Models;

use App\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Calendar extends Model
{
    protected $casts = [
        'appointmentDate' => 'datetime',
        Model::CREATED_AT => 'datetime',
        Model::UPDATED_AT => 'datetime',
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class, 'userId');
    }
}
