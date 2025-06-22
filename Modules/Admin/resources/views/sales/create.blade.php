@extends('admin::layouts.layout')

@section('title', 'Registrar Nova Venda')

@section('content')
    <form id="saleForm" novalidate>
        @csrf
        <div class="row">
            {{-- COLUNA PRINCIPAL DA VENDA (ITENS) --}}
            <div class="col-lg-8">
                {{-- PAINEL DE PRODUTOS --}}
                <div class="card card-primary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">1. Adicionar Produtos à Venda</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label>Buscar Produto por Nome ou ID</label>
                            <select id="product_search" class="form-control" style="width: 100%;"></select>
                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Produto</th>
                                        <th style="width: 100px;">Qtd.</th>
                                        <th style="width: 130px;">Custo Unit.</th>
                                        <th style="width: 130px;">Preço Venda</th>
                                        <th style="width: 130px;">Subtotal</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="saleProductsTbody">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Nenhum produto adicionado.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                {{-- PAINEL DE CUSTOS DIVERSOS --}}
                <div class="card card-secondary card-outline">
                    <div class="card-header">
                        <h3 class="card-title">2. Adicionar Custos e Itens Adicionais</h3>
                    </div>
                    <div class="card-body">
                        <div class="row g-2 align-items-end">
                            <div class="col-md-4 form-group"><label>Categoria do Custo</label><select
                                    id="miscCategorySelector" class="form-control select2" style="width: 100%;">
                                    <option value="">Selecione...</option>
                                    @foreach ($miscCategories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-5 form-group"><label>Item</label><select id="miscItemSelector"
                                    class="form-control select2" style="width: 100%;" disabled></select></div>
                            <div class="col-md-1 form-group"><label>Qtd.</label><input type="number" id="miscItemQty"
                                    class="form-control" min="1" value="1"></div>
                            <div class="col-md-2 form-group">
                                <button type="button" id="addMiscItemBtn"
                                    class="btn btn-sm btn-info w-100 mt-auto">Adicionar</button>
                            </div>

                        </div>
                        <div class="table-responsive mt-3">
                            <table class="table table-bordered table-hover">
                                <thead>
                                    <tr>
                                        <th>Custo Adicional</th>
                                        <th style="width: 100px;">Qtd.</th>
                                        <th style="width: 130px;">Custo Unit.</th>
                                        <th style="width: 130px;">Custo Total</th>
                                        <th style="width: 50px;"></th>
                                    </tr>
                                </thead>
                                <tbody id="saleMiscCostsTbody">
                                    <tr>
                                        <td colspan="5" class="text-center text-muted">Nenhum custo adicional.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            {{-- COLUNA DE DETALHES E TOTAIS --}}
            <div class="col-lg-4">
                <div class="card card-info sticky-top">
                    <div class="card-header">
                        <h3 class="card-title">3. Fechamento e Pagamento</h3>
                    </div>
                    <div class="card-body">
                        <div class="form-group"><label>Cliente</label><select name="client_id" class="form-control select2">
                                <option value="">Consumidor Final</option>
                                @foreach ($clients as $client)
                                    <option value="{{ $client->id }}">{{ $client->name }}</option>
                                @endforeach
                            </select></div>
                        <div class="form-group mt-2">
                            <label>Data da Venda</label>
                            <input type="date" name="order_date" class="form-control"
                                value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <hr>
                        <table class="table table-sm">
                            <tr>
                                <th>Subtotal dos Produtos:</th>
                                <td class="text-end" id="subtotalDisplay">R$ 0,00</td>
                            </tr>
                            <tr>
                                <td>Frete:</td>
                                <td class="text-end"><input type="text" name="shipping_cost"
                                        class="form-control form-control-sm text-end input-price total-field"
                                        value="0,00"></td>
                            </tr>
                            <tr>
                                <td>Desconto (-):</td>
                                <td class="text-end"><input type="text" name="discount_amount"
                                        class="form-control form-control-sm text-end input-price total-field"
                                        value="0,00"></td>
                            </tr>
                            <tr>
                                <td>Acréscimos (+):</td>
                                <td class="text-end"><input type="text" name="adjustment_amount"
                                        class="form-control form-control-sm text-end input-price total-field"
                                        value="0,00"></td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    <textarea name="adjustment_notes" class="form-control form-control-sm" rows="1"
                                        placeholder="Observações sobre ajustes..."></textarea>
                                </td>
                            </tr>
                            <tr class="bg-light">
                                <th class="fs-5">TOTAL A PAGAR:</th>
                                <td class="text-end fs-5 fw-bold" id="grandTotalDisplay">R$ 0,00</td>
                            </tr>
                        </table>
                        <hr>
                        <div class="p-2 rounded" style="background-color: #f1f6ff;">
                            <h6 class="text-center text-secondary">Análise de Lucratividade da Venda</h6>
                            <table class="table table-sm table-borderless">
                                <tr>
                                    <td>Receita (Total da Venda):</td>
                                    <td class="text-end" id="profit-revenue">R$ 0,00</td>
                                </tr>
                                <tr>
                                    <td>(-) Custo dos Produtos (CMV):</td>
                                    <td class="text-end text-danger" id="profit-products-cost">R$ 0,00</td>
                                </tr>
                                <tr>
                                    <td>(-) Custo dos Itens Adicionais:</td>
                                    <td class="text-end text-danger" id="profit-misc-cost">R$ 0,00</td>
                                </tr>
                                <tr class="bg-light">
                                    <th class="border-top">Lucro Bruto:</th>
                                    <td class="text-end fw-bold border-top" id="profit-gross">R$ 0,00</td>
                                </tr>
                            </table>
                        </div>
                        <hr>
                        <div class="form-group"><label>Forma de Pagamento</label><select name="payment_method"
                                class="form-control select2" required>
                                <option value="PIX">PIX</option>
                                <option value="Cartão de Crédito">Cartão de Crédito</option>
                                <option value="Cartão de Débito">Cartão de Débito</option>
                                <option value="Dinheiro">Dinheiro</option>
                                <option value="Outro">Outro</option>
                            </select></div>
                        <div class="form-group mt-2"><label>Status da Venda</label><select name="status"
                                class="form-control select2" required>
                                <option value="Concluído">Concluído (Dar baixa no estoque)</option>
                                <option value="Pendente">Pendente</option>
                                <option value="Cancelado">Cancelado</option>
                            </select></div>
                    </div>
                    <div class="card-footer">
                        <button type="submit" class="btn btn-success w-100 fw-bold">Finalizar e Registrar Venda</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <script>
        $(function() {
            // --- SETUP, FUNÇÕES GLOBAIS ---
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });
            let saleProducts = [],
                saleMiscCosts = [];
            const formatCurrency = v => !isNaN(parseFloat(v)) ? v.toLocaleString('pt-BR', {
                style: 'currency',
                currency: 'BRL'
            }) : 'R$ 0,00';
            const parseNumber = v => parseFloat(String(v).replace(/\./g, '').replace(',', '.')) || 0;
            const handleAjaxError = xhr => {
                /* ... */
            };

            // --- INICIALIZAÇÃO DE PLUGINS ---
            $('.select2').select2({
                theme: 'bootstrap-5'
            });
            $('.input-price').mask('000.000.000,00', {
                reverse: true
            });
            $('#product_search').select2({
                theme: 'bootstrap-5',
                placeholder: 'Digite para buscar um produto...',
                ajax: {
                    url: "{{ route('admin.sales.search-products') }}",
                    dataType: 'json',
                    delay: 250,
                    processResults: data => ({
                        results: data.results // já está no formato optgroup
                    }),
                    cache: true
                }
            });
            $('#miscItemSelector').select2({
                theme: 'bootstrap-5',
                placeholder: 'Selecione um item...'
            });

            // --- LÓGICA DE ATUALIZAÇÃO E RENDERIZAÇÃO ---
            function renderTablesAndTotals() {
                // Renderiza tabela de produtos
                let productsHtml = '';
                if (saleProducts.length > 0) {
                    saleProducts.forEach((item, index) => {
                        productsHtml += `
                            <tr data-index="${index}">
                                <td>
                                ${item.name}<br>
                                <small class="text-muted">Estoque: ${item.stock}</small>
                                </td>
                                <td>
                                <input
                                    type="number"
                                    class="form-control form-control-sm item-quantity"
                                    value="${item.quantity}"
                                    min="1" max="${item.stock}"
                                >
                                </td>
                                <td class="text-end item-cost">
                                ${formatCurrency(item.cost_per_unit)}
                                </td>
                                <td>
                                <input
                                    type="text"
                                    class="form-control form-control-sm input-price item-price"
                                    value="${item.price_per_unit.toFixed(2).replace('.',',')}"
                                >
                                </td>
                                <td class="text-end item-subtotal">
                                ${formatCurrency(item.quantity * item.price_per_unit)}
                                </td>
                                <td>
                                <button type="button" class="btn btn-sm btn-danger remove-product-btn">
                                    <i class="fas fa-trash"></i>
                                </button>
                                </td>
                            </tr>`;
                    });
                } else {
                    productsHtml =
                        '<tr><td colspan="5" class="text-center text-muted">Nenhum produto adicionado.</td></tr>';
                }
                $('#saleProductsTbody').html(productsHtml).find('.input-price').mask('000.000.000,00', {
                    reverse: true
                });

                // Renderiza tabela de custos diversos
                let miscCostsHtml = '';
                if (saleMiscCosts.length > 0) {
                    saleMiscCosts.forEach((item, index) => {
                        miscCostsHtml +=
                            `<tr data-index="${index}"><td>${item.name}<br><small class="text-muted">Estoque: ${item.stock}</small></td><td><input type="number" class="form-control form-control-sm misc-quantity" value="${item.quantity}" min="1" max="${item.stock}"></td><td class="text-end">${formatCurrency(item.cost_per_unit)}</td><td class="text-end misc-subtotal">${formatCurrency(item.quantity * item.cost_per_unit)}</td><td><button type="button" class="btn btn-sm btn-danger remove-misc-btn"><i class="fas fa-trash"></i></button></td></tr>`;
                    });
                } else {
                    miscCostsHtml =
                        '<tr><td colspan="5" class="text-center text-muted">Nenhum custo adicional.</td></tr>';
                }
                $('#saleMiscCostsTbody').html(miscCostsHtml);

                updateTotals();
            }

            function updateTotals() {
                const productsSubtotal = saleProducts.reduce((acc, item) => acc + (item.quantity * item
                    .price_per_unit), 0);
                const productsCost = saleProducts.reduce((acc, item) => acc + (item.quantity * item.cost_per_unit),
                    0);
                const miscTotalCost = saleMiscCosts.reduce((acc, item) => acc + (item.quantity * item
                    .cost_per_unit), 0);

                const shipping = parseNumber($('input[name="shipping_cost"]').val());
                const discount = parseNumber($('input[name="discount_amount"]').val());
                const adjustment = parseNumber($('input[name="adjustment_amount"]').val());

                const grandTotal = productsSubtotal + shipping - discount + adjustment;
                const revenueForProfit = productsSubtotal - discount + adjustment;
                const grossProfit = revenueForProfit - productsCost - miscTotalCost;

                $('#subtotalDisplay').text(formatCurrency(productsSubtotal));
                $('#grandTotalDisplay').text(formatCurrency(grandTotal));
                $('#profit-revenue').text(formatCurrency(grandTotal));
                $('#profit-products-cost').text(formatCurrency(productsCost));
                $('#profit-misc-cost').text(formatCurrency(miscTotalCost));
                $('#profit-gross').text(formatCurrency(grossProfit));
            }

            // --- EVENTOS DE ADIÇÃO E REMOÇÃO ---
            $('#product_search').on('select2:select', function(e) {
                const p = e.params.data;
                if (saleProducts.find(i => i.product_id == p.id)) {
                    Swal.fire('Atenção!', 'Este produto já foi adicionado.', 'warning');
                    return;
                }
                if (p.quantity_on_hand <= 0) {
                    Swal.fire('Estoque Insuficiente!',
                        `O produto "${p.text.split(' (')[0]}" não tem estoque.`, 'error');
                    return;
                }
                saleProducts.push({
                    product_id: p.id,
                    name: p.text.split(' (')[0],
                    quantity: 1,
                    price_per_unit: parseFloat(p.sale_price) || 0,
                    cost_per_unit: parseFloat(p.average_cost) || 0,
                    stock: p.quantity_on_hand
                });
                renderTablesAndTotals();
                $(this).val(null).trigger('change');
            });

            $('#miscCategorySelector').on('change', function() {
                const catId = $(this).val();
                const selector = $('#miscItemSelector');
                selector.empty().prop('disabled', true).trigger('change');
                if (!catId) return;
                $.get("{{ route('admin.sales.search-misc-items') }}", {
                    category_id: catId
                }).done(response => {
                    selector.append('<option value="">Selecione o item...</option>');
                    $.each(response.results, function(index, item) {
                        selector.append(
                            `<option 
     value="${item.id}" 
     data-cost="${item.average_cost}" 
     data-stock="${item.quantity_on_hand}"
   >
     ${item.text} (Estoque: ${item.quantity_on_hand})
   </option>`
                        );
                    });
                    selector.prop('disabled', false).trigger('change');
                });
            });

            $('#addMiscItemBtn').on('click', function() {
                const opt = $('#miscItemSelector').find('option:selected');
                const qty = parseInt($('#miscItemQty').val());
                if (!opt.val() || isNaN(qty) || qty <= 0) {
                    Swal.fire('Atenção', 'Selecione um item e a quantidade.', 'warning');
                    return;
                }
                if (saleMiscCosts.find(i => i.misc_item_id == opt.val())) {
                    Swal.fire('Atenção!', 'Este item já foi adicionado.', 'warning');
                    return;
                }
                const stock = parseInt(opt.data('stock'));
                if (qty > stock) {
                    Swal.fire('Estoque Insuficiente', `Disponível: ${stock}`, 'error');
                    return;
                }
                saleMiscCosts.push({
                    misc_item_id: opt.val(),
                    name: opt.text(),
                    quantity: qty,
                    cost_per_unit: parseFloat(opt.data('cost')) || 0,
                    stock: stock
                });
                renderTablesAndTotals();
                $('#miscCategorySelector, #miscItemSelector').val(null).trigger('change');
                $('#miscItemQty').val(1);
            });

            $('#saleProductsTbody').on('input', '.item-quantity, .item-price', function() {
                const row = $(this).closest('tr');
                const index = row.data('index');
                const item = saleProducts[index];
                let newQuantity = parseInt(row.find('.item-quantity').val()) || 1;

                if (newQuantity > item.stock) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Limite de Estoque!',
                        text: `A quantidade não pode ser maior que o estoque disponível (${item.stock}).`
                    });
                    newQuantity = item.stock;
                    row.find('.item-quantity').val(newQuantity);
                }

                item.quantity = newQuantity;
                item.price_per_unit = parseNumber(row.find('.item-price').val());

                const itemSubtotal = item.quantity * item.price_per_unit;
                row.find('.item-subtotal').text(formatCurrency(itemSubtotal));
                updateTotals();
            });

            $('#saleMiscCostsTbody').on('input', '.misc-quantity', function() {
                const row = $(this).closest('tr');
                const index = row.data('index');
                const item = saleMiscCosts[index];
                let newQuantity = parseInt($(this).val()) || 1;

                if (newQuantity > item.stock) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Limite de Estoque!',
                        text: `A quantidade não pode ser maior que o estoque disponível (${item.stock}).`
                    });
                    newQuantity = item.stock;
                    $(this).val(newQuantity);
                }

                item.quantity = newQuantity;
                const itemSubtotal = item.quantity * item.cost_per_unit;
                row.find('.misc-subtotal').text(formatCurrency(itemSubtotal));
                updateTotals();
            });
            $('#saleProductsTbody').on('click', '.remove-product-btn', function() {
                saleProducts.splice($(this).closest('tr').data('index'), 1);
                renderTablesAndTotals();
            });
            $('#saleMiscCostsTbody').on('click', '.remove-misc-btn', function() {
                saleMiscCosts.splice($(this).closest('tr').data('index'), 1);
                renderTablesAndTotals();
            });
            $('.total-field').on('input', updateTotals);

            // --- SUBMISSÃO FINAL ---
            $('#saleForm').on('submit', function(e) {
                e.preventDefault();

                // 1) Antes de enviar, garante que as quantidades e preços estão atualizados em saleProducts e saleMiscCosts
                $('#saleProductsTbody tr[data-index]').each(function() {
                    const idx = $(this).data('index');
                    const qty = parseInt($(this).find('.item-quantity').val(), 10) || 0;
                    const price = parseNumber($(this).find('.item-price').val());
                    saleProducts[idx].quantity = qty;
                    saleProducts[idx].price_per_unit = price;
                });
                $('#saleMiscCostsTbody tr[data-index]').each(function() {
                    const idx = $(this).data('index');
                    const qty = parseInt($(this).find('.misc-quantity').val(), 10) || 0;
                    saleMiscCosts[idx].quantity = qty;
                });
                updateTotals();

                // 2) Monta payload
                const payload = {
                    client_id: $('select[name="client_id"]').val() || null,
                    order_date: $('input[name="order_date"]').val(),
                    shipping_cost: parseNumber($('input[name="shipping_cost"]').val()),
                    discount_amount: parseNumber($('input[name="discount_amount"]').val()),
                    adjustment_amount: parseNumber($('input[name="adjustment_amount"]').val()),
                    adjustment_notes: $('textarea[name="adjustment_notes"]').val(),
                    payment_method: $('select[name="payment_method"]').val(),
                    status: $('select[name="status"]').val(),
                    products: saleProducts.map(i => ({
                        product_id: i.product_id,
                        quantity: i.quantity,
                        price_per_unit: i.price_per_unit,
                        cost_per_unit: i.cost_per_unit
                    })),
                    misc_costs: saleMiscCosts.map(i => ({
                        misc_item_id: i.misc_item_id,
                        quantity: i.quantity
                    }))
                };

                // 3) Envia por AJAX
                $.ajax({
                    url: "{{ route('admin.sales.store') }}",
                    type: 'POST',
                    data: payload,
                    success(response) {
                        Swal.fire('Sucesso', response.success, 'success')
                            .then(() => {
                                if (response.redirect_url) {
                                    window.location.href = response.redirect_url;
                                }
                            });
                    },
                    error(xhr) {
                        if (xhr.status === 422) {
                            // validação de campos
                            const errors = xhr.responseJSON.errors;
                            let html = '<ul class="text-left">';
                            $.each(errors, (field, msgs) => {
                                msgs.forEach(msg => html += `<li>${msg}</li>`);
                            });
                            html += '</ul>';
                            Swal.fire({
                                title: 'Erros de validação',
                                html,
                                icon: 'warning'
                            });
                        } else {
                            Swal.fire('Erro', 'Ocorreu um problema ao registrar a venda.', 'error');
                        }
                    }
                });
            });
        });
    </script>
@endpush
