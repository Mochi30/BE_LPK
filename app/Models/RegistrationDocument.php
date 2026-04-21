<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RegistrationDocument extends Model
{
    protected $fillable = [
        'certification_registration_id',
        'document_type',
        'original_name',
        'file_path',
        'mime_type',
        'file_size',
        'verification_status',
        'review_notes',
        'reviewed_at',
    ];

    protected $casts = [
        'certification_registration_id' => 'integer',
        'file_size' => 'integer',
        'reviewed_at' => 'datetime',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(CertificationRegistration::class, 'certification_registration_id');
    }
}
