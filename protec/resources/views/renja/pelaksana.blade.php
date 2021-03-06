<div class="row">
    <div class="col-md-12">
        <table id="w0" class="table table-striped table-bordered detail-view">
            <tbody>
                <tr>
                    <th>Misi</th>
                    <td>{{ $rkpd['uraian_misi_renstra'] }}</td>
                </tr>
                <tr>
                    <th>Tujuan/Sasaran</th>
                    <td>{{ $rkpd['uraian_tujuan_renstra'] }}</td>
                </tr>
                <tr>
                    <th>Program</th>
                    <td>{{ $rkpd['uraian_program_renstra'] }} </td>
                </tr>
                <tr>
                    <th>Kegiatan</th>
                    <td>{{ $rkpd['uraian_kegiatan_renstra'] }} </br>Pagu: {{ number_format($rkpd['pagu_tahun_kegiatan'], 0, ',', '.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <!-- Tabs Below -->
        <div class='tabs-x tabs-above tab-bordered tab-height-md tabs-krajee'>
            <ul id="myTab-6" class="nav nav-tabs" role="tablist">
                <li class="active"><a href="#unitpelaksana">Unit Pelaksana</a></li>
                <li><a href="#kebijakan" role="tab" data-toggle="tab">Kebijakan</a></li>
                <li><a href="#indikator" role="tab-kv" data-toggle="tab">Indikator</a></li>
            </ul>        
            <div id="myTabContent-6" class="tab-content">
                <div class="tab-pane fade in active" id="unitpelaksana">
                    <div class="row">
                        <div class="col-md-12">
                            <a class="btn btn-primary btn-xs" href="#" data-href="{{ url('/renja/'.$title[1].'/'.$rkpd->id_renja.'/pelaksana/tambah') }}" data-toggle="modal" data-target="#myModal" data-title="Tambah Pelaksana RKPD"><i class="glyphicon glyphicon-plus bg-white"></i> Tambah</a>
                            <table id="pelaksana-table" class="table table-striped table-bordered table-responsive">
                                <thead>
                                    <tr>
                                        <th style="text-align: center; vertical-align:middle">No Urut</th>
                                        <th style="text-align: center; vertical-align:middle">Kode Sub Unit</th>
                                        <th style="text-align: center; vertical-align:middle">Nama Sub Unit</th>
                                        <th style="text-align: right; vertical-align:middle">Pagu</th>
                                        <th style="text-align: center; vertical-align:middle">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; ?>
                                    @foreach($pelaksana as $data)
                                    <tr>
                                        <td> {{ $i }} </td>
                                        <td> {{ $data->getSubUnit->id_unit.'.'.$data->id_sub_unit }} </td>
                                        <td> {{ $data->getSubUnit->nm_sub }} </td>
                                        <td style="text-align: right; vertical-align:middle"> {{ number_format($data->pagu_aktivitas, 0, ',', '.') }} </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                 <a id="rincian-{{ $data->id_pelaksana_renja }}" class="btn btn-default btn-xs" data-href="{{ url('/renja/'.$title['1'].'/'.$data->id_renja.'/pelaksana/'.$data->id_pelaksana_renja.'/belanja') }}" ><i class="glyphicon glyphicon-menu-right bg-white"></i> Belanja</a>
                                                {{ Form::open(['method' => 'DELETE', 'url' => '/renja/'.$title[1].'/'.$data->id_renja.'/pelaksana/'.$data->id_pelaksana_renja.'/delete', 'onsubmit' => 'return confirm(\'Anda Yakin?\');']) }}
                                                    <button class="btn btn-xs btn-default" id="submit" type="submit">
                                                        <i class="glyphicon glyphicon-trash bg-white"></i> Hapus
                                                    </button>
                                                {{ Form::close() }}
                                            </div>              
                                        </td>
                                        
                                    </tr>
                                    <?php $i++; ?>
                                    @endforeach
                                </tbody>                
                            </table>
                        </div><!--column-->
                    </div><!--row-->             
                </div>
                <div class="tab-pane fade" id="kebijakan">
                    <div class="row">
                        <div class="col-md-12">
                            <a class="btn btn-primary btn-xs" href="#" data-href="{{ url('/renja/'.$title[1].'/'.$rkpd->id_renja.'/kebijakan/tambah') }}" data-toggle="modal" data-target="#myModal" data-title="Tambah Kebijakan RKPD"><i class="glyphicon glyphicon-plus bg-white"></i> Tambah</a>
                            <table id="kebijakan-table" class="table table-striped table-bordered table-responsive">
                                <thead>
                                    <tr>
                                        <th style="text-align: center; vertical-align:middle">No Urut</th>
                                        <th style="text-align: center; vertical-align:middle">Uraian</th>
                                        <th style="text-align: center; vertical-align:middle">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; ?>
                                    @foreach($kebijakan as $data)
                                    <tr>
                                        <td> {{ $i }} </td>
                                        <td> {{ $data->uraian_kebijakan }} </td>
                                        <td nowrap>
                                            <div class="btn-group" role="group">
                                                <a class="btn btn-default btn-xs" data-href="{{ url('/renja/'.$title[1].'/'.$data->id_renja.'/kebijakan/'.$data->id_kebijakan_renja.'/ubah') }}" data-toggle="modal" data-target="#myModal" data-title="Sesuaikan Kebijakan #{{ $data->uraian_kebijakan }}"><i class="glyphicon glyphicon-pencil bg-white"></i> Ubah</a>
                                                {{ Form::open(['method' => 'DELETE', 'url' => '/renja/'.$title[1].'/'.$data->id_renja.'/kebijakan/'.$data->id_kebijakan_renja.'/delete', 'onsubmit' => 'return confirm(\'Anda Yakin?\');']) }}
                                                    <button class="btn btn-xs btn-default" id="submit" type="submit">
                                                        <i class="glyphicon glyphicon-trash bg-white"></i> Hapus
                                                    </button>
                                                {{ Form::close() }}
                                            </div>    
                                        </td>
                                        
                                    </tr>
                                    <?php $i++; ?>
                                    @endforeach
                                </tbody>                
                            </table>
                        </div><!--column-->
                    </div><!--row-->                
                </div>
                <div class="tab-pane fade" id="indikator">
                    <div class="row">
                        <div class="col-md-12">
                            <!--<a class="btn btn-primary btn-xs" href="#" data-href="{{ url('/renja/'.$title[1].'/'.$rkpd->id_renja.'/indikator/tambah') }}" data-toggle="modal" data-target="#myModal" data-title="Tambah Indikator RKPD"><i class="glyphicon glyphicon-plus bg-white"></i> Tambah</a>-->
                            <table id="indikator-table" class="table table-striped table-bordered table-responsive">
                                <thead>
                                    <tr>
                                        <th style="text-align: center; vertical-align:middle">No Urut</th>
                                        <th style="text-align: center; vertical-align:middle">Jenis</th>
                                        <th style="text-align: center; vertical-align:middle">Uraian</th>
                                        <th style="text-align: right; vertical-align:middle">Tolak Ukur</th>
                                        <th style="text-align: center; vertical-align:middle">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php $i = 1; ?>
                                    @foreach($indikator as $data)
                                    <tr>
                                        <td> {{ $i }} </td>
                                        <td> {{ $data->kd_indikator }} </td>
                                        <td> {{ $data->uraian_indikator_program_rpjmd }} </td>
                                        <td> {{ $data->tolok_ukur_indikator }} </td>
                                        <td style="text-align: right; vertical-align:middle"> {{ number_format($data->angka_tahun, 0, ',', '.') }} </td>
                                        <td>
                                            {!! Form::open(['method' => 'POST', 'url' => '/admin/parameter/user/'.$data->user_id.'.'.$data->kd_unit.'.'.$data->kd_sub.'/deleteunit', 'onsubmit' => 'return confirm(\'Anda Yakin?\');']) !!}
                                                <button class="btn btn-xs btn-default" id="submit" type="submit">
                                                    <i class="glyphicon glyphicon-remove bg-white"></i> Hapus
                                                </button>
                                            {!! Form::close() !!}                
                                        </td>
                                        
                                    </tr>
                                    <?php $i++; ?>
                                    @endforeach
                                </tbody>                
                            </table>
                        </div><!--column-->
                    </div><!--row-->                 
                </div>
            </div>

        </div>        
    </div>
</div>


<script>
    $(document).ready(function(){    
        // // ajax for datatables
        // $(function() {
        //     $('#ranwal-table').DataTable({
        //         processing: true,
        //         serverSide: true,
        //         ajax: '{{ url('/renja/btl/') }}',
        //         columns: [
        //             { data: 'no_urut', name: 'no_urut' },
        //             { data: 'id_program_rpjmd', name: 'id_program_rpjmd' },
        //             { data: 'uraian_program_rpjmd', name: 'uraian_program_rpjmd' },
        //             { 
        //                 data: 'pagu_program_rpjmd', 
        //                 name: 'pagu_program_rpjmd',
        //                 render: $.fn.dataTable.render.number( '.', ',', 0, 'Rp' ),
        //                 sClass: "dt-right" 
        //             },
        //             { data: 'status_data', name: 'status_data' },
        //             { data: 'status_data', name: 'status_data' },
        //             // { data: 'action', name: 'action', orderable: false, searchable: false }
        //         ],
        //         aoColumnDefs: [
        //             { sClass: "dt-center", aTargets: [ 0, 1, 4, 5 ] },
        //         ],
        //         createdRow: function( row, data, dataIndex ) {
        //             $(row).attr('data-id', data.id_renja);
        //         }
        //     });
        // });     

        $('#myTab-6 a').click(function (e) {
            e.preventDefault()
            $(this).tab('show')
        });
        
        $('#pelaksana-table tbody').on('click', 'a', function(e){
            var id = $(this).attr('id')
            var target = e.target;
            if(id) var id = id.split('-');
            if(typeof id !== 'undefined' && id[0] == 'rincian'){
                var href = $(this).data('href');
                if(e.target == target){ //actually, we should check if e.target == this. But after I checked it, this method didn't work, and I dunno why
                    // var href = '{{ url('/ranwalrkpd/btl/') }}/' + id + '/pelaksana';
                    $('#tab-pelaksana').removeClass('active');
                    // $('#tab-home').attr('class', 'disabled');
                    $('#tab-pelaksana').html('<a href=\"#pelaksana\"  data-toggle=\"tab\" role=\"tab\" title=\"program\"><i class=\"glyphicon glyphicon-home\"></i> Kebijakan - Pelaksana - Indikator</a>');
                    $('#tab-belanja').attr('class', 'active');

                    $('#link-pelaksana').click();
                    $('#pelaksana').removeClass('active in');
                    $('#belanja').addClass('active in');
                    $('#belanja').html('<i class=\"fa fa-spinner fa-spin\"></i>');
                    $.get(href).done(function(data){
                        $('#belanja').html(data);
                        // console.log('voila pelaksana');
                    });
                }
            }
        });                 
    });
</script>
