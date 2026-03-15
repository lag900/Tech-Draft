<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Fabric;

class FabricController extends Controller
{
    public function index(Request $request)
    {
        $query = Fabric::query();
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }
        return $query->get();
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'nullable|string',
        ]);

        return Fabric::create($request->all());
    }

    public function update(Request $request, Fabric $fabric)
    {
        $request->validate([
            'name' => 'required|string',
            'type' => 'nullable|string',
        ]);

        $fabric->update($request->all());
        return $fabric;
    }

    public function destroy(Fabric $fabric)
    {
        $fabric->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
