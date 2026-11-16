<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MedicationSchedule;
use App\Services\MedReminderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MedReminderController extends Controller
{
    public function __construct(protected MedReminderService $medReminderService) {}

    public function schedules(Request $request, int $patientId): JsonResponse
    {
        $schedules = MedicationSchedule::with(['logs'])
            ->where('patient_id', $patientId)
            ->where('is_active', true)
            ->get();

        $adherence = $this->medReminderService->calculateAdherenceRate($patientId);

        return response()->json([
            'patient_id' => $patientId,
            'adherence_summary' => $adherence,
            'schedules' => $schedules,
        ]);
    }

    public function storeSchedule(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'medicine_name' => 'required|string',
            'dosage' => 'required|string',
            'scheduled_time' => 'required|string',
            'frequency' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date',
            'instructions' => 'nullable|string',
            'sms_reminder_enabled' => 'nullable|boolean',
        ]);

        $schedule = MedicationSchedule::create($validated);

        return response()->json([
            'message' => 'Medication schedule created successfully',
            'schedule' => $schedule,
        ], 201);
    }

    public function logDose(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'medication_schedule_id' => 'required|exists:medication_schedules,id',
            'patient_id' => 'required|exists:patients,id',
            'status' => 'required|in:taken,missed,skipped,delayed',
            'patient_notes' => 'nullable|string',
        ]);

        $log = $this->medReminderService->logDose(
            $validated['medication_schedule_id'],
            $validated['patient_id'],
            $validated['status'],
            $validated['patient_notes'] ?? null
        );

        $adherence = $this->medReminderService->calculateAdherenceRate($validated['patient_id']);

        return response()->json([
            'message' => 'Dose status recorded successfully',
            'log' => $log,
            'adherence_summary' => $adherence,
        ]);
    }

    public function triggerReminder(int $scheduleId): JsonResponse
    {
        $schedule = MedicationSchedule::with('patient')->findOrFail($scheduleId);
        $this->medReminderService->sendReminderNotification($schedule);

        return response()->json(['message' => 'Reminder notification dispatched via SMS & in-app channel']);
    }
}
