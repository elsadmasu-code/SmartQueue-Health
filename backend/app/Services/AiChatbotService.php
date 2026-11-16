<?php

namespace App\Services;

use App\Models\ChatbotLog;
use App\Models\MedicationSchedule;
use App\Models\Patient;
use App\Models\QueueTicket;
use Illuminate\Support\Str;

class AiChatbotService
{
    /**
     * Emergency keywords that demand immediate escalation to emergency clinical care
     */
    protected array $emergencyKeywords = [
        'chest pain', 'heart attack', 'cannot breathe', 'difficulty breathing', 'severe shortness of breath',
        'unconscious', 'passed out', 'bleeding heavily', 'severe trauma', 'stroke', 'face drooping',
        'የደረት ህመም', 'መተንፈስ አቃተኝ', 'ደም እየፈሰሰ', 'ራስ መሳት', 'ስትሮክ'
    ];

    /**
     * Process message from patient or visitor with clinical safety guardrails
     */
    public function respond(string $message, ?int $userId = null, ?string $sessionId = null, string $lang = 'en'): array
    {
        $sessionId = $sessionId ?: Str::uuid()->toString();
        $normalized = mb_strtolower(trim($message));

        // 1. Red-Flag Emergency Triage Safety Check (DOC-014, DOC-019 BR-014)
        foreach ($this->emergencyKeywords as $kw) {
            if (str_contains($normalized, $kw)) {
                $emergencyResponseEn = "EMERGENCY ALERT: Based on the symptoms described ('{$kw}'), please immediately go to the nearest Emergency Department or call Ethiopian Emergency Services (907 / 939 / 911). Do not wait for an online reply or regular queue!";
                $emergencyResponseAm = "አስቸኳይ ማስጠንቀቂያ: የገለጹት ምልክት ('{$kw}') አፋጣኝ የድንገተኛ ህክምና የሚያስፈልገው ነው:: እባክዎ በአቅራቢያዎ ወደሚገኝ የድንገተኛ ህክምና ክፍል (Emergency Department) በአስቸኳይ ይሂዱ ወይም 907 / 939 / 911 ይደውሉ:: በመስመር ላይ መልስ አይጠብቁ!";

                $response = ($lang === 'am') ? $emergencyResponseAm : $emergencyResponseEn;

                ChatbotLog::create([
                    'user_id' => $userId,
                    'session_id' => $sessionId,
                    'language' => $lang,
                    'user_message' => $message,
                    'ai_response' => $response,
                    'intent' => 'emergency_escalation',
                    'is_emergency_escalated' => true,
                ]);

                return [
                    'session_id' => $sessionId,
                    'response' => $response,
                    'is_emergency' => true,
                    'intent' => 'emergency_escalation',
                ];
            }
        }

        // 2. Queue Status Query
        if (str_contains($normalized, 'queue') || str_contains($normalized, 'ticket') || str_contains($normalized, 'ተራ') || str_contains($normalized, 'ቲኬት')) {
            $patient = $userId ? Patient::where('user_id', $userId)->first() : null;
            $activeTicket = $patient ? QueueTicket::where('patient_id', $patient->id)->active()->latest()->first() : null;

            if ($activeTicket) {
                $statusText = match ($activeTicket->status) {
                    'called' => ($lang === 'am') ? "ቲኬት ቁጥርዎ {$activeTicket->ticket_number} ተጠርቷል! ወደ {$activeTicket->counter_or_room} ይግቡ::" : "Ticket {$activeTicket->ticket_number} has been CALLED! Please proceed to {$activeTicket->counter_or_room}.",
                    'in_service' => ($lang === 'am') ? "በአሁኑ ሰዓት በህክምና አገልግሎት ላይ ነዎት::" : "You are currently in service.",
                    default => ($lang === 'am') ? "ቲኬትዎ: {$activeTicket->ticket_number}:: ሁኔታው: በመጠባበቅ ላይ:: ግምታዊ የጥበቃ ጊዜ: {$activeTicket->estimated_wait_minutes} ደቂቃ::" : "Your Ticket: {$activeTicket->ticket_number}. Status: Waiting. Est. wait: ~{$activeTicket->estimated_wait_minutes} minutes.",
                };
            } else {
                $statusText = ($lang === 'am')
                    ? "ለዛሬ ንቁ የሆነ የሰልፍ ቲኬት አላገኘሁም:: በሆስፒታሉ ሪሴፕሽን ወይም በሞባይል መተግበሪያው ላይ ተራ መያዝ ይችላሉ::"
                    : "No active queue ticket found for today. You can get a ticket at the hospital reception desk or check-in screen.";
            }

            $this->logChat($userId, $sessionId, $lang, $message, $statusText, 'queue_check');
            return ['session_id' => $sessionId, 'response' => $statusText, 'is_emergency' => false, 'intent' => 'queue_check'];
        }

        // 3. Medication & MedReminder Query
        if (str_contains($normalized, 'medication') || str_contains($normalized, 'medicine') || str_contains($normalized, 'pill') || str_contains($normalized, 'መድኃኒት')) {
            $patient = $userId ? Patient::where('user_id', $userId)->first() : null;
            $activeMeds = $patient ? MedicationSchedule::where('patient_id', $patient->id)->where('is_active', true)->get() : collect();

            if ($activeMeds->isNotEmpty()) {
                $list = $activeMeds->map(fn($m) => "• {$m->medicine_name} ({$m->dosage}) at {$m->scheduled_time}")->implode("\n");
                $medResponse = ($lang === 'am')
                    ? "የተመዘገቡ ንቁ መድሃኒቶችዎ:\n{$list}\n\nማሳሰቢያ: እባክዎ መድሃኒቶችን በሀኪምዎ ትዕዛዝ መሰረት ብቻ ይውሰዱ::"
                    : "Your active prescribed medication schedules:\n{$list}\n\nNote: Please adhere strictly to your prescriber's guidelines.";
            } else {
                $medResponse = ($lang === 'am')
                    ? "ስለ መድሃኒትዎ አወሳሰድ እና ማስታወሻ መረጃ ለማግኘት የ MedReminder ክፍልን ይመልከቱ:: ያለ ሀኪም ትዕዛዝ የመድሃኒት መጠን መቀየር አይፈቀድም::"
                    : "You can track your medication schedules in the MedReminder module. Please do not change dosages without doctor supervision.";
            }

            $this->logChat($userId, $sessionId, $lang, $message, $medResponse, 'med_explanation');
            return ['session_id' => $sessionId, 'response' => $medResponse, 'is_emergency' => false, 'intent' => 'med_explanation'];
        }

        // 4. General Assistant & Hospital Navigation
        $generalEn = "Welcome to SmartQueue Health (SQHM)! I can assist you with:\n1. Checking live queue status\n2. Viewing your MedReminder schedules\n3. Booking doctor appointments\n4. Department locations and visiting hours\n\nHow can I help you today?";
        $generalAm = "እንኳን ወደ ስማርት ሰልፍ ጤና (SmartQueue Health) በደህና መጡ!\nየሚከተሉትን ማከናወን እችላለሁ:\n1. የሰልፍ እና የተራ ሁኔታን ማየት\n2. የ MedReminder መድሃኒት ማስታወሻዎችን ማየት\n3. የሀኪም ቀጠሮ ማስያዝ\n4. የሆስፒታል ክፍሎች እና የአገልግሎት መረጃዎች\n\nዛሬ በምን ልርዳዎት?";

        $reply = ($lang === 'am') ? $generalAm : $generalEn;
        $this->logChat($userId, $sessionId, $lang, $message, $reply, 'general_faq');

        return ['session_id' => $sessionId, 'response' => $reply, 'is_emergency' => false, 'intent' => 'general_faq'];
    }

    protected function logChat(?int $userId, string $sessionId, string $lang, string $message, string $reply, string $intent): void
    {
        ChatbotLog::create([
            'user_id' => $userId,
            'session_id' => $sessionId,
            'language' => $lang,
            'user_message' => $message,
            'ai_response' => $reply,
            'intent' => $intent,
            'is_emergency_escalated' => false,
        ]);
    }
}
