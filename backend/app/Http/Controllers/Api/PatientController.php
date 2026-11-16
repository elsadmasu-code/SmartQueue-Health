<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Services\EmrService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PatientController extends Controller
{
    public function __construct(protected EmrService $emrService) {}

    public function index(Request $request): JsonResponse
    {
        $query = Patient::query();

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('mrn', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('father_name', 'like', "%{$search}%")
                  ->orWhere('amharic_full_name', 'like', "%{$search}%")
                  ->orWhere('national_id', 'like', "%{$search}%");
            });
        }

        $patients = $query->latest()->paginate($request->query('per_page', 20));

        return response()->json($patients);
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'registered_facility_id' => 'required|exists:facilities,id',
            'first_name' => 'required|string|max:100',
            'father_name' => 'required|string|max:100',
            'grandfather_name' => 'nullable|string|max:100',
            'amharic_full_name' => 'nullable|string|max:200',
            'date_of_birth' => 'nullable|date',
            'age_years' => 'nullable|integer|min:0|max:130',
            'gender' => 'required|in:male,female',
            'blood_group' => 'nullable|string|max:5',
            'phone_number' => 'nullable|string|max:20',
            'email' => 'nullable|email',
            'region' => 'nullable|string',
            'city' => 'nullable|string',
            'subcity_woreda' => 'nullable|string',
            'kebele' => 'nullable|string',
            'emergency_contact_name' => 'nullable|string',
            'emergency_contact_phone' => 'nullable|string',
            'emergency_contact_relation' => 'nullable|string',
            'allergies' => 'nullable|array',
            'chronic_conditions' => 'nullable|array',
            'preferred_language' => 'nullable|in:am,en,or',
            'cbhi_number' => 'nullable|string',
            'national_id' => 'nullable|string',
        ]);

        $patient = $this->emrService->registerPatient($validated);

        return response()->json([
            'message' => 'Patient registered successfully',
            'patient' => $patient,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $patient = Patient::with(['facility', 'encounters.doctor'])->findOrFail($id);

        return response()->json(['patient' => $patient]);
    }

    public function longitudinal(int $id): JsonResponse
    {
        $record = $this->emrService->getLongitudinalRecord($id);

        return response()->json($record);
    }
}
