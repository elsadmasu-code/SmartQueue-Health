<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('facilities')->cascadeOnDelete();
            $table->string('name'); // e.g. Male Medical Ward, Female Surgical Ward, Pediatric Ward, ICU, Maternity Ward
            $table->string('code', 32);
            $table->string('floor_location')->nullable();
            $table->integer('total_beds')->default(10);
            $table->string('gender_allowed')->default('all'); // all, male, female, pediatric
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('beds', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ward_id')->constrained('wards')->cascadeOnDelete();
            $table->string('bed_number', 32); // e.g. W1-B01
            $table->decimal('daily_rate', 10, 2)->default(200.00); // ETB / night
            $table->string('status')->default('available'); // available, occupied, maintenance, reserved
            $table->timestamps();
        });

        Schema::create('admissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('facilities')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('encounter_id')->nullable()->constrained('encounters')->nullOnDelete();
            $table->foreignId('bed_id')->constrained('beds')->cascadeOnDelete();
            $table->foreignId('admitting_doctor_id')->constrained('employees')->cascadeOnDelete();
            $table->timestamp('admission_date')->useCurrent();
            $table->timestamp('discharge_date')->nullable();
            $table->string('admission_diagnosis');
            $table->string('status')->default('admitted'); // admitted, transferred, discharged
            $table->text('discharge_condition')->nullable();
            $table->text('discharge_summary')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admissions');
        Schema::dropIfExists('beds');
        Schema::dropIfExists('wards');
    }
};
