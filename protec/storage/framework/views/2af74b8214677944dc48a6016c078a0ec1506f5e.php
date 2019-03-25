<?php
use hoaaah\LaravelBreadcrumb\Breadcrumb as Breadcrumb;
?>



<?php $__env->startSection('content'); ?>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <?php
                $this->title = ' Pejabat Eselon';
                $breadcrumb = new Breadcrumb();
                $breadcrumb->homeUrl = '/';
                $breadcrumb->begin();
                $breadcrumb->add(['url' => '/kinparam','label' => 'Parameter']);
                $breadcrumb->add(['label' => $this->title]);
                $breadcrumb->end();
            ?>
      </div>
    </div>    
    <div id="pesan"></div>
    <div class="row">
      <div class="col-md-12">
        <div class="panel panel-warning">
          <div class="panel-heading">
            <h2 class="panel-title">Daftar Pegawai Pejabat Eselon Perangkat Daerah</h2>
          </div>
          <div class="panel-body"> 
            <form name="" class="form-horizontal" role="form" autocomplete='off' action="" method="post">                    
            <div class="form-group">
              <div class="col-sm-9">
                  <button type="button" class="btn btn-labeled btn-success btnAddPegawai">
                    <span class="btn-label"><i class="fa fa-plus fa-lg fa-fw"></i></span> Tambah Pegawai
                  </button>  
              </div>
            </div>
            </form> 

          <ul class="nav nav-tabs" role="tablist">
            <li class="active"><a href="#pegawai" aria-controls="pegawai" role="tab" data-toggle="tab">Daftar Pegawai</a></li>
          </ul>

          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="pegawai">
              <br>
              <table id='tblPegawai' class="table display table-striped table-bordered table-responsive" cellspacing="0" width="100%">
                  <thead>
                      <tr>
                          
                          <th width="5%" style="text-align: center; vertical-align:middle">No Urut</th>
                          <th width="15%" style="text-align: center; vertical-align:middle">NIP</th>
                          <th style="text-align: center; vertical-align:middle">Nama Pegawai</th>
                          <th width="50px" style="text-align: center; vertical-align:middle">Aksi</th>
                      </tr>
                  </thead>
                  <tbody>
                  </tbody>
            </table>
            </div>
            </div>
            </div>
          </div>
          </div>
      </div>
</div>

<script id="details-inPangkat" type="text/x-handlebars-template">
  <table class="table table-striped display table-bordered table-responsive compact details-table" id="inPangkat-{{id_pegawai}}">
      <thead>
        <tr>
          <th width="5%" style="text-align: center; vertical-align:middle">No Urut</th>
          <th width="15%" style="text-align: center; vertical-align:middle">T M T</th>
          <th style="text-align: center; vertical-align:middle">Uraian Pangkat/Golongan</th>
          <th width="50px" style="text-align: center; vertical-align:middle">Aksi</th>
        </tr>
      </thead>
      <tbody>        
      </tbody>
  </table>
</script>

<?php echo $__env->make('kin.parameter.FrmPegawaiData', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo $__env->make('kin.parameter.FrmPegawaiRiwayatPangkat', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>
<?php echo $__env->make('kin.parameter.FrmPegawaiRiwayatUnit', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>


<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script type="text/javascript" language="javascript" class="init" src="<?php echo e(asset('/protected/resources/views/kin/parameter/js/js_pegawai.js')); ?>"> </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app4', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>