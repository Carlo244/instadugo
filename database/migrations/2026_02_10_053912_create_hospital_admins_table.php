<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hospital_admins', function (Blueprint $table) {
            $table->id();
            $table->string('hospital_name');
            $table->string('email')->unique();
            $table->string('password');
            $table->string('contact');
            $table->text('address');
            $table->unsignedTinyInteger('phlebotomist_count')->default(1);
            $table->rememberToken();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hospital_admins');
    }
};
