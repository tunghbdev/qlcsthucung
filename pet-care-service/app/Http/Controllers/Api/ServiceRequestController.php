<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ServiceRequest;
use App\Models\Appointment;
use App\Models\Customer;
use Illuminate\Http\Request;

class ServiceRequestController extends Controller
{
    // List all requests (admin) or own requests (customer)
    public function index(Request $request)
    {
        $token = $request->bearerToken();
        $userId = $this->getUserIdFromToken($token);
        $user = \App\Models\User::find($userId);

        if (!$user) {
            return response()->json(['message' => 'User not found'], 401);
        }

        if ($user->role === 'admin') {
            // Admin sees all requests
            $requests = ServiceRequest::with(['customer.user', 'pet', 'service', 'reviewer'])
                ->orderBy('created_at', 'desc')
                ->get();
        } else {
            // Customer sees only own requests
            $customer = Customer::where('user_id', $userId)->first();
            if (!$customer) {
                return response()->json(['message' => 'Customer not found'], 404);
            }
            
            $requests = ServiceRequest::where('customer_id', $customer->id)
                ->with(['pet', 'service'])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return response()->json(['data' => $requests]);
    }

    // Create service request (customer)
    public function store(Request $request)
    {
        $token = $request->bearerToken();
        $userId = $this->getUserIdFromToken($token);
        $user = \App\Models\User::find($userId);

        if (!$user || $user->role !== 'customer') {
            return response()->json(['message' => 'Only customers can create service requests'], 403);
        }

        $customer = Customer::where('user_id', $userId)->first();
        if (!$customer) {
            return response()->json(['message' => 'Customer not found'], 404);
        }

        $validated = $request->validate([
            'pet_id' => 'required|exists:pets,id',
            'service_id' => 'required|exists:services,id',
            'requested_date' => 'required|date|after:today',
            'requested_time' => 'required|date_format:H:i',
            'notes' => 'nullable|string'
        ]);

        // Check if pet belongs to customer
        $pet = \App\Models\Pet::find($validated['pet_id']);
        if ($pet->customer_id !== $customer->id) {
            return response()->json(['message' => 'Pet not found'], 404);
        }

        $serviceRequest = ServiceRequest::create([
            'customer_id' => $customer->id,
            'pet_id' => $validated['pet_id'],
            'service_id' => $validated['service_id'],
            'requested_date' => $validated['requested_date'],
            'requested_time' => $validated['requested_time'],
            'notes' => $validated['notes'],
            'status' => 'pending'
        ]);

        return response()->json(['data' => $serviceRequest], 201);
    }

    // Get single request
    public function show(Request $request, $id)
    {
        $serviceRequest = ServiceRequest::with(['customer.user', 'pet', 'service', 'reviewer'])
            ->find($id);

        if (!$serviceRequest) {
            return response()->json(['message' => 'Request not found'], 404);
        }

        return response()->json(['data' => $serviceRequest]);
    }

    // Update request notes (customer, only if pending)
    public function update(Request $request, $id)
    {
        $serviceRequest = ServiceRequest::find($id);

        if (!$serviceRequest) {
            return response()->json(['message' => 'Request not found'], 404);
        }

        if ($serviceRequest->status !== 'pending') {
            return response()->json(['message' => 'Can only update pending requests'], 400);
        }

        $token = $request->bearerToken();
        $userId = $this->getUserIdFromToken($token);
        $user = \App\Models\User::find($userId);

        $customer = Customer::where('user_id', $userId)->first();
        if (!$customer || $serviceRequest->customer_id !== $customer->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'notes' => 'nullable|string'
        ]);

        $serviceRequest->update($validated);
        return response()->json(['data' => $serviceRequest]);
    }

    // Cancel request (customer)
    public function destroy(Request $request, $id)
    {
        $serviceRequest = ServiceRequest::find($id);

        if (!$serviceRequest) {
            return response()->json(['message' => 'Request not found'], 404);
        }

        if ($serviceRequest->status !== 'pending') {
            return response()->json(['message' => 'Can only cancel pending requests'], 400);
        }

        $token = $request->bearerToken();
        $userId = $this->getUserIdFromToken($token);
        $user = \App\Models\User::find($userId);

        $customer = Customer::where('user_id', $userId)->first();
        if (!$customer || $serviceRequest->customer_id !== $customer->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $serviceRequest->delete();
        return response()->json(['message' => 'Request cancelled']);
    }

    // Approve request (admin only)
    public function approve(Request $request, $id)
    {
        $token = $request->bearerToken();
        $userId = $this->getUserIdFromToken($token);
        $user = \App\Models\User::find($userId);

        if (!$user || $user->role !== 'admin') {
            return response()->json(['message' => 'Only admin can approve requests'], 403);
        }

        $serviceRequest = ServiceRequest::find($id);

        if (!$serviceRequest) {
            return response()->json(['message' => 'Request not found'], 404);
        }

        if ($serviceRequest->status !== 'pending') {
            return response()->json(['message' => 'Only pending requests can be approved'], 400);
        }

        // Create appointment
        $appointment = Appointment::create([
            'customer_id' => $serviceRequest->customer_id,
            'pet_id' => $serviceRequest->pet_id,
            'service_id' => $serviceRequest->service_id,
            'scheduled_at' => $serviceRequest->requested_date . ' ' . $serviceRequest->requested_time,
            'status' => 'confirmed',
            'notes' => $serviceRequest->notes
        ]);

        // Update service request
        $serviceRequest->update([
            'status' => 'approved',
            'reviewed_by' => $userId,
            'reviewed_at' => now()
        ]);

        return response()->json([
            'data' => $serviceRequest,
            'appointment' => $appointment,
            'message' => 'Request approved and appointment created'
        ]);
    }

    // Reject request (admin only)
    public function reject(Request $request, $id)
    {
        $token = $request->bearerToken();
        $userId = $this->getUserIdFromToken($token);
        $user = \App\Models\User::find($userId);

        if (!$user || $user->role !== 'admin') {
            return response()->json(['message' => 'Only admin can reject requests'], 403);
        }

        $validated = $request->validate([
            'rejection_reason' => 'required|string'
        ]);

        $serviceRequest = ServiceRequest::find($id);

        if (!$serviceRequest) {
            return response()->json(['message' => 'Request not found'], 404);
        }

        if ($serviceRequest->status !== 'pending') {
            return response()->json(['message' => 'Only pending requests can be rejected'], 400);
        }

        $serviceRequest->update([
            'status' => 'rejected',
            'rejection_reason' => $validated['rejection_reason'],
            'reviewed_by' => $userId,
            'reviewed_at' => now()
        ]);

        return response()->json([
            'data' => $serviceRequest,
            'message' => 'Request rejected'
        ]);
    }

    private function getUserIdFromToken($token)
    {
        if (preg_match('/^user_(\d+)_/', $token, $matches)) {
            return $matches[1];
        }
        return null;
    }
}
