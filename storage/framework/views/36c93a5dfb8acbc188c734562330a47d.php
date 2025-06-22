

<?php $__env->startSection('title', 'Clientes'); ?>

<?php $__env->startSection('content'); ?>
    <style>
        :root {
            --terracota: #A0522D;
            --white:     #ffffff;
        }

        /* Card */
        .card {
            border: none;
            border-radius: .5rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .1);
            margin-bottom: 1.5rem;
        }
        .card-header {
            background-color: var(--terracota);
            color: var(--white);
            border-bottom: none;
            font-weight: 500;
        }

        /* Botão “Novo Cliente” */
        .card-tools .btn-success {
            background-color: var(--terracota);
            border-color: var(--terracota);
        }

        /* Tabela hover */
        #clientsTable.table-striped tbody tr:hover {
            background-color: rgba(160,82,45, .1);
        }

        /* Espaçamento entre os botões de ação */
        .edit-btn + .delete-btn {
            margin-left: .5rem;
        }
    </style>

    <div class="card">
        <div class="card-header">
            <div class="card-tools">
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#clientModal">
                    <i class="fas fa-plus"></i> Novo Cliente
                </button>
            </div>
        </div>
        <div class="card-body">
            <table id="clientsTable" class="table table-bordered table-striped w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Observações</th>
                        <th style="width: 80px">Ações</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="clientModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="clientModalLabel">Novo Cliente</h5>
                </div>
                <form id="clientForm">
                    <?php echo csrf_field(); ?>
                    <div class="modal-body">
                        <input type="hidden" id="client_id" name="id">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nome do Cliente</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="notes" class="form-label">Observações</label>
                            <textarea class="form-control" id="notes" name="notes" rows="4"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="clientFormSubmitButton">Salvar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(function () {
    // --- SETUP GLOBAL E FUNÇÕES ---
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' } });
    const dtLanguage = { "sEmptyTable": "Nenhum cliente encontrado", "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros", "sInfoEmpty": "Mostrando 0 até 0 de 0 registros", "sInfoFiltered": "(Filtrados de _MAX_ registros no total)", "sLengthMenu": "_MENU_ resultados por página", "sLoadingRecords": "Carregando...", "sProcessing": "Processando...", "sZeroRecords": "Nenhum registro encontrado", "sSearch": "Pesquisar", "oPaginate": { "sNext": "Próximo", "sPrevious": "Anterior", "sFirst": "Primeiro", "sLast": "Último" } };
    const handleAjaxError = xhr => { Swal.close(); if (xhr.status === 419) { Swal.fire('Sessão Expirada!', 'Recarregue a página.', 'error').then(() => location.reload()); } else if (xhr.status === 422) { var errors = xhr.responseJSON.errors; var errorHtml = '<ul class="text-start">'; $.each(errors, (k, v) => { errorHtml += `<li>${v[0]}</li>`; }); errorHtml += '</ul>'; Swal.fire({title: 'Erro de Validação', html: errorHtml, icon: 'error'}); } else { Swal.fire('Erro!', 'Ocorreu um erro inesperado.', 'error'); } };

    // --- DATATABLE ---
    const clientsTable = $('#clientsTable').DataTable({
        processing: true,
        ajax: "<?php echo e(route('admin.clients.data')); ?>",
        columns: [
            { data: 'id' }, { data: 'name' },
            { data: 'notes', render: data => data ? data.substring(0, 50) + '...' : '' },
            { data: null, searchable: false, orderable: false, render: (data, type, row) => `
                <button class="btn btn-sm btn-primary edit-btn" data-id="${row.id}" data-name="${row.name}" data-notes="${row.notes || ''}"><i class="fas fa-edit"></i></button>
                <button class="btn btn-sm btn-danger delete-btn" data-id="${row.id}" data-name="${row.name}"><i class="fas fa-trash"></i></button>
            `}
        ],
        language: dtLanguage
    });

    // --- LÓGICA DO MODAL ---
    $('[data-bs-target="#clientModal"]').on('click', function() {
        $('#clientForm')[0].reset();
        $('#clientModalLabel').text('Novo Cliente');
        $('#clientFormSubmitButton').text('Salvar');
        $('#clientForm').data('method', 'POST').data('url', '<?php echo e(route("admin.clients.store")); ?>');
    });

    $('#clientsTable tbody').on('click', '.edit-btn', function() {
        const data = $(this).data();
        $('#clientForm')[0].reset();
        $('#clientModalLabel').text('Editar Cliente');
        $('#clientFormSubmitButton').text('Atualizar');
        $('#client_id').val(data.id);
        $('#name').val(data.name);
        $('#notes').val(data.notes);
        $('#clientForm').data('method', 'PUT').data('url', `<?php echo e(url("admin/clients")); ?>/${data.id}`);
        $('#clientModal').modal('show');
    });

    // SUBMETER FORMULÁRIO (CRIAR E EDITAR)
    $('#clientForm').on('submit', function(e) {
        e.preventDefault();
        const form = $(this);
        Swal.fire({ title: 'Salvando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        $.ajax({
            url: form.data('url'), type: form.data('method'), data: form.serialize(),
            success: function(response) {
                $('#clientModal').modal('hide');
                Swal.fire('Sucesso!', response.success, 'success');
                clientsTable.ajax.reload(null, false);
            },
            error: handleAjaxError
        });
    });

    // DELETAR CLIENTE
    $('#clientsTable tbody').on('click', '.delete-btn', function() {
        const data = $(this).data();
        Swal.fire({
            title: 'Você tem certeza?', text: `Deseja excluir o cliente "${data.name}"?`, icon: 'warning',
            showCancelButton: true, confirmButtonText: 'Sim, excluir!', cancelButtonText: 'Cancelar', confirmButtonColor: '#d33'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `<?php echo e(url("admin/clients")); ?>/${data.id}`, type: 'DELETE',
                    success: function(response) {
                        Swal.fire('Excluído!', response.success, 'success');
                        clientsTable.ajax.reload(null, false);
                    },
                    error: handleAjaxError
                });
            }
        });
    });
});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('admin::layouts.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Samir\Desktop\lumina-app\Modules/Admin\resources/views/clients/index.blade.php ENDPATH**/ ?>