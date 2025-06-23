<?php $__env->startSection('title', 'Catálogo de Produtos'); ?>

<?php $__env->startSection('content'); ?>
    <div class="container my-5">
        
        <div class="row g-4">
            <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <?php
                    // primeira imagem (ou placeholder)
                    $img =
                        optional($imagesByProduct[$p->id] ?? null)->first()->image_url ??
                        asset('images/placeholder.png');
                ?>
                <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                    <div class="card product-card h-100 shadow-sm">
                        <a href="<?php echo e(route('frontend.products.show', $p->id)); ?>" class="card-img-link">
                            <img src="<?php echo e($img); ?>" class="card-img-top" style="height:260px; object-fit:cover;"
                                alt="<?php echo e($p->name); ?>">
                        </a>
                        <div class="card-body d-flex flex-column">
                            <h6 class="card-title mb-2">
                                <a href="<?php echo e(route('frontend.products.show', $p->id)); ?>"
                                    class="stretched-link text-dark text-decoration-none">
                                    <?php echo e(\Illuminate\Support\Str::limit($p->name, 30)); ?>

                                </a>
                            </h6>
                            <p class="card-price mb-4 text-terracota">
                                R$ <?php echo e(number_format($p->sale_price, 2, ',', '.')); ?>

                            </p>
                            <button class="btn btn-outline-terracota mt-auto btn-view-more" data-id="<?php echo e($p->id); ?>">
                                <i class="fas fa-eye me-1"></i> Ver mais
                            </button>
                        </div>
                    </div>
                </div>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </div>
    </div>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
    <script>
        $(function() {
            $('.btn-add-to-cart').on('click', function() {
                let btn = $(this),
                    id = btn.data('id'),
                    orig = btn.html();
                btn.prop('disabled', true)
                    .html('<span class="spinner-border spinner-border-sm"></span>');
                $.post("<?php echo e(route('cart.add')); ?>", {
                    _token: '<?php echo e(csrf_token()); ?>',
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('frontend::layouts.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Bruno\Documents\lumina-app\lumina-app\Modules/Frontend\resources/views/products/index.blade.php ENDPATH**/ ?>