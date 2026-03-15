<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Design;
use Illuminate\Http\Request;

class DesignController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Design::forUser($user)->with(['category', 'creator']);

        if ($search = $request->input('search')) {
            $query->where(function(\Illuminate\Database\Eloquent\Builder $q) use ($search) {
                $q->where('name', 'LIKE', "%{$search}%")
                  ->orWhere('product_code', 'LIKE', "%{$search}%");
            });
        }

        if ($season = $request->input('season')) {
            $query->where('season', $season);
        }

        if ($fabric = $request->input('fabric_type')) {
            $query->where('fabric_type', 'LIKE', "%{$fabric}%");
        }

        return $query->latest()->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'client_id' => 'nullable|exists:clients,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $data = $request->except('image');
        $data['user_id'] = $request->user()->id;

        if ($request->user()->role === 'client') {
            $data['client_id'] = $request->user()->client?->id;
        }

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('designs', 'public');
        }

        $design = Design::create($data);

        return response()->json($design->load(['category', 'creator']), 201);
    }

    public function show(Design $design)
    {
        return $design->load(['category', 'creator']);
    }

    public function update(Request $request, Design $design)
    {
        $request->validate([
            'name' => 'sometimes|string|max:255',
            'category_id' => 'sometimes|exists:categories,id',
            'client_id' => 'nullable|exists:clients,id',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $data = $request->except('image');

        if ($request->hasFile('image')) {
            $data['image_path'] = $request->file('image')->store('designs', 'public');
        }

        $design->update($data);

        return response()->json($design->load(['category', 'creator']));
    }

    public function destroy(Design $design)
    {
        $design->delete();
        return response()->json(['message' => 'Deleted successfully']);
    }
}
