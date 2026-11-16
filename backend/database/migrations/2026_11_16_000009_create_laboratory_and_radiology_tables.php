<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('lab_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('facilities')->cascadeOnDelete();
            $table->foreignId('encounter_id')->nullable()->constrained('encounters')->nullOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('employees')->cascadeOnDelete();
            $table->string('order_number', 32)->unique();
            $table->string('test_type'); // e.g. Complete Blood Count (CBC), Malaria Rapid Test, Urinalysis, Lipid Panel
            $table->string('priority')->default('normal'); // normal, urgent, stat
            $table->string('status')->default('ordered'); // ordered, sample_collected, in_progress, completed, cancelled
            $table->string('specimen_type')->nullable(); // Whole blood, Serum, Urine, Sputum, Stool
            $table->string('specimen_barcode', 64)->nullable()->unique();
            $table->timestamp('collected_at')->nullable();
            $table->text('clinical_notes')->nullable();
            $table->timestamps();
        });

        Schema::create('lab_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('lab_order_id')->constrained('lab_orders')->cascadeOnDelete();
            $table->string('test_name'); // e.g. Hemoglobin, WBC, Platelets, Fasting Blood Sugar
            $table->string('result_value');
            $table->string('unit')->nullable(); // g/dL, 10^3/uL, mg/dL
            $table->string('reference_range')->nullable(); // e.g. 12.0 - 16.0 g/dL
            $table->string('flag')->default('normal'); // normal, low, high, critical
            $table->foreignId('technician_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->foreignId('verified_by_id')->nullable()->constrained('employees')->nullOnDelete();
            $table->timestamp('verified_at')->nullable();
            $table->text('comments')->nullable();
            $table->timestamps();
        });

        Schema::create('imaging_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('facilities')->cascadeOnDelete();
            $table->foreignId('encounter_id')->nullable()->constrained('encounters')->nullOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('employees')->cascadeOnDelete();
            $table->string('order_number', 32)->unique();
            $table->string('modality'); // X-Ray, Ultrasound, CT Scan, MRI, ECG
            $table->string('body_part'); // Chest, Abdomen, Pelvis, Brain, Spine
            $table->text('clinical_indication')->nullable();
            $table->string('status')->default('ordered'); // ordered, scheduled, completed, cancelled
            $table->timestamps();
        });

        Schema::create('imaging_reports', function (Blueprint $table) {
            $table->id();
            $table->foreignId('imaging_order_id')->constrained('imaging_orders')->cascadeOnDelete();
            $table->foreignId('radiologist_id')->constrained('employees')->cascadeOnDelete();
            $table->text('findings');
            $table->text('impression');
            $table->text('recommendations')->nullable();
            $table->string('report_file_path')->nullable();
            $table->boolean('is_verified')->default(true);
            $table->timestamp('verified_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('imaging_reports');
        Schema::dropIfExists('imaging_orders');
        Schema::dropIfExists('lab_results');
        Schema::dropIfExists('lab_orders');
    }
};
