<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Dispensation;
use App\Models\Medication;
use App\Models\Prescription;
use App\Models\PrescriptionItem;
use App\Models\StockBatch;
use App\Services\MedReminderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class PharmacyController extends Controller
{
    public function __construct(protected MedReminderService $medReminderService) {}

    public function medications(Request $request): JsonResponse
    {
        $query = Medication::with(['stockBatches'])->where('is_active', true);

        if ($search = $request->query('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('generic_name', 'like', "%{$search}%")
                  ->orWhere('brand_name', 'like', "%{$search}%")
                  ->orWhere('category', 'like', "%{$search}%");
            });
        }

        return response()->json($query->paginate(30));
    }

    public function prescriptions(Request $request): JsonResponse
    {
        $query = Prescription::with(['items.medication', 'patient', 'doctor', 'dispensation']);

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

    public function storePrescription(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'patient_id' => 'required|exists:patients,id',
            'encounter_id' => 'nullable|exists:encounters,id',
            'doctor_id' => 'required|exists:employees,id',
            'diagnosis' => 'nullable|string',
            'notes' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.medication_id' => 'nullable|exists:medications,id',
            'items.*.medication_name' => 'required|string',
            'items.*.dosage' => 'required|string',
            'items.*.frequency' => 'required|string',
            'items.*.duration_days' => 'required|integer|min:1',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.instructions' => 'nullable|string',
        ]);

        $prescription = DB::transaction(function () use ($validated) {
            $code = 'RX-' . date('Ymd') . '-' . strtoupper(Str::random(5));

            $rx = Prescription::create([
                'facility_id' => $validated['facility_id'],
                'patient_id' => $validated['patient_id'],
                'encounter_id' => $validated['encounter_id'] ?? null,
                'doctor_id' => $validated['doctor_id'],
                'prescription_code' => $code,
                'diagnosis' => $validated['diagnosis'] ?? null,
                'status' => 'pending',
                'notes' => $validated['notes'] ?? null,
            ]);

            foreach ($validated['items'] as $itemData) {
                $item = PrescriptionItem::create([
                    'prescription_id' => $rx->id,
                    'medication_id' => $itemData['medication_id'] ?? null,
                    'medication_name' => $itemData['medication_name'],
                    'dosage' => $itemData['dosage'],
                    'frequency' => $itemData['frequency'],
                    'duration_days' => $itemData['duration_days'],
                    'quantity' => $itemData['quantity'],
                    'instructions' => $itemData['instructions'] ?? null,
                ]);

                // Automatically generate patient MedReminder schedules!
                $this->medReminderService->createSchedulesFromPrescription($item, $validated['patient_id']);
            }

            return $rx;
        });

        return response()->json([
            'message' => 'Prescription created and MedReminder schedules initialized',
            'prescription' => $prescription->load(['items', 'patient', 'doctor']),
        ], 201);
    }

    public function dispense(Request $request, int $prescriptionId): JsonResponse
    {
        $validated = $request->validate([
            'pharmacist_id' => 'required|exists:employees,id',
            'counseling_notes' => 'nullable|string',
        ]);

        $dispensation = DB::transaction(function () use ($prescriptionId, $validated) {
            $rx = Prescription::with('items.medication')->findOrFail($prescriptionId);

            $totalAmount = 0.00;
            foreach ($rx->items as $item) {
                $item->update([
                    'is_dispensed' => true,
                    'dispensed_quantity' => $item->quantity,
                ]);

                if ($item->medication_id) {
                    $med = Medication::find($item->medication_id);
                    if ($med) {
                        $totalAmount += ($med->unit_price * $item->quantity);

                        // Deduct stock
                        $batch = StockBatch::where('facility_id', $rx->facility_id)
                            ->where('medication_id', $med->id)
                            ->where('quantity_in_stock', '>', 0)
                            ->orderBy('expiry_date')
                            ->first();

                        if ($batch) {
                            $batch->decrement('quantity_in_stock', min($batch->quantity_in_stock, $item->quantity));
                        }
                    }
                }
            }

            $disp = Dispensation::create([
                'facility_id' => $rx->facility_id,
                'prescription_id' => $rx->id,
                'patient_id' => $rx->patient_id,
                'pharmacist_id' => $validated['pharmacist_id'],
                'total_amount' => $totalAmount,
                'counseling_notes' => $validated['counseling_notes'] ?? 'Medication counseling completed with patient.',
                'dispensed_at' => now(),
            ]);

            $rx->update(['status' => 'dispensed']);

            return $disp;
        });

        return response()->json([
            'message' => 'Prescription dispensed successfully',
            'dispensation' => $dispensation,
        ]);
    }
}
