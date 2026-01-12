<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\IntegrationJob;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ProcessIntegrationJob;

class IntegrationJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_integration_job()
    {
        Queue::fake();

        $payload = [
            'external_id' => '123',
            'nome' => 'Fulano',
            'cpf' => '12345678901',
        ];

        $response = $this->postJson('/api/integrations/customers', $payload);

        $response->assertStatus(202)
            ->assertJsonStructure([
                'id',
                'status',
            ])
            ->assertJson([
                'status' => 'PENDING',
            ]);

        $this->assertDatabaseHas('integration_jobs', [
            'external_id' => '123',
            'status' => 'PENDING',
        ]);

        Queue::assertPushed(ProcessIntegrationJob::class);
    }

    public function test_validates_required_fields()
    {
        $response = $this->postJson('/api/integrations/customers', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['external_id', 'cpf']);
    }

    public function test_can_get_job_status()
    {
        $job = IntegrationJob::create([
            'external_id' => '123',
            'payload' => [],
            'status' => 'PENDING',
        ]);

        $response = $this->getJson("/api/integrations/customers/{$job->id}");

        $response->assertStatus(200)
            ->assertJson([
                'id' => $job->id,
                'external_id' => '123',
                'status' => 'PENDING',
            ]);
    }
}
