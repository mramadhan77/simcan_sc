$(document).ready(function() {
  
  var detInDokumen = Handlebars.compile($("#details-inDokumen").html());
  var detInTujuan = Handlebars.compile($("#details-inTujuan").html());
  var detInSasaran = Handlebars.compile($("#details-inSasaran").html());
  var detInProgram = Handlebars.compile($("#details-inProgram").html());

  var id_visi_rpjmd;
  var id_misi_rpjmd;
  var id_tujuan_rpjmd;
  var id_sasaran_rpjmd;
  var id_kebijakan_rpjmd;
  var id_strategi_rpjmd;
  var id_program_rpjmd;
  var id_indikator_program;
  var id_urusan_program;
  var id_pelaksana_program;
  var tahun_rpjmd;
  
  function formatTgl(val_tanggal){
      var formattedDate = new Date(val_tanggal);
      var d = formattedDate.getDate();
      var m = formattedDate.getMonth();
      var y = formattedDate.getFullYear();
      var m_names = new Array("Januari", "Februari", "Maret", 
        "April", "Mei", "Juni", "Juli", "Agustus", "September", 
        "Oktober", "November", "Desember")

      var tgl= d + " " + m_names[m] + " " + y;
      return tgl;
  }

  function hariIni(){
    var today = new Date();
    var dd = today.getDate();
    var mm = today.getMonth()+1; //January is 0!
    var yyyy = today.getFullYear();

    var hariIni = yyyy + '-' + mm + '-' + dd;
    return hariIni;
  }

  function createPesan(message, type) {
    var html = '<div id="pesanx" class="alert alert-' + type + ' alert-dismissable flyover flyover-bottom in">';    
    html += '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>';   
    html += '<p><strong>'+message+'</strong></p>';
    html += '</div>';    
    $(html).hide().prependTo('#pesan').slideDown();

    setTimeout(function() {
        $('#pesanx').removeClass('in');
         }, 3500);
  };

  $('[data-toggle="popover"]').popover();
  $('.number').number(true,4,',', '.');
  $('#thn_1_dok').number(true,0,'','');
  $('#thn_5_dok').number(true,0,'','');
  $('#no_urut_edit').number(true,0,'','');
  $('#id_perubahan_edit').number(true,0,'','');

  $('.page-alert .close').click(function(e) {
          e.preventDefault();
          $(this).closest('.page-alert').slideUp();
      });

  $.datepicker.setDefaults( $.datepicker.regional[ "id" ] );

  $('#tgl_perda_dok_x').datepicker({
    altField: "#tgl_perda_dok",
    altFormat: "yy-mm-dd", 
    dateFormat: "dd MM yy",
  });

  $('#btn').click(function() {
    $("#tgl_perda_dok_x").focus();
  });

  $.ajax({
    type: "GET",
    url: './rpjmd/getDokumen',
    dataType: "json",

    success: function(data) {
      $('#no_perda_rpjmd').text(data[0].no_perda);
      $('#tgl_perda_rpjmd').text(formatTgl(data[0].tgl_perda));
      $('#periode_awal_rpjmd').text(data[0].tahun_1);
      $('#periode_akhir_rpjmd').text(data[0].tahun_5);
    }
  });

  var tbl_Dokumen = $('#tblDokumen').DataTable( {
          processing: true,
          serverSide: true,
          responsive: true,
          dom : 'bfrtip',                  
          autoWidth : false,
          order: [[0, 'asc']],
          bDestroy: true,
          language: {
              "decimal": ",",
              "thousands": ".",
              "sEmptyTable":   "Tidak ada data yang tersedia pada tabel ini",
              "sProcessing":   "Sedang memproses...",
              "sLengthMenu":   "Tampilkan _MENU_ entri",
              "sZeroRecords":  "Tidak ditemukan data yang sesuai",
              "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
              "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
              "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
              "sInfoPostFix":  "",
              "sSearch":       "Cari:",
              "sUrl":          "",
              "oPaginate": {
                  "sFirst":    "Pertama",
                  "sPrevious": "Sebelumnya",
                  "sNext":     "Selanjutnya",
                  "sLast":     "Terakhir"
              }
            },
           "ajax": {"url": "./rpjmd/getDokumenRpjmd"},
          columns: [
                 {
                  "className":      'details-control',
                  "orderable":      false,
                  "searchable":      false,
                  "data":           null,
                  "defaultContent": '',
                  "width" : "5px"
                },
                { data: 'no_urut','searchable': false, 'orderable':false, sClass: "dt-center"},
                { data: 'nm_dokumen','searchable': false, 'orderable':false},
                { data: 'id_revisi','searchable': false, 'orderable':false, sClass: "dt-center"},
                { data: 'no_perda','searchable': false, 'orderable':false},
                { data: 'tgl_perda_view','searchable': false, 'orderable':false, sClass: "dt-center",
                  },
                { data: 'icon','searchable': false, 'orderable':false, sClass: "dt-center",width:"15px",
                  render: function(data, type, row,meta) {
                  return '<i class="'+row.status_icon+'" style="font-size:16px;color:'+row.warna+';"/>';
                }},
                { data: 'action', 'searchable': false, 'orderable':false }
              ]
        } );

  // $('#tblDokumen tbody').on( 'dblclick', 'tr', function () {
  //     var data = tbl_Dokumen.row(this).data();
  //     $('.nav-tabs a[href="#visi"]').tab('show');
  //     loadTblVisi(data.id_rpjmd);

  // });

  var tblInDokumen;
    function initInDokumen(tableId, data) {
        tblInDokumen=$('#' + tableId).DataTable({
                processing: true,
                serverSide: true,
                ajax: data.details_url,
                dom : 'BfRtIP',
                autoWidth: false,
                language: {
              "decimal": ",",
              "thousands": ".",
              "sEmptyTable":   "Tidak ada data yang tersedia pada tabel ini",
              "sProcessing":   "Sedang memproses...",
              "sLengthMenu":   "Tampilkan _MENU_ entri",
              "sZeroRecords":  "Tidak ditemukan data yang sesuai",
              "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
              "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
              "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
              "sInfoPostFix":  "",
              "sSearch":       "Cari:",
              "sUrl":          "",
              "oPaginate": {
                  "sFirst":    "Pertama",
                  "sPrevious": "Sebelumnya",
                  "sNext":     "Selanjutnya",
                  "sLast":     "Terakhir"
              }
            },
                "columns": [
                            { data: 'no_urut','searchable': false, 'orderable':false, sClass: "dt-center"},
                            { data: 'uraian_visi_rpjmd','searchable': false, 'orderable':false},
                            { data: 'action', 'searchable': false, 'orderable':false }
                          ],
                "order": [[0, 'asc']],
                "bDestroy": true
            })

        $('#' + tableId+'  tbody').on( 'dblclick', 'tr', function () {
            var data = tblInDokumen.row(this).data();
            id_visi_rpjmd =  data.id_visi_rpjmd;
            tahun_rpjmd = data.thn_id;
            $('.nav-tabs a[href="#misi"]').tab('show');
            loadTblMisi(id_visi_rpjmd);
        });
    }

  $('#tblDokumen tbody').on('click', 'td.details-control', function () {
      var tr = $(this).closest('tr');
      var row = tbl_Dokumen.row( tr );
      var tableId = 'inDokumen-' + row.data().id_rpjmd;

      if (row.child.isShown()) {
          row.child.hide();
          tr.removeClass('shown');
      } else {
          row.child(detInDokumen(row.data())).show();
          initInDokumen(tableId, row.data());
          tr.addClass('shown');
          tr.next().find('td').addClass('no-padding bg-gray');
      }    
  });

  var tbl_Visi 
  function loadTblVisi(id_rpjmd){
  tbl_Visi = $('#tblVisi').DataTable( {
          processing: true,
          serverSide: true,
          responsive: true,
          dom : 'BFRTIP',                  
          autoWidth : false,
          order: [[0, 'asc']],
          bDestroy: true,
          language: {
              "decimal": ",",
              "thousands": ".",
              "sEmptyTable":   "Tidak ada data yang tersedia pada tabel ini",
              "sProcessing":   "Sedang memproses...",
              "sLengthMenu":   "Tampilkan _MENU_ entri",
              "sZeroRecords":  "Tidak ditemukan data yang sesuai",
              "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
              "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
              "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
              "sInfoPostFix":  "",
              "sSearch":       "Cari:",
              "sUrl":          "",
              "oPaginate": {
                  "sFirst":    "Pertama",
                  "sPrevious": "Sebelumnya",
                  "sNext":     "Selanjutnya",
                  "sLast":     "Terakhir"
              }
            },
           "ajax": {"url": "./rpjmd/visi/"+id_rpjmd},
          columns: [
                { data: 'id_visi_rpjmd','searchable': false, 'orderable':false, sClass: "dt-center"},
                { data: 'uraian_visi_rpjmd','searchable': false, 'orderable':false},
                { data: 'action', 'searchable': false, 'orderable':false }
              ]
        } );
};

  
  $(document).on('click', '.btnAddVisi', function() {
    var data = tbl_Dokumen.row( $(this).parents('tr') ).data();
      $('.btnSimpanVisi').removeClass('editVisi');
      $('.btnSimpanVisi').addClass('addVisi');
      $('.modal-title').text('Data Visi Daerah');
      $('.form-horizontal').show();
      $('#id_visi_rpjmd_edit').val(null);
      $('#thn_id_edit').val(null);
      $('#no_urut_edit').val(1);
      $('#id_rpjmd_edit').val(data.id_rpjmd);
      $('#id_perubahan_edit').val(0);
      $('#ur_visi_rpjmd_edit').val(null);
      $('#thn_periode_visi').val(data.tahun_1 + ' sampai dengan ' + data.tahun_5);      
      $('#btnDelVisi').hide();
      $('#EditVisi').modal('show');
  });

  $('.modal-footer').on('click', '.addVisi', function() {
      $.ajaxSetup({
         headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
      });

      $.ajax({
          type: 'post',
          url: './rpjmd/addVisi',
          data: {
              '_token': $('input[name=_token]').val(),
              'no_urut_edit': $('#no_urut_edit').val(),
              'id_rpjmd_edit': $('#id_rpjmd_edit').val(),
              'id_perubahan_edit': $('#id_perubahan_edit').val(),
              'ur_visi_rpjmd_edit': $('#ur_visi_rpjmd_edit').val(),
          },
          success: function(data) {
              tblInDokumen.ajax.reload(null,false);
              if(data.status_pesan==1){
                createPesan(data.pesan,"success");
                } else {
                createPesan(data.pesan,"danger"); 
              }
          }
      });
  });

  $(document).on('click', '.edit-visi', function() {
    var data = tblInDokumen.row( $(this).parents('tr') ).data();
	    $('.btnSimpanVisi').addClass('editVisi');
      $('.btnSimpanVisi').removeClass('addVisi');
	    $('.modal-title').text('Data Visi Daerah');
	    $('.form-horizontal').show();
	    $('#id_visi_rpjmd_edit').val(data.id_visi_rpjmd);
	    $('#thn_id_edit').val(data.thn_id);
	    $('#no_urut_edit').val(data.no_urut);
	    $('#id_rpjmd_edit').val(data.id_rpjmd);
	    $('#id_perubahan_edit').val(data.id_perubahan);
	    $('#ur_visi_rpjmd_edit').val(data.uraian_visi_rpjmd);
      $('#thn_periode_visi').val(data.tahun_1 + ' sampai dengan ' + data.tahun_5);    
      $('#btnDelVisi').show();
	    $('#EditVisi').modal('show');
	  });

  $('.modal-footer').on('click', '.editVisi', function() {
      $.ajaxSetup({
         headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
      });

      $.ajax({
          type: 'post',
          url: './rpjmd/editVisi',
          data: {
              '_token': $('input[name=_token]').val(),
              'id_visi_rpjmd_edit': $('#id_visi_rpjmd_edit').val(),
              'thn_id_edit': $('#thn_id_edit').val(),
              'no_urut_edit': $('#no_urut_edit').val(),
              'id_rpjmd_edit': $('#id_rpjmd_edit').val(),
              'id_perubahan_edit': $('#id_perubahan_edit').val(),
              'ur_visi_rpjmd_edit': $('#ur_visi_rpjmd_edit').val(),
          },
          success: function(data) {
              tblInDokumen.ajax.reload(null,false);
              if(data.status_pesan==1){
                createPesan(data.pesan,"success");
                } else {
                createPesan(data.pesan,"danger"); 
              }
          }
      });
  });

  $('.modal-footer').on('click', '.btnDelVisi', function() {
    $.ajaxSetup({
        headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
    });

      $.ajax({
        type: 'post',
        url: './rpjmd/deleteVisi',
        data: {
          '_token': $('input[name=_token]').val(),
          'id_visi_rpjmd_edit': $('#id_visi_rpjmd_edit').val(),
          'id_rpjmd_edit': $('#id_rpjmd_edit').val(),
        },
        success: function(data) {
          tblInDokumen.ajax.reload(null,false);
          $('#EditVisi').modal('hide');
          if(data.status_pesan==1){
            createPesan(data.pesan,"success");
          } else {
            createPesan(data.pesan,"danger"); 
          }
        }
      });
  });

  var  tblMisi;
  function loadTblMisi(id_visi_rpjmd){
  tblMisi = $('#tblMisi').DataTable( {
          processing: true,
          serverSide: true,
          dom: 'BFrtip',
          responsive: true,                
          autoWidth : false,
          order: [[0, 'asc']],
          bDestroy: true,
          language: {
              "decimal": ",",
              "thousands": ".",
              "sEmptyTable":   "Tidak ada data yang tersedia pada tabel ini",
              "sProcessing":   "Sedang memproses...",
              "sLengthMenu":   "Tampilkan _MENU_ entri",
              "sZeroRecords":  "Tidak ditemukan data yang sesuai",
              "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
              "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
              "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
              "sInfoPostFix":  "",
              "sSearch":       "Cari:",
              "sUrl":          "",
              "oPaginate": {
                  "sFirst":    "Pertama",
                  "sPrevious": "Sebelumnya",
                  "sNext":     "Selanjutnya",
                  "sLast":     "Terakhir"
              }
            },
           "ajax": {"url": "./rpjmd/misi/"+id_visi_rpjmd},
          "columns": [
                { data: 'id_visi_rpjmd','searchable': false,  sClass: "dt-center"},
                { data: 'no_urut','searchable': false, sClass: "dt-center"},
                { data: 'uraian_misi_rpjmd'},
                { data: 'action', 'searchable': false, 'orderable':false }]
        } );
}

  $(document).on('click', '.edit-misi', function() {
    var data = tblMisi.row( $(this).parents('tr') ).data();
	    $('.actionBtn_misi').addClass('editMisi');
	    $('.modal-title').text('Data Misi Daerah');
	    $('.form-horizontal').show();
	    $('#id_misi_rpjmd_edit').val($(this).data('id_misi_rpjmd_misi'));
	    $('#thn_id_misi_edit').val($(this).data('thn_id_misi'));
	    $('#no_urut_misi_edit').val($(this).data('no_urut_misi'));
	    $('#id_visi_rpjmd_misi_edit').val($(this).data('id_visi_rpjmd_misi'));
	    $('#id_perubahan_misi_edit').val($(this).data('id_perubahan_misi'));
	    $('#ur_misi_rpjmd_edit').val($(this).data('uraian_misi_rpjmd_misi'));
	    $('#EditMisi').modal('show');
	  });
  $('.modal-footer').on('click', '.editMisi', function() {
      $.ajaxSetup({
         headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
      });
      $.ajax({
          type: 'post',
          url: './rpjmd/editMisi',
          data: {
              '_token': $('input[name=_token]').val(),
              'id_misi_rpjmd_edit': $('#id_misi_rpjmd_edit').val(),
              'thn_id_misi_edit': $('#thn_id_misi_edit').val(),
              'no_urut_misi_edit': $('#no_urut_misi_edit').val(),
              'id_visi_rpjmd_misi_edit': $('#id_visi_rpjmd_misi_edit').val(),
              'id_perubahan_misi_edit': $('#id_perubahan_misi_edit').val(),
              'ur_misi_rpjmd_edit': $('#ur_misi_rpjmd_edit').val(),
          },
          success: function(data) {
              tblMisi.ajax.reload(null,false);
              if(data.status_pesan==1){
                createPesan(data.pesan,"success");
                } else {
                createPesan(data.pesan,"danger"); 
              }
          }
      });
  });
  $('#tblMisi tbody').on( 'dblclick', 'tr', function () {

	    var data = tblMisi.row(this).data();
	    id_misi_rpjmd =  data.id_misi_rpjmd;
	    $('.nav-tabs a[href="#tujuan"]').tab('show');
      loadTblTujuan(id_misi_rpjmd);
	      // $('#tblTujuan').DataTable().ajax.url("./rpjmd/tujuan/"+id_misi_rpjmd).load();
	});

  var tblTujuan;
  function loadTblTujuan(id_misi_rpjmd){
  tblTujuan = $('#tblTujuan').DataTable( {
          processing: true,
          serverSide: true,
          dom: 'BFrtip',
          responsive: true,                
          autoWidth : false,
          order: [[0, 'asc']],
          bDestroy: true,
          language: {
              "decimal": ",",
              "thousands": ".",
              "sEmptyTable":   "Tidak ada data yang tersedia pada tabel ini",
              "sProcessing":   "Sedang memproses...",
              "sLengthMenu":   "Tampilkan _MENU_ entri",
              "sZeroRecords":  "Tidak ditemukan data yang sesuai",
              "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
              "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
              "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
              "sInfoPostFix":  "",
              "sSearch":       "Cari:",
              "sUrl":          "",
              "oPaginate": {
                  "sFirst":    "Pertama",
                  "sPrevious": "Sebelumnya",
                  "sNext":     "Selanjutnya",
                  "sLast":     "Terakhir"
              }
            },
          "ajax": {"url": "./rpjmd/tujuan/"+id_misi_rpjmd},
          "columns": [
                {
                  "className":      'details-control',
                  "orderable":      false,
                  "searchable":      false,
                  "data":           null,
                  "defaultContent": '',
                  "width" : "5px"
                },
                { data: 'id_visi_rpjmd','searchable': false, 'orderable':false, sClass: "dt-center"},
                { data: 'id_misi','searchable': false, 'orderable':false, sClass: "dt-center"},
                { data: 'no_urut','searchable': false, sClass: "dt-center"},
                { data: 'uraian_tujuan_rpjmd'},
                { data: 'jml_indikator','searchable': false, sClass: "dt-center"},
                { data: 'action', 'searchable': false, 'orderable':false }
              ]
        });
    }
    
    var tblInTujuan;
    function initInTujuan(tableId, data) {
        tblInTujuan=$('#' + tableId).DataTable({
                processing: true,
                serverSide: true,
                ajax: data.details_url,
                dom : 'BfRtIP',
                autoWidth: false,
                language: {
                    "decimal": ",",
                    "thousands": ".",
                    "sEmptyTable":   "Tidak ada data yang tersedia pada tabel ini",
                    "sProcessing":   "Sedang memproses...",
                    "sLengthMenu":   "Tampilkan _MENU_ entri",
                    "sZeroRecords":  "Tidak ditemukan data yang sesuai",
                    "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
                    "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                    "sInfoPostFix":  "",
                    "sSearch":       "Cari:",
                    "sUrl":          "",
                    "oPaginate": {
                        "sFirst":    "Pertama",
                        "sPrevious": "Sebelumnya",
                        "sNext":     "Selanjutnya",
                        "sLast":     "Terakhir"
                    }
                  },
                "columns": [
                            { data: 'urut', sClass: "dt-center"},
                            { data: 'uraian_indikator_sasaran_rpjmd'},
                            { data: 'action', 'searchable': false, 'orderable':false, sClass: "dt-center" }
                          ],
                "order": [[0, 'asc']],
                "bDestroy": true
            })
    }

  $('#tblTujuan tbody').on('click', 'td.details-control', function () {
      var tr = $(this).closest('tr');
      var row = tblTujuan.row( tr );
      var tableId = 'inTujuan-' + row.data().id_tujuan_rpjmd;

      if (row.child.isShown()) {
          row.child.hide();
          tr.removeClass('shown');
      } else {
          row.child(detInTujuan(row.data())).show();
          initInTujuan(tableId, row.data());
          tr.addClass('shown');
          tr.next().find('td').addClass('no-padding bg-gray');
      }    
  });

  $(document).on('click', '.add-indikator', function() {
    var data = tblTujuan.row( $(this).parents('tr') ).data();
    $('.btnSimpanTujuanIndikator').removeClass('editTujuanIndikator');
    $('.btnSimpanTujuanIndikator').addClass('addTujuanIndikator');
    $('.modal-title').text('Data Indikator Tujuan Daerah');
    $('.form-horizontal').show();
    $('#id_indikator_tujuan_rpjmd_edit').val(null);
    $('#thn_id_indikator_tujuan_edit').val(tahun_rpjmd);
    $('#id_tujuan_rpjmd_indikator_edit').val(data.id_tujuan_rpjmd);
    $('#no_urut_indikator_tujuan_edit').val(1);
    $('#id_perubahan_indikator_tujuan_edit').val(0);
    $('#ur_indikator_tujuan_rpjmd').val(null);
    $('#kd_indikator_tujuan_rpjmd').val(null);
    $('#inditujuan1_edit').val(0);
    $('#inditujuan2_edit').val(0);
    $('#inditujuan3_edit').val(0);
    $('#inditujuan4_edit').val(0);
    $('#inditujuan5_edit').val(0);
    $('#inditujuan_awal_edit').val(0);
    $('#inditujuan_akhir_edit').val(0);
    $('#satuan_tujuan_indikator_edit').val(null);
    $('#ModalIndikatorTujuan').modal('show');
});

$('.modal-footer').on('click', '.addTujuanIndikator', function() {
    $.ajaxSetup({
       headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
    });

    $.ajax({
        type: 'post',
        url: './rpjmd/addIndikatorTujuan',
        data: {
            '_token': $('input[name=_token]').val(),
            'thn_id': $('#thn_id_indikator_tujuan_edit').val(),
            'no_urut': $('#no_urut_indikator_tujuan_edit').val(),
            'id_tujuan_rpjmd': $('#id_tujuan_rpjmd_indikator_edit').val(),
            'id_perubahan': $('#id_perubahan_indikator_tujuan_edit').val(),
            'kd_indikator': $('#kd_indikator_tujuan_rpjmd').val(),
            'uraian_indikator_sasaran_rpjmd': $('#ur_indikator_tujuan_rpjmd').val(),
            'tolok_ukur_indikator': $('#ur_indikator_tujuan_rpjmd').val(),
            'angka_awal_periode': $('#inditujuan_awal_edit').val(),
            'angka_tahun1': $('#inditujuan1_edit').val(),
            'angka_tahun2': $('#inditujuan2_edit').val(),
            'angka_tahun3': $('#inditujuan3_edit').val(),
            'angka_tahun4': $('#inditujuan4_edit').val(),
            'angka_tahun5': $('#inditujuan5_edit').val(),
            'angka_akhir_periode': $('#inditujuan_akhir_edit').val(),    
        },
        success: function(data) {
            $('#tblTujuan').DataTable().ajax.reload(null,false);
              if(data.status_pesan==1){
                createPesan(data.pesan,"success");
                } else {
                createPesan(data.pesan,"danger"); 
              }
        },
        error: function(data){            
        }
    });
});

$(document).on('click', '.edit-indikator', function() {
var data = tblInTujuan.row( $(this).parents('tr') ).data();
    $('.btnSimpanTujuanIndikator').removeClass('addTujuanIndikator');
    $('.btnSimpanTujuanIndikator').addClass('editTujuanIndikator');
  $('.modal-title').text('Data Indikator Tujuan Daerah');
  $('.form-horizontal').show();
    $('#id_indikator_tujuan_rpjmd_edit').val(data.id_indikator_tujuan_rpjmd);
    $('#thn_id_indikator_tujuan_edit').val(data.thn_id);
    $('#id_tujuan_rpjmd_indikator_edit').val(data.id_tujuan_rpjmd);
    $('#no_urut_indikator_tujuan_edit').val(data.no_urut);
    $('#id_perubahan_indikator_tujuan_edit').val(data.id_perubahan);
    $('#ur_indikator_tujuan_rpjmd').val(data.nm_indikator);
    $('#kd_indikator_tujuan_rpjmd').val(data.kd_indikator);
    $('#inditujuan1_edit').val(data.angka_tahun1);
    $('#inditujuan2_edit').val(data.angka_tahun2);
    $('#inditujuan3_edit').val(data.angka_tahun3);
    $('#inditujuan4_edit').val(data.angka_tahun4);
    $('#inditujuan5_edit').val(data.angka_tahun5);
    $('#inditujuan_awal_edit').val(data.angka_awal_periode);
    $('#inditujuan_akhir_edit').val(data.angka_akhir_periode);
    $('#satuan_tujuan_indikator_edit').val(data.uraian_satuan);
    $('#ModalIndikatorTujuan').modal('show');
});

$('.modal-footer').on('click', '.editTujuanIndikator', function() {
    $.ajaxSetup({
       headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
    });

    $.ajax({
        type: 'post',
        url: './rpjmd/editIndikatorTujuan',
        data: {
            '_token': $('input[name=_token]').val(),
            'id_indikator_tujuan_rpjmd': $('#id_indikator_tujuan_rpjmd_edit').val(),
            'thn_id': $('#thn_id_indikator_tujuan_edit').val(),
            'thn_id': $('#thn_id_indikator_tujuan_edit').val(),
            'no_urut': $('#no_urut_indikator_tujuan_edit').val(),
            'id_tujuan_rpjmd': $('#id_tujuan_rpjmd_indikator_edit').val(),
            'id_perubahan': $('#id_perubahan_indikator_tujuan_edit').val(),
            'kd_indikator': $('#kd_indikator_tujuan_rpjmd').val(),
            'uraian_indikator_sasaran_rpjmd': $('#ur_indikator_tujuan_rpjmd').val(),
            'tolok_ukur_indikator': $('#ur_indikator_tujuan_rpjmd').val(),
            'angka_awal_periode': $('#inditujuan_awal_edit').val(),
            'angka_tahun1': $('#inditujuan1_edit').val(),
            'angka_tahun2': $('#inditujuan2_edit').val(),
            'angka_tahun3': $('#inditujuan3_edit').val(),
            'angka_tahun4': $('#inditujuan4_edit').val(),
            'angka_tahun5': $('#inditujuan5_edit').val(),
            'angka_akhir_periode': $('#inditujuan_akhir_edit').val(),    
        },
        success: function(data) {
            $('#tblTujuan').DataTable().ajax.reload(null,false);
              if(data.status_pesan==1){
                createPesan(data.pesan,"success");
                } else {
                createPesan(data.pesan,"danger"); 
              }

        }
    });
});

$(document).on('click', '.btnHapusIndikator', function() {
    var data = tblInTujuan.row( $(this).parents('tr') ).data();
      $('.actionBtn').addClass('delete');
      $('.modal-title').text('Hapus Referensi Indikator');
      $('.form-horizontal').hide();
      $('#id_tujuan_indikator_hapus').val(data.id_indikator_tujuan_rpjmd);
      $('#nm_tujuan_indikator_hapus').html(data.nm_indikator);
      $('#HapusModal').modal('show');

}); 
    
$('.modal-footer').on('click', '.delete', function() {
      $.ajaxSetup({
         headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
      });
    
      $.ajax({
        type: 'post',
        url: './rpjmd/delIndikatorTujuan',
        data: {
          '_token': $('input[name=_token]').val(),
          'id_indikator_tujuan_rpjmd': $('#id_tujuan_indikator_hapus').val(),
        },
        success: function(data) {
            $('#tblTujuan').DataTable().ajax.reload(null,false);
            if(data.status_pesan==1){
              createPesan(data.pesan,"success");
              } else {
              createPesan(data.pesan,"danger"); 
            }
        }
      });
});

  var cariindikator
  $(document).on('click', '.btnCariIndi', function() {    
    $('#judulModal').text('Daftar Indikator yang terdapat dalam RPJMD/Renstra');    
    $('#cariIndikator').modal('show');

    cariindikator = $('#tblCariIndikator').DataTable( {
        processing: true,
        serverSide: true,
        dom: 'bfrtIp',
        autoWidth : false,
        language: {
              "decimal": ",",
              "thousands": ".",
              "sEmptyTable":   "Tidak ada data yang tersedia pada tabel ini",
              "sProcessing":   "Sedang memproses...",
              "sLengthMenu":   "Tampilkan _MENU_ entri",
              "sZeroRecords":  "Tidak ditemukan data yang sesuai",
              "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
              "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
              "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
              "sInfoPostFix":  "",
              "sSearch":       "Cari:",
              "sUrl":          "",
              "oPaginate": {
                  "sFirst":    "Pertama",
                  "sPrevious": "Sebelumnya",
                  "sNext":     "Selanjutnya",
                  "sLast":     "Terakhir"
              }
            },
        "ajax": {"url": "./admin/parameter/getRefIndikator"},
        "columns": [
              { data: 'no_urut'},
              { data: 'nm_indikator'},
              { data: 'uraian_satuan'},
              { data: 'type_display'},
              { data: 'kualitas_display'},
              { data: 'jenis_display'},
              { data: 'sifat_display'}
            ],
        "order": [[0, 'asc']],
        "bDestroy": true
    });
  });

  $(document).on('click', '.edit-tujuan', function() {
    var data = tblTujuan.row( $(this).parents('tr') ).data();
	    $('.actionBtn_tujuan').addClass('edittujuan');
	    $('.modal-title').text('Data Tujuan Daerah');
	    $('.form-horizontal').show();
	    $('#id_tujuan_rpjmd_edit').val($(this).data('id_tujuan_rpjmd_tujuan'));
	    $('#thn_id_tujuan_edit').val($(this).data('thn_id_tujuan'));
	    $('#no_urut_tujuan_edit').val($(this).data('no_urut_tujuan'));
	    $('#id_misi_rpjmd_tujuan_edit').val($(this).data('id_misi_rpjmd_tujuan'));
	    $('#id_perubahan_tujuan_edit').val($(this).data('id_perubahan_tujuan'));
	    $('#ur_tujuan_rpjmd_edit').val($(this).data('uraian_tujuan_rpjmd_tujuan'));
      $('#id_misi_tujuan_edit').val(data.id_misi);
	    $('#EditTujuan').modal('show');
	  });

$('.modal-footer').on('click', '.edittujuan', function() {
    $.ajaxSetup({
       headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
    });

    $.ajax({
        type: 'post',
        url: './rpjmd/edittujuan',
        data: {
            '_token': $('input[name=_token]').val(),
            'id_tujuan_rpjmd_edit': $('#id_tujuan_rpjmd_edit').val(),
            'thn_id_tujuan_edit': $('#thn_id_tujuan_edit').val(),
            'no_urut_tujuan_edit': $('#no_urut_tujuan_edit').val(),
            'id_misi_rpjmd_tujuan_edit': $('#id_misi_rpjmd_tujuan_edit').val(),
            'id_perubahan_tujuan_edit': $('#id_perubahan_tujuan_edit').val(),
            'ur_tujuan_rpjmd_edit': $('#ur_tujuan_rpjmd_edit').val(),
        },
        success: function(data) {
           tblTujuan.ajax.reload(null,false);
              if(data.status_pesan==1){
                createPesan(data.pesan,"success");
                } else {
                createPesan(data.pesan,"danger"); 
              }
        }
    });
});

$('#tblTujuan tbody').on( 'dblclick', 'tr', function () {
	    var data = tblTujuan.row(this).data();
	    id_tujuan_rpjmd =  data.id_tujuan_rpjmd;
	    $('.nav-tabs a[href="#sasaran"]').tab('show');
      loadTblSasaran(id_tujuan_rpjmd);
});
	  
var tblSasaran;
function loadTblSasaran(id_tujuan_rpjmd){
tblSasaran = $('#tblSasaran').DataTable( {
          processing: true,
          serverSide: true,
          dom: 'BFrtip',
          responsive: true,                
          autoWidth : false,
          order: [[0, 'asc']],
          bDestroy: true,
          language: {
              "decimal": ",",
              "thousands": ".",
              "sEmptyTable":   "Tidak ada data yang tersedia pada tabel ini",
              "sProcessing":   "Sedang memproses...",
              "sLengthMenu":   "Tampilkan _MENU_ entri",
              "sZeroRecords":  "Tidak ditemukan data yang sesuai",
              "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
              "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
              "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
              "sInfoPostFix":  "",
              "sSearch":       "Cari:",
              "sUrl":          "",
              "oPaginate": {
                  "sFirst":    "Pertama",
                  "sPrevious": "Sebelumnya",
                  "sNext":     "Selanjutnya",
                  "sLast":     "Terakhir"
              }
            },
          "ajax": {"url": "./rpjmd/sasaran/"+id_tujuan_rpjmd},
          "columns": [
                {
                  "className":      'details-control',
                  "orderable":      false,
                  "searchable":      false,
                  "data":           null,
                  "defaultContent": '',
                  "width" : "5px"
                },
                { data: 'id_visi_rpjmd', sClass: "dt-center"},
                { data: 'id_misi', sClass: "dt-center"},
                { data: 'id_tujuan', sClass: "dt-center"},
                { data: 'no_urut', sClass: "dt-center"},
                { data: 'uraian_sasaran_rpjmd'},
                { data: 'jml_indikator', sClass: "dt-center"},
                { data: 'action', 'searchable': false, 'orderable':false }
              ]
        } );
}

  var tblInSasaran;
    function initInSasaran(tableId, data) {
        tblInSasaran=$('#' + tableId).DataTable({
                processing: true,
                serverSide: true,
                ajax: data.details_url,
                dom : 'BfRtIP',
                autoWidth: false,
                language: {
                    "decimal": ",",
                    "thousands": ".",
                    "sEmptyTable":   "Tidak ada data yang tersedia pada tabel ini",
                    "sProcessing":   "Sedang memproses...",
                    "sLengthMenu":   "Tampilkan _MENU_ entri",
                    "sZeroRecords":  "Tidak ditemukan data yang sesuai",
                    "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
                    "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                    "sInfoPostFix":  "",
                    "sSearch":       "Cari:",
                    "sUrl":          "",
                    "oPaginate": {
                        "sFirst":    "Pertama",
                        "sPrevious": "Sebelumnya",
                        "sNext":     "Selanjutnya",
                        "sLast":     "Terakhir"
                    }
                  },
                "columns": [
                            { data: 'urut', sClass: "dt-center"},
                            { data: 'uraian_indikator_sasaran_rpjmd'},
                            { data: 'action', 'searchable': false, 'orderable':false, sClass: "dt-center" }
                          ],
                "order": [[0, 'asc']],
                "bDestroy": true
            })
    };

  $('#tblSasaran tbody').on('click', 'td.details-control', function () {
      var tr = $(this).closest('tr');
      var row = tblSasaran.row( tr );
      var tableId = 'inSasaran-' + row.data().id_sasaran_rpjmd;

      if (row.child.isShown()) {
          row.child.hide();
          tr.removeClass('shown');
      } else {
          row.child(detInSasaran(row.data())).show();
          initInSasaran(tableId, row.data());
          tr.addClass('shown');
          tr.next().find('td').addClass('no-padding bg-gray');
      }    
  });

  $(document).on('click', '.btnEditSasaran', function() {    
    var data = tblSasaran.row( $(this).parents('tr') ).data();
	    $('.actionBtn_sasaran').addClass('editsasaran');
	    $('.modal-title').text('Edit Data sasaran Daerah');
	    $('.form-horizontal').show();
	    $('#id_sasaran_rpjmd_edit').val(data.id_sasaran_rpjmd);
	    $('#thn_id_sasaran_edit').val(data.thn_id);
	    $('#no_urut_sasaran_edit').val(data.no_urut);
	    $('#id_tujuan_rpjmd_sasaran_edit').val(data.id_tujuan_rpjmd);
	    $('#id_perubahan_sasaran_edit').val(data.id_perubahan);
	    $('#ur_sasaran_rpjmd_edit').val(data.uraian_sasaran_rpjmd);
      $('#id_tujuan_sasaran_edit').val(data.id_tujuan);
	    $('#EditSasaranModal').modal('show');
    });


$(document).on('click', '.btnAddIndikatorSasaran', function() {
    var data = tblSasaran.row( $(this).parents('tr') ).data();
    $('.btnSimpanSasaranIndikator').removeClass('editSasaranIndikator');
    $('.btnSimpanSasaranIndikator').addClass('addSasaranIndikator');
    $('.modal-title').text('Data Indikator Sasaran Daerah');
    $('.form-horizontal').show();
    $('#id_indikator_sasaran_rpjmd_edit').val(null);
    $('#thn_id_indikator_sasaran_edit').val(tahun_rpjmd);
    $('#id_sasaran_rpjmd_indikator_edit').val(data.id_sasaran_rpjmd);
    $('#no_urut_indikator_sasaran_edit').val(1);
    $('#id_perubahan_indikator_sasaran_edit').val(0);
    $('#ur_indikator_sasaran_rpjmd').val(null);
    $('#kd_indikator_sasaran_rpjmd').val(null);
    $('#indisasaran1_edit').val(0);
    $('#indisasaran2_edit').val(0);
    $('#indisasaran3_edit').val(0);
    $('#indisasaran4_edit').val(0);
    $('#indisasaran5_edit').val(0);
    $('#indisasaran_awal_edit').val(0);
    $('#indisasaran_akhir_edit').val(0);
    $('#satuan_sasaran_indikator_edit').val(null);
    $('#ModalIndikatorSasaran').modal('show');
});

$('.modal-footer').on('click', '.addSasaranIndikator', function() {
    $.ajaxSetup({
       headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
    });

    $.ajax({
        type: 'post',
        url: './rpjmd/addIndikatorSasaran',
        data: {
            '_token': $('input[name=_token]').val(),
            'thn_id': $('#thn_id_indikator_sasaran_edit').val(),
            'no_urut': $('#no_urut_indikator_sasaran_edit').val(),
            'id_sasaran_rpjmd': $('#id_sasaran_rpjmd_indikator_edit').val(),
            'id_perubahan': $('#id_perubahan_indikator_sasaran_edit').val(),
            'kd_indikator': $('#kd_indikator_sasaran_rpjmd').val(),
            'uraian_indikator_sasaran_rpjmd': $('#ur_indikator_sasaran_rpjmd').val(),
            'tolok_ukur_indikator': $('#ur_indikator_sasaran_rpjmd').val(),
            'angka_awal_periode': $('#indisasaran_awal_edit').val(),
            'angka_tahun1': $('#indisasaran1_edit').val(),
            'angka_tahun2': $('#indisasaran2_edit').val(),
            'angka_tahun3': $('#indisasaran3_edit').val(),
            'angka_tahun4': $('#indisasaran4_edit').val(),
            'angka_tahun5': $('#indisasaran5_edit').val(),
            'angka_akhir_periode': $('#indisasaran_akhir_edit').val(),    
        },
        success: function(data) {
            tblSasaran.ajax.reload(null,false);
              if(data.status_pesan==1){
                createPesan(data.pesan,"success");
                } else {
                createPesan(data.pesan,"danger"); 
              }
        }
    });
});

$(document).on('click', '.btnEditIndikatorSasaran', function() {
var data = tblInSasaran.row( $(this).parents('tr') ).data();
    $('.btnSimpanSasaranIndikator').removeClass('addSasaranIndikator');
    $('.btnSimpanSasaranIndikator').addClass('editSasaranIndikator');
  $('.modal-title').text('Data Indikator Sasaran Daerah');
  $('.form-horizontal').show();
    $('#id_indikator_sasaran_rpjmd_edit').val(data.id_indikator_sasaran_rpjmd);
    $('#thn_id_indikator_sasaran_edit').val(data.thn_id);
    $('#id_sasaran_rpjmd_indikator_edit').val(data.id_sasaran_rpjmd);
    $('#no_urut_indikator_sasaran_edit').val(data.no_urut);
    $('#id_perubahan_indikator_sasaran_edit').val(data.id_perubahan);
    $('#ur_indikator_sasaran_rpjmd').val(data.nm_indikator);
    $('#kd_indikator_sasaran_rpjmd').val(data.kd_indikator);
    $('#indisasaran1_edit').val(data.angka_tahun1);
    $('#indisasaran2_edit').val(data.angka_tahun2);
    $('#indisasaran3_edit').val(data.angka_tahun3);
    $('#indisasaran4_edit').val(data.angka_tahun4);
    $('#indisasaran5_edit').val(data.angka_tahun5);
    $('#indisasaran_awal_edit').val(data.angka_awal_periode);
    $('#indisasaran_akhir_edit').val(data.angka_akhir_periode);
    $('#satuan_sasaran_indikator_edit').val(data.uraian_satuan);
    $('#ModalIndikatorSasaran').modal('show');
});

$('.modal-footer').on('click', '.editSasaranIndikator', function() {
    $.ajaxSetup({
       headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
    });

    $.ajax({
        type: 'post',
        url: './rpjmd/editIndikatorSasaran',
        data: {
            '_token': $('input[name=_token]').val(),
            'id_indikator_sasaran_rpjmd': $('#id_indikator_sasaran_rpjmd_edit').val(),
            'thn_id': $('#thn_id_indikator_sasaran_edit').val(),
            'thn_id': $('#thn_id_indikator_sasaran_edit').val(),
            'no_urut': $('#no_urut_indikator_sasaran_edit').val(),
            'id_sasaran_rpjmd': $('#id_sasaran_rpjmd_indikator_edit').val(),
            'id_perubahan': $('#id_perubahan_indikator_sasaran_edit').val(),
            'kd_indikator': $('#kd_indikator_sasaran_rpjmd').val(),
            'uraian_indikator_sasaran_rpjmd': $('#ur_indikator_sasaran_rpjmd').val(),
            'tolok_ukur_indikator': $('#ur_indikator_sasaran_rpjmd').val(),
            'angka_awal_periode': $('#indisasaran_awal_edit').val(),
            'angka_tahun1': $('#indisasaran1_edit').val(),
            'angka_tahun2': $('#indisasaran2_edit').val(),
            'angka_tahun3': $('#indisasaran3_edit').val(),
            'angka_tahun4': $('#indisasaran4_edit').val(),
            'angka_tahun5': $('#indisasaran5_edit').val(),
            'angka_akhir_periode': $('#indisasaran_akhir_edit').val(),    
        },
        success: function(data) {
            tblSasaran.ajax.reload(null,false);
              if(data.status_pesan==1){
                createPesan(data.pesan,"success");
                } else {
                createPesan(data.pesan,"danger"); 
              }
        }
    });
});

$(document).on('click', '.btnHapusIndikatorSasaran', function() {
    var data = tblInSasaran.row( $(this).parents('tr') ).data();
      $('.btnDelSasaranIndikator').addClass('delSasaranIndikator');
      $('.modal-title').text('Hapus Referensi Indikator');
      $('.form-horizontal').hide();
      $('#id_sasaran_indikator_hapus').val(data.id_indikator_sasaran_rpjmd);
      $('#nm_sasaran_indikator_hapus').html(data.nm_indikator);
      $('#HapusSasaranIndikatorModal').modal('show');

});
    
$('.modal-footer').on('click', '.delSasaranIndikator', function() {
      $.ajaxSetup({
         headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
      });
    
      $.ajax({
        type: 'post',
        url: './rpjmd/delIndikatorSasaran',
        data: {
          '_token': $('input[name=_token]').val(),
          'id_indikator_sasaran_rpjmd': $('#id_sasaran_indikator_hapus').val(),
        },
        success: function(data) {
            tblSasaran.ajax.reload(null,false);
            if(data.status_pesan==1){
              createPesan(data.pesan,"success");
              } else {
              createPesan(data.pesan,"danger"); 
            }
        }
      });
});

$('.modal-footer').on('click', '.editsasaran', function() {
  $.ajaxSetup({
     headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
  });

  $.ajax({
      type: 'post',
      url: './rpjmd/editsasaran',
      data: {
          '_token': $('input[name=_token]').val(),
          'id_sasaran_rpjmd_edit': $('#id_sasaran_rpjmd_edit').val(),
          'thn_id_sasaran_edit': $('#thn_id_sasaran_edit').val(),
          'no_urut_sasaran_edit': $('#no_urut_sasaran_edit').val(),
          'id_tujuan_rpjmd_sasaran_edit': $('#id_tujuan_rpjmd_sasaran_edit').val(),
          'id_perubahan_sasaran_edit': $('#id_perubahan_sasaran_edit').val(),
          'ur_sasaran_rpjmd_edit': $('#ur_sasaran_rpjmd_edit').val(),
      },
      success: function(data) {
          tblSasaran.ajax.reload(null,false);
              if(data.status_pesan==1){
                createPesan(data.pesan,"success");
                } else {
                createPesan(data.pesan,"danger"); 
              }
      }
  });
});

$('#tblSasaran tbody').on( 'dblclick', 'tr', function () {
    var data = tblSasaran.row(this).data();
    id_sasaran_rpjmd =  data.id_sasaran_rpjmd;
    $('.nav-tabs a[href="#program"]').tab('show');
    loadTblProgram(id_sasaran_rpjmd);
    // $('#tblProgram').DataTable().ajax.url("./rpjmd/program/"+id_sasaran_rpjmd).load();
  } );


  var tblKebijakan ;
  function loadTblKebijakan(id_sasaran_rpjmd){
  tblKebijakan = $('#tblKebijakan').DataTable( {
          processing: true,
          serverSide: true,          
          dom: 'BFrtip',
          responsive: true,                
          autoWidth : false,
          order: [[0, 'asc']],
          bDestroy: true,
          language: {
              "decimal": ",",
              "thousands": ".",
              "sEmptyTable":   "Tidak ada data yang tersedia pada tabel ini",
              "sProcessing":   "Sedang memproses...",
              "sLengthMenu":   "Tampilkan _MENU_ entri",
              "sZeroRecords":  "Tidak ditemukan data yang sesuai",
              "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
              "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
              "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
              "sInfoPostFix":  "",
              "sSearch":       "Cari:",
              "sUrl":          "",
              "oPaginate": {
                  "sFirst":    "Pertama",
                  "sPrevious": "Sebelumnya",
                  "sNext":     "Selanjutnya",
                  "sLast":     "Terakhir"
              }
            },
          "ajax": {"url": "./rpjmd/kebijakan/"+id_sasaran_rpjmd},
          "columns": [
                { data: 'id_visi_rpjmd', sClass: "dt-center"},
                { data: 'id_misi', sClass: "dt-center"},
                { data: 'id_tujuan', sClass: "dt-center"},
                { data: 'id_sasaran', sClass: "dt-center"},
                { data: 'no_urut', sClass: "dt-center"},
                { data: 'uraian_kebijakan_rpjmd'},
                { data: 'action', 'searchable': false, 'orderable':false }
            ]
        });
  };

  $(document).on('click', '.edit-kebijakan', function() {
      var data = tblKebijakan.row( $(this).parents('tr') ).data();

	    $('.actionBtn_kebijakan').addClass('editkebijakan');
	    $('.modal-title').text('Edit Data kebijakan Daerah');
	    $('.form-horizontal').show();
	    $('#id_kebijakan_rpjmd_edit').val($(this).data('id_kebijakan_rpjmd_kebijakan'));
	    $('#thn_id_kebijakan_edit').val($(this).data('thn_id_kebijakan'));
	    $('#no_urut_kebijakan_edit').val($(this).data('no_urut_kebijakan'));
	    $('#id_sasaran_rpjmd_kebijakan_edit').val($(this).data('id_sasaran_rpjmd_kebijakan'));
	    $('#id_perubahan_kebijakan_edit').val($(this).data('id_perubahan_kebijakan'));
	    $('#ur_kebijakan_rpjmd_edit').val($(this).data('uraian_kebijakan_rpjmd_kebijakan'));
      $('#id_sasaran_kebijakan_edit').val(data.id_sasaran);
	    $('#Editkebijakan').modal('show');
	  });

$('.modal-footer').on('click', '.editkebijakan', function() {
  $.ajaxSetup({
     headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
  });

  $.ajax({
      type: 'post',
      url: './rpjmd/editkebijakan',
      data: {
          '_token': $('input[name=_token]').val(),
          'id_kebijakan_rpjmd_edit': $('#id_kebijakan_rpjmd_edit').val(),
          'thn_id_kebijakan_edit': $('#thn_id_kebijakan_edit').val(),
          'no_urut_kebijakan_edit': $('#no_urut_kebijakan_edit').val(),
          'id_sasaran_rpjmd_kebijakan_edit': $('#id_sasaran_rpjmd_kebijakan_edit').val(),
          'id_perubahan_kebijakan_edit': $('#id_perubahan_kebijakan_edit').val(),
          'ur_kebijakan_rpjmd_edit': $('#ur_kebijakan_rpjmd_edit').val(),
      },
      success: function(data) {
          tblKebijakan.ajax.reload(null,false);
          if(data.status_pesan==1){
            createPesan(data.pesan,"success");
          } else {
            createPesan(data.pesan,"danger"); 
          }
      }
  });
});

var tblStrategi;
  function loadTblStrategi(id_sasaran_rpjmd){
   tblStrategi = $('#tblStrategi').DataTable( {
          processing: true,
          serverSide: true,
          dom: 'BFrtip',
          responsive: true,                
          autoWidth : false,
          order: [[0, 'asc']],
          bDestroy: true,
          language: {
              "decimal": ",",
              "thousands": ".",
              "sEmptyTable":   "Tidak ada data yang tersedia pada tabel ini",
              "sProcessing":   "Sedang memproses...",
              "sLengthMenu":   "Tampilkan _MENU_ entri",
              "sZeroRecords":  "Tidak ditemukan data yang sesuai",
              "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
              "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
              "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
              "sInfoPostFix":  "",
              "sSearch":       "Cari:",
              "sUrl":          "",
              "oPaginate": {
                  "sFirst":    "Pertama",
                  "sPrevious": "Sebelumnya",
                  "sNext":     "Selanjutnya",
                  "sLast":     "Terakhir"
              }
            },
          "ajax": {"url": "./rpjmd/strategi/"+id_sasaran_rpjmd},
          "columns": [
                { data: 'id_visi_rpjmd', sClass: "dt-center"},
                { data: 'id_misi', sClass: "dt-center"},
                { data: 'id_tujuan', sClass: "dt-center"},
                { data: 'id_sasaran', sClass: "dt-center"},
                { data: 'no_urut', sClass: "dt-center"},
                { data: 'uraian_strategi_rpjmd'},
                { data: 'action', 'searchable': false, 'orderable':false }
            ]
        });
 };

  $(document).on('click', '.edit-strategi', function() {
      var data = tblStrategi.row( $(this).parents('tr') ).data();

	    $('.actionBtn_strategi').addClass('editstrategi');
	    $('.modal-title').text('Edit Data strategi Daerah');
	    $('.form-horizontal').show();
	    $('#id_strategi_rpjmd_edit').val($(this).data('id_strategi_rpjmd_strategi'));
	    $('#thn_id_strategi_edit').val($(this).data('thn_id_strategi'));
	    $('#no_urut_strategi_edit').val($(this).data('no_urut_strategi'));
	    $('#id_sasaran_rpjmd_strategi_edit').val($(this).data('id_sasaran_rpjmd_strategi'));
	    $('#id_perubahan_strategi_edit').val($(this).data('id_perubahan_strategi'));
	    $('#ur_strategi_rpjmd_edit').val($(this).data('uraian_strategi_rpjmd_strategi'));
      $('#id_sasaran_strategi_edit').val(data.id_sasaran);
	    $('#Editstrategi').modal('show');
	  });

$('.modal-footer').on('click', '.editstrategi', function() {
$.ajaxSetup({
 headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
});
		//alert($('input[name=_token]').val());
$.ajax({
  type: 'post',
  url: './rpjmd/editstrategi',
  data: {
      '_token': $('input[name=_token]').val(),
      'id_strategi_rpjmd_edit': $('#id_strategi_rpjmd_edit').val(),
      'thn_id_strategi_edit': $('#thn_id_strategi_edit').val(),
      'no_urut_strategi_edit': $('#no_urut_strategi_edit').val(),
      'id_sasaran_rpjmd_strategi_edit': $('#id_sasaran_rpjmd_strategi_edit').val(),
      'id_perubahan_strategi_edit': $('#id_perubahan_strategi_edit').val(),
      'ur_strategi_rpjmd_edit': $('#ur_strategi_rpjmd_edit').val(),
  },
  success: function(data) {
      tblStrategi.ajax.reload(null,false);
      if(data.status_pesan==1){
        createPesan(data.pesan,"success");
      } else {
        createPesan(data.pesan,"danger"); 
      }
  }
});
});

  var tblProgram;
  function loadTblProgram(id_sasaran_rpjmd){
  tblProgram = $('#tblProgram').DataTable( {
          processing: true,
          serverSide: true,
          dom: 'BFrtip',
          responsive: true,                
          autoWidth : false,
          order: [[0, 'asc']],
          bDestroy: true,
          language: {
              "decimal": ",",
              "thousands": ".",
              "sEmptyTable":   "Tidak ada data yang tersedia pada tabel ini",
              "sProcessing":   "Sedang memproses...",
              "sLengthMenu":   "Tampilkan _MENU_ entri",
              "sZeroRecords":  "Tidak ditemukan data yang sesuai",
              "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
              "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
              "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
              "sInfoPostFix":  "",
              "sSearch":       "Cari:",
              "sUrl":          "",
              "oPaginate": {
                  "sFirst":    "Pertama",
                  "sPrevious": "Sebelumnya",
                  "sNext":     "Selanjutnya",
                  "sLast":     "Terakhir"
              }
            },
          "ajax": {"url": "./rpjmd/program/"+id_sasaran_rpjmd},
          "columns": [
                {
                  "className":      'details-control',
                  "orderable":      false,
                  "searchable":      false,
                  "data":           null,
                  "defaultContent": '',
                  "width" : "5px"
                },
                { data: 'kd_sasaran', sClass: "dt-center"},
                { data: 'no_urut', sClass: "dt-center"},
                { data: 'uraian_program_rpjmd'},
                { data: 'pagu_tahun1',
                  render: $.fn.dataTable.render.number( '.', ',', 0, '' ), sClass: "dt-right"},
                { data: 'pagu_tahun2',
                  render: $.fn.dataTable.render.number( '.', ',', 0, '' ), sClass: "dt-right"},
                { data: 'pagu_tahun3',
                  render: $.fn.dataTable.render.number( '.', ',', 0, '' ), sClass: "dt-right"},
                { data: 'pagu_tahun4',
                  render: $.fn.dataTable.render.number( '.', ',', 0, '' ), sClass: "dt-right"},
                { data: 'pagu_tahun5',
                  render: $.fn.dataTable.render.number( '.', ',', 0, '' ), sClass: "dt-right"},
                { data: 'total_pagu',
                  render: $.fn.dataTable.render.number( '.', ',', 0, '' ), sClass: "dt-right"},
                { data: 'action', 'searchable': false, 'orderable':false }
            ],
            "columnDefs": [ {
                  "visible": false
              } ]
          });
  };

  var tblInProgram;
    function initInProgram(tableId, data) {
        tblInProgram=$('#' + tableId).DataTable({
                processing: true,
                serverSide: true,
                ajax: data.details_url,
                dom : 'BfRtIP',
                autoWidth: false,
                language: {
                  "decimal": ",",
                  "thousands": ".",
                  "sEmptyTable":   "Tidak ada data yang tersedia pada tabel ini",
                  "sProcessing":   "Sedang memproses...",
                  "sLengthMenu":   "Tampilkan _MENU_ entri",
                  "sZeroRecords":  "Tidak ditemukan data yang sesuai",
                  "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                  "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
                  "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                  "sInfoPostFix":  "",
                  "sSearch":       "Cari:",
                  "sUrl":          "",
                  "oPaginate": {
                      "sFirst":    "Pertama",
                      "sPrevious": "Sebelumnya",
                      "sNext":     "Selanjutnya",
                      "sLast":     "Terakhir"
                  }
                },
                "columns": [
                            { data: 'urut', sClass: "dt-center"},
                            { data: 'uraian_indikator_program_rpjmd'},
                            { data: 'action', 'searchable': false, 'orderable':false, sClass: "dt-center" }
                          ],
                "order": [[0, 'asc']],
                "bDestroy": true
            })

  };

  $('#tblProgram tbody').on('click', 'td.details-control', function () {
      var tr = $(this).closest('tr');
      var row = tblProgram.row( tr );
      var tableId = 'inProgram-' + row.data().id_program_rpjmd;

      if (row.child.isShown()) {
          row.child.hide();
          tr.removeClass('shown');
      } else {
          row.child(detInProgram(row.data())).show();
          initInProgram(tableId, row.data());
          tr.addClass('shown');
          tr.next().find('td').addClass('no-padding bg-gray');
      }    
  });

  $(document).on('click', '.btnAddIndikatorProgram', function() {
        var data = tblProgram.row( $(this).parents('tr') ).data();
        $('.btnSimpanProgramIndikator').removeClass('editProgramIndikator');
        $('.btnSimpanProgramIndikator').addClass('addProgramIndikator');
        $('.modal-title').text('Data Indikator Program Daerah');
        $('.form-horizontal').show();
        $('#id_indikator_program_rpjmd_edit').val(null);
        $('#thn_id_indikator_program_edit').val(tahun_rpjmd);
        $('#id_program_rpjmd_indikator_edit').val(data.id_program_rpjmd);
        $('#no_urut_indikator_program_edit').val(1);
        $('#id_perubahan_indikator_program_edit').val(0);
        $('#ur_indikator_program_rpjmd').val(null);
        $('#kd_indikator_program_rpjmd').val(null);
        $('#indiprogram1_edit').val(0);
        $('#indiprogram2_edit').val(0);
        $('#indiprogram3_edit').val(0);
        $('#indiprogram4_edit').val(0);
        $('#indiprogram5_edit').val(0);
        $('#indiprogram_awal_edit').val(0);
        $('#indiprogram_akhir_edit').val(0);
        $('#satuan_program_indikator_edit').val(null);
        $('#ModalIndikatorProgram').modal('show');
    });

    $('.modal-footer').on('click', '.addProgramIndikator', function() {
        $.ajaxSetup({
        headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
        });

        $.ajax({
            type: 'post',
            url: './rpjmd/addIndikatorProgram',
            data: {
                '_token': $('input[name=_token]').val(),
                'thn_id': $('#thn_id_indikator_program_edit').val(),
                'no_urut': $('#no_urut_indikator_program_edit').val(),
                'id_program_rpjmd': $('#id_program_rpjmd_indikator_edit').val(),
                'id_perubahan': $('#id_perubahan_indikator_program_edit').val(),
                'kd_indikator': $('#kd_indikator_program_rpjmd').val(),
                'uraian_indikator_program_rpjmd': $('#ur_indikator_program_rpjmd').val(),
                'tolok_ukur_indikator': $('#ur_indikator_program_rpjmd').val(),
                'angka_awal_periode': $('#indiprogram_awal_edit').val(),
                'angka_tahun1': $('#indiprogram1_edit').val(),
                'angka_tahun2': $('#indiprogram2_edit').val(),
                'angka_tahun3': $('#indiprogram3_edit').val(),
                'angka_tahun4': $('#indiprogram4_edit').val(),
                'angka_tahun5': $('#indiprogram5_edit').val(),
                'angka_akhir_periode': $('#indiprogram_akhir_edit').val(),    
            },
            success: function(data) {
                $('#tblProgram').DataTable().ajax.reload(null,false);
                if(data.status_pesan==1){
                    createPesan(data.pesan,"success");
                    } else {
                    createPesan(data.pesan,"danger"); 
                }

            }
        });
    });

    $(document).on('click', '.btnEditIndikatorProgram', function() {
    var data = tblInProgram.row( $(this).parents('tr') ).data();
        $('.btnSimpanProgramIndikator').removeClass('addProgramIndikator');
        $('.btnSimpanProgramIndikator').addClass('editProgramIndikator');
        $('.modal-title').text('Data Indikator Program Daerah');
        $('.form-horizontal').show();
        $('#id_indikator_program_rpjmd_edit').val(data.id_indikator_program_rpjmd);
        $('#thn_id_indikator_program_edit').val(data.thn_id);
        $('#id_program_rpjmd_indikator_edit').val(data.id_program_rpjmd);
        $('#no_urut_indikator_program_edit').val(data.no_urut);
        $('#id_perubahan_indikator_program_edit').val(data.id_perubahan);
        $('#ur_indikator_program_rpjmd').val(data.nm_indikator);
        $('#kd_indikator_program_rpjmd').val(data.id_indikator);
        $('#indiprogram1_edit').val(data.angka_tahun1);
        $('#indiprogram2_edit').val(data.angka_tahun2);
        $('#indiprogram3_edit').val(data.angka_tahun3);
        $('#indiprogram4_edit').val(data.angka_tahun4);
        $('#indiprogram5_edit').val(data.angka_tahun5);
        $('#indiprogram_awal_edit').val(data.angka_awal_periode);
        $('#indiprogram_akhir_edit').val(data.angka_akhir_periode);
        $('#satuan_program_indikator_edit').val(data.uraian_satuan);
        $('#ModalIndikatorProgram').modal('show');
    });

    $('.modal-footer').on('click', '.editProgramIndikator', function() {
        $.ajaxSetup({
        headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
        });

        $.ajax({
            type: 'post',
            url: './rpjmd/editIndikatorProgram',
            data: {
                '_token': $('input[name=_token]').val(),
                'id_indikator_program_rpjmd': $('#id_indikator_program_rpjmd_edit').val(),
                'thn_id': $('#thn_id_indikator_program_edit').val(),
                'thn_id': $('#thn_id_indikator_program_edit').val(),
                'no_urut': $('#no_urut_indikator_program_edit').val(),
                'id_program_rpjmd': $('#id_program_rpjmd_indikator_edit').val(),
                'id_perubahan': $('#id_perubahan_indikator_program_edit').val(),
                'kd_indikator': $('#kd_indikator_program_rpjmd').val(),
                'uraian_indikator_program_rpjmd': $('#ur_indikator_program_rpjmd').val(),
                'tolok_ukur_indikator': $('#ur_indikator_program_rpjmd').val(),
                'angka_awal_periode': $('#indiprogram_awal_edit').val(),
                'angka_tahun1': $('#indiprogram1_edit').val(),
                'angka_tahun2': $('#indiprogram2_edit').val(),
                'angka_tahun3': $('#indiprogram3_edit').val(),
                'angka_tahun4': $('#indiprogram4_edit').val(),
                'angka_tahun5': $('#indiprogram5_edit').val(),
                'angka_akhir_periode': $('#indiprogram_akhir_edit').val(),    
            },
            success: function(data) {
                $('#tblProgram').DataTable().ajax.reload(null,false);
                if(data.status_pesan==1){
                    createPesan(data.pesan,"success");
                    } else {
                    createPesan(data.pesan,"danger"); 
                }

            }
        });
    });

    $(document).on('click', '.btnHapusIndikatorProgram', function() {
        var data = tblInProgram.row( $(this).parents('tr') ).data();
        $('.btnDelProgramIndikator').addClass('delProgramIndikator');
        $('.modal-title').text('Hapus Referensi Indikator');
        $('.form-horizontal').hide();
        $('#id_program_indikator_hapus').val(data.id_indikator_program_rpjmd);
        $('#nm_program_indikator_hapus').html(data.nm_indikator);
        $('#HapusProgramIndikatorModal').modal('show');

    });
        
    $('.modal-footer').on('click', '.delProgramIndikator', function() {
        $.ajaxSetup({
            headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
        });
        
        $.ajax({
            type: 'post',
            url: './rpjmd/delIndikatorProgram',
            data: {
            '_token': $('input[name=_token]').val(),
            'id_indikator_program_rpjmd': $('#id_program_indikator_hapus').val(),
            },
            success: function(data) {
                $('#tblProgram').DataTable().ajax.reload(null,false);
                if(data.status_pesan==1){
                createPesan(data.pesan,"success");
                } else {
                createPesan(data.pesan,"danger"); 
                }
            }
        });
    });

  $(document).on('click', '.edit-program', function() {

      var data = tblProgram.row( $(this).parents('tr') ).data();

	    $('.actionBtn_program').addClass('editprogram');
	    $('.modal-title').text('Edit Data program Daerah');
	    $('.form-horizontal').show();
	    $('#id_program_rpjmd_edit').val($(this).data('id_program_rpjmd_program'));
	    $('#thn_id_program_edit').val($(this).data('thn_id_program'));
	    $('#no_urut_program_edit').val($(this).data('no_urut_program'));
	    $('#id_sasaran_rpjmd_program_edit').val($(this).data('id_sasaran_rpjmd_program'));
	    $('#id_perubahan_program_edit').val($(this).data('id_perubahan_program'));
	    $('#pagu1_edit').val($(this).data('pagu_tahun1_program'));
	    $('#pagu2_edit').val($(this).data('pagu_tahun2_program'));
	    $('#pagu3_edit').val($(this).data('pagu_tahun3_program'));
	    $('#pagu4_edit').val($(this).data('pagu_tahun4_program'));
	    $('#pagu5_edit').val($(this).data('pagu_tahun5_program'));
	    $('#ur_program_rpjmd_edit').val($(this).data('uraian_program_rpjmd_program'));
      $('#id_sasaran_program_edit').val(data.id_sasaran);
      $('#pagu_total_edit').val(data.total_pagua);
      // $('#thn_id_program_edit').setAttribute('readonly','readonly');
	    $('#Editprogram').modal('show');
	  });

$('.modal-footer').on('click', '.editprogram', function() {
$.ajaxSetup({
   headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
});

$.ajax({
    type: 'post',
    url: './rpjmd/editprogram',
    data: {
        '_token': $('input[name=_token]').val(),
        'id_program_rpjmd_edit': $('#id_program_rpjmd_edit').val(),
        'thn_id_program_edit': $('#thn_id_program_edit').val(),
        'no_urut_program_edit': $('#no_urut_program_edit').val(),
        'id_sasaran_rpjmd_program_edit': $('#id_sasaran_rpjmd_program_edit').val(),
        'id_perubahan_program_edit': $('#id_perubahan_program_edit').val(),
        'pagu1_edit': $('#pagu1_edit').val(),
        'pagu2_edit': $('#pagu2_edit').val(),
        'pagu3_edit': $('#pagu3_edit').val(),
        'pagu4_edit': $('#pagu4_edit').val(),
        'pagu5_edit': $('#pagu5_edit').val(),
        'pagu_total_edit': $('#pagu_total_edit').val(),
        'ur_program_rpjmd_edit': $('#ur_program_rpjmd_edit').val(),
    },
    success: function(data) {
        $('#tblProgram').DataTable().ajax.reload(null,false);
              if(data.status_pesan==1){
                createPesan(data.pesan,"success");
                } else {
                createPesan(data.pesan,"danger"); 
              }
    }
});
});

function hitungpagu_program(){
  var a = Number($('#pagu1_edit').val()); 
  var b = Number($('#pagu2_edit').val()); 
  var c = Number($('#pagu3_edit').val()); 
  var d = Number($('#pagu4_edit').val()); 
  var e = Number($('#pagu5_edit').val());

  var x = a+b+c+d+e;
  $('#pagu_total_edit').val(x);
}

$( "#pagu1_edit" ).change(function() {
  hitungpagu_program();
});

$( "#pagu2_edit" ).change(function() {
  hitungpagu_program();
});

$( "#pagu3_edit" ).change(function() {
  hitungpagu_program();
});

$( "#pagu4_edit" ).change(function() {
  hitungpagu_program();
});

$( "#pagu5_edit" ).change(function() {
  hitungpagu_program();
});

$('#tblProgram tbody').on( 'dblclick', 'tr', function () {

  var data = tblProgram.row(this).data();
  id_program_rpjmd =  data.id_program_rpjmd;
  $('.nav-tabs a[href="#urusan"]').tab('show');
  loadTblUrusan(id_program_rpjmd);

  $.ajax({
          type: "GET",
          url: './rpjmd/getUrusan/'+id_program_rpjmd,
          dataType: "json",

          success: function(data) {

          var j = data.length;
          var post, i;

          $('select[name="kd_urusan"]').empty();
          $('select[name="kd_urusan"]').append('<option value="-1">---Pilih Urusan Pemerintahan---</option>');

          for (i = 0; i < j; i++) {
            post = data[i];
            $('select[name="kd_urusan"]').append('<option value="'+ post.kd_urusan +'">'+ post.nm_urusan +'</option>');
          }
          }
      });
});

  var UrusanProg;
  function loadTblUrusan(id_program_rpjmd){
   UrusanProg= $('#tblUrusan').DataTable( {
        processing: true,
        serverSide: true,
          dom: 'BFrtip',
          responsive: true,                
          autoWidth : false,
          order: [[0, 'asc']],
          bDestroy: true,
          language: {
              "decimal": ",",
              "thousands": ".",
              "sEmptyTable":   "Tidak ada data yang tersedia pada tabel ini",
              "sProcessing":   "Sedang memproses...",
              "sLengthMenu":   "Tampilkan _MENU_ entri",
              "sZeroRecords":  "Tidak ditemukan data yang sesuai",
              "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
              "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
              "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
              "sInfoPostFix":  "",
              "sSearch":       "Cari:",
              "sUrl":          "",
              "oPaginate": {
                  "sFirst":    "Pertama",
                  "sPrevious": "Sebelumnya",
                  "sNext":     "Selanjutnya",
                  "sLast":     "Terakhir"
              }
            },
        "ajax": {"url": "./rpjmd/programurusan/"+id_program_rpjmd},
        "columns": [
                { data: 'kd_program', sClass: "dt-center"},
                { data: 'kode_bid', sClass: "dt-center"},
                { data: 'nm_urusan'},
                { data: 'nm_bidang'},
                { data: 'action', 'searchable': false, 'orderable':false }
              ]
        });
  };

  $(document).on('click', '.add-urbidprog', function() {
      $.ajax({
          type: "GET",
          url: './rpjmd/getBidang/0',
          dataType: "json",

          success: function(data) {
            var j = data.length;
            var post, i;

            $('select[name="kd_bidang"]').empty();
            $('select[name="kd_bidang"]').append('<option value="-1">---Pilih Kode Bidang---</option>');

            for (i = 0; i < j; i++) {
              post = data[i];
              $('select[name="kd_bidang"]').append('<option value="'+ post.id_bidang +'">'+ post.nm_bidang +'</option>');
            }
          }
      });

      $('.btnUrusan').addClass('addUrusan');
      $('.btnUrusan').removeClass('editUrusan');
      $('.modal-title').text('Tambah Urusan dan Bidang Pemerintahan RPJMD');
      $('.form-horizontal').show();
      $('#id_urbid_rpjmd_edit').val(null);
      $('#thn_urbid_rpjmd_edit').val(tahun_rpjmd);
      $('#no_urbid_rpjmd_edit').val(99);
      $('#id_prog_urbid_rpjmd_edit').val(id_program_rpjmd);
      $('#kd_urusan option[value="-1"]').attr("selected",true);

      $('#ModalUrusan').modal('show');
    });

  $( ".kd_urusan" ).change(function() {      
      $.ajax({
          type: "GET",
          url: './rpjmd/getBidang/'+$('.kd_urusan').val(),
          dataType: "json",

          success: function(data) {
            var j = data.length;
            var post, i;

            $('select[name="kd_bidang"]').empty();
            $('select[name="kd_bidang"]').append('<option value="-1">---Pilih Kode Bidang---</option>');

            for (i = 0; i < j; i++) {
              post = data[i];
              $('select[name="kd_bidang"]').append('<option value="'+ post.id_bidang +'">'+ post.nm_bidang +'</option>');
            }
          }
      });
  });

  $('.modal-footer').on('click', '.addUrusan', function() {
      $.ajaxSetup({
         headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
      });

      $.ajax({
          type: 'post',
          url: './rpjmd/addUrusan',
          data: {
              '_token': $('input[name=_token]').val(),
              'thn_id': $('#thn_urbid_rpjmd_edit').val(),
              'no_urut': $('#no_urbid_rpjmd_edit').val(),
              'id_program_rpjmd': $('#id_prog_urbid_rpjmd_edit').val(),
              'id_bidang': $('#kd_bidang').val(),
          },
          success: function(data) {             
              $('#tblUrusan').DataTable().ajax.reload(null,false);
              if(data.status_pesan==1){
                createPesan(data.pesan,"success");
              } else {
                createPesan(data.pesan,"danger"); 
              }             
          }
      });
  });

  $(document).on('click', '.edit-urbidprog', function() {

      var data = UrusanProg.row( $(this).parents('tr') ).data();
      $.ajax({
          type: "GET",
          url: './rpjmd/getBidang/'+data.kd_urusan,
          dataType: "json",

          success: function(data) {
            var j = data.length;
            var post, i;

            $('select[name="kd_bidang"]').empty();
            // $('select[name="kd_bidang"]').append('<option value="-1">---Pilih Kode Bidang---</option>');

            for (i = 0; i < j; i++) {
              post = data[i];
              $('select[name="kd_bidang"]').append('<option value="'+ post.id_bidang +'">'+ post.nm_bidang +'</option>');
            }
          }
      });

      $('.btnUrusan').addClass('editUrusan');
      $('.btnUrusan').removeClass('addUrusan');
      $('.modal-title').text('Tambah Urusan dan Bidang Pemerintahan RPJMD');
      $('.form-horizontal').show();
      $('#id_urbid_rpjmd_edit').val(data.id_urbid_rpjmd);
      $('#thn_urbid_rpjmd_edit').val(data.thn_id);
      $('#no_urbid_rpjmd_edit').val(data.no_urut);
      $('#id_prog_urbid_rpjmd_edit').val(data.id_program_rpjmd);
      $('#kd_urusan option[value="'+data.kd_urusan+'"]').attr("selected",true);
      $('#kd_bidang option[value="'+data.id_bidang+'"]').attr("selected",true);

      $('#ModalUrusan').modal('show');
  });

  $('.modal-footer').on('click', '.editUrusan', function() {
      $.ajaxSetup({
         headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
      });

      $.ajax({
          type: 'post',
          url: './rpjmd/editUrusan',
          data: {
              '_token': $('input[name=_token]').val(),
              'id_urbid_rpjmd' : $('#id_urbid_rpjmd_edit').val(),
              'thn_id': $('#thn_urbid_rpjmd_edit').val(),
              'no_urut': $('#no_urbid_rpjmd_edit').val(),
              'id_program_rpjmd': $('#id_prog_urbid_rpjmd_edit').val(),
              'id_bidang': $('#kd_bidang').val(),
          },
          success: function(data) {             
              $('#tblUrusan').DataTable().ajax.reload(null,false);
              if(data.status_pesan==1){
                createPesan(data.pesan,"success");
              } else {
                createPesan(data.pesan,"danger"); 
              }             
          }
      });
  });

  $(document).on('click', '.del-urbidprog', function() {
    var data = UrusanProg.row( $(this).parents('tr') ).data();

    $('.btnDelUrusan').addClass('delUrusanRPJMD');
    $('.modal-title').text('Hapus Urusan - Bidang pada RPJMD');
    $('#id_urusan_rkpd_hapus').val(data.id_urbid_rpjmd);
    $('.ur_bidang_del').html(data.nm_bidang);
    $('.ur_urusan_del').html(data.nm_urusan);

    $('#HapusUrusan').modal('show');
  });

  $('.modal-footer').on('click', '.delUrusanRPJMD', function() {
    $.ajaxSetup({
       headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
    });

    $.ajax({
      type: 'post',
      url: './rpjmd/delUrusan',
      data: {
        '_token': $('input[name=_token]').val(),
        'id_urbid_rpjmd': $('#id_urusan_rkpd_hapus').val()
      },
      success: function(data) {
        $('#tblUrusan').DataTable().ajax.reload(null,false);
        $('#tblPelaksana').DataTable().ajax.reload(null,false);
        if(data.status_pesan==1){
          createPesan(data.pesan,"success");
        } else {
          createPesan(data.pesan,"danger"); 
        } 
      }
    });
  });

  $(document).on('click', '.repivot-renstra', function() {
    // var data = UrusanProg.row(this).data();
    var data = Program.row( $(this).parents('tr') ).data();

    $('#ModalProgress').modal('show');

      $.ajaxSetup({
         headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
      });

      $.ajax({
          type: 'post',
          url: './rpjmd/RePivotRenstra',
          data: {
              '_token': $('input[name=_token]').val(),
              'id_program_rpjmd': data.id_program_rpjmd,
          },
          success: function(data) {             
              Program.ajax.reload(null,false);                            
              if(data.status_pesan==1){
                createPesan(data.pesan,"success");
              } else {
                createPesan(data.pesan,"danger"); 
              }

              setTimeout(function() {
                $('#ModalProgress').modal('hide');
              }, 3500);             
          },
          error: function(data) {             
              Program.ajax.reload(null,false);
              setTimeout(function() {
                $('#ModalProgress').modal('hide');
              }, 3500);             
          }
      });    
  });

  $(document).on('click', '.post-urbidprog', function() {
    // var data = UrusanProg.row(this).data();
    var data = Program.row( $(this).parents('tr') ).data();

    $('#ModalProgress').modal('show');

      $.ajaxSetup({
         headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
      });

      $.ajax({
          type: 'post',
          url: './rpjmd/ReprosesPivotPelaksana',
          data: {
              '_token': $('input[name=_token]').val(),
              'id_program_rpjmd': data.id_program_rpjmd,
              // 'id_urbid_rpjmd': data.id_urbid_rpjmd,
          },
          success: function(data) {             
              Program.ajax.reload(null,false);                            
              if(data.status_pesan==1){
                createPesan(data.pesan,"success");
              } else {
                createPesan(data.pesan,"danger"); 
              }

              setTimeout(function() {
                $('#ModalProgress').modal('hide');
              }, 3500);             
          },
          error: function(data) {             
              Program.ajax.reload(null,false);
              setTimeout(function() {
                $('#ModalProgress').modal('hide');
              }, 3500);             
          }
      });    
  });

  var id_bidang_temp;
  $('#tblUrusan tbody').on( 'dblclick', 'tr', function () {
    var data = UrusanProg.row(this).data();
    id_urusan_program =  data.id_urbid_rpjmd;
    id_bidang_temp = data.id_bidang;
    $('.nav-tabs a[href="#pelaksana"]').tab('show');
    loadTblPelaksana(id_urusan_program);
  });  

  var PelaksanaProg;
  function loadTblPelaksana(id_program_rpjmd){
   PelaksanaProg= $('#tblPelaksana').DataTable( {
        processing: true,
        serverSide: true,
          dom: 'BFrtip',
          responsive: true,                
          autoWidth : false,
          order: [[0, 'asc']],
          bDestroy: true,
          language: {
              "decimal": ",",
              "thousands": ".",
              "sEmptyTable":   "Tidak ada data yang tersedia pada tabel ini",
              "sProcessing":   "Sedang memproses...",
              "sLengthMenu":   "Tampilkan _MENU_ entri",
              "sZeroRecords":  "Tidak ditemukan data yang sesuai",
              "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
              "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
              "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
              "sInfoPostFix":  "",
              "sSearch":       "Cari:",
              "sUrl":          "",
              "oPaginate": {
                  "sFirst":    "Pertama",
                  "sPrevious": "Sebelumnya",
                  "sNext":     "Selanjutnya",
                  "sLast":     "Terakhir"
              }
            },
        "ajax": {"url": "./rpjmd/programpelaksana/"+id_program_rpjmd},
        "columns": [
                { data: 'kd_program', sClass: "dt-center"},
                { data: 'kd_unit', sClass: "dt-center"},
                { data: 'nm_unit'},
                { data: 'action', 'searchable': false, 'orderable':false }
              ]
        });
  };

  var UnitPelaksana;
  function loadTblUnitPelaksana(id_program,id_bidang){
    UnitPelaksana=$('#tblUnitPelaksana').DataTable({
                  processing: true,
                  serverSide: true,
                  responsive: true,  
                  autoWidth : false,
                  dom : 'BFRtIp',
                  "ajax": {"url": "./rpjmd/getUnitPelaksana/"+id_program+"/"+id_bidang},
                  language: {
                    "decimal": ",",
                    "thousands": ".",
                    "sEmptyTable":   "Tidak ada data yang tersedia pada tabel ini",
                    "sProcessing":   "Sedang memproses...",
                    "sLengthMenu":   "Tampilkan _MENU_ entri",
                    "sZeroRecords":  "Tidak ditemukan data yang sesuai",
                    "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                    "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
                    "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
                    "sInfoPostFix":  "",
                    "sSearch":       "Cari:",
                    "sUrl":          "",
                    "oPaginate": {
                        "sFirst":    "Pertama",
                        "sPrevious": "Sebelumnya",
                        "sNext":     "Selanjutnya",
                        "sLast":     "Terakhir"
                    }
                  },
                  "columns": [
                        { data: 'kd_unit', sClass: "dt-center"},
                        { data: 'nm_unit'},
                        { data: 'action', 'searchable': false, 'orderable':false, sClass: "dt-center" }
                      ],
                  "order": [[0, 'asc']],
                  "bDestroy": true
        });
  };

  $(document).on('click', '.add-pelaksanaprog', function() {
    // var data = UrusanProg.row( $(this).parents('tr') ).data();
      $('.form-horizontal').show();
      $('.modal-title').text('Daftar Unit Pelaksana yang dapat ditambahkan');
      $('#id_urbid_pelaksana').val(id_urusan_program);
      $('#thn_pelaksana').val(tahun_rpjmd);
      $('#no_pelaksana').val(99);
      $('#id_pelaksana_rpjmd').val(null);

      loadTblUnitPelaksana(id_program_rpjmd,id_bidang_temp);

      $('#ModalPelaksana').modal('show');
  });

  $(document).on('click', '.add-unitpelaksana', function() {

      var data = UnitPelaksana.row( $(this).parents('tr') ).data();

      $.ajaxSetup({
         headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
      });

      $.ajax({
          type: 'post',
          url: './rpjmd/addPelaksana',
          data: {
              '_token': $('input[name=_token]').val(),
              'thn_id': $('#thn_pelaksana').val(),
              'id_urbid_rpjmd': $('#id_urbid_pelaksana').val(),
              'no_urut': $('#no_pelaksana').val(),
              'id_unit': data.id_unit,
          },
          success: function(data) {             
              $('#tblPelaksana').DataTable().ajax.reload(null,false);
              $('#ModalPelaksana').modal('hide'); 
              if(data.status_pesan==1){
                createPesan(data.pesan,"success");
              } else {
                createPesan(data.pesan,"danger"); 
              }             
          }
      });
  });

  $(document).on('click', '.del-pelaksanaprog', function() {
    var data = PelaksanaProg.row( $(this).parents('tr') ).data();
    $('.btnDelPelaksana').addClass('delUnitPelaksana');
    $('.modal-title').text('Hapus Pelaksana pada RPJMD');
    $('#no_urut_hapus').val(data.no_urut);
    $('#id_pelaksana_hapus').val(data.id_pelaksana_rpjmd);
    $('#ur_pelaksana_hapus').html(data.nm_unit);

    $('#HapusUnitPelaksana').modal('show');
  });

  $('.modal-footer').on('click', '.delUnitPelaksana', function() {
    $.ajaxSetup({
       headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
    });

    $.ajax({
      type: 'post',
      url: './rpjmd/delPelaksana',
      data: {
        '_token': $('input[name=_token]').val(),
        'id_pelaksana_rpjmd': $('#id_pelaksana_hapus').val()
      },
      success: function(data) {
        $('#tblPelaksana').DataTable().ajax.reload(null,false);
        if(data.status_pesan==1){
          createPesan(data.pesan,"success");
        } else {
          createPesan(data.pesan,"danger"); 
        } 
      }
    });
  });  
  
  $(document).on('click', '.view-rpjmdtujuan', function() {
      var data = tblMisi.row( $(this).parents('tr') ).data();      
      id_misi_rpjmd =  data.id_misi_rpjmd;
      $('.nav-tabs a[href="#tujuan"]').tab('show');
      loadTblMisi(data.id_misi_rpjmd);
    });

  $(document).on('click', '.view-rpjmdsasaran', function() {
      var data = tblTujuan.row( $(this).parents('tr') ).data();
      id_tujuan_rpjmd = data.id_tujuan_rpjmd;
      $('.nav-tabs a[href="#sasaran"]').tab('show');
      $('.nav-tabs a[href="#sasaran1"]').tab('show');
      loadTblSasaran(data.id_tujuan_rpjmd);
    });

  $(document).on('click', '.view-rpjmdkebijakan', function() {
      var data = tblSasaran.row( $(this).parents('tr') ).data();
      id_sasaran_rpjmd = data.id_sasaran_rpjmd;
      $('.nav-tabs a[href="#kebijakan"]').tab('show');
      loadTblKebijakan(data.id_sasaran_rpjmd);
    });

  $(document).on('click', '.view-rpjmdstrategi', function() {
      var data = tblSasaran.row( $(this).parents('tr') ).data();
      id_sasaran_rpjmd = data.id_sasaran_rpjmd;
      $('.nav-tabs a[href="#strategi"]').tab('show');
      loadTblStrategi(data.id_sasaran_rpjmd);
    });
  $(document).on('click', '.view-rpjmdprogram', function() {
      var data = tblSasaran.row( $(this).parents('tr') ).data();
      id_sasaran_rpjmd = data.id_sasaran_rpjmd;
      $('.nav-tabs a[href="#program"]').tab('show');
      $('.nav-tabs a[href="#program1"]').tab('show');
      loadTblProgram(id_sasaran_rpjmd);
    });
  $(document).on('click', '.view-rpjmdurusan', function() {
      var data = tblProgram.row( $(this).parents('tr') ).data();
      id_program_rpjmd =  data.id_program_rpjmd;
      $('.nav-tabs a[href="#urusan"]').tab('show');
      loadTblUrusan(id_program_rpjmd);
    });
  $(document).on('click', '.view-rpjmdpelaksana', function() {
      var data = UrusanProg.row( $(this).parents('tr') ).data();
      id_urusan_program =  data.id_urbid_rpjmd;
      $('.nav-tabs a[href="#pelaksana"]').tab('show');
      loadTblPelaksana(id_urusan_program);
    });

  $('#tblCariIndikator tbody').on( 'dblclick', 'tr', function () {
    var data = cariindikator.row(this).data();

    document.getElementById("ur_indikator_tujuan_rpjmd").value = data.nm_indikator;
    document.getElementById("kd_indikator_tujuan_rpjmd").value = data.id_indikator;
    document.getElementById("satuan_tujuan_indikator_edit").value = data.uraian_satuan;

    document.getElementById("ur_indikator_sasaran_rpjmd").value = data.nm_indikator;
    document.getElementById("kd_indikator_sasaran_rpjmd").value = data.id_indikator;
    document.getElementById("satuan_sasaran_indikator_edit").value = data.uraian_satuan;

    $('#cariIndikator').modal('hide');    

  });

  var tblPendapatan;
  function loadTblPendapatan(id_visi){
  tblPendapatan = $('#tblPendapatan').DataTable( {
          processing: true,
          serverSide: true,
          dom: 'BFrtip',
          responsive: true,                
          autoWidth : false,
          order: [[0, 'asc']],
          bDestroy: true,
          language: {
              "decimal": ",",
              "thousands": ".",
              "sEmptyTable":   "Tidak ada data yang tersedia pada tabel ini",
              "sProcessing":   "Sedang memproses...",
              "sLengthMenu":   "Tampilkan _MENU_ entri",
              "sZeroRecords":  "Tidak ditemukan data yang sesuai",
              "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
              "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
              "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
              "sInfoPostFix":  "",
              "sSearch":       "Cari:",
              "sUrl":          "",
              "oPaginate": {
                  "sFirst":    "Pertama",
                  "sPrevious": "Sebelumnya",
                  "sNext":     "Selanjutnya",
                  "sLast":     "Terakhir"
              }
            },
          "ajax": {"url": "./rpjmd/pdtRpjmd/"+id_visi},
          "language": {
                "decimal": ",",
                "thousands": "."},
          "columns": [
                // { data: 'kd_sasaran', sClass: "dt-center"},
                { data: 'no_urut', sClass: "dt-center"},
                { data: 'uraian_program_rpjmd'},
                { data: 'pagu_tahun1',
                  render: $.fn.dataTable.render.number( '.', ',', 0, '' ), sClass: "dt-right"},
                { data: 'pagu_tahun2',
                  render: $.fn.dataTable.render.number( '.', ',', 0, '' ), sClass: "dt-right"},
                { data: 'pagu_tahun3',
                  render: $.fn.dataTable.render.number( '.', ',', 0, '' ), sClass: "dt-right"},
                { data: 'pagu_tahun4',
                  render: $.fn.dataTable.render.number( '.', ',', 0, '' ), sClass: "dt-right"},
                { data: 'pagu_tahun5',
                  render: $.fn.dataTable.render.number( '.', ',', 0, '' ), sClass: "dt-right"},
                { data: 'total_pagu',
                  render: $.fn.dataTable.render.number( '.', ',', 0, '' ), sClass: "dt-right"},
                { data: 'action', 'searchable': false, 'orderable':false }
            ],
            "columnDefs": [ {
                  "visible": false
              } ]
          } )
  };

  var tblBtl;
  function loadTblBtl(id_visi){
  tblBtl = $('#tblBtl').DataTable( {
          processing: true,
          serverSide: true,
          dom: 'BFrtip',
          responsive: true,                
          autoWidth : false,
          order: [[0, 'asc']],
          bDestroy: true,
          language: {
              "decimal": ",",
              "thousands": ".",
              "sEmptyTable":   "Tidak ada data yang tersedia pada tabel ini",
              "sProcessing":   "Sedang memproses...",
              "sLengthMenu":   "Tampilkan _MENU_ entri",
              "sZeroRecords":  "Tidak ditemukan data yang sesuai",
              "sInfo":         "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
              "sInfoEmpty":    "Menampilkan 0 sampai 0 dari 0 entri",
              "sInfoFiltered": "(disaring dari _MAX_ entri keseluruhan)",
              "sInfoPostFix":  "",
              "sSearch":       "Cari:",
              "sUrl":          "",
              "oPaginate": {
                  "sFirst":    "Pertama",
                  "sPrevious": "Sebelumnya",
                  "sNext":     "Selanjutnya",
                  "sLast":     "Terakhir"
              }
            },
          "ajax": {"url": "./rpjmd/btlRpjmd/"+id_visi},
          "language": {
                "decimal": ",",
                "thousands": "."},
          "columns": [
                // { data: 'kd_sasaran', sClass: "dt-center"},
                { data: 'no_urut', sClass: "dt-center"},
                { data: 'uraian_program_rpjmd'},
                { data: 'pagu_tahun1',
                  render: $.fn.dataTable.render.number( '.', ',', 0, '' ), sClass: "dt-right"},
                { data: 'pagu_tahun2',
                  render: $.fn.dataTable.render.number( '.', ',', 0, '' ), sClass: "dt-right"},
                { data: 'pagu_tahun3',
                  render: $.fn.dataTable.render.number( '.', ',', 0, '' ), sClass: "dt-right"},
                { data: 'pagu_tahun4',
                  render: $.fn.dataTable.render.number( '.', ',', 0, '' ), sClass: "dt-right"},
                { data: 'pagu_tahun5',
                  render: $.fn.dataTable.render.number( '.', ',', 0, '' ), sClass: "dt-right"},
                { data: 'total_pagu',
                  render: $.fn.dataTable.render.number( '.', ',', 0, '' ), sClass: "dt-right"},
                { data: 'action', 'searchable': false, 'orderable':false }
            ],
            "columnDefs": [ {
                  "visible": false
              } ]
          } )
  };

  $(document).on('click', '.btnViewPendapatan', function() {  
      // var data = tbl_Visi.row(this).data();
      // tblInSasaran.row( $(this).parents('tr') ).data();
      var data = tblInDokumen.row( $(this).parents('tr') ).data();
      $('.nav-tabs a[href="#pendapatan"]').tab('show');
      loadTblPendapatan(data.id_visi_rpjmd);
    });

  $(document).on('click', '.btnViewBtl', function() {  
      // var data = tbl_Visi.row(this).data();
      var data = tblInDokumen.row( $(this).parents('tr') ).data();
      $('.nav-tabs a[href="#btl"]').tab('show');
      loadTblBtl(data.id_visi_rpjmd);
    });

  $.ajax({
          type: "GET",
          url: './rpjmd/getJnsDokumen',
          dataType: "json",

          success: function(data) {
            var j = data.length;
            var post, i;

            $('select[name="cb_jns_dokumen"]').empty();
            $('select[name="cb_jns_dokumen"]').append('<option value="-1">---Pilih Jenis Dokumen RPJMD---</option>');

            for (i = 0; i < j; i++) {
              post = data[i];
              $('select[name="cb_jns_dokumen"]').append('<option value="'+ post.id_dokumen +'">'+ post.nm_dokumen +'</option>');
            }
          }
  });

  $(document).on('click', '.btnAddDokumen', function() {
      $('.btnDokumen').addClass('addDokumen');
      $('.btnDokumen').removeClass('editDokumen');
      $('.modal-title').text('Data Dokumen Perencanaan Daerah');
      $('.form-horizontal').show();
      $('#id_rpjmd_dok').val(null);
      $('#cb_jns_dokumen').val(-1);
      $('#id_revisi_dok').val(0);
      $('#thn_1_dok').val(2019);
      $('#thn_5_dok').val(2024);
      $('#no_perda_dok').val(null);
      $('#tgl_perda_dok').val(hariIni());
      $('#tgl_perda_dok_x').val(formatTgl(hariIni()));
      $('#uraian_perda_dok').val(null);
      $('#cb_ref_dokumen').val(-1);
      $('#btnDelDokumen').hide();
      $('#ModalDokumen').modal('show');
    });

  $('.modal-footer').on('click', '.addDokumen', function() {
      $.ajaxSetup({
         headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
      });

      $.ajax({
          type: 'post',
          url: './rpjmd/addDokumen',
          data: {
              '_token': $('input[name=_token]').val(),
              'tahun_1' : $('#thn_1_dok').val(),
              'tahun_5': $('#thn_5_dok').val(),
              'no_perda': $('#no_perda_dok').val(),
              'tgl_perda': $('#tgl_perda_dok').val(),
              'keterangan_dokumen': $('#uraian_perda_dok').val(),
              'jns_dokumen':$('#cb_jns_dokumen').val(),
              'id_rpjmd_ref':$('#cb_ref_dokumen').val(),
              'id_revisi':$('#id_revisi_dok').val(),
          },
          success: function(data) {             
              tbl_Dokumen.ajax.reload(null,false);
              if(data.status_pesan==1){
                createPesan(data.pesan,"success");
              } else {
                createPesan(data.pesan,"danger"); 
              }             
          }
      });
    });

  $(document).on('click', '.btnViewDok', function() {
      var data = tbl_Dokumen.row( $(this).parents('tr') ).data();

      $('.btnDokumen').addClass('editDokumen');
      $('.btnDokumen').removeClass('addDokumen');
      $('.modal-title').text('Data Dokumen Perencanaan Daerah');
      $('.form-horizontal').show();
      $('#id_rpjmd_dok').val(data.id_rpjmd);
      $('#cb_jns_dokumen').val(data.jns_dokumen);
      $('#cb_ref_dokumen').val(data.id_rpjmd_ref);
      $('#id_revisi_dok').val(data.id_revisi);
      $('#thn_1_dok').val(data.tahun_1);
      $('#thn_5_dok').val(data.tahun_5);
      $('#no_perda_dok').val(data.no_perda);
      $('#tgl_perda_dok').val(data.tgl_perda);
      $('#tgl_perda_dok_x').val(formatTgl(data.tgl_perda));
      $('#uraian_perda_dok').val(data.keterangan_dokumen);
      $('#btnDelDokumen').show();
      $('#ModalDokumen').modal('show');
    });

  $('.modal-footer').on('click', '.editDokumen', function() {
      $.ajaxSetup({
         headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
      });

      $.ajax({
          type: 'post',
          url: './rpjmd/editDokumen',
          data: {
              '_token': $('input[name=_token]').val(),
              'id_rpjmd' : $('#id_rpjmd_dok').val(),
              'tahun_1' : $('#thn_1_dok').val(),
              'tahun_5': $('#thn_5_dok').val(),
              'no_perda': $('#no_perda_dok').val(),
              'tgl_perda': $('#tgl_perda_dok').val(),
              'keterangan_dokumen': $('#uraian_perda_dok').val(),
              'jns_dokumen':$('#cb_jns_dokumen').val(),
              'id_rpjmd_ref':$('#cb_ref_dokumen').val(),
              'id_revisi':$('#id_revisi_dok').val(),
          },
          success: function(data) {             
              tbl_Dokumen.ajax.reload(null,false);
              if(data.status_pesan==1){
                createPesan(data.pesan,"success");
              } else {
                createPesan(data.pesan,"danger"); 
              }             
          }
      });
    });

  $('.modal-footer').on('click', '.btnDelDokumen', function() {
  $.ajaxSetup({
      headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
  });

    $.ajax({
      type: 'post',
      url: './rpjmd/deleteDokumen',
      data: {
        '_token': $('input[name=_token]').val(),
        'id_rpjmd' : $('#id_rpjmd_dok').val(),
      },
      success: function(data) {
        tbl_Dokumen.ajax.reload(null,false);
        $('#ModalDokumen').modal('hide');
        if(data.status_pesan==1){
          createPesan(data.pesan,"success");
        } else {
          createPesan(data.pesan,"danger"); 
        }
      }
    });
  });

    $.ajax({
          type: "GET",
          url: './rpjmd/getDokumenRef?jns=0',
          dataType: "json",
          success: function(data) {
            var j = data.length;
            var post, i;
            $('select[name="cb_ref_dokumen"]').empty();
            $('select[name="cb_ref_dokumen"]').append('<option value="-1">---Pilih Dokumen RPJMD Referensi---</option>');
            $('select[name="cb_ref_dokumen"]').append('<option value="0">---Tidak ada Dokumen Referensi---</option>');
            for (i = 0; i < j; i++) {
              post = data[i];
              $('select[name="cb_ref_dokumen"]').append('<option value="'+ post.id_rpjmd +'">'+ post.no_perda +'</option>');
            }
          }
  });

  $('#ModalDokumen').on('hidden.bs.modal', function () {
    $.ajax({
          type: "GET",
          url: './rpjmd/getDokumenRef?jns=0',
          dataType: "json",
          success: function(data) {
            var j = data.length;
            var post, i;
            $('select[name="cb_ref_dokumen"]').empty();
            $('select[name="cb_ref_dokumen"]').append('<option value="-1">---Pilih Dokumen RPJMD Referensi---</option>');
            $('select[name="cb_ref_dokumen"]').append('<option value="0">---Tidak ada Dokumen Referensi---</option>');
            for (i = 0; i < j; i++) {
              post = data[i];
              $('select[name="cb_ref_dokumen"]').append('<option value="'+ post.id_rpjmd +'">'+ post.no_perda +'</option>');
            }
          }
  });
  });

$( ".cb_jns_dokumen" ).change(function() {
    var jns, xyz;
    xyz = parseInt($( "#cb_jns_dokumen" ).val(),10);
    switch (xyz) {
      case 1:
        jns = 22;
        break;
      case 2:
         jns = 1;
        break;
      case 3:
        jns = 2;
        break;
      case 4:
        jns = 3;
        break;
      case 5:
        jns = 4;
        break;
      case 6:
        jns = 5;
        break;
      case 22:
        jns = 0;
    }

    $.ajax({
          type: "GET",
          url: './rpjmd/getDokumenRef?jns='+ jns,
          dataType: "json",
          success: function(data) {
            var j = data.length;
            var post, i;
            $('select[name="cb_ref_dokumen"]').empty();
            $('select[name="cb_ref_dokumen"]').append('<option value="-1">---Pilih Dokumen RPJMD Referensi---</option>');
            $('select[name="cb_ref_dokumen"]').append('<option value="0">---Tidak ada Dokumen Referensi---</option>');
            for (i = 0; i < j; i++) {
              post = data[i];
              $('select[name="cb_ref_dokumen"]').append('<option value="'+ post.id_rpjmd +'">'+ post.no_perda +'</option>');
            }
          }
  });
});

  $(document).on('click', '.btnPrintRPJMDTSK', function() {  
   window.open('./printRPJMDTSK');  
  });

  $(document).on('click', '.btnPrintProgPrio', function() {    
    window.open('./printProgPrio');    
  });

  $(document).on('click', '.btnPrintKompilasiProgramdanPagu', function() {
    window.open('./PrintKompilasiProgramdanPagu');
  });

  });