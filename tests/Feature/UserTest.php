<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_can_list_users()
    {
        User::factory()->count(5)->create();

        $response = $this->actingAs($this->user)->getJson('/api/users');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data']);
    }

    #[Test]
    public function it_can_create_a_user()
    {
        $payload = [
            'name' => 'Test User',
            'email' => 'test@example.com',
            'password' => 'password',
            'type' => 'recruiter',
        ];

        $response = $this->actingAs($this->user)->postJson('/api/users', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment(['email' => 'test@example.com']);
    }

    #[Test]
    public function it_can_update_a_user()
    {
        $user = User::factory()->create();

        $payload = ['name' => 'Updated Name'];

        $response = $this->actingAs($this->user)->putJson("/api/users/{$user->id}", $payload);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'Updated Name']);
    }

    #[Test]
    public function it_can_delete_a_user()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->user)->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Deleted successfully']);
    }
}
