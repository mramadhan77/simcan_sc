  <div id="ModalKegiatan" class="modal fade" role="dialog">
      <div class="modal-dialog modal-lg"  >
        <div class="modal-content">
          <div class="modal-header">
            <h4 class="modal-title"></h4>
          </div>
          <div class="modal-body">
            <form class="form-horizontal" role="form" autocomplete='off' action="" method="post" >
                <input type="hidden" name="_token" value="<?php echo e(csrf_token()); ?>">
                <input type="hidden" class="form-control" id="id_perkin_kegiatan" name="id_perkin_kegiatan" readonly >
                <input type="hidden" class="form-control" id="id_perkin_dokumen_kegiatan" name="id_perkin_dokumen_kegiatan" readonly>
                <input type="hidden" class="form-control" id="id_kegiatan_renstra" name="id_kegiatan_renstra" readonly>
                <div class="form-group">
                    <label class="control-label col-sm-3" for="title">Uraian Kegiatan :</label>
                    <div class="col-sm-8">
                      <textarea type="name" class="form-control" id="uraian_kegiatan" rows="3" disabled></textarea>
                    </div>
                    <input type="hidden" id="id_kegiatan_ref" name="id_kegiatan_ref">
                </div> 
                <label class="col-sm-12" style="text-align: left;">Rincian Pagu Kegiatan :</label>
                <br>
                <table id="tblIndikatorSasaran" class="table table-bordered"  cellspacing="0" width="100%">
                        <thead style="background: #428bca; color: #fff">
                            <tr>
                              <th width="20%" style="text-align: center; vertical-align:middle">Triwulan 1</th>
                              <th width="20%" style="text-align: center; vertical-align:middle">Triwulan 2</th>
                              <th width="20%" style="text-align: center; vertical-align:middle">Triwulan 3</th>
                              <th width="20%" style="text-align: center; vertical-align:middle">Triwulan 4</th>
                            </tr>
                        </thead>
                        <tbody>
                          <tr>
                              <td width="20%" style="text-align: center; vertical-align:middle">
                                <input type="text" class="form-control number" id="pagu_t1" name="pagu_t1" style="text-align: right; ">
                              </td>
                              <td width="20%" style="text-align: center; vertical-align:middle">
                                <input type="text" class="form-control number" id="pagu_t2" name="pagu_t2" style="text-align: right; " >
                              </td>
                              <td width="20%" style="text-align: center; vertical-align:middle">
                                <input type="text" class="form-control number" id="pagu_t3" name="pagu_t3" style="text-align: right; " >
                              </td>
                              <td width="20%" style="text-align: center; vertical-align:middle">
                                <input type="text" class="form-control number" id="pagu_t4" name="pagu_t4" style="text-align: right; " >
                              </td>
                          </tr>
                          <tr style="background: #428bca; color: #fff">
                              <td colspan="2" style="text-align: center; vertical-align:middle; font-weight: bold;">Pagu Total :
                              </td>
                              <td style="text-align: center; vertical-align:middle">
                                 <input type="text" class="form-control number" id="pagu_tahun" name="pagu_tahun" style="text-align: right; " readonly >
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
                          <button type="button" class="btn btn-success btnSimpanProgram btn-labeled" data-dismiss="modal">
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