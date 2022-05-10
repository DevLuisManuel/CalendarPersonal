<?php

namespace App\Models;

use App\Eloquent\Model;
use Illuminate\Database\Eloquent\{Factories\HasFactory, Relations\BelongsTo};

class Calendar extends Model
{
    protected $casts = [
        'appointmentDate' => 'datetime'
    ];

    public function users(): BelongsTo
    {
        return $this->belongsTo(User::class,'userId');
    }
}
