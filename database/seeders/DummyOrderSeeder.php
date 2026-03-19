<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Order;
use App\Models\Design;
use App\Models\Client;
use App\Models\Category;
use App\Models\User;

class DummyOrderSeeder extends Seeder
{
    public function run()
    {
        $client = Client::first();
        $category = Category::where('type', 'item')->first();
        $user = User::where('role', 'admin')->first();

        // 1. Create Design
        $design = Design::create([
            'name' => 'Tech Pant V1',
            'product_code' => 'PANT-001',
            'season' => 'SS24',
            'fabric_type' => 'Premium Cotton Blend',
            'category_id' => $category->id,
            'description' => 'A comfortable and stretchy pant.',
            'user_id' => $user->id,
            'client_id' => $client->id,
            'version' => '1.0',
            'sizes' => [
                ['size' => 'S', 'quantity' => 50],
                ['size' => 'M', 'quantity' => 100],
                ['size' => 'L', 'quantity' => 50]
            ],
            'measurements' => [
                ['point' => 'Waist', 'value' => '32"', 'tolerance' => '0.5"'],
                ['point' => 'Inseam', 'value' => '30"', 'tolerance' => '0.5"'],
                ['point' => 'Leg Opening', 'value' => '14"', 'tolerance' => '0.25"']
            ],
        ]);

        // 2. Create Order
        Order::create([
            'order_code' => 'ORD-TEST-001',
            'title' => 'Tech Pant V1 Production',
            'client_id' => $client->id,
            'category_id' => $category->id,
            'season' => 'summer',
            'year' => date('Y'),
            'design_id' => $design->id,
            'status' => 'pending',
            'created_by' => $client->user_id,
            'product_code' => 'PANT-001',
            'notes' => 'Please follow the design specs exactly.',
            'fabric_details' => [
                'type' => 'Premium Cotton',
                'weight' => '250gsm',
                'texture' => 'Smooth',
                'composition' => '98% Cotton 2% Spandex',
                'yarn_type' => 'Combed ringspun',
                'structure' => 'Twill tape'
            ],
            'production_details' => [
                'quantity' => 200,
                'sizes' => ['S', 'M', 'L'],
                'item_type' => 'Pant',
                'fit' => 'Slim'
            ],
            'colors' => [
                ['name' => 'Navy Blue', 'hex' => '#0A1128', 'pantone' => '19-3921 TCX']
            ],
            'measurements' => [
                ['point' => 'Waist', 'value' => '32"', 'tolerance' => '0.5"']
            ],
            'main_label_type' => 'Woven Label',
            'care_label_type' => 'Printed Satin',
            'size_label_type' => 'Woven',
            'hangtag_type' => 'Matte Cardboard'
        ]);
        
        $order2 = Order::create([
            'order_code' => 'ORD-TEST-002',
            'title' => 'Classic Tee',
            'client_id' => $client->id,
            'category_id' => $category->id,
            'season' => 'summer',
            'year' => date('Y'),
            'status' => 'draft',
            'created_by' => $client->user_id,
        ]);
    }
}
