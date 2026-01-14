# Laravel Integration API

## Descrição
Projeto para processamento assíncrono de integrações com sistema de filas.
- SQLite como banco de dados (simples e portátil)
- Fila Database (processamento em background)
- Interface web para visualização e gerenciamento

## Instalação

1. Instalar dependências:
   ```bash
   composer install
   ```

2. Configurar ambiente:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

3. Configurar banco de dados:
   O projeto já vem configurado para SQLite. Apenas crie o arquivo e rode as migrações:
   ```powershell
   # Criar arquivo vazio (Windows PowerShell)
   New-Item -ItemType File -Path database/database.sqlite -Force
   
   # Rodar migrações
   php artisan migrate
   ```

4. Rodar servidor e queue worker:
   ```bash
   # Terminal 1: Servidor
   php artisan serve
   
   # Terminal 2: Queue Worker (processar jobs em background)
   php artisan queue:work
   ```
   Acesse: http://127.0.0.1:8000

## Endpoints da API

### 1. Criar Pedido de Integração
**POST** `/api/integrations/customers`

**Body:**
```json
{
  "external_id": "123",
  "nome": "Fulano",
  "cpf": "12345678901"
}
```

**Resposta:** 202 Accepted
```json
{
  "id": 1,
  "status": "PENDING"
}
```

---

### 2. Consultar Status
**GET** `/api/integrations/customers/{id}`

**Resposta:** 200 OK
```json
{
  "id": 1,
  "external_id": "123",
  "status": "SUCCESS",
  "last_error": null
}
```

**Status possíveis:** `PENDING`, `PROCESSING`, `SUCCESS`, `ERROR`

---

### 3. Atualizar Job (Editar)
**PUT** `/api/integrations/customers/{id}`

**Body:**
```json
{
  "external_id": "456",
  "nome": "Fulano Atualizado",
  "cpf": "98765432109"
}
```

**Resposta:** 200 OK
```json
{
  "message": "Job atualizado e reenviado para processamento",
  "job": { ... }
}
```

> **Nota:** Ao editar, o job volta para status `PENDING` e é re-processado pela fila.

---

### 4. Deletar Job
**DELETE** `/api/integrations/customers/{id}`

**Resposta:** 200 OK
```json
{
  "message": "Job removido com sucesso"
}
```

---

## Interface Web

### Dashboard de Integração
Acesse `/integrations` no navegador para:
- Visualizar jobs paginados (8 por página)
- Editar jobs existentes (via modal)
- Deletar jobs (com confirmação)
- Ver status em tempo real

**URL:** http://127.0.0.1:8000/integrations

---

## Regra de Negócio (Simulação)

A lógica de processamento simula uma integração externa:
- **ID Externo PAR** (2, 4, 100): ✅ Sucesso
- **ID Externo ÍMPAR** (1, 3, 101): ❌ Erro ("Falha na integração simulada, numero é impar")

---

## Testes Automatizados

O projeto possui **12 testes** cobrindo todas as funcionalidades:

```bash
php artisan test
```

### Suites de Teste:

#### `Tests\Feature\Api\IntegrationJobTest` (3 testes)
- Criação de job
- Validação de campos obrigatórios
- Consulta de status

#### `Tests\Feature\Api\IntegrationJobCrudTest` (7 testes)
- Atualização de job
- Validação em update
- Reset de status/erro ao editar
- Exclusão de job
- Erros 404 (update/delete)
- Re-dispatch de job após edição

#### `Tests\Feature\Jobs\ProcessIntegrationJobTest` (2 testes)
- Processamento com sucesso (ID par)
- Processamento com erro (ID ímpar)

**Resultado esperado:** `Tests: 12 passed`

---

## Estrutura do Projeto

```
app/
├── Http/Controllers/Api/
│   └── IntegrationJobController.php  # CRUD endpoints
├── Jobs/
│   └── ProcessIntegrationJob.php     # Lógica de processamento
└── Models/
    └── IntegrationJob.php             # Model

public/
├── css/
│   └── integrations.css               # Estilos da interface
└── js/
    └── integrations.js                # Lógica dos modais

resources/views/integrations/
└── index.blade.php                    # Dashboard web

tests/Feature/
├── Api/
│   ├── IntegrationJobTest.php
│   └── IntegrationJobCrudTest.php
└── Jobs/
    └── ProcessIntegrationJobTest.php
```

---

## Configurações Importantes

### Timezone
Configurado para **America/Sao_Paulo** em `config/app.php`

### Fila
Configurada para usar **database** em `.env`:
```env
QUEUE_CONNECTION=database
```

---

## Como Testar o Fluxo Completo

1. **Inicie os servidores:**
   ```bash
   php artisan serve          # Terminal 1
   php artisan queue:work     # Terminal 2
   ```

2. **Crie um job (ID ímpar = erro):**
   ```bash
   curl -X POST http://127.0.0.1:8000/api/integrations/customers \
     -H "Content-Type: application/json" \
     -d '{"external_id": "35", "cpf": "12345678901", "nome": "Teste"}'
   ```

3. **Veja o processamento no Terminal 2:**
   ```
   Processing: App\Jobs\ProcessIntegrationJob
   Processed:  App\Jobs\ProcessIntegrationJob
   ```

4. **Acesse a interface:**
   - Abra: http://127.0.0.1:8000/integrations
   - Veja o job com status `ERROR`
   - Clique em "Editar"
   - Mude o `external_id` para `36` (par)
   - Salve e veja o status mudar para `SUCCESS`

