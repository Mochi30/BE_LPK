<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\CertificationRegistration;
use App\Models\LspPartner;
use App\Models\NotificationLog;
use App\Models\RegistrationDocument;

class AdminDashboardController extends Controller
{
    public function index()
    {
        return [
            'lsp_partners' => [
                'total' => LspPartner::count(),
                'active' => LspPartner::query()->where('is_active', true)->count(),
                'archived' => LspPartner::onlyTrashed()->count(),
            ],
            'registrations' => [
                'total' => CertificationRegistration::count(),
                'pending' => CertificationRegistration::query()->where('status', 'pending')->count(),
                'verified' => CertificationRegistration::query()->where('status', 'diverifikasi')->count(),
                'approved' => CertificationRegistration::query()->where('status', 'approved')->count(),
                'rejected' => CertificationRegistration::query()->where('status', 'rejected')->count(),
            ],
            'documents' => [
                'pending_review' => RegistrationDocument::query()->where('verification_status', 'pending')->count(),
                'verified' => RegistrationDocument::query()->where('verification_status', 'verified')->count(),
                'rejected' => RegistrationDocument::query()->where('verification_status', 'rejected')->count(),
            ],
            'certificates' => [
                'total' => Certificate::count(),
                'pending_approval' => Certificate::query()->where('approval_status', 'pending_approval')->count(),
                'issued' => Certificate::query()->where('approval_status', 'issued')->count(),
                'email_sent' => Certificate::query()->where('email_delivery_status', 'sent')->count(),
                'email_failed' => Certificate::query()->where('email_delivery_status', 'failed')->count(),
            ],
            'notifications' => [
                'sent' => NotificationLog::query()->where('status', 'sent')->count(),
                'failed' => NotificationLog::query()->where('status', 'failed')->count(),
            ],
        ];
    }
}
