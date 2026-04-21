<?php

use App\Http\Controllers\Api\ArticleController;
use App\Http\Controllers\Api\AdminCertificateController;
use App\Http\Controllers\Api\AdminDashboardController;
use App\Http\Controllers\Api\AdminAuthController;
use App\Http\Controllers\Api\AdminLspPartnerController;
use App\Http\Controllers\Api\AdminRegistrationController;
use App\Http\Controllers\Api\ContactMessageController;
use App\Http\Controllers\Api\FaqController;
use App\Http\Controllers\Api\GalleryItemController;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\ImpactStatController;
use App\Http\Controllers\Api\InstructorController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\NewsletterSubscriberController;
use App\Http\Controllers\Api\ProgramController;
use App\Http\Controllers\Api\TestimonialController;
use Illuminate\Support\Facades\Route;

Route::get('/home', [HomeController::class, 'index']);

Route::apiResource('programs', ProgramController::class);
Route::apiResource('instructors', InstructorController::class);
Route::apiResource('testimonials', TestimonialController::class);
Route::get('articles', [ArticleController::class, 'index']);
Route::get('articles/{article}', [ArticleController::class, 'show']);
Route::apiResource('gallery', GalleryItemController::class);
Route::get('faqs', [FaqController::class, 'index']);
Route::get('faqs/{faq}', [FaqController::class, 'show']);
Route::apiResource('impact-stats', ImpactStatController::class);

Route::post('contact-messages', [ContactMessageController::class, 'store']);
Route::post('leads', [LeadController::class, 'store']);
Route::post('newsletter', [NewsletterSubscriberController::class, 'store']);

Route::prefix('admin')->group(function () {
    Route::post('login', [AdminAuthController::class, 'login']);

    Route::middleware('admin.api')->group(function () {
        Route::get('session', [AdminAuthController::class, 'me']);
        Route::post('logout', [AdminAuthController::class, 'logout']);

        Route::get('dashboard', [AdminDashboardController::class, 'index']);

        Route::get('articles', [ArticleController::class, 'index']);
        Route::post('articles', [ArticleController::class, 'store']);
        Route::match(['put', 'patch'], 'articles/{article}', [ArticleController::class, 'update']);
        Route::delete('articles/{article}', [ArticleController::class, 'destroy']);

        Route::get('faqs', [FaqController::class, 'index']);
        Route::post('faqs', [FaqController::class, 'store']);
        Route::match(['put', 'patch'], 'faqs/{faq}', [FaqController::class, 'update']);
        Route::delete('faqs/{faq}', [FaqController::class, 'destroy']);

        Route::get('lsp-partners', [AdminLspPartnerController::class, 'index']);
        Route::post('lsp-partners', [AdminLspPartnerController::class, 'store']);
        Route::get('lsp-partners/{lspPartner}', [AdminLspPartnerController::class, 'show']);
        Route::match(['put', 'patch'], 'lsp-partners/{lspPartner}', [AdminLspPartnerController::class, 'update']);
        Route::delete('lsp-partners/{lspPartner}', [AdminLspPartnerController::class, 'destroy']);
        Route::post('lsp-partners/{id}/restore', [AdminLspPartnerController::class, 'restore']);

        Route::get('registrations', [AdminRegistrationController::class, 'index']);
        Route::post('registrations', [AdminRegistrationController::class, 'store']);
        Route::get('registrations/export', [AdminRegistrationController::class, 'export']);
        Route::get('registrations/{registration}', [AdminRegistrationController::class, 'show']);
        Route::match(['put', 'patch'], 'registrations/{registration}', [AdminRegistrationController::class, 'update']);
        Route::post('registrations/{registration}/status', [AdminRegistrationController::class, 'updateStatus']);
        Route::post('registrations/{registration}/documents', [AdminRegistrationController::class, 'storeDocument']);
        Route::post(
            'registrations/{registration}/documents/{document}/verify',
            [AdminRegistrationController::class, 'verifyDocument']
        );
        Route::post('registrations/{registration}/resend-confirmation', [AdminRegistrationController::class, 'resendConfirmation']);

        Route::get('certificates', [AdminCertificateController::class, 'index']);
        Route::get('certificates/{certificate}', [AdminCertificateController::class, 'show']);
        Route::post('registrations/{registration}/issue-certificate', [AdminCertificateController::class, 'issue']);
        Route::post('certificates/{certificate}/resend', [AdminCertificateController::class, 'resend']);
        Route::post('certificates/{certificate}/mark-opened', [AdminCertificateController::class, 'markOpened']);
    });
});
