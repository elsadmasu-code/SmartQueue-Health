<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('queue_tickets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('facilities')->cascadeOnDelete();
            $table->foreignId('department_id')->constrained('departments')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->string('ticket_number', 32)->index(); // e.g. OPD-014, LAB-005, EMG-001
            $table->string('queue_type')->default('walk_in'); // walk_in, appointment, emergency, referral, lab, pharmacy, radiology
            $table->string('priority_level')->default('normal'); // normal, urgent, emergency
            $table->string('triage_category')->default('green'); // red (immediate), yellow (urgent), green (standard), blue (non-urgent)
            $table->string('status')->default('waiting'); // waiting, called, in_service, completed, skipped, cancelled, transferred
            $table->string('counter_or_room')->nullable(); // e.g. Room 102, Counter 3
            $table->integer('estimated_wait_minutes')->default(15);
            $table->timestamp('issued_at')->useCurrent();
            $table->timestamp('called_at')->nullable();
            $table->timestamp('service_started_at')->nullable();
            $table->timestamp('service_ended_at')->nullable();
            $table->string('created_by_role')->default('receptionist');
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('queue_events', function (Blueprint $table) {
            $table->id();
            $table->foreignId('queue_ticket_id')->constrained('queue_tickets')->cascadeOnDelete();
            $table->string('from_status')->nullable();
            $table->string('to_status');
            $table->string('action_by')->nullable();
            $table->string('room_or_counter')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamp('occurred_at')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('queue_events');
        Schema::dropIfExists('queue_tickets');
    }
};
