<?php
use hoaaah\LaravelBreadcrumb\Breadcrumb as Breadcrumb;
?>




<?php $__env->startSection('content'); ?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <?php
                $this->title = ' Angka Melek Huruf ';
                $breadcrumb = new Breadcrumb();
                $breadcrumb->homeUrl = '/modul4';
                $breadcrumb->begin();
                $breadcrumb->add(['label' => $this->title]);
                $breadcrumb->end();
            ?>          
        </div>
    </div>
    
    <div role="tabpanel" class="tab-pane fade in active" id="amh">
                  <div class="col-md-12">
                  <div class="add">
                      <button id="btnAddamh" type="button" class="btn btn-labeled btn-sm btn-success"><span class="btn-label"><i class="glyphicon glyphicon-plus"></i></span>Tambah Angka Melek Huruf</button>
                      <button id="btnPrintamh" type="button" class="btn btn-labeled btn-sm btn-primary"><span class="btn-label"><i class="glyphicon glyphicon-print"></i></span>Print Angka Melek Huruf</button>
                  </div>
                  <div class="">
                  <table id="tblamh" class="table table-striped table-bordered table-responsive compact" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th width="5%" style="text-align: center; vertical-align:middle">No</th>
                                <th width="5%" style="text-align: center; vertical-align:middle">Tahun</th>
                                <th width="15%" style="text-align: center; vertical-align:middle">Kecamatan</th>
                                <th width="15%" style="text-align: center; vertical-align:middle">Uraian</th>
                                <th width="10%" style="text-align: center; vertical-align:middle">N-5</th>
                                <th width="10%" style="text-align: center; vertical-align:middle">N-4</th>
                                <th width="10%" style="text-align: center; vertical-align:middle">N-3</th>
                                <th width="10%" style="text-align: center; vertical-align:middle">N-2</th>
                                <th width="10%" style="text-align: center; vertical-align:middle">N-1</th>
                                <th width="10%" style="text-align: center; vertical-align:middle">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>                                        
                        </tbody>
                    </table> 
                    </div> 
                  </div>  
                </div> 
    </div>
 <div id="Modalamh" class="modal fade" role="dialog" data-backdrop="static">
  <div class="modal-dialog modal-lg"  >
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <form name="frmModalamh" class="form-horizontal" role="form" autocomplete='off' action="" method="post" onsubmit="return false;">
            <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
<!--             <input type="hidden" name="id_pemda" id="id_pemda"> -->
<!--             <input type="hidden" name="id_amh" id="id_amh"> -->
            <div class="row">
            <div class="col-sm-2" style="text-align: center;">
              <i class="fa fa-blind fa-fw" style="font-size: 100px;color: #357EBD;"></i>
            </div>
            <div class="col-sm-10">
            <div class="form-group">
                <label for="tahun" class="col-sm-2 control-label" align='left'>Tahun :</label>
                <div class="col-sm-10">
                  <select class="form-control tahun" name="tahun" id="tahun">
                  
                  </select>
<!--                   <input type="text" class="form-control number" id="text_tahun" name="text_tahun" required="required" align='right' > -->
                </div>
              </div>
              <div class="form-group">
                <label for="kecamatan" class="col-sm-2 control-label" align='left'>Kecamatan :</label>
                <div class="col-sm-10">
                  <select class="form-control kecamatan" name="kecamatan" id="kecamatan">
                 
                  </select>
<!--                    <input type="text" class="form-control number" id="text_kecamatan" name="text_kecamatan" required="required" align='right'> -->
                </div>
              </div>
              <div class="form-group">
                <label for="nama_kolom" class="col-sm-2 control-label" align='left'>Uraian :</label>
                <div class="col-sm-10">
                  <select class="form-control nama_kolom" name="nama_kolom" id="nama_kolom">
                  </select>
                   <input type="hidden" class="form-control " id="id_isi" name="id_isi" required="required" align='right' >
                </div>
              </div>
            <div class="form-group">
              <label for="nmin1" class="col-sm-2" align='left'>Nilai N-1</label>
              <div class="col-sm-10">
                <input type="text" class="form-control number" id="nmin1" name="nmin1" required="required" align='right'>
              </div>
              
            </div>  
            <div class="form-group">
              <label for="nmin2" class="col-sm-2" align='left'>Nilai N-2</label>
              <div class="col-sm-10">
                <input type="text" class="form-control number" id="nmin2" name="nmin2" required="required" align='right' >
              </div>
              
            </div>  
            <div class="form-group">
              <label for="nmin3" class="col-sm-2" align='left'>Nilai N-3</label>
              <div class="col-sm-10">
                <input type="text" class="form-control number" id="nmin3" name="nmin3" required="required" align='right' >
              </div>
              
            </div>  
            <div class="form-group">
              <label for="nmin4" class="col-sm-2" align='left'>Nilai N-4</label>
              <div class="col-sm-10">
                <input type="text" class="form-control number" id="nmin4" name="nmin4" required="required" align='right' >
              </div>
              
            </div>  
            <div class="form-group">
              <label for="nmin5" class="col-sm-2" align='left'>Nilai N-5</label>
              <div class="col-sm-10">
                <input type="text" class="form-control number" id="nmin5" name="nmin5" required="required" align='right' >
              </div>
             
            </div>            
            
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
                        <button type="button" class="btn btn-sm btn-success btnamh btn-labeled" data-dismiss="modal">
                            <span class="btn-label"><i id="nmbtnSatuan" class="glyphicon glyphicon-save"></i></span>Simpan</button>
                        <div class="or"></div>
                        <button type="button" class="btn btn-sm btn-warning btn-labeled" data-dismiss="modal" aria-hidden="true">
                            <span class="btn-label"><i class="glyphicon glyphicon-log-out"></i></span>Tutup</button>
                      </div>
                    </div>
                </div>
              </div>
      </div>
    </div>
  </div>

<div id="ModalHapusamh" class="modal fade" role="dialog" data-backdrop="static">
  <div class="modal-dialog modal-lg"  >
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
        <form name="frmModalHapusamh" class="form-horizontal" role="form" autocomplete='off' action="" method="post" onsubmit="return false;">
            <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
<!--             <input type="hidden" name="id_pemda" id="id_pemda"> -->
<!--             <input type="hidden" name="id_amh" id="id_amh"> -->
            <div class="row">
            <div class="col-sm-2" style="text-align: center;">
              <i class="fa fa-map-marker fa-fw" style="font-size: 100px;color: #357EBD;"></i>
            </div>
            <div class="col-sm-10">
            	<div class="form-group">
                <label  class="col-sm-8 control-label" align='left'>Apakah Anda yakin untuk menghapus?</label>
                <input type="hidden" class="form-control " id="id_isi_hapus" name="id_isi_hapus" required="required" align='right' >
                </div>
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
                        <button type="button" class="btn btn-sm btn-success btnHapusamh btn-labeled" data-dismiss="modal">
                            <span class="btn-label"><i id="nmbtnSatuan" class="glyphicon glyphicon-remove"></i></span>Ya</button>
                        <div class="or"></div>
                        <button type="button" class="btn btn-sm btn-warning btn-labeled" data-dismiss="modal" aria-hidden="true">
                            <span class="btn-label"><i class="glyphicon glyphicon-log-out"></i></span>Tidak</button>
                      </div>
                    </div>
                </div>
              </div>
      </div>
    </div>
  </div>
</div>
 <?php $__env->stopSection(); ?>
 

<?php $__env->startSection('scripts'); ?>
<script  type="text/javascript" language="javascript" class="init">
$(document).ready(function() {
	//alert('A');
var amh_tbl=$('#tblamh').DataTable({
                  processing: true,
                  serverSide: true,
                  dom : 'BfRtip',                  
                  autoWidth : false,
                  "ajax": {"url": "./amh/getListamh"},
                  "language": {
                      "decimal": ",",
                      "thousands": "."},
                  "columns": [
                        { data: 'no_urut','searchable': false, 'orderable':false, sClass: "dt-center", width :"5%"},
                       // { data: 'id_isian_tabel_dasar', sClass: "dt-left"},
                        { data: 'tahun', sClass: "dt-center"},
                        { data: 'nama_kecamatan', sClass: "dt-left"},
                        { data: 'nama_kolom', sClass: "dt-left"},
                        { data: 'nmin5', sClass: "dt-right",
                              render: $.fn.dataTable.render.number( '.', ',', 0, '' )},
                        { data: 'nmin4', sClass: "dt-right",
                              render: $.fn.dataTable.render.number( '.', ',', 0, '' )},
                        { data: 'nmin3', sClass: "dt-right",
                              render: $.fn.dataTable.render.number( '.', ',', 0, '' )},
                        { data: 'nmin2', sClass: "dt-right",
                              render: $.fn.dataTable.render.number( '.', ',', 0, '' )},
                        { data: 'nmin1', sClass: "dt-right",
                              render: $.fn.dataTable.render.number( '.', ',', 0, '' )},
                        { data: 'action', 'searchable': false, width :"10%", 'orderable':false, sClass: "dt-center" }
                      ],
                  "order": [[0, 'asc']],
                  "bDestroy": true
        });
$(document).on('click', '#btnAddamh', function() {
	  $('.btnamh').removeClass('edit');
	  $('.btnamh').addClass('add');
	  $('.modal-title').text('Tambah Angka Melek Huruf');
	  $('.form-horizontal').show();
	  $('#Modalamh').modal('show');
	  $('#nama_kolom').val("");
	  $('#kecamatan').val("");
	  $('#nmin1').val("");
	  $('#nmin2').val("");
	  $('#nmin3').val("");
	  $('#nmin4').val("");
	  $('#nmin5').val("");
	  $('#tahun').val("");
	  $('#nmin1persen').val("");
	  $('#nmin2persen').val("");
	  $('#nmin3persen').val("");
	  $('#nmin4persen').val("");
	  $('#nmin5persen').val("");
	 
// 	  var text_tahun_1 = document.getElementById("text_tahun");
// 	  text_tahun_1.style.visibility = 'hidden';

	  $.ajax({
          type: "GET",
          url: './amh/getTahunamh',
          dataType: "json",
          success: function(data) {

          var j = data.length;
          var post, i;
          $('select[name="nama_kolom"]').empty();
          
          $('select[name="tahun"]').empty();
          $('select[name="tahun"]').append('<option value="-1">---Pilih Tahun---</option>');

          for (i = 0; i < j; i++) {
            post = data[i];
            $('select[name="tahun"]').append('<option value="'+ post.tahun +'">'+ post.tahun +'</option>');
          }
          }
      });
	  $.ajax({
          type: "GET",
          url: './amh/getKecamatanamh',
          dataType: "json",
          success: function(data) {

          var j = data.length;
          var post, i;

          $('select[name="kecamatan"]').empty();
          $('select[name="kecamatan"]').append('<option value="-1">---Pilih Kecamatan---</option>');

          for (i = 0; i < j; i++) {
            post = data[i];
            $('select[name="kecamatan"]').append('<option value="'+ post.id_kecamatan +'">'+ post.nama_kecamatan +'</option>');
          }
          }
      });	  
	});


$( ".kecamatan" ).change(function() {
    
    $.ajax({

        type: "GET",
        url: './amh/getSektoramh/'+$('#tahun').val()+'/'+$('#kecamatan').val(),
        dataType: "json",

        success: function(data) {

        var j = data.length;
        var post, i;

        $('select[name="nama_kolom"]').empty();
        $('select[name="nama_kolom"]').append('<option value="-1">---Pilih Uraian---</option>');

        for (i = 0; i < j; i++) {
          post = data[i];
          $('select[name="nama_kolom"]').append('<option value="'+ post.id_kolom_tabel_dasar +'">'+ post.nama_kolom +'</option>');
        }
        }
    });
  });
$( ".tahun" ).change(function() {
    
    $.ajax({

        type: "GET",
        url: './amh/getSektoramh/'+$('#tahun').val()+'/'+$('#kecamatan').val(),
        dataType: "json",

        success: function(data) {

        var j = data.length;
        
        var post, i;

        $('select[name="nama_kolom"]').empty();
        $('select[name="nama_kolom"]').append('<option value="-1">---Pilih Uraian---</option>');

        for (i = 0; i < j; i++) {
          post = data[i];
          $('select[name="nama_kolom"]').append('<option value="'+ post.id_kolom_tabel_dasar +'">'+ post.nama_kolom +'</option>');
        }
        }
    });
  });
// $("#nmin1").keyup(function() {

// 	 $("#nmin1").css("background-color", "pink");
 
// } );

$('.modal-footer').on('click', '.add', function() {
    $.ajaxSetup({
       headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
    });

    $.ajax({
        type: 'post',
        url: './amh/addamh',
        data: {
            '_token': $('input[name=_token]').val(),
            'id_kolom_tabel_dasar':$('#nama_kolom').val(),
            'id_kecamatan':$('#kecamatan').val(),
            'nmin1':$('#nmin1').val(),
            'nmin2':$('#nmin2').val(),
            'nmin3':$('#nmin3').val(),
            'nmin4':$('#nmin4').val(),
            'nmin5':$('#nmin5').val(),
            'tahun':$('#tahun').val(),
            'nmin1_persen':$('#nmin1persen').val(),
            'nmin2_persen':$('#nmin2persen').val(),
            'nmin3_persen':$('#nmin3persen').val(),
            'nmin4_persen':$('#nmin4persen').val(),
            'nmin5_persen':$('#nmin5persen').val(),

        },
        success: function(data) {
              $('#tblamh').DataTable().ajax.reload();
              if(data.status_pesan==1){
              createPesan(data.pesan,"success");
              } else {
              createPesan(data.pesan,"danger"); 
              }
        },
  });
});
//edit function
$(document).on('click', '#btnEditamh', function() {
//alert("1");

var data = amh_tbl.row( $(this).parents('tr') ).data();

  $('.btnamh').removeClass('add');
  $('.btnamh').addClass('edit');
  $('.modal-title').text('Edit Angka Melek Huruf');
  $('.form-horizontal').show();
  $('#Modalamh').modal('show');
  $('#id_isi').val(data.id_isian_tabel_dasar);
  $('#nama_kolom').val(data.id_kolom_tabel_dasar);
  $('#kecamatan').val(data.id_kecamatan);
  $('#nmin1').val(data.nmin1);
  $('#nmin2').val(data.nmin2);
  $('#nmin3').val(data.nmin3);
  $('#nmin4').val(data.nmin4);
  $('#nmin5').val(data.nmin5);
  $('#tahun').val(data.tahun);
  $('#nmin1persen').val(data.nmin1_persen);
  $('#nmin2persen').val(data.nmin2_persen);
  $('#nmin3persen').val(data.nmin3_persen);
  $('#nmin4persen').val(data.nmin4_persen);
  $('#nmin5persen').val(data.nmin5_persen);


  $('select[name="tahun"]').empty();
  $('select[name="tahun"]').append('<option value="'+data.tahun+'">'+data.tahun+'</option>');
  $('select[name="kecamatan"]').empty();
  $('select[name="kecamatan"]').append('<option value="'+data.id_kecamatan+'">'+data.nama_kecamatan+'</option>');
  $('select[name="nama_kolom"]').empty();
  $('select[name="nama_kolom"]').append('<option value="'+data.id_kolom_tabel_dasar+'">'+data.nama_kolom+'</option>');
  
  
  
  
});
$('.modal-footer').on('click', '.edit', function() {
	 $.ajaxSetup({
	       headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
	    });

	    $.ajax({
	        type: 'post',
	        url: './amh/editamh',
	        data: {
	            '_token': $('input[name=_token]').val(),
	            'id_isian_tabel_dasar' : $('#id_isi').val(),
	            'id_kolom_tabel_dasar' : $('#nama_kolom').val(),
	            'id_kecamatan' : $('#kecamatan').val(),
	            'nmin1' : $('#nmin1').val(),
	            'nmin2' : $('#nmin2').val(),
	            'nmin3' : $('#nmin3').val(),
	            'nmin4' : $('#nmin4').val(),
	            'nmin5' : $('#nmin5').val(),
	            'tahun' : $('#tahun').val(),
	            'nmin1_persen' : $('#nmin1persen').val(),
	            'nmin2_persen' : $('#nmin2persen').val(),
	            'nmin3_persen' : $('#nmin3persen').val(),
	            'nmin4_persen' : $('#nmin4persen').val(),
	            'nmin5_persen' : $('#nmin5persen').val(),


	        },
	        success: function(data) {
	            $('#tblamh').DataTable().ajax.reload();
	            if(data.status_pesan==1){
	              createPesan(data.pesan,"success");
	              } else {
	              createPesan(data.pesan,"danger"); 
	              }
	        }
	    });
});
$(document).on('click', '#btnHapusamh', function() {
	var data = amh_tbl.row( $(this).parents('tr') ).data();
	$('.btnHapusamh').removeClass('add');
	  $('.btnHapusamh').addClass('remove');
	  $('.modal-title').text('Hapus Data PDRB');
	  $('.form-horizontal').show();
	 $('#ModalHapusamh').modal('show');
	 $('#id_isi_hapus').val(data.id_isian_tabel_dasar);
	 
});

$('.modal-footer').on('click', '.remove', function() {
	 $.ajaxSetup({
	       headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
	    });

	    $.ajax({
	        type: 'post',
	        url: './amh/hapusamh',
	        data: {
	            '_token': $('input[name=_token]').val(),
	            'id_isian_tabel_dasar' : $('#id_isi_hapus').val(),
	           


	        },
	        success: function(data) {
	            $('#tblamh').DataTable().ajax.reload();
	            if(data.status_pesan==1){
	              createPesan(data.pesan,"success");
	              } else {
	              createPesan(data.pesan,"danger"); 
	              }
	        }
	    });
});
$(document).on('click', '#btnPrintamh', function() {

	//alert("print");
     window.open('./PrintAmh/2018');
   
 });
});

</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app1', array_except(get_defined_vars(), array('__data', '__path')))->render(); ?>