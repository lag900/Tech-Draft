<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin user
        User::create([
            'name' => 'System Admin',
            'email' => 'admin@orderflow.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
        ]);

        // Factory User
        User::create([
            'name' => 'Main Factory',
            'email' => 'factory@orderflow.com',
            'password' => bcrypt('password'),
            'role' => 'factory',
        ]);

        // Demo Client
        $clientUser = User::create([
            'name' => 'Demo Client',
            'email' => 'client@orderflow.com',
            'password' => bcrypt('password'),
            'role' => 'client',
        ]);

        \App\Models\Client::create([
            'user_id' => $clientUser->id,
            'brand_name' => 'Fashion Brand Co.',
        ]);

        // Categories
        $women = \App\Models\Category::create(['name' => 'Women Clothing', 'type' => 'parent']);
        $pants = \App\Models\Category::create(['name' => 'Pants', 'parent_id' => $women->id, 'type' => 'item']);
        $slim = \App\Models\Category::create(['name' => 'Slim', 'parent_id' => $pants->id, 'type' => 'fit_cut']);
        \App\Models\Category::create(['name' => 'Wide', 'parent_id' => $pants->id, 'type' => 'fit_cut']);
        \App\Models\Category::create(['name' => 'Straight', 'parent_id' => $pants->id, 'type' => 'fit_cut']);

        // Measurement Template for Pants
        \App\Models\MeasurementTemplate::create([
            'category_id' => $slim->id,
            'labels' => ['Length', 'Waist', 'Hip', 'Thigh', 'Leg Opening'],
        ]);

        // Fabrics
        \App\Models\Fabric::create(['name' => 'Cotton', 'type' => 'Natural']);
        \App\Models\Fabric::create(['name' => 'Polyester', 'type' => 'Synthetic']);
        \App\Models\Fabric::create(['name' => 'Linen', 'type' => 'Natural']);
    }
}
