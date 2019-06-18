<?php
use hoaaah\LaravelBreadcrumb\Breadcrumb as Breadcrumb;
?>



<?php $__env->startSection('content'); ?>
  <div class="container-fluid">
    <div class="row">
      <div class="col-md-12">
        <?php
                $this->title = ' RPJMD ';
                $breadcrumb = new Breadcrumb();
                $breadcrumb->homeUrl = '/';
                $breadcrumb->begin();
                $breadcrumb->add(['url' => '/rpjmd','label' => 'RPJMD dan Renstra']);
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
            <h2 class="panel-title">Data Rencana Pembangunan Jangka Menengah Daerah</h2>
          </div>

          <div class="panel-body">
          <ul class="nav nav-tabs" role="tablist">
            <li class="active"><a href="#dokumen" aria-controls="dokumen" role="tab" data-toggle="tab">Dokumen RPJMD</a></li>
            
            <li><a href="#misi" aria-controls="misi" role="tab" data-toggle="tab">Misi</a></li>
            <li><a href="#tujuan" aria-controls="tujuan" role="tab" data-toggle="tab">Tujuan</a></li>
            <li><a href="#sasaran" aria-controls="sasaran" role="tab" data-toggle="tab">Sasaran</a></li>
            <li><a href="#program" aria-controls="program" role="tab" data-toggle="tab">Program Daerah</a></li>
            <li><a href="#btl" aria-controls="btl" role="tab" data-toggle="tab">Belanja Non Program</a></li>
            <li><a href="#pendapatan" aria-controls="pendapatan" role="tab" data-toggle="tab">Pendapatan</a></li>
          </ul>

          <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="dokumen">
              <br>
              <div class="add">
                <button class="btnAddDokumen btn-labeled btn btn-sm btn-success"><span class="btn-label"><i class="fa fa-plus-square-o fa-fw fa-lg"></i></span>Tambah Dokumen</button>
              </div>
              <table id='tblDokumen' class="table display table-striped table-bordered table-responsive" cellspacing="0" width="100%">
                  <thead>
                      <tr>
                          <th width="10px" style="text-align: center; vertical-align:middle"></th>
                          <th width="5%" style="text-align: center; vertical-align:middle">No Urut</th>
                          <th width="10%" style="text-align: center; vertical-align:middle">Jenis Dokumen</th>
                          <th width="5%" style="text-align: center; vertical-align:middle">Perubahan ke</th>
                          <th style="text-align: center; vertical-align:middle">Nomor Dokumen</th>
                          <th width="15%" style="text-align: center; vertical-align:middle">Tanggal Dokumen</th>
                          <th width="10%" style="text-align: center; vertical-align:middle">Status Dokumen</th>
                          <th width="50px" style="text-align: center; vertical-align:middle">Aksi</th>
                      </tr>
                  </thead>
                  <tbody>
                  </tbody>
            </table>
            </div>

            <div role="tabpanel" class="tab-pane hidden" id="visi">
              <br>
              <form class="form-horizontal" autocomplete='off' method="post">
                <div class="form-group">
                  <label for="txt_no_perda" class="col-xs-2 text-left">Nomor Perda :</label>
                  <div class="col-xs-4">
                    <p class=""><span id="no_perda_rpjmd"></span></p>
                  </div>
                    <div class="btn-group">
                  <button type="button" class="btn btn-primary dropdown-toggle btn-labeled" aria-haspopup="true" aria-expanded="false" data-toggle="dropdown"><span class="btn-label"><i class="fa fa-print fa-fw fa-lg"></i></span>Cetak RPJMD <span class="caret"></span></button>
                        <ul class="dropdown-menu">
                            <li>
                              <a class="dropdown-item btnPrintRPJMDTSK" ><i class="fa fa-print fa-fw fa-lg text-success"></i> Cetak RPJMD </a> 
                            </li>
                            <li>
                              <a class="dropdown-item btnPrintProgPrio" ><i class="fa fa-print fa-fw fa-lg text-danger"></i> Cetak Program Prioritas</a>
                            </li>                  
                        </ul>
                </div>
                </div>
                <div class="form-group">
                  <label for="txt_tgl_perda" class="col-xs-2" align='left'>Tanggal Perda :</label>
                  <div class="col-xs-6">
                    <p class=""><span id="tgl_perda_rpjmd"></span></p>
                  </div>
                </div>
                <div class="form-group">
                  <label for="txt_periode" class="col-xs-2" align='left'>Periode RPJMD :</label>
                  <div class="col-xs-6">
                    <p class=""><span id="periode_awal_rpjmd"></span> sampai dengan <span id="periode_akhir_rpjmd"></span></p>
                  </div>
                  
                </div>

              </form>
              <br>
              <table id='tblVisi' class="table display table-striped table-bordered table-responsive" cellspacing="0" width="100%">
                  <thead>
                      <tr>
                          <th width="5%" style="text-align: center; vertical-align:middle">No Visi</th>
                          <th style="text-align: center; vertical-align:middle">Uraian Visi</th>
                          <th width="50px" style="text-align: center; vertical-align:middle">Aksi</th>
                      </tr>
                  </thead>
                  <tbody>
                  </tbody>
            </table>
            </div>

            <div role="tabpanel" class="tab-pane" id="misi">
               <br>
                  <div class="add">
                    <button class="btnAddMisi btn-labeled btn btn-sm btn-success"><span class="btn-label"><i class="fa fa-plus-square-o fa-fw fa-lg"></i></span>Tambah Misi</button>
              </div>
              <br>
              <table id="tblMisi" class="table display table-striped table-bordered table-responsive" cellspacing="0" width="100%">
                  <thead>
                      <tr>
                          <th width="5%" style="text-align: center; vertical-align:middle">No Visi</th>
                          <th width="5%" style="text-align: center; vertical-align:middle">No Misi</th>
                          <th style="text-align: center; vertical-align:middle">Uraian Misi</th>
                          <th width="50px" style="text-align: center; vertical-align:middle">Aksi</th>

                      </tr>
                  </thead>
                  <tbody>
                  </tbody>
            </table>
            </div>

            <div role="tabpanel" class="tab-pane" id="tujuan">
              <br>
                  <div class="add">
                    <button class="btnAddTujuan btn-labeled btn btn-sm btn-success"><span class="btn-label"><i class="fa fa-plus-square-o fa-fw fa-lg"></i></span>Tambah Tujuan</button>
              </div>
            <br>
            <table id="tblTujuan" class="table display table-striped table-bordered table-responsive"  cellspacing="0" width="100%">
                  <thead>
                      <tr>
                        <th width="3%" style="text-align: center; vertical-align:middle"></th>
                        <th width="5%" style="text-align: center; vertical-align:middle">No Visi</th>
                        <th width="5%" style="text-align: center; vertical-align:middle">No Misi</th>
                        <th width="5%" style="text-align: center; vertical-align:middle">No Tujuan</th>
                        <th style="text-align: center; vertical-align:middle">Uraian Tujuan</th>
                        <th width="5%" style="text-align: center; vertical-align:middle">Jumlah Indikator</th>
                        <th width="50px" style="text-align: center; vertical-align:middle">Aksi</th>
                      </tr>
                  </thead>
                  <tbody>
                  </tbody>
            </table>
            </div>

            <div role="tabpanel" class="tab-pane" id="sasaran">
              <br>
                  <div class="add">
                    <button class="btnAddSasaran btn-labeled btn btn-sm btn-success"><span class="btn-label"><i class="fa fa-plus-square-o fa-fw fa-lg"></i></span>Tambah Sasaran</button>
              </div>
            <br>
            <ul class="nav nav-tabs" role="tablist">
                <li class="active"><a href="#sasaran1" aria-controls="visi" role="tab" data-toggle="tab">Sasaran</a></li>
                <li><a href="#strategi" aria-controls="sasaran" role="tab" data-toggle="tab">Strategi</a></li>
                <li><a href="#kebijakan" aria-controls="tujuan" role="tab" data-toggle="tab">Kebijakan</a></li>
            </ul>

            <div class="tab-content">
              <div role="tabpanel" class="tab-pane active" id="sasaran1">
              <br>
                <table id="tblSasaran" class="table display table-striped table-bordered table-responsive"  cellspacing="0" width="100%">
                      <thead>
                          <tr>
                            <th width="3%" style="text-align: center; vertical-align:middle"></th>
                            <th width="5%" style="text-align: center; vertical-align:middle">No Visi</th>
                            <th width="5%" style="text-align: center; vertical-align:middle">No Misi</th>
                            <th width="5%" style="text-align: center; vertical-align:middle">No Tujuan</th>
                            <th width="5%" style="text-align: center; vertical-align:middle">No Sasaran</th>
                            <th style="text-align: center; vertical-align:middle">Uraian Sasaran</th>
                            <th width="5%" style="text-align: center; vertical-align:middle">Jumlah Indikator</th>
                            <th width="50px" style="text-align: center; vertical-align:middle">Aksi</th>
                          </tr>
                      </thead>
                      <tbody>
                      </tbody>
                </table>
              </div>
              <div role="tabpanel" class="tab-pane" id="strategi">
              <br>
                <table id="tblStrategi" class="table display table-striped table-bordered table-responsive"  cellspacing="0" width="100%">
                      <thead>
                          <tr>
                            <th width="5%" style="text-align: center; vertical-align:middle">No Visi</th>
                            <th width="5%" style="text-align: center; vertical-align:middle">No Misi</th>
                            <th width="5%" style="text-align: center; vertical-align:middle">No Tujuan</th>
                            <th width="5%" style="text-align: center; vertical-align:middle">No Sasaran</th>
                            <th width="10%" style="text-align: center; vertical-align:middle">No Urut</th>
                            <th style="text-align: center; vertical-align:middle">Uraian Strategi</th>
                             <th width="50px" style="text-align: center; vertical-align:middle">Aksi</th>
                          </tr>
                      </thead>
                      <tbody>
                      </tbody>
                </table>
              </div>
              <div role="tabpanel" class="tab-pane" id="kebijakan">
              <br>
                <table id="tblKebijakan" class="table display table-striped table-bordered table-responsive"  cellspacing="0" width="100%">
                      <thead>
                          <tr>
                            <th width="5%" style="text-align: center; vertical-align:middle">No Visi</th>
                            <th width="5%" style="text-align: center; vertical-align:middle">No Misi</th>
                            <th width="5%" style="text-align: center; vertical-align:middle">No Tujuan</th>
                            <th width="5%" style="text-align: center; vertical-align:middle">No Sasaran</th>
                            <th width="5%" style="text-align: center; vertical-align:middle">No Urut</th>
                            <th style="text-align: center; vertical-align:middle">Uraian Kebijakan</th>
                             <th width="50px" style="text-align: center; vertical-align:middle">Aksi</th>
                          </tr>
                      </thead>
                      <tbody>
                      </tbody>
                </table>
              </div>              
            </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="program">
            <br>
            <ul class="nav nav-tabs" role="tablist">
                <li class="active"><a href="#program1" aria-controls="visi" role="tab" data-toggle="tab">Program</a></li>
                <li><a href="#urusan" aria-controls="tujuan" role="tab" data-toggle="tab">Urusan</a></li>
                <li><a href="#pelaksana" aria-controls="sasaran" role="tab" data-toggle="tab">OPD Pelaksana</a></li>
            </ul>
            <div class="tab-content">
              <div role="tabpanel" class="tab-pane active" id="program1">
                <br>
                  <div class="add">
                    <button class="btnAddProgram btn-labeled btn btn-sm btn-success"><span class="btn-label"><i class="fa fa-plus-square-o fa-fw fa-lg"></i></span>Tambah Program</button>
              </div>
              <br>
                <table id="tblProgram" class="table display table-striped table-bordered table-responsive"  cellspacing="0" width="100%">
                  <thead>
                      <tr>
                          <th rowspan="2" width="10px" style="text-align: center; vertical-align:middle"></th>
                          <th rowspan="2" width="50px" style="text-align: center; vertical-align:middle">Kode Sasaran</th>
                          <th rowspan="2" width="50px" style="text-align: center; vertical-align:middle">Kode Program</th>
                          <th rowspan="2" style="text-align: center; vertical-align:middle">Uraian Program</th>
                          <th colspan="6" style="text-align: center; vertical-align:middle">Pagu Program per Tahun (juta rupiah)</th>
                          <th rowspan="2" width="50px" style="text-align: center; vertical-align:middle">Aksi</th>
                      </tr>
                      <tr>
                      <?php $__currentLoopData = $dataperdarpjmd; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $datas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <th width="5%" style="text-align: right"><?php echo e($datas->tahun_1); ?></th>
                          <th width="5%" style="text-align: right"><?php echo e($datas->tahun_2); ?></th>
                          <th width="5%" style="text-align: right"><?php echo e($datas->tahun_3); ?></th>
                          <th width="5%" style="text-align: right"><?php echo e($datas->tahun_4); ?></th>
                          <th width="5%" style="text-align: right"><?php echo e($datas->tahun_5); ?></th>
                          <th width="5%" style="text-align: right">Jumlah</th>
                       <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </tr>
                  </thead>
                  <tbody>
                  </tbody>
              </table>
              </div>
              <div role="tabpanel" class="tab-pane" id="urusan">
              <br>
              <div class="add">
                <button class="add-urbidprog btn-labeled btn btn-sm btn-success"><span class="btn-label"><i class="fa fa-plus-square-o fa-fw fa-lg"></i></span>Tambah Urusan</button>
              </div>
                <table id="tblUrusan" class="table display table-striped table-bordered table-responsive"  cellspacing="0" width="100%">
                      <thead>
                          <tr>
                              <th width="10%" style="text-align: center; vertical-align:middle">Kode Program</th>
                              <th width="10%" style="text-align: center; vertical-align:middle">Kd Urusan</th>
                              <th width="30%" style="text-align: center; vertical-align:middle">Uraian Urusan</th>
                              <th style="text-align: center; vertical-align:middle">Uraian Bidang</th>
                              <th width="50px" style="text-align: center; vertical-align:middle">Aksi</th>
                          </tr>
                      </thead>
                      <tbody>
                      </tbody>
                </table>
              </div>
              <div role="tabpanel" class="tab-pane" id="pelaksana">
              <br>
              <div class="add">
                <button class="add-pelaksanaprog btn-labeled btn btn-sm btn-success"><span class="btn-label"><i class="fa fa-plus-square-o fa-fw fa-lg"></i></span>Tambah Pelaksana</button>
              </div>
                <table id="tblPelaksana" class="table display table-striped table-bordered table-responsive"  cellspacing="0" width="100%">
                      <thead>
                          <tr>
                              <th width="10%" style="text-align: center; vertical-align:middle">Kode Program</th>
                              <th width="10%" style="text-align: center; vertical-align:middle">Kode Unit</th>
                              <th style="text-align: center; vertical-align:middle">Uraian OPD Pelaksana</th>
                              <th width="50px" style="text-align: center; vertical-align:middle">Aksi</th>
                          </tr>
                      </thead>
                      <tbody>
                      </tbody>
                </table>
              </div>
            </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="btl">
              <br>
                  <div class="add">
                    <button class="btnAddBtl btn-labeled btn btn-sm btn-success"><span class="btn-label"><i class="fa fa-plus-square-o fa-fw fa-lg"></i></span>Tambah Belanja Non Program</button>
              </div>
            <br>
            <ul class="nav nav-tabs" role="tablist">
                <li class="active"><a href="#uraianBtl" aria-controls="visi" role="tab" data-toggle="tab">Belanja</a></li>
                <li><a href="#urusanBtl" aria-controls="urusanBtl" role="tab" data-toggle="tab">Urusan</a></li>
                <li><a href="#pelaksanaBtl" aria-controls="pelaksanaBtl" role="tab" data-toggle="tab">OPD Pelaksana</a></li>
            </ul>
            <div class="tab-content">
              <div role="tabpanel" class="tab-pane active" id="uraianBtl">
              <br>
                <table id="tblBtl" class="table display table-striped table-bordered table-responsive"  cellspacing="0" width="100%">
                  <thead>
                      <tr>
                          <th rowspan="2" width="10px" style="text-align: center; vertical-align:middle"></th>
                          <th rowspan="2" style="text-align: center; vertical-align:middle">Uraian Belanja Tidak Langsung</th>
                          <th colspan="6" style="text-align: center; vertical-align:middle">Pagu Belanja per Tahun (juta rupiah)</th>
                          <th rowspan="2" width="50px" style="text-align: center; vertical-align:middle">Aksi</th>
                      </tr>
                      <tr>
                      <?php $__currentLoopData = $dataperdarpjmd; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $datas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <th width="10%" style="text-align: center"><?php echo e($datas->tahun_1); ?></th>
                          <th width="10%" style="text-align: center"><?php echo e($datas->tahun_2); ?></th>
                          <th width="10%" style="text-align: center"><?php echo e($datas->tahun_3); ?></th>
                          <th width="10%" style="text-align: center"><?php echo e($datas->tahun_4); ?></th>
                          <th width="10%" style="text-align: center"><?php echo e($datas->tahun_5); ?></th>
                          <th width="10%" style="text-align: center">Jumlah</th>
                       <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </tr>
                  </thead>
                  <tbody>
                  </tbody>
              </table>
              </div>
              <div role="tabpanel" class="tab-pane" id="urusanBtl">
              <br>
              <div class="add">
                <button class="add-urbidbtl btn-labeled btn btn-sm btn-success"><span class="btn-label"><i class="fa fa-plus-square-o fa-fw fa-lg"></i></span>Tambah Urusan</button>
              </div>
                <table id="tblUrusanBtl" class="table display table-striped table-bordered table-responsive"  cellspacing="0" width="100%">
                      <thead>
                          <tr>
                              <th width="10%" style="text-align: center; vertical-align:middle">Kd Urusan</th>
                              <th width="30%" style="text-align: center; vertical-align:middle">Uraian Urusan</th>
                              <th style="text-align: center; vertical-align:middle">Uraian Bidang</th>
                              <th width="50px" style="text-align: center; vertical-align:middle">Aksi</th>
                          </tr>
                      </thead>
                      <tbody>
                      </tbody>
                </table>
              </div>
              <div role="tabpanel" class="tab-pane" id="pelaksanaBtl">
              <br>
              <div class="add">
                <button class="add-pelaksanabtl btn-labeled btn btn-sm btn-success"><span class="btn-label"><i class="fa fa-plus-square-o fa-fw fa-lg"></i></span>Tambah Pelaksana</button>
              </div>
                <table id="tblPelaksanaBtl" class="table display table-striped table-bordered table-responsive"  cellspacing="0" width="100%">
                      <thead>
                          <tr>
                              <th width="10%" style="text-align: center; vertical-align:middle">Kode Urusan</th>
                              <th width="10%" style="text-align: center; vertical-align:middle">Kode Unit</th>
                              <th style="text-align: center; vertical-align:middle">Uraian OPD Pelaksana</th>
                              <th width="50px" style="text-align: center; vertical-align:middle">Aksi</th>
                          </tr>
                      </thead>
                      <tbody>
                      </tbody>
                </table>
              </div>
            </div>
            </div>

            <div role="tabpanel" class="tab-pane" id="pendapatan">
              <br>
                  <div class="add">
                    <button class="btnAddPdt btn-labeled btn btn-sm btn-success"><span class="btn-label"><i class="fa fa-plus-square-o fa-fw fa-lg"></i></span>Tambah Pendapatan</button>
              </div>
            <br>
            <ul class="nav nav-tabs" role="tablist">
                <li class="active"><a href="#sumberdana" aria-controls="visi" role="tab" data-toggle="tab">Sumber Dana</a></li>
                <li><a href="#urusanpdt" aria-controls="urusanpdt" role="tab" data-toggle="tab">Urusan</a></li>
                <li><a href="#pelaksanapdt" aria-controls="pelaksanapdt" role="tab" data-toggle="tab">OPD Pelaksana</a></li>
            </ul>
            <div class="tab-content">
              <div role="tabpanel" class="tab-pane active" id="sumberdana">
              <br>
                <table id="tblPendapatan" class="table display table-striped table-bordered table-responsive"  cellspacing="0" width="100%">
                  <thead>
                      <tr>
                          <th rowspan="2" width="10px" style="text-align: center; vertical-align:middle"></th>
                          <th rowspan="2" style="text-align: center; vertical-align:middle">Uraian Sumber data</th>
                          <th colspan="6" style="text-align: center; vertical-align:middle">Kerangka Pendanaan per Tahun (juta rupiah)</th>
                          <th rowspan="2" width="50px" style="text-align: center; vertical-align:middle">Aksi</th>
                      </tr>
                      <tr>
                      <?php $__currentLoopData = $dataperdarpjmd; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $datas): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                          <th width="10%" style="text-align: center"><?php echo e($datas->tahun_1); ?></th>
                          <th width="10%" style="text-align: center"><?php echo e($datas->tahun_2); ?></th>
                          <th width="10%" style="text-align: center"><?php echo e($datas->tahun_3); ?></th>
                          <th width="10%" style="text-align: center"><?php echo e($datas->tahun_4); ?></th>
                          <th width="10%" style="text-align: center"><?php echo e($datas->tahun_5); ?></th>
                          <th width="10%" style="text-align: center">Jumlah</th>
                       <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                      </tr>
                  </thead>
                  <tbody>
                  </tbody>
              </table>
              </div>
              <div role="tabpanel" class="tab-pane" id="urusanpdt">
              <br>
              <div class="add">
                <button class="add-urbidpdt btn-labeled btn btn-sm btn-success"><span class="btn-label"><i class="fa fa-plus-square-o fa-fw fa-lg"></i></span>Tambah Urusan</button>
              </div>
                <table id="tblUrusanPdt" class="table display table-striped table-bordered table-responsive"  cellspacing="0" width="100%">
                      <thead>
                          <tr>
                              <th width="10%" style="text-align: center; vertical-align:middle">Kd Urusan</th>
                              <th width="30%" style="text-align: center; vertical-align:middle">Uraian Urusan</th>
                              <th style="text-align: center; vertical-align:middle">Uraian Bidang</th>
                              <th width="50px" style="text-align: center; vertical-align:middle">Aksi</th>
                          </tr>
                      </thead>
                      <tbody>
                      </tbody>
                </table>
              </div>
              <div role="tabpanel" class="tab-pane" id="pelaksanapdt">
              <br>
              <div class="add">
                <button class="add-pelaksanapdt btn-labeled btn btn-sm btn-success"><span class="btn-label"><i class="fa fa-plus-square-o fa-fw fa-lg"></i></span>Tambah Pelaksana</button>
              </div>
                <table id="tblPelaksanaPdt" class="table display table-striped table-bordered table-responsive"  cellspacing="0" width="100%">
                      <thead>
                          <tr>
                              <th width="10%" style="text-align: center; vertical-align:middle">Kode Urusan</th>
                              <th width="10%" style="text-align: center; vertical-align:middle">Kode Unit</th>
                              <th style="text-align: center; vertical-align:middle">Uraian OPD Pelaksana</th>
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
  </div>
</div>

<script id="details-inDokumen" type="text/x-handlebars-template">
  <table class="table table-striped display table-bordered table-responsive compact details-table" id="inDokumen-{{id_rpjmd}}">
      <thead>
        <tr>
          <th width="5%" style="text-align: center; vertical-align:middle">No Urut</th>
          <th style="text-align: center; vertical-align:middle">Uraian Visi RPJMD</th>
          <th width="10%" style="text-align: center; vertical-align:middle">Aksi</th>
        </tr>
      </thead>
      <tbody>        
      </tbody>
  </table>
</script>

<script id="details-inTujuan" type="text/x-handlebars-template">
  <table class="table table-striped display table-bordered table-responsive compact details-table" id="inTujuan-{{id_tujuan_rpjmd}}">
      <thead>
        <tr>
          <th width="5%" style="text-align: center; vertical-align:middle">No Urut</th>
          <th style="text-align: center; vertical-align:middle">Uraian Indikator</th>
          <th width="10%" style="text-align: center; vertical-align:middle">Aksi</th>
        </tr>
      </thead>
      <tbody>        
      </tbody>
  </table>
</script>

<script id="details-inSasaran" type="text/x-handlebars-template">
  <table class="table table-striped display table-bordered table-responsive compact details-table" id="inSasaran-{{id_sasaran_rpjmd}}">
      <thead>
        <tr>
          <th width="5%" style="text-align: center; vertical-align:middle">No Urut</th>
          <th style="text-align: center; vertical-align:middle">Uraian Indikator</th>
          <th width="10%" style="text-align: center; vertical-align:middle">Aksi</th>
        </tr>
      </thead>
      <tbody>        
      </tbody>
  </table>
</script>

<script id="details-inProgram" type="text/x-handlebars-template">
  <table class="table table-striped display table-bordered table-responsive compact details-table" id="inProgram-{{id_program_rpjmd}}">
      <thead>
        <tr>
          <th width="5%" style="text-align: center; vertical-align:middle">No Urut</th>
          <th style="text-align: center; vertical-align:middle">Uraian Indikator</th>
          <th width="10%" style="text-align: center; vertical-align:middle">Aksi</th>
        </tr>
      </thead>
      <tbody>        
      </tbody>
  </table>
</script>


<div id="EditVisi" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg"  >
      <div class="modal-content">
        <div class="modal-header">
          
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" role="form" autocomplete='off' action="" method="post" >
              <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
              <input type="hidden" class="form-control" id="id_visi_rpjmd_edit" name="id_visi_rpjmd_edit">
              <input type="hidden" class="form-control" id="id_rpjmd_edit" name="id_rpjmd_edit">
              <input type="hidden" class="form-control" id="thn_id_edit" name="thn_id_edit">
              <div class="form-group">
                <label for="thn_periode_visi" class="col-sm-3 control-label" align='left'>Periode RPJMD :</label>
                <div class="col-sm-7">
                  <input type="text" class="form-control" id="thn_periode_visi" name="thn_periode_visi" required="required" readonly>
                </div>
              </div>
              <div class="form-group">
                <label for="no_urut_edit" class="col-sm-3 control-label" align='left'>Nomor Urut :</label>
                <div class="col-sm-2">
                <div class="input-group">
                  <input type="text" class="form-control number" id="no_urut_edit" name="no_urut_edit" required="required" style="text-align:center;">                  
                </div>
                </div>
              </div>
              <div class="form-group">
                <label for="id_perubahan_edit" class="col-sm-3 control-label" align='left'>ID Perubahan :</label>
                <div class="col-sm-2">
                <div class="input-group">
                  <input type="text" class="form-control number" id="id_perubahan_edit" name="id_perubahan_edit" required="required" 
                  style="text-align:center;">             
                </div>
                </div>
              </div>
              <div class="form-group">
                <label for="ur_visi_rpjmd_edit" class="col-sm-3 control-label" align='left'>Uraian Visi RPJMD :</label>
                <div class="col-sm-8">
                  <textarea type="text" class="form-control" rows="5" id="ur_visi_rpjmd_edit" name="ur_visi_rpjmd_edit" required="required" ></textarea>
                </div>
              </div>
          </form>
        </div>
        <div class="modal-footer">
                <div class="row">
                    <div class="col-sm-2 text-left">
                    <button type="button" id="btnDelVisi" class="btn btn-labeled btn-danger btnDelVisi"><span class="btn-label"><i class="fa fa-trash-o fa-lg fa-fw"></i></span>Hapus</button>
                </div>
                    <div class="col-sm-10 text-right">
                      <div class="ui-group-buttons">
                        <button type="button" id="btnSimpanVisi" class="btn btn-success btnSimpanVisi btn-labeled" data-dismiss="modal">
                            <span class="btn-label"><i class="glyphicon glyphicon-save"></i></span>Simpan</button>
                        <div class="or hidden"></div>
                        <button type="button" class="btn btn-warning btn-labeled" data-dismiss="modal" aria-hidden="true">
                            <span class="btn-label"><i class="glyphicon glyphicon-log-out"></i></span>Tutup</button>
                      </div>
                    </div>
                </div>
              </div>
      </div>
    </div>
</div>

<div id="EditMisi" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg"  >
      <div class="modal-content">
        <div class="modal-header">
          
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" role="form" autocomplete='off' action="" method="post" >
              <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
              <input type="hidden" class="form-control" id="id_misi_rpjmd_edit" name="id_misi_rpjmd_edit" readonly >
              <input type="hidden" class="form-control" id="thn_id_misi_edit" name="thn_id_edit" readonly >
              <div class="form-group">
                <label for="id_visi_rpjmd_edit" class="col-sm-3 control-label" align='left'>ID Visi RPJMD :</label>
                <div class="col-sm-2">
                <div class="input-group">
                  <input type="text" class="form-control" id="id_visi_rpjmd_misi_edit" name="id_visi_rpjmd_edit" readonly >                  
                </div>
                </div>
              </div>
              <div class="form-group">
                <label for="no_urut_edit" class="col-sm-3 control-label" align='left'>Nomor Urut :</label>
                <div class="col-sm-2">
                <div class="input-group">
                  <input type="text" class="form-control" id="no_urut_misi_edit" name="no_urut_misi_edit" required="required" readonly>                  
                </div>
                </div>
              </div>
              <div class="form-group">
                <label for="id_perubahan_edit" class="col-sm-3 control-label" align='left'>ID Perubahan :</label>
                <div class="col-sm-2">
                <div class="input-group">
                  <input type="text" class="form-control" id="id_perubahan_misi_edit" name="id_perubahan_edit" required="required" readonly >                  
                </div>
                </div>
              </div>
              <div class="form-group">
                <label for="ur_misi_rpjmd_edit" class="col-sm-3 control-label" align='left'>Uraian Misi RPJMD :</label>
                <div class="col-sm-8">
                  <textarea type="text" class="form-control" rows="5" id="ur_misi_rpjmd_edit" name="ur_misi_rpjmd_edit" required="required" readonly ></textarea>
                </div>
              </div>
          </form>
        </div>
        <div class="modal-footer">
                <div class="row">
                    <div class="col-sm-2 text-left">
                    </div>
                    <div class="col-sm-10 text-right">
                      <div class="ui-group-buttons">
                        <button type="button" class="btn btn-success actionBtn_misi btn-labeled hidden" data-dismiss="modal">
                            <span class="btn-label"><i class="glyphicon glyphicon-save"></i></span>Simpan</button>
                        <div class="or hidden"></div>
                        <button type="button" class="btn btn-warning btn-labeled" data-dismiss="modal" aria-hidden="true">
                            <span class="btn-label"><i class="glyphicon glyphicon-log-out"></i></span>Tutup</button>
                      </div>
                    </div>
                </div>
              </div>
      </div>
    </div>
  </div>

<div id="EditTujuan" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg"  >
      <div class="modal-content">
        <div class="modal-header">
          
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" role="form" autocomplete='off' action="" method="post" >
              <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
              <input type="hidden" class="form-control" id="id_tujuan_rpjmd_edit" name="id_tujuan_rpjmd_edit" readonly >
              <input type="hidden" class="form-control" id="thn_id_tujuan_edit" name="thn_id_edit" required="required" >
              <input type="hidden" class="form-control" id="id_misi_rpjmd_tujuan_edit" name="id_misi_rpjmd_edit" required="required">
              <div class="form-group">
                <label for="id_misi_rpjmd_edit" class="col-sm-3 control-label" align='left'>ID Misi RPJMD :</label>
                <div class="col-sm-6">
                  <div class="input-group">
                    <input type="text" class="form-control" id="id_misi_tujuan_edit" name="id_misi_tujuan_edit" readonly >
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="no_urut_edit" class="col-sm-3 control-label" align='left'>Nomor Urut :</label>
                <div class="col-sm-2">
                <div class="input-group">
                  <input type="text" class="form-control" id="no_urut_tujuan_edit" name="no_urut_tujuan_edit" required="required"  readonly >                  
                </div>
                </div>
              </div>
              <div class="form-group">
                <label for="id_perubahan_edit" class="col-sm-3 control-label" align='left'>ID Perubahan :</label>
                <div class="col-sm-2">
                <div class="input-group">
                  <input type="text" class="form-control" id="id_perubahan_tujuan_edit" name="id_perubahan_edit" required="required"  readonly >
                  
                </div>
                </div>
              </div>
              <div class="form-group">
                <label for="ur_tujuan_rpjmd_edit" class="col-sm-3 control-label" align='left'>Uraian tujuan RPJMD :</label>
                <div class="col-sm-8">
                  <textarea type="text" class="form-control" rows="5" id="ur_tujuan_rpjmd_edit" name="ur_tujuan_rpjmd_edit" required="required"  readonly ></textarea>
                </div>
              </div>
          </form>
        </div>
        <div class="modal-footer">
                <div class="row">
                    <div class="col-sm-2 text-left">
                    </div>
                    <div class="col-sm-10 text-right">
                      <div class="ui-group-buttons">
                        <button type="button" class="btn btn-success actionBtn_tujuan btn-labeled hidden" data-dismiss="modal">
                            <span class="btn-label"><i class="glyphicon glyphicon-save"></i></span>Simpan</button>
                        <div class="or hidden"></div>
                        <button type="button" class="btn btn-warning btn-labeled" data-dismiss="modal" aria-hidden="true">
                            <span class="btn-label"><i class="glyphicon glyphicon-log-out"></i></span>Tutup</button>
                      </div>
                    </div>
                </div>
              </div>
      </div>
    </div>
  </div>

<?php echo $__env->make('rpjmd.FrmRpjmdTujuanIndikator', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<div id="EditSasaranModal" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg"  >
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" role="form" autocomplete='off' action="" method="post" >
              <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
              <input type="hidden" class="form-control" id="id_sasaran_rpjmd_edit" name="id_sasaran_rpjmd_edit">
              <input type="hidden" class="form-control" id="thn_id_sasaran_edit" name="thn_id_edit">
              <input type="hidden" class="form-control" id="id_tujuan_rpjmd_sasaran_edit" name="id_tujuan_rpjmd_sasaran_edit">
              <div class="form-group">
                <label class="control-label col-sm-3" for="id_sasaran_rpjmd_edit">ID Tujuan RPJMD :</label>
                <div class="col-sm-2">
                    <input type="text" class="form-control" id="id_tujuan_sasaran_edit" name="id_tujuan_sasaran_edit" readonly>
                </div>
              </div>
              <div class="form-group">
                <label for="no_urut_edit" class="col-sm-3 control-label" align='left'>Nomor Urut :</label>
                <div class="col-sm-2">
                <div class="input-group">
                  <input type="text" class="form-control" id="no_urut_sasaran_edit" name="no_urut_sasaran_edit" required="required" >                  
                </div>
                </div>
              </div>
              <div class="form-group">
                <label for="id_perubahan_edit" class="col-sm-3 control-label" align='left'>ID Perubahan :</label>
                <div class="col-sm-2">
                <div class="input-group">
                  <input type="text" class="form-control" id="id_perubahan_sasaran_edit" name="id_perubahan_edit" required="required" >                  
                </div>
                </div>
              </div>
              <div class="form-group">
                <label for="ur_sasaran_rpjmd_edit" class="col-sm-3 control-label" align='left'>Uraian sasaran RPJMD :</label>
                <div class="col-sm-8">
                  <textarea type="text" class="form-control" rows="5" id="ur_sasaran_rpjmd_edit" name="ur_sasaran_rpjmd_edit" required="required" ></textarea>
                </div>
              </div>
          </form>
        </div>
        <div class="modal-footer">
                <div class="row">
                    <div class="col-sm-2 text-left">
                    </div>
                    <div class="col-sm-10 text-right">
                      <div class="ui-group-buttons">
                        <button type="button" class="btn btn-success actionBtn_sasaran btn-labeled hidden" data-dismiss="modal">
                            <span class="btn-label"><i class="glyphicon glyphicon-save"></i></span>Simpan</button>
                        <div class="or hidden"></div>
                        <button type="button" class="btn btn-warning btn-labeled" data-dismiss="modal" aria-hidden="true">
                            <span class="btn-label"><i class="glyphicon glyphicon-log-out"></i></span>Tutup</button>
                      </div>
                    </div>
                </div>
              </div>
      </div>
    </div>
  </div>

<?php echo $__env->make('rpjmd.FrmRpjmdSasaranIndikator', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

<div id="Editkebijakan" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg"  >
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" role="form" autocomplete='off' action="" method="post" >
              <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
              <input type="hidden" class="form-control" id="id_kebijakan_rpjmd_edit" name="id_kebijakan_rpjmd_edit" readonly >
              <input type="hidden" class="form-control" id="thn_id_kebijakan_edit" name="thn_id_edit" readonly >
              <input type="hidden" class="form-control" id="id_sasaran_rpjmd_kebijakan_edit" name="id_sasaran_rpjmd_kebijakan_edit" readonly >
              <div class="form-group">
                <label class="control-label col-sm-3" for="id_kebijakan_rpjmd_edit">ID Sasaran RPJMD :</label>
                <div class="col-sm-2">
                  <div class="input-group">
                    <input type="text" class="form-control" id="id_sasaran_kebijakan_edit" name="id_sasaran_kebijakan_edit" readonly >
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="no_urut_edit" class="col-sm-3 control-label" align='left'>Nomor Urut :</label>
                <div class="col-sm-2">
                <div class="input-group">
                  <input type="text" class="form-control" id="no_urut_kebijakan_edit" name="no_urut_kebijakan_edit" required="required" >                  
                </div>
                </div>
              </div>
              <div class="form-group">
                <label for="id_perubahan_edit" class="col-sm-3 control-label" align='left'>ID Perubahan :</label>
                <div class="col-sm-2">
                <div class="input-group">
                  <input type="text" class="form-control" id="id_perubahan_kebijakan_edit" name="id_perubahan_edit" required="required" >                  
                </div>
                </div>
              </div>
              <div class="form-group">
                <label for="ur_kebijakan_rpjmd_edit" class="col-sm-3 control-label" align='left'>Uraian kebijakan RPJMD :</label>
                <div class="col-sm-8">
                  <textarea type="text" class="form-control" rows="5" id="ur_kebijakan_rpjmd_edit" name="ur_kebijakan_rpjmd_edit" required="required" ></textarea>
                </div>
              </div>
          </form>
        </div>
        <div class="modal-footer">
                <div class="row">
                    <div class="col-sm-2 text-left">
                    </div>
                    <div class="col-sm-10 text-right">
                      <div class="ui-group-buttons">
                        <button type="button" class="btn btn-success actionBtn_kebijakan btn-labeled hidden" data-dismiss="modal">
                            <span class="btn-label"><i class="glyphicon glyphicon-save"></i></span>Simpan</button>
                        <div class="or hidden"></div>
                        <button type="button" class="btn btn-warning btn-labeled" data-dismiss="modal" aria-hidden="true">
                            <span class="btn-label"><i class="glyphicon glyphicon-log-out"></i></span>Tutup</button>
                      </div>
                    </div>
                </div>
              </div>
      </div>
    </div>
  </div>

<div id="Editstrategi" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg"  >
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" role="form" autocomplete='off' action="" method="post" >
              <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
              <input type="hidden" class="form-control" id="id_strategi_rpjmd_edit" name="id_strategi_rpjmd_edit" readonly >
              <input type="hidden" class="form-control" id="thn_id_strategi_edit" name="thn_id_edit" readonly >
              <input type="hidden" class="form-control" id="id_sasaran_rpjmd_strategi_edit" name="id_sasaran_rpjmd_edit" readonly >
              <div class="form-group">
                <label class="control-label col-sm-3" for="id_strategi_rpjmd_edit">ID strategi RPJMD :</label>
                <div class="col-sm-6">
                  <div class="input-group">
                    <input type="text" class="form-control" id="id_sasaran_strategi_edit" name="id_sasaran_strategi_edit" readonly >
                  </div>
                </div>
              </div>
              <div class="form-group">
                <label for="no_urut_edit" class="col-sm-3 control-label" align='left'>Nomor Urut :</label>
                <div class="col-sm-2">
                <div class="input-group">
                  <input type="text" class="form-control" id="no_urut_strategi_edit" name="no_urut_strategi_edit" required="required" >                  
                </div>
                </div>
              </div>
              <div class="form-group">
                <label for="id_perubahan_edit" class="col-sm-3 control-label" align='left'>ID Perubahan :</label>
                <div class="col-sm-2">
                <div class="input-group">
                  <input type="text" class="form-control" id="id_perubahan_strategi_edit" name="id_perubahan_edit" required="required" >                  
                </div>
                </div>
              </div>
              <div class="form-group">
                <label for="ur_strategi_rpjmd_edit" class="col-sm-3 control-label" align='left'>Uraian strategi RPJMD :</label>
                <div class="col-sm-8">
                  <textarea type="text" class="form-control" rows="5" id="ur_strategi_rpjmd_edit" name="ur_strategi_rpjmd_edit" required="required" ></textarea>
                </div>
              </div>
          </form>
        </div>
        <div class="modal-footer">
                <div class="row">
                    <div class="col-sm-2 text-left">
                    </div>
                    <div class="col-sm-10 text-right">
                      <div class="ui-group-buttons">
                        <button type="button" class="btn btn-success actionBtn_strategi btn-labeled hidden" data-dismiss="modal">
                            <span class="btn-label"><i class="glyphicon glyphicon-save"></i></span>Simpan</button>
                        <div class="or hidden"></div>
                        <button type="button" class="btn btn-warning btn-labeled" data-dismiss="modal" aria-hidden="true">
                            <span class="btn-label"><i class="glyphicon glyphicon-log-out"></i></span>Tutup</button>
                      </div>
                    </div>
                </div>
              </div>
      </div>
    </div>
  </div>

<div id="Editprogram" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg"  >
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" role="form" autocomplete='off' action="" method="post" >
              <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
              <input type="hidden" class="form-control" id="id_program_rpjmd_edit" name="id_program_rpjmd_edit" readonly >
              <input type="hidden" class="form-control" id="thn_id_program_edit" name="thn_id_edit" readonly>
              <input type="hidden" class="form-control" id="id_sasaran_rpjmd_program_edit" name="id_sasaran_rpjmd_edit" readonly>
              <div class="form-group">
                <label for="id_sasaran_rpjmd_edit" class="col-sm-3 control-label" align='left'>ID Sasaran RPJMD :</label>
                <div class="col-sm-2">
                <div class="input-group">
                  <input type="text" class="form-control" id="id_sasaran_program_edit" name="id_sasaran_program_edit" readonly>                  
                </div>
                </div>
              </div>
              <div class="form-group">
                <label for="no_urut_edit" class="col-sm-3 control-label" align='left'>Nomor Urut :</label>
                <div class="col-sm-2">
                <div class="input-group">
                  <input type="text" class="form-control" id="no_urut_program_edit" name="no_urut_program_edit" required="required" >                  
                </div>
                </div>
              </div>              
              <div class="form-group">
                <label for="id_perubahan_edit" class="col-sm-3 control-label" align='left'>ID Perubahan :</label>
                <div class="col-sm-2">
                <div class="input-group">
                  <input type="text" class="form-control" id="id_perubahan_program_edit" name="id_perubahan_edit" required="required" >                  
                </div>
                </div>
              </div>
              <div class="form-group">
                <label for="ur_program_rpjmd_edit" class="col-sm-3 control-label" align='left'>Uraian program RPJMD :</label>
                <div class="col-sm-8">
                  <textarea type="text" class="form-control" rows="5" id="ur_program_rpjmd_edit" name="ur_program_rpjmd_edit" required="required" ></textarea>
                </div>
              </div>
              <label class="col-sm-12" style="text-align: left;">Rincian Pagu Program RPJMD :</label>
              <br>
              <table id="tblPaguProgram" class="table table-bordered"  cellspacing="0" width="100%">
                      <thead style="background: #428bca; color: #fff">
                          <tr>
                            <th width="20%" style="text-align: center; vertical-align:middle">Pagu Tahun 1</th>
                            <th width="20%" style="text-align: center; vertical-align:middle">Pagu Tahun 2</th>
                            <th width="20%" style="text-align: center; vertical-align:middle">Pagu Tahun 3</th>
                            <th width="20%" style="text-align: center; vertical-align:middle">Pagu Tahun 4</th>
                            <th width="20%" style="text-align: center; vertical-align:middle">Pagu Tahun 5</th>
                          </tr>
                      </thead>
                      <tbody>
                        <tr>
                            <td width="20%" style="text-align: center; vertical-align:middle">
                              <input type="text" class="form-control number" id="pagu1_edit" name="pagu1_edit" style="text-align: right; ">
                            </td>
                            <td width="20%" style="text-align: center; vertical-align:middle">
                              <input type="text" class="form-control number" id="pagu2_edit" name="pagu2_edit" style="text-align: right; " >
                            </td>
                            <td width="20%" style="text-align: center; vertical-align:middle">
                              <input type="text" class="form-control number" id="pagu3_edit" name="pagu3_edit" style="text-align: right; " >
                            </td>
                            <td width="20%" style="text-align: center; vertical-align:middle">
                              <input type="text" class="form-control number" id="pagu4_edit" name="pagu4_edit" style="text-align: right; " >
                            </td>
                            <td width="20%" style="text-align: center; vertical-align:middle">
                              <input type="text" class="form-control number" id="pagu5_edit" name="pagu5_edit" style="text-align: right; " >
                            </td>
                          </tr>
                          <tr>
                            <td colspan="3" style="text-align: center; vertical-align:middle; font-weight: bold;">Pagu Total :
                            </td>
                            <td colspan="2" style="text-align: center; vertical-align:middle">
                              <input type="text" class="form-control number" id="pagu_total_edit" name="pagu_total_edit" style="text-align: right;" readonly>
                            </td>
                          </tr>
                      </tbody>
                </table>
          </form>
        </div>
        <div class="modal-footer">
                <div class="row">
                    <div class="col-sm-2 text-left">
                    </div>
                    <div class="col-sm-10 text-right">
                      <div class="ui-group-buttons">
                        <button type="button" class="btn btn-success actionBtn_program btn-labeled hidden" data-dismiss="modal">
                            <span class="btn-label"><i class="glyphicon glyphicon-save"></i></span>Simpan</button>
                        <div class="or hidden"></div>
                        <button type="button" class="btn btn-warning btn-labeled" data-dismiss="modal" aria-hidden="true">
                            <span class="btn-label"><i class="glyphicon glyphicon-log-out"></i></span>Tutup</button>
                      </div>
                    </div>
                </div>
              </div>
      </div>
    </div>
  </div>

<?php echo $__env->make('rpjmd.FrmRpjmdProgramIndikator', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>

  <div id="ModalUrusan" class="modal fade" role="dialog" data-backdrop="static">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" role="form" autocomplete='off' action="" method="post" onsubmit="return false;">
            <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
            <input type="hidden" id="id_urbid_rpjmd_edit" name="id_urbid_rpjmd_edit">
            <input type="hidden" id="thn_urbid_rpjmd_edit" name="thn_urbid_rpjmd_edit">
            <input type="hidden" id="no_urbid_rpjmd_edit" name="no_urbid_rpjmd_edit">
            <input type="hidden" id="id_prog_urbid_rpjmd_edit" name="id_prog_urbid_rpjmd_edit">
            <div class="form-group">
              <label class="control-label col-sm-3" for="kd_urusan">Urusan Pemerintahan :</label>
              <div class="col-sm-8">
                <select type="text" class="form-control kd_urusan" id="kd_urusan" name="kd_urusan"></select>
              </div>
              </div>
              <div class="form-group">
              <label class="control-label col-sm-3" for="kd_bidang">Bidang :</label>
              <div class="col-sm-8">
                <select type="text" class="form-control kd_bidang" id="kd_bidang" name="kd_bidang"></select>
              </div>
            </div>
          </form>
        </div>
          <div class="modal-footer">
                <div class="row">
                    <div class="col-sm-12 text-right">
                      <div class="ui-group-buttons">
                         <button type="button" class="btn btn-labeled btn-success btnUrusan" data-dismiss="modal"><span class="btn-label"><i class="fa fa-floppy-o fa-lg fa-fw"></i></span> Simpan</button>
                         <div class="or"></div>
                        <button type="button" class="btn btn-labeled btn-warning" data-dismiss="modal" aria-hidden="true"><span class="btn-label"><i class="fa fa-sign-out fa-lg fa-fw"></i></span> Tutup</button>
                      </div>
                    </div>
                </div>
              </div>
          </div>
        </div>
      </div>

  <div id="HapusUrusan" class="modal fade" role="dialog" tabindex="-1" data-focus-on="input:first" data-backdrop="static">
    <div class="modal-dialog modal-xs">
      <div class="modal-content">
        <div class="modal-header">
            <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
            <input type="hidden" id="id_urusan_rkpd_hapus" name="id_urusan_rkpd_hapus">
            <div class="alert alert-danger">
              <i class="fa fa-exclamation-triangle fa-2x fa-pull-left text-danger"  aria-hidden="true"></i>
                Yakin akan menghapus Bidang : <strong><span class="ur_bidang_del"></span></strong> dalam urusan <strong><span class="ur_urusan_del"></span></strong> ?
          </div>
        </div>
          <div class="modal-footer">
            <div class="ui-group-buttons">
              <button type="button" class="btn btn-labeled btn-danger btnDelUrusan" data-dismiss="modal" ><span class="btn-label"><i class="fa fa-trash fa-lg fa-fw"></i></span> Hapus</button>
              <div class="or"></div>
              <button type="button" class="btn btn-labeled btn-warning" data-dismiss="modal" aria-hidden="true"><span class="btn-label"><i class="fa fa-sign-out fa-lg fa-fw"></i></span> Tutup</button>
            </div>
          </div>
        </div>
      </div>
    </div>


<div id="ModalPelaksana" class="modal fade" role="dialog" tabindex="-1" data-focus-on="input:first" data-backdrop="static">
<div class="modal-dialog modal-lg"  >
<div class="modal-content">
<div class="modal-header">
  <h3 class="modal-title" >Daftar Unit Pelaksana yang akan ditambahkan</h3>
</div>
<div class="modal-body">
<form class="form-horizontal" role="form" autocomplete='off' action="" onsubmit="return false;">
  <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
  <input type="hidden" id="id_urbid_pelaksana" name="id_urbid_pelaksana">
  <input type="hidden" id="thn_pelaksana" name="thn_pelaksana">
  <input type="hidden" id="no_pelaksana" name="no_pelaksana">
  <input type="hidden" id="id_pelaksana_rpjmd" name="id_pelaksana_rpjmd">
  <div class="form-group">
  <div class="col-sm-12">
    <table id='tblUnitPelaksana' class="table display compact table-striped table-bordered" width="100%">
        <thead>
              <tr>
                <th width="5%" style="text-align: center; vertical-align:middle">No Urut</th>
                <th width="85%" style="text-align: center; vertical-align:middle">Nama Unit</th>
                <th width="10%" style="text-align: center; vertical-align:middle">Aksi</th>
              </tr>
        </thead>
        <tbody>
        </tbody>
    </table>
  </div>
  </div>
</form>
</div>
<div class="modal-footer">
    <div class="row">
        <div class="col-sm-2 text-left"></div>
        <div class="col-sm-10 text-right">
            <button type="button" class="btn btn-sm btn-warning btn-labeled" data-dismiss="modal" aria-hidden="true">
                <span class="btn-label"><i class="glyphicon glyphicon-log-out"></i></span>Tutup</button>
        </div>
    </div>
</div> 
</div>
</div>
</div>


<div id="HapusUnitPelaksana" class="modal fade" role="dialog" tabindex="-1" data-focus-on="input:first" data-backdrop="static">
  <div class="modal-dialog modal-xs">
  <div class="modal-content">
    <div class="modal-header">
        <h4 class="modal-title"></h4>
    </div>
    <div class="modal-body">
        <input type="hidden" id="id_pelaksana_hapus" name="id_pelaksana_hapus">
        <input type="hidden" id="no_urut_hapus" name="no_urut_hapus">
        <div class="alert alert-danger">
          <i class="fa fa-exclamation-triangle fa-2x fa-pull-left text-danger"  aria-hidden="true"></i>
            Yakin akan menghapus Unit Pelaksana : <strong><span id="ur_pelaksana_hapus"></span></strong> ?
      </div>
    </div>
      <div class="modal-footer">
        <div class="ui-group-buttons">
          <button type="button" class="btn btn-labeled btn-danger btnDelPelaksana" data-dismiss="modal" ><span class="btn-label"><i class="fa fa-trash fa-lg fa-fw"></i></span> Hapus</button>
            <div class="or"></div>
          <button type="button" class="btn btn-labeled btn-warning" data-dismiss="modal" aria-hidden="true"><span class="btn-label"><i class="fa fa-sign-out fa-lg fa-fw"></i></span> Tutup</button>
        </div>
      </div>
  </div>
  </div>
</div>


<?php echo $__env->make('rpjmd.FrmRpjmdDokumen', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>;


<div id="cariIndikator" class="modal fade" role="dialog" tabindex="-1" data-focus-on="input:first" data-backdrop="static">
    <div class="modal-dialog modal-lg"  >
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h4 class="modal-title judulModal"></h4>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" role="form" autocomplete='off' action="" method="" >
           <div class="form-group">
             <div class="col-sm-12">
                <table id='tblCariIndikator' class="table display table-striped table-bordered" cellspacing="0" width="100%">
                    <thead>
                          <tr>
                            <th width="5%" style="text-align: center; vertical-align:middle">No Urut</th>
                            <th style="text-align: center; vertical-align:middle">Uraian Indikator</th>
                            <th width="10%" style="text-align: center; vertical-align:middle">Satuan</th>
                            <th width="10%" style="text-align: center; vertical-align:middle">Tipe</th>
                            <th width="10%" style="text-align: center; vertical-align:middle">Jenis</th>
                            <th width="10%" style="text-align: center; vertical-align:middle">Sifat</th>
                            <th width="10%" style="text-align: center; vertical-align:middle">Pengukuran</th>
                          </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
              </div>
            </div>
          </form> 
        </div>
          <div class="modal-footer">
                <div class="row">
                    <div class="col-sm-2 text-left idbtnHapusKeg">
                    </div>
                    <div class="col-sm-10 text-right">
                        <button type="button" class="btn btn-warning btn-labeled" data-dismiss="modal" aria-hidden="true">
                            <span class="btn-label"><i class="fa fa-sign-out fa-fw fa-lg"></i></span>Tutup</button>
                    </div>
                </div>
              </div>
      </div>
    </div>
  </div>

  <div id="ModalProgress" class="modal fade modal-static" role="dialog" data-backdrop="static" tabindex="-1" data-focus-on="input:first">
    <div class="modal-dialog"  >
      <div class="modal-content" style="background-color: #5bc0de;">
        <div class="modal-body" style="background-color: #5bc0de;">
          <div style="text-align: center;">
          <h4><strong>Sedang proses...</strong></h4>
            <i class="fa fa-spinner fa-pulse fa-5x fa-fw text-info"></i>
          </div>
        </div>
      </div>
    </div>
</div>



<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script type="text/javascript" language="javascript" class="init" src="<?php echo e(asset('/protected/resources/views/rpjmd/js_rpjmd_final.js')); ?>"> </script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app1', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>