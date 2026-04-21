<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CertificationRegistration;
use App\Models\NotificationLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCertificateController extends Controller
{
    public function index(Request $request)
    {
        $query = Certificate::query()
            ->with(['registration:id,registration_number,participant_name,email,scheme_name'])
            ->orderByDesc('created_at');

        if ($status = $request->string('approval_status')->toString()) {
            $query->where('approval_status', $status);
        }

        if ($delivery = $request->string('email_delivery_status')->toString()) {
            $query->where('email_delivery_status', $delivery);
        }

        return $query->get();
    }

    public function show(Certificate $certificate)
    {
        return $certificate->load('registration.documents');
    }

    public function issue(Request $request, CertificationRegistration $registration)
    {
        if ($registration->status !== 'approved') {
            return response()->json([
                'message' => 'Sertifikat hanya dapat diterbitkan untuk pendaftaran yang sudah berstatus approved.',
            ], 422);
        }

        $data = $request->validate([
            'certificate_number' => ['nullable', 'string', 'max:255', 'unique:certificates,certificate_number'],
            'approval_reference' => ['required', 'string', 'max:255'],
            'issued_by' => ['nullable', 'string', 'max:255'],
            'pdf_path' => ['nullable', 'string', 'max:255'],
        ]);

        $existingCertificate = Certificate::query()
            ->where('certification_registration_id', $registration->id)
            ->first();

        $certificate = Certificate::updateOrCreate(
            ['certification_registration_id' => $registration->id],
            [
                'certificate_number' => $data['certificate_number']
                    ?? $existingCertificate?->certificate_number
                    ?? $this->nextCertificateNumber(),
                'approval_status' => 'issued',
                'approval_reference' => $data['approval_reference'],
                'issued_by' => $data['issued_by'] ?? 'Admin Konten & Operasional',
                'pdf_path' => $data['pdf_path'] ?? null,
                'issued_at' => now(),
            ]
        );

        return response()->json($certificate->fresh()->load('registration'), 201);
    }

    public function resend(Certificate $certificate)
    {
        $certificate->increment('resend_count');
        $certificate->update([
            'email_delivery_status' => 'sent',
            'email_sent_at' => now(),
            'last_error' => null,
        ]);

        $notification = NotificationLog::create([
            'certification_registration_id' => $certificate->certification_registration_id,
            'recipient_email' => $certificate->registration->email,
            'notification_type' => 'certificate_resend',
            'subject' => 'Pengiriman ulang sertifikat',
            'body' => 'Sertifikat Anda telah dikirim ulang oleh admin.',
            'status' => 'sent',
            'meta' => ['certificate_number' => $certificate->certificate_number],
            'sent_at' => now(),
        ]);

        return response()->json([
            'message' => 'Pengiriman ulang sertifikat berhasil dicatat.',
            'certificate' => $certificate->fresh(),
            'notification' => $notification,
        ]);
    }

    public function markOpened(Certificate $certificate)
    {
        $certificate->update([
            'email_delivery_status' => 'opened',
            'email_opened_at' => now(),
        ]);

        return $certificate;
    }

    protected function nextCertificateNumber(): string
    {
        do {
            $number = 'CERT-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (Certificate::query()->where('certificate_number', $number)->exists());

        return $number;
    }
}
