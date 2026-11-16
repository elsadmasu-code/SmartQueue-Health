<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Employee;
use App\Models\Facility;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FacilityController extends Controller
{
    public function index(): JsonResponse
    {
        $facilities = Facility::with(['departments'])
            ->where('is_active', true)
            ->get();

        return response()->json(['facilities' => $facilities]);
    }

    public function show(int $id): JsonResponse
    {
        $facility = Facility::with(['departments.employees', 'wards.beds'])->findOrFail($id);

        return response()->json(['facility' => $facility]);
    }

    public function departments(int $facilityId): JsonResponse
    {
        $departments = Department::where('facility_id', $facilityId)
            ->where('is_active', true)
            ->with(['employees' => fn($q) => $q->where('is_on_duty', true)])
            ->get();

        return response()->json(['departments' => $departments]);
    }

    public function employees(Request $request, int $facilityId): JsonResponse
    {
        $query = Employee::where('facility_id', $facilityId)->with(['department']);

        if ($request->has('role')) {
            $query->where('role', $request->role);
        }

        if ($request->has('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        return response()->json(['employees' => $query->get()]);
    }
}
