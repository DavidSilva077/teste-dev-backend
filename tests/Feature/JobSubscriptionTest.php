<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\Job;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class JobSubscriptionTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_can_subscribe_a_candidate_to_a_job()
    {
        $job = Job::factory()->create(['paused' => false]);
        $candidate = Candidate::factory()->create();

        $response = $this->actingAs($this->user)
            ->postJson("/api/jobs/{$job->id}/subscribe/{$candidate->id}");

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Inscrição realizada com sucesso.']);
    }

    #[Test]
    public function it_cannot_subscribe_when_job_is_paused()
    {
        $job = Job::factory()->create(['paused' => true]);
        $candidate = Candidate::factory()->create();

        $response = $this->actingAs($this->user)
            ->postJson("/api/jobs/{$job->id}/subscribe/{$candidate->id}");

        $response->assertStatus(403)
                 ->assertJson(['message' => 'Vaga pausada. Inscrição não permitida.']);
    }
}
