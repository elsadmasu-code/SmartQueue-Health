<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Encounter;
use App\Services\EthiopianHealthInteropService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class InteropController extends Controller
{
    public function __construct(protected EthiopianHealthInteropService $interopService) {}

    public function verifyMfr(Request $request): JsonResponse
    {
        $request->validate(['mfr_code' => 'required|string']);
        $result = $this->interopService->verifyMfrFacility($request->mfr_code);

        return response()->json($result);
    }

    public function exportDhis2(Request $request, int $facilityId): JsonResponse
    {
        $year = $request->query('year', (int) date('Y'));
        $month = $request->query('month', (int) date('m'));

        $data = $this->interopService->exportDhis2Indicators($facilityId, (int) $year, (int) $month);

        return response()->json([
            'message' => 'DHIS2 Monthly Indicators generated successfully',
            'report' => $data,
        ]);
    }

    public function exportFhir(int $encounterId): JsonResponse
    {
        $encounter = Encounter::with(['patient', 'facility'])->findOrFail($encounterId);
        $bundle = $this->interopService->exportFhirEncounterBundle($encounter);

        return response()->json($bundle);
    }

    public function syncOfflineBatch(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'device_id' => 'required|string',
            'facility_id' => 'required|exists:facilities,id',
            'records' => 'required|array',
            'records.*.client_uuid' => 'required|string',
            'records.*.entity_type' => 'required|string|in:patient,vitals,queue_ticket,medication_taken',
            'records.*.payload' => 'required|array',
            'records.*.client_timestamp' => 'nullable|string',
        ]);

        $res = $this->interopService->processOfflineSyncBatch(
            deviceId: $validated['device_id'],
            facilityId: (int) $validated['facility_id'],
            records: $validated['records']
        );

        return response()->json($res);
    }
}
