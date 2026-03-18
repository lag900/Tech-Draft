<?php

namespace App\Services;

use App\Models\Order;

class OrderTechPackService
{
    /**
     * Get the unified data for an order's tech pack.
     * This serves as the single source of truth for both API and PDF.
     */
    public function getFullOrderData(int $orderId): array
    {
        $order = Order::with([
            'client', 
            'category', 
            'creator', 
            'images', 
            'production', 
            'anatomies', 
            'markerPlans', 
            'statusHistory.changer', 
            'orderColors', 
            'orderSizes', 
            'orderMeasurements'
        ])->findOrFail($orderId);

        $tpData = $order->tech_pack ?? [];
        $prod = $order->production;
        $marker = $order->markerPlans->first();
        
        // Ensure "cm" is appended only if the value exists and doesn't already contain it
        $fabWidth = $tpData['fabric']['width'] ?? $prod?->fabric_width ?? $order->fabric_details['width'] ?? $order->fabric_width;
        $fabWidthFormatted = $fabWidth ? (str_contains(strtolower($fabWidth), 'cm') ? $fabWidth : $fabWidth . ' cm') : '---';

        return [
            'product' => [
                'id' => $order->id,
                'order_code' => $order->order_code,
                'title' => $order->title,
                'status' => $order->status,
                'category_name' => $order->category?->name ?? '---',
                'season' => ($order->season ?? '') . ' ' . ($order->year ?? ''),
                'client_name' => $order->client?->brand_name ?? $order->brand_name ?? '---',
                'creator_name' => $order->creator?->name ?? '---',
                'front_image' => $order->design_front_image,
                'back_image' => $order->design_back_image,
                'technical_sketch' => $order->technical_sketch,
                'detail_zoom' => $tpData['detail_zoom'] ?? $order->detail_zoom ?? '',
                'total_quantity' => $this->calculateTotalQuantity($order),
                'colors' => $this->formatColors($order),
                'version' => $order->version_number ?? 1,
                'revision' => $order->revision_number ?? 0,
            ],
            'materials' => [
                'fabric' => [
                    'type' => $tpData['fabric']['code'] ?? $prod?->fabric_code ?? $order->fabric_details['type'] ?? $order->fabric_type ?? '---',
                    'supplier' => $tpData['fabric']['supplier'] ?? $prod?->fabric_supplier ?? $order->fabric_details['supplier'] ?? $order->fabric_supplier ?? '---',
                    'weight' => $order->fabric_details['weight'] ?? $order->fabric_weight ?? '---',
                    'composition' => $order->fabric_details['composition'] ?? $order->fabric_composition ?? '---',
                    'width' => $fabWidthFormatted,
                    'dye_method' => $tpData['fabric']['dye'] ?? $prod?->dye_method ?? '---',
                    'finish' => $tpData['fabric']['finish'] ?? $prod?->fabric_finish ?? $order->fabric_details['finish'] ?? $order->fabric_finish ?? '---',
                ],
                'trims' => [
                    'zipper' => $tpData['trims']['zipper'] ?? $prod?->zipper_type ?? $order->zipper_type ?? '---',
                    'button' => $tpData['trims']['button'] ?? $prod?->button_type ?? $order->button_type ?? '---',
                    'cord' => $tpData['trims']['cord'] ?? $prod?->cord_type ?? $order->cord_type ?? $order->drawcord_type ?? '---',
                    'rib' => $tpData['trims']['rib'] ?? $prod?->rib_type ?? $order->rib_type ?? '---',
                    'thread' => $tpData['trims']['thread'] ?? $prod?->thread_type ?? $order->thread_type ?? '---',
                ],
                'bom' => $this->generateBOM($order, $tpData, $prod),
            ],
            'measurements' => [
                'sizes' => $order->orderSizes->map(fn($s) => ['name' => $s->size_name, 'quantity' => $s->quantity])->toArray(),
                'points' => $this->formatMeasurements($order, $tpData),
                'tolerance' => $order->measurement_tolerance ?? '0.5 cm',
            ],
            'construction' => [
                'stitch_type' => $tpData['stitch']['stitch'] ?? $prod?->stitch_type ?? $order->stitching_type ?? '---',
                'seam_type' => $tpData['stitch']['seam'] ?? $prod?->seam_type ?? $order->seam_type ?? '---',
                'spi' => $tpData['stitch']['spi'] ?? $prod?->spi ?? $order->spi ?? '---',
                'thread_type' => $tpData['stitch']['thread'] ?? $prod?->thread_type ?? $order->thread_type ?? '---',
                'reinforcement' => $tpData['stitch']['reinforcement'] ?? $prod?->reinforcement ?? $order->reinforcement ?? '---',
                'seam_allowance' => $tpData['stitch']['allowance'] ?? $prod?->seam_allowance ?? $order->seam_allowance ?? '---',
            ],
            'production' => [
                'operations' => !empty($tpData['steps']) ? $tpData['steps'] : ($prod?->sewing_sequence ?? $order->sewing_sequence ?? []),
                'anatomy' => $this->formatAnatomy($order, $tpData),
                'marker' => [
                    'length' => $tpData['marker']['length'] ?? $marker?->marker_length ?? '---',
                    'width' => $tpData['marker']['width'] ?? $marker?->fabric_width ?? '---',
                    'efficiency' => $tpData['marker']['eff'] ?? $marker?->efficiency ?? '---',
                    'parts' => $tpData['marker']['parts'] ?? $marker?->part_count ?? '---',
                ],
            ],
            'packaging' => [
                'labels' => [
                    'main' => $tpData['labels']['main'] ?? $prod?->label_type ?? $order->main_label_type ?? '---',
                    'size' => $tpData['labels']['size'] ?? $prod?->size_label_type ?? $order->size_label_type ?? '---',
                    'care' => $tpData['labels']['care'] ?? $prod?->care_label_type ?? $order->care_label_type ?? '---',
                    'position' => $tpData['labels']['pos'] ?? $prod?->label_position ?? $order->label_position ?? '---',
                ],
                'packaging_type' => $tpData['packaging']['type'] ?? $prod?->packaging_type ?? $order->packaging_type ?? '---',
                'folding_method' => $tpData['packaging']['fold'] ?? $prod?->folding_method ?? $order->folding_method ?? '---',
                'carton_qty' => $tpData['packaging']['qty'] ?? $prod?->carton_quantity ?? $order->carton_qty ?? '---',
                'barcode_required' => $tpData['packaging']['barcode'] ?? (bool)($prod?->barcode_required ?? $order->barcode_required),
                'notes' => $tpData['notes'] ?? $prod?->factory_notes ?? $order->factory_notes ?? $order->notes_factory ?? '---',
            ]
        ];
    }

    protected function calculateTotalQuantity(Order $order): int
    {
        if ($order->production_details && isset($order->production_details['quantity'])) {
            return (int)$order->production_details['quantity'];
        }
        return (int)$order->orderSizes->sum('quantity');
    }

    protected function formatColors(Order $order): array
    {
        if ($order->orderColors->isNotEmpty()) {
            return $order->orderColors->map(fn($c) => [
                'name' => $c->color_name,
                'hex' => $c->color_hex,
                'code' => $c->color_code
            ])->toArray();
        }
        
        return is_array($order->colors) ? $order->colors : [];
    }

    protected function formatMeasurements(Order $order, array $tpData): array
    {
        if (!empty($tpData['measurements'])) {
            return $tpData['measurements'];
        }

        if ($order->orderMeasurements->isNotEmpty()) {
            return $order->orderMeasurements->map(fn($m) => [
                'point' => $m->point_of_measure,
                'value' => $m->dimension_value,
                'tol' => $m->tolerance ?? '0.5 cm'
            ])->toArray();
        }

        return [];
    }

    protected function formatAnatomy(Order $order, array $tpData): array
    {
        if (!empty($tpData['parts'])) {
            return $tpData['parts'];
        }

        if ($order->anatomies->isNotEmpty()) {
            return $order->anatomies->map(fn($a) => [
                'piece' => $a->piece_name,
                'part' => $a->part_name,
                'qty' => $a->count,
                'img' => $a->image_path
            ])->toArray();
        }

        return [];
    }

    protected function generateBOM(Order $order, array $tpData, $prod = null): array
    {
        // Logic similar to TechPackGenerator.vue but purely backend driven
        $fabType = $tpData['fabric']['code'] ?? $prod?->fabric_code ?? $order->fabric_details['type'] ?? $order->fabric_type ?? '---';
        $zipper = $tpData['trims']['zipper'] ?? $prod?->zipper_type ?? $order->zipper_type ?? '---';
        $button = $tpData['trims']['button'] ?? $prod?->button_type ?? $order->button_type ?? '---';
        
        $bom = [
            ['name' => 'Main Fabric', 'desc' => $fabType, 'unit' => 'MTR'],
        ];

        if ($zipper !== '---') {
            $bom[] = ['name' => 'Zipper', 'desc' => $zipper, 'unit' => 'PCS'];
        }
        if ($button !== '---') {
            $bom[] = ['name' => 'Buttons', 'desc' => $button, 'unit' => 'PCS'];
        }

        return $bom;
    }
}
