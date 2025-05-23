<?php

namespace App\Http\Controllers;

use App\Models\Bengkel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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
            DB::raw("ROUND((6371000 * acos(
        cos(radians(?)) * cos(radians(lat)) *
        cos(radians(`long`) - radians(?)) +
        sin(radians(?)) * sin(radians(lat))
    )), 2) AS distance")
        )
            ->addBinding([$latitude, $longitude, $latitude], 'select')
            ->orderBy('distance')
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
            'alamat' => 'required|string',
            'deskripsi' => 'nullable|string',
            'jam_buka' => 'required',
            'jam_selesai' => 'required',
            'lat' => 'required|numeric',
            'long' => 'required|numeric',
            'image' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $validated['owner_id'] = auth()->id();

        $imagePath = $request->file('image')->store('bengkel_images', 'public');
        $validated['image'] = $imagePath;

        $bengkel = Bengkel::create($validated);

        return response()->json([
            'message' => 'Bengkel created successfully',
            'bengkel' => $bengkel
        ], 201);
    }


    public function show(Request $request, $id)
    {
        $request->validate([
            'lat' => 'required|numeric',
            'long' => 'required|numeric',
        ]);

        $latitude = $request->lat;
        $longitude = $request->long;

        $latitude = $request->lat;
        $longitude = $request->long;

        $bengkel = Bengkel::select(
            'bengkels.*',
            DB::raw("ROUND((6371000 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(`long`) - radians(?)) + sin(radians(?)) * sin(radians(lat)))), 2) AS distance")
        )
            ->addBinding([$latitude, $longitude, $latitude], 'select') // Add bindings
            ->findOrFail($id);



        return response()->json($bengkel);
    }


    public function update(Request $request, $id)
    {
        $bengkel = Bengkel::findOrFail($id);

        if ($bengkel->owner_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'nama' => 'sometimes|required|string|max:255',
            'alamat' => 'sometimes|string',
            'deskripsi' => 'nullable|string',
            'jam_buka' => 'sometimes|required',
            'jam_selesai' => 'sometimes|required',
            'lat' => 'sometimes|required|numeric',
            'long' => 'sometimes|required|numeric',
            'image' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);


        if ($request->hasFile('image')) {
            if ($bengkel->image) {
                Storage::disk('public')->delete($bengkel->image);
            }
            $validated['image'] = $request->file('image')->store('bengkel_images', 'public');
        }

        $bengkel->update($validated);

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

        $bengkel = Bengkel::where('owner_id', $user->id)->first();

        if (!$bengkel) {
            return response()->json(['message' => 'Bengkel not found'], 404);
        }

        $data = $bengkel->toArray();

        return response()->json($data);
    }

}
