<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\BillingController;
use App\Http\Controllers\Api\ChatbotController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\EncounterController;
use App\Http\Controllers\Api\FacilityController;
use App\Http\Controllers\Api\InpatientController;
use App\Http\Controllers\Api\InteropController;
use App\Http\Controllers\Api\LabRadiologyController;
use App\Http\Controllers\Api\MedReminderController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\PharmacyController;
use App\Http\Controllers\Api\QueueController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| SmartQueue Health & MedReminder (SQHM) - REST API Routes
|--------------------------------------------------------------------------
*/

// Public Authentication & Health Check
Route::get('/health', fn() => response()->json(['status' => 'healthy', 'system' => 'SmartQueue Health & MedReminder', 'version' => '1.0.0', 'time' => now()]));
Route::post('/auth/register', [AuthController::class, 'register']);
Route::post('/auth/login', [AuthController::class, 'login']);

// Public Queues & Monitor Boards (Public displays in hospital waiting areas)
Route::get('/facilities', [FacilityController::class, 'index']);
Route::get('/facilities/{id}', [FacilityController::class, 'show']);
Route::get('/facilities/{id}/departments', [FacilityController::class, 'departments']);
Route::get('/facilities/{id}/employees', [FacilityController::class, 'employees']);
Route::get('/facilities/{facilityId}/queue/board', [QueueController::class, 'liveBoard']);

// AI Chatbot Assistant (Public or Authenticated)
Route::post('/chatbot/message', [ChatbotController::class, 'chat']);

// National Interoperability (Master Facility Registry MFR check)
Route::get('/interop/mfr/verify', [InteropController::class, 'verifyMfr']);

// Offline Sync endpoint (Supports mobile clients & edge offline syncing)
Route::post('/sync/offline-batch', [InteropController::class, 'syncOfflineBatch']);

// Protected Clinical & Hospital Administration Routes
Route::middleware('auth:sanctum')->group(function () {
    // Current User Profile & Logout
    Route::get('/auth/user', [AuthController::class, 'profile']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Hospital Executive Dashboard & KPIs
    Route::get('/facilities/{facilityId}/dashboard/stats', [DashboardController::class, 'stats']);

    // Patients & EMR
    Route::get('/patients', [PatientController::class, 'index']);
    Route::post('/patients', [PatientController::class, 'store']);
    Route::get('/patients/{id}', [PatientController::class, 'show']);
    Route::get('/patients/{id}/longitudinal', [PatientController::class, 'longitudinal']);

    // Smart Queue Operations
    Route::post('/queue/issue', [QueueController::class, 'issue']);
    Route::post('/queue/{ticketId}/call', [QueueController::class, 'callNext']);
    Route::post('/queue/{ticketId}/start', [QueueController::class, 'start']);
    Route::post('/queue/{ticketId}/complete', [QueueController::class, 'complete']);
    Route::post('/queue/{ticketId}/skip', [QueueController::class, 'skip']);

    // Appointments
    Route::get('/appointments', [AppointmentController::class, 'index']);
    Route::post('/appointments', [AppointmentController::class, 'store']);
    Route::post('/appointments/{id}/cancel', [AppointmentController::class, 'cancel']);

    // Encounters, Triage & Doctor Consultations
    Route::get('/encounters', [EncounterController::class, 'index']);
    Route::post('/encounters', [EncounterController::class, 'store']);
    Route::get('/encounters/{id}', [EncounterController::class, 'show']);
    Route::put('/encounters/{id}/vitals', [EncounterController::class, 'updateVitals']);

    // Pharmacy & Medication Inventory
    Route::get('/pharmacy/medications', [PharmacyController::class, 'medications']);
    Route::get('/pharmacy/prescriptions', [PharmacyController::class, 'prescriptions']);
    Route::post('/pharmacy/prescriptions', [PharmacyController::class, 'storePrescription']);
    Route::post('/pharmacy/prescriptions/{id}/dispense', [PharmacyController::class, 'dispense']);

    // MedReminder Adherence Engine
    Route::get('/patients/{patientId}/med-reminders', [MedReminderController::class, 'schedules']);
    Route::post('/med-reminders', [MedReminderController::class, 'storeSchedule']);
    Route::post('/med-reminders/log-dose', [MedReminderController::class, 'logDose']);
    Route::post('/med-reminders/{scheduleId}/trigger', [MedReminderController::class, 'triggerReminder']);

    // Laboratory & Diagnostic Services
    Route::get('/lab/orders', [LabRadiologyController::class, 'labOrders']);
    Route::post('/lab/orders', [LabRadiologyController::class, 'storeLabOrder']);
    Route::post('/lab/orders/{orderId}/results', [LabRadiologyController::class, 'recordLabResults']);

    // Radiology & Imaging
    Route::get('/radiology/orders', [LabRadiologyController::class, 'imagingOrders']);
    Route::post('/radiology/orders', [LabRadiologyController::class, 'storeImagingOrder']);
    Route::post('/radiology/orders/{orderId}/report', [LabRadiologyController::class, 'recordImagingReport']);

    // Inpatient & Ward Bed Management
    Route::get('/facilities/{facilityId}/wards', [InpatientController::class, 'wards']);
    Route::get('/inpatient/admissions', [InpatientController::class, 'admissions']);
    Route::post('/inpatient/admit', [InpatientController::class, 'admit']);
    Route::post('/inpatient/admissions/{id}/discharge', [InpatientController::class, 'discharge']);

    // Cashier, Billing & Ethiopian Payments
    Route::get('/billing/invoices', [BillingController::class, 'invoices']);
    Route::post('/billing/invoices', [BillingController::class, 'storeInvoice']);
    Route::post('/billing/invoices/{id}/pay', [BillingController::class, 'pay']);
    Route::post('/billing/invoices/{id}/claim', [BillingController::class, 'submitClaim']);

    // National Health System Interoperability
    Route::get('/facilities/{facilityId}/interop/dhis2', [InteropController::class, 'exportDhis2']);
    Route::get('/encounters/{encounterId}/interop/fhir', [InteropController::class, 'exportFhir']);
});
