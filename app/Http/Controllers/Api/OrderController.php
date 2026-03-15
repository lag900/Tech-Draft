<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\OrderImage;
use App\Models\OrderStatusHistory;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Order::forUser($user)->with(['client', 'category', 'creator', 'images', 'production', 'statusHistory' => function($q) {
            $q->latest()->limit(1);
        }]);

        // Search & Filtering
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('order_code', 'LIKE', "%{$search}%");
            });
        }

        if ($season = $request->input('season')) {
            $query->where('season', $season);
        }

        if ($fabric = $request->input('fabric_type')) {
            $query->where('fabric_details->type', 'LIKE', "%{$fabric}%");
        }

        if ($perPage = $request->input('per_page')) {
            return $query->latest()->paginate($perPage);
        }

        return $query->latest()->get();
    }

    public function export(Request $request)
    {
        $user = $request->user();
        $query = Order::forUser($user)->with(['client', 'category']);

        $orders = $query->latest()->get();

        $filename = "orders_" . date('Y-m-d_H-i-s') . ".csv";
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$filename",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($orders) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Order Code', 'Title', 'Client', 'Category', 'Season', 'Year', 'Status', 'Created At']);

            foreach ($orders as $order) {
                fputcsv($file, [
                    $order->order_code,
                    $order->title,
                    $order->client?->brand_name ?? 'N/A',
                    $order->category?->name ?? 'N/A',
                    $order->season,
                    $order->year,
                    $order->status,
                    $order->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function store(Request $request)
    {
        $user = $request->user();
        
        $data = $request->all();
        
        // If user is a client, automatically set client_id
        if ($user->role === 'client') {
            $data['client_id'] = $user->client?->id;
        }

        // Handle JSON strings if sent from FormData
        if (is_string($data['fabric_details'] ?? null)) $data['fabric_details'] = json_decode($data['fabric_details'], true);
        if (is_string($data['colors'] ?? null)) $data['colors'] = json_decode($data['colors'], true);
        if (is_string($data['measurements'] ?? null)) $data['measurements'] = json_decode($data['measurements'], true);
        if (is_string($data['production_details'] ?? null)) $data['production_details'] = json_decode($data['production_details'], true);

        $request->merge($data);

        $request->validate([
            'title' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'category_id' => 'nullable|exists:categories,id',
            'product_id' => 'nullable|exists:products,id',
            'season' => 'required|string',
            'year' => 'required|integer',
            'fabric_details' => 'nullable|array',
            'colors' => 'nullable|array',
            'measurements' => 'nullable|array',
            'production_details' => 'nullable|array',
            'notes' => 'nullable|string',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:10240',
            
            // Tech Pack Specific Validations
            'design_front_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'design_back_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'technical_sketch' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'zipper_type' => 'nullable|string',
            'button_type' => 'nullable|string',
            'cord_type' => 'nullable|string',
            'drawcord_type' => 'nullable|string',
            'rib_type' => 'nullable|string',
            'stitching_type' => 'nullable|string',
            'label_details' => 'nullable|string',
            'main_label_type' => 'nullable|string',
            'care_label_type' => 'nullable|string',
            'size_label_type' => 'nullable|string',
            'hangtag_type' => 'nullable|string',
            'packaging_notes' => 'nullable|string',
            'packaging_type' => 'nullable|string',
            'folding_method' => 'nullable|string',
            'barcode_required' => 'nullable|boolean',
            'pattern_code' => 'nullable|string',
            'version_number' => 'nullable|integer',
            'revision_number' => 'nullable|integer',
        ]);

        $orderCode = $this->generateOrderCode();

        // Point 9: Brand Info Check
        if ($user->role === 'client' && (!$user->client || !$user->client->brand_name)) {
            return response()->json(['message' => 'Please provide brand information before placing an order.'], 403);
        }

        $orderData = array_merge($request->all(), [
            'order_code' => $orderCode,
            'created_by' => $user->id,
            'status' => $request->input('status', 'pending'),
            'brand_name' => $user->client?->brand_name,
        ]);

        // Handle Specialized Asset Uploads
        foreach (['design_front_image', 'design_back_image', 'technical_sketch'] as $fileKey) {
            if ($request->hasFile($fileKey)) {
                $orderData[$fileKey] = $request->file($fileKey)->store('designs', 'public');
            }
        }

        if ($request->hasFile('reference_images')) {
            $refPaths = [];
            foreach ($request->file('reference_images') as $file) {
                $refPaths[] = $file->store('references', 'public');
            }
            $orderData['reference_images'] = $refPaths;
        }

        $order = Order::create($orderData);

        // --- Structured Data Sync (Point 2, 3, 5, 6) ---
        
        // 1. Sync Colors
        $colorModels = [];
        if (!empty($order->colors) && is_array($order->colors)) {
            foreach ($order->colors as $color) {
                $colorModels[] = \App\Models\OrderColor::create([
                    'order_id' => $order->id,
                    'color_name' => $color['name'] ?? ($color['color'] ?? 'Unknown'),
                    'color_hex' => $color['hex'] ?? null,
                    'color_code' => $color['code'] ?? null,
                ]);
            }
        }

        // 2. Sync Sizes
        $sizes = $request->input('production_details.sizes') ?? $request->input('sizes');
        if (!empty($sizes) && is_array($sizes)) {
            foreach ($sizes as $sizeName => $qty) {
                if (is_numeric($qty)) {
                    \App\Models\OrderSize::create([
                        'order_id' => $order->id,
                        'size_name' => $sizeName,
                        'quantity' => $qty,
                    ]);
                }
            }
        }

        // 3. Sync Measurements
        if (!empty($order->measurements) && is_array($order->measurements)) {
            foreach ($order->measurements as $measure) {
                if (isset($measure['point']) && isset($measure['value'])) {
                    \App\Models\OrderMeasurement::create([
                        'order_id' => $order->id,
                        'point_of_measure' => $measure['point'],
                        'dimension_value' => $measure['value'],
                        'unit' => $measure['unit'] ?? 'cm',
                    ]);
                }
            }
        }

        // 4. Basic Variant Sync (Combinations of Colors and current Fabric)
        // This is a lightweight tie-in for Point 5
        if (!empty($colorModels) && $request->input('fabric_details.id')) {
            foreach ($colorModels as $cModel) {
                \App\Models\OrderVariant::create([
                    'order_id' => $order->id,
                    'order_color_id' => $cModel->id,
                    'fabric_id' => $request->input('fabric_details.id'),
                ]);
            }
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('orders', 'public');
                OrderImage::create([
                    'order_id' => $order->id,
                    'file_path' => $path,
                ]);
            }
        }

        return $order->load(['images', 'orderColors', 'orderSizes', 'orderMeasurements']);
    }

    public function show(Order $order)
    {
        $user = request()->user();
        if ($user->role === 'client' && $order->client?->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        return $order->load(['client', 'category', 'creator', 'images', 'production', 'messages.sender', 'anatomies', 'markerPlans', 'statusHistory', 'orderColors', 'orderSizes', 'orderMeasurements', 'orderVariants']);
    }



    public function update(Request $request, Order $order)
    {
        $user = $request->user();
        if ($user->role === 'client' && $order->client?->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'status' => 'required|string|in:draft,submitted,pending,technical_ready,in_review,sampling,approved,production,completed,cancelled',
        ]);

        if ($user->role === 'admin' || $user->role === 'manager') {
            $data = $request->all();
            
            // Handle JSON strings if sent from FormData
            if (is_string($data['fabric_details'] ?? null)) $data['fabric_details'] = json_decode($data['fabric_details'], true);
            if (is_string($data['colors'] ?? null)) $data['colors'] = json_decode($data['colors'], true);
            if (is_string($data['measurements'] ?? null)) $data['measurements'] = json_decode($data['measurements'], true);
            if (is_string($data['production_details'] ?? null)) $data['production_details'] = json_decode($data['production_details'], true);

            $order->update($data);

            // Sync Structured Data if provided
            if (isset($data['measurements']) && is_array($data['measurements'])) {
                $order->orderMeasurements()->delete();
                foreach ($data['measurements'] as $measure) {
                    if (isset($measure['point']) && isset($measure['value'])) {
                        \App\Models\OrderMeasurement::create([
                            'order_id' => $order->id,
                            'point_of_measure' => $measure['point'],
                            'dimension_value' => $measure['value'],
                            'unit' => $measure['unit'] ?? 'cm',
                        ]);
                    }
                }
            }
        }

        $oldStatus = $order->status;
        $newStatus = $request->status;

        if ($oldStatus !== $newStatus) {
            $order->update(['status' => $newStatus]);

            OrderStatusHistory::create([
                'order_id' => $order->id,
                'old_status' => $oldStatus,
                'new_status' => $newStatus,
                'changed_by' => $request->user()->id,
            ]);
        }

        return $order;
    }

    public function updateStatus(Request $request, Order $order)
    {
        return $this->update($request, $order);
    }

    public function stats(Request $request)
    {
        $user = $request->user();
        $query = Order::forUser($user);

        $stats = $query->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->get()
            ->pluck('count', 'status');

        $statuses = ['draft', 'submitted', 'pending', 'technical_ready', 'in_review', 'sampling', 'approved', 'production', 'completed', 'cancelled'];
        $response = [];

        foreach ($statuses as $status) {
            $response[$status] = $stats->get($status, 0);
        }

        return $response;
    }

    private function generateOrderCode()
    {
        $year = date('Y');
        $lastOrder = Order::where('order_code', 'LIKE', "ORD-{$year}-%")->latest()->first();
        $nextNumber = 1;

        if ($lastOrder) {
            $lastNumber = (int) Str::afterLast($lastOrder->order_code, '-');
            $nextNumber = $lastNumber + 1;
        }

        return "ORD-{$year}-" . str_pad($nextNumber, 5, '0', STR_PAD_LEFT);
    }
}
