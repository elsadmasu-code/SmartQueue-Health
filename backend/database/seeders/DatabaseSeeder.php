<?php

namespace Database\Seeders;

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
use App\Models\LabResult;
use App\Models\Medication;
use App\Models\MedicationLog;
use App\Models\MedicationSchedule;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\QueueEvent;
use App\Models\QueueTicket;
use App\Models\StockBatch;
use App\Models\User;
use App\Models\Ward;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $defaultPassword = Hash::make('password123');

        // 1. FACILITIES (Ethiopian Hospitals)
        $tash = Facility::create([
            'mfr_code' => 'MFR-AA-001',
            'name' => 'Tikur Anbessa Specialized Hospital',
            'name_amharic' => 'ጥቁር አንበሳ ስፔሻላይዝድ ሆስፒታል',
            'type' => 'Specialized Hospital',
            'tier' => 'tertiary',
            'region' => 'Addis Ababa',
            'city' => 'Addis Ababa',
            'subcity_woreda' => 'Lideta Subcity, Woreda 04',
            'kebele' => '04/05',
            'phone' => '+251 11 551 1211',
            'email' => 'info@tash.gov.et',
            'is_active' => true,
            'offline_sync_enabled' => true,
            'settings' => ['currency' => 'ETB', 'timezone' => 'Africa/Addis_Ababa'],
        ]);

        $stpaul = Facility::create([
            'mfr_code' => 'MFR-AA-002',
            'name' => "St. Paul's Hospital Millennium Medical College",
            'name_amharic' => 'ቅዱስ ጳውሎስ ሆስፒታል ሚሌኒየም ሜዲካል ኮሌጅ',
            'type' => 'Specialized Hospital',
            'tier' => 'tertiary',
            'region' => 'Addis Ababa',
            'city' => 'Addis Ababa',
            'subcity_woreda' => 'Gullele Subcity',
            'kebele' => '08',
            'phone' => '+251 11 275 0125',
            'email' => 'info@sphmmc.edu.et',
            'is_active' => true,
            'offline_sync_enabled' => true,
        ]);

        // 2. DEPARTMENTS FOR TASH
        $opd = Department::create([
            'facility_id' => $tash->id,
            'name' => 'Outpatient Department (OPD)',
            'name_amharic' => 'የተመላላሽ ታካሚዎች ክፍል (OPD)',
            'code' => 'OPD',
            'location_floor_or_room' => 'Building B, Ground Floor',
            'description' => 'General and specialist outpatient consultations',
        ]);

        $emerg = Department::create([
            'facility_id' => $tash->id,
            'name' => 'Emergency & Critical Triage',
            'name_amharic' => 'ድንገተኛ እና አስቸኳይ ህክምና ክፍል',
            'code' => 'EMG',
            'location_floor_or_room' => 'Emergency Wing, Gate 1',
            'description' => '24/7 Red/Yellow emergency triage',
        ]);

        $lab = Department::create([
            'facility_id' => $tash->id,
            'name' => 'Central Pathology Laboratory',
            'name_amharic' => 'ማዕከላዊ የላቦራቶሪ ምርመራ ክፍል',
            'code' => 'LAB',
            'location_floor_or_room' => 'Block C, 1st Floor',
            'description' => 'Hematology, biochemistry, microbiology, serology',
        ]);

        $pharm = Department::create([
            'facility_id' => $tash->id,
            'name' => 'Main Outpatient Pharmacy',
            'name_amharic' => 'ዋና ፋርማሲና መድሃኒት ማደያ',
            'code' => 'PHARM',
            'location_floor_or_room' => 'Main Lobby, Counter 1-6',
            'description' => 'Medication dispensing, counseling, and inventory',
        ]);

        $rad = Department::create([
            'facility_id' => $tash->id,
            'name' => 'Radiology & Medical Imaging',
            'name_amharic' => 'ራዲዮሎጂ እና የኤክስሬይ ክፍል',
            'code' => 'RAD',
            'location_floor_or_room' => 'Block A, Basement',
            'description' => 'Digital X-ray, Ultrasound, CT Scan, MRI',
        ]);

        $inpat = Department::create([
            'facility_id' => $tash->id,
            'name' => 'Inpatient Wards Management',
            'name_amharic' => 'ተኝተው የሚታከሙ ታካሚዎች ክፍል',
            'code' => 'INPAT',
            'location_floor_or_room' => 'Floors 2 to 5',
            'description' => 'Internal medicine, surgical, pediatric, maternity wards',
        ]);

        $cashierDept = Department::create([
            'facility_id' => $tash->id,
            'name' => 'Cashier & Patient Billing Accounts',
            'name_amharic' => 'የሂሳብ እና ክፍያ መቀበያ ክፍል',
            'code' => 'CASH',
            'location_floor_or_room' => 'Main Hall, Counter 1-4',
            'description' => 'Telebirr, CBE Birr, CBHI and cash settlements',
        ]);

        // 3. USERS & EMPLOYEES
        // Super Admin
        User::create([
            'name' => 'System Administrator',
            'email' => 'admin@smartqueue.et',
            'password' => $defaultPassword,
            'role' => 'super_admin',
            'facility_id' => $tash->id,
            'preferred_language' => 'en',
            'is_active' => true,
        ]);

        // Doctor User & Employee
        $doctorUser = User::create([
            'name' => 'Dr. Almaz Tefera',
            'email' => 'doctor@smartqueue.et',
            'password' => $defaultPassword,
            'role' => 'doctor',
            'facility_id' => $tash->id,
            'department_id' => $opd->id,
            'phone_number' => '+251911440011',
            'preferred_language' => 'am',
            'is_active' => true,
        ]);

        $doctorEmployee = Employee::create([
            'facility_id' => $tash->id,
            'department_id' => $opd->id,
            'user_id' => $doctorUser->id,
            'employee_code' => 'DOC-0042',
            'first_name' => 'Almaz',
            'last_name' => 'Tefera',
            'role' => 'doctor',
            'specialization' => 'Internal Medicine Specialist',
            'license_number' => 'ETH-MD-90412',
            'phone' => '+251911440011',
            'room_number' => 'OPD Room 104',
            'shift' => 'morning',
            'is_on_duty' => true,
        ]);

        // Nurse User & Employee
        $nurseUser = User::create([
            'name' => 'Sr. Meron Haile',
            'email' => 'nurse@smartqueue.et',
            'password' => $defaultPassword,
            'role' => 'nurse',
            'facility_id' => $tash->id,
            'department_id' => $opd->id,
            'phone_number' => '+251911440022',
            'preferred_language' => 'am',
            'is_active' => true,
        ]);

        $nurseEmployee = Employee::create([
            'facility_id' => $tash->id,
            'department_id' => $opd->id,
            'user_id' => $nurseUser->id,
            'employee_code' => 'NUR-0118',
            'first_name' => 'Meron',
            'last_name' => 'Haile',
            'role' => 'nurse',
            'specialization' => 'Triage & Clinical Nursing',
            'license_number' => 'ETH-RN-77210',
            'phone' => '+251911440022',
            'room_number' => 'Triage Station 1',
            'shift' => 'morning',
            'is_on_duty' => true,
        ]);

        // Pharmacist User & Employee
        $pharmUser = User::create([
            'name' => 'Pharm. Daniel Tadesse',
            'email' => 'pharmacist@smartqueue.et',
            'password' => $defaultPassword,
            'role' => 'pharmacist',
            'facility_id' => $tash->id,
            'department_id' => $pharm->id,
            'phone_number' => '+251911440033',
            'preferred_language' => 'am',
            'is_active' => true,
        ]);

        $pharmEmployee = Employee::create([
            'facility_id' => $tash->id,
            'department_id' => $pharm->id,
            'user_id' => $pharmUser->id,
            'employee_code' => 'PHM-0087',
            'first_name' => 'Daniel',
            'last_name' => 'Tadesse',
            'role' => 'pharmacist',
            'specialization' => 'Clinical Pharmacy',
            'license_number' => 'ETH-PH-51209',
            'phone' => '+251911440033',
            'room_number' => 'Pharmacy Counter 2',
            'shift' => 'morning',
            'is_on_duty' => true,
        ]);

        // Lab Tech User & Employee
        $labUser = User::create([
            'name' => 'Henok Girma',
            'email' => 'lab@smartqueue.et',
            'password' => $defaultPassword,
            'role' => 'lab_technician',
            'facility_id' => $tash->id,
            'department_id' => $lab->id,
            'phone_number' => '+251911440044',
            'preferred_language' => 'en',
            'is_active' => true,
        ]);

        $labEmployee = Employee::create([
            'facility_id' => $tash->id,
            'department_id' => $lab->id,
            'user_id' => $labUser->id,
            'employee_code' => 'LAB-0205',
            'first_name' => 'Henok',
            'last_name' => 'Girma',
            'role' => 'lab_technician',
            'specialization' => 'Medical Laboratory Technologist',
            'license_number' => 'ETH-MLT-33981',
            'phone' => '+251911440044',
            'room_number' => 'Lab Bench 3',
            'shift' => 'morning',
            'is_on_duty' => true,
        ]);

        // Cashier User & Employee
        $cashierUser = User::create([
            'name' => 'Tigist Alemu',
            'email' => 'cashier@smartqueue.et',
            'password' => $defaultPassword,
            'role' => 'cashier',
            'facility_id' => $tash->id,
            'department_id' => $cashierDept->id,
            'phone_number' => '+251911440055',
            'preferred_language' => 'am',
            'is_active' => true,
        ]);

        $cashierEmployee = Employee::create([
            'facility_id' => $tash->id,
            'department_id' => $cashierDept->id,
            'user_id' => $cashierUser->id,
            'employee_code' => 'CSH-0034',
            'first_name' => 'Tigist',
            'last_name' => 'Alemu',
            'role' => 'cashier',
            'room_number' => 'Cashier Window 1',
            'shift' => 'morning',
            'is_on_duty' => true,
        ]);

        // Patient User
        $patientUser = User::create([
            'name' => 'Abebe Kebede',
            'email' => 'patient@smartqueue.et',
            'password' => $defaultPassword,
            'role' => 'patient',
            'facility_id' => $tash->id,
            'phone_number' => '+251911223344',
            'preferred_language' => 'am',
            'is_active' => true,
        ]);

        // 4. DEMO PATIENTS
        $abebe = Patient::create([
            'user_id' => $patientUser->id,
            'registered_facility_id' => $tash->id,
            'mrn' => 'SQH-AA-2026-00001',
            'national_id' => 'FA-9812-4412',
            'cbhi_number' => 'CBHI-AA-04-1290',
            'first_name' => 'Abebe',
            'father_name' => 'Kebede',
            'grandfather_name' => 'Wolde',
            'amharic_full_name' => 'አበበ ከበደ ወልዴ',
            'date_of_birth' => '1984-05-12',
            'age_years' => 42,
            'gender' => 'male',
            'blood_group' => 'O+',
            'phone_number' => '+251911223344',
            'email' => 'abebe@example.com',
            'region' => 'Addis Ababa',
            'city' => 'Addis Ababa',
            'subcity_woreda' => 'Kirkos Subcity, Woreda 02',
            'kebele' => '02/03',
            'emergency_contact_name' => 'Tirunesh Kebede',
            'emergency_contact_phone' => '+251911556677',
            'emergency_contact_relation' => 'Sister',
            'allergies' => ['Penicillin', 'Sulfa drugs'],
            'chronic_conditions' => ['Type 2 Diabetes Mellitus', 'Essential Hypertension'],
            'preferred_language' => 'am',
            'sms_notifications_enabled' => true,
        ]);

        $chaltu = Patient::create([
            'registered_facility_id' => $tash->id,
            'mrn' => 'SQH-AA-2026-00002',
            'national_id' => 'FA-3312-9901',
            'cbhi_number' => 'CBHI-AA-07-8821',
            'first_name' => 'Chaltu',
            'father_name' => 'Tolosa',
            'grandfather_name' => 'Geda',
            'amharic_full_name' => 'ጫልቱ ቶሎሳ ገዳ',
            'date_of_birth' => '1992-09-24',
            'age_years' => 34,
            'gender' => 'female',
            'blood_group' => 'A+',
            'phone_number' => '+251922334455',
            'region' => 'Addis Ababa',
            'city' => 'Addis Ababa',
            'subcity_woreda' => 'Yeka Subcity',
            'kebele' => '11',
            'allergies' => [],
            'chronic_conditions' => ['Mild Asthma'],
            'preferred_language' => 'am',
            'sms_notifications_enabled' => true,
        ]);

        $yohannes = Patient::create([
            'registered_facility_id' => $tash->id,
            'mrn' => 'SQH-AA-2026-00003',
            'first_name' => 'Yohannes',
            'father_name' => 'Desta',
            'grandfather_name' => 'Beyene',
            'amharic_full_name' => 'ዮሐንስ ደስታ በየነ',
            'date_of_birth' => '1970-02-18',
            'age_years' => 56,
            'gender' => 'male',
            'blood_group' => 'B+',
            'phone_number' => '+251933445566',
            'region' => 'Addis Ababa',
            'city' => 'Addis Ababa',
            'subcity_woreda' => 'Bole Subcity',
            'kebele' => '03',
            'allergies' => ['Aspirin'],
            'chronic_conditions' => ['Hypertension'],
            'preferred_language' => 'en',
            'sms_notifications_enabled' => true,
        ]);

        // 5. ESSENTIAL MEDICATIONS & STOCK BATCHES
        $meds = [
            ['code' => 'MED-AMOX-500', 'generic_name' => 'Amoxicillin', 'brand_name' => 'Amoxil', 'category' => 'Antibiotics', 'dosage_form' => 'Capsule', 'strength' => '500mg', 'unit_price' => 14.50, 'reorder' => 100],
            ['code' => 'MED-PARA-500', 'generic_name' => 'Paracetamol', 'brand_name' => 'Panadol', 'category' => 'Analgesics', 'dosage_form' => 'Tablet', 'strength' => '500mg', 'unit_price' => 5.00, 'reorder' => 200],
            ['code' => 'MED-METF-500', 'generic_name' => 'Metformin HCl', 'brand_name' => 'Glucophage', 'category' => 'Antidiabetics', 'dosage_form' => 'Tablet', 'strength' => '500mg', 'unit_price' => 8.25, 'reorder' => 150],
            ['code' => 'MED-AMLO-005', 'generic_name' => 'Amlodipine Besylate', 'brand_name' => 'Norvasc', 'category' => 'Antihypertensives', 'dosage_form' => 'Tablet', 'strength' => '5mg', 'unit_price' => 9.50, 'reorder' => 100],
            ['code' => 'MED-COAR-020', 'generic_name' => 'Artemether + Lumefantrine', 'brand_name' => 'Coartem', 'category' => 'Antimalarials', 'dosage_form' => 'Tablet', 'strength' => '20/120mg', 'unit_price' => 45.00, 'reorder' => 80],
            ['code' => 'MED-OMEP-020', 'generic_name' => 'Omeprazole', 'brand_name' => 'Prilosec', 'category' => 'Gastrointestinal', 'dosage_form' => 'Capsule', 'strength' => '20mg', 'unit_price' => 12.00, 'reorder' => 120],
            ['code' => 'MED-CEFT-001', 'generic_name' => 'Ceftriaxone Sodium', 'brand_name' => 'Rocephin', 'category' => 'Antibiotics', 'dosage_form' => 'Injection', 'strength' => '1g Vial', 'unit_price' => 95.00, 'reorder' => 50],
            ['code' => 'MED-ORS-SCT', 'generic_name' => 'Oral Rehydration Salts', 'brand_name' => 'ORS WHO Formula', 'category' => 'Electrolytes', 'dosage_form' => 'Sachet', 'strength' => '20.5g', 'unit_price' => 10.00, 'reorder' => 300],
        ];

        foreach ($meds as $m) {
            $createdMed = Medication::create([
                'code' => $m['code'],
                'generic_name' => $m['generic_name'],
                'brand_name' => $m['brand_name'],
                'category' => $m['category'],
                'dosage_form' => $m['dosage_form'],
                'strength' => $m['strength'],
                'unit_price' => $m['unit_price'],
                'reorder_level' => $m['reorder'],
                'is_controlled' => false,
                'is_active' => true,
            ]);

            StockBatch::create([
                'facility_id' => $tash->id,
                'medication_id' => $createdMed->id,
                'batch_number' => 'BAT-' . strtoupper(substr($m['code'], 4, 4)) . '-2026',
                'quantity_in_stock' => rand(150, 600),
                'expiry_date' => Carbon::today()->addMonths(rand(12, 36)),
                'unit_price' => $m['unit_price'],
                'supplier' => 'Ethiopian Pharmaceuticals Supply Service (EPSS)',
            ]);
        }

        // 6. INPATIENT WARDS & BEDS
        $maleWard = Ward::create([
            'facility_id' => $tash->id,
            'name' => 'Male Medical Ward',
            'code' => 'MMW',
            'floor_location' => 'Block B, 2nd Floor',
            'total_beds' => 10,
            'gender_allowed' => 'male',
        ]);

        $femaleWard = Ward::create([
            'facility_id' => $tash->id,
            'name' => 'Female Surgical Ward',
            'code' => 'FSW',
            'floor_location' => 'Block B, 3rd Floor',
            'total_beds' => 8,
            'gender_allowed' => 'female',
        ]);

        $icuWard = Ward::create([
            'facility_id' => $tash->id,
            'name' => 'Intensive Care Unit (ICU)',
            'code' => 'ICU',
            'floor_location' => 'Block A, 1st Floor',
            'total_beds' => 4,
            'gender_allowed' => 'all',
        ]);

        for ($i = 1; $i <= 10; $i++) {
            Bed::create([
                'ward_id' => $maleWard->id,
                'bed_number' => sprintf('MMW-B%02d', $i),
                'daily_rate' => 250.00,
                'status' => ($i === 1) ? 'occupied' : 'available',
            ]);
        }

        for ($i = 1; $i <= 8; $i++) {
            Bed::create([
                'ward_id' => $femaleWard->id,
                'bed_number' => sprintf('FSW-B%02d', $i),
                'daily_rate' => 250.00,
                'status' => 'available',
            ]);
        }

        for ($i = 1; $i <= 4; $i++) {
            Bed::create([
                'ward_id' => $icuWard->id,
                'bed_number' => sprintf('ICU-B%02d', $i),
                'daily_rate' => 850.00,
                'status' => 'available',
            ]);
        }

        // 7. QUEUE TICKETS (Live Hospital Demo)
        $t1 = QueueTicket::create([
            'facility_id' => $tash->id,
            'department_id' => $opd->id,
            'patient_id' => $abebe->id,
            'doctor_id' => $doctorEmployee->id,
            'ticket_number' => 'OPD-001',
            'queue_type' => 'walk_in',
            'priority_level' => 'normal',
            'triage_category' => 'green',
            'status' => 'called',
            'counter_or_room' => 'OPD Room 104',
            'estimated_wait_minutes' => 0,
            'issued_at' => now()->subMinutes(25),
            'called_at' => now()->subMinutes(2),
        ]);

        QueueEvent::create([
            'queue_ticket_id' => $t1->id,
            'from_status' => 'waiting',
            'to_status' => 'called',
            'action_by' => 'Dr. Almaz Tefera',
            'room_or_counter' => 'OPD Room 104',
            'occurred_at' => now()->subMinutes(2),
        ]);

        $t2 = QueueTicket::create([
            'facility_id' => $tash->id,
            'department_id' => $opd->id,
            'patient_id' => $chaltu->id,
            'doctor_id' => $doctorEmployee->id,
            'ticket_number' => 'OPD-002',
            'queue_type' => 'walk_in',
            'priority_level' => 'normal',
            'triage_category' => 'green',
            'status' => 'waiting',
            'estimated_wait_minutes' => 12,
            'issued_at' => now()->subMinutes(15),
        ]);

        $t3 = QueueTicket::create([
            'facility_id' => $tash->id,
            'department_id' => $emerg->id,
            'patient_id' => $yohannes->id,
            'ticket_number' => 'EMG-001',
            'queue_type' => 'emergency',
            'priority_level' => 'urgent',
            'triage_category' => 'yellow',
            'status' => 'waiting',
            'estimated_wait_minutes' => 4,
            'issued_at' => now()->subMinutes(8),
        ]);

        // 8. CLINICAL ENCOUNTER, SOAP NOTES & VITALS
        $encounter = Encounter::create([
            'facility_id' => $tash->id,
            'patient_id' => $abebe->id,
            'doctor_id' => $doctorEmployee->id,
            'department_id' => $opd->id,
            'queue_ticket_id' => $t1->id,
            'encounter_type' => 'opd',
            'chief_complaint' => 'Follow up on elevated blood glucose and mild bilateral ankle swelling for 1 week',
            'triage_category' => 'green',
            'triage_assessment' => 'Alert, oriented, stable vital signs. Moderate ankle pitting edema.',
            'vital_signs' => [
                'bp_systolic' => 138,
                'bp_diastolic' => 88,
                'heart_rate' => 76,
                'temperature' => 36.8,
                'respiratory_rate' => 16,
                'spo2' => 98,
                'weight_kg' => 78.5,
                'height_cm' => 174,
                'bmi' => 25.9,
            ],
            'status' => 'in_progress',
            'started_at' => now()->subMinutes(10),
        ]);

        ClinicalNote::create([
            'encounter_id' => $encounter->id,
            'patient_id' => $abebe->id,
            'doctor_id' => $doctorEmployee->id,
            'subjective' => 'Patient reports compliant medication intake but notes occasional morning headaches and pedal edema.',
            'objective' => 'CVS: S1, S2 audible, no murmurs. Lungs: Clear bilaterally. Extremities: +1 pitting pedal edema.',
            'assessment' => 'Type 2 Diabetes Mellitus with sub-optimal glycemic control; Essential Hypertension.',
            'plan' => '1. Continue Metformin 500mg BD. 2. Start Amlodipine 5mg OD. 3. Order HbA1c, Fasting Blood Sugar, Lipid Profile, and Urinalysis. 4. Return in 2 weeks.',
            'icd10_codes' => [
                ['code' => 'E11.9', 'title' => 'Type 2 diabetes mellitus without complications'],
                ['code' => 'I10', 'title' => 'Essential (primary) hypertension'],
            ],
            'follow_up_date' => Carbon::today()->addWeeks(2),
            'is_signed' => true,
            'signed_at' => now(),
        ]);

        // 9. PRESCRIPTION & MEDREMINDER SCHEDULES
        $metforminMed = Medication::where('code', 'MED-METF-500')->first();
        $amlodipineMed = Medication::where('code', 'MED-AMLO-005')->first();

        $rx = Prescription::create([
            'facility_id' => $tash->id,
            'encounter_id' => $encounter->id,
            'patient_id' => $abebe->id,
            'doctor_id' => $doctorEmployee->id,
            'prescription_code' => 'RX-20261116-0042',
            'diagnosis' => 'Type 2 DM & Hypertension',
            'status' => 'pending',
            'notes' => 'Take with or immediately after meals.',
        ]);

        $item1 = PrescriptionItem::create([
            'prescription_id' => $rx->id,
            'medication_id' => $metforminMed->id,
            'medication_name' => 'Metformin HCl 500mg',
            'dosage' => '1 Tablet (500mg)',
            'frequency' => 'Twice daily with meals',
            'duration_days' => 30,
            'quantity' => 60,
            'instructions' => 'Take 1 tablet morning and evening with food',
        ]);

        $item2 = PrescriptionItem::create([
            'prescription_id' => $rx->id,
            'medication_id' => $amlodipineMed->id,
            'medication_name' => 'Amlodipine Besylate 5mg',
            'dosage' => '1 Tablet (5mg)',
            'frequency' => 'Once daily morning',
            'duration_days' => 30,
            'quantity' => 30,
            'instructions' => 'Take 1 tablet every morning at 08:00',
        ]);

        // MedReminder Schedules for Abebe
        $sched1 = MedicationSchedule::create([
            'patient_id' => $abebe->id,
            'prescription_item_id' => $item1->id,
            'medicine_name' => 'Metformin HCl 500mg',
            'dosage' => '1 Tablet (500mg)',
            'scheduled_time' => '08:00',
            'frequency' => 'Twice daily with meals',
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today()->addDays(30),
            'instructions' => 'Take morning with breakfast',
            'sms_reminder_enabled' => true,
            'is_active' => true,
        ]);

        $sched2 = MedicationSchedule::create([
            'patient_id' => $abebe->id,
            'prescription_item_id' => $item1->id,
            'medicine_name' => 'Metformin HCl 500mg',
            'dosage' => '1 Tablet (500mg)',
            'scheduled_time' => '20:00',
            'frequency' => 'Twice daily with meals',
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today()->addDays(30),
            'instructions' => 'Take evening with dinner',
            'sms_reminder_enabled' => true,
            'is_active' => true,
        ]);

        $sched3 = MedicationSchedule::create([
            'patient_id' => $abebe->id,
            'prescription_item_id' => $item2->id,
            'medicine_name' => 'Amlodipine Besylate 5mg',
            'dosage' => '1 Tablet (5mg)',
            'scheduled_time' => '08:00',
            'frequency' => 'Once daily morning',
            'start_date' => Carbon::today(),
            'end_date' => Carbon::today()->addDays(30),
            'instructions' => 'Take every morning at 08:00',
            'sms_reminder_enabled' => true,
            'is_active' => true,
        ]);

        // Log sample taken dose
        MedicationLog::create([
            'medication_schedule_id' => $sched1->id,
            'patient_id' => $abebe->id,
            'scheduled_at' => Carbon::today()->setHour(8)->setMinute(0),
            'taken_at' => Carbon::today()->setHour(8)->setMinute(12),
            'status' => 'taken',
            'patient_notes' => 'Taken with tea and bread',
        ]);

        // 10. LABORATORY ORDERS & RESULTS
        $labOrder = LabOrder::create([
            'facility_id' => $tash->id,
            'encounter_id' => $encounter->id,
            'patient_id' => $abebe->id,
            'doctor_id' => $doctorEmployee->id,
            'order_number' => 'LAB-20261116-0012',
            'test_type' => 'Fasting Blood Sugar & Lipid Profile',
            'priority' => 'normal',
            'status' => 'completed',
            'specimen_type' => 'Venous Blood',
            'specimen_barcode' => 'SPEC-882901',
            'collected_at' => now()->subHours(1),
            'clinical_notes' => 'Check diabetic control',
        ]);

        LabResult::create([
            'lab_order_id' => $labOrder->id,
            'test_name' => 'Fasting Blood Sugar (FBS)',
            'result_value' => '142',
            'unit' => 'mg/dL',
            'reference_range' => '70 - 100 mg/dL',
            'flag' => 'high',
            'technician_id' => $labEmployee->id,
            'verified_by_id' => $labEmployee->id,
            'verified_at' => now()->subMinutes(30),
            'comments' => 'Elevated fasting glucose',
        ]);

        LabResult::create([
            'lab_order_id' => $labOrder->id,
            'test_name' => 'Total Cholesterol',
            'result_value' => '186',
            'unit' => 'mg/dL',
            'reference_range' => '< 200 mg/dL',
            'flag' => 'normal',
            'technician_id' => $labEmployee->id,
            'verified_by_id' => $labEmployee->id,
            'verified_at' => now()->subMinutes(30),
        ]);

        // 11. INVOICE, CASHIER PAYMENT (Telebirr & CBE Birr)
        $invoice = Invoice::create([
            'facility_id' => $tash->id,
            'patient_id' => $abebe->id,
            'encounter_id' => $encounter->id,
            'invoice_number' => 'INV-20261116-0091',
            'subtotal' => 280.00,
            'discount' => 0.00,
            'tax' => 0.00,
            'total_amount' => 280.00,
            'paid_amount' => 280.00,
            'balance' => 0.00,
            'payment_status' => 'paid',
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'service_type' => 'consultation',
            'description' => 'Outpatient Specialist Consultation (Internal Medicine)',
            'quantity' => 1,
            'unit_price' => 150.00,
            'total_price' => 150.00,
        ]);

        InvoiceItem::create([
            'invoice_id' => $invoice->id,
            'service_type' => 'lab',
            'description' => 'Fasting Blood Sugar & Lipid Profile Examination',
            'quantity' => 1,
            'unit_price' => 130.00,
            'total_price' => 130.00,
        ]);

        Payment::create([
            'invoice_id' => $invoice->id,
            'facility_id' => $tash->id,
            'patient_id' => $abebe->id,
            'cashier_id' => $cashierEmployee->id,
            'amount' => 280.00,
            'payment_method' => 'telebirr',
            'transaction_reference' => 'TB-9821876402',
            'receipt_number' => 'REC-20261116-0001',
            'status' => 'successful',
            'provider_response' => [
                'provider' => 'telebirr',
                'payer_phone' => '+251911223344',
                'merchant' => 'Tikur Anbessa Hospital',
                'currency' => 'ETB',
            ],
            'paid_at' => now()->subMinutes(40),
        ]);

        // 12. SAMPLE APPOINTMENT
        Appointment::create([
            'facility_id' => $tash->id,
            'department_id' => $opd->id,
            'doctor_id' => $doctorEmployee->id,
            'patient_id' => $abebe->id,
            'appointment_date' => Carbon::today()->addWeeks(2),
            'time_slot' => '09:30 - 10:00',
            'reason' => '2-week diabetes & blood pressure follow up check',
            'status' => 'scheduled',
            'is_teleconsult' => false,
            'sms_reminder_sent' => false,
        ]);
    }
}
