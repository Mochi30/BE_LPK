<?php

use App\Http\Controllers\Api\ArticleController;
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
Route::apiResource('articles', ArticleController::class);
Route::apiResource('gallery', GalleryItemController::class);
Route::apiResource('faqs', FaqController::class);
Route::apiResource('impact-stats', ImpactStatController::class);

Route::post('contact-messages', [ContactMessageController::class, 'store']);
Route::post('leads', [LeadController::class, 'store']);
Route::post('newsletter', [NewsletterSubscriberController::class, 'store']);
