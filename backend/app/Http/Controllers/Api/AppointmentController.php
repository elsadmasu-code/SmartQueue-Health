<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Employee;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Appointment::with(['facility', 'department', 'doctor', 'patient']);

        if ($request->has('facility_id')) {
            $query->where('facility_id', $request->facility_id);
        }

        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->has('doctor_id')) {
            $query->where('doctor_id', $request->doctor_id);
        }

        if ($request->has('date')) {
            $query->whereDate('appointment_date', $request->date);
        }

        return response()->json($query->orderBy('appointment_date')->orderBy('time_slot')->paginate(20));
    }

    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'department_id' => 'required|exists:departments,id',
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:employees,id',
            'appointment_date' => 'required|date|after_or_equal:today',
            'time_slot' => 'required|string',
            'reason' => 'nullable|string',
            'is_teleconsult' => 'nullable|boolean',
            'notes' => 'nullable|string',
        ]);

        // Conflict check
        $conflict = Appointment::where('facility_id', $validated['facility_id'])
            ->where('appointment_date', $validated['appointment_date'])
            ->where('time_slot', $validated['time_slot'])
            ->where('status', 'scheduled')
            ->when(!empty($validated['doctor_id']), fn($q) => $q->where('doctor_id', $validated['doctor_id']))
            ->exists();

        if ($conflict) {
            return response()->json(['message' => 'The selected doctor or slot is already booked for this time.'], 422);
        }

        $appointment = Appointment::create($validated);

        return response()->json([
            'message' => 'Appointment scheduled successfully',
            'appointment' => $appointment->load(['patient', 'doctor', 'department']),
        ], 201);
    }

    public function cancel(Request $request, int $id): JsonResponse
    {
        $appointment = Appointment::findOrFail($id);
        $appointment->update([
            'status' => 'cancelled',
            'notes' => $request->input('reason', 'Cancelled by user'),
        ]);

        return response()->json(['message' => 'Appointment cancelled', 'appointment' => $appointment]);
    }
}
