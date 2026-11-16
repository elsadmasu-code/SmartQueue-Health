<?php

namespace App\Services;

use App\Models\Department;
use App\Models\Employee;
use App\Models\QueueEvent;
use App\Models\QueueTicket;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class QueueService
{
    /**
     * Issue a new queue ticket for a patient in a department
     */
    public function issueTicket(
        int $facilityId,
        int $departmentId,
        int $patientId,
        string $queueType = 'walk_in',
        string $priorityLevel = 'normal',
        string $triageCategory = 'green',
        ?int $doctorId = null,
        ?string $notes = null,
        string $createdByRole = 'receptionist'
    ): QueueTicket {
        return DB::transaction(function () use (
            $facilityId, $departmentId, $patientId, $queueType,
            $priorityLevel, $triageCategory, $doctorId, $notes, $createdByRole
        ) {
            $dept = Department::findOrFail($departmentId);

            // Generate daily sequence number
            $todayCount = QueueTicket::where('facility_id', $facilityId)
                ->where('department_id', $departmentId)
                ->whereDate('created_at', today())
                ->count();

            $sequence = $todayCount + 1;
            $codePrefix = strtoupper($dept->code ?: 'GEN');
            $ticketNumber = sprintf('%s-%03d', $codePrefix, $sequence);

            // Calculate estimated wait time based on waiting queue count
            $waitingPatients = QueueTicket::where('facility_id', $facilityId)
                ->where('department_id', $departmentId)
                ->where('status', 'waiting')
                ->count();

            $onDutyDoctors = Employee::where('facility_id', $facilityId)
                ->where('department_id', $departmentId)
                ->where('is_on_duty', true)
                ->count();

            $activeStaff = max(1, $onDutyDoctors);
            $avgMinutesPerPatient = 12;

            if ($priorityLevel === 'emergency' || $triageCategory === 'red') {
                $estimatedWait = 0;
            } elseif ($priorityLevel === 'urgent' || $triageCategory === 'yellow') {
                $estimatedWait = max(3, (int) round(($waitingPatients * $avgMinutesPerPatient) / ($activeStaff * 2)));
            } else {
                $estimatedWait = (int) round(($waitingPatients * $avgMinutesPerPatient) / $activeStaff);
            }

            $ticket = QueueTicket::create([
                'facility_id' => $facilityId,
                'department_id' => $departmentId,
                'patient_id' => $patientId,
                'doctor_id' => $doctorId,
                'ticket_number' => $ticketNumber,
                'queue_type' => $queueType,
                'priority_level' => $priorityLevel,
                'triage_category' => $triageCategory,
                'status' => 'waiting',
                'estimated_wait_minutes' => $estimatedWait,
                'issued_at' => now(),
                'created_by_role' => $createdByRole,
                'notes' => $notes,
            ]);

            QueueEvent::create([
                'queue_ticket_id' => $ticket->id,
                'from_status' => null,
                'to_status' => 'waiting',
                'action_by' => $createdByRole,
                'remarks' => "Ticket {$ticketNumber} issued. Est. wait: {$estimatedWait} min",
                'occurred_at' => now(),
            ]);

            return $ticket;
        });
    }

    /**
     * Call the ticket to a room or counter
     */
    public function callTicket(int $ticketId, string $roomOrCounter, ?string $actor = 'Doctor'): QueueTicket
    {
        $ticket = QueueTicket::findOrFail($ticketId);
        $oldStatus = $ticket->status;

        $ticket->update([
            'status' => 'called',
            'counter_or_room' => $roomOrCounter,
            'called_at' => now(),
        ]);

        QueueEvent::create([
            'queue_ticket_id' => $ticket->id,
            'from_status' => $oldStatus,
            'to_status' => 'called',
            'action_by' => $actor,
            'room_or_counter' => $roomOrCounter,
            'remarks' => "Called to {$roomOrCounter}",
            'occurred_at' => now(),
        ]);

        return $ticket;
    }

    /**
     * Start consultation / service
     */
    public function startService(int $ticketId, ?string $actor = 'Doctor'): QueueTicket
    {
        $ticket = QueueTicket::findOrFail($ticketId);
        $oldStatus = $ticket->status;

        $ticket->update([
            'status' => 'in_service',
            'service_started_at' => now(),
        ]);

        QueueEvent::create([
            'queue_ticket_id' => $ticket->id,
            'from_status' => $oldStatus,
            'to_status' => 'in_service',
            'action_by' => $actor,
            'occurred_at' => now(),
        ]);

        return $ticket;
    }

    /**
     * Complete service
     */
    public function completeTicket(int $ticketId, ?string $actor = 'Doctor'): QueueTicket
    {
        $ticket = QueueTicket::findOrFail($ticketId);
        $oldStatus = $ticket->status;

        $ticket->update([
            'status' => 'completed',
            'service_ended_at' => now(),
        ]);

        QueueEvent::create([
            'queue_ticket_id' => $ticket->id,
            'from_status' => $oldStatus,
            'to_status' => 'completed',
            'action_by' => $actor,
            'occurred_at' => now(),
        ]);

        return $ticket;
    }

    /**
     * Skip or mark ticket as no-show
     */
    public function skipTicket(int $ticketId, ?string $reason = 'Patient did not answer call'): QueueTicket
    {
        $ticket = QueueTicket::findOrFail($ticketId);
        $oldStatus = $ticket->status;

        $ticket->update([
            'status' => 'skipped',
            'service_ended_at' => now(),
        ]);

        QueueEvent::create([
            'queue_ticket_id' => $ticket->id,
            'from_status' => $oldStatus,
            'to_status' => 'skipped',
            'remarks' => $reason,
            'occurred_at' => now(),
        ]);

        return $ticket;
    }

    /**
     * Get live board snapshot for public monitors
     */
    public function getLiveBoard(int $facilityId, ?int $departmentId = null): array
    {
        $query = QueueTicket::with(['department', 'patient'])
            ->where('facility_id', $facilityId)
            ->whereDate('created_at', today());

        if ($departmentId) {
            $query->where('department_id', $departmentId);
        }

        $allToday = (clone $query)->get();

        $calledNow = $allToday->where('status', 'called')->values();
        $inService = $allToday->where('status', 'in_service')->values();
        $waiting = $allToday->where('status', 'waiting')
            ->sortBy(function ($t) {
                // Prioritize emergency and triage category
                $priorityWeight = match ($t->priority_level) {
                    'emergency' => 1,
                    'urgent' => 2,
                    default => 3,
                };
                return sprintf('%d_%s', $priorityWeight, $t->created_at);
            })
            ->values();

        $completedCount = $allToday->where('status', 'completed')->count();

        return [
            'facility_id' => $facilityId,
            'timestamp' => now()->toISOString(),
            'called_now' => $calledNow,
            'in_service' => $inService,
            'waiting' => $waiting,
            'total_waiting' => $waiting->count(),
            'total_completed' => $completedCount,
            'total_served_today' => $allToday->count(),
        ];
    }
}
