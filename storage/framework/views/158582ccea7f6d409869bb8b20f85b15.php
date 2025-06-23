<?php $__env->startSection('title', "Imagens de: {$product->name}"); ?>

<?php $__env->startSection('content'); ?>
    <div id="catalogContent">
        <style>
            :root {
                --terracota: #A0522D;
                --white: #ffffff;
            }

            /* Cabeçalho dos cards em terracota */
            #catalogContent .card>.card-header {
                background-color: var(--terracota) !important;
                color: var(--white) !important;
                border-bottom: none;
            }

            /* Botões primários em terracota */
            #catalogContent .btn-primary,
            #catalogContent .btn-success {
                background-color: var(--terracota) !important;
                border-color: var(--terracota) !important;
            }

            /* Fecha-modal personalizado */
            #catalogContent .modal-header .btn-close {
                width: 1.6rem;
                height: 1.6rem;
                background-color: #dc3545;
                border-radius: .25rem;
                position: relative;
            }

            #catalogContent .modal-header .btn-close::before {
                content: "\f00d";
                font-family: "Font Awesome 6 Free";
                font-weight: 900;
                color: #fff;
                font-size: .9rem;
                position: absolute;
                top: 50%;
                left: 50%;
                transform: translate(-50%, -50%);
            }

            #catalogContent .modal-header .btn-close:focus {
                box-shadow: none;
            }

            /* Galeria de imagens */
            #catalogContent .image-gallery {
                display: flex;
                flex-wrap: wrap;
                gap: 1rem;
            }

            #catalogContent .image-card {
                position: relative;
                border: 1px solid #ddd;
                border-radius: .25rem;
                overflow: hidden;
                width: 150px;
                height: 150px;
                background-color: #f8f9fa;
            }

            #catalogContent .image-card img {
                width: 100%;
                height: 100%;
                object-fit: cover;
            }

            #catalogContent .image-card .delete-btn {
                position: absolute;
                top: 5px;
                right: 5px;
                z-index: 10;
            }

            #catalogContent .image-card .reorder-handle {
                position: absolute;
                bottom: 5px;
                right: 5px;
                cursor: move;
                color: white;
                background: rgba(0, 0, 0, 0.5);
                padding: 5px;
                border-radius: .25rem;
            }

            /* dentro do mesmo escopo onde está seu header da galeria */
            #catalogContent .card-header {
                /* só por garantia, mas o d-flex já dispara o display:flex */
                display: flex !important;
                justify-content: space-between !important;
                align-items: center !important;
            }

            #catalogContent .card-tools {
                color: #fff !important;
            }
        </style>

        
        <div class="card card-success">
            <div class="card-header">
                <h3 class="card-title">Adicionar Novas Imagens</h3>
            </div>
            <form action="<?php echo e(route('admin.products.images.store', $product->id)); ?>" method="POST"
                enctype="multipart/form-data">
                <?php echo csrf_field(); ?>
                <div class="card-body">
                    <?php if($errors->any()): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php $__currentLoopData = $errors->all(); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <li><?php echo e($error); ?></li>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </ul>
                        </div>
                    <?php endif; ?>
                    <div class="form-group">
                        <label for="images">Selecione as imagens (pode selecionar várias)</label>
                        <input type="file" class="form-control" name="images[]" id="images" multiple required>
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Enviar Imagens</button>
                    <a href="<?php echo e(route('admin.products.index')); ?>" class="btn btn-secondary">Voltar para a Lista de
                        Produtos</a>
                </div>
            </form>
        </div>

        
        <div class="card">
            <div class="card">
                <div class="card-header bg-terracota text-white d-flex justify-content-between align-items-center">
                    <h3 class="card-title mb-0">Imagens Atuais</h3>
                    <div class="card-tools">
                        <i class="fas fa-arrows-alt me-1"></i>
                        Arraste para reordenar
                    </div>
                </div>
                <div class="card-body">
                    <?php if($images->isEmpty()): ?>
                        <p class="text-center text-muted">Nenhuma imagem cadastrada para este produto.</p>
                    <?php else: ?>
                        <div id="sortable-gallery" class="image-gallery">
                            <?php $__currentLoopData = $images; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $image): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <div class="image-card" data-id="<?php echo e($image->id); ?>">
                                    <img src="<?php echo e($image->image_url); ?>" alt="Imagem do produto">
                                    <button class="btn btn-danger btn-sm delete-btn" data-id="<?php echo e($image->id); ?>">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <div class="reorder-handle"><i class="fas fa-grip-vertical"></i></div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

        </div> 
    <?php $__env->stopSection(); ?>

    <?php $__env->startPush('scripts'); ?>
        <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.js"></script>
        <script>
            $(function() {
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
                    }
                });
                $("#sortable-gallery").sortable({
                    handle: '.reorder-handle',
                    update(e, u) {
                        let order = [];
                        $(this).find('.image-card').each(function() {
                            order.push($(this).data('id'));
                        });
                        $.post("<?php echo e(route('admin.products.images.update-order')); ?>", {
                            order
                        }).done(res => {
                            const Toast = Swal.mixin({
                                toast: true,
                                position: 'top-end',
                                showConfirmButton: false,
                                timer: 2000
                            });
                            Toast.fire({
                                icon: 'success',
                                title: res.success
                            });
                        }).fail(() => Swal.fire('Erro!', 'Não foi possível salvar a nova ordem.', 'error'));
                    }
                }).disableSelection();
                $('#sortable-gallery').on('click', '.delete-btn', function() {
                    let btn = $(this),
                        id = btn.data('id');
                    Swal.fire({
                        title: 'Você tem certeza?',
                        text: 'Esta ação não pode ser desfeita!',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Sim, excluir!',
                        cancelButtonText: 'Cancelar',
                        confirmButtonColor: '#d33'
                    }).then(r => {
                        if (!r.isConfirmed) return;
                        $.ajax({
                                url: `<?php echo e(url('admin/product-images')); ?>/${id}`,
                                type: 'DELETE'
                            })
                            .done(res => {
                                btn.closest('.image-card').fadeOut(300, function() {
                                    $(this).remove();
                                });
                                Swal.fire('Excluído!', res.success, 'success');
                            })
                            .fail(() => Swal.fire('Erro!', 'Não foi possível remover a imagem.',
                                'error'));
                    });
                });
            });
        </script>
    <?php $__env->stopPush(); ?>

<?php echo $__env->make('admin::layouts.layout', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?><?php /**PATH C:\Users\Bruno\Documents\lumina-app\lumina-app\Modules/Admin\resources/views/products/images/index.blade.php ENDPATH**/ ?>