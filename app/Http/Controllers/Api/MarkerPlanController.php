<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\MarkerPlan;
use Illuminate\Http\Request;

class MarkerPlanController extends Controller
{
    public function index(Request $request)
    {
        $query = MarkerPlan::query();
        if ($orderId = $request->input('order_id')) {
            $query->where('order_id', $orderId);
        }
        return $query->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id'      => 'required|exists:orders,id',
            'marker_length' => 'required|numeric',
            'fabric_width'  => 'required|numeric',
            'efficiency'    => 'required|numeric',
            'part_count'    => 'required|integer',
            'marker_width'  => 'nullable|numeric',
            'dxf_file'      => 'nullable|file|max:20480',
        ]);

        if ($request->hasFile('dxf_file')) {
            $validated['dxf_file_path'] = $request->file('dxf_file')->store('markers', 'public');
        }

        $markerPlan = MarkerPlan::updateOrCreate(
            ['order_id' => $request->order_id],
            [
                'marker_length' => $validated['marker_length'],
                'fabric_width'  => $validated['fabric_width'],
                'efficiency'    => $validated['efficiency'],
                'part_count'    => $validated['part_count'],
                'marker_width'  => $validated['marker_width'] ?? null,
                'dxf_file_path' => $validated['dxf_file_path'] ?? null,
            ]
        );

        return response()->json([
            'message' => 'Marker plan saved successfully',
            'data'    => $markerPlan
        ]);
    }

    public function update(Request $request, MarkerPlan $markerPlan)
    {
        $validated = $request->validate([
            'marker_length' => 'required|numeric',
            'fabric_width'  => 'required|numeric',
            'efficiency'    => 'required|numeric',
            'part_count'    => 'required|integer',
            'marker_width'  => 'nullable|numeric',
            'dxf_file'      => 'nullable|file|max:20480',
        ]);

        if ($request->hasFile('dxf_file')) {
            $validated['dxf_file_path'] = $request->file('dxf_file')->store('markers', 'public');
        }

        $markerPlan->update($validated);
        
        return response()->json([
            'message' => 'Marker plan updated successfully',
            'data'    => $markerPlan
        ]);
    }

    public function destroy(MarkerPlan $markerPlan)
    {
        $markerPlan->delete();
        return response()->json([
            'message' => 'Marker plan deleted successfully'
        ]);
    }
}
