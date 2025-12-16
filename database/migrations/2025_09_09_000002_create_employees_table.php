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
            $table->foreignId('department_id');
            $table->foreignId('designation_id')->nullable();
            $table->integer('office_id')->nullable();
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->string('last_name');
            $table->string('fathers_name')->nullable();
            $table->date('dob')->nullable();
            $table->string('gender')->nullable();
            $table->string('marital_status')->nullable();
            $table->integer('nationality')->nullable();
            $table->string('place_of_birth')->nullable();
            $table->string('email')->unique();
            $table->string('password');
            $table->string('mobile')->unique();
            $table->string('photo_path')->nullable();
            $table->string('blood_group')->nullable();
            $table->string('joining_date')->nullable(); //Kept as string after insistance from the HR team.
            $table->integer('status')->default(0);
            $table->integer('offer_letter_status')->default(0);
            $table->integer('division')->default(0);
            $table->integer('category')->default(0);
            $table->boolean('is_verified')->default(0);
            $table->boolean('is_pre_joining_completed')->default(0);
            $table->boolean('is_joining_date_update_approved')->nullable()->default(null);
            $table->date('updated_joining_date')->nullable();
            $table->boolean('is_open')->default(0);
            $table->integer('buddy_id')->nullable();
            $table->integer('poc_1_id')->nullable();
            $table->integer('poc_2_id')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('employees');

        Schema::enableForeignKeyConstraints();
    }
};
