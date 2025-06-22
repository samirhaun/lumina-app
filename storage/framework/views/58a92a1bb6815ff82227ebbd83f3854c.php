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
    <header class="store-header p-3 mb-4">
        <div class="container">
            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-lg-start">
                <a href="<?php echo e(route('frontend.home')); ?>" class="navbar-brand me-lg-4">
                    <img src="<?php echo e(asset('images/logo-terracota.png')); ?>" alt="Lúmina Joias">
                </a>
                <ul class="nav col-12 col-lg-auto me-lg-auto mb-2 justify-content-center mb-md-0">
                    
                </ul>
                <div class="ms-lg-auto">
                    <a href="<?php echo e(route('cart.index')); ?>" class="btn btn-outline-primary position-relative">
                        <i class="fas fa-shopping-cart"></i>
                        Carrinho
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                            id="cart-count" <?php if(Cart::count() == 0): ?> style="display: none;" <?php endif; ?>>
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
    <?php echo $__env->yieldPushContent('scripts'); ?>
</body>

</html>
<?php /**PATH C:\Users\Samir\Desktop\lumina-app\Modules/Frontend\resources/views/layouts/layout.blade.php ENDPATH**/ ?>