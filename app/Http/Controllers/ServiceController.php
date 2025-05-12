<?php

namespace App\Http\Controllers;

use App\Models\Bengkel;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    public function index()
    {
        return response()->json(Service::all());
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

        $service = Service::create($validated);
        return response()->json([
            'message' => 'Service created successfully',
            'bengkel' => $service
        ], 201);
    }

    public function show($id)
    {
        $service = Service::findOrFail($id);
        return response()->json($service);
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);
        $user = auth()->user();

        if ($service->bengkel->owner_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $validated = $request->validate([
            'nama' => 'sometimes|required|string|max:255',
            'deskripsi' => 'sometimes|required|string',
            'harga' => 'sometimes|required|numeric',
        ]);

        $service->update($validated);
        return response()->json($service->makeHidden('bengkel'));
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);
        $user = auth()->user();

        if ($service->bengkel->owner_id !== $user->id) {
            return response()->json(['message' => 'Unauthorized'], 403);
        }

        $service->delete();

        return response()->json(['message' => 'Service deleted successfully']);
    }
    public function getByBengkelId($id)
    {
        $spareparts = Service::where('bengkel_id', $id)->get();

        if ($spareparts->isEmpty()) {
            return response()->json(['message' => 'No service found for this bengkel.'], 404);
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

        $spareparts = Service::where('bengkel_id', $bengkel->id)->get();

        if ($spareparts->isEmpty()) {
            return response()->json(['message' => 'No service found for this bengkel.'], 404);
        }

        return response()->json($spareparts);
    }
}
