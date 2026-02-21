<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('blood_requests', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->enum('blood_type', ['A+', 'A-', 'B+', 'B-', 'AB+', 'AB-', 'O+', 'O-']);
            $table->integer('quantity');
            $table->enum('urgency', ['Emergency', 'High', 'Normal'])->default('Normal');
            $table->foreignId('hospital_admin_id')->nullable()->constrained('hospital_admins')->nullOnDelete();
            $table->date('date_needed'); // This is required!
            $table->text('reason'); // ADD THIS LINE
            $table->enum('status', ['pending', 'approved', 'fulfilled', 'cancelled'])->default('pending');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('blood_requests');
    }
};
