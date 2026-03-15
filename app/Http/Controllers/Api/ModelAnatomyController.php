<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ModelAnatomy;
use Illuminate\Http\Request;

class ModelAnatomyController extends Controller
{
    public function index(Request $request)
    {
        $query = ModelAnatomy::query();
        if ($orderId = $request->input('order_id')) {
            $query->where('order_id', $orderId);
        }
        return $query->get();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id'   => 'required|exists:orders,id',
            'piece_name' => 'required|string',
            'part_name'  => 'required|string',
            'count'      => 'required|integer|min:1',
            'image'      => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('anatomy', 'public');
        }

        $anatomy = ModelAnatomy::create($validated);

        return response()->json([
            'message' => 'Model anatomy saved successfully',
            'data'    => $anatomy
        ]);
    }

    public function update(Request $request, ModelAnatomy $modelAnatomy)
    {
        $validated = $request->validate([
            'piece_name' => 'sometimes|string',
            'part_name'  => 'sometimes|string',
            'count'      => 'sometimes|integer|min:1',
            'image'      => 'nullable|image|max:5120',
        ]);

        if ($request->hasFile('image')) {
            $validated['image_path'] = $request->file('image')->store('anatomy', 'public');
        }

        $modelAnatomy->update($validated);

        return response()->json([
            'message' => 'Model anatomy updated successfully',
            'data'    => $modelAnatomy
        ]);
    }

    public function destroy(ModelAnatomy $modelAnatomy)
    {
        $modelAnatomy->delete();
        return response()->noContent();
    }

    /**
     * Point 8: Suggest part names based on piece name
     */
    public function suggest(Request $request)
    {
        $piece = $request->input('piece_name');
        if (!$piece) return [];

        return ModelAnatomy::where('piece_name', 'LIKE', "%{$piece}%")
            ->distinct()
            ->pluck('part_name');
    }
}
