<?php

namespace App\Http\Controllers;

use App\Models\Bengkel;
use Illuminate\Http\Request;
use App\Models\BookingServis;

class BookingServisController extends Controller
{
    public function index()
    {
        $bookings = BookingServis::all();
        return response()->json($bookings);
    }

    public function store(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'jenis_kendaraan' => 'required|string',
            'plat' => 'required|string',
            'keluhan' => 'required|string',
            'status' => 'required|in:pending,onprogress,completed',
            'bengkel_id' => 'required|exists:bengkels,id',
        ]);

        $validated['nama'] = $user->name;
        $validated['user_id'] = $user->id;

        $booking = BookingServis::create($validated);
        return response()->json([
            'message' => 'Booking created successfully',
            'bengkel' => $booking
        ], 201);
    }

    public function show($id)
    {
        $booking = BookingServis::findOrFail($id);
        $user = auth()->user();

        if ($booking->user_id === $user->id) {
            return response()->json($booking);
        }

        if ($booking->bengkel && $booking->bengkel->owner_id === $user->id) {
            return response()->json($booking->makeHidden('bengkel'));
        }

        return response()->json(['message' => 'Unauthorized'], 403);
    }

    public function update(Request $request, $id)
    {
        $booking = BookingServis::findOrFail($id);
        $user = auth()->user();

        if ($user->role === 'user') {
            if ($booking->user_id !== $user->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $request->validate([
                'jenis_kendaraan' => 'sometimes|string',
                'plat' => 'sometimes|string',
                'keluhan' => 'sometimes|string',
            ]);

            $booking->update($request->only(['jenis_kendaraan', 'plat', 'keluhan']));
        } elseif ($user->role === 'owner_bengkel') {
            if ($booking->bengkel->owner_id !== $user->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $request->validate([
                'status' => 'required|in:pending,onprogress,completed',
            ]);

            $booking->update([
                'status' => $request->status,
            ]);
        }

        return response()->json($booking->makeHidden('bengkel'));

    }

    public function destroy($id)
    {
        $booking = BookingServis::findOrFail($id);
        $user = auth()->user();

        if ($booking->user_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $booking->delete();
        return response()->json(['message' => 'Booking deleted']);
    }

    public function getByUserId()
    {
        $userId = auth()->id();
        $bookings = BookingServis::where('user_id', $userId)->get();

        if ($bookings->isEmpty()) {
            return response()->json(['message' => 'No bookings found for this user.'], 404);
        }

        return response()->json($bookings);
    }

    public function getByOwnerId()
    {
        $userId = auth()->id();
        $bengkel = Bengkel::where('owner_id', $userId)->first();

        if (!$bengkel) {
            return response()->json(['message' => 'Bengkel not found for this owner.'], 404);
        }

        $bookings = BookingServis::where('bengkel_id', $bengkel->id)->get();

        if ($bookings->isEmpty()) {
            return response()->json(['message' => 'No bookings found for this bengkel.'], 404);
        }

        return response()->json($bookings);
    }
}
