<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Payment;
use App\Services\EthiopianPaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class BillingController extends Controller
{
    public function __construct(protected EthiopianPaymentService $paymentService) {}

    public function invoices(Request $request): JsonResponse
    {
        $query = Invoice::with(['items', 'patient', 'payments', 'claim']);

        if ($request->has('facility_id')) {
            $query->where('facility_id', $request->facility_id);
        }

        if ($request->has('patient_id')) {
            $query->where('patient_id', $request->patient_id);
        }

        if ($request->has('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        return response()->json($query->latest()->paginate(20));
    }

    public function storeInvoice(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'facility_id' => 'required|exists:facilities,id',
            'patient_id' => 'required|exists:patients,id',
            'encounter_id' => 'nullable|exists:encounters,id',
            'items' => 'required|array|min:1',
            'items.*.service_type' => 'required|string', // consultation, lab, imaging, pharmacy, bed
            'items.*.description' => 'required|string',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ]);

        $invoice = DB::transaction(function () use ($validated) {
            $invNumber = 'INV-' . date('Ymd') . '-' . strtoupper(Str::random(4));
            $subtotal = 0.00;

            foreach ($validated['items'] as $item) {
                $subtotal += ($item['quantity'] * $item['unit_price']);
            }

            $inv = Invoice::create([
                'facility_id' => $validated['facility_id'],
                'patient_id' => $validated['patient_id'],
                'encounter_id' => $validated['encounter_id'] ?? null,
                'invoice_number' => $invNumber,
                'subtotal' => $subtotal,
                'discount' => 0.00,
                'tax' => 0.00,
                'total_amount' => $subtotal,
                'paid_amount' => 0.00,
                'balance' => $subtotal,
                'payment_status' => 'unpaid',
            ]);

            foreach ($validated['items'] as $item) {
                InvoiceItem::create([
                    'invoice_id' => $inv->id,
                    'service_type' => $item['service_type'],
                    'description' => $item['description'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'total_price' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            return $inv;
        });

        return response()->json([
            'message' => 'Invoice created successfully',
            'invoice' => $invoice->load(['items', 'patient']),
        ], 201);
    }

    public function pay(Request $request, int $invoiceId): JsonResponse
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'payment_method' => 'required|in:cash,telebirr,cbe_birr,chapa,insurance,bank_transfer',
            'customer_phone' => 'nullable|string',
        ]);

        $cashierId = $request->user()?->employee?->id;

        $payment = $this->paymentService->processPayment(
            invoiceId: $invoiceId,
            amount: (float) $validated['amount'],
            paymentMethod: $validated['payment_method'],
            cashierId: $cashierId,
            customerPhone: $validated['customer_phone'] ?? null
        );

        return response()->json([
            'message' => 'Payment recorded and confirmed',
            'payment' => $payment->load('invoice'),
        ]);
    }

    public function submitClaim(Request $request, int $invoiceId): JsonResponse
    {
        $validated = $request->validate([
            'policy_number' => 'required|string',
            'provider' => 'nullable|string',
        ]);

        $claim = $this->paymentService->submitInsuranceClaim(
            $invoiceId,
            $validated['policy_number'],
            $validated['provider'] ?? 'Ethiopian CBHI'
        );

        return response()->json([
            'message' => 'Insurance claim processed and verified',
            'claim' => $claim,
        ]);
    }
}
