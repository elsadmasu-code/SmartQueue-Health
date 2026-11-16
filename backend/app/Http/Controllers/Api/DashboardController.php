<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Admission;
use App\Models\Appointment;
use App\Models\Bed;
use App\Models\Encounter;
use App\Models\LabOrder;
use App\Models\Patient;
use App\Models\Payment;
use App\Models\Prescription;
use App\Models\QueueTicket;
use App\Models\StockBatch;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats(Request $request, int $facilityId): JsonResponse
    {
        $today = today();

        // 1. Queue & Patient Metrics
        $totalPatients = Patient::count();
        $todayVisits = QueueTicket::where('facility_id', $facilityId)->whereDate('created_at', $today)->count();
        $waitingPatients = QueueTicket::where('facility_id', $facilityId)->whereDate('created_at', $today)->where('status', 'waiting')->count();
        $completedEncounters = Encounter::where('facility_id', $facilityId)->whereDate('created_at', $today)->where('status', 'completed')->count();
        $avgWaitTime = (int) round(QueueTicket::where('facility_id', $facilityId)->whereDate('created_at', $today)->avg('estimated_wait_minutes') ?: 12);

        // 2. Inpatient Bed Occupancy
        $totalBeds = Bed::whereHas('ward', fn($q) => $q->where('facility_id', $facilityId))->count();
        $occupiedBeds = Bed::whereHas('ward', fn($q) => $q->where('facility_id', $facilityId))->where('status', 'occupied')->count();
        $availableBeds = max(0, $totalBeds - $occupiedBeds);
        $occupancyRate = $totalBeds > 0 ? round(($occupiedBeds / $totalBeds) * 100, 1) : 0;

        // 3. Financial Metrics
        $todayRevenue = Payment::where('facility_id', $facilityId)->whereDate('paid_at', $today)->where('status', 'successful')->sum('amount');
        $monthlyRevenue = Payment::where('facility_id', $facilityId)->whereMonth('paid_at', now()->month)->whereYear('paid_at', now()->year)->where('status', 'successful')->sum('amount');

        // 4. Pharmacy & Labs
        $pendingPrescriptions = Prescription::where('facility_id', $facilityId)->where('status', 'pending')->count();
        $pendingLabs = LabOrder::where('facility_id', $facilityId)->where('status', 'ordered')->count();
        $lowStockAlerts = StockBatch::where('facility_id', $facilityId)->where('quantity_in_stock', '<=', 20)->count();

        // 5. Emergency Volume
        $emergencyToday = QueueTicket::where('facility_id', $facilityId)->whereDate('created_at', $today)->where('queue_type', 'emergency')->count();

        return response()->json([
            'facility_id' => $facilityId,
            'summary' => [
                'total_registered_patients' => $totalPatients,
                'today_visits' => $todayVisits,
                'waiting_patients' => $waitingPatients,
                'completed_encounters' => $completedEncounters,
                'avg_waiting_minutes' => $avgWaitTime,
                'emergency_cases_today' => $emergencyToday,
                'total_beds' => $totalBeds,
                'occupied_beds' => $occupiedBeds,
                'available_beds' => $availableBeds,
                'bed_occupancy_percentage' => $occupancyRate,
                'today_revenue_etb' => round($todayRevenue, 2),
                'monthly_revenue_etb' => round($monthlyRevenue, 2),
                'pending_prescriptions' => $pendingPrescriptions,
                'pending_labs' => $pendingLabs,
                'low_stock_alerts' => $lowStockAlerts,
            ],
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
