@extends('frontend::layouts.layout')

@section('title', 'Página Inicial')

@section('content')
  {{-- Hero full-screen --}}
  <section class="hero-section d-flex align-items-center justify-content-center text-center">
    <div class="hero-overlay"></div>
    <div class="hero-content">
      <h1 class="hero-title">Lúmina Joias</h1>
      <p class="hero-subtitle">Onde o luxo encontra a elegância atemporal</p>
      <a href="#produtos" class="btn btn-hero">Ver Coleção</a>
    </div>
  </section>

  {{-- Grade de Produtos --}}
  <section id="produtos" class="container py-5">
    <h2 class="section-title">Coleção em Destaque</h2>
    <div class="row g-4">
      @foreach($products as $p)
        @php
          $img = optional($imagesByProduct[$p->id] ?? null)->first()->image_url 
                 ?? asset('images/placeholder.png');
        @endphp

        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
          <div class="card product-card h-100 shadow-sm">
            <a href="{{ route('frontend.products.show', $p->id) }}" class="card-img-link">
              <img src="{{ $img }}"
                   class="card-img-top"
                   alt="{{ $p->name }}">
            </a>
            <div class="card-body d-flex flex-column">
              <h5 class="card-title mb-2">
                <a href="{{ route('frontend.products.show', $p->id) }}"
                   class="stretched-link text-dark text-decoration-none">
                  {{ \Illuminate\Support\Str::limit($p->name, 30) }}
                </a>
              </h5>
              <p class="card-price mb-4">R$ {{ number_format($p->sale_price,2,',','.') }}</p>
              <button class="btn btn-outline-primary mt-auto btn-add-to-cart"
                      data-id="{{ $p->id }}">
                <i class="fas fa-shopping-cart me-1"></i> Adicionar
              </button>
            </div>
          </div>
        </div>
      @endforeach
    </div>
  </section>

  {{-- Sobre a Marca --}}
  <section class="brand-story py-5 bg-light text-center">
    <div class="container">
      <h2 class="section-title">Nossa História</h2>
      <p class="lead mx-auto" style="max-width:700px">
        Desde 1992, celebrando momentos únicos com design exclusivo e gemas selecionadas.
        Cada peça é criada para durar gerações.
      </p>
    </div>
  </section>
@endsection

@push('scripts')
<script>
$(function(){
  $(document).on('click','.btn-add-to-cart',function(){
    let btn = $(this), id = btn.data('id'), orig = btn.html();
    btn.prop('disabled',true).html('<span class="spinner-border spinner-border-sm"></span>');
    $.post("{{ route('cart.add') }}",{
      _token:'{{ csrf_token() }}',
      product_id:id
    }).done(res=>{
      $('#cart-count').text(res.cartCount).toggle(res.cartCount>0);
      Swal.fire({
        icon:'success', title:'Adicionado!', toast:true,
        position:'top-end', showConfirmButton:false, timer:2000
      });
    }).fail(()=>{
      Swal.fire('Erro!','Não foi possível adicionar.','error');
    }).always(()=>{
      btn.prop('disabled',false).html(orig);
    });
  });
});
</script>
@endpush
