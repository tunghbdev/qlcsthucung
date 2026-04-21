<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Customer;
use App\Models\Appointment;
use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function stats(Request $request)
    {
        $token = $request->bearerToken();
        $userId = $this->getUserIdFromToken($token);

        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::find($userId);

        if ($user->role !== 'admin') {
            return response()->json(['message' => 'Only admins can access dashboard stats'], 403);
        }

        // Calculate stats
        $totalCustomers = Customer::count();
        $totalAppointments = Appointment::count();
        $completedAppointments = Appointment::where('status', 'completed')->count();
        $totalRevenue = Invoice::sum('total_amount');
        $paidRevenue = Payment::where('status', 'completed')->sum('amount');
        $pendingRevenue = $totalRevenue - $paidRevenue;
        $todayAppointments = Appointment::whereDate('scheduled_at', now())->count();

        // Upcoming appointments (next 7 days)
        $upcomingAppointments = Appointment::where('status', '!=', 'completed')
            ->where('scheduled_at', '>=', now())
            ->where('scheduled_at', '<=', now()->addDays(7))
            ->with('pet', 'service', 'customer')
            ->orderBy('scheduled_at')
            ->limit(5)
            ->get()
            ->map(function ($apt) {
                return [
                    'id' => $apt->id,
                    'pet_name' => $apt->pet->name,
                    'service_name' => $apt->service->name,
                    'customer_name' => $apt->customer->user->name,
                    'scheduled_at' => $apt->scheduled_at->format('Y-m-d H:i'),
                    'status' => $apt->status
                ];
            });

        // Recent invoices
        $recentInvoices = Invoice::with('customer')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get()
            ->map(function ($invoice) {
                $paid = Payment::where('invoice_id', $invoice->id)->where('status', 'completed')->sum('amount');
                return [
                    'id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number,
                    'customer_name' => $invoice->customer->user->name,
                    'total_amount' => (float) $invoice->total_amount,
                    'paid_amount' => (float) $paid,
                    'status' => $paid >= $invoice->total_amount ? 'paid' : 'unpaid',
                    'created_at' => $invoice->created_at->format('Y-m-d H:i')
                ];
            });

        return response()->json([
            'data' => [
                'totalCustomers' => $totalCustomers,
                'totalAppointments' => $totalAppointments,
                'completedAppointments' => $completedAppointments,
                'totalRevenue' => (float) $totalRevenue,
                'paidRevenue' => (float) $paidRevenue,
                'pendingRevenue' => (float) $pendingRevenue,
                'todayAppointments' => $todayAppointments,
                'upcomingAppointments' => $upcomingAppointments,
                'recentInvoices' => $recentInvoices
            ]
        ]);
    }

    private function getUserIdFromToken($token)
    {
        if (!$token) return null;
        preg_match('/user_(\d+)_/', $token, $matches);
        return $matches[1] ?? null;
    }
}
