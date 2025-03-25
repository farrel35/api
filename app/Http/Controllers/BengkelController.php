<?php

namespace App\Http\Controllers;

use App\Models\Bengkel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class BengkelController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'long' => 'required|numeric',
        ]);

        $latitude = $request->lat;
        $longitude = $request->long;

        $bengkels = Bengkel::select(
            'bengkels.*',
            DB::raw("ROUND((6371000 * acos(cos(radians($latitude)) * cos(radians(lat)) * cos(radians(`long`) - radians($longitude)) + sin(radians($latitude)) * sin(radians(lat)))), 2) AS distance")
        )
            ->orderBy('distance') // Sort by nearest
            ->get();

        return response()->json($bengkels);
    }

    public function store(Request $request)
    {
        if (Bengkel::where('owner_id', auth()->id())->exists()) {
            return response()->json(['message' => 'You can only create one Bengkel'], 403);
        }

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
            'jam_buka' => 'required',
            'jam_selesai' => 'required',
            'lat' => 'required|numeric',
            'long' => 'required|numeric',
        ]);

        $validated['owner_id'] = auth()->id();

        $bengkel = Bengkel::create($validated);
        return response()->json([
            'message' => 'Bengkel insert successfully',
            'bengkel' => $bengkel
        ]);
    }

    public function show($id)
    {
        $bengkel = Bengkel::findOrFail($id);
        return response()->json($bengkel);
    }

    public function update(Request $request, $id)
    {
        $bengkel = Bengkel::findOrFail($id);

        if ($bengkel->owner_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $bengkel->update($request->only(['nama', 'deskripsi', 'jam_buka', 'jam_selesai', 'lat', 'long']));
        return response()->json([
            'message' => 'Bengkel updated successfully',
            'bengkel' => $bengkel
        ]);
    }

    public function destroy($id)
    {
        $bengkel = Bengkel::findOrFail($id);

        if ($bengkel->owner_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $bengkel->delete();
        return response()->json(['message' => 'Bengkel deleted']);
    }

    public function getByOwner()
    {
        $user = auth()->user();
        return response()->json(Bengkel::where('owner_id', $user->id)->first());
    }
}
