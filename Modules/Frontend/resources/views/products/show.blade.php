@extends('frontend::layouts.layout')

@section('title', $product->name)

@section('styles')
{{-- CSS específico para a galeria de imagens --}}
<style>
    .gallery-thumbnails img {
        cursor: pointer;
        opacity: 0.7;
        transition: opacity 0.2s ease;
        border: 2px solid transparent;
        border-radius: .25rem;
    }
    .gallery-thumbnails img:hover, .gallery-thumbnails img.active {
        opacity: 1;
        border-color: var(--color-gold);
    }
    .main-product-image {
        max-height: 550px;
        object-fit: cover;
    }
</style>
@endsection


@section('content')
<div class="container my-5">
    <div class="row">
        {{-- COLUNA DA ESQUERDA: GALERIA DE IMAGENS --}}
        <div class="col-lg-7">
            {{-- Imagem Principal --}}
            <img src="{{ $product->image_url ?? 'https://via.placeholder.com/600x600.png/EDE8E0/A0522D?text=Lúmina' }}" 
                 id="main-image" class="img-fluid rounded shadow-sm w-100 main-product-image" alt="{{ $product->name }}">

            {{-- Miniaturas (Thumbnails) --}}
            <div class="d-flex mt-3 gallery-thumbnails">
                {{-- A primeira miniatura é sempre a imagem de capa --}}
                <div class="col-3 p-1">
                    <img src="{{ $product->image_url ?? 'https://via.placeholder.com/150x150.png' }}" 
                         data-large-src="{{ $product->image_url ?? 'https://via.placeholder.com/600x600.png' }}"
                         class="img-fluid w-100 thumbnail-image active" alt="Thumbnail 1">
                </div>
                {{-- As outras miniaturas vêm da nova tabela --}}
                @foreach ($productImages as $key => $image)
                    <div class="col-3 p-1">
                         <img src="{{ $image->image_url }}" 
                              data-large-src="{{ $image->image_url }}"
                              class="img-fluid w-100 thumbnail-image" alt="Thumbnail {{ $key + 2 }}">
                    </div>
                @endforeach
            </div>
        </div>

        {{-- COLUNA DA DIREITA: INFORMAÇÕES E COMPRA --}}
        <div class="col-lg-5">
             {{-- O conteúdo aqui permanece o mesmo da versão anterior --}}
             <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('frontend.home') }}">Home</a></li>
                    <li class="breadcrumb-item"><a href="#">{{ $product->type_name }}</a></li>
                    <li class="breadcrumb-item active" aria-current="page">{{ $product->name }}</li>
                </ol>
            </nav>

            <h1 class="product-title display-5">{{ $product->name }}</h1>
            <p class="product-price fs-2 my-3">R$ {{ number_format($product->sale_price, 2, ',', '.') }}</p>
            <p class="text-muted">{{ $product->description ?? 'Descrição detalhada do produto em breve.' }}</p>
            <hr>
            <div class="row align-items-center g-3">
                <div class="col-md-4"><label for="quantity" class="form-label">Quantidade:</label><input type="number" id="quantity" class="form-control" value="1" min="1"></div>
                <div class="col-md-8"><button class="btn btn-primary btn-lg w-100 btn-add-to-cart" data-id="{{ $product->id }}"><i class="fas fa-shopping-cart me-2"></i> Adicionar ao Carrinho</button></div>
            </div>
        </div>
    </div>

    {{-- PRODUTOS RELACIONADOS (sem alteração) --}}
</div>
@endsection


@push('scripts')
<script>
$(function () {
    // Lógica para a galeria de imagens
    $('.thumbnail-image').on('click', function() {
        const newSrc = $(this).data('large-src');
        $('#main-image').attr('src', newSrc);

        // Atualiza a classe 'active' para destacar a miniatura selecionada
        $('.thumbnail-image').removeClass('active');
        $(this).addClass('active');
    });

    // Lógica para o botão "Adicionar ao Carrinho" (sem alteração)
    $('.btn-add-to-cart').on('click', function() {
        // ... (código anterior)
    });
});
</script>
@endpush