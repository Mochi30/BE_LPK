<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    protected $fillable = [
        'name',
        'role',
        'company',
        'quote',
        'rating',
        'photo_url',
        'is_featured',
        'is_active',
        'order_index',
    ];

    protected $casts = [
        'rating' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'order_index' => 'integer',
    ];
}
