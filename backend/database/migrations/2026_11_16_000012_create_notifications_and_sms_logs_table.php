<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notifications', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->foreignId('patient_id')->nullable()->constrained('patients')->nullOnDelete();
            $table->string('title');
            $table->text('message');
            $table->string('type'); // queue_called, appointment_reminder, medication_due, lab_ready, payment_received
            $table->string('channel')->default('app'); // app, sms, push
            $table->boolean('is_read')->default(false);
            $table->json('data')->nullable();
            $table->timestamps();
        });

        Schema::create('sms_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->nullable()->constrained('facilities')->nullOnDelete();
            $table->foreignId('patient_id')->nullable()->constrained('patients')->nullOnDelete();
            $table->string('phone_number', 32)->index();
            $table->string('command')->nullable(); // REGISTER, QUEUE, APPOINTMENT, REMINDER, HELP, CANCEL
            $table->text('message_text');
            $table->string('direction')->default('outbound'); // inbound, outbound
            $table->string('gateway')->default('ethio_telecom'); // ethio_telecom, safaricom_et, mock
            $table->string('status')->default('sent'); // sent, delivered, failed
            $table->string('delivery_reference')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_logs');
        Schema::dropIfExists('notifications');
    }
};
