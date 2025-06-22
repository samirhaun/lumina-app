@extends('admin::layouts.layout')

@section('title', 'Custos Diversos')

@section('content')
    <div id="miscCostsContent">
    <style>
        :root {
            --terracota: #A0522D;
            --white: #ffffff;
        }

        /* Cards */
        #miscCostsContent .card {
            border: none;
            border-radius: .5rem;
            box-shadow: 0 2px 6px rgba(0, 0, 0, .1);
            margin-bottom: 1.5rem;
        }

        /* Cabeçalho dos cards */
        #miscCostsContent .card-header {
            background-color: var(--terracota);
            color: var(--white);
            border-bottom: none;
            font-weight: 500;
        }

        /* Botões de ação (+) */
        #miscCostsContent .btn-success {
            background-color: var(--terracota);
            border-color: var(--terracota);
        }

        /* Table striped hover */
        #miscCostsContent table.table-striped tbody tr:hover {
            background-color: rgba(160, 82, 45, 0.1);
        }

        /* Espaçamento entre ações */
        #miscCostsContent .btn + .btn {
            margin-left: .5rem;
        }
    </style>

        <div class="row">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header d-flex justify-content-end align-items-center">
                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                            data-bs-target="#miscCategoryModal">
                            <i class="fas fa-plus"></i> Nova Categoria
                        </button>
                    </div>
                    <div class="card-body">
                        <table id="miscCategoriesTable" class="table table-bordered table-striped w-100">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th style="width: 80px">Ações</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card">
                    <div class="card-header d-flex justify-content-end align-items-center">
                        <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                            data-bs-target="#miscItemModal">
                            <i class="fas fa-plus"></i> Novo Item
                        </button>
                    </div>
                    <div class="card-body">
                        <table id="miscItemsTable" class="table table-bordered table-striped w-100">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome do Item</th>
                                    <th>Categoria</th>
                                    <th style="width: 80px">Ações</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal Categoria --}}
        <div class="modal fade" id="miscCategoryModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="miscCategoryModalLabel">Nova Categoria</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="miscCategoryForm">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" id="misc_category_id_field" name="id">
                            <div class="mb-3">
                                <label for="misc_category_name" class="form-label">Nome da Categoria</label>
                                <input type="text" class="form-control" id="misc_category_name" name="name" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal Item --}}
        <div class="modal fade" id="miscItemModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="miscItemModalLabel">Novo Item</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form id="miscItemForm">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" id="misc_item_id" name="id">
                            <div class="mb-3">
                                <label for="misc_item_name" class="form-label">Nome do Item</label>
                                <input type="text" class="form-control" id="misc_item_name" name="name" required>
                            </div>
                            <div class="mb-3">
                                <label for="misc_item_category_id" class="form-label">Categoria</label>
                                <select class="form-select" id="misc_item_category_id" name="misc_category_id" required>
                                    {{-- via AJAX --}}
                                </select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary">Salvar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            // --- SETUP GLOBAL ---
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            // --- TRADUÇÃO PADRÃO PARA TODAS AS DATATABLES ---
            // Definimos a tradução uma vez para reutilizar e evitar erros de carregamento
            var dataTableLanguage = {
                "sEmptyTable": "Nenhum registro encontrado",
                "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
                "sInfoFiltered": "(Filtrados de _MAX_ registros no total)",
                "sInfoPostFix": "",
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
                },
                "oAria": {
                    "sSortAscending": ": Ordenar colunas de forma ascendente",
                    "sSortDescending": ": Ordenar colunas de forma descendente"
                }
            };

            // --- INICIALIZAÇÃO DO SELECT2 ---
            $('#misc_item_category_id').select2({
                theme: 'bootstrap-5',
                dropdownParent: $('#miscItemModal')
            });

            // --- FUNÇÃO PARA ATUALIZAR DROPDOWN ---
            function updateMiscCategoriesDropdown(selectedValue = null) {
                var select = $('#misc_item_category_id');
                var previouslySelected = selectedValue || select.val();
                select.prop('disabled', true).html('<option>Carregando...</option>');
                $.ajax({
                    url: "{{ route('admin.misc-categories.list') }}",
                    type: 'GET',
                    success: function(categories) {
                        select.empty().append('<option value="">Selecione...</option>');
                        $.each(categories, function(index, category) {
                            select.append($('<option>', {
                                value: category.id,
                                text: category.name
                            }));
                        });
                        if (previouslySelected) {
                            select.val(previouslySelected);
                        }
                        select.prop('disabled', false);
                        select.trigger('change');
                    }
                });
            }

            // --- INICIALIZAÇÃO DAS DATATABLES ---
            var miscCategoriesTable = $('#miscCategoriesTable').DataTable({
                processing: true,
                ajax: "{{ route('admin.misc-categories.data') }}",
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: null,
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row) {
                            return `<button class="btn btn-sm btn-primary edit-category-btn" data-id="${row.id}" data-name="${row.name}"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger delete-category-btn" data-id="${row.id}" data-name="${row.name}"><i class="fas fa-trash"></i></button>`;
                        }
                    }
                ],
                language: dataTableLanguage // Usando a tradução local
            });

            var miscItemsTable = $('#miscItemsTable').DataTable({
                processing: true,
                ajax: "{{ route('admin.misc-items.data') }}",
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: 'category_name'
                    },
                    {
                        data: null,
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row) {
                            return `<button class="btn btn-sm btn-primary edit-item-btn" data-id="${row.id}" data-name="${row.name}" data-category-id="${row.misc_category_id}"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-danger delete-item-btn" data-id="${row.id}" data-name="${row.name}"><i class="fas fa-trash"></i></button>`;
                        }
                    }
                ],
                language: dataTableLanguage // Usando a mesma tradução local
            });

            // --- LÓGICA GERAL DE AJAX E SWAL ---
            function handleAjaxError(xhr) {
                Swal.close();
                if (xhr.status === 419) {
                    Swal.fire('Sessão Expirada!', 'Sua sessão expirou. Recarregue a página.', 'error').then(() =>
                        location.reload());
                } else if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorHtml = '<ul class="text-start">';
                    $.each(errors, (key, value) => {
                        errorHtml += '<li>' + value[0] + '</li>';
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
                    Swal.fire('Erro Inesperado!', 'Ocorreu um erro no servidor.', 'error');
                }
            }

            // --- EVENTOS PARA CATEGORIAS ---
            $('[data-bs-target="#miscCategoryModal"]').on('click', function() {
                $('#miscCategoryForm')[0].reset();
                $('#miscCategoryModalLabel').text('Nova Categoria');
                $('#miscCategoryForm').data('method', 'POST').data('url',
                    '{{ route('admin.misc-categories.store') }}');
            });

            $('#miscCategoriesTable tbody').on('click', '.edit-category-btn', function() {
                var data = $(this).data();
                $('#miscCategoryForm')[0].reset();
                $('#miscCategoryModalLabel').text('Editar Categoria');
                $('#misc_category_id_field').val(data.id);
                $('#misc_category_name').val(data.name);
                $('#miscCategoryForm').data('method', 'PUT').data('url',
                    `{{ url('admin/misc-categories') }}/${data.id}`);
                $('#miscCategoryModal').modal('show');
            });

            $('#miscCategoryForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
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
                        $('#miscCategoryModal').modal('hide');
                        Swal.fire('Sucesso!', response.success, 'success');
                        miscCategoriesTable.ajax.reload(null, false);
                        updateMiscCategoriesDropdown(); // Atualiza o dropdown em outra tabela
                    },
                    error: handleAjaxError
                });
            });

            $('#miscCategoriesTable tbody').on('click', '.delete-category-btn', function() {
                var data = $(this).data();
                Swal.fire({
                    title: 'Você tem certeza?',
                    text: `Deseja excluir a categoria "${data.name}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, excluir!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ url('admin/misc-categories') }}/${data.id}`,
                            type: 'DELETE',
                            success: function(response) {
                                Swal.fire('Excluído!', response.success, 'success');
                                miscCategoriesTable.ajax.reload(null, false);
                                updateMiscCategoriesDropdown
                                    (); // Atualiza o dropdown em outra tabela
                            },
                            error: handleAjaxError
                        });
                    }
                });
            });

            // --- EVENTOS PARA ITENS ---
            $('[data-bs-target="#miscItemModal"]').on('click', function() {
                updateMiscCategoriesDropdown();
                $('#miscItemForm')[0].reset();
                $('#miscItemModalLabel').text('Novo Item');
                $('#miscItemForm').data('method', 'POST').data('url',
                    '{{ route('admin.misc-items.store') }}');
            });

            $('#miscItemsTable tbody').on('click', '.edit-item-btn', function() {
                var data = $(this).data();
                updateMiscCategoriesDropdown(data.categoryId);
                $('#miscItemForm')[0].reset();
                $('#miscItemModalLabel').text('Editar Item');
                $('#misc_item_id').val(data.id);
                $('#misc_item_name').val(data.name);
                $('#miscItemForm').data('method', 'PUT').data('url',
                    `{{ url('admin/misc-items') }}/${data.id}`);
                $('#miscItemModal').modal('show');
            });

            $('#miscItemForm').on('submit', function(e) {
                e.preventDefault();
                var form = $(this);
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
                        $('#miscItemModal').modal('hide');
                        Swal.fire('Sucesso!', response.success, 'success');
                        miscItemsTable.ajax.reload(null, false);
                    },
                    error: handleAjaxError
                });
            });

            $('#miscItemsTable tbody').on('click', '.delete-item-btn', function() {
                var data = $(this).data();
                Swal.fire({
                    title: 'Você tem certeza?',
                    text: `Deseja excluir o item "${data.name}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, excluir!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: `{{ url('admin/misc-items') }}/${data.id}`,
                            type: 'DELETE',
                            success: function(response) {
                                Swal.fire('Excluído!', response.success, 'success');
                                miscItemsTable.ajax.reload(null, false);
                            },
                            error: handleAjaxError
                        });
                    }
                });
            });

        });
    </script>
@endpush
