<?php

namespace Tests\Feature;

use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class JobTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_can_list_jobs()
    {
        Job::factory()->count(5)->create();

        $response = $this->actingAs($this->user)->getJson('/api/jobs');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data']);
    }

    #[Test]
    public function it_can_create_a_job()
    {
        $payload = [
            'title' => 'Desenvolvedor PHP',
            'description' => 'Vaga para PHP pleno.',
            'type' => 'clt',
            'paused' => false,
        ];

        $response = $this->actingAs($this->user)->postJson('/api/jobs', $payload);

        $response->assertStatus(201)
                 ->assertJsonFragment(['title' => 'Desenvolvedor PHP']);
    }

    #[Test]
    public function it_can_update_a_job()
    {
        $job = Job::factory()->create();

        $payload = ['title' => 'Desenvolvedor Laravel'];

        $response = $this->actingAs($this->user)->putJson("/api/jobs/{$job->id}", $payload);

        $response->assertStatus(200)
                 ->assertJsonFragment(['title' => 'Desenvolvedor Laravel']);
    }

    #[Test]
    public function it_can_delete_a_job()
    {
        $job = Job::factory()->create();

        $response = $this->actingAs($this->user)->deleteJson("/api/jobs/{$job->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Deleted successfully']);
    }
}
