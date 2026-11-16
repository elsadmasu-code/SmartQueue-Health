<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->string('mfr_code', 64)->nullable()->index(); // Ethiopian Master Facility Register code
            $table->string('name');
            $table->string('name_amharic')->nullable();
            $table->string('type'); // Specialized Hospital, General Hospital, Primary Hospital, Health Center, Clinic
            $table->string('tier')->default('tertiary'); // tertiary, secondary, primary
            $table->string('region')->default('Addis Ababa');
            $table->string('city')->default('Addis Ababa');
            $table->string('subcity_woreda')->nullable();
            $table->string('kebele')->nullable();
            $table->string('phone')->nullable();
            $table->string('email')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('offline_sync_enabled')->default(true);
            $table->json('settings')->nullable();
            $table->timestamps();
        });

        Schema::create('departments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('facilities')->cascadeOnDelete();
            $table->string('name');
            $table->string('name_amharic')->nullable();
            $table->string('code', 32); // OPD, EMERG, LAB, PHARM, RAD, MAT, PED, SURG, DENT, INPAT
            $table->string('location_floor_or_room')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('facilities')->cascadeOnDelete();
            $table->foreignId('department_id')->nullable()->constrained('departments')->nullOnDelete();
            $table->unsignedBigInteger('user_id')->nullable()->index();
            $table->string('employee_code', 32)->unique();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('role'); // doctor, nurse, pharmacist, lab_tech, radiologist, cashier, receptionist, admin
            $table->string('specialization')->nullable();
            $table->string('license_number')->nullable();
            $table->string('phone')->nullable();
            $table->string('room_number')->nullable();
            $table->string('shift')->default('morning'); // morning, afternoon, night, full_day
            $table->boolean('is_on_duty')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
        Schema::dropIfExists('departments');
        Schema::dropIfExists('facilities');
    }
};
