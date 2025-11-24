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
        Schema::create('salary_components', function (Blueprint $table) {
            $table->id();
            $table->float('basic_percentage')->default(0);
            $table->float('da_percentage')->default(0);
            $table->float('hra_percentage')->default(0);
            $table->float('travel_allowance_percentage')->default(0);
            $table->float('communication_allowance_threshold')->default(0);
            $table->float('communication_allowance_amount')->default(0);
            $table->float('research_allowance_threshold')->default(0);
            $table->float('research_allowance_amount')->default(0);
            $table->float('insurance_internal')->default(0);
            $table->float('insurance_external')->default(0);
            $table->float('employer_pf_annual')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('salary_components');
    }
};
