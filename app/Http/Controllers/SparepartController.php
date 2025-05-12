<?php

namespace App\Http\Controllers;

use App\Models\Bengkel;
use App\Models\Sparepart;
use Illuminate\Http\Request;

class SparepartController extends Controller
{
    public function index()
    {
        return response()->json(Sparepart::all());
    }

    public function store(Request $request)
    {
        $userId = auth()->id();
        $bengkel = Bengkel::where('owner_id', $userId)->first();

        if (!$bengkel) {
            return response()->json(['message' => 'Bengkel not found for this owner.'], 404);
        }

        $bengkel_id = $bengkel->id;

        $validated = $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'harga' => 'required|numeric',
        ]);

        $validated['bengkel_id'] = $bengkel_id;

        $sparepart = Sparepart::create($validated);
        return response()->json([
            'message' => 'Sparepart created successfully',
            'bengkel' => $sparepart
        ], 201);
    }

    public function show($id)
    {
        $sparepart = Sparepart::findOrFail($id);
        return response()->json($sparepart);
    }

    public function update(Request $request, $id)
    {
        $sparepart = Sparepart::findOrFail($id);
        $user = auth()->user();

        if ($sparepart->bengkel->owner_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'nama' => 'sometimes|required|string|max:255',
            'deskripsi' => 'sometimes|required|string',
            'harga' => 'sometimes|required|numeric',
        ]);

        $sparepart->update($validated);
        return response()->json($sparepart->makeHidden('bengkel'));
    }

    public function destroy($id)
    {
        $sparepart = Sparepart::findOrFail($id);
        $user = auth()->user();

        if ($sparepart->bengkel->owner_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $sparepart->delete();

        return response()->json(['message' => 'Sparepart deleted successfully']);
    }
    public function getByBengkelId($id)
    {
        $spareparts = Sparepart::where('bengkel_id', $id)->get();

        if ($spareparts->isEmpty()) {
            return response()->json(['message' => 'No sparepart found for this bengkel.'], 404);
        }

        return response()->json($spareparts);
    }
    public function getByOwnerId()
    {
        $userId = auth()->id();
        $bengkel = Bengkel::where('owner_id', $userId)->first();

        if (!$bengkel) {
            return response()->json(['message' => 'Bengkel not found for this owner.'], 404);
        }

        $spareparts = Sparepart::where('bengkel_id', $bengkel->id)->get();

        if ($spareparts->isEmpty()) {
            return response()->json(['message' => 'No spareparts found for this bengkel.'], 404);
        }

        return response()->json($spareparts);
    }
}
