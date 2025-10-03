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
        Schema::create('employments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('company_name');
            $table->string('employee_id_at_company')->nullable();
            $table->string('designation');
            $table->string('location');
            $table->string('mode_of_employment');
            $table->date('start_date');
            $table->date('last_working_date')->nullable();
            $table->string('resignation_acceptance_letter_file')->nullable();
            $table->string('resignation_acceptance_letter_preview_url')->nullable();
            $table->string('experience_letter_file')->nullable();
            $table->string('experience_letter_preview_url')->nullable();
            $table->boolean('is_current_org')->default(false);
            $table->boolean('is_serving_notice_period')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employments');
    }
};
