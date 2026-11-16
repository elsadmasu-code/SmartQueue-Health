<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Encounter;
use App\Services\EmrService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EncounterController extends Controller
{
    public function __construct(protected EmrService $emrService) {}

    public function index(Request $request): JsonResponse
    {
        $query = Encounter::with(['patient', 'doctor', 'department', 'clinicalNotes']);

        if ($request->has('facility_id')) {
            $query->where('facility_id', $request->facility_id);
        }

        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->latest()->paginate(20));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'patient_id' => 'required|exists:patients,id',
            'department_id' => 'required|exists:departments,id',
            'doctor_id' => 'nullable|exists:employees,id',
            'queue_ticket_id' => 'nullable|exists:queue_tickets,id',
            'encounter_type' => 'nullable|string|in:opd,emergency,inpatient,follow_up',
            'chief_complaint' => 'nullable|string',
            'triage_category' => 'nullable|string|in:red,yellow,green,blue',
            'triage_assessment' => 'nullable|string',
            'vital_signs' => 'nullable|array',
            'clinical_note' => 'nullable|array',
        ]);

        $clinicalNote = $validated['clinical_note'] ?? null;
        unset($validated['clinical_note']);

        $encounter = $this->emrService->recordEncounter($validated, $clinicalNote);

        return response()->json([
            'message' => 'Encounter recorded successfully',
            'encounter' => $encounter,
        ], 201);
    }

    public function show(int $id): JsonResponse
    {
        $encounter = Encounter::with([
            'patient',
            'doctor',
            'department',
            'clinicalNotes',
            'prescriptions.items',
            'labOrders.results',
            'imagingOrders.report',
        ])->findOrFail($id);

        return response()->json(['encounter' => $encounter]);
    }

    public function updateVitals(Request $request, int $id): JsonResponse
    {
        $request->validate([
            'vital_signs' => 'required|array',
            'triage_category' => 'nullable|string|in:red,yellow,green,blue',
        ]);

        $encounter = Encounter::findOrFail($id);
        $encounter->update([
            'vital_signs' => $request->vital_signs,
            'triage_category' => $request->triage_category ?? $encounter->triage_category,
        ]);

        return response()->json(['message' => 'Vitals recorded successfully', 'encounter' => $encounter]);
    }
}
