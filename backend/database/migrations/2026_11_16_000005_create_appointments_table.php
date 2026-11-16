<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('facilities')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->date('appointment_date')->index();
            $table->string('time_slot', 32); // e.g. 09:00 - 09:30
            $table->string('reason')->nullable();
            $table->string('status')->default('scheduled'); // scheduled, confirmed, checked_in, completed, cancelled, rescheduled
            $table->boolean('is_teleconsult')->default(false);
            $table->boolean('sms_reminder_sent')->default(false);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('appointments');
    }
};
