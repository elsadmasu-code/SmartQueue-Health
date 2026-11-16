<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Bed;
use App\Models\Ward;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InpatientController extends Controller
{
    public function wards(Request $request, int $facilityId): JsonResponse
    {
        $wards = Ward::with(['beds.admissions' => fn($q) => $q->where('status', 'admitted')->with('patient')])
            ->where('facility_id', $facilityId)
            ->where('is_active', true)
            ->get();

        return response()->json(['wards' => $wards]);
    }

    public function admissions(Request $request): JsonResponse
    {
        $query = Admission::with(['patient', 'bed.ward', 'admittingDoctor', 'facility']);

        if ($request->has('facility_id')) {
            $query->where('facility_id', $request->facility_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->latest()->paginate(20));
    }

    public function admit(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'patient_id' => 'required|exists:patients,id',
            'encounter_id' => 'nullable|exists:encounters,id',
            'bed_id' => 'required|exists:beds,id',
            'admitting_doctor_id' => 'required|exists:employees,id',
            'admission_diagnosis' => 'required|string',
        ]);

        $admission = DB::transaction(function () use ($validated) {
            $bed = Bed::findOrFail($validated['bed_id']);
            if ($bed->status !== 'available') {
                abort(422, 'The selected bed is currently not available.');
            }

            $bed->update(['status' => 'occupied']);

            return Admission::create([
                'facility_id' => $validated['facility_id'],
                'patient_id' => $validated['patient_id'],
                'encounter_id' => $validated['encounter_id'] ?? null,
                'bed_id' => $bed->id,
                'admitting_doctor_id' => $validated['admitting_doctor_id'],
                'admission_date' => now(),
                'admission_diagnosis' => $validated['admission_diagnosis'],
                'status' => 'admitted',
            ]);
        });

        return response()->json([
            'message' => 'Patient admitted successfully',
            'admission' => $admission->load(['patient', 'bed.ward']),
        ], 201);
    }

    public function discharge(Request $request, int $admissionId): JsonResponse
    {
        $validated = $request->validate([
            'discharge_summary' => 'required|string',
            'discharge_condition' => 'nullable|string', // Stable, Improved, Referred
        ]);

        $admission = DB::transaction(function () use ($admissionId, $validated) {
            $adm = Admission::with('bed')->findOrFail($admissionId);
            $adm->update([
                'status' => 'discharged',
                'discharge_date' => now(),
                'discharge_summary' => $validated['discharge_summary'],
                'discharge_condition' => $validated['discharge_condition'] ?? 'Improved',
            ]);

            $adm->bed->update(['status' => 'available']);

            return $adm;
        });

        return response()->json([
            'message' => 'Patient discharged and bed marked available',
            'admission' => $admission,
        ]);
    }
}
