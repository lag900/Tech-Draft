<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Order;
use App\Models\OrderImage;
use App\Models\OrderStatusHistory;
use App\Services\OrderTechPackService;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    protected $techPackService;

    public function __construct(OrderTechPackService $techPackService)
    {
        $this->techPackService = $techPackService;
    }
    public function index(Request $request)
    {
        $user = $request->user();
        $query = Order::forUser($user)
            ->select([
                'id', 'order_code', 'title', 'client_id', 'category_id', 'status', 
                'created_by', 'season', 'year', 'fabric_details', 'production_details', 'created_at'
            ])
            ->with(['client', 'category', 'creator', 'statusHistory' => function($q) {
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

        if ($status = $request->input('status')) {
            if ($status !== 'all') {
                $query->where('status', $status);
            }
        }

        $perPage = (int) $request->input('per_page', 10);
        $perPage = in_array($perPage, [10, 25, 50, 100]) ? $perPage : 10;

        return $query->latest()->paginate($perPage);
    }

    public function export(Request $request)
    {
        $user = $request->user();
        $orders = Order::forUser($user)
            ->select(['id', 'order_code', 'title', 'client_id', 'category_id', 'season', 'year', 'status', 'created_at'])
            ->with(['client:id,brand_name', 'category:id,name'])
            ->latest()
            ->get();

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
            'reference_images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
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
            } else {
                unset($orderData[$fileKey]);
            }
        }

        if ($request->hasFile('reference_images')) {
            $refPaths = [];
            foreach ($request->file('reference_images') as $file) {
                if ($file->isValid()) {
                    $refPaths[] = $file->store('references', 'public');
                }
            }
            $orderData['reference_images'] = $refPaths;
        } else {
            unset($orderData['reference_images']);
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
                    $val = $measure['value'];
                    if (is_array($val)) continue;
                    if (is_string($val)) {
                        $trimmed = ltrim($val);
                        if (Str::startsWith($trimmed, '[') || Str::startsWith($trimmed, '{')) continue;
                    }
                    $numericVal = ($val !== null && $val !== '')
                        ? (float) filter_var($val, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION)
                        : 0.0;

                    \App\Models\OrderMeasurement::create([
                        'order_id' => $order->id,
                        'point_of_measure' => $measure['point'],
                        'dimension_value' => $numericVal,
                        'unit' => $measure['unit'] ?? 'cm',
                        'grading' => isset($measure['grading']) && is_array($measure['grading']) ? $measure['grading'] : null,
                        'tolerance' => $measure['tolerance'] ?? null,
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

        Log::info('Order attributes before return: ', $order->getAttributes());
        return $order->load(['images', 'orderColors', 'orderSizes', 'orderMeasurements']);
    }


    public function show(Order $order)
    {
        $user = request()->user();
        if ($user->role === 'client' && $order->client?->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $order->load(['client', 'category', 'creator', 'images', 'production', 'messages.sender', 'anatomies', 'markerPlans', 'statusHistory', 'orderColors', 'orderSizes', 'orderMeasurements', 'orderVariants']);
        
        $orderData = $order->toArray();
        $orderData['model_anatomy'] = $order->anatomies;
        
        $marker = $order->markerPlans->first();
        if ($marker) {
            $orderData['marker_length'] = $marker->marker_length;
            $orderData['fabric_width'] = $marker->fabric_width;
            $orderData['parts_count'] = $marker->part_count; // standardizing DB column
            $orderData['efficiency'] = $marker->efficiency;
        } else {
            $orderData['marker_length'] = null;
            $orderData['fabric_width'] = null;
            $orderData['parts_count'] = null;
            $orderData['efficiency'] = null;
        }

        return response()->json($orderData);
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

        try {
            $oldStatus = $order->status;
            $newStatus = $request->status;

            if ($oldStatus !== $newStatus) {
                $order->update(['status' => $newStatus]);

                OrderStatusHistory::create([
                    'order_id' => $order->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus,
                    'changed_by' => $user->id,
                ]);
            }

            if ($user->role === 'admin' || $user->role === 'manager') {
                $data = $request->all();
                
                // Handle JSON decoding for multi-part requests
                foreach(['fabric_details', 'colors', 'measurements', 'production_details'] as $field) {
                    if (is_string($data[$field] ?? null)) {
                        $decoded = json_decode($data[$field], true);
                        if (json_last_error() === JSON_ERROR_NONE) {
                            $data[$field] = $decoded;
                        }
                    }
                }

                $order->update($data);

                // Sync Normalized Measurements if provided
                if (isset($data['measurements']) && is_array($data['measurements'])) {
                    $order->orderMeasurements()->delete();
                    foreach ($data['measurements'] as $m) {
                        $val = $m['value'] ?? $m['dimension_value'] ?? 0;
                        if (is_array($val)) continue;
                        if (is_string($val)) {
                            $trimmed = ltrim($val);
                            if (Str::startsWith($trimmed, '[') || Str::startsWith($trimmed, '{')) continue;
                        }
                        if (is_numeric($val) || (is_string($val) && !empty($val))) {
                            // Strip unit 'cm', 'in' etc and parse float
                            $numericVal = (float) filter_var($val, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                            
                            $order->orderMeasurements()->create([
                                'point_of_measure' => $m['point'] ?? $m['name'] ?? 'Unknown',
                                'dimension_value' => $numericVal,
                                'unit' => $m['unit'] ?? 'cm',
                                'grading' => isset($m['grading']) && is_array($m['grading']) ? $m['grading'] : null,
                                'tolerance' => $m['tol'] ?? $m['tolerance'] ?? null
                            ]);
                        }
                    }
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Order updated successfully',
                'order' => $order->fresh(['orderMeasurements'])
            ]);

        } catch (\Exception $e) {
            Log::error('Order Update Error: ' . $e->getMessage(), ['order_id' => $order->id]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to update order details.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
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

    public function getTechPack(Request $request, Order $order)
    {
        $user = $request->user();
        if ($user->role === 'client' && $order->client?->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        return response()->json($this->techPackService->getFullOrderData($order->id));
    }

    public function saveTechPack(Request $request, Order $order)
    {
        $user = $request->user();
        if (!$user || !in_array($user->role, ['admin', 'superadmin', 'manager'])) {
            abort(403, 'Unauthorized action.');
        }

        try {
            $data = $request->validate([
                'fabric' => 'nullable|array',
                'trims' => 'nullable|array',
                'stitch' => 'nullable|array',
                'labels' => 'nullable|array',
                'marker' => 'nullable|array',
                'packaging' => 'nullable|array',
                'steps' => 'nullable|array',
                'parts' => 'nullable|array',
                'measurements' => 'nullable|array',
                'notes' => 'nullable|string',
                'detail_zoom' => 'nullable|string'
            ]);
            // 0. Pre-process Uploaded Images (Base64 to Physical Storage Paths)
            // This prevents DB limit crashes 'Data too long' and PDF layout issues.
            if (isset($data['detail_zoom']) && is_string($data['detail_zoom']) && str_starts_with($data['detail_zoom'], 'data:image')) {
                $file_data = explode(',', $data['detail_zoom']);
                if (count($file_data) > 1) {
                    $imgName = 'models/' . uniqid('zoom_') . '.png';
                    \Illuminate\Support\Facades\Storage::disk('public')->put($imgName, base64_decode($file_data[1]));
                    $data['detail_zoom'] = $imgName;
                }
            }

            if (isset($data['parts']) && is_array($data['parts'])) {
                foreach ($data['parts'] as &$p) {
                    if (isset($p['img']) && is_string($p['img']) && str_starts_with($p['img'], 'data:image')) {
                        $file_data = explode(',', $p['img']);
                        if (count($file_data) > 1) {
                            $imgName = 'models/' . uniqid('anatomy_') . '.png';
                            \Illuminate\Support\Facades\Storage::disk('public')->put($imgName, base64_decode($file_data[1]));
                            $p['img'] = $imgName;
                        } else {
                            $p['img'] = null;
                        }
                    }
                }
            }

            // 1. Primary Save to JSON field (Now safe since images are Paths)
            $order->update(['tech_pack' => $data]);

            // 2. Sync to Normalized Tables with Data Cleaning

            // A. Measurements
            // IMPORTANT: Only save entries with a real numeric value to the normalized table.
            // Entries like "breakdown" whose value is a JSON array are size-distribution data,
            // not dimensions — they are already stored in the tech_pack JSON column above.
            if (!empty($data['measurements']) && is_array($data['measurements'])) {
                $order->orderMeasurements()->delete();
                foreach ($data['measurements'] as $m) {
                    $point = trim($m['point'] ?? '');
                    if (empty($point)) continue;

                    $val = $m['value'] ?? null;

                    // Skip non-numeric values entirely — these are JSON payloads, not dimensions.
                    if (is_array($val)) continue;
                    if (is_string($val)) {
                        $trimmed = ltrim($val);
                        if (Str::startsWith($trimmed, '[') || Str::startsWith($trimmed, '{')) continue;
                    }

                    // Extract numeric value (handles "32 cm" -> 32.0)
                    $numericVal = ($val !== null && $val !== '')
                        ? (float) filter_var($val, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION)
                        : 0.0;

                    // Tolerance: must be numeric or null
                    $tol = $m['tol'] ?? $m['tolerance'] ?? null;
                    if ($tol !== null && !is_numeric($tol)) {
                        $tol = (float) filter_var($tol, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
                    }

                    $order->orderMeasurements()->create([
                        'point_of_measure' => $point,
                        'dimension_value'  => $numericVal,
                        'unit'             => $m['unit'] ?? 'cm',
                        'grading'          => isset($m['grading']) && is_array($m['grading']) ? $m['grading'] : null,
                        'tolerance'        => $tol,
                    ]);
                }
            }

            // B. Model Anatomy
            if (isset($data['parts']) && is_array($data['parts'])) {
                $order->anatomies()->delete();
                foreach ($data['parts'] as $p) {
                    $piece = $p['piece'] ?? null;
                    $part = $p['part'] ?? null;
                    if ($piece || $part) {
                        $imgPath = $p['img'] ?? null;

                        $order->anatomies()->create([
                            'piece_name' => $piece ?? '',
                            'part_name' => $part ?? '',
                            'count' => (int) ($p['qty'] ?? 1),
                            'image_path' => (is_string($imgPath) && strlen($imgPath) <= 255) ? $imgPath : null
                        ]);
                    }
                }
            }

            // C. Marker Plan
            if (isset($data['marker']) && is_array($data['marker'])) {
                $m = $data['marker'];
                $order->markerPlans()->updateOrCreate(
                    ['order_id' => $order->id],
                    [
                        'marker_length' => isset($m['length']) ? (float) filter_var($m['length'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : null,
                        'marker_width' => isset($m['width']) ? (float) filter_var($m['width'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : null,
                        'fabric_width' => isset($m['fabric_width']) ? (float) filter_var($m['fabric_width'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : null, // Use specific fabric_width if available
                        'efficiency' => isset($m['eff']) ? (float) filter_var($m['eff'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : null,
                        'part_count' => (int) ($m['parts'] ?? 0)
                    ]
                );
            }

            // D. Production Table
            $fabric = $data['fabric'] ?? [];
            $trims = $data['trims'] ?? [];
            $stitch = $data['stitch'] ?? [];
            $labels = $data['labels'] ?? [];
            $packaging = $data['packaging'] ?? [];

            $order->production()->updateOrCreate(
                ['order_id' => $order->id],
                [
                    'fabric_code' => $fabric['code'] ?? null,
                    'fabric_supplier' => $fabric['supplier'] ?? null,
                    'fabric_width' => isset($fabric['width']) ? (float) filter_var($fabric['width'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION) : null,
                    'dye_method' => $fabric['dye'] ?? null,
                    'fabric_finish' => $fabric['finish'] ?? null,
                    'zipper_type' => $trims['zipper'] ?? null,
                    'button_type' => $trims['button'] ?? null,
                    'cord_type' => $trims['cord'] ?? null,
                    'rib_type' => $trims['rib'] ?? null,
                    'thread_type' => $trims['thread'] ?? null,
                    'stitch_type' => $stitch['stitch'] ?? null,
                    'seam_type' => $stitch['seam'] ?? null,
                    'reinforcement' => $stitch['reinforcement'] ?? null,
                    'label_type' => $labels['main'] ?? null,
                    'label_position' => $labels['pos'] ?? null,
                    'packaging_type' => $packaging['type'] ?? null,
                    'folding_method' => $packaging['fold'] ?? null,
                    'carton_quantity' => $packaging['qty'] ?? null,
                    'barcode_required' => (bool) ($packaging['barcode'] ?? false),
                    'factory_notes' => $data['notes'] ?? null,
                    'sewing_sequence' => $data['steps'] ?? []
                ]
            );

            return response()->json([
                'success' => true,
                'message' => 'Tech Pack data persisted successfully.',
                'tech_pack' => $order->tech_pack
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Tech Pack Validation Error: ' . $e->getMessage(), ['order_id' => $order->id, 'errors' => $e->errors()]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed for Tech Pack data.',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            Log::error('Tech Pack Save Error: ' . $e->getMessage(), ['order_id' => $order->id, 'trace' => $e->getTraceAsString()]);
            return response()->json([
                'success' => false,
                'message' => 'Internal Server Error while persisting Tech Pack.',
                'error' => config('app.debug') ? $e->getMessage() : null
            ], 500);
        }
    }

    public function destroy(Request $request, Order $order)
    {
        $user = $request->user();
        
        // Authorization check
        if ($user->role === 'client' && $order->client?->user_id !== $user->id) {
            abort(403, 'Unauthorized action.');
        }

        $order->delete();

        return response()->json([
            'success' => true,
            'message' => 'Order deleted successfully'
        ]);
    }
}
