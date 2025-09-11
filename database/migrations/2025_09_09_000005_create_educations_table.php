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
        Schema::create('educations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('board');
            $table->string('school');
            $table->string('specialization');
            $table->string('percentage');
            $table->date('from_date');
            $table->date('to_date');
            $table->string('mode_of_education');
            $table->string('certificate_path');
            $table->string('certificate_preview_url')->nullable();
            $table->boolean('is_highest')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('educations');
    }
};
