$(document).on('click', '.btnDetailIndikatorSasaran', function() {
var data = tblInSasaran.row( $(this).parents('tr') ).data();
    $('.btnSimpanSasaranIndikator').removeClass('addSasaranIndikator');
    $('.btnSimpanSasaranIndikator').addClass('editSasaranIndikator');
	$('.modal-title').text('Data Indikator Kinerja Sasaran Perangkat Daerah');
	$('.form-horizontal').show();
    $('#id_iku_pemda_rinci').val(data.id_iku_opd_sasaran);
    $('#id_dokumen_rinci').val(data.id_dokumen);
    $('#id_indikator_sasaran_rpjmd_rinci').val(data.id_indikator_sasaran_renstra);
    $('#nm_indikator_rinci').val(data.nm_indikator);
    $('#target_sasaran_rinci').val(data.angka_akhir_periode);
    $('#satuan_sasaran_rinci').val(data.uraian_satuan);
    $('#type_indikator_rinci').val(data.sifat_indikator_display);
    $('#metode_ukur_rinci').val(data.metode_penghitungan,);
    $('#sumber_data_ukur_rinci').val(data.sumber_data_indikator);
    $('#jenis_indikator_rinci').val(data.kualitas_indikator_display);
    $('#sifat_indikator_rinci').val(data.jenis_indikator_display);
    $('#tipe_indikator_rinci').val(data.type_indikator_display);
    $('#flag_iku_rinci').val(data.flag_iku);
    if(data.flag_iku == 0){
        $('#flag_iku_rinci_1').removeClass('active').addClass('notActive');
        $('#flag_iku_rinci_0').addClass('active').removeClass('notActive');
    } else {
        $('#flag_iku_rinci_0').removeClass('active').addClass('notActive');
        $('#flag_iku_rinci_1').addClass('active').removeClass('notActive');
    }
    
    $('#ModalIndikatorSasaran').modal('show');
});

$('.modal-footer').on('click', '.editSasaranIndikator', function() {
    $.ajaxSetup({
       headers: { 'X-CSRF-Token' : $('meta[name=_token]').attr('content') }
    });

    $.ajax({
        type: 'post',
        url: './opd/editIndikatorSasaran',
        data: {
            '_token': $('input[name=_token]').val(),
            'id_iku_opd_sasaran': $('#id_iku_pemda_rinci').val(),
            'flag_iku': $('#flag_iku_rinci').val(),
        },
        success: function(data) {
            $('#tblSasaran').DataTable().ajax.reload(null,false);
              if(data.status_pesan==1){
                createPesan(data.pesan,"success");
                } else {
                createPesan(data.pesan,"danger"); 
              }

        }
    });
});
