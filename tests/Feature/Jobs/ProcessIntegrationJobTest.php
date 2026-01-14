<?php

namespace Tests\Feature\Jobs;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\IntegrationJob;
use App\Jobs\ProcessIntegrationJob;

class ProcessIntegrationJobTest extends TestCase
{
    use RefreshDatabase;

    public function test_even_external_id_results_in_success()
    {
        $job = IntegrationJob::create([
            'external_id' => '124', // Even
            'payload' => [],
            'status' => 'PENDING',
        ]);

        (new ProcessIntegrationJob($job->id))->handle();

        $job->refresh();

        $this->assertEquals('SUCCESS', $job->status);
        $this->assertNull($job->last_error);
    }

    public function test_odd_external_id_results_in_error()
    {
        $job = IntegrationJob::create([
            'external_id' => '123', // Odd
            'payload' => [],
            'status' => 'PENDING',
        ]);

        (new ProcessIntegrationJob($job->id))->handle();

        $job->refresh();

        $this->assertEquals('ERROR', $job->status);
        $this->assertEquals('Falha na integração simulada, numero é impar', $job->last_error);
    }
}
