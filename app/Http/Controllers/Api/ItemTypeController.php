<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ItemType;
use Illuminate\Http\Request;

class ItemTypeController extends Controller
{
    public function index(Request $request)
    {
        $query = ItemType::with('category');

        if ($request->has('category_id')) {
            $query->where('category_id', $request->category_id);
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
            'category_id' => 'required|exists:categories,id',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        return ItemType::create($validated);
    }

    public function show(ItemType $itemType)
    {
        return $itemType->load('category', 'fits');
    }

    public function update(Request $request, ItemType $itemType)
    {
        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'category_id' => 'sometimes|required|exists:categories,id',
            'status' => 'nullable|string|in:active,inactive',
        ]);

        $itemType->update($validated);
        return $itemType;
    }

    public function destroy(ItemType $itemType)
    {
        $itemType->delete();
        return response()->noContent();
    }
}
