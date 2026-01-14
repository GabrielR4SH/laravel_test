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

    public function update(Request $request, $id)
    {
        $job = IntegrationJob::findOrFail($id);

        $validated = $request->validate([
            'external_id' => 'required',
            'cpf' => 'required|digits:11',
            'nome' => 'nullable|string',
        ]);

        // Atualiza os dados
        $payload = $job->payload;
        $payload['nome'] = $request->nome ?? $payload['nome'] ?? '';
        $payload['cpf'] = $validated['cpf'];
        
        $job->update([
            'external_id' => $validated['external_id'],
            'payload' => $payload,
            'status' => 'PENDING',
            'last_error' => null,
        ]);

        // Re-envia para a fila
        ProcessIntegrationJob::dispatch($job->id);

        return response()->json([
            'message' => 'Job atualizado e reenviado para processamento',
            'job' => $job
        ]);
    }

    public function destroy($id)
    {
        $job = IntegrationJob::findOrFail($id);
        $job->delete();

        return response()->json(['message' => 'Job removido com sucesso']);
    }
}
