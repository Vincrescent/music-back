<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaintenanceTicket;
use App\Models\Equipment;

class MaintenanceController extends Controller
{
    public function index()
    {
        $tickets = MaintenanceTicket::with(['equipment.studio', 'technician'])->get();
        return response()->json([
            'success' => true,
            'data' => $tickets
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->all();
        if (empty($data['technician_id'])) {
            $data['technician_id'] = null;
        }

        $validated = validator($data, [
            'equipment_id' => 'required|exists:equipment,id',
            'technician_id' => 'nullable|exists:technicians,id',
            'issue_description' => 'required|string',
            'status' => 'nullable|string',
            'scheduled_date' => 'nullable',
        ])->validate();

        $ticket = MaintenanceTicket::create($validated);

        $equipment = Equipment::find($validated['equipment_id']);
        $status = $validated['status'] ?? 'Pending';
        if ($equipment && $status !== 'Completed') {
            $equipment->update(['status' => 'Service']);
        }

        return response()->json([
            'success' => true,
            'data' => $ticket
        ]);
    }

    public function update(Request $request, $id)
    {
        $ticket = MaintenanceTicket::findOrFail($id);
        
        $validated = $request->validate([
            'technician_id' => 'nullable|exists:technicians,id',
            'issue_description' => 'nullable|string',
            'status' => 'nullable|string',
            'scheduled_date' => 'nullable|date',
            'completed_date' => 'nullable|date',
            'resolution_notes' => 'nullable|string',
        ]);

        $ticket->update($validated);

        if (isset($validated['status']) && $validated['status'] === 'Completed') {
            $equipment = Equipment::find($ticket->equipment_id);
            if ($equipment) {
                $equipment->update(['status' => 'Good']);
            }
        }

        return response()->json([
            'success' => true,
            'data' => $ticket
        ]);
    }
}
