<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lúmina Joias - <?php echo $__env->yieldContent('title', 'Página Inicial'); ?></title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
    <link rel="stylesheet" href="<?php echo e(asset('css/store.css')); ?>"> 

    <?php echo $__env->yieldPushContent('styles'); ?>
</head>

<body>
    <?php
        use Illuminate\Support\Facades\DB;
        // pega o filtro atual
        $currentType = request()->routeIs('frontend.products.index') ? request('type') : null;

        if (request()->routeIs('frontend.products.show')) {
            $prodId = request()->route('id');
            $currentType = DB::table('products')->where('id', $prodId)->value('product_type_id');
        }
    ?>

    <header class="store-header navbar navbar-expand-lg navbar-light bg-white shadow-sm">
        <div class="container">
            <a class="navbar-brand" href="<?php echo e(route('frontend.home')); ?>">
                <img src="<?php echo e(asset('images/logo-terracota.png')); ?>" alt="Lúmina Joias">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav"
                aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="mainNav">
                
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a href="<?php echo e(route('frontend.home')); ?>"
                            class="nav-link <?php echo e(is_null($currentType) ? 'active' : ''); ?>">
                            Início
                        </a>
                    </li>
                    <?php $__currentLoopData = $productTypes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $type): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <li class="nav-item">
                            <a href="<?php echo e(route('frontend.products.index', ['type' => $type->id])); ?>"
                                class="nav-link <?php echo e($currentType == $type->id ? 'active' : ''); ?>">
                                <?php echo e($type->name); ?>

                            </a>
                        </li>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                </ul>

                
                <div class="d-flex">
                    <a href="<?php echo e(route('cart.index')); ?>" class="btn btn-outline-dark position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        <span class="ms-2">Carrinho</span>
                        <span id="cart-count"
                            class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            <?php if(Cart::count() == 0): ?> style="display:none" <?php endif; ?>>
                            <?php echo e(Cart::count()); ?>

                        </span>
                    </a>
                </div>
            </div>
        </div>
    </header>
    <main class="container">
        <?php echo $__env->yieldContent('content'); ?>
    </main>

    <footer class="py-5 mt-5 bg-light text-center">
        <p class="mb-1">&copy; <?php echo e(date('Y')); ?> Lúmina Joias</p>
        <ul class="list-inline">
            <li class="list-inline-item"><a href="#" class="text-muted">Privacidade</a></li>
            <li class="list-inline-item"><a href="#" class="text-muted">Termos</a></li>
        </ul>
    </footer>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

    
    <a href="https://w.app/vlphga" class="whatsapp-float" target="_blank" aria-label="Chama a gente no WhatsApp">
        <i class="fab fa-whatsapp"></i>
    </a>
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html>
<?php /**PATH C:\Users\Bruno\Documents\lumina-app\lumina-app\Modules/Frontend\resources/views/layouts/layout.blade.php ENDPATH**/ ?>