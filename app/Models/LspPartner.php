<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class LspPartner extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'slug',
        'logo_url',
        'sector',
        'schemes',
        'contact_name',
        'contact_email',
        'contact_phone',
        'website_url',
        'city',
        'province',
        'address',
        'description',
        'is_active',
    ];

    protected $casts = [
        'schemes' => 'array',
        'is_active' => 'boolean',
    ];

    public function registrations(): HasMany
    {
        return $this->hasMany(CertificationRegistration::class);
    }
}
