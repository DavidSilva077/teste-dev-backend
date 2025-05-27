<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class ImportTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        $this->user = User::factory()->create();
    }

    #[Test]
    public function it_can_import_csv_file()
    {
        Storage::fake('local');

        $file = UploadedFile::fake()->createWithContent(
            'data.csv',
            "date,value\n2025-05-01,10\n2025-05-01,20"
        );

        $response = $this->actingAs($this->user)
            ->postJson('/api/import', ['file' => $file]);

        $response->assertStatus(200)
                 ->assertJson(['message' => 'Importação iniciada.']);
    }

    #[Test]
    public function it_can_get_analysis_data()
    {
        \DB::table('imported_data')->insert([
            ['date' => '2025-05-01', 'value' => 10],
            ['date' => '2025-05-01', 'value' => 20],
            ['date' => '2025-05-02', 'value' => -5],
        ]);

        $response = $this->actingAs($this->user)
            ->getJson('/api/import/analysis');

        $response->assertStatus(200)
                 ->assertJsonStructure([['date', 'average', 'min', 'max', 'median']]);
    }
}
