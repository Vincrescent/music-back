<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Equipment;

class EquipmentController extends Controller
{
    public function index()
    {
        $equipment = Equipment::with('studio')->get();
        return response()->json([
            'success' => true,
            'data' => $equipment
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'studio_id' => 'required|exists:studios,id',
            'name' => 'required|string|max:255',
            'status' => 'required|string',
            'notes' => 'nullable|string'
        ]);

        $equipment = Equipment::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Equipment added successfully',
            'data' => $equipment
        ], 201);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'studio_id' => 'sometimes|required|exists:studios,id',
            'name' => 'sometimes|required|string|max:255',
            'status' => 'sometimes|required|string',
            'notes' => 'nullable|string'
        ]);

        $equipment = Equipment::findOrFail($id);
        $equipment->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Equipment updated successfully',
            'data' => $equipment
        ]);
    }

    public function destroy($id)
    {
        $equipment = Equipment::findOrFail($id);
        $equipment->delete();

        return response()->json([
            'success' => true,
            'message' => 'Equipment deleted successfully'
        ]);
    }
}
