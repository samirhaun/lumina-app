<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lúmina Joias - @yield('title', 'Página Inicial')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="{{ asset('css/store.css') }}"> {{-- CONECTANDO O NOVO CSS --}}

    @stack('styles')
</head>

<body>
    @php
        use Illuminate\Support\Facades\DB;
        // pega o filtro atual
        $currentType = request()->routeIs('frontend.products.index') ? request('type') : null;

        if (request()->routeIs('frontend.products.show')) {
            $prodId = request()->route('id');
            $currentType = DB::table('products')->where('id', $prodId)->value('product_type_id');
        }
    @endphp

    <header class="store-header navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="{{ route('frontend.home') }}">
                <img src="{{ asset('images/logo-terracota.png') }}" alt="Lúmina Joias">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                {{-- === CATEGORIAS NO PRÓPRIO NAVBAR === --}}
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a href="{{ route('frontend.home') }}"
                            class="nav-link {{ is_null($currentType) ? 'active' : '' }}">
                            Início
                        </a>
                    </li>
                    @foreach ($productTypes as $type)
                        <li class="nav-item">
                            <a href="{{ route('frontend.products.index', ['type' => $type->id]) }}"
                                class="nav-link {{ $currentType == $type->id ? 'active' : '' }}">
                                {{ $type->name }}
                            </a>
                        </li>
                    @endforeach
                </ul>

                {{-- CARRINHO --}}
                <div class="d-flex">
                    <a href="{{ route('cart.index') }}" class="btn btn-outline-dark position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="ms-2">Carrinho</span>
                        <span id="cart-count"
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            @if (Cart::count() == 0) style="display:none" @endif>
                            {{ Cart::count() }}
                        </span>
                    </a>
                </div>
            </div>
        </div>
    </header>
    <main class="container">
        @yield('content')
    </main>

    <footer class="py-5 mt-5 bg-light text-center">
        <p class="mb-1">&copy; {{ date('Y') }} Lúmina Joias</p>
        <ul class="list-inline">
            <li class="list-inline-item"><a href="#" class="text-muted">Privacidade</a></li>
            <li class="list-inline-item"><a href="#" class="text-muted">Termos</a></li>
        </ul>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

    {{-- Botão WhatsApp fixo --}}
    <a href="https://w.app/vlphga" class="whatsapp-float" target="_blank" aria-label="Chama a gente no WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>
    @stack('scripts')
</body>

</html>
