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
        Schema::create('offers', function (Blueprint $table) {
            $table->id();
            $table->longText('email_attachment_content_for_client');
            $table->longText('email_content_for_employee');
            $table->json('client_emails')->nullable();
            $table->json('beo_emails')->nullable();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('employee_id')->constrained()->onDelete('cascade');
            $table->foreignId('department_id');
            $table->string('name')->nullable();
            $table->string('comment')->nullable();
            $table->string('sign_file_path')->nullable();
            $table->boolean('is_accepted')->default(0);
            $table->boolean('is_declined')->default(0);
            $table->string('decline_reason')->nullable();
            $table->boolean('is_revoked')->default(false);
            $table->string('revoke_reason')->nullable();
            $table->boolean('is_family_insurance_paid_by_client')->default(false);
            $table->tinyInteger('status')->default(0)->comment('Offer status');
            $table->timestamp('last_reminder_sent_at')->nullable()->comment('Tracks when last reminder email was sent for 2-day frequency');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('offers');

        Schema::enableForeignKeyConstraints();
    }
};
