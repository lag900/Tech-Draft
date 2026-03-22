<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Client;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class OrderCreationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Ensure unauthorized users cannot access the order creation wizard endpoint.
     */
    public function test_guest_cannot_access_order_creation_endpoint()
    {
        $response = $this->getJson('/api/orders');

        // Middleware should block guests
        $response->assertStatus(401);
    }

    /**
     * Ensure authorized users can validate basic validation rules.
     */
    public function test_authenticated_user_encounters_validation_errors()
    {
        $this->withoutMiddleware();
        Gate::after(function () { return true; });
        // Assuming your backend has a User factory
        $user = User::factory()->create(['role' => 'admin']);

        // Posting empty payload to trigger 422
        $response = $this->actingAs($user)->postJson('/api/orders', []);

        $response->assertStatus(422);
        // Assert specific validation errors are returned based on your controller
        $response->assertJsonValidationErrors(['title', 'season', 'year']);
    }

    /**
     * Test successful creation of an order record.
     */
    public function test_user_can_create_a_valid_order()
    {
        $this->withoutMiddleware();
        Gate::after(function () { return true; });
        $user = User::factory()->create(['role' => 'admin']);
        // Factory creation of client (or raw insert if no factory)
        $client = Client::create(['brand_name' => 'Demo Brand', 'name' => 'Demo', 'user_id' => $user->id]);
        $category = Category::create(['name' => 'Winter Collection', 'status' => 'active', 'type' => 'Apparel']);
        Storage::fake('public');

        $payload = [
            'title' => 'Spring Heavyweight Hoodie',
            'client_id' => $client->id,
            'design_front_image' => UploadedFile::fake()->image('front.jpg'),
            'product_code' => 'SHH-2026',
            'category_id' => $category->id,
            'season' => 'spring',
            'year' => 2026,
            'notes' => 'Priority shipping required.',
            'colors' => json_encode([['hex' => '#000000', 'name' => 'Black']]),
            'production_details' => json_encode([
                'quantity' => 500,
                'sizes_text' => 'S, M, L'
            ])
        ];

        $response = $this->actingAs($user)->postJson('/api/orders', $payload);

        // Expect successful creation
        $response->assertStatus(201);
        
        $this->assertDatabaseHas('orders', [
            'title' => 'Spring Heavyweight Hoodie',
            'product_code' => 'SHH-2026',
            'status' => 'pending', // or whatever your default status is
        ]);
    }
}
