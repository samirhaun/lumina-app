<?php if(isset($lowStockItems) && $lowStockItems->count() > 0): ?>
    <ul class="products-list product-list-in-card ps-2 pe-2">
        <?php $__currentLoopData = $lowStockItems; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <li class="item">
            <div class="product-info">
                <span class="product-title"><?php echo e($item->name); ?></span>
                <span class="badge <?php echo e($item->quantity_on_hand > 0 ? 'bg-warning' : 'bg-danger'); ?> float-end">
                    <?php echo e($item->quantity_on_hand); ?> / <?php echo e($item->minimum_stock); ?>

                </span>
                <span class="product-description">
                    Estoque atual / Mínimo
                </span>
            </div>
        </li>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </ul>
<?php else: ?>
    <div class="text-center text-success p-4">
        <i class="fas fa-check-circle fa-2x"></i>
        <p class="mt-2 mb-0">Nenhum item com estoque baixo no momento.</p>
    </div>
<?php endif; ?><?php /**PATH C:\Users\Samir\Desktop\lumina-app\Modules/Admin\resources/views/partials/low-stock-list.blade.php ENDPATH**/ ?>