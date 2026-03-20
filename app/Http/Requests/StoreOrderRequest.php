<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreOrderRequest extends FormRequest
{
    public function authorize()
    {
        return true; // Assume authorization is handled by middleware
    }

    protected function prepareForValidation()
    {
        $data = $this->all();
        
        $user = $this->user();
        if ($user && $user->role === 'client') {
            $data['client_id'] = $user->client?->id;
        }

        if (is_string($data['fabric_details'] ?? null)) $data['fabric_details'] = json_decode($data['fabric_details'], true);
        if (is_string($data['colors'] ?? null)) $data['colors'] = json_decode($data['colors'], true);
        if (is_string($data['measurements'] ?? null)) $data['measurements'] = json_decode($data['measurements'], true);
        if (is_string($data['production_details'] ?? null)) $data['production_details'] = json_decode($data['production_details'], true);

        $this->merge($data);
    }

    public function rules()
    {
        return [
            'title' => 'required|string|max:255',
            'client_id' => 'required|exists:clients,id',
            'category_id' => 'nullable|exists:categories,id',
            'product_id' => 'nullable|exists:products,id',
            'season' => 'required|string',
            'year' => 'required|integer',
            
            // Validate fabric data
            'fabric_details' => 'nullable|array',
            'fabric_details.type' => 'nullable|string',
            
            'colors' => 'nullable|array',
            'measurements' => 'nullable|array',
            'production_details' => 'nullable|array',
            
            // Sizes & Quantity validation
            'production_details.sizes' => 'nullable|array',
            'production_details.quantity' => 'required|numeric|min:1',
            
            'notes' => 'nullable|string',
            
            // File uploads validation
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:10240',
            'design_front_image' => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'design_back_image' => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'technical_sketch' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:10240',
            'reference_images.*' => 'nullable|image|mimes:jpg,jpeg,png|max:10240',

            // Factory tech specs
            'zipper_type' => 'nullable|string',
            'button_type' => 'nullable|string',
            'drawcord_type' => 'nullable|string',
            'rib_type' => 'nullable|string',
            'stitching_type' => 'nullable|string',
            'main_label_type' => 'nullable|string',
            'care_label_type' => 'nullable|string',
            'size_label_type' => 'nullable|string',
            'hangtag_type' => 'nullable|string',
            'packaging_type' => 'nullable|string',
            'folding_method' => 'nullable|string',
            'barcode_required' => 'nullable|boolean',
            'pattern_code' => 'nullable|string',
            'version_number' => 'nullable|integer',
            'revision_number' => 'nullable|integer',
        ];
    }

    public function messages()
    {
        return [
            'production_details.quantity.required' => 'Total quantity is required.',
            'production_details.quantity.min' => 'Total quantity must be greater than 0.',
            'title.required' => 'Design title is required.',
            'season.required' => 'Season is required.',
            'client_id.required' => 'Client is required.',
            'design_front_image.required' => 'The Front View image is required as the primary design reference.'
        ];
    }
}
