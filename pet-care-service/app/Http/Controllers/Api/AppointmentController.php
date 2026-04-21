<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Customer;
use App\Models\User;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index(Request $request)
    {
        $token = $request->bearerToken();
        $userId = $this->getUserIdFromToken($token);

        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::find($userId);
        $appointments = Appointment::query();

        if ($user->role === 'customer') {
            $customer = $user->customer;
            $appointments = $appointments->where('customer_id', $customer->id);
        }

        $appointments = $appointments->with('service', 'pet', 'staff', 'customer')
            ->orderBy('scheduled_at', 'desc')
            ->get()
            ->map(function ($apt) {
                $scheduledAt = $apt->scheduled_at;
                return [
                    'id' => $apt->id,
                    'pet' => [
                        'id' => $apt->pet->id,
                        'name' => $apt->pet->name,
                        'type' => $apt->pet->type
                    ],
                    'service' => [
                        'id' => $apt->service->id,
                        'name' => $apt->service->name,
                        'price' => (int) $apt->service->price
                    ],
                    'staff_name' => $apt->staff ? $apt->staff->user->name : 'Chưa gán',
                    'appointment_date' => $scheduledAt->format('Y-m-d'),
                    'appointment_time' => $scheduledAt->format('H:i'),
                    'status' => $apt->status,
                    'notes' => $apt->notes
                ];
            });

        return response()->json(['data' => $appointments]);
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
            return response()->json(['message' => 'Only customers can create appointments'], 403);
        }

        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'service_id' => 'required|exists:services,id',
            'appointment_date' => 'required|date',
            'appointment_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string'
        ]);

        $customer = $user->customer;
        $service = \App\Models\Service::find($validated['service_id']);

        // Verify pet belongs to customer
        $pet = \App\Models\Pet::find($validated['pet_id']);
        if ($pet->customer_id !== $customer->id) {
            return response()->json(['message' => 'This pet does not belong to you'], 403);
        }

        // Combine date and time
        $scheduledAt = $validated['appointment_date'] . ' ' . $validated['appointment_time'];

        $appointment = Appointment::create([
            'customer_id' => $customer->id,
            'pet_id' => $validated['pet_id'],
            'service_id' => $validated['service_id'],
            'scheduled_at' => $scheduledAt,
            'status' => 'pending',
            'notes' => $validated['notes'] ?? null,
            'price' => $service->price
        ]);

        $appointment->load('service', 'pet', 'staff', 'customer');

        return response()->json([
            'data' => [
                'id' => $appointment->id,
                'pet' => [
                    'id' => $appointment->pet->id,
                    'name' => $appointment->pet->name,
                    'type' => $appointment->pet->type
                ],
                'service' => [
                    'id' => $appointment->service->id,
                    'name' => $appointment->service->name,
                    'price' => (int) $appointment->service->price
                ],
                'staff_name' => $appointment->staff ? $appointment->staff->user->name : 'Chưa gán',
                'appointment_date' => $appointment->scheduled_at->format('Y-m-d'),
                'appointment_time' => $appointment->scheduled_at->format('H:i'),
                'status' => $appointment->status,
                'notes' => $appointment->notes
            ],
            'message' => 'Đặt lịch hẹn thành công'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $token = $request->bearerToken();
        $userId = $this->getUserIdFromToken($token);

        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        $user = User::find($userId);

        if ($user->role === 'customer' && $appointment->customer_id !== $user->customer->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'status' => 'nullable|in:pending,confirmed,processing,completed,cancelled',
            'scheduled_at' => 'nullable|date|after:now',
            'notes' => 'nullable|string'
        ]);

        $appointment->update($validated);

        return response()->json(['message' => 'Cập nhật lịch hẹn thành công']);
    }

    public function destroy(Request $request, $id)
    {
        $token = $request->bearerToken();
        $userId = $this->getUserIdFromToken($token);

        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $appointment = Appointment::find($id);

        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found'], 404);
        }

        $user = User::find($userId);

        if ($user->role === 'customer' && $appointment->customer_id !== $user->customer->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $appointment->update(['status' => 'cancelled']);

        return response()->json(['message' => 'Xóa lịch hẹn thành công']);
    }

    private function getUserIdFromToken($token)
    {
        if (!$token) return null;

        // Extract user ID from token format: user_ID_timestamp
        preg_match('/user_(\d+)_/', $token, $matches);
        return $matches[1] ?? null;
    }
}
