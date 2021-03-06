<div id="ModalIndikatorKegiatan" class="modal fade" role="dialog">
    <div class="modal-dialog modal-lg"  >
      <div class="modal-content">
        <div class="modal-header">
          <h4 class="modal-title"></h4>
        </div>
        <div class="modal-body">
          <form class="form-horizontal" role="form" autocomplete='off' action="" method="post" >
              <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
              <input type="hidden" class="form-control" id="id_indikator_hasil_kegiatan_edit" name="id_indikator_hasil_kegiatan_edit" readonly >
              <input type="hidden" class="form-control" id="id_hasil_kegiatan_indikator" name="id_hasil_kegiatan_indikator" readonly>
              <div class="form-group">
                  <label class="control-label col-sm-3" for="title">Uraian Indikator Kegiatan Renstra :</label>
                  <div class="col-sm-8">
                    <textarea type="name" class="form-control" id="ur_indikator_kegiatan_renstra" rows="3" disabled></textarea>
                  </div>
                  <input type="hidden" id="kd_indikator_kegiatan_renstra" name="kd_indikator_kegiatan_renstra">
                  <span class="btn btn-primary btnCariIndiKeg" id="btnCariIndKegg" name="btnCariIndiKeg"><i class="fa fa-search fa-fw fa-lg"></i></span>
              </div>             
              <div class="form-group">
                  <label for="satuan_program_indikator_edit" class="col-sm-3 control-label" align='left'>Satuan Indikator :</label>
                  <div class="col-sm-8">
                      <div class="input-group">
                        <input type="text" class="form-control" id="satuan_kegiatan_indikator_edit" name="satuan_kegiatan_indikator_edit" readonly>                  
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
                        <button type="button" class="btn btn-success btnSimpanKegiatanIndikator btn-labeled" data-dismiss="modal">
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

  <div id="HapusKegiatanIndikatorModal" class="modal fade" role="dialog" data-backdrop="static">
          <div class="modal-dialog modal-xs">
            <div class="modal-content">
              <div class="modal-header">
                  <h4 class="modal-title"></h4>
              </div>
              <div class="modal-body">
                <input type="hidden" id="id_kegiatan_indikator_hapus" name="id_kegiatan_indikator_hapus">
                <div class="alert alert-danger deleteContent">
                    <i class="fa fa-exclamation-triangle fa-3x fa-pull-left fa-border"  style="color:red;" aria-hidden="true"></i>
                      <br>
                      Yakin akan menghapus Indikator Kegiatan Renstra : <strong><span id="nm_kegiatan_indikator_hapus"></span></strong> ?
                      <br>
                      <br>
                </div>
              </div>
                <div class="modal-footer">
                  <div class="ui-group-buttons">
                    <button type="button" class="btn btn-sm btn-danger btn-labeled btnDelKegiatanIndikator" data-dismiss="modal" ><span class="btn-label"><i id="footer_action_button" class="glyphicon glyphicon-trash"></i></span> Hapus</button>
                    <div class="or"></div>
                    <button type="button" class="btn btn-sm btn-warning btn-labeled" data-dismiss="modal" aria-hidden="true"><span class="btn-label"><i class="glyphicon glyphicon-log-out"></i></span> Tutup</button>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
