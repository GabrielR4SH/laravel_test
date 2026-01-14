// Edit Modal Functions
function openEditModal(id, externalId, nome, cpf) {
    document.getElementById('editJobId').value = id;
    document.getElementById('editExternalId').value = externalId;
    document.getElementById('editNome').value = nome;
    document.getElementById('editCpf').value = cpf;
    document.getElementById('editModal').classList.add('active');
}

function closeEditModal() {
    document.getElementById('editModal').classList.remove('active');
}

// Delete Modal Functions
function openDeleteModal(id) {
    document.getElementById('deleteJobId').value = id;
    document.getElementById('deleteModal').classList.add('active');
}

function closeDeleteModal() {
    document.getElementById('deleteModal').classList.remove('active');
}

// Edit Form Submit
document.getElementById('editForm').addEventListener('submit', async (e) => {
    e.preventDefault();
    
    const id = document.getElementById('editJobId').value;
    const data = {
        external_id: document.getElementById('editExternalId').value,
        nome: document.getElementById('editNome').value,
        cpf: document.getElementById('editCpf').value
    };
    
    try {
        const response = await fetch(`/api/integrations/customers/${id}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        if (response.ok) {
            alert('Job atualizado com sucesso!');
            location.reload();
        } else {
            alert('Erro ao atualizar job');
        }
    } catch (error) {
        alert('Erro na requisição');
    }
});

// Delete Confirm
async function confirmDelete() {
    const id = document.getElementById('deleteJobId').value;
    
    try {
        const response = await fetch(`/api/integrations/customers/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        if (response.ok) {
            alert('Job deletado com sucesso!');
            location.reload();
        } else {
            alert('Erro ao deletar job');
        }
    } catch (error) {
        alert('Erro na requisição');
    }
}

// Close modal on outside click
window.onclick = function(event) {
    if (event.target.classList.contains('modal')) {
        event.target.classList.remove('active');
    }
}
