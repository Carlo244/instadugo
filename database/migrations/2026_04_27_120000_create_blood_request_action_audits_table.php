<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blood_request_action_audits', function (Blueprint $table) {
            $table->id();
            $table->foreignId('blood_request_id')->constrained('blood_requests')->cascadeOnDelete();
            $table->foreignId('hospital_admin_id')->nullable()->constrained('hospital_admins')->nullOnDelete();
            $table->string('action');
            $table->string('from_status')->nullable();
            $table->string('to_status')->nullable();
            $table->string('from_urgency')->nullable();
            $table->string('to_urgency')->nullable();
            $table->json('meta')->nullable();
            $table->timestamps();

            $table->index(['blood_request_id', 'created_at'], 'idx_audits_request_created');
            $table->index('action', 'idx_audits_action');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blood_request_action_audits');
    }
};
