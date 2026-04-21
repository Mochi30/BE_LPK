<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class CertificationRegistration extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'registration_number',
        'lsp_partner_id',
        'participant_name',
        'email',
        'phone',
        'address',
        'scheme_name',
        'preferred_schedule',
        'notes',
        'status',
        'status_notes',
        'verified_at',
        'approved_at',
        'last_notification_sent_at',
    ];

    protected $casts = [
        'lsp_partner_id' => 'integer',
        'preferred_schedule' => 'date',
        'verified_at' => 'datetime',
        'approved_at' => 'datetime',
        'last_notification_sent_at' => 'datetime',
    ];

    public function lspPartner(): BelongsTo
    {
        return $this->belongsTo(LspPartner::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(RegistrationDocument::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    public function certificate(): HasOne
    {
        return $this->hasOne(Certificate::class);
    }
}
