@if(isset($lowStockItems) && $lowStockItems->count() > 0)
    <ul class="products-list product-list-in-card ps-2 pe-2">
        @foreach($lowStockItems as $item)
        <li class="item">
            <div class="product-info">
                <span class="product-title">{{ $item->name }}</span>
                <span class="badge {{ $item->quantity_on_hand > 0 ? 'bg-warning' : 'bg-danger' }} float-end">
                    {{ $item->quantity_on_hand }} / {{ $item->minimum_stock }}
                </span>
                <span class="product-description">
                    Estoque atual / Mínimo
                </span>
            </div>
        </li>
        @endforeach
    </ul>
@else
    <div class="text-center text-success p-4">
        <i class="fas fa-check-circle fa-2x"></i>
        <p class="mt-2 mb-0">Nenhum item com estoque baixo no momento.</p>
    </div>
@endif