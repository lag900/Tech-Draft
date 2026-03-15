<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderProduction;
use Illuminate\Http\Request;

class OrderProductionController extends Controller
{
    public function store(Request $request, Order $order)
    {
        $validated = $request->validate([
            'fabric_code' => 'nullable|string',
            'fabric_supplier' => 'nullable|string',
            'fabric_width' => 'nullable|numeric',
            'dye_method' => 'nullable|string',
            'fabric_finish' => 'nullable|string',
            'zipper_type' => 'nullable|string',
            'button_type' => 'nullable|string',
            'thread_type' => 'nullable|string',
            'stitch_type' => 'nullable|string',
            'seam_type' => 'nullable|string',
            'label_type' => 'nullable|string',
            'packaging_type' => 'nullable|string',
            'carton_quantity' => 'nullable|integer',
            'factory_notes' => 'nullable|string',
        ]);

        $production = $order->production()->updateOrCreate(
            ['order_id' => $order->id],
            $validated
        );

        return response()->json([
            'success' => true,
            'message' => 'Production details saved successfully',
            'data' => $production
        ]);
    }
}
