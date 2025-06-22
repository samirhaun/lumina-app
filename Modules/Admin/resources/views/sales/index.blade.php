@extends('admin::layouts.layout')

@section('title', 'Histórico de Vendas')

@section('content')
    <style>
        :root {
            --terracota: #A0522D;
            --beige:    #EDE8E0;
            --white:    #ffffff;
        }

        /* Botões primários */
        .btn-primary {
            background-color: var(--terracota) !important;
            border-color: var(--terracota) !important;
            color: var(--white) !important;
        }

        /* Cards primários */
        .card-primary .card-header {
            background-color: var(--terracota) !important;
            color: var(--white) !important;
            border-bottom: none;
        }

        /* Modal de detalhes */
        #saleDetailsModal .modal-content {
            background-color: var(--beige);
        }
        #saleDetailsModal .modal-header.bg-info {
            background-color: var(--terracota) !important;
        }
        #saleDetailsModal .card-header.bg-primary {
            background-color: var(--terracota) !important;
        }
        #saleDetailsModal .card-header.bg-secondary {
            background-color: #6c757d !important;
        }
        #saleDetailsModal .modal-header .text-white,
        #saleDetailsModal .card-header.text-white {
            color: var(--white) !important;
        }

        /* Ícones e selects */
        .icon-label {
            display: flex;
            align-items: center;
            gap: .75rem;
            margin-bottom: 1rem;
        }
        .form-select-sm.rounded-pill {
            border-radius: 50px;
            padding: .25rem .75rem;
        }
    </style>

    <div class="card card-primary mb-3">
        <div class="card-header d-flex justify-content-between align-items-center">
            <a href="{{ route('admin.sales.create') }}" class="btn btn-success">
                <i class="fas fa-plus"></i> Registrar Nova Venda
            </a>
        </div>

        <div class="card-header p-0 border-bottom-0">
            <ul class="nav nav-tabs" id="salesStatusTabs" role="tablist">
                <li class="nav-item"><a class="nav-link active" href="#" data-status="">Todos</a></li>
                <li class="nav-item"><a class="nav-link" href="#" data-status="Pendente">Pendentes</a></li>
                <li class="nav-item"><a class="nav-link" href="#" data-status="Concluído">Concluídas</a></li>
                <li class="nav-item"><a class="nav-link" href="#" data-status="Cancelado">Canceladas</a></li>
            </ul>
        </div>

        <div class="card-body">
            <table id="salesTable" class="table table-bordered table-striped mb-0" style="width:100%;">
                <thead>
                    <tr>
                        <th>ID Venda</th>
                        <th>Cliente</th>
                        <th>Data</th>
                        <th>Valor Total</th>
                        <th>Status</th>
                        <th style="width: 80px">Ações</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>
    </div>

    <div class="modal fade" id="saleDetailsModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content rounded-lg shadow">
                {{-- Cabeçalho --}}
                <div class="modal-header bg-info text-white border-0">
                    <h5 class="modal-title">
                        <i class="fas fa-shopping-cart me-2 fs-4"></i>
                        Detalhes da Venda #<span id="view_sale_id"></span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                {{-- Corpo --}}
                <div class="modal-body py-4">
                    {{-- 1. Informações Gerais --}}
                    <div class="card mb-4">
                        <div class="card-body p-3">
                            <div class="icon-label">
                                <i class="fas fa-user text-info fs-5"></i>
                                <strong>Cliente:</strong>
                                <span id="view_client_name"></span>
                            </div>
                            <div class="icon-label">
                                <i class="fas fa-calendar-alt text-info fs-5"></i>
                                <strong>Data:</strong>
                                <span id="view_order_date"></span>
                            </div>
                            <div class="icon-label">
                                <i class="fas fa-credit-card text-info fs-5"></i>
                                <strong>Pagamento:</strong>
                                <span id="view_payment_method"></span>
                            </div>
                            <div class="icon-label mb-0">
                                <i class="fas fa-sticky-note text-info fs-5"></i>
                                <strong>Observações:</strong>
                                <span id="view_notes">—</span>
                            </div>
                        </div>
                    </div>

                    {{-- 2. Itens da Venda --}}
                    <div class="card mb-4">
                        <div class="card-header bg-primary text-white py-2">
                            <h6 class="mb-0">
                                <i class="fas fa-box-open me-2"></i>
                                Itens da Venda
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-striped mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Produto</th>
                                            <th class="text-center">Qtd.</th>
                                            <th class="text-end">Preço Unit.</th>
                                            <th class="text-end">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody id="view_saleItemsTbody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- 3. Custos Adicionais --}}
                    <div class="card mb-4">
                        <div class="card-header bg-info text-white py-2">
                            <h6 class="mb-0"><i class="fas fa-receipt me-2"></i>Custos Adicionais</h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="table-responsive">
                                <table class="table table-sm table-hover table-striped mb-0">
                                    <thead class="table-light">
                                        <tr>
                                            <th>Item</th>
                                            <th class="text-center">Qtd.</th>
                                            <th class="text-end">Custo Unit.</th>
                                            <th class="text-end">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody id="view_miscItemsTbody"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    {{-- 4. Resumo Financeiro --}}
                    <div class="card mb-4 border-info">
                        <div class="card-header bg-info text-white py-2">
                            <h6 class="mb-0">
                                <i class="fas fa-chart-line me-2"></i>
                                Resumo Financeiro
                            </h6>
                        </div>
                        <div class="card-body p-3">
                            <div class="row text-center gy-3">
                                <div class="col-md-4">
                                    <small class="text-muted">Total Custo Produtos</small>
                                    <div class="fs-5 fw-bold text-danger" id="view_totalProductsCost">R$ 0,00</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Total Custo Diversos</small>
                                    <div class="fs-5 fw-bold text-danger" id="view_totalMiscCost">R$ 0,00</div>
                                </div>
                                <div class="col-md-4">
                                    <small class="text-muted">Lucro Bruto</small>
                                    <div class="fs-5 fw-bold text-success" id="view_grossProfit">R$ 0,00</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Rodapé --}}
                <div class="modal-footer justify-content-between border-0 py-3">
                    <form id="updateSaleStatusForm" method="POST" class="d-flex align-items-center gap-2">
                        @csrf
                        <label class="mb-0">Status:</label>
                        <select name="status" id="view_statusSelect"
                                class="form-select form-select-solid form-select-sm rounded-pill" style="width:160px;">
                            <option value="Pendente">Pendente</option>
                            <option value="Concluído">Concluído</option>
                            <option value="Cancelado">Cancelado</option>
                        </select>
                        <button type="submit" class="btn btn-primary btn-sm">Salvar</button>
                    </form>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {

            // --- INICIALIZAÇÃO DE PLUGINS ---
            $('#view_statusSelect').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#saleDetailsModal'), // Essencial para funcionar no modal
                minimumResultsForSearch: Infinity // Esconde a caixa de busca
            });

            // --- Setup e Utilitários ---
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            const formatCurrency = v => !isNaN(+v) ?
                (+v).toLocaleString('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                }) :
                'R$ 0,00';
            const formatDate = d => d ?
                new Date(d).toLocaleString('pt-BR', {
                    dateStyle: 'short',
                    timeStyle: 'short',
                    timeZone: 'UTC'
                }) :
                'N/A';
            const handleAjaxError = xhr => {
                Swal.close();
                if (xhr.status === 419) return Swal.fire('Sessão Expirada!', 'Recarregue a página.', 'error')
                    .then(() => location.reload());
                Swal.fire('Erro!', 'Ocorreu um erro inesperado.', 'error');
            };

            // --- DataTable ---
            const table = $('#salesTable').DataTable({
                processing: true,
                ajax: "{{ route('admin.sales.data') }}",
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'client_name',
                        defaultContent: '<em>Consumidor Final</em>'
                    },
                    {
                        data: 'order_date',
                        render: formatDate
                    },
                    {
                        data: 'grand_total',
                        render: formatCurrency
                    },
                    {
                        data: 'status',
                        render: s => `<span class="badge ${
          s==='Concluído'?'bg-success':
          s==='Cancelado'?'bg-danger':'bg-warning text-dark'
        }">${s}</span>`
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: (d, t, r) => `
          <button class="btn btn-sm btn-info view-btn" data-id="${r.id}">
            <i class="fas fa-eye"></i>
          </button>`
                    }
                ],
                language: {
                    sEmptyTable: "Nenhuma venda encontrada",
                    // ... seus outros textos ...
                },
                order: [
                    [0, 'desc']
                ]
            });

            // --- Filtro por abas ---
            $('#salesStatusTabs .nav-link').on('click', function(e) {
                e.preventDefault();
                $('#salesStatusTabs .nav-link').removeClass('active');
                $(this).addClass('active');
                table.column(4).search($(this).data('status')).draw();
            });

            // --- Abrir Modal de Detalhes ---
            $('#salesTable tbody').on('click', '.view-btn', function() {
                const id = $(this).data('id');
                $('#view_sale_id').text(id);

                Swal.fire({
                    title: 'Carregando...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                $.get(`{{ url('admin/sales') }}/${id}`)
                    .done(res => {
                        const o = res.order;
                        const items = res.items;
                        const misc = res.misc_costs;

                        // --- Preenche info geral ---
                        $('#view_client_name').text(o.client_name || 'Consumidor Final');
                        $('#view_order_date').text(formatDate(o.order_date));
                        $('#view_payment_method').text(o.payment_method);
                        $('#view_notes').text(o.notes || '—');

                        // --- Tabela de produtos ---
                        let ph = '';
                        let totalProdCost = 0;
                        items.forEach(i => {
                            const subtotal = i.quantity * i.price_per_unit;
                            ph += `
            <tr>
              <td>${i.product_name}</td>
              <td>${i.quantity}</td>
              <td>${formatCurrency(i.price_per_unit)}</td>
              <td>${formatCurrency(subtotal)}</td>
            </tr>`;
                            totalProdCost += i.quantity * i.cost_per_unit;
                        });
                        $('#view_saleItemsTbody').html(ph);

                        // --- Tabela de custos diversos ---
                        let mh = '';
                        let totalMiscCost = 0;
                        misc.forEach(m => {
                            const total = m.quantity * m.cost_per_unit;
                            mh += `
            <tr>
              <td>${m.misc_item_name}</td>
              <td>${m.quantity}</td>
              <td>${formatCurrency(m.cost_per_unit)}</td>
              <td>${formatCurrency(total)}</td>
            </tr>`;
                            totalMiscCost += total;
                        });
                        $('#view_miscItemsTbody').html(mh);

                        // --- Totais e Lucro Bruto ---
                        $('#view_totalProductsCost').text(formatCurrency(totalProdCost));
                        $('#view_totalMiscCost').text(formatCurrency(totalMiscCost));
                        const grossProfit = o.grand_total - totalProdCost - totalMiscCost;
                        $('#view_grossProfit').text(formatCurrency(grossProfit));

                        // --- Status form ---
                        $('#updateSaleStatusForm')
                            .attr('action', `{{ url('admin/sales') }}/${id}/update-status`);
                        $('#view_statusSelect').val(o.status).trigger('change');

                        Swal.close();
                        $('#saleDetailsModal').modal('show');
                    })
                    .fail(handleAjaxError);
            });

            // --- Atualizar Status ---
            $('#updateSaleStatusForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                Swal.fire({
                    title: 'Salvando...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                $.post(form.attr('action'), form.serialize())
                    .done(r => {
                        Swal.fire('Sucesso!', r.success, 'success');
                        $('#saleDetailsModal').modal('hide');
                        table.ajax.reload(null, false);
                    })
                    .fail(handleAjaxError);
            });

        });
    </script>
@endpush
