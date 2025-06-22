

<?php $__env->startSection('title', 'Fornecedores'); ?>

<?php $__env->startSection('content'); ?>
    <style>
        :root {
            --terracota: #A0522D;
            --white:     #ffffff;
        }

        /* Card principal */
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

        /* Botão de “Novo Fornecedor” */
        .card-tools .btn-success {
            background-color: var(--terracota);
            border-color: var(--terracota);
        }

        /* Tabela com hover customizado */
        #suppliersTable.table-striped tbody tr:hover {
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
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#supplierModal">
                    <i class="fas fa-plus"></i> Novo Fornecedor
                </button>
            </div>
        </div>
        <div class="card-body">
            <table id="suppliersTable" class="table table-bordered table-striped w-100">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Contato</th>
                        <th>Telefone</th>
                        <th>E-mail</th>
                        <th style="width: 80px">Ações</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>
<div class="modal fade" id="supplierModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="supplierModalLabel">Novo Fornecedor</h5>
            </div>
            <form id="supplierForm"> 
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    <input type="hidden" id="supplier_id" name="id">
                    <div class="row">
                        <div class="col-md-12 mb-3">
                            <label for="name" class="form-label">Nome do Fornecedor</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="contact_person" class="form-label">Pessoa de Contato</label>
                            <input type="text" class="form-control" id="contact_person" name="contact_person">
                        </div>
                         <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Telefone</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-mail</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary" id="supplierFormSubmitButton">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(function () {
    // --- SETUP GLOBAL ---
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>' } });

    var dataTableLanguage = {
        "sEmptyTable": "Nenhum registro encontrado", "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
        "sInfoEmpty": "Mostrando 0 até 0 de 0 registros", "sInfoFiltered": "(Filtrados de _MAX_ registros no total)",
        "sInfoPostFix": "", "sInfoThousands": ".", "sLengthMenu": "_MENU_ resultados por página",
        "sLoadingRecords": "Carregando...", "sProcessing": "Processando...",
        "sZeroRecords": "Nenhum registro encontrado", "sSearch": "Pesquisar",
        "oPaginate": { "sNext": "Próximo", "sPrevious": "Anterior", "sFirst": "Primeiro", "sLast": "Último" },
        "oAria": { "sSortAscending": ": Ordenar colunas de forma ascendente", "sSortDescending": ": Ordenar colunas de forma descendente" }
    };

    // --- DATATABLE PARA FORNECEDORES ---
    var suppliersTable = $('#suppliersTable').DataTable({
        processing: true,
        ajax: "<?php echo e(route('admin.suppliers.data')); ?>",
        columns: [
            { data: 'id' },
            { data: 'name' },
            { data: 'contact_person' },
            { data: 'phone' },
            { data: 'email' },
            { data: null, searchable: false, orderable: false, render: function (data, type, row) {
                return `
                    <button class="btn btn-sm btn-primary edit-btn" 
                        data-id="${row.id}" 
                        data-name="${row.name}"
                        data-contact_person="${row.contact_person || ''}"
                        data-phone="${row.phone || ''}"
                        data-email="${row.email || ''}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-btn" data-id="${row.id}" data-name="${row.name}"><i class="fas fa-trash"></i></button>
                `;
            }}
        ],
        language: dataTableLanguage
    });

    // --- LÓGICA GERAL DE AJAX E SWAL ---
    function handleAjaxError(xhr) {
        Swal.close();
        if (xhr.status === 419) {
            Swal.fire('Sessão Expirada!', 'Sua sessão expirou. Recarregue a página.', 'error').then(() => location.reload());
        } else if (xhr.status === 422) {
            var errors = xhr.responseJSON.errors;
            var errorHtml = '<ul class="text-start">';
            $.each(errors, (key, value) => { errorHtml += '<li>' + value[0] + '</li>'; });
            errorHtml += '</ul>';
            Swal.fire({title: 'Erro de Validação', html: errorHtml, icon: 'error'});
        } else if (xhr.status === 409) {
            Swal.fire('Ação não permitida', xhr.responseJSON.error, 'warning');
        } else {
            Swal.fire('Erro Inesperado!', 'Ocorreu um erro no servidor.', 'error');
        }
    }

    // --- EVENTOS PARA O MODAL DE FORNECEDOR ---
    $('[data-bs-target="#supplierModal"]').on('click', function() {
        $('#supplierForm')[0].reset();
        $('#supplierModalLabel').text('Novo Fornecedor');
        $('#supplierFormSubmitButton').text('Salvar');
        $('#supplierForm').data('method', 'POST').data('url', '<?php echo e(route("admin.suppliers.store")); ?>');
    });

    $('#suppliersTable tbody').on('click', '.edit-btn', function() {
        var data = $(this).data();
        $('#supplierForm')[0].reset();
        $('#supplierModalLabel').text('Editar Fornecedor');
        $('#supplierFormSubmitButton').text('Atualizar');

        $('#supplier_id').val(data.id);
        $('#name').val(data.name);
        $('#contact_person').val(data.contact_person);
        $('#phone').val(data.phone);
        $('#email').val(data.email);

        $('#supplierForm').data('method', 'PUT').data('url', `<?php echo e(url("admin/suppliers")); ?>/${data.id}`);
        $('#supplierModal').modal('show');
    });

    // SUBMETER FORMULÁRIO (CRIAR E EDITAR)
    $('#supplierForm').on('submit', function(e) {
        e.preventDefault();
        var form = $(this);
        Swal.fire({ title: 'Salvando...', allowOutsideClick: false, didOpen: () => Swal.showLoading() });
        $.ajax({
            url: form.data('url'),
            type: form.data('method'),
            data: form.serialize(),
            success: function(response) {
                $('#supplierModal').modal('hide');
                Swal.fire('Sucesso!', response.success, 'success');
                suppliersTable.ajax.reload(null, false);
            },
            error: handleAjaxError
        });
    });

    // DELETAR FORNECEDOR
    $('#suppliersTable tbody').on('click', '.delete-btn', function() {
        var data = $(this).data();
        Swal.fire({
            title: 'Você tem certeza?',
            text: `Deseja excluir o fornecedor "${data.name}"?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: 'Sim, excluir!',
            cancelButtonText: 'Cancelar',
            confirmButtonColor: '#d33',
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: `<?php echo e(url("admin/suppliers")); ?>/${data.id}`,
                    type: 'DELETE',
                    success: function(response) {
                        Swal.fire('Excluído!', response.success, 'success');
                        suppliersTable.ajax.reload(null, false);
                    },
                    error: handleAjaxError
                });
            }
        });
    });

});
</script>
<?php $__env->stopPush(); ?>
<?php echo $__env->make('admin::layouts.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Samir\Desktop\lumina-app\Modules/Admin\resources/views/suppliers/index.blade.php ENDPATH**/ ?>