<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blood_requests', function (Blueprint $table) {
            $table->bigIncrements('id');

            // The person asking for blood
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();

            // NEW: The specific donor being requested (NULL if it's a public/general request)
            $table->foreignId('receiver_id')->nullable()->constrained('users')->nullOnDelete();

            $table->enum('blood_type', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']);
            $table->integer('quantity');
            $table->enum('urgency', ['Emergency', 'High', 'Normal'])->default('Normal');

            $table->foreignId('hospital_admin_id')->nullable()->constrained('hospital_admins')->nullOnDelete();

            $table->date('date_needed');
            $table->text('reason');

            // Updated statuses to handle the "Invitation" flow
            $table->enum('status', ['pending', 'accepted', 'declined', 'fulfilled', 'cancelled'])->default('pending');

            $table->timestamps();

            $table->index('hospital_admin_id', 'idx_blood_requests_hospital');
            $table->index('status', 'idx_blood_requests_status');
            $table->index('urgency', 'idx_blood_requests_urgency');
            $table->index('blood_type', 'idx_blood_requests_blood_type');
            $table->index(['hospital_admin_id', 'status', 'urgency'], 'idx_blood_requests_hospital_status_urgency');
            $table->index('created_at', 'idx_blood_requests_created_at');
            $table->index('date_needed', 'idx_blood_requests_date_needed');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blood_requests');
    }
};
