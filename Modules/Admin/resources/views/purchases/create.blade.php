@extends('admin::layouts.layout')

@section('title', 'Registrar Nova Compra')
@section('header', 'Nova Compra')

@section('content')
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
            @csrf

            {{-- CARD 1: DADOS GERAIS DA COMPRA --}}
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
                                @foreach ($suppliers as $supplier)
                                    <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Data da Compra</label>
                            <input type="date" name="purchase_date" class="form-control" value="{{ date('Y-m-d') }}"
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

            {{-- CARD 2: ITENS DA COMPRA --}}
            <div class="card card-secondary mb-4">
                <div class="card-header">
                    <h3 class="card-title">Itens da Compra</h3>
                </div>
                <div class="card-body">
                    {{-- Form para adicionar itens --}}
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
                            {{-- Será populado via JS --}}
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

            {{-- BOTÕES DE SUBMISSÃO --}}
            <div class="d-flex btn-spacing mb-5">
                <button type="submit" class="btn btn-lg btn-success">Salvar Compra</button>
                <a href="{{ route('admin.purchases.index') }}" class="btn btn-lg btn-secondary">Cancelar</a>
            </div>
        </form>
    </div>
@endsection
@push('scripts')
    <script>
        $(function() {
            // --- SETUP INICIAL ---
            $('.select2').select2({
                theme: 'bootstrap-5'
            });

            // ====================================================================
            // NOVO: APLICANDO A MÁSCARA DE MOEDA AO CAMPO DE CUSTO UNITÁRIO
            // ====================================================================
            $('#itemUnitCost').mask('000.000.000.000.000,00', {
                reverse: true
            });


            // ====================================================================
            // FUNÇÃO DE MOEDA (VERSÃO FINAL E CORRIGIDA)
            // Usando o método .cleanVal() do próprio plugin de máscara.
            // ====================================================================
            const parseCurrency = () => {
                // .cleanVal() retorna apenas os dígitos do campo. Ex: "R$ 15,00" -> "1500"
                const valorEmCentavos = $('#itemUnitCost').cleanVal();

                // Se o valor for vazio ou nulo, retorna 0.
                if (!valorEmCentavos) {
                    return 0;
                }

                // Converte os centavos (string) para um número e divide por 100 para ter o valor real.
                // Ex: parseInt("1500") / 100 = 15.0
                return parseInt(valorEmCentavos) / 100;
            };


            const products = @json($products);
            const productTypes = @json($productTypes);
            const miscItems = @json($miscItems);
            const miscCategories = @json($miscCategories);
            // (Restante das variáveis globais...)
            const itemTypeSelector = $('#itemTypeSelector');
            const productTypeContainer = $('#productTypeSelectorContainer');
            const productTypeSelector = $('#productTypeSelector');
            const miscCategoryContainer = $('#miscCategorySelectorContainer');
            const miscCategorySelector = $('#miscCategorySelector');
            const itemSelector = $('#itemSelector');
            let purchaseItems = [];


            // --- LÓGICA DOS DROPDOWNS ---
            // (Todo o código de lógica dos seletores permanece o mesmo)
            itemTypeSelector.on('change', function() {
                const selectedType = $(this).val();
                productTypeContainer.hide();
                miscCategoryContainer.hide();
                itemSelector.empty().prop('disabled', true).html(
                    '<option value="">Selecione acima</option>').trigger('change');
                if (selectedType === 'Product') {
                    populateProductTypeSelector();
                    productTypeContainer.show();
                } else if (selectedType === 'MiscItem') {
                    populateMiscCategorySelector();
                    miscCategoryContainer.show();
                }
            });
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


            // --- LÓGICA PARA ADICIONAR ITEM À LISTA ---
            $("#addItemBtn").on("click", function() {
                const selectedOption = $("#itemSelector").find("option:selected");
                const itemId = selectedOption.val();
                if (!itemId) {
                    return Swal.fire("Atenção!", "Por favor, selecione um item válido.", "warning");
                }

                const itemName = selectedOption.text();
                const itemType = selectedOption.data("type");
                const quantity = parseInt($("#itemQuantity").val());

                // ====================================================================
                // ALTERAÇÃO CRÍTICA: Usando a nova função parseCurrency
                // ====================================================================
                const unitCost = parseCurrency($("#itemUnitCost").val());

                if (isNaN(quantity) || quantity <= 0 || isNaN(unitCost) || unitCost < 0) {
                    return Swal.fire("Atenção!", "Preencha a quantidade e o custo unitário corretamente.",
                        "warning");
                }

                purchaseItems.push({
                    id: itemId,
                    type: itemType,
                    name: itemName,
                    quantity: quantity,
                    unit_cost: unitCost,
                    total_cost: quantity * unitCost
                });

                renderItemsTable();

                // Limpa os campos para o próximo item
                itemTypeSelector.val("").trigger("change");
                $("#itemQuantity").val(1);
                $("#itemUnitCost").val("");
            });


            // --- FUNÇÕES DE RENDERIZAÇÃO E SUBMISSÃO (com a mesma lógica de antes) ---
            function renderItemsTable() {
                const tbody = $('#purchaseItemsTbody');
                tbody.empty();
                let grandTotal = 0;
                $.each(purchaseItems, function(index, item) {
                    grandTotal += item.total_cost;
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
                            <td><button type="button" class="btn btn-sm btn-danger removeItemBtn" data-index="${index}"><i class="fas fa-trash"></i></button></td>
                        </tr>
                    `;
                    tbody.append(row);
                });
                $('#grandTotal').text(grandTotal.toLocaleString('pt-BR', {
                    style: 'currency',
                    currency: 'BRL'
                }));
            }

            $("#purchaseItemsTbody").on("click", ".removeItemBtn", function() {
                purchaseItems.splice($(this).data("index"), 1);
                renderItemsTable();
            });

            $('#purchaseForm').on('submit', function(e) {
                e.preventDefault();
                if (purchaseItems.length === 0) {
                    return Swal.fire("Atenção!", "Você precisa adicionar pelo menos um item à compra.",
                        "warning");
                }
                Swal.fire({
                    title: "Salvando Compra...",
                    text: "Por favor, aguarde.",
                    allowOutsideClick: false,
                    didOpen: () => Swal.showLoading()
                });

                let formData = $(this).serializeArray();
                $.each(purchaseItems, function(i, item) {
                    formData.push({
                        name: `items[${i}][id]`,
                        value: item.id
                    });
                    formData.push({
                        name: `items[${i}][type]`,
                        value: item.type
                    });
                    formData.push({
                        name: `items[${i}][quantity]`,
                        value: item.quantity
                    });
                    formData.push({
                        name: `items[${i}][unit_cost]`,
                        value: item.unit_cost
                    });
                });

                $.ajax({
                    url: "{{ route('admin.purchases.store') }}",
                    type: "POST",
                    data: $.param(formData),
                    headers: {
                        'X-CSRF-TOKEN': "{{ csrf_token() }}"
                    },
                    success: function(response) {
                        Swal.fire({
                                icon: "success",
                                title: "Sucesso!",
                                text: response.success
                            })
                            .then(() => {
                                window.location.href = response.redirect_url
                            });
                    },
                    error: function(xhr) {
                        Swal.close();
                        if (xhr.status === 422) {
                            let errors = xhr.responseJSON.errors;
                            let html = '<ul class="text-start">';
                            $.each(errors, (key, value) => {
                                html += `<li>${value[0]}</li>`
                            });
                            html += "</ul>";
                            Swal.fire({
                                title: "Erro de Validação",
                                html: html,
                                icon: "error"
                            });
                        } else {
                            Swal.fire("Erro Inesperado!", "Ocorreu um erro no servidor.",
                                "error");
                        }
                    }
                });
            });
        });
    </script>
@endpush
