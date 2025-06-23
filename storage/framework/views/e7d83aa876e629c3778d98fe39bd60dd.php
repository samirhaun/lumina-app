<?php $__env->startSection('title', $product->name); ?>

<?php $__env->startPush('styles'); ?>
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
<?php $__env->stopPush(); ?>

<?php $__env->startSection('content'); ?>
    <main class="container py-5">
        
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="<?php echo e(route('frontend.home')); ?>">Início</a></li>
                <li class="breadcrumb-item">
                    <a href="<?php echo e(route('frontend.products.index', ['type' => $product->product_type_id])); ?>">
                        <?php echo e($product->type_name); ?>

                    </a>
                </li>
                <li class="breadcrumb-item active" aria-current="page"><?php echo e($product->name); ?></li>
            </ol>
        </nav>

        <div class="row gx-5">
            
            <div class="col-lg-6">
                <div id="productGallery" class="carousel slide carousel-fade main-image-wrapper" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <?php $__empty_1 = true; $__currentLoopData = $productImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                            <div class="carousel-item <?php if($loop->first): ?> active <?php endif; ?>">
                                <img src="<?php echo e($img->image_url); ?>" class="d-block w-100" alt="Foto <?php echo e($loop->iteration); ?>">
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                            <div class="carousel-item active">
                                <img src="<?php echo e(asset('images/placeholder.png')); ?>" class="d-block w-100" alt="Sem imagem">
                            </div>
                        <?php endif; ?>
                    </div>
                    <?php if($productImages->count() > 1): ?>
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
                    <?php endif; ?>
                </div>

                <?php if($productImages->count() > 1): ?>
                    <div class="row gallery-thumbnails gx-2 mt-3">
                        <?php $__currentLoopData = $productImages; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $img): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <div class="col-3 p-1">
                                <img src="<?php echo e($img->image_url); ?>" data-bs-target="#productGallery"
                                    data-bs-slide-to="<?php echo e($loop->index); ?>"
                                    class="img-fluid thumbnail-image <?php if($loop->first): ?> active <?php endif; ?>"
                                    alt="Miniatura <?php echo e($loop->iteration); ?>">
                            </div>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
                <?php endif; ?>
            </div>

            
            <div class="col-lg-6">
                <div class="info-panel">
                    
                    <h3><?php echo e($product->name); ?></h3>

                    <?php if($product->code): ?>
                        <div class="sku-badge">
                            <i class="fas fa-barcode"></i>
                            <span>Cód: <?php echo e($product->code); ?></span>
                        </div>
                    <?php endif; ?>

                    
                    <h2 class="price">R$ <?php echo e(number_format($product->sale_price, 2, ',', '.')); ?></h2>

                    
                    <?php $inst = $product->sale_price/3; ?>
                    <div class="installments">
                        ou 3× de <span>R$<?php echo e(number_format($inst, 2, ',', '.')); ?></span> sem juros
                    </div>

                    
                    <p class="short-desc">
                        <?php
                            // Apenas decodifica entidades e remove tags
                            $cleanDescription = html_entity_decode(
                                strip_tags($product->description ?? 'Nenhuma descrição disponível.'),
                            );
                        ?>
                        <?php echo e($cleanDescription); ?>

                    </p>


                    
                    <div class="payments mb-4">
                        <i class="fab fa-cc-visa"></i>
                        <i class="fab fa-cc-mastercard"></i>
                        <i class="fab fa-cc-amex"></i>
                        <a href="#" class="small">Ver meios de pagamento</a>
                    </div>

                    
                    <div class="d-grid gap-3 mb-4"> 
                        <div class="row g-2 align-items-center">
                            <div class="col-auto">
                                <label for="quantity" class="form-label mb-0">Quantidade:</label>
                            </div>
                            <div class="col-auto">
                                
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
                        <div class="d-grid"> 
                            <button class="btn btn-terracota btn-lg btn-add-to-cart" data-id="<?php echo e($product->id); ?>">
                                <i class="fas fa-shopping-cart me-2"></i>Adicionar ao Carrinho
                            </button>
                        </div>
                    </div>

                    
                    <div class="share mb-4">
                        <span class="me-2">Compartilhe:</span>
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-pinterest"></i></a>
                    </div>

                    
                    <div>
                        <h6>Especificações</h6>
                        
                        <?php echo $product->specifications ?? '<ul><li>Nenhuma especificação disponível.</li></ul>'; ?>

                    </div>
                </div>
            </div>
        </div>
    </main>
<?php $__env->stopSection(); ?>

<?php $__env->startPush('scripts'); ?>
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
            $.post("<?php echo e(route('cart.add')); ?>", {
                    _token: '<?php echo e(csrf_token()); ?>',
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
<?php $__env->stopPush(); ?>

<?php echo $__env->make('frontend::layouts.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Bruno\Documents\lumina-app\lumina-app\Modules/Frontend\resources/views/products/show.blade.php ENDPATH**/ ?>