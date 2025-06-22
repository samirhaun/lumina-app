

<?php $__env->startSection('title', 'Registrar Nova Compra'); ?>
<?php $__env->startSection('header', 'Nova Compra'); ?>

<?php $__env->startSection('content'); ?>
    <div id="purchaseContent">
        <style>
            /* Espaçamento extra entre os botões de salvar/cancelar */
            .btn-spacing>.btn+.btn {
                margin-left: 1rem;
                /* ajusta a distância aqui */
            }

            :root {
                --terracota: #A0522D;
                --white: #ffffff;
            }

            /* 1) Card-primary em terracota */
            #purchaseContent .card-primary .card-header {
                background-color: var(--terracota);
                color: var(--white);
                border-bottom: none;
            }

            /* Se quiser um card secundário neutro */
            #purchaseContent .card-secondary .card-header {
                background-color: #f5f5f5;
                color: #333;
                border-bottom: none;
            }

            /* 2) Botões de sucesso em terracota */
            #purchaseContent .btn-success {
                background-color: var(--terracota);
                border-color: var(--terracota);
            }

            /* 3) Select2 full-width dentro da página */
            #purchaseContent .select2-container--bootstrap-5 .select2-selection--single {
                min-width: 100%;
            }

            /* 4) Labels com peso um pouco maior */
            #purchaseContent label {
                font-weight: 500;
            }
        </style>

        <form id="purchaseForm">
            <?php echo csrf_field(); ?>

            
            <div class="card card-primary mb-4">
                <div class="card-header">
                    <h3 class="card-title">Dados da Compra</h3>
                </div>
                <div class="card-body">
                    <div class="row gy-3">
                        <div class="col-md-4">
                            <label class="form-label">Fornecedor</label>
                            <select name="supplier_id" class="form-control select2">
                                <option value="">Sem fornecedor</option>
                                <?php $__currentLoopData = $suppliers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $supplier): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <option value="<?php echo e($supplier->id); ?>"><?php echo e($supplier->name); ?></option>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Data da Compra</label>
                            <input type="date" name="purchase_date" class="form-control" value="<?php echo e(date('Y-m-d')); ?>"
                                required>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Data de Vencimento</label>
                            <input type="date" name="due_date" class="form-control">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Status</label>
                            <select name="status" class="form-control">
                                <option value="Pendente">Pendente</option>
                                <option value="Pago">Pago</option>
                            </select>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Observações</label>
                        <textarea name="notes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
            </div>

            
            <div class="card card-secondary mb-4">
                <div class="card-header">
                    <h3 class="card-title">Itens da Compra</h3>
                </div>
                <div class="card-body">
                    
                    <div class="row align-items-end gy-3" id="addItemForm">
                        <div class="col-md-3">
                            <label class="form-label">Tipo de Item</label>
                            <select id="itemTypeSelector" class="form-control">
                                <option value="">Selecione...</option>
                                <option value="Product">Produto</option>
                                <option value="MiscItem">Custo Diverso</option>
                            </select>
                        </div>
                        <div id="productTypeSelectorContainer" class="col-md-3" style="display:none;">
                            <label class="form-label">Tipo do Produto</label>
                            <select id="productTypeSelector" class="form-control select2"></select>
                        </div>
                        <div id="miscCategorySelectorContainer" class="col-md-3" style="display:none;">
                            <label class="form-label">Categoria do Custo</label>
                            <select id="miscCategorySelector" class="form-control select2"></select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label">Item</label>
                            <select id="itemSelector" class="form-control select2" disabled></select>
                        </div>
                        <div class="col-md-1">
                            <label class="form-label">Qtd.</label>
                            <input type="number" id="itemQuantity" class="form-control" min="1" value="1">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Custo Unit. (R$)</label>
                            <input type="text" id="itemUnitCost" class="form-control" placeholder="0,00">
                        </div>
                    </div>

                    <button type="button" id="addItemBtn" class="btn btn-info mt-3">
                        <i class="fas fa-plus me-1"></i>Adicionar Item à Lista
                    </button>

                    <hr>

                    <h5>Itens Adicionados à Compra</h5>
                    <table class="table table-bordered mt-2">
                        <thead class="table-light">
                            <tr>
                                <th>Item</th>
                                <th>Quantidade</th>
                                <th>Custo Unit.</th>
                                <th>Custo Total</th>
                                <th style="width:50px">Ação</th>
                            </tr>
                        </thead>
                        <tbody id="purchaseItemsTbody">
                            
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="3" class="text-end">TOTAL GERAL:</th>
                                <th id="grandTotal" colspan="2">R$ 0,00</th>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            
<div class="d-flex btn-spacing mb-5">
  <button type="submit" class="btn btn-lg btn-success">Salvar Compra</button>
  <a href="<?php echo e(route('admin.purchases.index')); ?>" class="btn btn-lg btn-secondary">Cancelar</a>
</div>
        </form>
    </div>
<?php $__env->stopSection(); ?>
<?php $__env->startPush('scripts'); ?>
    <script>
        $(function() {
            // --- SETUP INICIAL ---
            $('.select2').select2({
                theme: 'bootstrap-5'
            });

            // --- VARIÁVEIS GLOBAIS ---
            const products = <?php echo json_encode($products, 15, 512) ?>;
            const productTypes = <?php echo json_encode($productTypes, 15, 512) ?>;
            const miscItems = <?php echo json_encode($miscItems, 15, 512) ?>;
            const miscCategories = <?php echo json_encode($miscCategories, 15, 512) ?>;

            const itemTypeSelector = $('#itemTypeSelector');
            const productTypeContainer = $('#productTypeSelectorContainer');
            const productTypeSelector = $('#productTypeSelector');
            const miscCategoryContainer = $('#miscCategorySelectorContainer');
            const miscCategorySelector = $('#miscCategorySelector');
            const itemSelector = $('#itemSelector');

            let purchaseItems = [];

            // --- LÓGICA DO PRIMEIRO DROPDOWN (TIPO DE ITEM) ---
            itemTypeSelector.on('change', function() {
                const selectedType = $(this).val();

                // Esconde ambos os filtros e reseta o seletor de item
                productTypeContainer.hide();
                miscCategoryContainer.hide();
                itemSelector.empty().prop('disabled', true).html(
                    '<option value="">Selecione acima</option>').trigger('change');

                if (selectedType === 'Product') {
                    populateProductTypeSelector();
                    productTypeContainer.show(); // Mostra o filtro de Tipo de Produto
                } else if (selectedType === 'MiscItem') {
                    populateMiscCategorySelector();
                    miscCategoryContainer.show(); // Mostra o filtro de Categoria Diversa
                }
            });

            // --- LÓGICA DO FILTRO DE TIPO DE PRODUTO ---
            productTypeSelector.on('change', function() {
                const selectedProductTypeId = $(this).val();
                if (!selectedProductTypeId) {
                    itemSelector.empty().prop('disabled', true).html(
                        '<option value="">Selecione um tipo</option>').trigger('change');
                    return;
                }
                const filteredProducts = products.filter(p => p.product_type_id == selectedProductTypeId);
                populateItemSelector(filteredProducts, 'Product');
            });

            // --- LÓGICA DO FILTRO DE CATEGORIA DIVERSA ---
            miscCategorySelector.on('change', function() {
                const selectedCategoryId = $(this).val();
                if (!selectedCategoryId) {
                    itemSelector.empty().prop('disabled', true).html(
                        '<option value="">Selecione uma categoria</option>').trigger('change');
                    return;
                }
                const filteredItems = miscItems.filter(item => item.misc_category_id == selectedCategoryId);
                populateItemSelector(filteredItems, 'MiscItem');
            });

            // --- FUNÇÕES AUXILIARES DE POPULAÇÃO ---
            function populateProductTypeSelector() {
                productTypeSelector.empty().append('<option value="">Selecione um tipo...</option>');
                $.each(productTypes, function(index, type) {
                    productTypeSelector.append(`<option value="${type.id}">${type.name}</option>`);
                });
                productTypeSelector.trigger('change');
            }

            function populateMiscCategorySelector() {
                miscCategorySelector.empty().append('<option value="">Selecione uma categoria...</option>');
                $.each(miscCategories, function(index, category) {
                    miscCategorySelector.append(`<option value="${category.id}">${category.name}</option>`);
                });
                miscCategorySelector.trigger('change');
            }

            function populateItemSelector(items, type) {
                itemSelector.empty().append('<option value="">Selecione um item...</option>');
                $.each(items, function(index, item) {
                    let itemName = item.item_name ? `${item.item_name} (${item.category_name})` : item.name;
                    itemSelector.append(
                        `<option value="${item.id}" data-type="${type}">${itemName}</option>`);
                });
                itemSelector.prop('disabled', false).trigger('change');
            }

            // --- LÓGICA PARA ADICIONAR, REMOVER E SALVAR ITENS ---
            // (Este código permanece o mesmo da resposta anterior)
            $('#addItemBtn').on('click', function() {
                /* ... */
            });

            function renderItemsTable() {
                /* ... */
            }
            $('#purchaseItemsTbody').on('click', '.removeItemBtn', function() {
                /* ... */
            });
            $('#purchaseForm').on('submit', function(e) {
                /* ... */
            });

            // --- FUNÇÃO PARA RENDERIZAR A TABELA DE ITENS ---
            function renderItemsTable() {
                const tbody = $('#purchaseItemsTbody');
                tbody.empty();
                let grandTotal = 0;

                $.each(purchaseItems, function(index, item) {
                    grandTotal += item.total_cost;

                    // --- CORREÇÃO APLICADA AQUI ---
                    // Usamos a função toLocaleString para formatar a moeda corretamente para pt-BR
                    const unitCostFormatted = item.unit_cost.toLocaleString('pt-BR', {
                        style: 'currency',
                        currency: 'BRL'
                    });

                    const totalCostFormatted = item.total_cost.toLocaleString('pt-BR', {
                        style: 'currency',
                        currency: 'BRL'
                    });

                    let row = `
                <tr>
                    <td>${item.name}</td>
                    <td>${item.quantity}</td>
                    <td>${unitCostFormatted}</td>
                    <td>${totalCostFormatted}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger removeItemBtn" data-index="${index}">
                            <i class="fas fa-trash"></i>
                        </button>
                    </td>
                </tr>
            `;
                    tbody.append(row);
                });

                // Formata também o total geral
                $('#grandTotal').text(grandTotal.toLocaleString('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                }));
            }
            $("#addItemBtn").on("click", function() {
                const t = $("#itemSelector").find("option:selected"),
                    e = t.val();
                if (!e) return void Swal.fire("Atenção!", "Por favor, selecione um item válido.",
                    "warning");
                const r = t.text(),
                    o = t.data("type"),
                    n = parseInt($("#itemQuantity").val()),
                    a = parseFloat($("#itemUnitCost").val().replace(",", "."));
                if (isNaN(n) || n <= 0 || isNaN(a) || a < 0) return void Swal.fire("Atenção!",
                    "Preencha a quantidade e o custo unitário corretamente.", "warning");
                purchaseItems.push({
                        id: e,
                        type: o,
                        name: r,
                        quantity: n,
                        unit_cost: a,
                        total_cost: n * a
                    }), renderItemsTable(), itemTypeSelector.val("").trigger("change"), $("#itemQuantity")
                    .val(1), $("#itemUnitCost").val("")
            }), $("#purchaseItemsTbody").on("click", ".removeItemBtn", function() {
                purchaseItems.splice($(this).data("index"), 1), renderItemsTable()
            }), $("#purchaseForm").on("submit", function(t) {
                t.preventDefault(), 0 !== purchaseItems.length ? (Swal.fire({
                    title: "Salvando Compra...",
                    text: "Por favor, aguarde.",
                    allowOutsideClick: !1,
                    didOpen: () => Swal.showLoading()
                }), $.ajax({
                    url: "<?php echo e(route('admin.purchases.store')); ?>",
                    type: "POST",
                    data: $.param(function(t) {
                        var e = $(t).serializeArray();
                        return $.each(purchaseItems, function(t, r) {
                            e.push({
                                name: `items[${t}][id]`,
                                value: r.id
                            }), e.push({
                                name: `items[${t}][type]`,
                                value: r.type
                            }), e.push({
                                name: `items[${t}][quantity]`,
                                value: r.quantity
                            }), e.push({
                                name: `items[${t}][unit_cost]`,
                                value: r.unit_cost
                            })
                        }), e
                    }(this)),
                    headers: {
                        "X-CSRF-TOKEN": "<?php echo e(csrf_token()); ?>"
                    },
                    success: function(t) {
                        Swal.fire({
                            icon: "success",
                            title: "Sucesso!",
                            text: t.success
                        }).then(() => {
                            window.location.href = t.redirect_url
                        })
                    },
                    error: function(t) {
                        if (Swal.close(), 422 === t.status) {
                            var e = t.responseJSON.errors,
                                r = '<ul class="text-start">';
                            $.each(e, (t, e) => {
                                r += `<li>${e[0]}</li>`
                            }), r += "</ul>", Swal.fire({
                                title: "Erro de Validação",
                                html: r,
                                icon: "error"
                            })
                        } else Swal.fire("Erro Inesperado!", "Ocorreu um erro no servidor.",
                            "error")
                    }
                })) : Swal.fire("Atenção!", "Você precisa adicionar pelo menos um item à compra.",
                    "warning")
            });

        });
    </script>
<?php $__env->stopPush(); ?>

<?php echo $__env->make('admin::layouts.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Samir\Desktop\lumina-app\Modules/Admin\resources/views/purchases/create.blade.php ENDPATH**/ ?>