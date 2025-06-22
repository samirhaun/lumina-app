<?php if (isset($component)) { $__componentOriginal7923c505f22ee491987643f7cc735ee7 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal7923c505f22ee491987643f7cc735ee7 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'frontend::components.layouts.master','data' => []] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('frontend::layouts.master'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
    <h1>Hello World</h1>

    <p>Module: <?php echo config('frontend.name'); ?></p>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal7923c505f22ee491987643f7cc735ee7)): ?>
<?php $attributes = $__attributesOriginal7923c505f22ee491987643f7cc735ee7; ?>
<?php unset($__attributesOriginal7923c505f22ee491987643f7cc735ee7); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal7923c505f22ee491987643f7cc735ee7)): ?>
<?php $component = $__componentOriginal7923c505f22ee491987643f7cc735ee7; ?>
<?php unset($__componentOriginal7923c505f22ee491987643f7cc735ee7); ?>
<?php endif; ?>
<?php /**PATH C:\Users\Samir\Desktop\lumina-app\Modules/Frontend\resources/views/index.blade.php ENDPATH**/ ?>