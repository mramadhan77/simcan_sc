<?php
use App\CekAkses;
use hoaaah\LaravelMenu\Menu;

?>
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
    <link href="<?php echo e(asset('css/jquery.dataTables.css')); ?>" rel="stylesheet">
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
    <div id="wrapper">
        <nav class="navbar navbar-default navbar-static-top" role="navigation" style="margin-bottom: 0; background: #0E203A; border-color: #ccc; box-shadow: 0 0 2px 0 #E8FFFF;">
            
                <div class="navbar-header">
                    <a class="navbar-brand navbar-right" href="<?php echo e(url('/home')); ?>" style="margin-top: -5px; margin-left: 10px; max-height: 40px;">
                    <span class="fa-stack">
                      <i class="fa fa-square-o fa-stack-2x text-info"></i>
                      <i class="fa fa-home fa-stack-1x" style="color:#fff"></i>
                    </span><span style="color:#fff"> simd@<strong>Perencanaan</strong> ver <strong>1.0 </strong></span> 
                    </a>
                </div>
                <ul class="nav navbar-top-links pull-right">
                        <!-- Authentication Links -->
                        <?php if(Auth::guest()): ?>
                            <li class="dropdown" style="color:#fff">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" style="color:#fff">
                                    User <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu dropdown-user" role="menu">
                                    <li>
                                        <a href="<?php echo e(route('register')); ?>">Register</a>
                                    </li>
                                    <li>
                                        <a href="<?php echo e(route('login')); ?>">Login</a>
                                    </li>
                                </ul>
                            </li>
                        <?php else: ?>
                        <span style="color:#fff">
                            <i class="fa fa-flag fa-fw"></i> Tahun Anggaran: <?= Session::get('tahun') != NULL ? Session::get('tahun') : 'Pilih!' ?></i>
                        </span>
                            <li class="dropdown" style="color:#fff">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false" style="color:#fff">
                                    <?php echo e(Auth::user()->name); ?> <span class="caret"></span>
                                </a>

                                <ul class="dropdown-menu dropdown-user" role="menu">
                                    <li>
                                        <a href="<?php echo e(url('/home')); ?>"><i class="fa fa-home fa-fw text-info"></i> Home</a>
                                        <a href="<?php echo e(route('logout')); ?>" onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="fa fa-sign-out fa-fw text-info"></i> Logout</a>

                                        <form id="logout-form" action="<?php echo e(route('logout')); ?>" method="POST" style="display: none;">
                                            <?php echo e(csrf_field()); ?>

                                        </form>
                                    </li>
                                </ul>
                            </li>
                        <?php endif; ?>
                    </ul>

                   <div class="navbar-default sidebar" role="navigation">
                    <?php
                            $akses = new CekAkses();
                            $menu = new Menu();
                            $menu->render([
                                'options' => [
                                    'ulId' => 'side-menu'
                                ],
                                'items' => [
                                    [   'label' => 'Modul SSH dan ASB',
                                        'icon'=>'fa fa-database fa-fw fa-lg' , 
                                        'url' => '/asb/dash'],
                                    [
                                        'label' => 'Standard Satuan Harga',
                                        'icon' => 'fa fa-book fa-fw', 
                                        'visible' => $akses->get(801)||$akses->get(802)||$akses->get(803)||$akses->get(807),
                                        'items' => [
                                            ['label' => 'Zona SSH','url' => '/zonassh', 'visible' => $akses->get(801)],
                                            ['label' => 'Struktur SSH', 'url' => '/ssh', 'visible' => $akses->get(802)],
                                            ['label' => 'Perkada SSH', 'url' => '/sshperkada/perkada','visible' => $akses->get(803)],
                                            ['label' => 'Pencetakan SSH','url' => '/printSsh','visible' => $akses->get(803)],
                                        ]
                                    ],                                    
                                    [
                                        'label' => 'Analisis Standar Belanja',
                                        'icon' => 'fa fa-bookmark fa-fw', 
                                        'visible' => $akses->get(804)||$akses->get(805)||$akses->get(806)||$akses->get(808),
                                        'items' => [
                                            ['label' => 'Perkada & Struktur ASB','url' => '/asb/aktivitas','visible' => $akses->get(805)],
                                            ['label' => 'Perhitungan ASB','url' => '/asb/hitungasb','visible' => $akses->get(806)],
                                        ]
                                    ],
                                    /*[
                                        'label' => 'Pencetakan SSH & ASB',
                                        'icon' => 'fa fa-bookmark fa-fw', 
                                        'visible' => $akses->get(806)||$akses->get(808),
                                        'items' => [
                                            ['label' => 'Standard Satuan Harga','url' => '/printSsh','visible' => $akses->get(805)],
                                            ['label' => 'Analisis Standar Belanja','url' => '/printSsh','visible' => $akses->get(806)],
                                        ]
                                    ],*/
                                ]
                            ]);
                        ?>
                        
                        </div>
        </nav>

        <div id="page-wrapper" style="background-image: linear-gradient(to bottom, rgb(96,108,136) 0%,rgb(63,76,107) 100%);
        background-repeat: no-repeat; background-attachment: fixed;">
            <br>
            <?php echo $__env->yieldContent('content'); ?>
        </div>

    </div>

        <script type="text/javascript" src="<?php echo e(asset('/js/jquery.min.js')); ?>"></script>
        <script type="text/javascript" src="<?php echo e(asset('/js/jquery-ui.js')); ?>"></script>
        <script type="text/javascript" src="<?php echo e(asset('/js/bootstrap.min.js')); ?>"></script>
        <script type="text/javascript" src="<?php echo e(asset('/js/jquery.dataTables.min.js')); ?>"></script>
        <script type="text/javascript" src="<?php echo e(asset('/js/dataTables.bootstrap.min.js')); ?>"></script>
        <script type="text/javascript" src="<?php echo e(asset('/js/dataTables.checkboxes.min.js')); ?>"></script>
        <script src="<?php echo e(asset('/js/dataTables.responsive.min.js')); ?>"></script>
        <script type="text/javascript" src="<?php echo e(asset('/js/input.js')); ?>"></script>
        <script type="text/javascript" src="<?php echo e(asset('vendor/metisMenu/metisMenu.min.js')); ?>"></script>        
        <script type="text/javascript" src="<?php echo e(asset('/js/jquery.number.js')); ?>"></script>
        <script type="text/javascript" src="<?php echo e(asset('/js/sb-admin-2.js')); ?>"></script>
        <script src="<?php echo e(asset('/js/datepicker-id.js')); ?>"></script>


        <?php echo $__env->yieldContent('scripts'); ?>

</body>
</html>
