<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Datatables;
use Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use App\MenuForm;
use App\CekAkses;

class TransferController extends Controller
{

    protected $client;

    // ////////////////////////////// Hapus ////////////////////////////////////////////////////////////////////////
    public function hapusdataindex(Request $request)
    {
        if (Auth::check()) {
            return view('api.hapusdata');
        } else {
            return view('errors.401');
        }
    }

    public function proseshapusdataumum()
    {
        $url = env('URL_API_KEU','forge');
        $getUrl = json_decode(@file_get_contents($url . 'DeleteDataUmum/1'), true);
        $temp = '';
        $count = 0;
        foreach ($getUrl['result'] as $key => $value) {
            if (! is_array($value)) {                
                $temp = $temp . $value;
            } else {
                foreach ($value as $key => $val) {                    
                    $temp = $temp . $value;
                }
            }
        }
        return json_encode([
            'pesan' => $temp,
            'status_pesan' => '1'
        ]);
    }

    public function prosestrfApiurbid()
    {
        $url = env('URL_API_KEU','forge');
        $getUrl = json_decode(@file_get_contents($url . 'KirimUrusan/1'), true);
        $temp = '';
        $count = 0;
        foreach ($getUrl['result'] as $key => $value) {
            if (! is_array($value)) {                
                $temp = $temp . $value;
            } else {
                foreach ($value as $key => $val) {                    
                    $temp = $temp . $value;
                }
            }
        }
        return json_encode([
            'pesan' => $temp,
            'status_pesan' => '1'
        ]);
    }
    
    public function prosestrfApiunit()
    {
        $url = env('URL_API_KEU','forge');
        $getUrl = json_decode(@file_get_contents($url . 'KirimUnit/1'), true);
        $temp = '';
        $count = 0;
        foreach ($getUrl['result'] as $key => $value) {
            if (! is_array($value)) {                
                $temp = $temp . $value;
            } else {
                foreach ($value as $key => $val) {                    
                    $temp = $temp . $value;
                }
            }
        }
        return json_encode([
            'pesan' => $temp,
            'status_pesan' => '1'
        ]);
    }
    

    public function getApiRefRek5()
    {
        $url = env('URL_API_KEU','forge');
        $getUrl = json_decode(@file_get_contents($url . 'CekRefRek5/1'), true);
        $temp = '';
        $count = 0;
      /*  foreach ($getUrl['result'][0] as $key => $value) {
            if (! is_array($value)) {
                
                if ($key == 'Id_Rekening') {
                    if ($count == 0) {
                        
                        $temp = $temp . $value;
                        $count ++;
                    } else {
                        $temp = $temp . ',' . $value;
                        $count ++;
                    }
                }
            } else {
                foreach ($value as $key => $val) {
                    
                    if ($key == 'Id_Rekening') {
                        if ($count == 0) {
                            $temp = $temp . $val;
                            $count ++;
                        } else {
                            $temp = $temp . ',' . $val;
                            $count ++;
                        }
                    }
                }
            }
        }*/
		$temp=0;
        if($temp>0)
        {
        $getRek5 = DB::select('SELECT kd_rek_1, kd_rek_2, kd_rek_3,kd_rek_4,kd_rek_5,nama_kd_rek_5,peraturan,"Tidak Ada di Simda Keuangan" as keterangan
                    from ref_rek_5
                    where id_rekening in (' . $temp . ')');
        }
        else 
        {
            $getRek5 = DB::select('SELECT kd_rek_1,kd_rek_2,kd_rek_3,kd_rek_4,kd_rek_5,nama_kd_rek_5,peraturan,"Tidak Ada di Simda Keuangan" as keterangan
                    from ref_rek_5
                    where id_rekening=0');
        }
        
        return DataTables::of($getRek5)->make(true);
    }

    public function prosestrfApiRefRek5()
    {
        $url = env('URL_API_KEU','forge');
        $getUrl = json_decode(@file_get_contents($url . 'KirimRekening/1'), true);
        $temp = '';
        $count = 0;
        foreach ($getUrl['result'] as $key => $value) {
            if (! is_array($value)) {
                
                $temp = $temp . $value;
            } else {
                foreach ($value as $key => $val) {
                    
                    $temp = $temp . $value;
                }
            }
        }
        return json_encode([
            'pesan' => $temp,
            'status_pesan' => '1'
        ]);
    }

    public function prosestrfApirogram()
    {
        $url = env('URL_API_KEU','forge');
        $getUrl = json_decode(@file_get_contents($url . 'KirimProgram/1'), true);
        $temp = '';
        $count = 0;
        foreach ($getUrl['result'] as $key => $value) {
            if (! is_array($value)) {
                
                $temp = $temp . $value;
            } else {
                foreach ($value as $key => $val) {
                    
                    $temp = $temp . $value;
                }
            }
        }
        return json_encode([
            'pesan' => $temp,
            'status_pesan' => '1'
        ]);
    }

    public function prosestrfApirenstra()
    {
        $url = env('URL_API_KEU','forge');
        $getUrl = json_decode(@file_get_contents($url . 'KirimRenstra/1'), true);
        $temp = '';
        $count = 0;
        foreach ($getUrl['result'] as $key => $value) {
            if (! is_array($value)) {
                
                $temp = $temp . $value;
            } else {
                foreach ($value as $key => $val) {
                    
                    $temp = $temp . $value;
                }
            }
        }
        return json_encode([
            'pesan' => $temp,
            'status_pesan' => '1'
        ]);
    }

    public function prosestrfApiPendapatan()
    {
        $url = env('URL_API_KEU','forge');
        $getUrl = json_decode(@file_get_contents($url . 'KirimPendapatan/1'), true);
        $temp = '';
        $count = 0;
        foreach ($getUrl['result'] as $key => $value) {
            if (! is_array($value)) {
                
                $temp = $temp . $value;
            } else {
                foreach ($value as $key => $val) {
                    
                    $temp = $temp . $value;
                }
            }
        }
        return json_encode([
            'pesan' => $temp,
            'status_pesan' => '1'
        ]);
    }

    public function prosestrfApiBelanja()
    {
        $url = env('URL_API_KEU','forge');
        $unit = DB::select('SELECT c.kd_urusan,c.kd_bidang,b.kd_unit from trx_anggaran_program_pd a
                    inner join ref_unit b on a.id_unit=b.id_unit
                    inner join ref_bidang c on b.id_bidang=c.id_bidang 
                    GROUP BY b.kd_unit,c.kd_bidang,c.kd_urusan 
                    order by c.kd_urusan,c.kd_bidang,b.kd_unit');

        if (count($unit) > 0) {
            foreach ($unit as $row) {
                $getUrl = json_decode(@file_get_contents($url . 'KirimBelanja/1/' . $row->kd_urusan . '.' . $row->kd_bidang . '.' . $row->kd_unit), true);
                $temp = '';
                $count = 0;
                //$type=$getUrl[0];

                // foreach ($getUrl['result'] as $key => $value) {
                //     if (! is_array($value)) {                        
                //         $temp = $temp . $value;
                //     } else {
                //         foreach ($value as $key => $val) {                            
                //             $temp = $temp . $value;
                //         }
                //     }
                // }
            }
            return json_encode([
                // 'pesan' => $url . 'KirimBelanja/1/' . $row->kd_urusan . '.' . $row->kd_bidang . '.' . $row->kd_unit,
                'pesan' => $temp,
                'status_pesan' => '1'
            ]);
        } else {
            return json_encode([
                'pesan' => 'tidak ada data yang akan dikirim',
                'status_pesan' => '1'
            ]);
        }
    }


public function getApiRealisasi()
    {
        $url = env('URL_API_KEU','forge');
        $getUrl = json_decode(@file_get_contents($url . 'CekRealisasi/1'), true);
        $temp = '';
        $count = 0;
		$count2=0;
        foreach ($getUrl['result'][0] as $key => $value) {
			if($count==0)
			{
            if (! is_array($value)) {
                
                if ($key == 'Kd_urusan') {
                 $temp=$temp.' Select '.$value.' as Kd_urusan,' ;  
                }
				else if ($key == 'Kd_Bidang') {
                 $temp=$temp.$value.' as Kd_Bidang,';   
                }
				else if ($key == 'Kd_Unit') {
                 $temp=$temp.$value.' as Kd_Unit,'   ;
                }
				else if ($key == 'Kd_Sub') {
                 $temp=$temp.$value.' as Kd_Sub,'   ;
                }
				else if ($key == 'Kd_Prog') {
                 $temp=$temp.$value.' as Kd_Prog,'   ;
                }
				else if ($key == 'ID_Prog') {
                 $temp=$temp.$value.' as ID_Prog,'   ;
                }
				else if ($key == 'Kd_Keg') {
                 $temp=$temp.$value.' as Kd_Keg,'   ;
                }
				else if ($key == 'Kd_Rek_1') {
                 $temp=$temp.$value.' as Kd_Rek_1,'  ; 
                }
				else if ($key == 'Kd_Rek_2') {
                 $temp=$temp.$value.' as Kd_Rek_2,'   ;
                }
				else if ($key == 'Kd_Rek_3') {
                 $temp=$temp.$value.' as Kd_Rek_3,'   ;
                }
				
				else if ($key == 'Kd_Rek_4') {
                 $temp=$temp.$value.' as Kd_Rek_4,'   ;
                }
				else if ($key == 'Kd_Rek_5') {
                 $temp=$temp.$value.' as Kd_Rek_5,'   ;
                }
				else if ($key == 'Anggaran') {
                 $temp=$temp.$value.' as Anggaran,'   ;
                }
				else if ($key == 'Saldo') {
                 $temp=$temp.$value.' as Saldo' ;  
                }
				
            } else {
				$count2=0;
                foreach ($value as $key => $val) {
                    if($count2<14)
					{
                     if ($key == 'Kd_urusan') {
                 $temp=$temp.' Select '.$val.' as Kd_urusan,'   ;
                }
				else if ($key == 'Kd_Bidang') {
                 $temp=$temp.$val.' as Kd_Bidang,'   ;
                }
				else if ($key == 'Kd_Unit') {
                 $temp=$temp.$val.' as Kd_Unit,'   ;
                }
				else if ($key == 'Kd_Sub') {
                 $temp=$temp.$val.' as Kd_Sub,'   ;
                }
				else if ($key == 'Kd_Prog') {
                 $temp=$temp.$val.' as Kd_Prog,'   ;
                }
				else if ($key == 'ID_Prog') {
                 $temp=$temp.$val.' as ID_Prog,'   ;
                }
				else if ($key == 'Kd_Keg') {
                 $temp=$temp.$val.' as Kd_Keg,'   ;
                }
				else if ($key == 'Kd_Rek_1') {
                 $temp=$temp.$val.' as Kd_Rek_1,'  ; 
                }
				else if ($key == 'Kd_Rek_2') {
                 $temp=$temp.$val.' as Kd_Rek_2,'   ;
                }
				else if ($key == 'Kd_Rek_3') {
                 $temp=$temp.$val.' as Kd_Rek_3,'   ;
                }
				
				else if ($key == 'Kd_Rek_4') {
                 $temp=$temp.$val.' as Kd_Rek_4,'   ;
                }
				else if ($key == 'Kd_Rek_5') {
                 $temp=$temp.$val.' as Kd_Rek_5,'   ;
                }
				else if ($key == 'Anggaran') {
                 $temp=$temp.$val.' as Anggaran,'   ;
                }
				else if ($key == 'Saldo') {
                 $temp=$temp.$val.' as Saldo '   ;
                }
				$count2=$count2+1;
					}
                }
            }
			}
			else
			{
            if (! is_array($value)) {
                
                if ($key == 'Kd_urusan') {
                 $temp=$temp.' Union All Select '.$value.' as Kd_urusan,' ;  
                }
				else if ($key == 'Kd_Bidang') {
                 $temp=$temp.$value.' as Kd_Bidang,';   
                }
				else if ($key == 'Kd_Unit') {
                 $temp=$temp.$value.' as Kd_Unit,'   ;
                }
				else if ($key == 'Kd_Sub') {
                 $temp=$temp.$value.' as Kd_Sub,'   ;
                }
				else if ($key == 'Kd_Prog') {
                 $temp=$temp.$value.' as Kd_Prog,'   ;
                }
				else if ($key == 'ID_Prog') {
                 $temp=$temp.$value.' as ID_Prog,'   ;
                }
				else if ($key == 'Kd_Keg') {
                 $temp=$temp.$value.' as Kd_Keg,'   ;
                }
				else if ($key == 'Kd_Rek_1') {
                 $temp=$temp.$value.' as Kd_Rek_1,'  ; 
                }
				else if ($key == 'Kd_Rek_2') {
                 $temp=$temp.$value.' as Kd_Rek_2,'   ;
                }
				else if ($key == 'Kd_Rek_3') {
                 $temp=$temp.$value.' as Kd_Rek_3,'   ;
                }
				
				else if ($key == 'Kd_Rek_4') {
                 $temp=$temp.$value.' as Kd_Rek_4,'   ;
                }
				else if ($key == 'Kd_Rek_5') {
                 $temp=$temp.$value.' as Kd_Rek_5,'   ;
                }
				else if ($key == 'Anggaran') {
                 $temp=$temp.$value.' as Anggaran,'   ;
                }
				else if ($key == 'Saldo') {
                 $temp=$temp.$value.' as Saldo' ;  
                }
				
            } else {
                foreach ($value as $key => $val) {
                    
                     if ($key == 'Kd_urusan') {
                 $temp=$temp.' Union All Select '.$val.' as Kd_urusan,'   ;
                }
				else if ($key == 'Kd_Bidang') {
                 $temp=$temp.$val.' as Kd_Bidang,'   ;
                }
				else if ($key == 'Kd_Unit') {
                 $temp=$temp.$val.' as Kd_Unit,'   ;
                }
				else if ($key == 'Kd_Sub') {
                 $temp=$temp.$val.' as Kd_Sub,'   ;
                }
				else if ($key == 'Kd_Prog') {
                 $temp=$temp.$val.' as Kd_Prog,'   ;
                }
				else if ($key == 'ID_Prog') {
                 $temp=$temp.$val.' as ID_Prog,'   ;
                }
				else if ($key == 'Kd_Keg') {
                 $temp=$temp.$val.' as Kd_Keg,'   ;
                }
				else if ($key == 'Kd_Rek_1') {
                 $temp=$temp.$val.' as Kd_Rek_1,'  ; 
                }
				else if ($key == 'Kd_Rek_2') {
                 $temp=$temp.$val.' as Kd_Rek_2,'   ;
                }
				else if ($key == 'Kd_Rek_3') {
                 $temp=$temp.$val.' as Kd_Rek_3,'   ;
                }
				
				else if ($key == 'Kd_Rek_4') {
                 $temp=$temp.$val.' as Kd_Rek_4,'   ;
                }
				else if ($key == 'Kd_Rek_5') {
                 $temp=$temp.$val.' as Kd_Rek_5,'   ;
                }
				if ($key == 'Anggaran') {
                 $temp=$temp.$val.' as Anggaran,'   ;
                }
				if ($key == 'Saldo') {
                 $temp=$temp.$val.' as Saldo '   ;
                }
                }
            }
			}
			$count++;
        }
		$getRek5 = DB::select($temp);
        return DataTables::of($getRek5)->make(true);
    }


}