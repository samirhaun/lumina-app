<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">

        <title>Frontend Module - <?php echo e(config('app.name', 'Laravel')); ?></title>

        <meta name="description" content="<?php echo e($description ?? ''); ?>">
        <meta name="keywords" content="<?php echo e($keywords ?? ''); ?>">
        <meta name="author" content="<?php echo e($author ?? ''); ?>">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        
        
    </head>

    <body>
        <?php echo e($slot); ?>


        
        
    </body>
<?php /**PATH C:\Users\Samir\Desktop\lumina-app\Modules/Frontend\resources/views/components/layouts/master.blade.php ENDPATH**/ ?>