<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Sanctum\HasApiTokens;

class Movie extends Model
{
    use HasApiTokens, HasUuids, SoftDeletes;
    protected $guarded = [];

    protected $casts = [
        'genre' => 'array',
        'created_at' => 'datetime',
        'deleted_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getCoversUrlAttribute()
    {
        return $this->covers ? asset('storage/' . $this->covers) : null;
    }
}
