<?php $__env->startSection('title', 'Catálogo de Produtos'); ?>

<?php $__env->startSection('content'); ?>
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

            /* abas terracota/bege no catálogo de produtos */
            #catalogContent .card-terracotta.card-tabs>.card-header .nav-link {
                background: transparent;
                border: 0;
                color: rgba(240, 230, 230, .8);
            }

            #catalogContent .card-terracotta.card-tabs>.card-header .nav-link.active {
                background: var(--beige);
                color: var(--terracota);
                border-color: #dee2e6 #dee2e6 var(--beige);
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
                    <div class="card card-terracotta card-tabs mb-3 mt-3">
                        <div class="card-header p-0 border-bottom-0">
                            <ul class="nav nav-tabs" id="productTypeTabs" role="tablist">
                                <li class="nav-item">
                                    <a class="nav-link active" href="#" data-type="">Todos</a>
                                </li>
                                <?php $__currentLoopData = $productTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li class="nav-item">
                                        <a class="nav-link" href="#" data-type="<?php echo e($type->name); ?>">
                                            <?php echo e($type->name); ?>

                                        </a>
                                    </li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    </div>
                    <div class="card-body px-3">
                        <table id="productsTable" class="table table-bordered table-striped w-100">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Código</th>
                                    <th>Nome do Produto</th>
                                    <th>Tipo</th>
                                    <th>Visível na Loja?</th>
                                    <th style="width:80px">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>


<div class="modal fade" id="typeModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="typeModalLabel">Novo Tipo de Produto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="typeForm">
                <?php echo csrf_field(); ?>
                <div class="modal-body">
                    
                    <input type="hidden" id="type_id" name="id">
                    <div class="mb-3">
                        <label for="type_name" class="form-label">Nome do Tipo</label>
                        <input type="text" class="form-control" id="type_name" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="productModalLabel">Novo Produto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="productForm">
                <?php echo csrf_field(); ?>
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
                        <select id="product_type_id" name="product_type_id" class="form-select" style="width:100%" required></select>
                    </div>

                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea id="description" name="description" class="form-control tinymce-editor"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="specifications" class="form-label">Especificações</label>
                        <textarea id="specifications" name="specifications" class="form-control tinymce-editor"></textarea>
                    </div>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" value="1" id="show_in_store" name="show_in_store">
                        <label class="form-check-label" for="show_in_store">
                            Mostrar na loja
                        </label>
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
    </div> 
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
    <script src="https://cdn.tiny.cloud/1/45t54fd85pkigdxhieoirre5pbt4xchm5g5sb5z52gxa4q3r/tinymce/7/tinymce.min.js"
        referrerpolicy="origin"></script>

    <script>
        $(function() {

            // --- SETUP GLOBAL ---
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                }
            });

            // =================================================================
            // INICIALIZAÇÃO DO EDITOR DE TEXTO RICO (TINYMCE)
            // =================================================================
            tinymce.init({
                selector: 'textarea.tinymce-editor',
                plugins: 'code table lists link autoresize',
                toolbar: 'undo redo | blocks | bold italic | alignleft aligncenter alignright | bullist numlist | code | table | link',
                language: 'pt_BR',
                language_url: 'https://cdn.jsdelivr.net/npm/tinymce-i18n/langs/pt_BR.js',
                menubar: false,
                height: 250,
            });

            // --- FUNÇÃO PARA ATUALIZAR O DROPDOWN DE TIPOS ---
            function updateProductTypesDropdown(selectedValue = null) {
                var select = $('#product_type_id');
                var previouslySelected = selectedValue || select.val();

                select.prop('disabled', true).html('<option>Carregando tipos...</option>');

                $.ajax({
                    url: "<?php echo e(route('admin.product-types.list')); ?>",
                    type: 'GET',
                    success: function(types) {
                        select.empty().append('<option value="">Selecione um tipo...</option>');
                        $.each(types, function(index, type) {
                            select.append($('<option>', {
                                value: type.id,
                                text: type.name
                            }));
                        });
                        if (previouslySelected) {
                            select.val(previouslySelected);
                        }
                        select.prop('disabled', false);
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
                theme: 'bootstrap-5',
                dropdownParent: $('#productModal')
            });

            // --- DATATABLE PARA TIPOS DE PRODUTO ---
            var typesTable = $('#typesTable').DataTable({
                processing: true,
                ajax: "<?php echo e(route('admin.product-types.data')); ?>",
                columns: [{
                        data: 'id'
                    },
                    {
                        data: 'name'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            return `
                        <button class="btn btn-sm btn-primary edit-type-btn"
                                data-id="${row.id}"
                                data-name="${row.name}">
                          <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-type-btn"
                                data-id="${row.id}"
                                data-name="${row.name}">
                          <i class="fas fa-trash"></i>
                        </button>`;
                        }
                    }
                ],
                language: {
                    sEmptyTable: "Nenhum registro encontrado",
                    sInfo: "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                    sInfoEmpty: "Mostrando 0 até 0 de 0 registros",
                    sInfoFiltered: "(Filtrados de _MAX_ registros no total)",
                    sLengthMenu: "_MENU_ resultados por página",
                    sLoadingRecords: "Carregando...",
                    sProcessing: "Processando...",
                    sZeroRecords: "Nenhum registro encontrado",
                    sSearch: "Pesquisar",
                    oPaginate: {
                        sNext: "Próximo",
                        sPrevious: "Anterior",
                        sFirst: "Primeiro",
                        sLast: "Último"
                    }
                }
            });

            // --- DATATABLE PARA PRODUTOS ---
            var productsTable = $('#productsTable').DataTable({
                processing: true,
                ajax: "<?php echo e(route('admin.products.data')); ?>",
                columns: [{
                        data: 'id'
                    }, {
                        data: 'code'
                    }, {
                        data: 'name'
                    }, {
                        data: 'type_name'
                    },
                    {
                        data: 'show_in_store',
                        className: 'text-center',
                        render: d => d ? '<span class="badge bg-success">Sim</span>' :
                            '<span class="badge bg-secondary">Não</span>'
                    },
                    {
                        data: null,
                        orderable: false,
                        searchable: false,
                        render: function(data, type, row) {
                            let imagesUrl =
                                "<?php echo e(route('admin.products.images.index', ['product' => ':id'])); ?>"
                                .replace(':id', row.id);

                            // =================================================================
                            // ATUALIZAÇÃO CRÍTICA: Passando os novos dados para o botão
                            // =================================================================
                            return `
                        <a href="${imagesUrl}" class="btn btn-sm btn-info" title="Gerenciar Imagens"><i class="fas fa-camera"></i></a>
                        <button class="btn btn-sm btn-primary edit-product-btn"
                                data-id="${row.id}"
                                data-name="${row.name}"
                                data-code="${row.code || ''}"
                                data-product-type-id="${row.product_type_id}"
                                data-show-in-store="${row.show_in_store}"
                                data-description="${row.description || ''}"
                                data-specifications="${row.specifications || ''}"
                                title="Editar Produto">
                            <i class="fas fa-edit"></i>
                        </button>
                        <button class="btn btn-sm btn-danger delete-product-btn" data-id="${row.id}" data-name="${row.name}" title="Excluir Produto"><i class="fas fa-trash"></i></button>
                    `;
                        }
                    }
                ],
                language: {
                    sEmptyTable: "Nenhum registro encontrado",
                    sInfo: "Mostrando de _START_ até _END_ de _TOTAL_ registros",
                    sInfoEmpty: "Mostrando 0 até 0 de 0 registros",
                    sInfoFiltered: "(Filtrados de _MAX_ registros no total)",
                    sLengthMenu: "_MENU_ resultados por página",
                    sLoadingRecords: "Carregando...",
                    sProcessing: "Processando...",
                    sZeroRecords: "Nenhum registro encontrado",
                    sSearch: "Pesquisar",
                    oPaginate: {
                        sNext: "Próximo",
                        sPrevious: "Anterior",
                        sFirst: "Primeiro",
                        sLast: "Último"
                    }
                }
            });

            // --- FILTRO POR TIPO VIA ABAS ---
            $('#productTypeTabs').on('click', '.nav-link', function(e) {
                e.preventDefault();
                // ativa visualmente a aba
                $('#productTypeTabs .nav-link').removeClass('active');
                $(this).addClass('active');

                // lê o tipo (string vazia = sem filtro)
                var type = $(this).data('type');

                // coluna 3 é "Tipo" (0:id,1:code,2:name,3:type,4:visível,5:ações)
                productsTable.column(3).search(type).draw();
            });
            // --- HANDLER DE ERROS AJAX ---
            function handleAjaxError(xhr) {
                Swal.close();
                if (xhr.status === 419) {
                    Swal.fire('Sessão Expirada!', 'Recarregue a página.', 'error').then(() => location.reload());
                } else if (xhr.status === 422) {
                    let html = '<ul class="text-start">';
                    $.each(xhr.responseJSON.errors, (k, v) => html += `<li>${v[0]}</li>`);
                    html += '</ul>';
                    Swal.fire('Erro de Validação', html, 'error');
                } else if (xhr.status === 409) {
                    Swal.fire('Ação não permitida', xhr.responseJSON.error, 'warning');
                } else {
                    Swal.fire('Erro!', 'Ocorreu um erro inesperado.', 'error');
                }
            }

            // --- MODAL DE TIPO DE PRODUTO ---
            $('[data-bs-target="#typeModal"]').on('click', function() {
                $('#typeForm')[0].reset();
                $('#typeModalLabel').text('Novo Tipo de Produto');
                $('#typeForm').data('method', 'POST').data('url',
                    '<?php echo e(route('admin.product-types.store')); ?>');
            });
            $('#typesTable').on('click', '.edit-type-btn', function() {
                let data = $(this).data();
                $('#typeModalLabel').text('Editar Tipo de Produto');
                $('#type_id').val(data.id);
                $('#type_name').val(data.name);
                $('#typeForm').data('method', 'PUT').data('url',
                    `<?php echo e(url('admin/product-types')); ?>/${data.id}`);
                $('#typeModal').modal('show');
            });
            $('#typeForm').on('submit', function(e) {
                e.preventDefault();
                let form = $(this);
                Swal.fire({
                    title: 'Salvando...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                $.ajax({
                    url: form.data('url'),
                    type: form.data('method'),
                    data: form.serialize(),
                    success(res) {
                        $('#typeModal').modal('hide');
                        Swal.fire('Sucesso!', res.success, 'success');
                        typesTable.ajax.reload(null, false);
                        updateProductTypesDropdown();
                    },
                    error: handleAjaxError
                });
            });
            $('#typesTable').on('click', '.delete-type-btn', function() {
                let data = $(this).data();
                Swal.fire({
                    title: 'Você tem certeza?',
                    text: `Excluir tipo "${data.name}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, excluir'
                }).then(res => {
                    if (!res.isConfirmed) return;
                    $.ajax({
                        url: `<?php echo e(url('admin/product-types')); ?>/${data.id}`,
                        type: 'DELETE',
                        success(resp) {
                            Swal.fire('Excluído!', resp.success, 'success');
                            typesTable.ajax.reload(null, false);
                            updateProductTypesDropdown();
                        },
                        error: handleAjaxError
                    });
                });
            });

            // --- MODAL DE PRODUTO ---
            $('[data-bs-target="#productModal"]').on('click', function() {
                $('#productForm')[0].reset();
                $('#productModalLabel').text('Novo Produto');
                $('#productForm').data('method', 'POST').data('url',
                    "<?php echo e(route('admin.products.store')); ?>");
                updateProductTypesDropdown();
                $('#show_in_store').prop('checked', false);

                // Limpa os editores de texto ao criar novo produto
                tinymce.get('description').setContent('');
                tinymce.get('specifications').setContent('');
            });

            $('#productsTable').on('click', '.edit-product-btn', function() {
                let data = $(this).data();
                $('#productModalLabel').text('Editar Produto');
                $('#product_id').val(data.id);
                $('#product_name').val(data.name);
                $('#product_code').val(data.code);
                $('#show_in_store').prop('checked', data.showInStore == 1);

                // =================================================================
                // ATUALIZAÇÃO CRÍTICA: Preenchendo os editores de texto
                // =================================================================
                tinymce.get('description').setContent(data.description);
                tinymce.get('specifications').setContent(data.specifications);

                $('#productForm').data('method', 'PUT').data('url',
                    `<?php echo e(url('admin/products')); ?>/${data.id}`);
                updateProductTypesDropdown(data.productTypeId);
                $('#productModal').modal('show');
            });

            $('#productForm').on('submit', function(e) {
                e.preventDefault();

                // =================================================================
                // ATUALIZAÇÃO CRÍTICA: Salvar o conteúdo do TinyMCE antes do AJAX
                // =================================================================
                tinymce.triggerSave();

                let form = $(this);
                Swal.fire({
                    title: 'Salvando...',
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });
                $.ajax({
                    url: form.data('url'),
                    type: form.data('method'),
                    data: form.serialize(),
                    success(res) {
                        $('#productModal').modal('hide');
                        Swal.fire('Sucesso!', res.success, 'success');
                        productsTable.ajax.reload(null, false);
                    },
                    error: handleAjaxError
                });
            });
            $('#productsTable').on('click', '.delete-product-btn', function() {
                let data = $(this).data();
                Swal.fire({
                    title: 'Você tem certeza?',
                    text: `Excluir produto "${data.name}"?`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, excluir'
                }).then(res => {
                    if (!res.isConfirmed) return;
                    $.ajax({
                        url: `<?php echo e(url('admin/products')); ?>/${data.id}`,
                        type: 'DELETE',
                        success(resp) {
                            Swal.fire('Excluído!', resp.success, 'success');
                            productsTable.ajax.reload(null, false);
                        },
                        error: handleAjaxError
                    });
                });
            });

        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin::layouts.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Bruno\Documents\lumina-app\lumina-app\Modules/Admin\resources/views/products/index.blade.php ENDPATH**/ ?>