<?php $__env->startSection('title', 'Clientes'); ?>

<?php $__env->startSection('content'); ?>
    <style>
        :root {
            --terracota: #A0522D;
            --white: #ffffff;
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
            background-color: rgba(160, 82, 45, .1);
        }

        /* Espaçamento entre os botões de ação */
        .edit-btn+.delete-btn {
            margin-left: .5rem;
        }

        /* Adicione um espaçamento para o novo botão */
        .actions-cell .btn {
            margin-right: .5rem;
        }

        .actions-cell .btn:last-child {
            margin-right: 0;
        }

        /* Colunas de totais centralizadas */
        .total-col {
            text-align: center;
            vertical-align: middle;
            font-weight: 500;
        }

        /* Estilo para o modal de histórico de vendas */
        #salesHistoryModal .modal-header {
            background-color: var(--terracota);
            color: var(--white);
        }

        #salesHistoryModal .summary-box {
            background-color: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: .5rem;
            padding: 1rem;
        }

        #salesHistoryModal .summary-item {
            text-align: center;
        }

        #salesHistoryModal .summary-item strong {
            display: block;
            font-size: 1.5rem;
            color: var(--terracota);
        }

        #salesHistoryModal .summary-item span {
            font-size: .9rem;
            color: #6c757d;
        }

        .sales-accordion .accordion-item {
            border: 1px solid #dee2e6;
            border-radius: .375rem !important;
            margin-bottom: .5rem;
        }

        .sales-accordion .accordion-button {
            font-weight: 500;
            background-color: #f8f9fa;
        }

        .sales-accordion .accordion-button:not(.collapsed) {
            color: var(--terracota);
            background-color: #fdf5f0;
            box-shadow: inset 0 -1px 0 rgba(0, 0, 0, .125);
        }

        .sales-accordion .accordion-button:focus {
            box-shadow: 0 0 0 0.25rem rgba(160, 82, 45, .25);
        }

        .sales-accordion .sale-summary-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            width: 100%;
            align-items: center;
        }

        .sales-accordion .sale-summary-grid .text-end {
            justify-self: end;
        }

        /* 1. Remove a borda pesada de cada item e padroniza */
        .sales-accordion .accordion-item {
            border: none;
            border-bottom: 1px solid #dee2e6;
            border-radius: 0;
            margin-bottom: 0;
        }

        /* 2. Ajusta o botão para ter fundo branco e padding consistente */
        .sales-accordion .accordion-button {
            background-color: #fff;
            border: none;
            padding: .75rem 1.25rem;
            font-size: 1rem;
            color: #333;
            box-shadow: none;
        }

        /* 3. Cor quando aberto */
        .sales-accordion .accordion-button:not(.collapsed) {
            background-color: var(--beige);
            color: var(--terracota);
            font-weight: 600;
        }

        /* 4. Hover / focus mais sutil */
        .sales-accordion .accordion-button:hover,
        .sales-accordion .accordion-button:focus {
            background-color: rgba(160, 82, 45, 0.05);
        }

        /* 5. Estilo específico para o container interno flex */
        .sale-header span {
            /* espaçamento mínimo entre ID/data e total */
            margin: 0 .5rem;
        }

        /* 1) reativa o ::after */
        .sales-accordion .accordion-button::after {
            flex-shrink: 0;
            width: 1.25rem;
            height: 1.25rem;
            margin-left: auto;
            content: "";
            background-image: var(--bs-accordion-btn-icon);
            /* usa o ícone padrão */
            background-repeat: no-repeat;
            transition: transform .2s ease;
        }

        /* 2) rotaciona ao expandir */
        .sales-accordion .accordion-button:not(.collapsed)::after {
            transform: rotate(-180deg);
        }

        .rotate-icon {
            transition: transform .2s ease;
            margin-right: 2.5rem;
            /* ajusta à vontade */

        }

        .sales-accordion .accordion-button:not(.collapsed) .rotate-icon {
            transform: rotate(180deg);
        }

        .sales-accordion .sale-header>span:last-child {
            margin-right: 1rem;
            /* ou 0.5rem, 1.5rem… */
        }

        /* Modal content */
        #clientModal .modal-content {
            border-radius: .5rem;
            overflow: hidden;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .2);
        }

        /* Cabeçalho terracota */
        #clientModal .modal-header {
            background-color: var(--terracota);
            color: var(--white);
            border-bottom: none;
        }

        /* Título */
        #clientModal .modal-title {
            color: var(--white);
            font-weight: 500;
        }

        /* Botão de fechar */
        #clientModal .btn-close {
            filter: brightness(0) invert(1);
        }

        /* Corpo com fundo suave */
        #clientModal .modal-body {
            background-color: var(--beige);
            padding: 1.5rem;
        }

        /* Labels em terracota */
        #clientModal .form-label {
            color: var(--terracota);
            font-weight: 500;
        }

        /* Inputs com borda terracota ao focar */
        #clientModal .form-control:focus {
            border-color: var(--terracota);
            box-shadow: 0 0 0 .2rem rgba(160, 82, 45, .25);
        }

        /* Rodapé */
        #clientModal .modal-footer {
            background-color: #f1f1f1;
            border-top: none;
            padding: 1rem 1.5rem;
        }

        /* Botões */
        #clientModal .btn-secondary {
            border-color: var(--terracota);
            color: var(--terracota);
            background-color: transparent;
            transition: background .2s, color .2s;
        }

        #clientModal .btn-secondary:hover {
            background-color: var(--terracota);
            color: var(--white);
        }

        #clientModal .btn-primary {
            background-color: var(--terracota);
            border-color: var(--terracota);
            transition: filter .2s ease;
        }

        #clientModal .btn-primary:hover {
            filter: brightness(90%);
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
                        
                        <th class="text-center">Vendas</th>
                        <th class="text-center">Total Gasto</th>
                        <th style="width: 120px">Ações</th>
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

    <div class="modal fade" id="salesHistoryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="salesHistoryModalLabel">Histórico de Vendas</h5>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
</div><?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        $(function() {
            // --- SETUP GLOBAL E FUNÇÕES ---
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                }
            });
            const dtLanguage = {
                "sEmptyTable": "Nenhum cliente encontrado",
                "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                "sInfoFiltered": "(Filtrados de _MAX_ registros no total)",
                "sLengthMenu": "_MENU_ resultados por página",
                "sLoadingRecords": "Carregando...",
                "sProcessing": "Processando...",
                "sZeroRecords": "Nenhum registro encontrado",
                "sSearch": "Pesquisar",
                "oPaginate": {
                    "sNext": "Próximo",
                    "sPrevious": "Anterior",
                    "sFirst": "Primeiro",
                    "sLast": "Último"
                }
            };
            const handleAjaxError = xhr => {
                Swal.close();
                if (xhr.status === 419) {
                    Swal.fire('Sessão Expirada!', 'Recarregue a página.', 'error').then(() => location
                        .reload());
                } else if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorHtml = '<ul class="text-start">';
                    $.each(errors, (k, v) => {
                        errorHtml += `<li>${v[0]}</li>`;
                    });
                    errorHtml += '</ul>';
                    Swal.fire({
                        title: 'Erro de Validação',
                        html: errorHtml,
                        icon: 'error'
                    });
                } else {
                    Swal.fire('Erro!', 'Ocorreu um erro inesperado.', 'error');
                }
            };

            // --- DATATABLE ---
            const clientsTable = $('#clientsTable').DataTable({
                processing: true,
                ajax: "<?php echo e(route('admin.clients.data')); ?>",
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'notes',
                        render: data => data && data.length > 50 ? data.substring(0, 50) + '...' : (
                            data || '')
                    },
                    {
                        data: 'sales_count',
                        className: 'total-col'
                    },
                    {
                        data: 'total_spent',
                        className: 'total-col',
                        render: data => 'R$ ' + parseFloat(data).toLocaleString('pt-BR', {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                        })
                    },
                    {
                        data: null,
                        searchable: false,
                        orderable: false,
                        className: 'actions-cell',
                        render: (data, type, row) => `
                    
                    <button class="btn btn-sm btn-info view-sales-btn" title="Ver Vendas de ${row.name}" data-id="${row.id}" data-name="${row.name}">
                        <i class="fas fa-shopping-cart"></i>
                    </button>
                    <button class="btn btn-sm btn-primary edit-btn" title="Editar Cliente" data-id="${row.id}" data-name="${row.name}" data-notes="${row.notes || ''}">
                        <i class="fas fa-edit"></i>
                    </button>
                    <button class="btn btn-sm btn-danger delete-btn" title="Excluir Cliente" data-id="${row.id}" data-name="${row.name}">
                        <i class="fas fa-trash"></i>
                    </button>
                `
                    }
                ],
                language: dtLanguage
            });

            // --- LÓGICA DO MODAL ---
            $('[data-bs-target="#clientModal"]').on('click', function() {
                $('#clientForm')[0].reset();
                $('#clientModalLabel').text('Novo Cliente');
                $('#clientFormSubmitButton').text('Salvar');
                $('#clientForm').data('method', 'POST').data('url', '<?php echo e(route('admin.clients.store')); ?>');
            });

            $('#clientsTable tbody').on('click', '.edit-btn', function() {
                const data = $(this).data();
                $('#clientForm')[0].reset();
                $('#clientModalLabel').text('Editar Cliente');
                $('#clientFormSubmitButton').text('Atualizar');
                $('#client_id').val(data.id);
                $('#name').val(data.name);
                $('#notes').val(data.notes);
                $('#clientForm').data('method', 'PUT').data('url', `<?php echo e(url('admin/clients')); ?>/${data.id}`);
                $('#clientModal').modal('show');
            });

            // SUBMETER FORMULÁRIO (CRIAR E EDITAR)
            $('#clientForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                Swal.fire({
                    title: 'Salvando...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                $.ajax({
                    url: form.data('url'),
                    type: form.data('method'),
                    data: form.serialize(),
                    success: function(response) {
                        $('#clientModal').modal('hide');
                        Swal.fire('Sucesso!', response.success, 'success');
                        clientsTable.ajax.reload(null, false);
                    },
                    error: handleAjaxError
                });
            });

            // --- NOVA LÓGICA PARA ABRIR E PREENCHER O MODAL DE VENDAS ---
            $('#clientsTable tbody').on('click', '.view-sales-btn', function() {
                const clientId = $(this).data('id');
                const clientName = $(this).data('name');
                const modal = $('#salesHistoryModal');
                const modalBody = modal.find('.modal-body');

                // 1. Reset e exibe loading
                modal.find('#salesHistoryModalLabel').text(`Histórico de Vendas de: ${clientName}`);
                modalBody.html(
                    '<div class="text-center py-5">' +
                    '<div class="spinner-border text-primary"></div>' +
                    '<p class="mt-2">Buscando histórico...</p>' +
                    '</div>'
                );
                modal.modal('show');

                // 2. Requisição AJAX
                $.get(`<?php echo e(url('admin/clients')); ?>/${clientId}/sales`)
                    .done(function(response) {
                        const sales = response.sales;
                        let content = '';

                        // 3.a Nenhuma venda
                        if (sales.length === 0) {
                            content = '<div class="alert alert-warning text-center">' +
                                'Nenhuma venda encontrada para este cliente.' +
                                '</div>';
                        } else {
                            // 3.b Calcular totais
                            const totalSpent = sales.reduce((sum, sale) => sum + parseFloat(sale
                                .grand_total), 0);
                            const avgTicket = totalSpent / sales.length;

                            // 4. Sumário
                            content += `
                    <div class="summary-box mb-4">
                        <div class="row">
                            <div class="col-md-4 summary-item">
                                <strong>${sales.length}</strong><span>Vendas Realizadas</span>
                            </div>
                            <div class="col-md-4 summary-item">
                                <strong>${totalSpent.toLocaleString('pt-BR',{style:'currency',currency:'BRL'})}</strong>
                                <span>Valor Total Gasto</span>
                            </div>
                            <div class="col-md-4 summary-item">
                                <strong>${avgTicket.toLocaleString('pt-BR',{style:'currency',currency:'BRL'})}</strong>
                                <span>Ticket Médio</span>
                            </div>
                        </div>
                    </div>`;

                            // 5. Accordion de vendas
                            content += `<div class="sales-accordion accordion" id="salesAccordion">`;
                            sales.forEach(sale => {
                                const saleDate = new Date(sale.order_date)
                                    .toLocaleDateString('pt-BR', {
                                        timeZone: 'UTC'
                                    });

                                const formattedTotal = parseFloat(sale.grand_total)
                                    .toLocaleString('pt-BR', {
                                        style: 'currency',
                                        currency: 'BRL'
                                    });
                                content += `
                        <div class="accordion-item">
                            <h2 class="accordion-header" id="headingSale${sale.id}">
                            <button class="accordion-button collapsed" type="button"
                                    data-bs-toggle="collapse"
                                    data-bs-target="#collapseSale${sale.id}"
                                    aria-expanded="false"
                                    aria-controls="collapseSale${sale.id}">
                            <div class="d-flex w-100 justify-content-between align-items-center sale-header">
                            <span>#${sale.id} — ${saleDate}</span>
                            <span class="fw-semibold d-flex align-items-center me-3">
                                ${formattedTotal}
                                <i class="fas fa-chevron-down ms-2 rotate-icon"></i>
                            </span>
                            </div>                            </button>
                            </h2>
                            <div id="collapseSale${sale.id}"
                                 class="accordion-collapse collapse"
                                 aria-labelledby="headingSale${sale.id}"
                                 data-bs-parent="#salesAccordion">
                                <div class="accordion-body py-0">
                                    <table class="table table-sm mb-0">
                                        <thead>
                                            <tr>
                                                <th>Produto</th>
                                                <th>Quantidade</th>
                                                <th class="text-end">Preço Unitário</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            ${sale.items.map(item => `
                                                                                        <tr>
                                                                                            <td>${item.product_name}</td>
                                                                                            <td>${item.quantity}</td>
                                                                                            <td class="text-end">
                                                                                                ${parseFloat(item.price_per_unit)
                                                                                                  .toLocaleString('pt-BR',{style:'currency',currency:'BRL'})}
                                                                                            </td>
                                                                                        </tr>
                                                                                    `).join('')}
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>`;
                            });
                            content += `</div>`;
                        }

                        // 6. Insere no modal
                        modalBody.html(content);
                    })
                    .fail(function() {
                        modalBody.html(
                            '<div class="alert alert-danger text-center">' +
                            'Ocorreu um erro ao buscar o histórico de vendas.' +
                            '</div>'
                        );
                    });
            });


            // DELETAR CLIENTE
            $('#clientsTable tbody').on('click', '.delete-btn', function() {
                const data = $(this).data();
                Swal.fire({
                    title: 'Você tem certeza?',
                    text: `Deseja excluir o cliente "${data.name}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, excluir!',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `<?php echo e(url('admin/clients')); ?>/${data.id}`,
                            type: 'DELETE',
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

<?php echo $__env->make('admin::layouts.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Bruno\Documents\lumina-app\lumina-app\Modules/Admin\resources/views/clients/index.blade.php ENDPATH**/ ?>