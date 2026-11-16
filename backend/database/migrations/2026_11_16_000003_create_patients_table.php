<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->foreignId('registered_facility_id')->nullable()->constrained('facilities')->nullOnDelete();
            $table->string('mrn', 64)->unique(); // Medical Record Number e.g. SQH-AA-2026-0001
            $table->string('national_id', 64)->nullable()->index(); // Ethiopian Fayda / National ID
            $table->string('cbhi_number', 64)->nullable()->index(); // Community Based Health Insurance
            $table->string('first_name');
            $table->string('father_name');
            $table->string('grandfather_name')->nullable();
            $table->string('amharic_full_name')->nullable();
            $table->date('date_of_birth')->nullable();
            $table->integer('age_years')->nullable();
            $table->string('gender', 10); // male, female
            $table->string('blood_group', 5)->nullable(); // A+, A-, B+, B-, AB+, AB-, O+, O-
            $table->string('phone_number', 32)->nullable()->index();
            $table->string('email')->nullable();
            $table->string('region')->default('Addis Ababa');
            $table->string('city')->default('Addis Ababa');
            $table->string('subcity_woreda')->nullable();
            $table->string('kebele')->nullable();
            $table->string('house_number')->nullable();
            $table->string('emergency_contact_name')->nullable();
            $table->string('emergency_contact_phone')->nullable();
            $table->string('emergency_contact_relation')->nullable();
            $table->json('allergies')->nullable();
            $table->json('chronic_conditions')->nullable();
            $table->string('preferred_language')->default('am'); // am, en, or
            $table->boolean('sms_notifications_enabled')->default(true);
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
