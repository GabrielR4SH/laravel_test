<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Integration Jobs</title>
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="{{ asset('css/integrations.css') }}">
</head>
<body>
    <div class="container">
        <h1>
            <i class="fas fa-sync-alt"></i>
            Jobs de Integração
        </h1>
        
        @if($jobs->count() > 0)
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Externo</th>
                    <th>Nome</th>
                    <th>CPF</th>
                    <th>Status</th>
                    <th>Erro</th>
                    <th>Criado em</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                @foreach($jobs as $job)
                <tr>
                    <td>{{ $job->id }}</td>
                    <td>{{ $job->external_id }}</td>
                    <td>{{ $job->payload['nome'] ?? '-' }}</td>
                    <td>{{ $job->payload['cpf'] ?? '-' }}</td>
                    <td><span class="status {{ $job->status }}">{{ $job->status }}</span></td>
                    <td>{{ $job->last_error ?? '-' }}</td>
                    <td>{{ $job->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <div class="actions">
                            <button class="btn btn-edit" onclick="openEditModal({{ $job->id }}, '{{ addslashes($job->external_id) }}', '{{ addslashes($job->payload['nome'] ?? '') }}', '{{ addslashes($job->payload['cpf'] ?? '') }}')">
                                <i class="fas fa-edit"></i> Editar
                            </button>
                            <button class="btn btn-delete" onclick="openDeleteModal({{ $job->id }})">
                                <i class="fas fa-trash"></i> Deletar
                            </button>
                        </div>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        
        <!-- Pagination -->
        <div class="pagination">
            {{ $jobs->links('pagination::simple-default') }}
        </div>
        @else
        <div class="empty-state">
            <i class="fas fa-inbox"></i>
            <h3>Nenhum job encontrado</h3>
            <p>Crie um novo job através da API</p>
        </div>
        @endif
    </div>
    
    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-edit"></i> Editar Job</h2>
                <span class="close" onclick="closeEditModal()">&times;</span>
            </div>
            <form id="editForm">
                <input type="hidden" id="editJobId">
                <div class="form-group">
                    <label for="editExternalId">ID Externo</label>
                    <input type="text" id="editExternalId" required>
                </div>
                <div class="form-group">
                    <label for="editNome">Nome</label>
                    <input type="text" id="editNome">
                </div>
                <div class="form-group">
                    <label for="editCpf">CPF</label>
                    <input type="text" id="editCpf" maxlength="11" required>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" onclick="closeEditModal()">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
    
    <!-- Delete Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2><i class="fas fa-exclamation-triangle"></i> Confirmar Exclusão</h2>
                <span class="close" onclick="closeDeleteModal()">&times;</span>
            </div>
            <p style="margin-bottom: 20px; color: #666;">Tem certeza que deseja excluir este job? Esta ação não pode ser desfeita.</p>
            <input type="hidden" id="deleteJobId">
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeDeleteModal()">Cancelar</button>
                <button type="button" class="btn btn-delete" onclick="confirmDelete()">
                    <i class="fas fa-trash"></i> Deletar
                </button>
            </div>
        </div>
    </div>
    
    <!-- Custom JavaScript -->
    <script src="{{ asset('js/integrations.js') }}"></script>
</body>
</html>
