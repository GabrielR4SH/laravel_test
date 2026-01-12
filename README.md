# Laravel Integration API (Versão Enxuta)

## Descrição
Projeto para processamento assíncrono de integrações.
- SQLite como banco de dados (simples e portátil).
- Fila Sync (processamento imediato para teste).

## Instalação

1. Instalar dependências:
   ```bash
   composer install
   ```
2. Configurar banco de dados:
   O projeto já vem configurado para SQLite. Apenas crie o arquivo e rode as migrações:
   ```powershell
   # Criar arquivo vazio (Windows PowerShell)
   New-Item -ItemType File -Path database/database.sqlite -Force
   
   # Rodar migrações
   php artisan migrate
   ```
3. Rodar servidor:
   ```bash
   php artisan serve
   ```
   Acesse: http://127.0.0.1:8000

## Endpoints

### 1. Criar Pedido de Integração
**POST** `/api/integrations/customers`
- **Body**:
  ```json
  {
    "external_id": "123",
    "nome": "Fulano",
    "cpf": "12345678901"
  }
  ```
- **Resposta**: 202 Accepted.

### 2. Consultar Status
**GET** `/api/integrations/customers/{id}`
- **Resposta**: JSON com status (PENDING, PROCESSING, SUCCESS, ERROR).

## Dashboard
Acesse `/integrations` no navegador para ver os últimos 20 registros.

## Regra de Negócio (Simulação)
- ID Externo PAR: Sucesso.
- ID Externo ÍMPAR: Erro.
