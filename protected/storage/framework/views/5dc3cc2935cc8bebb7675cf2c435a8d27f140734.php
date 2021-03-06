<!DOCTYPE html>
<html lang="<?php echo e(app()->getLocale()); ?>">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="_token" content="<?php echo csrf_token(); ?>"/>

        <meta name="description" content="Simda@ Client-API dikembangkan oleh Tim Simda BPKP">
        <meta name="author" content="Tim Simda BPKP">
        <link rel="icon" href="<?php echo e(asset('simda-favicon.ico')); ?>">

        <title>simd@Perencanaan</title>

        <!-- Styles -->
        <link href="<?php echo e(asset('css/font-awesome.min.css')); ?>" rel='stylesheet' type='text/css'>
        <link href="<?php echo e(asset('css/bootstrap.min.css')); ?>" rel="stylesheet">
        <link href="<?php echo e(asset('css/jquery-ui.css')); ?>" rel="stylesheet">
        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css?family=Raleway:100,600" rel="stylesheet" type="text/css">

    <style>
            html, body {
              height: 100%;
            }

            body {
              margin: 0;
              padding: 0;
              width: 100%;
              color: #fff;
              display: table;
              font-weight: 300;
              font-family: 'Raleway';
              background: linear-gradient(to bottom, rgb(30,87,153) 0%,rgb(32,124,202) 39%,rgb(32,124,202) 39%,rgb(41,137,216) 57%,rgb(125,185,232) 100%);
            }

            .container {
              text-align: center;
              display: table-cell;
              vertical-align: middle;
            }

            .content {
              text-align: center;
              display: inline-block;
            }

            .title {
              font-size: 84px;
              font-weight: bold;
              color: #0cff00 ;
              margin-bottom: 40px;
              text-shadow: 10px 7px #020705;
            }
    </style>
  </head>
  <body>
    <div class="container">
      <div class="content">
        <div class="title"><i class="fa fa-coffee fa-5x" aria-hidden="true"></i></div>
        <h2 style="color: #f22c0e, font-weight: 40px">Maaf, Menu yang anda cari dalam pengembangan..!!!!</h2>
      </div>
    </div>
  </body>
</html>
