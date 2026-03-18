<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Ensure URLs are absolute for images
        $frontImage = $this->design_front_image ? asset('storage/' . $this->design_front_image) : null;
        $backImage = $this->design_back_image ? asset('storage/' . $this->design_back_image) : null;
        $technicalSketch = $this->technical_sketch ? asset('storage/' . $this->technical_sketch) : null;
        $detailZoom = $this->detail_zoom ? asset('storage/' . $this->detail_zoom) : null;

        $productImage = $this->images->first()?->file_path
            ? asset('storage/' . $this->images->first()->file_path)
            : ($this->product?->image_path ? asset('storage/' . $this->product->image_path) : ($frontImage ?? asset('images/product-placeholder.png')));


        return [
            'id' => $this->id,
            'order_code' => $this->order_code,
            'title' => $this->title,
            'status' => $this->status,
            'season' => $this->season,
            'year' => $this->year,
            'notes' => $this->notes,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            
            // Authorization checks passed down to frontend
            'can' => [
                'update' => $request->user()?->can('updateOrder', $this->resource) ?? false,
                'approve' => $request->user()?->can('approveSampling', $this->resource) ?? false,
                'export' => $request->user()?->can('exportTechPack', $this->resource) ?? false,
            ],

            // Flattened attributes & processed image URLs
            'design_front_image' => $frontImage,
            'design_back_image' => $backImage,
            'technical_sketch' => $technicalSketch,
            'detail_zoom' => $detailZoom,
            'product_image_url' => $productImage,
            
            'production_details' => $this->production_details,
            'fabric_details' => $this->fabric_details,
            
            // Relationships mapped (When Loaded)
            'client' => $this->whenLoaded('client'),
            'category' => $this->whenLoaded('category'),
            'creator' => $this->whenLoaded('creator'),
            
            'colors' => $this->whenLoaded('orderColors'),
            'sizes' => $this->whenLoaded('orderSizes'),
            'points_of_measure' => $this->whenLoaded('orderMeasurements'),
            
            'images' => $this->whenLoaded('images', function() {
                return $this->images->map(fn($img) => [
                    'id' => $img->id,
                    'file_path' => asset('storage/' . $img->file_path),
                ]);
            }),
            
            'anatomies' => $this->whenLoaded('anatomies', function() {
                 return $this->anatomies->map(fn($a) => [
                     'id' => $a->id,
                     'piece_name' => $a->piece_name,
                     'part_name' => $a->part_name,
                     'count' => $a->count,
                     'image_path' => $a->image_path ? asset('storage/' . $a->image_path) : null,
                 ]);
            }),
            
            'marker_plans' => $this->whenLoaded('markerPlans'),
            'production' => $this->whenLoaded('production'),
            'materials' => $this->whenLoaded('materials'),

            // Technical identification fields
            'product_code' => $this->product_code,
            'pattern_code' => $this->pattern_code,
            'order_number' => $this->order_number,
            'version_number' => $this->version_number,
            'last_update_date' => $this->last_update_date,
            'brand_name' => $this->brand_name,
            'target_marketplace' => $this->target_marketplace,

            // Trim & Label Details
            'zipper_type' => $this->zipper_type,
            'button_type' => $this->button_type,
            'cord_type' => $this->cord_type,
            'rib_type' => $this->rib_type,
            'stitching_type' => $this->stitching_type,
            'label_details' => $this->label_details,
            'main_label_type' => $this->main_label_type,
            'care_label_type' => $this->care_label_type,
            'size_label_type' => $this->size_label_type,
            'packaging_notes' => $this->packaging_notes,
            'packaging_type' => $this->packaging_type,
            'folding_method' => $this->folding_method,
            'barcode_required' => $this->barcode_required,
            
            'status_history' => $this->whenLoaded('statusHistory', function() {
                return $this->statusHistory->map(fn($h) => [
                   'id' => $h->id,
                   'old_status' => $h->old_status,
                   'new_status' => $h->new_status,
                   'created_at' => $h->created_at,
                   'changer' => $h->changer ? ['id' => $h->changer->id, 'name' => $h->changer->name] : null,
                ]);
            }),
        ];
    }
}
