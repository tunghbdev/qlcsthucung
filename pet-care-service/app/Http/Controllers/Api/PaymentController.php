<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Invoice;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function index(Request $request)
    {
        $token = $request->bearerToken();
        $userId = $this->getUserIdFromToken($token);

        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::find($userId);
        $payments = Payment::query();

        if ($user->role === 'customer') {
            $customer = $user->customer;
            $payments = $payments->where('customer_id', $customer->id);
        }

        $payments = $payments->with('invoice')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($payment) {
                return [
                    'id' => $payment->id,
                    'invoice_number' => $payment->invoice->invoice_number,
                    'amount' => (float) $payment->amount,
                    'payment_method' => $payment->payment_method,
                    'status' => $payment->status,
                    'transaction_id' => $payment->transaction_id,
                    'paid_at' => $payment->paid_at?->format('Y-m-d H:i:s'),
                    'created_at' => $payment->created_at->format('Y-m-d H:i:s')
                ];
            });

        return response()->json(['data' => $payments]);
    }

    public function store(Request $request)
    {
        $token = $request->bearerToken();
        $userId = $this->getUserIdFromToken($token);

        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::find($userId);

        if ($user->role !== 'customer') {
            return response()->json(['message' => 'Only customers can make payments'], 403);
        }

        $validated = $request->validate([
            'invoice_id' => 'required|exists:invoices,id',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,credit_card,debit_card,bank_transfer,e_wallet'
        ]);

        $invoice = Invoice::find($validated['invoice_id']);

        if ($invoice->customer_id !== $user->customer->id) {
            return response()->json(['message' => 'This invoice does not belong to you'], 403);
        }

        // Check if payment amount doesn't exceed remaining amount
        $paidAmount = $invoice->payments->where('status', 'completed')->sum('amount');
        $remainingAmount = $invoice->total_amount - $paidAmount;

        if ($validated['amount'] > $remainingAmount) {
            return response()->json([
                'message' => 'Số tiền thanh toán vượt quá số dư',
                'remaining_amount' => (float) $remainingAmount
            ], 422);
        }

        $payment = Payment::create([
            'invoice_id' => $validated['invoice_id'],
            'customer_id' => $user->customer->id,
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'status' => 'completed',
            'paid_at' => now()
        ]);

        return response()->json([
            'data' => [
                'id' => $payment->id,
                'invoice_number' => $invoice->invoice_number,
                'amount' => (float) $payment->amount,
                'payment_method' => $payment->payment_method,
                'status' => $payment->status,
                'paid_at' => $payment->paid_at->format('Y-m-d H:i:s'),
                'created_at' => $payment->created_at->format('Y-m-d H:i:s')
            ],
            'message' => 'Thanh toán thành công'
        ], 201);
    }

    private function getUserIdFromToken($token)
    {
        if (!$token) return null;
        preg_match('/user_(\d+)_/', $token, $matches);
        return $matches[1] ?? null;
    }
}
