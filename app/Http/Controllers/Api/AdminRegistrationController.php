<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\CertificationRegistration;
use App\Models\NotificationLog;
use App\Models\RegistrationDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Str;

class AdminRegistrationController extends Controller
{
    public function index(Request $request)
    {
        $query = CertificationRegistration::query()
            ->with(['lspPartner:id,name', 'documents', 'certificate'])
            ->orderByDesc('created_at');

        if ($search = $request->string('search')->toString()) {
            $query->where(function ($builder) use ($search) {
                $builder
                    ->where('participant_name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('registration_number', 'like', "%{$search}%")
                    ->orWhere('scheme_name', 'like', "%{$search}%");
            });
        }

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($request->filled('lsp_partner_id')) {
            $query->where('lsp_partner_id', $request->integer('lsp_partner_id'));
        }

        return $query->get();
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'lsp_partner_id' => ['nullable', 'integer', 'exists:lsp_partners,id'],
            'participant_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'scheme_name' => ['required', 'string', 'max:255'],
            'preferred_schedule' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'status' => ['nullable', 'in:pending,diverifikasi,approved,rejected'],
            'status_notes' => ['nullable', 'string'],
            'documents' => ['nullable', 'array'],
            'documents.*.document_type' => ['required_with:documents', 'string', 'max:100'],
            'documents.*.original_name' => ['required_with:documents', 'string', 'max:255'],
            'documents.*.file_path' => ['nullable', 'string', 'max:255'],
            'documents.*.mime_type' => ['nullable', 'string', 'max:120'],
            'documents.*.file_size' => ['nullable', 'integer', 'min:0'],
        ]);

        $data['registration_number'] = $this->nextRegistrationNumber();
        $registration = CertificationRegistration::create($data);

        foreach ($request->input('documents', []) as $document) {
            $registration->documents()->create($document);
        }

        return response()->json(
            $registration->load(['documents', 'lspPartner']),
            201
        );
    }

    public function show(CertificationRegistration $registration)
    {
        return $registration->load(['lspPartner', 'documents', 'notifications', 'certificate']);
    }

    public function update(Request $request, CertificationRegistration $registration)
    {
        $data = $request->validate([
            'lsp_partner_id' => ['nullable', 'integer', 'exists:lsp_partners,id'],
            'participant_name' => ['sometimes', 'required', 'string', 'max:255'],
            'email' => ['sometimes', 'required', 'email', 'max:255'],
            'phone' => ['nullable', 'string', 'max:100'],
            'address' => ['nullable', 'string'],
            'scheme_name' => ['sometimes', 'required', 'string', 'max:255'],
            'preferred_schedule' => ['nullable', 'date'],
            'notes' => ['nullable', 'string'],
            'status_notes' => ['nullable', 'string'],
        ]);

        $registration->update($data);

        return $registration->fresh()->load(['lspPartner', 'documents', 'certificate']);
    }

    public function updateStatus(Request $request, CertificationRegistration $registration)
    {
        $data = $request->validate([
            'status' => ['required', 'in:pending,diverifikasi,approved,rejected'],
            'status_notes' => ['nullable', 'string'],
            'send_notification' => ['nullable', 'boolean'],
        ]);

        $registration->status = $data['status'];
        $registration->status_notes = $data['status_notes'] ?? $registration->status_notes;

        if ($data['status'] === 'diverifikasi' && empty($registration->verified_at)) {
            $registration->verified_at = now();
        }

        if ($data['status'] === 'approved' && empty($registration->approved_at)) {
            $registration->approved_at = now();
        }

        $registration->save();

        if ($request->boolean('send_notification')) {
            $this->createNotification(
                $registration,
                'registration_status_update',
                'Pembaruan status pendaftaran sertifikasi',
                "Status pendaftaran Anda saat ini: {$registration->status}."
            );
        }

        return $registration->fresh()->load(['documents', 'certificate']);
    }

    public function storeDocument(Request $request, CertificationRegistration $registration)
    {
        $data = $request->validate([
            'document_type' => ['required', 'string', 'max:100'],
            'original_name' => ['required', 'string', 'max:255'],
            'file_path' => ['nullable', 'string', 'max:255'],
            'mime_type' => ['nullable', 'string', 'max:120'],
            'file_size' => ['nullable', 'integer', 'min:0'],
        ]);

        $document = $registration->documents()->create($data);

        return response()->json($document, 201);
    }

    public function verifyDocument(Request $request, CertificationRegistration $registration, RegistrationDocument $document)
    {
        abort_if($document->certification_registration_id !== $registration->id, 404);

        $data = $request->validate([
            'verification_status' => ['required', 'in:pending,verified,rejected'],
            'review_notes' => ['nullable', 'string'],
        ]);

        $document->update([
            'verification_status' => $data['verification_status'],
            'review_notes' => $data['review_notes'] ?? null,
            'reviewed_at' => now(),
        ]);

        return $document->fresh();
    }

    public function resendConfirmation(CertificationRegistration $registration)
    {
        $notification = $this->createNotification(
            $registration,
            'registration_confirmation_resend',
            'Konfirmasi pendaftaran sertifikasi',
            'Konfirmasi pendaftaran Anda telah dikirim ulang oleh admin.'
        );

        return response()->json([
            'message' => 'Notifikasi konfirmasi pendaftaran berhasil dicatat untuk pengiriman ulang.',
            'data' => $notification,
        ]);
    }

    public function export(Request $request)
    {
        $registrations = $this->index($request);
        $filename = 'registrations-' . Carbon::now()->format('Ymd-His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($registrations) {
            $stream = fopen('php://output', 'w');
            fputcsv($stream, [
                'registration_number',
                'participant_name',
                'email',
                'phone',
                'scheme_name',
                'status',
                'lsp_partner',
                'created_at',
            ]);

            foreach ($registrations as $registration) {
                fputcsv($stream, [
                    $registration->registration_number,
                    $registration->participant_name,
                    $registration->email,
                    $registration->phone,
                    $registration->scheme_name,
                    $registration->status,
                    optional($registration->lspPartner)->name,
                    optional($registration->created_at)?->toDateTimeString(),
                ]);
            }

            fclose($stream);
        };

        return Response::stream($callback, 200, $headers);
    }

    protected function nextRegistrationNumber(): string
    {
        do {
            $number = 'REG-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));
        } while (CertificationRegistration::query()->where('registration_number', $number)->exists());

        return $number;
    }

    protected function createNotification(
        CertificationRegistration $registration,
        string $type,
        string $subject,
        string $body
    ): NotificationLog {
        $registration->update(['last_notification_sent_at' => now()]);

        return NotificationLog::create([
            'certification_registration_id' => $registration->id,
            'recipient_email' => $registration->email,
            'notification_type' => $type,
            'subject' => $subject,
            'body' => $body,
            'status' => 'sent',
            'meta' => ['triggered_by' => 'admin_module'],
            'sent_at' => now(),
        ]);
    }
}
