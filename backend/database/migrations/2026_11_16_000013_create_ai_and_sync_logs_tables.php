<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chatbot_logs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('session_id', 64)->index();
            $table->string('language', 10)->default('en'); // en, am
            $table->text('user_message');
            $table->text('ai_response');
            $table->string('intent')->nullable(); // appointment_help, queue_check, med_explanation, emergency_escalation, general_faq
            $table->boolean('is_emergency_escalated')->default(false);
            $table->json('context')->nullable();
            $table->timestamps();
        });

        Schema::create('offline_sync_records', function (Blueprint $table) {
            $table->id();
            $table->string('device_id', 64)->index();
            $table->foreignId('facility_id')->constrained('facilities')->cascadeOnDelete();
            $table->string('entity_type'); // patient, vitals, queue_ticket, medication_taken
            $table->string('client_uuid', 64)->index();
            $table->unsignedBigInteger('server_record_id')->nullable();
            $table->string('sync_status')->default('synced'); // pending, synced, conflict, rejected
            $table->json('payload');
            $table->text('conflict_resolution_notes')->nullable();
            $table->timestamp('client_timestamp');
            $table->timestamp('server_synced_at')->useCurrent();
        });

        Schema::create('interoperability_records', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('facilities')->cascadeOnDelete();
            $table->string('national_system'); // MFR, DHIS2, NDHx_FHIR, eAPTS, eCHIS
            $table->string('transaction_type'); // facility_verification, dhis2_monthly_report, fhir_encounter_export
            $table->string('status')->default('success'); // success, failed, pending
            $table->json('payload')->nullable();
            $table->json('response_data')->nullable();
            $table->timestamp('synced_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('interoperability_records');
        Schema::dropIfExists('offline_sync_records');
        Schema::dropIfExists('chatbot_logs');
    }
};
