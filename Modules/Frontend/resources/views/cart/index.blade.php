@extends('frontend::layouts.layout')

@section('title', 'Carrinho de Compras')

@section('content')
<div class="container py-5">
    <h2>Seu Carrinho de Compras</h2>
    <hr>
    @if ($cartItems->count() > 0)
        <div class="row">
            {{-- Coluna dos Itens do Carrinho --}}
            <div class="col-lg-8">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th scope="col" class="border-0">
                                    <div class="p-2 px-3 text-uppercase">Produto</div>
                                </th>
                                <th scope="col" class="border-0">
                                    <div class="py-2 text-uppercase">Preço</div>
                                </th>
                                <th scope="col" class="border-0">
                                    <div class="py-2 text-uppercase">Quantidade</div>
                                </th>
                                <th scope="col" class="border-0">
                                    <div class="py-2 text-uppercase">Subtotal</div>
                                </th>
                                <th scope="col" class="border-0">
                                    <div class="py-2 text-uppercase">Remover</div>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($cartItems as $item)
                                <tr id="row-{{ $item->rowId }}">
                                    <th scope="row">
                                        <div class="p-2">
                                            <div class="ms-3 d-inline-block align-middle">
                                                <h5 class="mb-0">{{ $item->name }}</h5>
                                                <span class="text-muted font-weight-normal font-italic d-block">Código: {{ $item->options->code ?? 'N/A' }}</span>
                                            </div>
                                        </div>
                                    </th>
                                    <td class="align-middle"><strong>R$ {{ number_format($item->price, 2, ',', '.') }}</strong></td>
                                    <td class="align-middle">
                                        <div class="d-flex align-items-center">
                                            <input type="number" class="form-control form-control-sm text-center item-quantity-input" value="{{ $item->qty }}" min="1" data-rowid="{{ $item->rowId }}" style="width: 70px;">
                                        </div>
                                    </td>
                                    <td class="align-middle item-subtotal"><strong>R$ {{ number_format($item->subtotal, 2, ',', '.') }}</strong></td>
                                    <td class="align-middle">
                                        <button class="btn btn-outline-danger btn-sm remove-from-cart-btn" data-rowid="{{ $item->rowId }}">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Coluna do Resumo do Pedido --}}
            <div class="col-lg-4">
                <div class="card bg-light rounded-lg p-4">
                    <h3 class="font-weight-bold text-center">Resumo do Pedido</h3>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <span>Subtotal</span>
                        <strong id="cart-subtotal">R$ {{ $subtotal }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span>Frete</span>
                        <strong>A calcular</strong>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total</span>
                        <strong id="cart-total">R$ {{ $subtotal }}</strong>
                    </div>
                    <hr>
                    <a href="{{ route('frontend.checkout.index') }}" class="btn btn-primary w-100">Finalizar Compra</a>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-5">
            <i class="fas fa-shopping-cart fa-4x text-muted mb-3"></i>
            <h3>Seu carrinho está vazio.</h3>
            <p class="text-muted">Adicione produtos da nossa loja para continuar.</p>
            <a href="{{ route('frontend.home') }}" class="btn btn-primary mt-3">Voltar para a Loja</a>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
$(function () {
    $.ajaxSetup({ headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' } });

    function updateCart(rowId, quantity, button) {
        const url = `{{ url('carrinho/atualizar') }}/${rowId}`;
        $.post(url, { quantity: quantity })
            .done(response => {
                // Atualiza o subtotal da linha e o total geral
                $(`#row-${rowId}`).find('.item-subtotal strong').text('R$ ' + response.itemSubtotal);
                $('#cart-subtotal, #cart-total').text('R$ ' + response.subtotal);
                $('#cart-count').text(response.cartCount);
                if (button) button.prop('disabled', false);
            })
            .fail(() => alert('Erro ao atualizar o carrinho.'));
    }

    // Atualiza a quantidade quando o valor do input muda
    $('.item-quantity-input').on('change', function() {
        const input = $(this);
        updateCart(input.data('rowid'), input.val());
    });

    // Remove um item do carrinho
    $('.remove-from-cart-btn').on('click', function(e) {
        e.preventDefault();
        const button = $(this);
        const rowId = button.data('rowid');
        
        button.prop('disabled', true);

        $.get(`{{ url('carrinho/remover') }}/${rowId}`)
            .done(response => {
                $(`#row-${rowId}`).fadeOut(300, function() { $(this).remove(); });
                $('#cart-subtotal, #cart-total').text('R$ ' + response.subtotal);
                
                const cartCount = response.cartCount;
                $('#cart-count').text(cartCount);
                if(cartCount === 0) {
                    location.reload(); // Recarrega a página para mostrar a mensagem de carrinho vazio
                }
            })
            .fail(() => {
                alert('Erro ao remover o item.');
                button.prop('disabled', false);
            });
    });
});
</script>
@endpush