<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ImagingOrder;
use App\Models\ImagingReport;
use App\Models\LabOrder;
use App\Models\LabResult;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class LabRadiologyController extends Controller
{
    // --- Laboratory ---

    public function labOrders(Request $request): JsonResponse
    {
        $query = LabOrder::with(['patient', 'doctor', 'results']);

        if ($request->has('facility_id')) {
            $query->where('facility_id', $request->facility_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return response()->json($query->latest()->paginate(20));
    }

    public function storeLabOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'patient_id' => 'required|exists:patients,id',
            'encounter_id' => 'nullable|exists:encounters,id',
            'doctor_id' => 'required|exists:employees,id',
            'test_type' => 'required|string',
            'priority' => 'nullable|in:normal,urgent,stat',
            'specimen_type' => 'nullable|string',
            'clinical_notes' => 'nullable|string',
        ]);

        $orderNumber = 'LAB-' . date('Ymd') . '-' . strtoupper(Str::random(4));
        $barcode = 'SPEC-' . strtoupper(Str::random(8));

        $order = LabOrder::create(array_merge($validated, [
            'order_number' => $orderNumber,
            'specimen_barcode' => $barcode,
            'status' => 'ordered',
        ]));

        return response()->json([
            'message' => 'Laboratory test ordered successfully',
            'order' => $order->load(['patient', 'doctor']),
        ], 201);
    }

    public function recordLabResults(Request $request, int $orderId): JsonResponse
    {
        $validated = $request->validate([
            'technician_id' => 'nullable|exists:employees,id',
            'verified_by_id' => 'nullable|exists:employees,id',
            'results' => 'required|array|min:1',
            'results.*.test_name' => 'required|string',
            'results.*.result_value' => 'required|string',
            'results.*.unit' => 'nullable|string',
            'results.*.reference_range' => 'nullable|string',
            'results.*.flag' => 'nullable|in:normal,low,high,critical',
            'results.*.comments' => 'nullable|string',
        ]);

        $order = LabOrder::findOrFail($orderId);

        foreach ($validated['results'] as $res) {
            LabResult::create([
                'lab_order_id' => $order->id,
                'test_name' => $res['test_name'],
                'result_value' => $res['result_value'],
                'unit' => $res['unit'] ?? null,
                'reference_range' => $res['reference_range'] ?? null,
                'flag' => $res['flag'] ?? 'normal',
                'technician_id' => $validated['technician_id'] ?? null,
                'verified_by_id' => $validated['verified_by_id'] ?? null,
                'verified_at' => now(),
                'comments' => $res['comments'] ?? null,
            ]);
        }

        $order->update([
            'status' => 'completed',
            'collected_at' => $order->collected_at ?: now(),
        ]);

        return response()->json([
            'message' => 'Lab test results recorded and verified',
            'order' => $order->load('results'),
        ]);
    }

    // --- Radiology ---

    public function imagingOrders(Request $request): JsonResponse
    {
        $query = ImagingOrder::with(['patient', 'doctor', 'report']);

        if ($request->has('facility_id')) {
            $query->where('facility_id', $request->facility_id);
        }

        return response()->json($query->latest()->paginate(20));
    }

    public function storeImagingOrder(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'patient_id' => 'required|exists:patients,id',
            'encounter_id' => 'nullable|exists:encounters,id',
            'doctor_id' => 'required|exists:employees,id',
            'modality' => 'required|string', // X-Ray, Ultrasound, CT, MRI
            'body_part' => 'required|string',
            'clinical_indication' => 'nullable|string',
        ]);

        $orderNumber = 'RAD-' . date('Ymd') . '-' . strtoupper(Str::random(4));

        $order = ImagingOrder::create(array_merge($validated, [
            'order_number' => $orderNumber,
            'status' => 'ordered',
        ]));

        return response()->json(['message' => 'Imaging study ordered', 'order' => $order], 201);
    }

    public function recordImagingReport(Request $request, int $orderId): JsonResponse
    {
        $validated = $request->validate([
            'radiologist_id' => 'required|exists:employees,id',
            'findings' => 'required|string',
            'impression' => 'required|string',
            'recommendations' => 'nullable|string',
        ]);

        $order = ImagingOrder::findOrFail($orderId);

        $report = ImagingReport::create([
            'imaging_order_id' => $order->id,
            'radiologist_id' => $validated['radiologist_id'],
            'findings' => $validated['findings'],
            'impression' => $validated['impression'],
            'recommendations' => $validated['recommendations'] ?? null,
            'is_verified' => true,
            'verified_at' => now(),
        ]);

        $order->update(['status' => 'completed']);

        return response()->json(['message' => 'Radiology report filed', 'report' => $report]);
    }
}
