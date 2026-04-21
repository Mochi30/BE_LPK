<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NotificationLog extends Model
{
    protected $fillable = [
        'certification_registration_id',
        'recipient_email',
        'notification_type',
        'subject',
        'body',
        'status',
        'meta',
        'sent_at',
    ];

    protected $casts = [
        'certification_registration_id' => 'integer',
        'meta' => 'array',
        'sent_at' => 'datetime',
    ];

    public function registration(): BelongsTo
    {
        return $this->belongsTo(CertificationRegistration::class, 'certification_registration_id');
    }
}
