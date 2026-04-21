<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Appointment;
use App\Models\User;
use Illuminate\Http\Request;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        $token = $request->bearerToken();
        $userId = $this->getUserIdFromToken($token);

        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::find($userId);
        $invoices = Invoice::query();

        if ($user->role === 'customer') {
            $customer = $user->customer;
            $invoices = $invoices->where('customer_id', $customer->id);
        }

        $invoices = $invoices->with('appointment', 'customer', 'payments')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($invoice) {
                $paidAmount = $invoice->payments->where('status', 'completed')->sum('amount');
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'appointment_id' => $invoice->appointment_id,
                    'customer_name' => $invoice->customer->user->name,
                    'subtotal' => (float) $invoice->subtotal,
                    'discount_amount' => (float) $invoice->discount_amount,
                    'tax_amount' => (float) $invoice->tax_amount,
                    'total_amount' => (float) $invoice->total_amount,
                    'paid_amount' => (float) $paidAmount,
                    'remaining_amount' => (float) ($invoice->total_amount - $paidAmount),
                    'status' => $paidAmount >= $invoice->total_amount ? 'paid' : 'unpaid',
                    'notes' => $invoice->notes,
                    'created_at' => $invoice->created_at->format('Y-m-d H:i:s'),
                    'appointment' => $invoice->appointment ? [
                        'id' => $invoice->appointment->id,
                        'pet_name' => $invoice->appointment->pet->name,
                        'service_name' => $invoice->appointment->service->name
                    ] : null
                ];
            });

        return response()->json(['data' => $invoices]);
    }

    public function show(Request $request, $id)
    {
        $token = $request->bearerToken();
        $userId = $this->getUserIdFromToken($token);

        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $invoice = Invoice::with('appointment', 'customer', 'payments')->find($id);

        if (!$invoice) {
            return response()->json(['message' => 'Invoice not found'], 404);
        }

        $user = User::find($userId);

        if ($user->role === 'customer' && $invoice->customer_id !== $user->customer->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $paidAmount = $invoice->payments->where('status', 'completed')->sum('amount');

        return response()->json([
            'data' => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'appointment_id' => $invoice->appointment_id,
                'customer_name' => $invoice->customer->user->name,
                'subtotal' => (float) $invoice->subtotal,
                'discount_amount' => (float) $invoice->discount_amount,
                'tax_amount' => (float) $invoice->tax_amount,
                'total_amount' => (float) $invoice->total_amount,
                'paid_amount' => (float) $paidAmount,
                'remaining_amount' => (float) ($invoice->total_amount - $paidAmount),
                'status' => $paidAmount >= $invoice->total_amount ? 'paid' : 'unpaid',
                'notes' => $invoice->notes,
                'created_at' => $invoice->created_at->format('Y-m-d H:i:s'),
                'appointment' => $invoice->appointment ? [
                    'id' => $invoice->appointment->id,
                    'pet_name' => $invoice->appointment->pet->name,
                    'service_name' => $invoice->appointment->service->name
                ] : null,
                'payments' => $invoice->payments->map(function ($payment) {
                    return [
                        'id' => $payment->id,
                        'amount' => (float) $payment->amount,
                        'payment_method' => $payment->payment_method,
                        'status' => $payment->status,
                        'transaction_id' => $payment->transaction_id,
                        'paid_at' => $payment->paid_at?->format('Y-m-d H:i:s'),
                        'created_at' => $payment->created_at->format('Y-m-d H:i:s')
                    ];
                })
            ]
        ]);
    }

    public function store(Request $request)
    {
        $token = $request->bearerToken();
        $userId = $this->getUserIdFromToken($token);

        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::find($userId);

        if ($user->role !== 'admin' && $user->role !== 'staff') {
            return response()->json(['message' => 'Only admin or staff can create invoices'], 403);
        }

        $validated = $request->validate([
            'appointment_id' => 'required|exists:appointments,id',
            'subtotal' => 'required|numeric|min:0',
            'discount_amount' => 'nullable|numeric|min:0',
            'tax_amount' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string'
        ]);

        $appointment = Appointment::find($validated['appointment_id']);
        $customer = $appointment->customer;

        $discount = $validated['discount_amount'] ?? 0;
        $tax = $validated['tax_amount'] ?? 0;
        $totalAmount = $validated['subtotal'] - $discount + $tax;

        $invoiceNumber = 'INV-' . date('Ymd') . '-' . rand(1000, 9999);

        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'appointment_id' => $validated['appointment_id'],
            'customer_id' => $customer->id,
            'subtotal' => $validated['subtotal'],
            'discount_amount' => $discount,
            'tax_amount' => $tax,
            'total_amount' => $totalAmount,
            'notes' => $validated['notes'] ?? null
        ]);

        $invoice->load('appointment', 'customer', 'payments');

        return response()->json([
            'data' => [
                'id' => $invoice->id,
                'invoice_number' => $invoice->invoice_number,
                'appointment_id' => $invoice->appointment_id,
                'customer_name' => $invoice->customer->user->name,
                'subtotal' => (float) $invoice->subtotal,
                'discount_amount' => (float) $invoice->discount_amount,
                'tax_amount' => (float) $invoice->tax_amount,
                'total_amount' => (float) $invoice->total_amount,
                'paid_amount' => 0,
                'remaining_amount' => (float) $invoice->total_amount,
                'status' => 'unpaid',
                'notes' => $invoice->notes,
                'created_at' => $invoice->created_at->format('Y-m-d H:i:s')
            ],
            'message' => 'Tạo hóa đơn thành công'
        ], 201);
    }

    private function getUserIdFromToken($token)
    {
        if (!$token) return null;
        preg_match('/user_(\d+)_/', $token, $matches);
        return $matches[1] ?? null;
    }
}
