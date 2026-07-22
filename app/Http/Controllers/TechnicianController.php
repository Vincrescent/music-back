<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Technician;

class TechnicianController extends Controller
{
    public function index()
    {
        $technicians = Technician::all();
        return response()->json([
            'success' => true,
            'data' => $technicians
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string',
            'specialization' => 'nullable|string',
            'phone' => 'nullable|string',
            'status' => 'nullable|string'
        ]);

        $technician = Technician::create($validated);
        return response()->json([
            'success' => true,
            'data' => $technician
        ]);
    }
}
