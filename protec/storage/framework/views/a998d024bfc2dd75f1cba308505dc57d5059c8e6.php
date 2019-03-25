<?php
use hoaaah\LaravelBreadcrumb\Breadcrumb as Breadcrumb;
?>


<?php $__env->startSection('content'); ?>	
<div class="col-sm-12 col-md-12 col-lg-12">	
    <div class="panel panel-primary">
        <div class="panel-heading"> 
                <div class="row">
                    <div class="col-lg-10 text-left">
                        <?php $__currentLoopData = $unit; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $units): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <h3 class="panel-title">Pohon Kinerja Dokumen Renstra : <b> <?php echo e($units->nm_unit); ?>  </b> </h3> 
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </div>
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
  </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>

<script type="text/javascript" src="<?php echo e(asset('assets/orgchart/js/jquery.orgchart.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('assets/orgchart/js/html2canvas.min.js')); ?>"></script>

  <script type="text/javascript">
    $(function() {

    var datascource = {
      <?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $datas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        'name': 'Visi',
        'title': '<?php echo e($datas->uraian_visi_renstra); ?>',
        'className': 'level1',
        'nodeTitle': 'name',
        'nodeContent': 'title',
        'children': [
            <?php $__currentLoopData = $datas->TrxRenstraMisis; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $misi): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                {'name': 'Misi','title': '<?php echo e($misi->uraian_misi_renstra); ?>','className': 'level2',
                'children': [
                    <?php $__currentLoopData = $misi->TrxRenstraTujuans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $tujuan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                        {'name': 'Tujuan','title': '<?php echo e($tujuan->uraian_tujuan_renstra); ?>','className': 'level3',
                        'children': [
                            <?php $__currentLoopData = $tujuan->TrxRenstraSasarans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sasaran): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                {'name': 'Sasaran','title': '<?php echo e($sasaran->uraian_sasaran_renstra); ?>','className': 'level4',
                                'children': [
                                    <?php $__currentLoopData = $sasaran->TrxRenstraPrograms; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $program): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        {'name': 'Program','title': '<?php echo e($program->uraian_program_renstra); ?>','className': 'level5',
                                        'children': [
                                            <?php $__currentLoopData = $program->TrxRenstraKegiatans; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $kegiatan): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                {'name': 'Kegiatan','title': '<?php echo e($kegiatan->uraian_kegiatan_renstra); ?>',},
                                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                        ]
                                    },
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                ]
                            },
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        ]
                    },
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    ]
                },
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