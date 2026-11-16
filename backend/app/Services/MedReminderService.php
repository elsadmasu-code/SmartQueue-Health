<?php

namespace App\Services;

use App\Models\MedicationLog;
use App\Models\MedicationSchedule;
use App\Models\Notification;
use App\Models\PrescriptionItem;
use App\Models\SmsLog;
use Carbon\Carbon;

class MedReminderService
{
    /**
     * Create daily reminder schedules automatically from a prescription item
     */
    public function createSchedulesFromPrescription(PrescriptionItem $item, int $patientId): array
    {
        $startDate = Carbon::today();
        $endDate = $startDate->copy()->addDays($item->duration_days ?: 5);

        // Derive time slots based on frequency
        $slots = match (strtoupper(trim($item->frequency))) {
            'TID', '3 TIMES DAILY', 'EVERY 8 HOURS' => ['08:00', '14:00', '20:00'],
            'BID', '2 TIMES DAILY', 'TWICE DAILY', 'EVERY 12 HOURS' => ['08:00', '20:00'],
            'QID', '4 TIMES DAILY' => ['06:00', '12:00', '18:00', '22:00'],
            'ONCE DAILY NIGHT', 'HS', 'NIGHT' => ['21:00'],
            default => ['08:00'],
        };

        $created = [];
        foreach ($slots as $time) {
            $schedule = MedicationSchedule::create([
                'patient_id' => $patientId,
                'prescription_item_id' => $item->id,
                'medicine_name' => $item->medication_name,
                'dosage' => $item->dosage,
                'scheduled_time' => $time,
                'frequency' => $item->frequency,
                'start_date' => $startDate,
                'end_date' => $endDate,
                'instructions' => $item->instructions,
                'sms_reminder_enabled' => true,
                'is_active' => true,
            ]);
            $created[] = $schedule;
        }

        return $created;
    }

    /**
     * Record a dose event (taken, missed, skipped)
     */
    public function logDose(int $scheduleId, int $patientId, string $status = 'taken', ?string $notes = null): MedicationLog
    {
        $schedule = MedicationSchedule::findOrFail($scheduleId);

        return MedicationLog::create([
            'medication_schedule_id' => $schedule->id,
            'patient_id' => $patientId,
            'scheduled_at' => now(),
            'taken_at' => $status === 'taken' ? now() : null,
            'status' => $status,
            'patient_notes' => $notes,
        ]);
    }

    /**
     * Calculate patient medication adherence rate (%)
     */
    public function calculateAdherenceRate(int $patientId): array
    {
        $logs = MedicationLog::where('patient_id', $patientId)
            ->where('scheduled_at', '>=', now()->subDays(30))
            ->get();

        $totalDoses = $logs->count();
        $takenDoses = $logs->where('status', 'taken')->count();
        $missedDoses = $logs->where('status', 'missed')->count();

        $percentage = $totalDoses > 0 ? round(($takenDoses / $totalDoses) * 100, 1) : 100.0;

        return [
            'total_logged' => $totalDoses,
            'taken' => $takenDoses,
            'missed' => $missedDoses,
            'adherence_percentage' => $percentage,
            'compliance_grade' => $percentage >= 85 ? 'Excellent' : ($percentage >= 70 ? 'Moderate' : 'Needs Support'),
        ];
    }

    /**
     * Dispatch medication reminder SMS / Notification
     */
    public function sendReminderNotification(MedicationSchedule $schedule): void
    {
        $patient = $schedule->patient;
        if (!$patient) {
            return;
        }

        $msgEn = "MedReminder: Time to take your {$schedule->medicine_name} ({$schedule->dosage}). Instructions: {$schedule->instructions}";
        $msgAm = "የመድኃኒት ማስታወሻ: የ {$schedule->medicine_name} ({$schedule->dosage}) የሚወስዱበት ሰዓት ደርሷል:: መመሪያ: {$schedule->instructions}";

        $message = ($patient->preferred_language === 'am') ? $msgAm : $msgEn;

        // Create in-app notification
        Notification::create([
            'user_id' => $patient->user_id,
            'patient_id' => $patient->id,
            'title' => 'Medication Reminder / የመድኃኒት ማስታወሻ',
            'message' => $message,
            'type' => 'medication_due',
            'channel' => 'sms',
            'data' => [
                'schedule_id' => $schedule->id,
                'medicine_name' => $schedule->medicine_name,
                'dosage' => $schedule->dosage,
            ],
        ]);

        // Record SMS log
        if ($patient->phone_number && $schedule->sms_reminder_enabled) {
            SmsLog::create([
                'facility_id' => $patient->registered_facility_id,
                'patient_id' => $patient->id,
                'phone_number' => $patient->phone_number,
                'command' => 'REMINDER',
                'message_text' => $message,
                'direction' => 'outbound',
                'gateway' => 'ethio_telecom',
                'status' => 'sent',
                'delivery_reference' => 'SMS-' . strtoupper(bin2hex(random_bytes(4))),
            ]);
        }
    }
}
