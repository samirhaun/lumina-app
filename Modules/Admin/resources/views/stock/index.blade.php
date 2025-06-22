@extends('admin::layouts.layout')

@section('title', 'Controle de Estoque')

@section('content')
    <div id="stockContent">
        <style>
            :root {
                --terracota: #A0522D;
                --beige: #EDE8E0;
                --white: #ffffff;
            }

            /* Botões primários */
            #stockContent .btn-primary {
                background-color: var(--terracota) !important;
                border-color: var(--terracota) !important;
                color: var(--white) !important;
            }

            /* Cards primários (caso existam) */
            #stockContent .card-primary .card-header {
                background-color: var(--terracota) !important;
                color: var(--white) !important;
                border-bottom: none;
            }

            /* Botão fechar só dentro do stockContent */
            #stockContent .modal-header .btn-close {
                width: 1.6rem;
                height: 1.6rem;
                background-color: #dc3545;
                border-radius: .25rem;
                position: relative;
            }

            #stockContent .modal-header .btn-close::before {
                content: "\f00d";
                font-family: "Font Awesome 6 Free";
                font-weight: 900;
                color: #fff;
                font-size: .9rem;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            }

            #stockContent .modal-header .btn-close:focus {
                box-shadow: none;
            }

            /* Abas e cards só dentro de stockContent */
            #stockContent .card-tabs .nav-pills .nav-link.active {
                background-color: var(--terracota);
                color: var(--white);
            }

            #stockContent .card-tabs .nav-pills .nav-link {
                color: rgba(0, 0, 0, .7);
            }

            #stockContent .card-terracotta.card-tabs>.card-header {
                border-bottom: none;
                background-color: var(--terracota);
                color: var(--white);
            }

            #stockContent .card-terracotta.card-tabs>.card-header .nav-link {
                background: transparent;
                border: 0;
                color: rgba(240, 230, 230, .8);
            }

            #stockContent .card-terracotta.card-tabs>.card-header .nav-link.active {
                background: var(--beige);
                color: var(--terracota);
                border-color: #dee2e6 #dee2e6 var(--beige);
            }

            /* Botões primários */
            #stockContent .btn-primary {
                background-color: var(--terracota) !important;
                border-color: var(--terracota) !important;
                color: var(--white) !important;
            }
        </style>

        <div class="card card-terracotta card-tabs">
            <div class="card-header p-2">
                <ul class="nav nav-pills">
                    <li class="nav-item">
                        <a class="nav-link active" href="#products-stock" data-bs-toggle="tab">
                            <i class="fas fa-boxes me-1"></i> Produtos
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#misc-stock" data-bs-toggle="tab">
                            <i class="fas fa-puzzle-piece me-1"></i> Custos Diversos
                        </a>
                    </li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="products-stock">
                        <ul class="nav nav-pills mb-3" id="product-type-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" href="#" data-category="">Todos os Tipos</a>
                            </li>
                            @foreach ($productTypes as $type)
                                <li class="nav-item">
                                    <a class="nav-link" href="#" data-category="{{ $type->name }}">
                                        {{ $type->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <table id="productStockTable" class="table table-bordered table-hover w-100">
                            <thead>
                                <tr>
                                    <th>Produto</th>
                                    <th>Tipo</th>
                                    <th>Estoque Mínimo</th>
                                    <th>Estoque Atual</th>
                                    <th>Status</th>
                                    <th>Custo Médio Unit.</th>
                                    <th>Valor em Estoque</th>
                                    <th style="width:50px">Ações</th>
                                </tr>
                            </thead>
                        </table>
                    </div>

                    <div class="tab-pane" id="misc-stock">
                        <ul class="nav nav-pills mb-3" id="misc-category-tabs">
                            <li class="nav-item">
                                <a class="nav-link active" href="#" data-category="">Todas as Categorias</a>
                            </li>
                            @foreach ($miscCategories as $category)
                                <li class="nav-item">
                                    <a class="nav-link" href="#" data-category="{{ $category->name }}">
                                        {{ $category->name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                        <table id="miscStockTable" class="table table-bordered table-hover w-100">
                            <thead>
                                <tr>
                                    <th>Item Diverso</th>
                                    <th>Categoria</th>
                                    <th>Estoque Mínimo</th>
                                    <th>Estoque Atual</th>
                                    <th>Status</th>
                                    <th>Custo Médio Unit.</th>
                                    <th>Valor em Estoque</th>
                                    <th style="width:50px">Ações</th>
                                </tr>
                            </thead>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal de edição --}}
        <div class="modal fade" id="editMinimumStockModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Editar Estoque Mínimo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="editMinimumStockForm">
                        @csrf
                        <input type="hidden" id="edit_item_id" name="item_id">
                        <input type="hidden" id="edit_item_type" name="item_type">
                        <div class="modal-body">
                            <p>Item: <strong id="edit_item_name"></strong></p>
                            <div class="mb-3">
                                <label class="form-label" for="edit_minimum_stock">Novo Estoque Mínimo</label>
                                <input type="number" id="edit_minimum_stock" name="minimum_stock" class="form-control"
                                    min="0" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> {{-- fecha #stockContent --}}
@endsection
@push('scripts')
    <script>
        $(function() {
            // =================================================================
            // 1. SETUP E FUNÇÕES AUXILIARES
            // =================================================================
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            const dtLanguage = {
                "sEmptyTable": "Nenhum registro encontrado",
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

            const formatCurrency = value => parseFloat(value).toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            });

            function renderStockStatus(current, minimum) {
                if (current <= 0) {
                    return '<span class="badge bg-dark">Sem Estoque</span>';
                }
                if (current < minimum) {
                    return '<span class="badge bg-danger">Abaixo do Mínimo</span>';
                }
                if (current == minimum) {
                    return '<span class="badge bg-warning text-dark">No Limite</span>';
                }
                return '<span class="badge bg-success">OK</span>';
            }

            /**
             * Aplica a cor na linha inteira com base no status.
             * VERSÃO FINAL COM A LÓGICA CORRETA DE CORES
             */
            function applyRowColoring(row, data) {
                const current = parseInt(data.quantity_on_hand);
                const minimum = parseInt(data.minimum_stock);

                // Limpa classes de cor anteriores para garantir que a cor seja reavaliada corretamente no redraw
                $(row).removeClass('table-danger table-warning');

                if (current < minimum) {
                    // Vermelho para "Abaixo do Mínimo" e também para "Sem Estoque"
                    $(row).addClass('table-danger');
                } else if (current === minimum && current > 0) {
                    // Amarelo apenas se estiver EXATAMENTE no limite (e não for zero)
                    $(row).addClass('table-warning');
                }
                // Se estiver OK (current > minimum), nenhuma classe de cor é adicionada, ficando normal.
            }

            // =================================================================
            // 2. INICIALIZAÇÃO DAS DATATABLES
            // =================================================================
            const commonTableOptions = {
                processing: true,
                language: dtLanguage,
                createdRow: applyRowColoring // Aplica a nova função de colorir em cada linha
            };

            const productStockTable = $('#productStockTable').DataTable({
                ...commonTableOptions,
                ajax: "{{ route('admin.stock.product-data') }}",
                columns: [{
                        data: 'name'
                    }, {
                        data: 'category'
                    },
                    {
                        data: 'minimum_stock',
                        className: 'text-center'
                    },
                    {
                        data: 'quantity_on_hand',
                        className: 'text-center fw-bold'
                    },
                    {
                        data: null,
                        orderable: false,
                        className: 'text-center',
                        render: (d, t, r) => renderStockStatus(r.quantity_on_hand, r.minimum_stock)
                    },
                    {
                        data: 'average_cost',
                        render: formatCurrency
                    },
                    {
                        data: null,
                        orderable: false,
                        render: (d, t, r) => formatCurrency(r.quantity_on_hand * r.average_cost)
                    },
                    {
                        data: null,
                        orderable: false,
                        className: 'text-center',
                        render: (d, t, r) =>
                            `<button class="btn btn-xs btn-primary edit-stock-btn" data-id="${r.id}" data-name="${r.name}" data-current="${r.minimum_stock}" data-type="Product"><i class="fas fa-edit"></i></button>`
                    }
                ]
            });

            const miscStockTable = $('#miscStockTable').DataTable({
                ...commonTableOptions,
                ajax: "{{ route('admin.stock.misc-item-data') }}",
                columns: [{
                        data: 'name'
                    }, {
                        data: 'category'
                    },
                    {
                        data: 'minimum_stock',
                        className: 'text-center'
                    },
                    {
                        data: 'quantity_on_hand',
                        className: 'text-center fw-bold'
                    },
                    {
                        data: null,
                        orderable: false,
                        className: 'text-center',
                        render: (d, t, r) => renderStockStatus(r.quantity_on_hand, r.minimum_stock)
                    },
                    {
                        data: 'average_cost',
                        render: formatCurrency
                    },
                    {
                        data: null,
                        orderable: false,
                        render: (d, t, r) => formatCurrency(r.quantity_on_hand * r.average_cost)
                    },
                    {
                        data: null,
                        orderable: false,
                        className: 'text-center',
                        render: (d, t, r) =>
                            `<button class="btn btn-xs btn-primary edit-stock-btn" data-id="${r.id}" data-name="${r.name}" data-current="${r.minimum_stock}" data-type="MiscItem"><i class="fas fa-edit"></i></button>`
                    }
                ]
            });

            $('#product-type-tabs').on('click', '.nav-link', function(e) {
                e.preventDefault();
                $('#product-type-tabs .nav-link').removeClass('active');
                $(this).addClass('active');
                const category = $(this).data('category');
                // Filtra a coluna 1 (Tipo) da tabela de produtos
                productStockTable.column(1).search(category).draw();
            });

            // Filtro para Categorias Diversas
            $('#misc-category-tabs').on('click', '.nav-link', function(e) {
                e.preventDefault();
                $('#misc-category-tabs .nav-link').removeClass('active');
                $(this).addClass('active');
                const category = $(this).data('category');
                // Filtra a coluna 1 (Categoria) da tabela de itens diversos
                miscStockTable.column(1).search(category).draw();
            });
            // =================================================================
            // 3. LÓGICA DO MODAL E EVENTOS
            // =================================================================
            const editModal = new bootstrap.Modal(document.getElementById('editMinimumStockModal'));

            // Evento para abrir o modal e preencher os dados
            $('.table').on('click', '.edit-stock-btn', function() {
                const button = $(this);
                $('#edit_item_id').val(button.data('id'));
                $('#edit_item_type').val(button.data('type'));
                $('#edit_item_name').text(button.data('name'));
                $('#edit_minimum_stock').val(button.data('current'));
                editModal.show();
            });

            // Evento para submeter o formulário de edição
            $('#editMinimumStockForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                const data = form.serialize();

                $.post("{{ route('admin.stock.update-minimum') }}", data)
                    .done(function(response) {
                        editModal.hide();
                        Swal.fire('Sucesso!', response.success, 'success');
                        // Recarrega ambas as tabelas para garantir que os dados estejam atualizados
                        productStockTable.ajax.reload();
                        miscStockTable.ajax.reload();
                    })
                    .fail(function() {
                        Swal.fire('Erro!', 'Não foi possível atualizar o estoque mínimo.', 'error');
                    });
            });

            // Ajuste das tabelas ao trocar de aba
            $('a[data-bs-toggle="tab"]').on('shown.bs.tab', function() {
                $($.fn.dataTable.tables(true)).DataTable().columns.adjust();
            });
        });
    </script>
@endpush
