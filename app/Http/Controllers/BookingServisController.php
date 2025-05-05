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
            'nama_kendaraan' => 'required|string',
            'plat' => 'required|string',
            'keluhan' => 'required|string',
            'tgl_booking' => 'required|date',
            'status' => 'required|integer|in:0,1,2,3,4',
            'bengkel_id' => 'required|exists:bengkels,id',
        ]);

        $validated['nama'] = $user->name;
        $validated['no_hp'] = $user->no_hp;
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

            $validated = $request->validate([
                'nama_kendaraan' => 'sometimes|string',
                'plat' => 'sometimes|string',
                'keluhan' => 'sometimes|string',
                'tgl_ambil' => 'sometimes|date',
            ]);

            $booking->update($validated);
        } elseif ($user->role === 'admin_bengkel') {
            if ($booking->bengkel->owner_id !== $user->id) {
                return response()->json(['message' => 'Unauthorized'], 403);
            }

            $validated = $request->validate([
                'status' => 'sometimes|integer|in:0,1,2,3,4',
                'detail_servis' => 'sometimes|required|array|min:1',  // Validate it's an array
                'detail_servis.*.sparepart' => 'required|string',
                'detail_servis.*.harga' => 'required|numeric|min:1',
            ]);

            $booking->update($validated);
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
