<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('medications', function (Blueprint $table) {
            $table->id();
            $table->string('code', 32)->unique(); // e.g. MED-AMOX-500
            $table->string('generic_name');
            $table->string('brand_name')->nullable();
            $table->string('category'); // Antibiotics, Analgesics, Antihypertensives, Antidiabetics, Antimalarials, etc.
            $table->string('dosage_form'); // Tablet, Capsule, Syrup, Injection, Ointment, Inhaler
            $table->string('strength'); // 500mg, 250mg/5ml, 10mg
            $table->decimal('unit_price', 10, 2)->default(0.00); // ETB
            $table->integer('reorder_level')->default(50);
            $table->boolean('is_controlled')->default(false);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('stock_batches', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('facilities')->cascadeOnDelete();
            $table->foreignId('medication_id')->constrained('medications')->cascadeOnDelete();
            $table->string('batch_number', 64);
            $table->integer('quantity_in_stock')->default(0);
            $table->date('expiry_date')->index();
            $table->decimal('unit_price', 10, 2)->default(0.00);
            $table->string('supplier')->nullable();
            $table->timestamps();
        });

        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('facilities')->cascadeOnDelete();
            $table->foreignId('encounter_id')->nullable()->constrained('encounters')->nullOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('doctor_id')->constrained('employees')->cascadeOnDelete();
            $table->string('prescription_code', 32)->unique();
            $table->string('diagnosis')->nullable();
            $table->string('status')->default('pending'); // pending, dispensed, partially_dispensed, cancelled
            $table->text('notes')->nullable();
            $table->timestamps();
        });

        Schema::create('prescription_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('prescription_id')->constrained('prescriptions')->cascadeOnDelete();
            $table->foreignId('medication_id')->nullable()->constrained('medications')->nullOnDelete();
            $table->string('medication_name');
            $table->string('dosage'); // e.g. 1 tablet, 500mg
            $table->string('frequency'); // TID, BID, QD, Every 8 hours
            $table->integer('duration_days')->default(5);
            $table->integer('quantity')->default(1);
            $table->text('instructions')->nullable(); // e.g. Take after meals with full glass of water
            $table->boolean('is_dispensed')->default(false);
            $table->integer('dispensed_quantity')->default(0);
            $table->timestamps();
        });

        Schema::create('dispensations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('facility_id')->constrained('facilities')->cascadeOnDelete();
            $table->foreignId('prescription_id')->constrained('prescriptions')->cascadeOnDelete();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('pharmacist_id')->constrained('employees')->cascadeOnDelete();
            $table->decimal('total_amount', 10, 2)->default(0.00);
            $table->text('counseling_notes')->nullable();
            $table->timestamp('dispensed_at')->useCurrent();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispensations');
        Schema::dropIfExists('prescription_items');
        Schema::dropIfExists('prescriptions');
        Schema::dropIfExists('stock_batches');
        Schema::dropIfExists('medications');
    }
};
