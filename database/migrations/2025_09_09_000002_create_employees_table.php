<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('fathers_name')->nullable();
            $table->date('dob');
            $table->string('gender');
            $table->string('marital_status');
            $table->string('nationality');
            $table->string('place_of_birth');
            $table->string('email')->unique();
            $table->string('mobile')->unique();
            $table->string('photo_path')->nullable();
            $table->string('blood_group')->nullable();
            $table->integer('status')->default(0);
            $table->integer('offer_letter_status')->default(0);
            $table->integer('division')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
