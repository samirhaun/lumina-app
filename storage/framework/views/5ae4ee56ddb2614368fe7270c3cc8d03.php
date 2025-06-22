

<?php $__env->startSection('title', 'Página Inicial'); ?>

<?php $__env->startSection('content'); ?>
  
  <section class="hero-section d-flex align-items-center justify-content-center text-center">
    <div class="hero-overlay"></div>
    <div class="hero-content">
      <h1 class="hero-title">Lúmina Joias</h1>
      <p class="hero-subtitle">Onde o luxo encontra a elegância atemporal</p>
      <a href="#produtos" class="btn btn-hero">Ver Coleção</a>
    </div>
  </section>

  
  <section id="produtos" class="container py-5">
    <h2 class="section-title">Coleção em Destaque</h2>
    <div class="row g-4">
      <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <?php
          $img = optional($imagesByProduct[$p->id] ?? null)->first()->image_url 
                 ?? asset('images/placeholder.png');
        ?>

        <div class="col-12 col-sm-6 col-md-4 col-lg-3">
          <div class="card product-card h-100 shadow-sm">
            <a href="<?php echo e(route('frontend.products.show', $p->id)); ?>" class="card-img-link">
              <img src="<?php echo e($img); ?>"
                   class="card-img-top"
                   alt="<?php echo e($p->name); ?>">
            </a>
            <div class="card-body d-flex flex-column">
              <h5 class="card-title mb-2">
                <a href="<?php echo e(route('frontend.products.show', $p->id)); ?>"
                   class="stretched-link text-dark text-decoration-none">
                  <?php echo e(\Illuminate\Support\Str::limit($p->name, 30)); ?>

                </a>
              </h5>
              <p class="card-price mb-4">R$ <?php echo e(number_format($p->sale_price,2,',','.')); ?></p>
              <button class="btn btn-outline-primary mt-auto btn-add-to-cart"
                      data-id="<?php echo e($p->id); ?>">
                <i class="fas fa-shopping-cart me-1"></i> Adicionar
              </button>
            </div>
          </div>
        </div>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </div>
  </section>

  
  <section class="brand-story py-5 bg-light text-center">
    <div class="container">
      <h2 class="section-title">Nossa História</h2>
      <p class="lead mx-auto" style="max-width:700px">
        Desde 1992, celebrando momentos únicos com design exclusivo e gemas selecionadas.
        Cada peça é criada para durar gerações.
      </p>
    </div>
  </section>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
<script>
$(function(){
  $(document).on('click','.btn-add-to-cart',function(){
    let btn = $(this), id = btn.data('id'), orig = btn.html();
    btn.prop('disabled',true).html('<span class="spinner-border spinner-border-sm"></span>');
    $.post("<?php echo e(route('cart.add')); ?>",{
      _token:'<?php echo e(csrf_token()); ?>',
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('frontend::layouts.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Samir\Desktop\lumina-app\Modules/Frontend\resources/views/home.blade.php ENDPATH**/ ?>