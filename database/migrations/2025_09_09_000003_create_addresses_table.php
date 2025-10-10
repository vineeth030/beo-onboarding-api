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
        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->string('line1');
            $table->string('line2')->nullable();
            $table->string('line3')->nullable();
            $table->string('landmark')->nullable();
            $table->integer('country')->nullable();
            $table->string('state')->nullable();
            $table->string('city')->nullable();
            $table->string('pin')->nullable();
            $table->string('duration_of_stay')->nullable();
            $table->enum('type', ['current', 'permanent']);
            $table->boolean('is_present_address_same_as_current')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('addresses');
    }
};
