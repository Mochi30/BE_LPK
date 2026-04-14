<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Program extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'summary',
        'description',
        'duration_weeks',
        'level',
        'price',
        'is_featured',
        'is_active',
        'order_index',
    ];

    protected $casts = [
        'duration_weeks' => 'integer',
        'price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'order_index' => 'integer',
    ];

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }
}
