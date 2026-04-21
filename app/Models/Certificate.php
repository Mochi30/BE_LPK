<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Certificate extends Model
{
    protected $fillable = [
        'certification_registration_id',
        'certificate_number',
        'approval_status',
        'approval_reference',
        'issued_by',
        'pdf_path',
        'email_delivery_status',
        'resend_count',
        'last_error',
        'issued_at',
        'email_sent_at',
        'email_opened_at',
    ];

    protected $casts = [
        'certification_registration_id' => 'integer',
        'resend_count' => 'integer',
        'issued_at' => 'datetime',
        'email_sent_at' => 'datetime',
        'email_opened_at' => 'datetime',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(CertificationRegistration::class, 'certification_registration_id');
    }
}
