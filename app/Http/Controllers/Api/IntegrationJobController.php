<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\IntegrationJob;
use App\Jobs\ProcessIntegrationJob;

class IntegrationJobController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'external_id' => 'required',
            'cpf' => 'required|digits:11',
        ]);

        $job = IntegrationJob::create([
            'external_id' => $validated['external_id'],
            'payload' => $request->all(),
            'status' => 'PENDING',
        ]);

        ProcessIntegrationJob::dispatch($job->id);

        return response()->json([
            'id' => $job->id,
            'status' => $job->status,
        ], 202);
    }

    public function show($id)
    {
        $job = IntegrationJob::findOrFail($id);

        return response()->json([
            'id' => $job->id,
            'external_id' => $job->external_id,
            'status' => $job->status,
            'last_error' => $job->last_error,
        ]);
    }
}
