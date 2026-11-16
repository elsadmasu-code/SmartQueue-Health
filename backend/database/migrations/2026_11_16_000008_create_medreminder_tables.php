<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medication_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('prescription_item_id')->nullable()->constrained('prescription_items')->nullOnDelete();
            $table->string('medicine_name');
            $table->string('dosage'); // e.g. "1 Tablet (500mg)"
            $table->string('scheduled_time', 16); // e.g. "08:00", "14:00", "20:00"
            $table->string('frequency'); // "Every 8 hours", "Once daily morning"
            $table->date('start_date');
            $table->date('end_date')->nullable();
            $table->text('instructions')->nullable();
            $table->boolean('sms_reminder_enabled')->default(true);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('medication_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('medication_schedule_id')->constrained('medication_schedules')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->timestamp('scheduled_at');
            $table->timestamp('taken_at')->nullable();
            $table->string('status')->default('missed'); // taken, missed, skipped, delayed
            $table->text('patient_notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('medication_logs');
        Schema::dropIfExists('medication_schedules');
    }
};
