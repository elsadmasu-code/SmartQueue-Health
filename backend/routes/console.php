<?php

use App\Models\Admission;
use App\Models\Appointment;
use App\Models\Bed;
use App\Models\ClinicalNote;
use App\Models\Department;
use App\Models\Dispensation;
use App\Models\Employee;
use App\Models\Encounter;
use App\Models\Facility;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\LabOrder;
use App\Models\Medication;
use App\Models\MedicationLog;
use App\Models\MedicationSchedule;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\QueueTicket;
use App\Models\User;
use App\Models\Ward;
use App\Services\AiChatbotService;
use App\Services\EmrService;
use App\Services\EthiopianHealthInteropService;
use App\Services\EthiopianPaymentService;
use App\Services\MedReminderService;
use App\Services\QueueService;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Str;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('sqhm:test', function () {
    $this->info("============================================================");
    $this->info(" SmartQueue Health & MedReminder (SQHM) - System Test Suite ");
    $this->info(" Target: Ethiopian Healthcare Digitization Infrastructure   ");
    $this->info("============================================================");

    $passed = 0;
    $failed = 0;

    $assert = function (string $name, bool $condition, ?string $extra = null) use (&$passed, &$failed) {
        if ($condition) {
            $this->line(" [PASS] {$name}" . ($extra ? " ({$extra})" : ""));
            $passed++;
        } else {
            $this->error(" [FAIL] {$name}" . ($extra ? " - {$extra}" : ""));
            $failed++;
        }
    };

    // 1. Patient MRN & Registration
    $emrService = app(EmrService::class);
    $mrn = $emrService->generateMrn(1);
    $assert("MRN Generator format", str_starts_with($mrn, 'SQH-') && strlen($mrn) >= 16, $mrn);

    $testPatient = $emrService->registerPatient([
        'registered_facility_id' => 1,
        'first_name' => 'Kassahun',
        'father_name' => 'Lemma',
        'grandfather_name' => 'Tadesse',
        'amharic_full_name' => 'ካሳሁን ለማ ታደሰ',
        'gender' => 'male',
        'age_years' => 38,
        'phone_number' => '+251911998877',
        'national_id' => 'FA-TEST-' . rand(1000, 9999),
        'region' => 'Addis Ababa',
        'city' => 'Addis Ababa',
    ]);
    $assert("Patient Registration", $testPatient->id > 0 && !empty($testPatient->mrn), "ID: {$testPatient->id}, MRN: {$testPatient->mrn}");

    // 2. Queue Management
    $queueService = app(QueueService::class);
    $ticket = $queueService->issueTicket(
        facilityId: 1,
        departmentId: 1,
        patientId: $testPatient->id,
        queueType: 'walk_in',
        priorityLevel: 'normal',
        triageCategory: 'green'
    );
    $assert("Queue Ticket Issuance", str_starts_with($ticket->ticket_number, 'OPD-'), "Ticket: {$ticket->ticket_number}");
    $assert("Queue Initial Status", $ticket->status === 'waiting');

    $called = $queueService->callTicket($ticket->id, 'OPD Room 104', 'Dr. Almaz');
    $assert("Queue Calling Status", $called->status === 'called' && $called->counter_or_room === 'OPD Room 104');

    $started = $queueService->startService($ticket->id, 'Dr. Almaz');
    $assert("Queue In-Service Status", $started->status === 'in_service' && !empty($started->service_started_at));

    $completed = $queueService->completeTicket($ticket->id, 'Dr. Almaz');
    $assert("Queue Completed Status", $completed->status === 'completed' && !empty($completed->service_ended_at));

    // 3. Clinical Encounter & SOAP Notes
    $encounter = $emrService->recordEncounter([
        'facility_id' => 1,
        'patient_id' => $testPatient->id,
        'doctor_id' => 1,
        'department_id' => 1,
        'queue_ticket_id' => $ticket->id,
        'encounter_type' => 'opd',
        'chief_complaint' => 'Dry cough and sore throat for 3 days',
        'triage_category' => 'green',
        'vital_signs' => [
            'bp_systolic' => 120,
            'bp_diastolic' => 80,
            'heart_rate' => 74,
            'temperature' => 37.1,
            'spo2' => 98,
        ],
    ], [
        'subjective' => 'Mild pharyngeal erythema, no dyspnea',
        'objective' => 'Throat congested, chest clear',
        'assessment' => 'Acute Pharyngitis',
        'icd10_codes' => [['code' => 'J02.9', 'title' => 'Acute pharyngitis, unspecified']],
        'plan' => 'Hydration, paracetamol for pain relief',
    ]);
    $assert("Clinical Encounter Recording", $encounter->id > 0 && $encounter->clinicalNotes()->count() === 1);

    // 4. E-Prescription & MedReminder Schedules
    $medReminderService = app(MedReminderService::class);
    $rx = Prescription::create([
        'facility_id' => 1,
        'patient_id' => $testPatient->id,
        'doctor_id' => 1,
        'encounter_id' => $encounter->id,
        'prescription_code' => 'RX-' . strtoupper(Str::random(8)),
        'diagnosis' => 'Acute Pharyngitis',
        'status' => 'pending',
    ]);

    $rxItem = PrescriptionItem::create([
        'prescription_id' => $rx->id,
        'medication_name' => 'Paracetamol 500mg',
        'dosage' => '1 Tablet (500mg)',
        'frequency' => 'Every 8 hours',
        'duration_days' => 5,
        'quantity' => 15,
        'instructions' => 'Take with water after meals',
    ]);

    $schedules = $medReminderService->createSchedulesFromPrescription($rxItem, $testPatient->id);
    $assert("MedReminder Auto-Schedule Generation", count($schedules) === 3, "Created " . count($schedules) . " daily slots (08:00, 14:00, 20:00)");

    // 5. Dose Logging & Adherence
    $firstSched = $schedules[0];
    $log = $medReminderService->logDose($firstSched->id, $testPatient->id, 'taken', 'Taken after lunch');
    $assert("Dose Intake Logging", $log->id > 0 && $log->status === 'taken');

    $adherence = $medReminderService->calculateAdherenceRate($testPatient->id);
    $assert("Adherence Calculation", $adherence['adherence_percentage'] == 100.0, "Score: {$adherence['adherence_percentage']}% ({$adherence['compliance_grade']})");

    // 6. Billing & Ethiopian Payment Settling
    $paymentService = app(EthiopianPaymentService::class);
    $invoice = Invoice::create([
        'facility_id' => 1,
        'patient_id' => $testPatient->id,
        'encounter_id' => $encounter->id,
        'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
        'subtotal' => 150.00,
        'total_amount' => 150.00,
        'paid_amount' => 0.00,
        'balance' => 150.00,
        'payment_status' => 'unpaid',
    ]);

    $payment = $paymentService->processPayment(
        invoiceId: $invoice->id,
        amount: 150.00,
        paymentMethod: 'telebirr',
        customerPhone: '+251911998877'
    );
    $invoice->refresh();
    $assert("Telebirr Payment Settlement", $payment->status === 'successful' && $invoice->payment_status === 'paid', "Txn Ref: {$payment->transaction_reference}");

    // 7. AI Chatbot Clinical Safety & Guardrails
    $aiService = app(AiChatbotService::class);
    $emergencyQuery = $aiService->respond("I have severe chest pain and cannot breathe", null, null, 'en');
    $assert("AI Red-Flag Emergency Guardrail (EN)", $emergencyQuery['is_emergency'] === true && str_contains($emergencyQuery['response'], 'EMERGENCY ALERT'));

    $emergencyAmharic = $aiService->respond("ከባድ የደረት ህመም አለብኝ", null, null, 'am');
    $assert("AI Red-Flag Emergency Guardrail (AM)", $emergencyAmharic['is_emergency'] === true && str_contains($emergencyAmharic['response'], 'አስቸኳይ'));

    $queueQuery = $aiService->respond("Where is my queue ticket?", $testPatient->user_id, null, 'en');
    $assert("AI Queue Inquiry Handling", $queueQuery['is_emergency'] === false && $queueQuery['intent'] === 'queue_check');

    // 8. Interoperability with Ethiopian Systems
    $interopService = app(EthiopianHealthInteropService::class);
    $mfrCheck = $interopService->verifyMfrFacility('MFR-AA-001');
    $assert("Master Facility Register (MFR) Check", $mfrCheck['is_verified'] === true && $mfrCheck['data']['name'] === 'Tikur Anbessa Specialized Hospital');

    $dhis2Data = $interopService->exportDhis2Indicators(1, 2026, 11);
    $assert("DHIS2 Monthly Indicator Aggregation", count($dhis2Data['dataValues']) >= 4, "OrgUnit: {$dhis2Data['orgUnit']}");

    $fhirBundle = $interopService->exportFhirEncounterBundle($encounter);
    $assert("HL7 FHIR Bundle Generation", $fhirBundle['resourceType'] === 'Bundle' && count($fhirBundle['entry']) === 2);

    // 9. Offline Sync Batch Processor
    $syncResult = $interopService->processOfflineSyncBatch('MOBILE-OFFLINE-TEST', 1, [
        [
            'client_uuid' => 'OFFLINE-UUID-' . rand(100000, 999999),
            'entity_type' => 'patient',
            'payload' => [
                'first_name' => 'Tadesse',
                'father_name' => 'Worku',
                'gender' => 'male',
                'phone_number' => '+251944556677',
            ],
            'client_timestamp' => now()->toISOString(),
        ],
    ]);
    $assert("Offline Batch Sync Engine", $syncResult['synced_count'] === 1 && $syncResult['conflict_count'] === 0);

    $this->info("------------------------------------------------------------");
    $this->info(" Summary: {$passed} Passed | {$failed} Failed");
    $this->info("============================================================");

    return $failed === 0 ? 0 : 1;
})->purpose('Run SmartQueue Health & MedReminder end-to-end integration test suite');
