{{-- resources/views/frontend/products/show.blade.php --}}
@extends('frontend::layouts.layout')

@section('title', $product->name)

@push('styles')
    <style>
        :root {
            --terracota: #A0522D;
            --cinza-claro: #F5F5F5;
        }

        /* =====================
                                                       Breadcrumb customizado
                                                       ===================== */
        .breadcrumb {
            background: transparent;
            padding: 0;
            margin-bottom: 2rem;
        }

        .breadcrumb .breadcrumb-item+.breadcrumb-item::before {
            content: '›';
            color: #AAA;
            margin: 0 .5rem;
        }

        .breadcrumb .breadcrumb-item a {
            color: var(--terracota);
            text-decoration: none;
            transition: color .2s;
        }

        .breadcrumb .breadcrumb-item a:hover {
            color: #8A3F1A;
        }

        .breadcrumb .breadcrumb-item.active {
            color: #333;
            font-weight: 600;
        }

        /* =====================
                                                       Painel de informações
                                                       ===================== */
        .info-panel {
            background: #FFF;
            border-radius: .5rem;
            box-shadow: 0 8px 24px rgba(0, 0, 0, 0.05);
            padding: 2rem;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .info-panel h3 {
            font-family: var(--ff-head);
            font-size: 1.75rem;
            margin-bottom: 1rem;
            color: #333;
        }

        .info-panel h2.price {
            font-family: var(--ff-head);
            font-size: 2rem;
            color: var(--terracota);
            margin-bottom: 1rem;
        }

        .info-panel p.short-desc {
            color: #555;
            margin-bottom: 1.5rem;
            flex: none;
        }

        .info-panel .installments {
            font-size: .9rem;
            color: #555;
            margin-bottom: 1.5rem;
        }

        .info-panel .installments span {
            color: var(--terracota);
            font-weight: 600;
        }

        .info-panel .payments i {
            font-size: 1.75rem;
            margin-right: .75rem;
            color: #333;
        }

        .info-panel .payments a {
            display: block;
            font-size: .85rem;
            margin-top: .5rem;
            color: #444;
        }

        .info-panel .d-flex.mb-4 input {
            width: 4.5rem;
        }

        .info-panel .d-flex.mb-4 .btn-buy {
            background: var(--terracota);
            color: #fff;
            border: none;
            padding: .75rem 1.5rem;
            border-radius: .5rem;
            flex: none;
        }

        .info-panel .share {
            margin-bottom: 1.5rem;
        }

        .info-panel .share a {
            margin-right: 1rem;
            color: #666;
            transition: color .2s;
        }

        .info-panel .share a:hover {
            color: var(--terracota);
        }

        .info-panel h6 {
            font-size: 1rem;
            margin-bottom: .75rem;
            font-weight: 600;
        }

        .info-panel ul.specs {
            list-style: disc inside;
            font-size: .9rem;
            color: #555;
            padding-left: .5rem;
            margin: 0;
        }

        /* =====================
                                                       Galeria & thumbnails
                                                       ===================== */
        .main-image-wrapper {
            position: relative;
            overflow: hidden;
            border-radius: .5rem;
            background: var(--cinza-claro);
        }

        .carousel-fade .carousel-item {
            transition: opacity .8s ease-in-out;
        }

        .carousel-control-icon-circle {
            width: 3rem;
            height: 3rem;
            background: #FFF;
            border: 2px solid var(--terracota);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background .2s;
        }

        .carousel-control-icon-circle i {
            color: var(--terracota);
        }

        .carousel-control-prev:hover .carousel-control-icon-circle,
        .carousel-control-next:hover .carousel-control-icon-circle {
            background: var(--terracota);
        }

        .carousel-control-prev:hover .carousel-control-icon-circle i,
        .carousel-control-next:hover .carousel-control-icon-circle i {
            color: #FFF;
        }

        .gallery-thumbnails img {
            cursor: pointer;
            opacity: .7;
            transition: opacity .2s, border-color .2s;
            border: 2px solid transparent;
            border-radius: .25rem;
        }

        .gallery-thumbnails img.active,
        .gallery-thumbnails img:hover {
            opacity: 1;
            border-color: var(--terracota);
        }

        /* ===================================
                           NOVO SELETOR DE QUANTIDADE (STEPPER)
                           =================================== */

        .quantity-selector {
            display: flex;
            align-items: center;
            border: 1px solid #ced4da;
            /* Borda cinza claro, como a do Bootstrap */
            border-radius: .375rem;
            /* Mesma borda arredondada dos inputs */
        }

        .quantity-selector .btn-qty {
            background-color: transparent;
            border: none;
            color: var(--terracota);
            font-size: 1.5rem;
            font-weight: 300;
            line-height: 1;
            padding: 0.5rem 1rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .quantity-selector .btn-qty:hover {
            background-color: #f8f9fa;
            /* Um cinza bem clarinho no hover */
        }

        .quantity-selector .qty-input {
            width: 3rem;
            /* Largura do campo do número */
            text-align: center;
            border: none;
            /* Remove a borda do input */
            background-color: transparent;
            font-size: 1.1rem;
            font-weight: 500;
            /* Remove as setas padrão do input number */
            -moz-appearance: textfield;
        }

        .quantity-selector .qty-input::-webkit-outer-spin-button,
        .quantity-selector .qty-input::-webkit-inner-spin-button {
            -webkit-appearance: none;
            margin: 0;
        }


        /* ===================================
               AJUSTE DE POSIÇÃO PARA A NOTIFICAÇÃO
               =================================== */
        .custom-swal-toast {
            margin-top: 5rem;
            /* Empurra a notificação 5rem para baixo */
        }
    </style>
@endpush

@section('content')
    <main class="container py-5">
        {{-- Breadcrumb --}}
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Início</a></li>
                <li class="breadcrumb-item">
                    <a href="{{ route('frontend.products.index', ['type' => $product->product_type_id]) }}">
                        {{ $product->type_name }}
                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
            </ol>
        </nav>

        <div class="row gx-5">
            {{-- GALERIA --}}
            <div class="col-lg-6">
                <div id="productGallery" class="carousel slide carousel-fade main-image-wrapper" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        @forelse($productImages as $img)
                            <div class="carousel-item @if ($loop->first) active @endif">
                                <img src="{{ $img->image_url }}" class="d-block w-100" alt="Foto {{ $loop->iteration }}">
                            </div>
                        @empty
                            <div class="carousel-item active">
                                <img src="{{ asset('images/placeholder.png') }}" class="d-block w-100" alt="Sem imagem">
                            </div>
                        @endforelse
                    </div>
                    @if ($productImages->count() > 1)
                        <button class="carousel-control-prev" type="button" data-bs-target="#productGallery"
                            data-bs-slide="prev">
                            <span class="carousel-control-icon-circle">
                                <i class="fas fa-chevron-left"></i>
                            </span>
                            <span class="visually-hidden">Anterior</span>
                        </button>
                        <button class="carousel-control-next" type="button" data-bs-target="#productGallery"
                            data-bs-slide="next">
                            <span class="carousel-control-icon-circle">
                                <i class="fas fa-chevron-right"></i>
                            </span>
                            <span class="visually-hidden">Próximo</span>
                        </button>
                    @endif
                </div>

                @if ($productImages->count() > 1)
                    <div class="row gallery-thumbnails gx-2 mt-3">
                        @foreach ($productImages as $img)
                            <div class="col-3 p-1">
                                <img src="{{ $img->image_url }}" data-bs-target="#productGallery"
                                    data-bs-slide-to="{{ $loop->index }}"
                                    class="img-fluid thumbnail-image @if ($loop->first) active @endif"
                                    alt="Miniatura {{ $loop->iteration }}">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- PAINEL DE INFORMAÇÕES --}}
            <div class="col-lg-6">
                <div class="info-panel">
                    {{-- Nome do produto dentro do card --}}
                    <h3>{{ $product->name }}</h3>

                    @if ($product->code)
                        <div class="sku-badge">
                            <i class="fas fa-barcode"></i>
                            <span>Cód: {{ $product->code }}</span>
                        </div>
                    @endif

                    {{-- Preço --}}
                    <h2 class="price">R$ {{ number_format($product->sale_price, 2, ',', '.') }}</h2>

                    {{-- Parcelamento --}}
                    @php $inst = $product->sale_price/3; @endphp
                    <div class="installments">
                        ou 3× de <span>R${{ number_format($inst, 2, ',', '.') }}</span> sem juros
                    </div>

                    {{-- Descrição curta --}}
                    <p class="short-desc">
                        @php
                            // Apenas decodifica entidades e remove tags
                            $cleanDescription = html_entity_decode(
                                strip_tags($product->description ?? 'Nenhuma descrição disponível.'),
                            );
                        @endphp
                        {{ $cleanDescription }}
                    </p>


                    {{-- Meios de pagamento --}}
                    <div class="payments mb-4">
                        <i class="fab fa-cc-visa"></i>
                        <i class="fab fa-cc-mastercard"></i>
                        <i class="fab fa-cc-amex"></i>
                        <a href="#" class="small">Ver meios de pagamento</a>
                    </div>

                    {{-- Quantidade + Adicionar ao Carrinho --}}
                    <div class="d-grid gap-3 mb-4"> {{-- Usamos d-grid para empilhar os elementos --}}
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <label for="quantity" class="form-label mb-0">Quantidade:</label>
                            </div>
                            <div class="col-auto">
                                {{-- Nosso novo componente "Stepper" --}}
                                <div class="quantity-selector">
                                    <button type="button" class="btn-qty btn-minus"
                                        aria-label="Diminuir quantidade">-</button>
                                    <input type="text" id="quantity" class="form-control qty-input" value="1"
                                        min="1" readonly>
                                    <button type="button" class="btn-qty btn-plus"
                                        aria-label="Aumentar quantidade">+</button>
                                </div>
                            </div>
                        </div>
                        <div class="d-grid"> {{-- d-grid para o botão ocupar 100% da largura --}}
                            <button class="btn btn-terracota btn-lg btn-add-to-cart" data-id="{{ $product->id }}">
                                <i class="fas fa-shopping-cart me-2"></i>Adicionar ao Carrinho
                            </button>
                        </div>
                    </div>

                    {{-- Compartilhar --}}
                    <div class="share mb-4">
                        <span class="me-2">Compartilhe:</span>
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-pinterest"></i></a>
                    </div>

                    {{-- Especificações --}}
                    <div>
                        <h6>Especificações</h6>
                        {{-- A sintaxe {!! !!} renderiza o HTML salvo pelo editor TinyMCE --}}
                        {!! $product->specifications ?? '<ul><li>Nenhuma especificação disponível.</li></ul>' !!}
                    </div>
                </div>
            </div>
        </div>
    </main>
@endsection

@push('scripts')
    <script>
        // thumbnail → slide
        document.querySelectorAll('.thumbnail-image').forEach(img => {
            img.addEventListener('click', e => {
                let idx = e.currentTarget.dataset.bsSlideTo;
                bootstrap.Carousel.getInstance('#productGallery').to(idx);
                document.querySelectorAll('.thumbnail-image').forEach(i => i.classList.remove('active'));
                e.currentTarget.classList.add('active');
            });
        });

        // AJAX add to cart
        $('.btn-add-to-cart').click(function() {
            let btn = $(this),
                id = btn.data('id'),
                qty = $('#quantity').val(),
                html = btn.html();
            btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
            $.post("{{ route('cart.add') }}", {
                    _token: '{{ csrf_token() }}',
                    product_id: id,
                    quantity: qty
                })
                .done(res => {
                    // Atualiza o contador do carrinho no cabeçalho
                    $('#cart-count').text(res.cartCount).toggle(res.cartCount > 0);

                    // Dispara a notificação de sucesso
                    Swal.fire({
                        // NOVO: Adiciona a mensagem de sucesso que vem do controller
                        title: res.success,

                        icon: 'success',
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000, // Aumentei um pouco o tempo para dar para ler

                        // NOVO: Adiciona uma barra de progresso para o timer
                        timerProgressBar: true,

                        // NOVO: Adiciona uma classe CSS para podermos ajustar a posição
                        customClass: {
                            popup: 'custom-swal-toast'
                        }
                    });
                }).fail(() => Swal.fire('Erro!', 'Não foi possível adicionar.', 'error'))
                .always(() => btn.prop('disabled', false).html(html));
        });

        // --- LÓGICA DO SELETOR DE QUANTIDADE (STEPPER) - VERSÃO CORRIGIDA ---
        $('.quantity-selector').on('click', '.btn-plus', function() {
            // Abordagem mais robusta: sobe para o container pai e depois encontra o input
            let input = $(this).closest('.quantity-selector').find('.qty-input');
            let currentValue = parseInt(input.val());
            input.val(currentValue + 1);
        });

        $('.quantity-selector').on('click', '.btn-minus', function() {
            // Abordagem mais robusta: sobe para o container pai e depois encontra o input
            let input = $(this).closest('.quantity-selector').find('.qty-input');
            let currentValue = parseInt(input.val());
            // Impede que a quantidade seja menor que 1
            if (currentValue > 1) {
                input.val(currentValue - 1);
            }
        });
    </script>
@endpush
