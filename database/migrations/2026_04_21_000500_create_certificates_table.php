<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('certificates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certification_registration_id')
                ->unique()
                ->constrained('certification_registrations')
                ->cascadeOnDelete();
            $table->string('certificate_number')->unique();
            $table->string('approval_status')->default('pending_approval');
            $table->string('approval_reference')->nullable();
            $table->string('issued_by')->nullable();
            $table->string('pdf_path')->nullable();
            $table->string('email_delivery_status')->default('pending');
            $table->unsignedInteger('resend_count')->default(0);
            $table->text('last_error')->nullable();
            $table->timestamp('issued_at')->nullable();
            $table->timestamp('email_sent_at')->nullable();
            $table->timestamp('email_opened_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('certificates');
    }
};
