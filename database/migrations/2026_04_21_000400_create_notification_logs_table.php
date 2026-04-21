<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('certification_registration_id')
                ->nullable()
                ->constrained('certification_registrations')
                ->nullOnDelete();
            $table->string('recipient_email');
            $table->string('notification_type');
            $table->string('subject');
            $table->text('body')->nullable();
            $table->string('status')->default('queued');
            $table->json('meta')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
