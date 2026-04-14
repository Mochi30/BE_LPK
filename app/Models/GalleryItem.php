<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GalleryItem extends Model
{
    protected $fillable = [
        'title',
        'image_url',
        'category',
        'order_index',
        'is_featured',
        'is_active',
    ];

    protected $casts = [
        'order_index' => 'integer',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
    ];
}
