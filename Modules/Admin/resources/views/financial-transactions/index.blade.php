@extends('admin::layouts.layout')

@section('title', 'Movimentações Financeiras')

@section('content')
    <style>
        /* Estilos para o card com a cor terracota */
        .card-terracotta.card-tabs>.card-header {
            border-bottom: none;
        }

        .card-terracotta.card-tabs>.card-header .nav-link {
            background: transparent;
            border: 0;
            color: #f0e6e6;
            /* Cor para abas inativas */
        }

        .card-terracotta.card-tabs>.card-header .nav-link.active {
            background: white;
            /* Fundo branco para a aba ativa */
            color: #A0522D;
            /* Texto terracota na aba ativa */
            border-color: #dee2e6 #dee2e6 #fff;
        }

        .card-terracotta>.card-header {
            background-color: #A0522D;
            color: #ffffff;
        }

        /* NOVA CLASSE PARA FORÇAR O ESPAÇAMENTO */
        .btn-action-group .btn {
            margin-left: 8px;
            /* Adiciona uma margem à esquerda de cada botão no grupo */
        }
    </style>

    <div class="card card-terracotta card-tabs">
        <div class="card-header p-0 pt-1 border-bottom-0">
            <ul class="nav nav-tabs" id="transaction-type-tabs" role="tablist">
                <li class="nav-item"><a class="nav-link active" href="#" data-type="">Todos</a></li>
                <li class="nav-item"><a class="nav-link" href="#" data-type="credit">Créditos (Entradas)</a></li>
                <li class="nav-item"><a class="nav-link" href="#" data-type="debit">Débitos (Saídas)</a></li>
            </ul>
        </div>
        <div class="card-body">
            <div class="d-flex justify-content-end mb-3 btn-action-group">
                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#categoriesModal">
                    <i class="fas fa-tags"></i> Gerenciar Categorias
                </button>
                <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#transactionModal">
                    <i class="fas fa-plus"></i> Novo Lançamento
                </button>
            </div>
            <div class="tab-content">
                <div class="tab-pane fade show active">
                    <table id="transactionsTable" class="table table-bordered table-striped" style="width:100%;">
                        <thead>
                            <tr>
                                <th style="width: 20px;"></th>
                                <th>ID</th>
                                <th>Data</th>
                                <th>Descrição</th>
                                <th>Categoria</th>
                                <th>Tipo</th>
                                <th class="text-end">Valor</th>
                                <th style="width: 50px">Ações</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="transactionModal" tabindex="-1" aria-hidden="true">

        <div class="modal-dialog modal-lg">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="transactionModalLabel">Novo Lançamento</h5>


                </div>

                <form id="transactionForm">

                    @csrf

                    <div class="modal-body">

                        <input type="hidden" id="transaction_id" name="id">

                        <div class="row">

                            <div class="col-md-4 form-group mb-3"><label for="transaction_date">Data</label><input
                                    type="date" class="form-control" id="transaction_date" name="transaction_date"
                                    value="{{ date('Y-m-d') }}" required>
                            </div>

                            <div class="col-md-8 form-group mb-3"><label for="description">Descrição
                                    Curta</label><input type="text" class="form-control" id="description"
                                    name="description" required></div>

                        </div>

                        <div class="row">

                            <div class="col-md-4 form-group mb-3"><label for="type">Tipo de
                                    Lançamento</label><select class="form-select" id="type" name="type" required
                                    style="width: 100%;">
                                    <option value="">Selecione...</option>
                                    <option value="credit">Entrada (Crédito)</option>
                                    <option value="debit">Saída (Débito)</option>
                                </select></div>

                            <div class="col-md-5 form-group mb-3"><label for="category_id">Categoria</label><select
                                    class="form-select" id="category_id" name="category_id" required style="width: 100%;"
                                    disabled></select></div>

                            <div class="col-md-3 form-group mb-3"><label for="amount">Valor
                                    (R$)</label><input type="text" class="form-control" id="amount" name="amount"
                                    required placeholder="0,00"></div>

                        </div>

                        <div class="form-group"><label for="notes">Observações (Opcional)</label>
                            <textarea class="form-control" id="notes" name="notes" rows="3"></textarea>
                        </div>

                    </div>

                    <div class="modal-footer"><button type="button" class="btn btn-secondary"
                            data-bs-dismiss="modal">Cancelar</button><button type="submit" class="btn btn-primary"
                            id="transactionSubmitButton">Salvar</button></div>

                </form>

            </div>

        </div>

    </div>
    <div class="modal fade" id="categoriesModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Gerenciar Categorias Financeiras</h5>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-5">
                            <div class="card card-success card-outline h-100">
                                <div class="card-header">
                                    <h5 class="card-title m-0">Adicionar Nova</h5>
                                </div>
                                <div class="card-body">
                                    <form id="categoryForm">
                                        @csrf
                                        <div class="form-group mb-2"><label for="category_name">Nome</label><input
                                                type="text" class="form-control" id="category_name" name="name"
                                                required></div>
                                        <div class="form-group mb-3"><label for="category_type">Tipo</label><select
                                                class="form-select" id="category_type" name="type" required
                                                style="width: 100%;">
                                                <option value="">Selecione...</option>
                                                <option value="credit">Entrada (Crédito)</option>
                                                <option value="debit">Saída (Débito)</option>
                                            </select></div>
                                        <button type="submit" class="btn btn-success w-100">Salvar Nova
                                            Categoria</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-7">
                            <div class="card card-primary card-outline h-100">
                                <div class="card-header">
                                    <h5 class="card-title m-0">Categorias Existentes</h5>
                                </div>
                                <div class="card-body p-0">
                                    <div class="table-responsive" style="max-height: 350px;">
                                        <table class="table table-sm table-hover">
                                            <tbody id="categoryList"></tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                {{-- CORREÇÃO: Adicionado botão de fechar --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                </div>
            </div>
        </div>
    </div>
@endsection
@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        $(function() {
            // --- SETUP GLOBAL ---
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            let categories = @json($categories); // Torna a variável mutável
            const dtLanguage = {
                "sEmptyTable": "Nenhum lançamento encontrado",
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
                } else if (xhr.status === 409) {
                    Swal.fire('Ação não permitida', xhr.responseJSON.error, 'warning');
                } else {
                    Swal.fire('Erro!', 'Ocorreu um erro inesperado.', 'error');
                }
            };

            // --- INICIALIZAÇÃO DE PLUGINS ---
            $('#type, #category_id').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#transactionModal')
            });
            $('#category_type').select2({
                theme: 'bootstrap-5',
                minimumResultsForSearch: Infinity,
                dropdownParent: $('#categoriesModal')
            });
            $('#amount').mask('000.000.000,00', {
                reverse: true
            });

            const formatDate = dateString => dateString ? new Date(dateString).toLocaleDateString('pt-BR', {
                timeZone: 'UTC'
            }) : 'N/A';
            const formatCurrency = value => !isNaN(parseFloat(value)) ? parseFloat(value).toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }) : 'R$ 0,00';


            // --- DATATABLE ---
            const transactionsTable = $('#transactionsTable').DataTable({
                processing: true,
                ajax: "{{ route('admin.financial-transactions.data') }}",
                columns: [{
                        className: 'dt-control',
                        orderable: false,
                        data: null,
                        defaultContent: ''
                    },
                    {
                        data: 'id'
                    }, {
                        data: 'transaction_date',
                        render: formatDate
                    },
                    {
                        data: 'description'
                    }, {
                        data: 'category_name',
                        defaultContent: 'N/A'
                    },
                    {
                        data: 'type',
                        render: d => d === 'credit' ? '<span class="badge bg-success">Crédito</span>' :
                            '<span class="badge bg-danger">Débito</span>'
                    },
                    {
                        data: 'amount',
                        className: 'text-end',
                        render: formatCurrency
                    },
                    {
                        data: null,
                        searchable: false,
                        orderable: false,
                        render: (d, t, r) =>
                            `<button class="btn btn-sm btn-danger delete-btn" data-id="${r.id}" data-desc="${r.description}"><i class="fas fa-trash"></i></button>`
                    }
                ],
                language: dtLanguage,
                order: [
                    [2, 'desc']
                ] // Ordena pela data
            });

            // --- NOVA FUNÇÃO PARA FORMATAR A LINHA-FILHA ---
            function formatChildRow(data) {
                if (data.notes) {
                    return `<div class="p-3 bg-light border rounded"><strong>Observações:</strong><br>${data.notes}</div>`;
                }
                return '<div class="p-3 text-muted">Nenhuma observação para este lançamento.</div>';
            }

            // --- NOVO EVENTO PARA EXPANDIR/RECOLHER A LINHA ---
            $('#transactionsTable tbody').on('click', 'td.dt-control', function(e) {
                var tr = $(this).closest('tr');
                var row = transactionsTable.row(tr);

                if (row.child.isShown()) {
                    row.child.hide();
                    tr.removeClass('dt-hasChild');
                } else {
                    row.child(formatChildRow(row.data())).show();
                    tr.addClass('dt-hasChild');
                }
            });
            // LÓGICA DE FILTRAGEM POR ABAS (CORRIGIDA)
            $('#transaction-type-tabs').on('click', '.nav-link', function(e) {
                e.preventDefault();
                $('#transaction-type-tabs .nav-link').removeClass('active');
                $(this).addClass('active');

                const type = $(this).data('type');
                let termo = '';

                if (type === 'credit') termo = 'Crédito';
                else if (type === 'debit') termo = 'Débito';

                // aqui usamos smart:false, regex:false para buscar exatamente o texto
                transactionsTable
                    .column(5)
                    .search(termo, false, false)
                    .draw();
            });


            function populateCategories(type, selectedId = null) {
                const select = $('#category_id');
                select.empty().prop('disabled', true);
                if (!type) {
                    select.html('<option value="">Selecione um tipo primeiro...</option>').trigger('change');
                    return;
                }
                const filtered = categories.filter(c => c.type === type);
                select.append('<option value="">Selecione uma categoria...</option>');
                $.each(filtered, (i, cat) => {
                    select.append(`<option value="${cat.id}">${cat.name}</option>`);
                });
                if (selectedId) select.val(selectedId);
                select.prop('disabled', false).trigger('change');
            }
            $('#type').on('change', function() {
                populateCategories($(this).val());
            });
            $('[data-bs-target="#transactionModal"]').on('click', function() {
                $('#transactionForm')[0].reset();
                $('#type, #category_id').val(null).trigger('change');
                $('#transactionModalLabel').text('Novo Lançamento');
                $('#transactionForm').data('method', 'POST').data('url',
                    '{{ route('admin.financial-transactions.store') }}');
                $('#category_id').prop('disabled', true);
            });
            $('#transactionForm').on('submit', function(e) {
                e.preventDefault();
                const form = $(this);
                let formData = form.serializeArray().reduce((obj, item) => {
                    obj[item.name] = (item.name === 'amount') ? (parseFloat(String(item.value)
                        .replace(/\./g, '').replace(',', '.')) || 0) : item.value;
                    return obj;
                }, {});
                Swal.fire({
                    title: 'Salvando...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                $.ajax({
                    url: form.data('url'),
                    type: 'POST',
                    data: formData,
                    success: function(r) {
                        $('#transactionModal').modal('hide');
                        Swal.fire('Sucesso!', r.success, 'success');
                        transactionsTable.ajax.reload(null, false);
                    },
                    error: handleAjaxError
                });
            });
            $('#transactionsTable tbody').on('click', '.delete-btn', function() {
                const data = $(this).data();
                Swal.fire({
                    title: 'Você tem certeza?',
                    text: `Deseja excluir o lançamento "${data.desc}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, excluir!',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ url('admin/financial-transactions') }}/${data.id}`,
                            type: 'DELETE',
                            success: function(r) {
                                Swal.fire('Excluído!', r.success, 'success');
                                transactionsTable.ajax.reload(null, false);
                            },
                            error: handleAjaxError
                        });
                    }
                });
            });

            // --- LÓGICA DO MODAL DE CATEGORIAS ---
            function renderCategoryList() {
                const list = $('#categoryList');
                list.empty();
                if (categories.length === 0) {
                    list.html('<tr><td class="text-center text-muted p-3">Nenhuma categoria cadastrada.</td></tr>');
                    return;
                }
                $.each(categories, (i, cat) => {
                    const badge = cat.type === 'credit' ? '<span class="badge bg-success">Crédito</span>' :
                        '<span class="badge bg-danger">Débito</span>';
                    list.append(
                        `<tr><td>${cat.name}</td><td class="text-center">${badge}</td><td class="text-end"><button class="btn btn-xs btn-outline-danger delete-category-btn" data-id="${cat.id}" data-name="${cat.name}"><i class="fas fa-trash"></i></button></td></tr>`
                    );
                });
            }

            $('[data-bs-target="#categoriesModal"]').on('click', function() {
                renderCategoryList();
            });

            $('#categoryForm').on('submit', function(e) {
                e.preventDefault();
                $.post("{{ route('admin.financial-categories.store') }}", $(this).serialize()).done(
                    response => {
                        Swal.fire({
                            icon: 'success',
                            title: response.success,
                            showConfirmButton: false,
                            timer: 1500
                        });
                        categories.push(response.category);
                        categories.sort((a, b) => a.name.localeCompare(b.name));
                        $(this)[0].reset();
                        renderCategoryList();
                    }).fail(handleAjaxError);
            });

            $('#categoryList').on('click', '.delete-category-btn', function() {
                const data = $(this).data();
                Swal.fire({
                    title: 'Você tem certeza?',
                    text: `Deseja excluir a categoria "${data.name}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, excluir!',
                    cancelButtonText: 'Cancelar',
                    confirmButtonColor: '#d33'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ url('admin/financial-categories') }}/${data.id}`,
                            type: 'DELETE',
                            success: response => {
                                Swal.fire({
                                    icon: 'success',
                                    title: response.success,
                                    showConfirmButton: false,
                                    timer: 1500
                                });
                                categories = categories.filter(cat => cat.id !== data
                                    .id);
                                renderCategoryList();
                            },
                            error: handleAjaxError
                        });
                    }
                });
            });
        });
    </script>
@endpush
