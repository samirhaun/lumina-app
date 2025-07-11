@extends('frontend::layouts.layout')

@section('title', 'Página Inicial')

@section('content')

    {{-- Hero full-width --}}
    <div class="container-fluid px-0">
        <section id="heroCarousel" class="carousel slide hero-section" data-bs-ride="carousel">

            {{-- 1) Carousel Indicators --}}
            <div class="carousel-indicators">
                @foreach ($heroes as $i => $hero)
                    <button type="button" data-bs-target="#heroCarousel" data-bs-slide-to="{{ $i }}"
                        class="{{ $i === 0 ? 'active' : '' }}" aria-current="{{ $i === 0 ? 'true' : '' }}"
                        aria-label="Slide {{ $i + 1 }}">
                    </button>
                @endforeach
            </div>

            {{-- 2) Slides --}}
            <div class="carousel-inner">
                @forelse($heroes as $i => $hero)
                    <div class="carousel-item {{ $i === 0 ? 'active' : '' }}">
                        <a href="{{ $hero->link_url ?: '#' }}">
                            <img src="{{ Storage::url($hero->image_path) }}" class="d-block w-100"
                                alt="Banner {{ $i + 1 }}">
                        </a>
                    </div>
                @empty
                    <div class="carousel-item active">
                        <img src="{{ asset('images/default-banner.png') }}" class="d-block w-100" alt="Banner padrão">
                    </div>
                @endforelse
            </div>

            {{-- 3) Controles de Navegação (opcional) --}}
            @if ($heroes->count() > 1)
                <button class="carousel-control-prev" type="button" data-bs-target="#heroCarousel" data-bs-slide="prev">
                    <span class="carousel-control-icon-circle">
                        <i class="fas fa-chevron-left"></i>
                    </span>
                    <span class="visually-hidden">Anterior</span>
                </button>

                <button class="carousel-control-next" type="button" data-bs-target="#heroCarousel" data-bs-slide="next">
                    <span class="carousel-control-icon-circle">
                        <i class="fas fa-chevron-right"></i>
                    </span>
                    <span class="visually-hidden">Próximo</span>
                </button>
            @endif

        </section>
    </div>

    {{-- Benefícios --}}
    <section class="benefits bg-white py-4 mt-5">
        <div class="container">
            <div class="row text-center align-items-center">

                {{-- Frete Grátis --}}
                <div class="col-md-4 d-flex align-items-center justify-content-center py-2">
                    <i class="fas fa-truck fa-2x me-3 text-terracota"></i>
                    <div>
                        <strong>Entrega Grátis</strong><br>
                        <small>em compras a partir de R$ 249,99</small>
                    </div>
                </div>

                {{-- Pague como preferir --}}
                <div class="col-md-4 py-2">
                    <div
                        class="d-flex align-items-center justify-content-center
                    border-start border-end h-100 px-4">
                        <i class="fas fa-credit-card fa-2x me-3 text-terracota"></i>
                        <div>
                            <strong>Pague como preferir!</strong><br>
                            <small>Pix ou Cartão em até 12× ou 3× sem juros</small>
                        </div>
                    </div>
                </div>

                {{-- Dúvidas? --}}
                <div class="col-md-4 d-flex align-items-center justify-content-center py-2">
                    <i class="fab fa-whatsapp fa-2x me-3 text-terracota"></i>
                    <div>
                        <strong>Dúvidas?</strong><br>
                        <small>
                            <a href="https://w.app/vlphga" target="_blank" class="text-decoration-none text-terracota">
                                Chama a gente no WhatsApp
                            </a>
                        </small>
                    </div>
                </div>

            </div>
        </div>
    </section>

{{-- Todos os Produtos --}}
{{-- Todos os Produtos --}}
<section id="todos-produtos" class="container py-5">
    <h2 class="section-title section-title-alt">Todos os Produtos</h2>
    <div class="row g-4 justify-content-center">
        @foreach ($products as $p)
            @php
                $productImagesCollection = $imagesByProduct[$p->id] ?? collect();

                // --- CORREÇÃO APLICADA AQUI ---
                // Trocamos Storage::url($path) por asset($path)
                // A função 'asset()' montará a URL corretamente sem duplicar o '/storage/'.
                $allImageUrls = $productImagesCollection->pluck('image_url')->map(fn($path) => asset($path));

                $primaryImgUrl = $allImageUrls->first() ?? asset('images/placeholder.png');
            @endphp
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card product-card-v2 h-100" data-images='{{ $allImageUrls->toJson() }}'>
                    <div class="card-top-border"></div>

                    <a href="{{ route('frontend.products.show', $p->id) }}" class="card-img-link">
                        <img src="{{ $primaryImgUrl }}" class="card-img-top" alt="{{ $p->name }}">
                    </a>
                    <div class="card-body d-flex flex-column text-center">
                        <h5 class="card-title">
                            <a href="{{ route('frontend.products.show', $p->id) }}"
                                class="stretched-link text-dark text-decoration-none">
                                {{ \Illuminate\Support\Str::limit($p->name, 40) }}
                            </a>
                        </h5>

                        @if($p->code)
                            <p class="product-code">Cód: {{ $p->code }}</p>
                        @endif

                        <p class="card-price">
                            R$ {{ number_format($p->sale_price, 2, ',', '.') }}
                        </p>

                        <div class="installment-box">
                            3x de R$ {{ number_format($p->sale_price / 3, 2, ',', '.') }} sem juros
                        </div>

                        <a href="{{ route('frontend.products.show', $p->id) }}" class="btn btn-custom-brown mt-auto">
                            VER MAIS
                        </a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
</section>
@endsection

@push('scripts')
    <script>
        $(function() {
            $(document).on('click', '.btn-add-to-cart', function(e) {
                e.stopPropagation();
                e.preventDefault();
                let btn = $(this),
                    id = btn.data('id'),
                    orig = btn.html();
                btn.prop('disabled', true).html('<span class="spinner-border spinner-border-sm"></span>');
                $.post("{{ route('cart.add') }}", {
                    _token: '{{ csrf_token() }}',
                    product_id: id
                }).done(res => {
                    window.location.href = "{{ route('cart.index') }}";
                }).fail(() => {
                    Swal.fire('Erro!', 'Não foi possível adicionar.', 'error');
                }).always(() => {
                    btn.prop('disabled', false).html(orig);
                });
            });
        });
    </script>
    @push('scripts')
    <script>
        $(function() {
            // Script de adicionar ao carrinho (se existir) pode ficar aqui...
            // $(document).on('click', '.btn-add-to-cart', function(e) { ... });

            // --- NOVO SCRIPT PARA TROCA DE IMAGEM NO HOVER ---

            let imageHoverTimeout; // Variável para controlar o tempo do hover

            $('.product-card-v2').on('mouseenter', function() {
                const card = $(this);
                const imageElement = card.find('.card-img-top');
                const allImages = card.data('images');

                // Armazena a imagem original na primeira vez que o mouse passa por cima
                if (!imageElement.data('original-src')) {
                    imageElement.data('original-src', imageElement.attr('src'));
                }

                // Continua somente se houver mais de uma imagem
                if (Array.isArray(allImages) && allImages.length > 1) {

                    // Pega a imagem que está sendo exibida no momento
                    const currentImage = imageElement.attr('src');

                    // Cria uma lista de imagens disponíveis, excluindo a imagem atual
                    let availableImages = allImages.filter(img => img !== currentImage);

                    // Se por algum motivo a lista ficar vazia, reseta com todas as imagens
                    if (availableImages.length === 0) {
                        availableImages = allImages;
                    }

                    // Sorteia uma nova imagem da lista de disponíveis
                    const randomIndex = Math.floor(Math.random() * availableImages.length);
                    const newSrc = availableImages[randomIndex];

                    // Troca a imagem
                    imageElement.attr('src', newSrc);
                }

            }).on('mouseleave', function() {
                const card = $(this);
                const imageElement = card.find('.card-img-top');
                const originalSrc = imageElement.data('original-src');

                // Restaura a imagem original que foi salva
                if (originalSrc) {
                    imageElement.attr('src', originalSrc);
                }
            });
        });
    </script>
@endpush
@endpush
