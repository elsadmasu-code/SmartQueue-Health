<?php

namespace App\Services;

use App\Models\ClinicalNote;
use App\Models\Encounter;
use App\Models\Facility;
use App\Models\Patient;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class EmrService
{
    /**
     * Generate unique Ethiopian MRN (e.g. SQH-AA-2026-00042)
     */
    public function generateMrn(int $facilityId): string
    {
        $facility = Facility::find($facilityId);
        $regionCode = 'ET';
        if ($facility && $facility->region) {
            $words = explode(' ', $facility->region);
            $regionCode = strtoupper(substr($words[0], 0, 2));
        }

        $year = date('Y');
        $count = Patient::whereYear('created_at', $year)->count() + 1;

        return sprintf('SQH-%s-%s-%05d', $regionCode, $year, $count);
    }

    /**
     * Register or find patient with duplicate-check prevention
     */
    public function registerPatient(array $data): Patient
    {
        // Duplicate check by national_id or full name + phone
        if (!empty($data['national_id'])) {
            $existing = Patient::where('national_id', $data['national_id'])->first();
            if ($existing) {
                return $existing;
            }
        }

        if (!empty($data['phone_number']) && !empty($data['first_name']) && !empty($data['father_name'])) {
            $existing = Patient::where('phone_number', $data['phone_number'])
                ->where('first_name', 'like', $data['first_name'])
                ->where('father_name', 'like', $data['father_name'])
                ->first();
            if ($existing) {
                return $existing;
            }
        }

        if (empty($data['mrn'])) {
            $facilityId = $data['registered_facility_id'] ?? 1;
            $data['mrn'] = $this->generateMrn($facilityId);
        }

        return Patient::create($data);
    }

    /**
     * Record clinical encounter with vitals and SOAP notes
     */
    public function recordEncounter(array $encounterData, ?array $clinicalNoteData = null): Encounter
    {
        return DB::transaction(function () use ($encounterData, $clinicalNoteData) {
            $encounter = Encounter::create($encounterData);

            if ($clinicalNoteData) {
                $clinicalNoteData['encounter_id'] = $encounter->id;
                $clinicalNoteData['patient_id'] = $encounter->patient_id;
                $clinicalNoteData['doctor_id'] = $encounter->doctor_id ?? 1;
                $clinicalNoteData['is_signed'] = true;
                $clinicalNoteData['signed_at'] = now();

                ClinicalNote::create($clinicalNoteData);
            }

            return $encounter->load(['clinicalNotes', 'patient', 'doctor']);
        });
    }

    /**
     * Get patient full longitudinal history across encounters
     */
    public function getLongitudinalRecord(int $patientId): array
    {
        $patient = Patient::with([
            'encounters.clinicalNotes',
            'encounters.prescriptions.items',
            'encounters.labOrders.results',
            'encounters.imagingOrders.report',
            'admissions.bed.ward',
            'medicationSchedules.logs',
            'invoices.payments',
        ])->findOrFail($patientId);

        return [
            'patient' => $patient,
            'total_encounters' => $patient->encounters->count(),
            'allergies' => $patient->allergies ?? [],
            'chronic_conditions' => $patient->chronic_conditions ?? [],
            'recent_vitals' => $patient->encounters->last()?->vital_signs,
        ];
    }
}
