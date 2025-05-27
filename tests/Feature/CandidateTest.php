<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CandidateTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_can_list_candidates()
    {
        Candidate::factory()->count(5)->create();

        $response = $this->actingAs($this->user)->getJson('/api/candidates');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data']);
    }

    #[Test]
    public function it_can_create_a_candidate()
    {
        $payload = [
            'name' => 'João Silva',
            'email' => 'joao@example.com',
        ];

        $response = $this->actingAs($this->user)->postJson('/api/candidates', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment(['email' => 'joao@example.com']);
    }

    #[Test]
    public function it_can_update_a_candidate()
    {
        $candidate = Candidate::factory()->create();

        $payload = ['name' => 'João Atualizado'];

        $response = $this->actingAs($this->user)->putJson("/api/candidates/{$candidate->id}", $payload);

        $response->assertStatus(200)
                 ->assertJsonFragment(['name' => 'João Atualizado']);
    }

    #[Test]
    public function it_can_delete_a_candidate()
    {
        $candidate = Candidate::factory()->create();

        $response = $this->actingAs($this->user)->deleteJson("/api/candidates/{$candidate->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Deleted successfully']);
    }
}
