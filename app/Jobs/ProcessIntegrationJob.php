<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\IntegrationJob;

class ProcessIntegrationJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $integrationJobId;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($integrationJobId)
    {
        $this->integrationJobId = $integrationJobId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $job = IntegrationJob::find($this->integrationJobId);

        if (!$job) {
            return;
        }

        try {
            $job->update(['status' => 'PROCESSING']);

            // Simulate external integration
            $externalIdInt = intval($job->external_id);

            if ($externalIdInt % 2 === 0) {
                // Even -> Success
                $job->update([
                    'status' => 'SUCCESS',
                    'last_error' => null,
                ]);
            } else {
                // Odd -> Error
                $job->update([
                    'status' => 'ERROR',
                    'last_error' => 'Falha na integração simulada',
                ]);
            }
        } catch (\Throwable $e) {
            $job->update([
                'status' => 'ERROR',
                'last_error' => $e->getMessage(),
            ]);
        }
    }
}
