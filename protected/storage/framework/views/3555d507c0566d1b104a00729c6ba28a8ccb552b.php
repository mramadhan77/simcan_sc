<?php
use hoaaah\LaravelBreadcrumb\Breadcrumb as Breadcrumb;
?>


<?php $__env->startSection('content'); ?>	
<div class="col-sm-12 col-md-12 col-lg-12">	
    <div class="panel panel-primary">
        <div class="panel-heading"> 
                <div class="row">
                    <?php $__currentLoopData = $dok; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $doks): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        <div class="col-lg-10 text-left"><h4>Pohon Kinerja Dokumen RPJMD Nomor : <?php echo e($doks->no_perda); ?></h4></div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    <div class="col-lg-2 text-right">
                        <a href="<?php echo e(url('/pokin')); ?>" id="btnBatal" type="button" class="btn btn-sm btn-danger btn-labeled">
                        <span class="btn-label"><i class="fa fa-undo fa-lg fa-fw"></i></span>Kembali</a>
                    </div>
                </div>
            
        </div>
        <div class="panel-body" style="background: white">
            <div id="pk" style="width: 100%;height: 100%;"></div>
        </div>
    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('css'); ?>
  <link rel="stylesheet" href="<?php echo e(asset('assets/orgchart/css/jquery.orgchart.css')); ?>">
  <style type="text/css">
    .orgchart { background: #fff; }
    .orgchart td.left, .orgchart td.right, .orgchart td.top { border-color: #aaa; }
    .orgchart td>.down { background-color: #aaa; }
    .orgchart .level1 .title { background-color: #006699; }
    .orgchart .level1 .content { border-color: #006699; }
    .orgchart .level2 .title { background-color: #009933; }
    .orgchart .level2 .content { border-color: #009933; }
    .orgchart .level3 .title { background-color: #993366; }
    .orgchart .level3 .content { border-color: #993366; }
    .orgchart .level4 .title { background-color: #996633; }
    .orgchart .level4 .content { border-color: #996633; }
    .orgchart .level5 .title { background-color: #cc0066; }
    .orgchart .level5 .content { border-color: #cc0066; }
    .orgchart .level6 .title { background-color: ##596582; }
    .orgchart .level6 .content { border-color: ##596582; }
    .orgchart .level7 .title { background-color: ##009689; }
    .orgchart .level7 .content { border-color: ##009689; }
  </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>

<script type="text/javascript" src="<?php echo e(asset('assets/orgchart/js/jquery.orgchart.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('assets/orgchart/js/html2canvas.min.js')); ?>"></script>

  <script type="text/javascript">
    $(function() {

    var datascource = {
      <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $datas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        'name': 'Visi RPJMD',
        'title': '<?php echo e($datas->uraian_visi_rpjmd); ?>',
        'className': 'level1',
        'nodeTitle': 'name',
        'nodeContent': 'title',
        'children': [
            <?php $__currentLoopData = $datas->TrxRpjmdMisis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $misi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                {'name': 'Misi RPJMD <?php echo e($misi->no_urut); ?>','title': '<?php echo e($misi->uraian_misi_rpjmd); ?>','className': 'level2',
                'children': [
                    <?php $__currentLoopData = $misi->TrxRpjmdTujuans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tujuan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        {'name': 'Tujuan RPJMD <?php echo e($tujuan->no_urut); ?>','title': '<?php echo e($tujuan->uraian_tujuan_rpjmd); ?>','className': 'level3',
                        'children': [
                            <?php $__currentLoopData = $tujuan->TrxRpjmdSasarans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sasaran): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                {'name': 'Sasaran RPJMD <?php echo e($sasaran->no_urut); ?>','title': '<?php echo e($sasaran->uraian_sasaran_rpjmd); ?>','className': 'level4',
                                'children': [
                                    <?php $__currentLoopData = $sasaran->TrxRenstraSasarans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sasrenstra): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        {'name': 'Sasaran Renstra','title': '<?php echo e($sasrenstra->uraian_sasaran_renstra); ?>',
                                        'children': [
                                            <?php $__currentLoopData = $sasrenstra->TrxRenstraPrograms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                {'name': 'Program Renstra','title': '<?php echo e($program->uraian_program_renstra); ?>','className': 'level5',
                                                'children': [
                                                    <?php $__currentLoopData = $program->TrxRenstraKegiatans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kegiatan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                        {'name': 'Kegiatan Renstra','title': '<?php echo e($kegiatan->uraian_kegiatan_renstra); ?>',},
                                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                                ]},
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        ]},
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                ]},
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        ]},
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    ]},
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        ]
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
     };

    var oc = $('#pk').orgchart({
        'data' : datascource,
        'nodeContent': 'title',
        'pan': true,
        'zoom': true,        
        'exportButton': true,
        'exportFilename': 'ChartRPJMD'
    });
    oc.hideChildren(oc.$chartContainer.find('.node:first'));
    oc.$chartContainer.on('touchmove', function(event) {
      event.preventDefault();
    });

  });
  </script>

<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app4', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>