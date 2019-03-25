<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="_token" content="<?php echo csrf_token(); ?>"/>

    <meta name="description" content="Sistem Perencanaan yang dikembangkan oleh Tim Simda BPKP">
    <meta name="author" content="Tim Simda BPKP">
    <link rel="icon" href="<?php echo e(asset('simda-favicon.ico')); ?>">

    <title>simd@Perencanaan</title>

    <!-- Styles -->
    
    <link href="<?php echo e(asset('css/font-awesome.min.css')); ?>" rel='stylesheet' type='text/css'>
    <link href="<?php echo e(asset('css/bootstrap.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/jquery.dataTables.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/jquery-ui.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('vendor/metisMenu/metisMenu.min.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/sb-admin-2.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/dataTables.bootstrap.css')); ?>" rel="stylesheet">
    <link href="<?php echo e(asset('css/dataTables.fontAwesome.css')); ?>" rel="stylesheet">

    
    
    <?php echo $__env->yieldContent('head'); ?>
    <style>
        h1.padding {
        padding-right: 1cm;
        }
    </style>
</head>
<body>

        <?php echo $__env->yieldContent('layoutBody'); ?>

        <script src="<?php echo e(asset('/js/jquery.min.js')); ?>"></script>
        <script src="<?php echo e(asset('/js/jquery-ui.js')); ?>"></script>
        <script src="<?php echo e(asset('/js/bootstrap.min.js')); ?>"></script>
        <script src="<?php echo e(asset('/js/handlebars.js')); ?>"></script>
        <script src="<?php echo e(asset('/js/jquery.dataTables.min.js')); ?>"></script>
        <script src="<?php echo e(asset('/js/dataTables.bootstrap.min.js')); ?>"></script>
        <script src="<?php echo e(asset('/js/dataTables.responsive.min.js')); ?>"></script>
        <script src="<?php echo e(asset('/js/dataTables.checkboxes.min.js')); ?>"></script>
        <script type="text/javascript" src="<?php echo e(asset('/js/input.js')); ?>"></script>
        <script src="<?php echo e(asset('/js/jquery.number.js')); ?>"></script>
        <script src="<?php echo e(asset('vendor/metisMenu/metisMenu.min.js')); ?>"></script>
        <script src="<?php echo e(asset('/js/sb-admin-2.js')); ?>"></script>


        <?php echo $__env->yieldContent('scripts'); ?>

</body>
</html>
