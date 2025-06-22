@extends('admin::layouts.layout')

@section('title', 'Catálogo de Produtos')

@section('content')
    <div id="catalogContent">
        <style>
            :root {
                --terracota: #A0522D;
                --white: #ffffff;
            }

            /* Só dentro de #catalogContent: cabeçalhos dos cards em terracota */
            #catalogContent .card>.card-header {
                background-color: var(--terracota) !important;
                color: var(--white) !important;
                border-bottom: none;
            }

            /* Botões primários em terracota */
            #catalogContent .btn-primary {
                background-color: var(--terracota) !important;
                border-color: var(--terracota) !important;
            }

            /* Fecha-modal personalizado, apenas aqui */
            #catalogContent .modal-header .btn-close {
                width: 1.6rem;
                height: 1.6rem;
                background-color: #dc3545;
                border-radius: .25rem;
                position: relative;
            }

            #catalogContent .modal-header .btn-close::before {
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

            #catalogContent .modal-header .btn-close:focus {
                box-shadow: none;
            }

            /* Botões de ação (+) */
            .btn-success {
                background-color: var(--terracota);
                border-color: var(--terracota);
            }
        </style>

        <div class="row">
            <div class="col-md-5">
                <div class="card">
                    <div class="card-header">
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                data-bs-target="#typeModal">
                                <i class="fas fa-plus"></i> Novo Tipo
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="typesTable" class="table table-bordered table-striped w-100">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Nome</th>
                                    <th style="width:80px">Ações</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="col-md-7">
                <div class="card">
                    <div class="card-header">
                        <div class="card-tools">
                            <button type="button" class="btn btn-sm btn-success" data-bs-toggle="modal"
                                data-bs-target="#productModal">
                                <i class="fas fa-plus"></i> Novo Produto
                            </button>
                        </div>
                    </div>
                    <div class="card-body">
                        <table id="productsTable" class="table table-bordered table-striped w-100">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Código</th>
                                    <th>Nome do Produto</th>
                                    <th>Tipo</th>
                                    <th style="width:80px">Ações</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        {{-- Modal: Tipo de Produto --}}
        <div class="modal fade" id="typeModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="typeModalLabel">Novo Tipo de Produto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="typeForm">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" id="type_id" name="id">
                            <div class="mb-3">
                                <label for="type_name" class="form-label">Nome do Tipo</label>
                                <input type="text" class="form-control" id="type_name" name="name" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary" id="typeFormSubmitButton">
                                Salvar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- Modal: Produto --}}
        <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="productModalLabel">Novo Produto</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <form id="productForm">
                        @csrf
                        <div class="modal-body">
                            <input type="hidden" id="product_id" name="id">
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="product_name" class="form-label">Nome do Produto</label>
                                    <input type="text" class="form-control" id="product_name" name="name" required>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="product_code" class="form-label">Código (SKU)</label>
                                    <input type="text" class="form-control" id="product_code" name="code">
                                </div>
                            </div>
                            <div class="mb-3">
                                <label for="product_type_id" class="form-label">Tipo do Produto</label>
                                <select id="product_type_id" name="product_type_id" class="form-select" style="width:100%"
                                    required></select>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                            <button type="submit" class="btn btn-primary" id="productFormSubmitButton">
                                Salvar
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div> {{-- fecha #catalogContent --}}
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

            // --- NOVA FUNÇÃO PARA ATUALIZAR O DROPDOWN DE TIPOS ---
            function updateProductTypesDropdown(selectedValue = null) {
                var select = $('#product_type_id');
                // Guarda o valor que estava selecionado antes (útil para edição)
                var previouslySelected = selectedValue || select.val();

                select.prop('disabled', true).html('<option>Carregando tipos...</option>');

                $.ajax({
                    url: "{{ route('admin.product-types.list') }}",
                    type: 'GET',
                    success: function(types) {
                        select.empty().append('<option value="">Selecione um tipo...</option>');
                        $.each(types, function(index, type) {
                            select.append($('<option>', {
                                value: type.id,
                                text: type.name
                            }));
                        });
                        // Restaura a seleção anterior se houver uma
                        if (previouslySelected) {
                            select.val(previouslySelected);
                        }
                        select.prop('disabled', false);
                        // Atualiza o Select2 se ele já foi inicializado
                        if (select.hasClass("select2-hidden-accessible")) {
                            select.trigger('change');
                        }
                    },
                    error: function() {
                        select.prop('disabled', false).html('<option>Erro ao carregar tipos</option>');
                    }
                });
            }

            $('#product_type_id').select2({
                theme: 'bootstrap-5', // Usa o tema do Bootstrap 5 que adicionamos
                dropdownParent: $('#productModal') // ESSENCIAL: Faz o dropdown funcionar dentro do modal
            });

            // --- DATATABLE PARA TIPOS DE PRODUTO ---
            var typesTable = $('#typesTable').DataTable({
                processing: true,
                ajax: "{{ route('admin.product-types.data') }}",
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
                            return `
                    <button class="btn btn-sm btn-primary edit-type-btn" data-id="${row.id}" data-name="${row.name}"><i class="fas fa-edit"></i></button>
                    <button class="btn btn-sm btn-danger delete-type-btn" data-id="${row.id}" data-name="${row.name}"><i class="fas fa-trash"></i></button>
                `;
                        }
                    }
                ],
                language: {
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
                }
            });

            // --- DATATABLE PARA PRODUTOS (SIMPLIFICADO) ---
            var productsTable = $('#productsTable').DataTable({
                processing: true,
                ajax: "{{ route('admin.products.data') }}",
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'code'
                    },

                    {
                        data: 'name'
                    },
                    {
                        data: 'type_name'
                    },
                    {
                        data: null,
                        searchable: false,
                        orderable: false,
                        render: function(data, type, row) {
                            // Gera a URL para a página de imagens do produto
                            let imagesUrl =
                                "{{ route('admin.products.images.index', ['product' => ':id']) }}";
                            imagesUrl = imagesUrl.replace(':id', row.id);

                            // Retorna os botões de ação, incluindo o novo atalho
                            return `
                                <a href="${imagesUrl}" class="btn btn-sm btn-info" title="Gerenciar Imagens">
                                    <i class="fas fa-camera"></i>
                                </a>
                                <button class="btn btn-sm btn-primary edit-product-btn" 
                                    data-id="${row.id}" 
                                    data-name="${row.name}" 
                                    data-code="${row.code || ''}"
                                    data-type-id="${row.product_type_id}"
                                    title="Editar Produto">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-danger delete-product-btn" 
                                    data-id="${row.id}" 
                                    data-name="${row.name}"
                                    title="Excluir Produto">
                                    <i class="fas fa-trash"></i>
                                </button>
                            `;
                        }
                    }
                ],
                language: {
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
                }
            });

            // --- LÓGICA PARA MODAIS E AJAX ---
            function handleAjaxError(xhr) {
                if (xhr.status === 419) {
                    Swal.fire('Sessão Expirada!', 'Sua sessão expirou. Recarregue a página.', 'error').then(() =>
                        location.reload());
                } else if (xhr.status === 422) {
                    var errors = xhr.responseJSON.errors;
                    var errorHtml = '<ul>';
                    $.each(errors, (key, value) => {
                        errorHtml += '<li>' + value[0] + '</li>';
                    });
                    errorHtml += '</ul>';
                    Swal.fire('Erro de Validação', errorHtml, 'error');
                } else if (xhr.status === 409) {
                    Swal.fire('Ação não permitida', xhr.responseJSON.error, 'warning');
                } else {
                    Swal.fire('Erro!', 'Ocorreu um erro inesperado no servidor.', 'error');
                }
            }

            // LÓGICA PARA TIPO DE PRODUTO (permanece a mesma)
            $('#typesTable tbody').on('click', '.edit-type-btn', function() {
                var data = $(this).data();
                $('#typeForm')[0].reset();
                $('#typeModalLabel').text('Editar Tipo de Produto');
                $('#type_id').val(data.id);
                $('#type_name').val(data.name);
                $('#typeForm').data('method', 'PUT').data('url', '{{ url('admin/product-types') }}/' + data
                    .id);
                $('#typeModal').modal('show');
            });

            $('[data-bs-target="#typeModal"]').on('click', function() {
                $('#typeForm')[0].reset();
                $('#typeModalLabel').text('Novo Tipo de Produto');
                $('#typeForm').data('method', 'POST').data('url',
                    '{{ route('admin.product-types.store') }}');
            });

            $('#typeForm').on('submit', function(e) {
                /* ...código ajax de tipo de produto... */
            });
            // --- DELETAR PRODUTO ---
            $('#productsTable tbody').on('click', '.delete-product-btn', function() {
                const id = $(this).data('id');
                const name = $(this).data('name');

                Swal.fire({
                    title: 'Você tem certeza?',
                    text: `Deseja excluir o produto "${name}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, excluir',
                    cancelButtonText: 'Cancelar'
                }).then(result => {
                    if (!result.isConfirmed) return;

                    Swal.fire({
                        title: 'Excluindo...',
                        allowOutsideClick: false,
                        showConfirmButton: false,
                        didOpen: () => Swal.showLoading()
                    });

                    $.ajax({
                        url: `{{ url('admin/products') }}/${id}`,
                        type: 'DELETE',
                        success: function(response) {
                            Swal.fire('Excluído!', response.success, 'success');
                            productsTable.ajax.reload(null, false);
                        },
                        error: function(xhr) {
                            Swal.close();
                            // Se for violação de FK (SQLSTATE 23000, code 1451)
                            const msg = xhr.responseJSON?.message || '';
                            if (xhr.status === 500 && msg.includes('1451')) {
                                Swal.fire(
                                    'Não foi possível excluir',
                                    'Este produto possui itens de pedido associados e não pode ser excluído. ' +
                                    'Remova primeiro os itens de pedido ou use um “cascade delete” na FK.',
                                    'error'
                                );
                            } else {
                                // seu handle genérico
                                handleAjaxError(xhr);
                            }
                        }
                    });
                });
            });


            // ABRIR MODAL DE PRODUTOS
            $('#productsTable tbody').on('click', '.edit-product-btn', function() {
                var data = $(this).data();
                updateProductTypesDropdown(data.typeId);

                $('#productForm')[0].reset();
                $('#productModalLabel').text('Editar Produto');
                $('#product_id').val(data.id);
                $('#product_name').val(data.name);
                $('#product_code').val(data.code); // Preenche o campo de código
                $('#productForm').data('method', 'PUT').data('url',
                    `{{ url('admin/products') }}/${data.id}`);
                $('#productModal').modal('show');
            });
            $('[data-bs-target="#productModal"]').on('click', function() {
                // ATUALIZA O DROPDOWN TAMBÉM AO CRIAR UM NOVO PRODUTO
                updateProductTypesDropdown();

                $('#productForm')[0].reset();
                $('#productModalLabel').text('Novo Produto');
                $('#productForm').data('method', 'POST').data('url', '{{ route('admin.products.store') }}');
            });
            // SUBMETER FORM DE PRODUTOS
            $('#productForm').on('submit', function(e) {
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
                        $('#productModal').modal('hide');
                        Swal.fire('Sucesso!', response.success, 'success');
                        productsTable.ajax.reload(null, false);
                    },
                    error: handleAjaxError
                });
            });

            // DELETAR PRODUTO
            $('#productsTable tbody').on('click', '.delete-product-btn', function() {
                /* ...código de exclusão de produto... */
            });

            // REUTILIZANDO CÓDIGO AJAX PARA EVITAR REPETIÇÃO
            // Cole o código do submit e do delete do tipo de produto aqui para não deixar a resposta muito longa
            // Submit Tipo de Produto
            $('#typeForm').on('submit', function(e) {
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
                        $('#typeModal').modal('hide');
                        Swal.fire('Sucesso!', response.success, 'success');
                        typesTable.ajax.reload(null, false);
                        updateProductTypesDropdown(); // <-- ADICIONE ESTA LINHA

                    },
                    error: handleAjaxError
                });
            });

            // Deletar Tipo de Produto
            $('#typesTable tbody').on('click', '.delete-type-btn', function() {
                var data = $(this).data();
                Swal.fire({
                    title: 'Você tem certeza?',
                    text: `Deseja excluir o tipo "${data.name}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, excluir!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ url('admin/product-types') }}/' + data.id,
                            type: 'DELETE',
                            success: function(response) {
                                Swal.fire('Excluído!', response.success, 'success');
                                typesTable.ajax.reload(null, false);
                                updateProductTypesDropdown(); // <-- ADICIONE ESTA LINHA

                            },
                            error: handleAjaxError
                        });
                    }
                });
            });

            // Deletar Produto
            $('#productsTable tbody').on('click', '.delete-product-btn', function() {
                var data = $(this).data();
                Swal.fire({
                    title: 'Você tem certeza?',
                    text: `Deseja excluir o produto "${data.name}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, excluir!',
                    cancelButtonText: 'Cancelar'
                }).then((result) => {
                    if (result.isConfirmed) {
                        $.ajax({
                            url: '{{ url('admin/products') }}/' + data.id,
                            type: 'DELETE',
                            success: function(response) {
                                Swal.fire('Excluído!', response.success, 'success');
                                productsTable.ajax.reload(null, false);
                            },
                            error: handleAjaxError
                        });
                    }
                });
            });

        });
    </script>
@endpush
