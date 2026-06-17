<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CrossImpactResponse extends Model
{
    protected $fillable = ['name', 'job', 'company','key_factor','key_actor','industrial_park'];

    // This automatically handles json_encode on save and json_decode on retrieve
    protected $casts = [
        'key_factor' => 'array',
        'key_actor' => 'array',
    ];
}
