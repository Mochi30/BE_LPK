<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Instructor extends Model
{
    protected $fillable = [
        'name',
        'title',
        'bio',
        'photo_url',
        'linkedin_url',
        'expertise',
        'order_index',
        'is_active',
    ];

    protected $casts = [
        'expertise' => 'array',
        'order_index' => 'integer',
        'is_active' => 'boolean',
    ];
}
