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
        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('notice_period')->default(3);
            $table->boolean('is_family_insurance_paid_by_client')->default(false);
            $table->boolean('is_support_staff_required')->default(false);
            $table->boolean('is_outsource')->default(true);
            $table->boolean('is_single_swipe')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('departments');
    }
};
