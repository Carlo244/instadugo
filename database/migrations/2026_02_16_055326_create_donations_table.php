<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('hospital_admin_id')->constrained('hospital_admins')->onDelete('cascade');

            // Link to request (Keep nullable, remove unique here)
            $table->foreignId('blood_request_id')->nullable()->constrained('blood_requests')->onDelete('set null');

            $table->enum('blood_type', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']);
            $table->date('donation_date');
            $table->time('donation_time');
            $table->enum('status', ['scheduled', 'completed', 'cancelled'])->default('scheduled');
            $table->text('notes')->nullable();

            // Reminder tracking
            $table->timestamp('reminder_24_sent_at')->nullable();
            $table->timestamp('reminder_2h_sent_at')->nullable();
            $table->string('reminder_24_job_id')->nullable();
            $table->string('reminder_2h_job_id')->nullable();

            $table->index('hospital_admin_id', 'idx_donations_hospital');
            $table->index('status', 'idx_donations_status');
            $table->index('donation_date', 'idx_donations_date');
            $table->index(['hospital_admin_id', 'donation_date', 'status'], 'idx_donations_hospital_date_status');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
