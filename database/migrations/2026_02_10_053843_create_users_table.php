<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
Schema::create('users', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable(); 
    $table->string('password');
    $table->rememberToken(); 
    $table->string('contact', 15);
    $table->integer('age');
    $table->enum('sex', ['Male', 'Female']);
    $table->enum('blood_type', ['A+','A-','B+','B-','AB+','AB-','O+','O-']);
    $table->text('address');
    $table->timestamps();
});
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};

