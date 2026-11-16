<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('encounters', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('facilities')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->foreignId('queue_ticket_id')->nullable()->constrained('queue_tickets')->nullOnDelete();
            $table->string('encounter_type')->default('opd'); // opd, emergency, inpatient, follow_up
            $table->text('chief_complaint')->nullable();
            $table->string('triage_category')->default('green'); // red, yellow, green, blue
            $table->text('triage_assessment')->nullable();
            $table->json('vital_signs')->nullable(); // bp_systolic, bp_diastolic, heart_rate, temperature, respiratory_rate, spo2, weight_kg, height_cm, bmi
            $table->string('status')->default('in_progress'); // open, in_progress, completed, referred, admitted
            $table->timestamp('started_at')->useCurrent();
            $table->timestamp('ended_at')->nullable();
            $table->timestamps();
        });

        Schema::create('clinical_notes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('encounter_id')->constrained('encounters')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('employees')->cascadeOnDelete();
            $table->text('subjective')->nullable(); // History of present illness, symptoms
            $table->text('objective')->nullable();  // Physical examination findings
            $table->text('assessment')->nullable(); // Clinical assessment / provisional diagnosis
            $table->text('plan')->nullable();       // Diagnostic / treatment / counseling plan
            $table->json('icd10_codes')->nullable(); // [{"code": "A09", "title": "Infectious gastroenteritis"}]
            $table->date('follow_up_date')->nullable();
            $table->boolean('is_signed')->default(true);
            $table->timestamp('signed_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('clinical_notes');
        Schema::dropIfExists('encounters');
    }
};
