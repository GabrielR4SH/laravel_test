<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\IntegrationJob;
use Illuminate\Support\Facades\Queue;
use App\Jobs\ProcessIntegrationJob;

class IntegrationJobCrudTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Testa se é possível atualizar um job de integração existente.
     * Verifica se os dados são atualizados corretamente e se o job é re-enviado para a fila.
     */
    public function test_can_update_integration_job()
    {
        Queue::fake(); // Simula a fila para não processar de verdade

        $job = IntegrationJob::create([
            'external_id' => '123',
            'payload' => ['nome' => 'Original', 'cpf' => '12345678901'],
            'status' => 'SUCCESS',
            'last_error' => null,
        ]);

        $updateData = [
            'external_id' => '456',
            'nome' => 'Atualizado',
            'cpf' => '98765432109',
        ];

        $response = $this->putJson("/api/integrations/customers/{$job->id}", $updateData);

        $response->assertStatus(200)
            ->assertJsonStructure(['message', 'job']);

        $this->assertDatabaseHas('integration_jobs', [
            'id' => $job->id,
            'external_id' => '456',
            'status' => 'PENDING',
        ]);

        // Verifica que o job foi re-enviado para a fila
        Queue::assertPushed(ProcessIntegrationJob::class);
    }

    /**
     * Testa se a validação de campos obrigatórios funciona no update.
     * Deve retornar erro 422 quando external_id ou cpf estiverem inválidos.
     */
    public function test_update_validates_required_fields()
    {
        $job = IntegrationJob::create([
            'external_id' => '123',
            'payload' => [],
            'status' => 'PENDING',
        ]);

        $response = $this->putJson("/api/integrations/customers/{$job->id}", [
            'external_id' => '', // Vazio
            'cpf' => '123', // Inválido (menos de 11 dígitos)
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['external_id', 'cpf']);
    }

    /**
     * Testa se ao atualizar um job com erro, o status volta para PENDING
     * e o campo last_error é limpo (resetado para null).
     */
    public function test_update_resets_status_and_error()
    {
        Queue::fake();

        $job = IntegrationJob::create([
            'external_id' => '123',
            'payload' => ['cpf' => '12345678901'],
            'status' => 'ERROR',
            'last_error' => 'Erro anterior',
        ]);

        $response = $this->putJson("/api/integrations/customers/{$job->id}", [
            'external_id' => '456',
            'cpf' => '12345678901',
        ]);

        $response->assertStatus(200);

        $job->refresh();
        $this->assertEquals('PENDING', $job->status);
        $this->assertNull($job->last_error);
    }

    /**
     * Testa se é possível deletar um job de integração.
     * Verifica se o registro é removido do banco de dados.
     */
    public function test_can_delete_integration_job()
    {
        $job = IntegrationJob::create([
            'external_id' => '123',
            'payload' => [],
            'status' => 'PENDING',
        ]);

        $response = $this->deleteJson("/api/integrations/customers/{$job->id}");

        $response->assertStatus(200)
            ->assertJson(['message' => 'Job removido com sucesso']);

        $this->assertDatabaseMissing('integration_jobs', [
            'id' => $job->id,
        ]);
    }

    /**
     * Testa se ao tentar deletar um job inexistente, retorna erro 404.
     */
    public function test_delete_returns_404_for_nonexistent_job()
    {
        $response = $this->deleteJson('/api/integrations/customers/99999');

        $response->assertStatus(404);
    }

    /**
     * Testa se ao tentar atualizar um job inexistente, retorna erro 404.
     */
    public function test_update_returns_404_for_nonexistent_job()
    {
        $response = $this->putJson('/api/integrations/customers/99999', [
            'external_id' => '123',
            'cpf' => '12345678901',
        ]);

        $response->assertStatus(404);
    }

    /**
     * Testa se ao atualizar um job, ele é re-enviado para a fila com o ID correto.
     * Isso garante que o job será reprocessado após a edição.
     */
    public function test_update_dispatches_job_with_correct_id()
    {
        Queue::fake();

        $job = IntegrationJob::create([
            'external_id' => '100',
            'payload' => ['cpf' => '12345678901'],
            'status' => 'SUCCESS',
        ]);

        $this->putJson("/api/integrations/customers/{$job->id}", [
            'external_id' => '200',
            'cpf' => '12345678901',
        ]);

        Queue::assertPushed(ProcessIntegrationJob::class, function ($queuedJob) use ($job) {
            return $queuedJob->integrationJobId === $job->id;
        });
    }
}
