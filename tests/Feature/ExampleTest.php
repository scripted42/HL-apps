<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    /**
     * A basic test example.
     */
    public function test_the_application_returns_a_successful_response(): void
    {
        $response = $this->get('/');

        $response->assertRedirect('/login');
    }

    /**
     * Test that authenticated users can render dashboard.
     */
    public function test_authenticated_user_can_render_dashboard(): void
    {
        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($user)->get('/dashboard');
        
        // Write the HTML to a temporary file in scratch so we can view it
        file_put_contents(base_path('html_dump.html'), $response->getContent());

        $response->assertStatus(200);
    }

    /**
     * Test that authenticated users can render transaction creator.
     */
    public function test_authenticated_user_can_render_transaction_creator(): void
    {
        $user = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
        ]);

        $response = $this->actingAs($user)->get('/transactions/create');

        $response->assertStatus(200);
    }
}
