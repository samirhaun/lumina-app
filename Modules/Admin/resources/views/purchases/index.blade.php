@extends('admin::layouts.layout')

@section('title', 'Contas a Pagar')

@section('content')
<div id="purchasesContent">
  <style>
    :root {
      --terracota: #A0522D;
      --beige:     #EDE8E0;
      --danger:    #dc3545;
      --info:      #0d6efd;
      --white:     #ffffff;
    }

    /* 1) Remove ícone/pseudo‐elemento do DataTables Responsive */
    #purchasesContent table.dataTable td.dt-control,
    #purchasesContent table.dataTable th.dt-control {
      background-image: none !important;
    }
    #purchasesContent table.dataTable td.dt-control::before,
    #purchasesContent table.dataTable th.dt-control::before {
      content: none !important;
      display: none !important;
    }

    /* 2) Controle via ícone */
    #purchasesContent td.dt-control {
      cursor: pointer;
      text-align: center;
      vertical-align: middle;
    }
    #purchasesContent td.dt-control i.fas {
      font-size: 1.3rem;
      transition: transform .2s, color .2s;
    }
    #purchasesContent td.dt-control:hover i.fas {
      transform: scale(1.2);
      color: var(--info);
    }

    /* 3) Remove triângulo da 1ª coluna */
    #purchasesContent #purchasesTable tbody tr td:first-child,
    #purchasesContent #purchasesTable tbody tr td:first-child::before {
      background: none !important;
      content: none !important;
    }

    /* estilos de card-tabs terracota/bege */
    #purchasesContent .card-terracotta.card-tabs > .card-header {
      border-bottom: none;
      background-color: var(--terracota);
      color: var(--white);
    }
    #purchasesContent .card-terracotta.card-tabs > .card-header .nav-link {
      background: transparent;
      border: 0;
      color: rgba(240,230,230,.8);
    }
    #purchasesContent .card-terracotta.card-tabs > .card-header .nav-link.active {
      background: var(--beige);
      color: var(--terracota);
      border-color: #dee2e6 #dee2e6 var(--beige);
    }

    /* abas via nav-tabs */
    #purchasesContent .nav-tabs .nav-link {
      color: rgba(0,0,0,.7);
      border-color: transparent;
    }
    #purchasesContent .nav-tabs .nav-link.active {
      background-color: var(--terracota);
      color: var(--white);
      border-color: var(--terracota);
    }

    /* botão fechar customizado */
    #purchasesContent .modal-header .btn-close {
      width: 1.6rem;
      height: 1.6rem;
      background-color: var(--danger);
      border-radius: .25rem;
      position: relative;
    }
    #purchasesContent .modal-header .btn-close::before {
      content: "\f00d";
      font-family: "Font Awesome 6 Free";
      font-weight: 900;
      color: var(--white);
      font-size: 1rem;
      position: absolute;
      top: 50%; left: 50%;
      transform: translate(-50%,-50%);
    }
    #purchasesContent .modal-header .btn-close:focus {
      box-shadow: none;
    }

    /* select2 no modal */
    #purchasesContent .select2-container--bootstrap-5 .select2-selection--single {
      min-width: 250px !important;
    }
  </style>

  <div class="card card-terracotta card-tabs">
    <div class="card-header d-flex justify-content-between align-items-center">
      <a href="{{ route('admin.purchases.create') }}" class="btn btn-success">
        <i class="fas fa-plus"></i> Registrar Nova Compra
      </a>
    </div>

    {{-- ABAS PARA FILTRAGEM --}}
    <div class="card-header p-0 border-bottom-0">
      <ul class="nav nav-tabs" id="purchaseStatusTabs" role="tablist">
        <li class="nav-item">
          <a class="nav-link active" href="#" data-status="">Todos</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" data-status="Pendente">Pendentes de Recebimento</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="#" data-status="Recebido">Recebidos</a>
        </li>
      </ul>
    </div>

    <div class="card-body">
      <table id="purchasesTable" class="table table-bordered table-striped w-100">
        <thead>
          <tr>
            <th class="dt-control" style="width:20px;"></th>
            <th>ID</th>
            <th>Fornecedor</th>
            <th>Data da Compra</th>
            <th>Vencimento</th>
            <th>Valor Total</th>
            <th>Status Pagamento</th>
            <th>Status Recebimento</th>
            <th style="width:120px">Ações</th>
          </tr>
        </thead>
        <tbody></tbody>
      </table>
    </div>
  </div>

  {{-- Modais seguem iguais aos anteriores --}}
</div>
@endsection
@push('scripts')
    <script>
        $(function() {
            // =================================================================
            // 1. SETUP, VARIÁVEIS GLOBAIS E FUNÇÕES AUXILIARES
            // =================================================================
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            let currentPurchaseData = {}; // Guarda os dados da compra selecionada no modal

            // SOLUÇÃO DEFINITIVA: Separar a criação do array PHP da injeção de JSON.
            @php
                $jsDataForReceiveModal = [
                    'products' => $products,
                    'productTypes' => $productTypes,
                    'miscItems' => $miscItems,
                    'miscCategories' => $miscCategories,
                ];
            @endphp
            let unexpectedItemsData = @json($jsDataForReceiveModal);
            const dtLanguage = {
                "sEmptyTable": "Nenhum registro encontrado",
                "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                "sInfoFiltered": "(Filtrados de _MAX_ registros no total)",
                "sInfoThousands": ".",
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
            const formatCurrency = value => parseFloat(value).toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            });
            const formatDate = dateString => dateString ? new Date(dateString).toLocaleDateString('pt-BR', {
                timeZone: 'UTC'
            }) : 'N/A';
            const handleAjaxError = xhr => {
                Swal.close();
                if (xhr.status === 419) {
                    Swal.fire('Sessão Expirada!', 'Recarregue a página.', 'error').then(() => location
                        .reload());
                } else if (xhr.status === 422) {
                    let errors = xhr.responseJSON.errors;
                    let errorHtml = '<ul class="text-start">';
                    $.each(errors, (k, v) => {
                        errorHtml += `<li>${v[0]}</li>`;
                    });
                    errorHtml += '</ul>';
                    Swal.fire({
                        title: 'Erro de Validação',
                        html: errorHtml,
                        icon: 'error'
                    });
                } else if (xhr.status === 409) {
                    Swal.fire('Ação não permitida', xhr.responseJSON.error, 'warning');
                } else {
                    Swal.fire('Erro!', 'Ocorreu um erro inesperado.', 'error');
                }
            };

            // Inicializa todos os plugins
            $('#statusSelect').select2({
                theme: 'bootstrap-5',
                minimumResultsForSearch: Infinity,
                dropdownParent: $('#purchaseDetailsModal'),
                width: '100%'
            });
            $('.select2-receiver').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#receiveItemsModal')
            });
            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]');
            [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl));

            // =================================================================
            // 2. DATATABLE E LÓGICA DA PÁGINA PRINCIPAL
            // =================================================================
            const purchasesTable = $('#purchasesTable').DataTable({
                processing: true,
                responsive: false, // <-- desliga o controle automático

                ajax: "{{ route('admin.purchases.data') }}",
                order: [
                    [1, 'desc']
                ],
                columns: [{
                    className: 'dt-control',
                    orderable: false,
                    data: null,
                    defaultContent: '<i class="fas fa-plus-circle text-primary"></i>'
                }, {
                    data: 'id'
                }, {
                    data: 'supplier_name',
                    defaultContent: '<i>N/A</i>'
                }, {
                    data: 'purchase_date',
                    render: formatDate
                }, {
                    data: 'due_date',
                    render: formatDate
                }, {
                    data: 'total_amount',
                    render: formatCurrency
                }, {
                    data: 'status',
                    render: d => {
                        let c = 'bg-secondary';
                        if (d === 'Pendente') c = 'bg-warning text-dark';
                        if (d === 'Pago') c = 'bg-success';
                        if (d === 'Atrasado') c = 'bg-danger';
                        return `<span class="badge ${c}">${d}</span>`;
                    }
                }, {
                    data: 'received_at',
                    render: d => d ? `<span class="badge bg-success">Recebido</span>` :
                        `<span class="badge bg-warning text-dark">Pendente</span>`
                }, {
                    data: null,
                    searchable: false,
                    orderable: false,
                    render: (d, t, r) =>
                        `<button class="btn btn-sm btn-info details-btn" data-id="${r.id}" title="Ver Detalhes"><i class="fas fa-eye"></i></button> <button class="btn btn-sm btn-danger delete-btn" data-id="${r.id}" title="Excluir"><i class="fas fa-trash"></i></button>`
                }],
                language: dtLanguage
            });

            // --- NOVA LÓGICA DE FILTRAGEM POR ABAS (CORRIGIDA) ---
            $('#purchaseStatusTabs .nav-link').on('click', function(e) {
                e.preventDefault();
                $('#purchaseStatusTabs .nav-link').removeClass('active');
                $(this).addClass('active');

                const status = $(this).data('status');

                // Busca a instância da tabela diretamente e aplica o filtro
                // na coluna de índice 6 (Status Pag.)
                $('#purchasesTable').DataTable().column(7).search(status).draw();
            });

            function formatChildRow(items) {
                let itemsHtml = `
            <table class="table table-hover table-sm" style="margin: 0; background-color: #f1f6ff;">
                <thead class="table-light">
                    <tr><th>Item</th><th>Qtd. Pedida</th><th>Qtd. Recebida</th><th>Custo Unit.</th><th>Custo Total</th><th>Obs.</th></tr>
                </thead>
                <tbody>`;

                if (items.length === 0) {
                    itemsHtml +=
                        '<tr><td colspan="6" class="text-center">Nenhum item encontrado para esta compra.</td></tr>';
                } else {
                    $.each(items, function(i, item) {
                        itemsHtml += `
                    <tr>
                        <td>${item.name}</td>
                        <td>${item.quantity}</td>
                        <td><span class="badge bg-info">${item.quantity_received ?? 0}</span></td>
                        <td>${formatCurrency(item.unit_cost)}</td>
                        <td>${formatCurrency(item.total_cost)}</td>
                        <td>${item.notes || ''}</td>
                    </tr>`;
                    });
                }

                itemsHtml += '</tbody></table>';
                return itemsHtml;
            }

            // --- EVENTO PARA EXPANDIR/RECOLHER A LINHA ---
            $('#purchasesTable tbody').on('click', 'td.dt-control', function(e) {
                e.stopPropagation();
                var tr = $(this).closest('tr');
                var row = purchasesTable.row(tr);
                var icon = $(this).find('i.fas');

                if (row.child.isShown()) {
                    // Fecha
                    row.child.hide();
                    tr.removeClass('shown');
                    icon
                        .removeClass('fa-minus-circle text-danger')
                        .addClass('fa-plus-circle text-primary');
                } else {
                    // Abre e mostra "carregando"
                    tr.addClass('shown');
                    icon
                        .removeClass('fa-plus-circle text-primary')
                        .addClass('fa-minus-circle text-danger');

                    row.child('<div class="p-2">Carregando itens...</div>').show();

                    $.get(`{{ url('admin/purchases') }}/${row.data().id}/items`)
                        .done(response => {
                            row.child(formatChildRow(response.data)).show();
                        })
                        .fail(() => {
                            row.child('<div class="p-2 text-danger">Erro ao carregar os itens.</div>')
                                .show();
                        });
                }
            });


            // Função para atualizar o estado do botão de recebimento
            function updateReceiveButtonState(paymentStatus, receivedAt) {
                const receiveBtn = $('#openReceiveModalBtn');
                const tooltip = bootstrap.Tooltip.getInstance(receiveBtn[0]);
                if (tooltip) tooltip.dispose();
                const canReceive = (paymentStatus === 'Pago' && !receivedAt);
                receiveBtn.prop('disabled', !canReceive);
                if (receivedAt) {
                    receiveBtn.html('<i class="fas fa-check-circle me-1"></i> Já Recebido').removeClass(
                        'btn-success btn-secondary').addClass('btn-light');
                    receiveBtn.attr('data-bs-original-title', 'Esta compra já foi marcada como recebida.');
                } else if (paymentStatus !== 'Pago') {
                    receiveBtn.html('<i class="fas fa-hourglass-half me-1"></i> Aguardando Pagamento').removeClass(
                        'btn-success btn-light').addClass('btn-secondary');
                    receiveBtn.attr('data-bs-original-title',
                        "É preciso marcar a compra como 'Paga' para registrar o recebimento.");
                } else {
                    receiveBtn.html('<i class="fas fa-box-open me-1"></i> Registrar Recebimento').removeClass(
                        'btn-secondary btn-light').addClass('btn-success');
                    receiveBtn.attr('data-bs-original-title', 'Clique para registrar os itens recebidos.');
                }
                new bootstrap.Tooltip(receiveBtn[0]);
            }

            // Abrir o modal de detalhes
            $('#purchasesTable tbody').on('click', '.details-btn', function() {
                const purchaseId = $(this).data('id');
                Swal.fire({
                    title: 'Buscando detalhes...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                $.get(`{{ url('admin/purchases') }}/${purchaseId}/details`).done(data => {
                    currentPurchaseData = data;
                    const purchase = data.purchase;
                    $('#purchaseModalTitle').text(`Detalhes da Compra #${purchase.id}`);
                    $('#purchaseDetailsGeneral').html(
                        `<div class="row"><div class="col-md-6"><strong>Fornecedor:</strong> ${purchase.supplier_name || 'N/A'}</div><div class="col-md-3"><strong>Data da Compra:</strong> ${formatDate(purchase.purchase_date)}</div><div class="col-md-3"><strong>Vencimento:</strong> ${formatDate(purchase.due_date)}</div></div><div class="mt-2"><strong>Observações:</strong> ${purchase.notes || 'Nenhuma'}</div>`
                    );
                    let itemsHtml = '';
                    $.each(data.items, (i, item) => {
                        itemsHtml +=
                            `<tr><td>${item.name}</td><td>${item.quantity}</td><td><span class="badge bg-info">${item.quantity_received ?? 0}</span></td><td>${formatCurrency(item.unit_cost)}</td><td>${formatCurrency(item.total_cost)}</td><td>${item.notes || ''}</td></tr>`;
                    });
                    $('#purchaseDetailsItemsTbody').html(itemsHtml);
                    $('#updatePaymentStatusForm').attr('action',
                        `{{ url('admin/purchases') }}/${purchase.id}/update-payment-status`);
                    $('#statusSelect').val(purchase.status).trigger('change');
                    updateReceiveButtonState(purchase.status, purchase.received_at);
                    Swal.close();
                    $('#purchaseDetailsModal').modal('show');
                }).fail(() => Swal.fire('Erro!', 'Não foi possível buscar os detalhes.', 'error'));
            });

            // Submeter atualização de status de pagamento
            $('#updatePaymentStatusForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                $.post(form.attr('action'), form.serialize()).done(response => {
                    Swal.fire({
                        icon: 'success',
                        title: 'Status Atualizado!',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    purchasesTable.ajax.reload(null, false);
                    const newStatus = form.find('select[name="status"]').val();
                    currentPurchaseData.purchase.status = newStatus;
                    updateReceiveButtonState(newStatus, currentPurchaseData.purchase.received_at);
                }).fail(() => Swal.fire('Erro!', 'Não foi possível atualizar o status.', 'error'));
            });

            // Deletar a compra
            $('#purchasesTable tbody').on('click', '.delete-btn', function() {
                const id = $(this).data('id');
                Swal.fire({
                        title: 'Você tem certeza?',
                        text: `Deseja excluir permanentemente a compra #${id}?`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sim, excluir!',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#d33'
                    })
                    .then(result => {
                        if (result.isConfirmed) {
                            $.ajax({
                                url: `{{ url('admin/purchases') }}/${id}`,
                                type: 'DELETE'
                            }).done(response => {
                                Swal.fire('Excluído!', response.success, 'success');
                                purchasesTable.ajax.reload(null, false);
                            }).fail(handleAjaxError);
                        }
                    });
            });

            // =================================================================
            // 3. LÓGICA DO MODAL DE RECEBIMENTO
            // =================================================================
            let openReceiveModalOnClose = false;

            // Abrir o modal de recebimento
            $('#openReceiveModalBtn').on('click', function() {
                if (!currentPurchaseData.purchase) return;
                openReceiveModalOnClose = true;
                $('#receiveModalTitle').text(
                    `Registrar Recebimento da Compra #${currentPurchaseData.purchase.id}`);
                let itemsHtml = '';
                $.each(currentPurchaseData.items, function(index, item) {
                    itemsHtml += `
                <tr data-item-db-id="${item.id}" data-purchasable-id="${item.purchasable_id}" data-purchasable-type="${item.purchasable_type}" data-unit-cost="${item.unit_cost}">
                    <td>${item.name}</td><td><span class="badge bg-secondary fs-6">${item.quantity}</span></td>
                    <td><input type="number" class="form-control form-control-sm" name="quantity_received" value="${item.quantity}" min="0"></td>
                    <td><input type="text" class="form-control form-control-sm" name="notes" placeholder="Opcional" value="${item.notes || ''}"></td>
                    <td><button type="button" class="btn btn-sm btn-outline-danger remove-receive-item-btn" title="Remover item"><i class="fas fa-trash-alt"></i></button></td>
                </tr>`;
                });
                $('#receiveItemsTbody').html(itemsHtml);
                $('#receiveItemsForm').data('purchase-id', currentPurchaseData.purchase.id);
                $('#purchaseDetailsModal').modal('hide');
            });

            // Evento que abre o segundo modal APÓS o primeiro fechar
            $('#purchaseDetailsModal').on('hidden.bs.modal', function() {
                if (openReceiveModalOnClose) {
                    $('#receiveItemsModal').modal('show');
                    openReceiveModalOnClose = false;
                }
            });

            // Lógica dos filtros para adicionar item inesperado
            $('#unexpectedItemTypeSelector').on('change', function() {
                const selected = $(this).val();
                $('#unexpectedProductTypeContainer, #unexpectedMiscCategoryContainer').hide();
                $('#unexpectedItemSelector').empty().prop('disabled', true).trigger('change');
                if (selected === 'Product') {
                    $('#unexpectedProductTypeSelector').empty().append(
                        '<option value="">Selecione o Tipo</option>');
                    $.each(unexpectedItemsData.productTypes, (i, type) => $(
                        '#unexpectedProductTypeSelector').append(
                        `<option value="${type.id}">${type.name}</option>`));
                    $('#unexpectedProductTypeContainer').show();
                } else if (selected === 'MiscItem') {
                    $('#unexpectedMiscCategorySelector').empty().append(
                        '<option value="">Selecione a Categoria</option>');
                    $.each(unexpectedItemsData.miscCategories, (i, cat) => $(
                        '#unexpectedMiscCategorySelector').append(
                        `<option value="${cat.id}">${cat.name}</option>`));
                    $('#unexpectedMiscCategoryContainer').show();
                }
            });
            $('#unexpectedProductTypeSelector').on('change', function() {
                const typeId = $(this).val();
                const items = unexpectedItemsData.products.filter(p => p.product_type_id == typeId);
                populateUnexpectedItemSelector(items, 'Product');
            });
            $('#unexpectedMiscCategorySelector').on('change', function() {
                const catId = $(this).val();
                const items = unexpectedItemsData.miscItems.filter(i => i.misc_category_id == catId);
                populateUnexpectedItemSelector(items, 'MiscItem');
            });

            function populateUnexpectedItemSelector(items, type) {
                const selector = $('#unexpectedItemSelector');
                selector.empty().append('<option value="">Selecione o Item</option>');
                $.each(items, (i, item) => {
                    let name = item.item_name ? `${item.item_name} (${item.category_name})` : item.name;
                    selector.append(`<option value="${item.id}" data-type="${type}">${name}</option>`);
                });
                selector.prop('disabled', false).trigger('change');
            }

            // Adicionar item inesperado na lista
            $('#addUnexpectedItemBtn').on('click', function() {
                const selectedOption = $('#unexpectedItemSelector').find('option:selected');
                const qty = parseInt($('#unexpectedItemQty').val());
                const cost = parseFloat($('#unexpectedItemCost').val().replace(',', '.'));
                if (!selectedOption.val() || isNaN(qty) || qty <= 0 || isNaN(cost) || cost < 0) {
                    Swal.fire('Atenção!',
                        'Selecione um item e preencha a quantidade e o custo corretamente.', 'warning');
                    return;
                }
                const newRow = `
            <tr data-item-db-id="0" data-purchasable-id="${selectedOption.val()}" data-purchasable-type="${selectedOption.data('type')}" data-unit-cost="${cost}">
                <td><span class="badge bg-primary me-1">NOVO</span> ${selectedOption.text()}</td>
                <td><span class="badge bg-secondary fs-6">0</span></td>
                <td><input type="number" class="form-control form-control-sm" name="quantity_received" value="${qty}" min="0"></td>
                <td><input type="text" class="form-control form-control-sm" name="notes" placeholder="Opcional"></td>
                <td><button type="button" class="btn btn-sm btn-outline-danger remove-receive-item-btn" title="Remover item"><i class="fas fa-trash-alt"></i></button></td>
            </tr>`;
                $('#receiveItemsTbody').append(newRow);
                // Resetar campos de adição
                $('#unexpectedItemTypeSelector').val('').trigger('change');
                $('#unexpectedItemQty').val('');
                $('#unexpectedItemCost').val('');
            });

            // Remover um item da lista de recebimento
            $('#receiveItemsTbody').on('click', '.remove-receive-item-btn', function() {
                $(this).closest('tr').remove();
            });

            // Submeter o formulário de recebimento
            $('#receiveItemsForm').on('submit', function(e) {
                e.preventDefault();
                let itemsData = [];
                $('#receiveItemsTbody tr').each(function() {
                    const row = $(this);
                    itemsData.push({
                        item_db_id: row.data('item-db-id'),
                        purchasable_id: row.data('purchasable-id'),
                        purchasable_type: row.data('purchasable-type'),
                        quantity_received: row.find('input[name="quantity_received"]')
                            .val(),
                        notes: row.find('input[name="notes"]').val(),
                        unit_cost: row.data('unit-cost')
                    });
                });

                if (itemsData.length === 0) {
                    Swal.fire('Atenção!', 'A lista de recebimento não pode estar vazia.', 'warning');
                    return;
                }

                Swal.fire({
                    title: 'Processando Recebimento...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                $.post(`{{ url('admin/purchases') }}/${$('#receiveItemsForm').data('purchase-id')}/receive`, {
                        items: itemsData
                    })
                    .done(response => {
                        $('#receiveItemsModal').modal('hide');
                        Swal.fire('Sucesso!', response.success, 'success');
                        purchasesTable.ajax.reload(null, false);
                    })
                    .fail(handleAjaxError);
            });

        });
    </script>
@endpush
