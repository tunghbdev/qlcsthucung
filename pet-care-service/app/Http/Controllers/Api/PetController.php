<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Pet;
use App\Models\User;
use Illuminate\Http\Request;

class PetController extends Controller
{
    public function index(Request $request)
    {
        $token = $request->bearerToken();
        $userId = $this->getUserIdFromToken($token);

        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $user = User::find($userId);

        if ($user->role !== 'customer') {
            return response()->json(['message' => 'Only customers can view pets'], 403);
        }

        $pets = Pet::where('customer_id', $user->customer->id)
            ->where('is_active', true)
            ->get()
            ->map(function ($pet) {
                return [
                    'id' => $pet->id,
                    'name' => $pet->name,
                    'type' => $pet->type,
                    'breed' => $pet->breed,
                    'age' => $pet->age,
                    'weight' => $pet->weight,
                    'color' => $pet->color,
                    'description' => $pet->description,
                    'health_notes' => $pet->health_notes,
                    'last_checkup' => $pet->last_checkup?->format('Y-m-d')
                ];
            });

        return response()->json(['data' => $pets]);
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
            return response()->json(['message' => 'Only customers can create pets'], 403);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:100',
            'breed' => 'nullable|string|max:100',
            'age' => 'nullable|integer',
            'weight' => 'nullable|numeric',
            'color' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'health_notes' => 'nullable|string'
        ]);

        $pet = Pet::create([
            'customer_id' => $user->customer->id,
            ...$validated,
            'is_active' => true,
            'last_checkup' => now()
        ]);

        return response()->json([
            'data' => [
                'id' => $pet->id,
                'name' => $pet->name,
                'type' => $pet->type,
                'breed' => $pet->breed,
                'age' => $pet->age,
                'weight' => $pet->weight,
                'color' => $pet->color,
                'description' => $pet->description,
                'health_notes' => $pet->health_notes,
                'last_checkup' => $pet->last_checkup?->format('Y-m-d')
            ],
            'message' => 'Thêm thú cưng thành công'
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $token = $request->bearerToken();
        $userId = $this->getUserIdFromToken($token);

        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $pet = Pet::find($id);

        if (!$pet) {
            return response()->json(['message' => 'Pet not found'], 404);
        }

        $user = User::find($userId);

        if ($user->role === 'customer' && $pet->customer_id !== $user->customer->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $validated = $request->validate([
            'name' => 'nullable|string|max:255',
            'breed' => 'nullable|string|max:100',
            'age' => 'nullable|integer',
            'weight' => 'nullable|numeric',
            'color' => 'nullable|string|max:100',
            'description' => 'nullable|string',
            'health_notes' => 'nullable|string'
        ]);

        $pet->update($validated);

        return response()->json([
            'data' => [
                'id' => $pet->id,
                'name' => $pet->name,
                'type' => $pet->type,
                'breed' => $pet->breed,
                'age' => $pet->age,
                'weight' => $pet->weight,
                'color' => $pet->color,
                'description' => $pet->description,
                'health_notes' => $pet->health_notes,
                'last_checkup' => $pet->last_checkup?->format('Y-m-d')
            ],
            'message' => 'Cập nhật thú cưng thành công'
        ]);
    }

    public function destroy(Request $request, $id)
    {
        $token = $request->bearerToken();
        $userId = $this->getUserIdFromToken($token);

        if (!$userId) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $pet = Pet::find($id);

        if (!$pet) {
            return response()->json(['message' => 'Pet not found'], 404);
        }

        $user = User::find($userId);

        if ($user->role === 'customer' && $pet->customer_id !== $user->customer->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $pet->update(['is_active' => false]);

        return response()->json(['message' => 'Xóa thú cưng thành công']);
    }

    private function getUserIdFromToken($token)
    {
        if (!$token) return null;
        preg_match('/user_(\d+)_/', $token, $matches);
        return $matches[1] ?? null;
    }
}
