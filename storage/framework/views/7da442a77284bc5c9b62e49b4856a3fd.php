

<?php $__env->startSection('title', $product->name); ?>

<?php $__env->startSection('styles'); ?>

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
<?php $__env->stopSection(); ?>


<?php $__env->startSection('content'); ?>
<div class="container my-5">
    <div class="row">
        
        <div class="col-lg-7">
            
            <img src="<?php echo e($product->image_url ?? 'https://via.placeholder.com/600x600.png/EDE8E0/A0522D?text=Lúmina'); ?>" 
                 id="main-image" class="img-fluid rounded shadow-sm w-100 main-product-image" alt="<?php echo e($product->name); ?>">

            
            <div class="d-flex mt-3 gallery-thumbnails">
                
                <div class="col-3 p-1">
                    <img src="<?php echo e($product->image_url ?? 'https://via.placeholder.com/150x150.png'); ?>" 
                         data-large-src="<?php echo e($product->image_url ?? 'https://via.placeholder.com/600x600.png'); ?>"
                         class="img-fluid w-100 thumbnail-image active" alt="Thumbnail 1">
                </div>
                
                <?php $__currentLoopData = $productImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-3 p-1">
                         <img src="<?php echo e($image->image_url); ?>" 
                              data-large-src="<?php echo e($image->image_url); ?>"
                              class="img-fluid w-100 thumbnail-image" alt="Thumbnail <?php echo e($key + 2); ?>">
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </div>

        
        <div class="col-lg-5">
             
             <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?php echo e(route('frontend.home')); ?>">Home</a></li>
                    <li class="breadcrumb-item"><a href="#"><?php echo e($product->type_name); ?></a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?php echo e($product->name); ?></li>
                </ol>
            </nav>

            <h1 class="product-title display-5"><?php echo e($product->name); ?></h1>
            <p class="product-price fs-2 my-3">R$ <?php echo e(number_format($product->sale_price, 2, ',', '.')); ?></p>
            <p class="text-muted"><?php echo e($product->description ?? 'Descrição detalhada do produto em breve.'); ?></p>
            <hr>
            <div class="row align-items-center g-3">
                <div class="col-md-4"><label for="quantity" class="form-label">Quantidade:</label><input type="number" id="quantity" class="form-control" value="1" min="1"></div>
                <div class="col-md-8"><button class="btn btn-primary btn-lg w-100 btn-add-to-cart" data-id="<?php echo e($product->id); ?>"><i class="fas fa-shopping-cart me-2"></i> Adicionar ao Carrinho</button></div>
            </div>
        </div>
    </div>

    
</div>
<?php $__env->stopSection(); ?>


<?php $__env->startPush('scripts'); ?>
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
<?php $__env->stopPush(); ?>
<?php echo $__env->make('frontend::layouts.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Samir\Desktop\lumina-app\Modules/Frontend\resources/views/products/show.blade.php ENDPATH**/ ?>