<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\QueueTicket;
use App\Services\QueueService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class QueueController extends Controller
{
    public function __construct(protected QueueService $queueService) {}

    public function issue(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'department_id' => 'required|exists:departments,id',
            'patient_id' => 'required|exists:patients,id',
            'doctor_id' => 'nullable|exists:employees,id',
            'queue_type' => 'nullable|string|in:walk_in,appointment,emergency,follow_up,lab,pharmacy,radiology',
            'priority_level' => 'nullable|string|in:normal,urgent,emergency',
            'triage_category' => 'nullable|string|in:red,yellow,green,blue',
            'notes' => 'nullable|string',
        ]);

        $ticket = $this->queueService->issueTicket(
            facilityId: $validated['facility_id'],
            departmentId: $validated['department_id'],
            patientId: $validated['patient_id'],
            queueType: $validated['queue_type'] ?? 'walk_in',
            priorityLevel: $validated['priority_level'] ?? 'normal',
            triageCategory: $validated['triage_category'] ?? 'green',
            doctorId: $validated['doctor_id'] ?? null,
            notes: $validated['notes'] ?? null,
            createdByRole: $request->user()?->role ?? 'receptionist'
        );

        return response()->json([
            'message' => 'Queue ticket issued successfully',
            'ticket' => $ticket->load(['patient', 'department', 'doctor']),
        ], 201);
    }

    public function liveBoard(Request $request, int $facilityId): JsonResponse
    {
        $departmentId = $request->query('department_id') ? (int) $request->query('department_id') : null;
        $board = $this->queueService->getLiveBoard($facilityId, $departmentId);

        return response()->json($board);
    }

    public function callNext(Request $request, int $ticketId): JsonResponse
    {
        $validated = $request->validate([
            'room_or_counter' => 'required|string|max:64',
        ]);

        $actor = $request->user()?->name ?? 'Staff';
        $ticket = $this->queueService->callTicket($ticketId, $validated['room_or_counter'], $actor);

        return response()->json([
            'message' => "Ticket {$ticket->ticket_number} called to {$validated['room_or_counter']}",
            'ticket' => $ticket->load(['patient', 'department']),
        ]);
    }

    public function start(Request $request, int $ticketId): JsonResponse
    {
        $actor = $request->user()?->name ?? 'Doctor';
        $ticket = $this->queueService->startService($ticketId, $actor);

        return response()->json([
            'message' => "Service started for ticket {$ticket->ticket_number}",
            'ticket' => $ticket,
        ]);
    }

    public function complete(Request $request, int $ticketId): JsonResponse
    {
        $actor = $request->user()?->name ?? 'Doctor';
        $ticket = $this->queueService->completeTicket($ticketId, $actor);

        return response()->json([
            'message' => "Ticket {$ticket->ticket_number} marked as completed",
            'ticket' => $ticket,
        ]);
    }

    public function skip(Request $request, int $ticketId): JsonResponse
    {
        $reason = $request->input('reason', 'Patient not present');
        $ticket = $this->queueService->skipTicket($ticketId, $reason);

        return response()->json([
            'message' => "Ticket {$ticket->ticket_number} marked as skipped",
            'ticket' => $ticket,
        ]);
    }
}
