<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Fit;
use Illuminate\Http\Request;

class FitController extends Controller
{
    public function index(Request $request)
    {
        $query = Fit::with('itemType.category');

        if ($request->has('item_type_id')) {
            $query->where('item_type_id', $request->item_type_id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        return $query->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'item_type_id' => 'required|exists:item_types,id',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        return Fit::create($validated);
    }

    public function show(Fit $fit)
    {
        return $fit->load('itemType');
    }

    public function update(Request $request, Fit $fit)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'item_type_id' => 'sometimes|required|exists:item_types,id',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        $fit->update($validated);
        return $fit;
    }

    public function destroy(Fit $fit)
    {
        $fit->delete();
        return response()->noContent();
    }
}
