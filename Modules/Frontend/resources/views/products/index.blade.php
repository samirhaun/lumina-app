{{-- Modules/Frontend/resources/views/products/index.blade.php --}}
@extends('frontend::layouts.layout')

@section('title', 'Catálogo de Produtos')

@section('content')
    <div class="container my-5">
        {{-- Grade de Produtos --}}
        <div class="row g-4">
            @foreach ($products as $p)
                @php
                    // primeira imagem (ou placeholder)
                    $img =
                        optional($imagesByProduct[$p->id] ?? null)->first()->image_url ??
                        asset('images/placeholder.png');
                @endphp
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card product-card h-100 shadow-sm">
                        <a href="{{ route('frontend.products.show', $p->id) }}" class="card-img-link">
                            <img src="{{ $img }}" class="card-img-top" style="height:260px; object-fit:cover;"
                                alt="{{ $p->name }}">
                        </a>
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title mb-2">
                                <a href="{{ route('frontend.products.show', $p->id) }}"
                                    class="stretched-link text-dark text-decoration-none">
                                    {{ \Illuminate\Support\Str::limit($p->name, 30) }}
                                </a>
                            </h6>
                            <p class="card-price mb-4 text-terracota">
                                R$ {{ number_format($p->sale_price, 2, ',', '.') }}
                            </p>
                            <button class="btn btn-outline-terracota mt-auto btn-view-more" data-id="{{ $p->id }}">
                                <i class="fas fa-eye me-1"></i> Ver mais
                            </button>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        $(function() {
            $('.btn-add-to-cart').on('click', function() {
                let btn = $(this),
                    id = btn.data('id'),
                    orig = btn.html();
                btn.prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm"></span>');
                $.post("{{ route('cart.add') }}", {
                    _token: '{{ csrf_token() }}',
                    product_id: id
                }).done(res => {
                    $('#cart-count').text(res.cartCount).toggle(res.cartCount > 0);
                    Swal.fire({
                        icon: 'success',
                        title: 'Adicionado!',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 1500
                    });
                }).fail(() => {
                    Swal.fire('Erro!', 'Não foi possível adicionar.', 'error');
                }).always(() => {
                    btn.prop('disabled', false).html(orig);
                });
            });
        });
    </script>
@endpush
