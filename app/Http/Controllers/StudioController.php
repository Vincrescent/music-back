<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Studio;

class StudioController extends Controller
{
    public function index()
    {
        $studios = Studio::with('equipment')->get();
        return response()->json([
            'success' => true,
            'data' => $studios
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type' => 'required|string|max:255',
            'price_per_hour' => 'required|numeric',
            'status' => 'required|string',
            'description' => 'nullable|string'
        ]);

        $studio = Studio::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Studio created successfully',
            'data' => $studio
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'type' => 'sometimes|required|string|max:255',
            'price_per_hour' => 'sometimes|required|numeric',
            'status' => 'sometimes|required|string',
            'description' => 'nullable|string'
        ]);

        $studio = Studio::findOrFail($id);
        $studio->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Studio updated successfully',
            'data' => $studio
        ]);
    }

    public function destroy($id)
    {
        $studio = Studio::findOrFail($id);
        $studio->delete();

        return response()->json([
            'success' => true,
            'message' => 'Studio deleted successfully'
        ]);
    }
}
