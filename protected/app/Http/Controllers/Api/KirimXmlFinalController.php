<?php
namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Input;
use App\Http\Requests;
use Carbon\Carbon;
use Session;
use DB;
use Validator;
use Response;
use XMLWriter;
use SimpleXMLElement;

class KirimXmlFinalController extends Controller
{

    public function getUrusan(Request $request)
    {
        $unit = DB::SELECT('SELECT kd_urusan, nm_urusan FROM ref_urusan');
        $fungsi = DB::SELECT('SELECT kd_fungsi, nm_fungsi FROM ref_fungsi');
        $bidang = DB::SELECT('SELECT id_bidang, kd_urusan, kd_bidang, nm_bidang, kd_fungsi FROM ref_bidang');
        
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><mydoc></mydoc>');
        
        $xml->addAttribute('Created', 'by Simd@');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $tabel = $xml->addChild('tabel');
        $tabel->addAttribute('tabel', 'ref_urusan');
        foreach ($unit as $unit) {
            $data = $tabel->addChild('data');
            $data->addChild('kd_urusan', $unit->kd_urusan);
            $data->addChild('nm_urusan', $unit->nm_urusan);
        }
        $tabel1 = $xml->addChild('tabel');
        $tabel1->addAttribute('tabel', 'ref_fungsi');
        foreach ($fungsi as $fungsi) {
            $data = $tabel1->addChild('data');
            $data->addChild('kd_fungsi', $fungsi->kd_fungsi);
            $data->addChild('nm_fungsi', $fungsi->nm_fungsi);
        }
        $tabel2 = $xml->addChild('tabel');
        $tabel2->addAttribute('tabel', 'ref_bidang');
        foreach ($bidang as $bidang) {
            $data = $tabel2->addChild('data');
            $data->addChild('kd_urusan', $bidang->kd_urusan);
            $data->addChild('kd_bidang', $bidang->kd_bidang);
            $data->addChild('nm_bidang', $bidang->nm_bidang);
            $data->addChild('kd_fungsi', $bidang->kd_fungsi);
        }
        
        $response = Response::make($xml->asXML(), 200);
        $response->header('Cache-Control', 'public');
        $response->header('Content-Description', 'File Transfer');
        $response->header('Content-Disposition', 'attachment; filename=' . TIME() . '.xml');
        $response->header('Content-Transfer-Encoding', 'binary');
        $response->header('Content-Type', 'text/xml');
        
        return $response;
    }

    public function getUnit(Request $request)
    {
        $unit = DB::SELECT('SELECT a.id_unit, a.id_bidang, b.kd_urusan, b.kd_bidang, a.kd_unit, a.nm_unit
        FROM ref_unit a INNER JOIN ref_bidang b ON a.id_bidang = b.id_bidang ');
        $sub = DB::SELECT('SELECT c.kd_urusan, c.kd_bidang, b.kd_unit, a.kd_sub, a.nm_sub, a.id_sub_unit, a.id_unit
        FROM ref_sub_unit a INNER JOIN ref_unit b ON a.id_unit = b.id_unit
        INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang ');
        
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><mydoc></mydoc>');
        
        $xml->addAttribute('Created', 'by Simd@');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $tabel = $xml->addChild('tabel');
        $tabel->addAttribute('tabel', 'ref_unit');
        foreach ($unit as $unit) {
            $data = $tabel->addChild('data');
            $data->addChild('id_unit', $unit->id_unit);
            $data->addChild('id_bidang', $unit->id_bidang);
            $data->addChild('kd_urusan', $unit->kd_urusan);
            $data->addChild('kd_bidang', $unit->kd_bidang);
            $data->addChild('kd_unit', $unit->kd_unit);
            $data->addChild('nm_unit', $unit->nm_unit);
        }
        $tabel1 = $xml->addChild('tabel');
        $tabel1->addAttribute('tabel', 'ref_sub_unit');
        foreach ($sub as $sub) {
            $data = $tabel1->addChild('data');
            $data->addChild('kd_urusan', $sub->kd_urusan);
            $data->addChild('kd_bidang', $sub->kd_bidang);
            $data->addChild('kd_unit', $sub->kd_unit);
            $data->addChild('kd_sub', $sub->kd_sub);
            $data->addChild('nm_sub_unit', $sub->nm_sub);
            $data->addChild('id_sub_unit', $sub->id_sub_unit);
            $data->addChild('id_unit', $sub->id_unit);
        }
        
        $response = Response::make($xml->asXML(), 200);
        $response->header('Cache-Control', 'public');
        $response->header('Content-Description', 'File Transfer');
        $response->header('Content-Disposition', 'attachment; filename=' . TIME() . '.xml');
        $response->header('Content-Transfer-Encoding', 'binary');
        $response->header('Content-Type', 'text/xml');
        
        return $response;
    }

    public function getProgram(Request $request)
    {
        $program = DB::SELECT('SELECT a.id_program, a.kd_program, XML_Encode(a.uraian_program) as uraian_program, b.kd_urusan, b.kd_bidang
            FROM ref_program AS a INNER JOIN ref_bidang AS b ON a.id_bidang = b.id_bidang ');
        
        $kegiatan = DB::SELECT('SELECT a.kd_kegiatan, XML_Encode(a.nm_kegiatan) as nama_kegiatan, a.id_kegiatan, b.kd_program, c.kd_urusan, c.kd_bidang
        FROM ref_kegiatan AS a
        INNER JOIN ref_program AS b ON a.id_program = b.id_program
        INNER JOIN ref_bidang AS c ON b.id_bidang = c.id_bidang');
        
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><mydoc></mydoc>');
        
        $xml->addAttribute('Created', 'by Simd@');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $tabel = $xml->addChild('tabel');
        $tabel->addAttribute('tabel', 'ref_program');
        foreach ($program as $program) {
            $data = $tabel->addChild('data');
            $data->addChild('id_program', $program->id_program);
            $data->addChild('kd_urusan', $program->kd_urusan);
            $data->addChild('kd_bidang', $program->kd_bidang);
            $data->addChild('kd_program', $program->kd_program);
            $data->addChild('uraian_program', $program->uraian_program);
        }
        $tabel1 = $xml->addChild('tabel');
        $tabel1->addAttribute('tabel', 'ref_kegiatan');
        foreach ($kegiatan as $kegiatan) {
            $data = $tabel1->addChild('data');
            $data->addChild('id_kegiatan', $kegiatan->id_kegiatan);
            $data->addChild('kd_urusan', $kegiatan->kd_urusan);
            $data->addChild('kd_bidang', $kegiatan->kd_bidang);
            $data->addChild('kd_program', $kegiatan->kd_program);
            $data->addChild('kd_kegiatan', $kegiatan->kd_kegiatan);
            $data->addChild('nm_kegiatan', $kegiatan->nama_kegiatan);
        }
        
        $response = Response::make($xml->asXML(), 200);
        $response->header('Cache-Control', 'public');
        $response->header('Content-Description', 'File Transfer');
        $response->header('Content-Disposition', 'attachment; filename=' . TIME() . '.xml');
        $response->header('Content-Transfer-Encoding', 'binary');
        $response->header('Content-Type', 'text/xml');
        
        return $response;
    }

    public function getItemSSH(Request $request)
    {
        $unit = DB::SELECT('SELECT ref_ssh_tarif.id_tarif_ssh,ref_ssh_tarif.no_urut,ref_ssh_tarif.id_sub_kelompok_ssh,
		XML_Encode(ref_ssh_tarif.uraian_tarif_ssh) as uraian_tarif_ssh ,XML_Encode(ref_ssh_tarif.keterangan_tarif_ssh) as keterangan_tarif_ssh,
		ref_ssh_tarif.id_satuan FROM ref_ssh_tarif ');
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><mydoc></mydoc>');
        
        $xml->addAttribute('Created', 'by Simd@');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $tabel = $xml->addChild('tabel');
        $tabel->addAttribute('tabel', 'ref_program');
        foreach ($unit as $program) {
            $data = $tabel->addChild('data');
            $data->addChild('id_tarif_ssh', $program->id_tarif_ssh);
            $data->addChild('no_urut', $program->no_urut);
            $data->addChild('id_sub_kelompok_ssh', $program->id_sub_kelompok_ssh);
            $data->addChild('uraian_tarif_ssh', $program->uraian_tarif_ssh);
            $data->addChild('keterangan_tarif_ssh', $program->keterangan_tarif_ssh);
            $data->addChild('id_satuan', $program->id_satuan);
        }
        
        $response = Response::make($xml->asXML(), 200);
        $response->header('Cache-Control', 'public');
        $response->header('Content-Description', 'File Transfer');
        $response->header('Content-Disposition', 'attachment; filename=' . TIME() . '.xml');
        $response->header('Content-Transfer-Encoding', 'binary');
        $response->header('Content-Type', 'text/xml');
        
        return $response;
    }

    public function getRefRek(Request $request)
    {
        $rek1 = DB::SELECT('select kd_rek_1,nama_kd_rek_1 from ref_rek_1 ');
        $rek2 = DB::SELECT('select kd_rek_1,kd_rek_2,nama_kd_rek_2 from ref_rek_2 ');
        $rek3 = DB::SELECT('select kd_rek_1,kd_rek_2, kd_rek_3,nama_kd_rek_3,saldo_normal from ref_rek_3 ');
        $rek4 = DB::SELECT('select kd_rek_1,kd_rek_2, kd_rek_3,kd_rek_4,nama_kd_rek_4 from ref_rek_4 ');
        $rek5 = DB::SELECT('SELECT kd_rek_1,kd_rek_2, kd_rek_3,kd_rek_4,kd_rek_5, min(XML_Encode(nama_kd_rek_5)) as nama_kd_rek_5, min(XML_Encode(Peraturan)) as peraturan
		FROM Ref_Rek_5 group by kd_rek_1,kd_rek_2, kd_rek_3,kd_rek_4,kd_rek_5 ');
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><mydoc></mydoc>');
        
        $xml->addAttribute('Created', 'by Simd@');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $tabel = $xml->addChild('tabel');
        $tabel->addAttribute('tabel', 'ref_rek_1');
        foreach ($rek1 as $rek1) {
            $data = $tabel->addChild('data');
            $data->addChild('kd_rek_1', $rek1->kd_rek_1);
            $data->addChild('nama_kd_rek_1', $rek1->nama_kd_rek_1);
            
        }
        $tabel1 = $xml->addChild('tabel');
        $tabel1->addAttribute('tabel', 'ref_rek_2');
        foreach ($rek2 as $rek2) {
            $data = $tabel1->addChild('data');
            $data->addChild('kd_rek_1', $rek2->kd_rek_1);
            $data->addChild('kd_rek_2', $rek2->kd_rek_2);
            $data->addChild('nama_kd_rek_2', $rek2->nama_kd_rek_2);
            
        }
        $tabel2 = $xml->addChild('tabel');
        $tabel2->addAttribute('tabel', 'ref_rek_3');
        foreach ($rek3 as $rek3) {
            $data = $tabel2->addChild('data');
            $data->addChild('kd_rek_1', $rek3->kd_rek_1);
            $data->addChild('kd_rek_2', $rek3->kd_rek_2);
            $data->addChild('kd_rek_3', $rek3->kd_rek_3);
            $data->addChild('nama_kd_rek_3', $rek3->nama_kd_rek_3);
            $data->addChild('saldo_normal', $rek3->saldo_normal);
            
        }
        $tabel3 = $xml->addChild('tabel');
        $tabel3->addAttribute('tabel', 'ref_rek_4');
        foreach ($rek4 as $rek4) {
            $data = $tabel3->addChild('data');
            $data->addChild('kd_rek_1', $rek4->kd_rek_1);
            $data->addChild('kd_rek_2', $rek4->kd_rek_2);
            $data->addChild('kd_rek_3', $rek4->kd_rek_3);
            $data->addChild('kd_rek_4', $rek4->kd_rek_4);
            $data->addChild('nama_kd_rek_4', $rek4->nama_kd_rek_4);
            
        }
        $tabel4 = $xml->addChild('tabel');
        $tabel4->addAttribute('tabel', 'ref_rek_5');
        foreach ($rek5 as $rek5) {
            $data = $tabel4->addChild('data');
            $data->addChild('kd_rek_1', $rek5->kd_rek_1);
            $data->addChild('kd_rek_2', $rek5->kd_rek_2);
            $data->addChild('kd_rek_3', $rek5->kd_rek_3);
            $data->addChild('kd_rek_4', $rek5->kd_rek_4);
            $data->addChild('kd_rek_5', $rek5->kd_rek_5);
            $data->addChild('nama_kd_rek_5', $rek5->nama_kd_rek_5);
            $data->addChild('peraturan', $rek5->peraturan);
           
            
            
        }
        $response = Response::make($xml->asXML(), 200);
        $response->header('Cache-Control', 'public');
        $response->header('Content-Description', 'File Transfer');
        $response->header('Content-Disposition', 'attachment; filename=' . TIME() . '.xml');
        $response->header('Content-Transfer-Encoding', 'binary');
        $response->header('Content-Type', 'text/xml');
        
        return $response;
    }
    
    public function getUnitx(Request $request)
    {
        $unit = DB::SELECT('SELECT a.id_unit, a.id_bidang, b.kd_urusan, b.kd_bidang, a.kd_unit, a.nm_unit
        FROM ref_unit a INNER JOIN ref_bidang b ON a.id_bidang = b.id_bidang ');
        
        return response()->json($unit);
    }

    public function getRenstra(Request $request)
    {
        $TaSub = DB::SELECT('SELECT DISTINCT YEAR(CURDATE()) as Tahun, f.kd_urusan, e.kd_bidang, d.kd_unit, c.kd_sub, 
 COALESCE(b.nama_pimpinan_skpd,"-") as nm_pimpinan, COALESCE(b.nip_pimpinan_skpd,"-") as nip_pimpinan, 
 COALESCE(b.nama_jabatan_pimpinan_skpd,"-") as jbt_pimpinan, COALESCE(b.alamat_sub_unit,"-") as Alamat, 
 case when COALESCE(g.uraian_visi_renstra,"-") = "" then "-" else COALESCE(g.uraian_visi_renstra,"-") end as Ur_Visi 
 FROM ref_sub_unit c LEFT OUTER JOIN ref_data_sub_unit b on b.id_sub_unit=c.id_sub_unit 
 INNER JOIN ref_unit d on d.id_unit=c.id_unit LEFT OUTER JOIN trx_renstra_visi g ON g.id_unit = d.id_unit 
 INNER JOIN ref_bidang e on e.id_bidang=d.id_bidang inner join ref_urusan f on f.kd_urusan=e.kd_urusan 
            
   ');
        $TaMisi = DB::SELECT('SELECT distinct YEAR(CURDATE()) as Tahun, k.kd_urusan, j.kd_bidang, i.kd_unit, h.kd_sub, x.no_urut as no_misi, 
XML_Encode(GantiEnter(x.uraian_misi_renstra)) as ur_misi
 from trx_renja_rancangan a INNER JOIN trx_renja_rancangan_pelaksana g on a.id_renja=g.id_renja 
 INNER JOIN ref_sub_unit h on g.id_sub_unit=h.id_sub_unit 
 INNER JOIN ref_unit i on h.id_unit=i.id_unit 
 INNER JOIN ref_bidang j on i.id_bidang=j.id_bidang 
 INNER JOIN ref_urusan k ON j.kd_urusan = k.kd_urusan 
 INNER JOIN trx_renstra_misi x on a.id_misi_renstra = x.id_misi_renstra 
 INNER JOIN trx_renstra_tujuan y on a.id_tujuan_renstra = y.id_tujuan_renstra 
 WHERE NOT x.no_urut IN (98,99) 
ORDER BY Tahun, k.kd_urusan, j.kd_bidang, i.kd_unit, h.kd_sub, x.no_urut
            
   ');
        $TaTujuan = DB::SELECT('SELECT DISTINCT YEAR(CURDATE()) as Tahun, k.kd_urusan, j.kd_bidang, i.kd_unit, 
 h.kd_sub, x.no_urut as no_misi, y.no_urut as no_tujuan, y.uraian_tujuan_renstra as ur_tujuan 
 from trx_renja_rancangan a INNER JOIN trx_renja_rancangan_pelaksana g on a.id_renja=g.id_renja 
 INNER JOIN ref_sub_unit h on g.id_sub_unit=h.id_sub_unit 
 INNER JOIN ref_unit i on h.id_unit=i.id_unit 
 INNER JOIN ref_bidang j on i.id_bidang=j.id_bidang 
 INNER JOIN ref_urusan k ON j.kd_urusan = k.kd_urusan 
 INNER JOIN trx_renstra_misi x on a.id_misi_renstra = x.id_misi_renstra 
 INNER JOIN trx_renstra_tujuan y on a.id_tujuan_renstra = y.id_tujuan_renstra 
where a.tahun_renja = 2019 and x.no_urut not in (98,99) 
   ');
        $TaSasaran = DB::SELECT('SELECT DISTINCT YEAR(CURDATE()) as Tahun, k.kd_urusan, j.kd_bidang, i.kd_unit, h.kd_sub, 
 x.no_urut as no_misi, y.no_urut as no_tujuan, z.no_urut as no_sasaran, 
 XML_Encode(z.uraian_sasaran_renstra) as ur_sasaran from trx_renja_rancangan a 
 INNER JOIN trx_renja_rancangan_pelaksana g on a.id_renja=g.id_renja 
 INNER JOIN ref_sub_unit h on g.id_sub_unit=h.id_sub_unit 
 INNER JOIN ref_unit i on h.id_unit=i.id_unit 
 INNER JOIN ref_bidang j on i.id_bidang=j.id_bidang 
 INNER JOIN ref_urusan k ON j.kd_urusan = k.kd_urusan 
 INNER JOIN trx_renstra_misi x on a.id_misi_renstra = x.id_misi_renstra 
 INNER JOIN trx_renstra_tujuan y on a.id_tujuan_renstra = y.id_tujuan_renstra 
 INNER JOIN trx_renstra_sasaran z on a.id_sasaran_renstra = z.id_sasaran_renstra 
 WHERE a.tahun_renja = 2019 and x.no_urut not in (98,99) 
   ');
        $TaProgram = DB::SELECT('SELECT
	a.tahun,
	a.Kd_Urusan,
	a.Kd_Bidang,
	a.kd_unit,
	a.kd_sub,
	a.Kd_Prog,
	a.id_prog,
	CASE
WHEN a.Kd_Prog = 0 THEN
	"Non Program"
ELSE
	min(a.ket_program)
END AS ket_program,
 COALESCE (
	min(XML_Encode(a.Tolak_Ukur)),
	"-"
) AS Tolak_Ukur,
 COALESCE (max(a.Target_Angka), 0) AS Target_Angka,
 COALESCE (min(a.Target_Uraian), "-") AS Target_Uraian,
 a.Kd_Urusan1,
 a.Kd_Bidang1,
 SUM(
	COALESCE (a.jml_belanja_forum, 0)
) AS jml_belanja_forum
FROM
	(
		SELECT
			a.tahun_anggaran AS tahun,
			k.Kd_Urusan,
			j.Kd_Bidang,
			i.kd_unit,
			h.kd_sub,
			f.Kd_Prog,
			f.id_prog,
			f.Kd_Keg,
			l.kd_rek_1,
			l.kd_rek_2,
			l.kd_rek_3,
			l.kd_rek_4,
			l.kd_rek_5,
			a.id_belanja_pd as id_belanja_renja,
			a.id_aktivitas_pd,
			a.id_zona_ssh,
			
			a.sumber_belanja as sumber_belanja,
			concat_WS(
				" ",
				XML_Encode(GantiEnter(b.uraian_aktivitas_kegiatan)),
				ab.uraian_lokasi
			) AS AKTIVITAS_LOKASI,
			a.id_aktivitas_asb,
			
			a.id_rekening_ssh,
			l.nama_kd_rek_5,
			a.uraian_belanja,
			a.volume_1 as volume_1_forum,
			a.id_satuan_1 as id_satuan_1_forum,
			a.volume_2 as volume_2_forum,
			a.id_satuan_2 as id_satuan_2_forum,
			a.harga_satuan,
			
			
			
			a.harga_satuan as harga_satuan_forum,
			COALESCE (a.jml_belanja, 0) AS jml_belanja_forum,
			a.status_data,
			
			f.Kd_Urusan AS Kd_Urusan1,
			f.Kd_Bidang AS Kd_Bidang1,
			g.uraian_program_renstra AS ket_program,
			gx.tolok_ukur_indikator AS Tolak_Ukur,
			gx.target_renja AS Target_Angka,
			xx.uraian_satuan AS Target_Uraian,
			COALESCE (e.pagu_tahun_kegiatan) AS Pagu_Anggaran
		FROM
			trx_anggaran_belanja_pd AS a
INNER JOIN trx_anggaran_aktivitas_pd AS b ON a.id_aktivitas_pd = b.id_aktivitas_pd
		LEFT OUTER JOIN (
			SELECT
				id_aktivitas_pd,
				GROUP_CONCAT(" ", uraian_lokasi) AS uraian_lokasi
			FROM
				trx_anggaran_lokasi_pd
			GROUP BY
				id_aktivitas_pd
		) AS ab ON ab.id_aktivitas_pd = b.id_aktivitas_pd
		INNER JOIN trx_anggaran_pelaksana_pd AS d ON b.id_pelaksana_pd = d.id_pelaksana_pd
		INNER JOIN trx_anggaran_kegiatan_pd AS e ON d.id_kegiatan_pd = e.id_kegiatan_pd
		INNER JOIN (
			SELECT
				d.Kd_Urusan,
				c.Kd_Bidang,
				b.Kd_Program AS kd_prog,
				a.kd_kegiatan AS kd_keg,
				a.id_kegiatan,
				a.id_program,
				a.nm_kegiatan,
				CASE
			WHEN b.kd_program = 0 THEN
				0
			ELSE
				CONCAT(
					d.kd_urusan,
					RIGHT (CONCAT(0, c.kd_bidang), 2)
				)
			END AS id_prog
			FROM
				ref_kegiatan a
			INNER JOIN ref_program b ON a.id_program = b.id_program
			INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang
			INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan
		) AS f ON e.id_kegiatan_ref = f.id_kegiatan
		INNER JOIN trx_anggaran_program_pd AS g ON e.id_program_pd = g.id_program_pd
		LEFT JOIN trx_anggaran_prog_indikator_pd AS gx ON gx.id_program_pd = g.id_program_pd
		LEFT JOIN ref_satuan xx ON gx.id_satuan_output = xx.id_satuan
		INNER JOIN ref_sub_unit h ON d.id_sub_unit = h.id_sub_unit
		INNER JOIN ref_unit i ON h.id_unit = i.id_unit
		INNER JOIN ref_bidang j ON i.id_bidang = j.id_bidang
		INNER JOIN ref_urusan k ON j.kd_urusan = k.kd_urusan
		 INNER JOIN ref_rek_5 l ON l.id_rekening = a.id_rekening_ssh
		WHERE
			a.tahun_anggaran = 2019
		 AND g.jenis_belanja = 0 -- and k.Kd_Urusan like "kode_ur" and j.Kd_Bidang like "kode_bid" and i.Kd_Unit like "kode_unit" and h.Kd_Sub like "kode_sub" 
	) a
WHERE
	a.Kd_Prog > 0
AND a.id_prog > 0
GROUP BY
	a.tahun,
	a.Kd_Urusan,
	a.Kd_Bidang,
	a.kd_unit,
	a.kd_sub,
	a.Kd_Prog,
	a.id_prog,
	a.Kd_Urusan1,
	a.Kd_Bidang1 
 
   ');
        $TaKegiatan = DB::SELECT(' SELECT
	a.tahun,
	a.Kd_Urusan,
	a.Kd_Bidang,
	a.kd_unit,
	a.kd_sub,
	a.Kd_Prog,
	a.id_prog,
	a.Kd_Keg,
	CASE
WHEN a.Kd_Keg = 0 THEN
	"Non Kegiatan"
ELSE
	XML_Encode(a.nm_kegiatan)
END AS ket_kegiatan,
 ifnull(a.uraian_lokasi, "-") AS Lokasi,
 "-" AS Kelompok_Sasaran,
 1 AS Status_Kegiatan,
 COALESCE (a.Pagu_Anggaran) AS Pagu_Anggaran,
 NULL AS Waktu_Pelaksanaan,
 NULL AS Kd_Sumber
FROM
	(
		SELECT
			a.tahun_anggaran AS tahun,
			k.Kd_Urusan,
			j.Kd_Bidang,
			i.kd_unit,
			h.kd_sub,
			f.Kd_Prog,
			f.id_prog,
			f.Kd_Keg,
			f.nm_kegiatan,
			l.kd_rek_1,
			l.kd_rek_2,
			l.kd_rek_3,
			l.kd_rek_4,
			l.kd_rek_5,
			a.id_belanja_pd,
			a.id_aktivitas_pd,
			a.id_zona_ssh,
			a.id_belanja_pd as id_belanja_renja,
			a.sumber_belanja,
			ab.uraian_lokasi,
			concat_WS(
				" ",
				XML_Encode(GantiEnter(b.uraian_aktivitas_kegiatan)),
				ab.uraian_lokasi
			) AS AKTIVITAS_LOKASI,
			a.id_aktivitas_asb,
			a.id_item_ssh,
			a.id_rekening_ssh,
			l.nama_kd_rek_5,
			a.uraian_belanja,
			a.volume_1,
			a.id_satuan_1,
			a.volume_2,
			a.id_satuan_2,
			a.harga_satuan,
			a.jml_belanja,
			a.volume_1 as volume_1_forum,
			a.id_satuan_1 as id_satuan_1_forum,
			a.volume_2 as volume_2_forum,
			a.id_satuan_2 as id_satuan_2_forum,
			a.harga_satuan as harga_satuan_forum,
			COALESCE (a.jml_belanja, 0) AS jml_belanja_forum,
			a.status_data,
			a.sumber_data,
			f.Kd_Urusan AS Kd_Urusan1,
			f.Kd_Bidang AS Kd_Bidang1,
			g.uraian_program_renstra AS ket_program,
			gx.tolok_ukur_indikator AS Tolak_Ukur,
			gx.target_renja AS Target_Angka,
			xx.uraian_satuan AS Target_Uraian,
			b.jenis_kegiatan,
			COALESCE (e.pagu_tahun_kegiatan, 0) AS pagu_tahun_kegiatan,
			b.sumber_dana,
			COALESCE (e.pagu_forum) AS Pagu_Anggaran
		FROM
			trx_anggaran_belanja_pd AS a
		INNER JOIN trx_anggaran_aktivitas_pd AS b ON a.id_aktivitas_pd = b.id_aktivitas_pd
		LEFT OUTER JOIN (
			SELECT
				id_aktivitas_pd,
				GROUP_CONCAT(" ", uraian_lokasi) AS uraian_lokasi
			FROM
				trx_anggaran_lokasi_pd
			GROUP BY
				id_aktivitas_pd
		) AS ab ON ab.id_aktivitas_pd = b.id_aktivitas_pd
		INNER JOIN trx_anggaran_pelaksana_pd AS d ON b.id_pelaksana_pd = d.id_pelaksana_pd
		INNER JOIN trx_anggaran_kegiatan_pd AS e ON d.id_kegiatan_pd = e.id_kegiatan_pd
		INNER JOIN (
			SELECT
				d.Kd_Urusan,
				c.Kd_Bidang,
				b.Kd_Program AS kd_prog,
				a.kd_kegiatan AS kd_keg,
				a.id_kegiatan,
				a.id_program,
				a.nm_kegiatan,
				CASE
			WHEN b.kd_program = 0 THEN
				0
			ELSE
				CONCAT(
					d.kd_urusan,
					RIGHT (CONCAT(0, c.kd_bidang), 2)
				)
			END AS id_prog
			FROM
				ref_kegiatan a
			INNER JOIN ref_program b ON a.id_program = b.id_program
			INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang
			INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan
		) AS f ON e.id_kegiatan_ref = f.id_kegiatan
		INNER JOIN trx_anggaran_program_pd AS g ON e.id_program_pd = g.id_program_pd
		LEFT JOIN trx_anggaran_prog_indikator_pd AS gx ON gx.id_program_pd = g.id_program_pd
		LEFT JOIN ref_satuan xx ON gx.id_satuan_output = xx.id_satuan
		INNER JOIN ref_sub_unit h ON d.id_sub_unit = h.id_sub_unit
		INNER JOIN ref_unit i ON h.id_unit = i.id_unit
		INNER JOIN ref_bidang j ON i.id_bidang = j.id_bidang
		INNER JOIN ref_urusan k ON j.kd_urusan = k.kd_urusan
		INNER JOIN ref_rek_5 l ON l.id_rekening = a.id_rekening_ssh
		WHERE
			a.tahun_anggaran = 2019
		AND g.jenis_belanja = 0 -- and k.Kd_Urusan like "kode_ur" and j.Kd_Bidang like "kode_bid" and i.Kd_Unit like "kode_unit" and h.Kd_Sub like "kode_sub" 
	) a
GROUP BY
	a.tahun,
	a.Kd_Urusan,
	a.Kd_Bidang,
	a.kd_unit,
	a.kd_sub,
	a.Kd_Prog,
	a.id_prog,
	a.kd_keg,
    a.nm_kegiatan,
    a.uraian_lokasi,
    a.Pagu_Anggaran
UNION
	SELECT
		a.tahun,
		a.Kd_Urusan,
		a.Kd_Bidang,
		a.kd_unit,
		a.kd_sub,
		a.Kd_Prog,
		a.id_prog,
		a.Kd_Keg,
		CASE
	WHEN a.Kd_Keg = 0 THEN
		"Non Kegiatan"
	ELSE
		a.nm_kegiatan
	END AS ket_kegiatan,
	ifnull(a.uraian_lokasi, "-") AS Lokasi,
	"-" AS Kelompok_Sasaran,
	a.jenis_kegiatan + 1 AS Status_Kegiatan,
	COALESCE (a.Pagu_Anggaran) AS Pagu_Anggaran,
	NULL AS Waktu_Pelaksanaan,
	"NULL" AS Kd_Sumber
FROM
	(
		SELECT
			a.tahun_anggaran AS tahun,
			k.Kd_Urusan,
			j.Kd_Bidang,
			i.kd_unit,
			h.kd_sub,
			f.Kd_Prog,
			f.id_prog,
			f.Kd_Keg,
			f.nm_kegiatan,
			l.kd_rek_1,
			l.kd_rek_2,
			l.kd_rek_3,
			l.kd_rek_4,
			l.kd_rek_5,
			a.id_belanja_pd,
			a.id_aktivitas_pd,
			a.id_zona_ssh,
			a.id_belanja_pd as id_belanja_renja,
			a.sumber_belanja,
			ab.uraian_lokasi,
			concat_WS(
				" ",
				XML_Encode(GantiEnter(b.uraian_aktivitas_kegiatan)),
				ab.uraian_lokasi
			) AS AKTIVITAS_LOKASI,
			a.id_aktivitas_asb,
			a.id_item_ssh,
			a.id_rekening_ssh,
			l.nama_kd_rek_5,
			a.uraian_belanja,
			a.volume_1,
			a.id_satuan_1,
			a.volume_2,
			a.id_satuan_2,
			a.harga_satuan,
			a.jml_belanja,
			a.volume_1 as volume_1_forum,
			a.id_satuan_1 as id_satuan_1_forum,
			a.volume_2 as volume_2_forum,
			a.id_satuan_2 as id_satuan_2_forum,
			a.harga_satuan as harga_satuan_forum,
			COALESCE (a.jml_belanja, 0) AS jml_belanja_forum,
			a.status_data,
			a.sumber_data,
			f.Kd_Urusan AS Kd_Urusan1,
			f.Kd_Bidang AS Kd_Bidang1,
			g.uraian_program_renstra AS ket_program,
			gx.tolok_ukur_indikator AS Tolak_Ukur,
			gx.target_renja AS Target_Angka,
			xx.uraian_satuan AS Target_Uraian,
			b.jenis_kegiatan,
			COALESCE (e.pagu_tahun_kegiatan, 0) AS pagu_tahun_kegiatan,
			b.sumber_dana,
			COALESCE (e.pagu_forum) AS Pagu_Anggaran
		FROM
			trx_anggaran_belanja_pd AS a
		INNER JOIN trx_anggaran_aktivitas_pd AS b ON a.id_aktivitas_pd = b.id_aktivitas_pd
		LEFT OUTER JOIN (
			SELECT
				id_aktivitas_pd,
				GROUP_CONCAT(" ", uraian_lokasi) AS uraian_lokasi
			FROM
				trx_anggaran_lokasi_pd
			GROUP BY
				id_aktivitas_pd
		) AS ab ON ab.id_aktivitas_pd = b.id_aktivitas_pd
		INNER JOIN trx_anggaran_pelaksana_pd AS d ON b.id_pelaksana_pd = d.id_pelaksana_pd
		INNER JOIN trx_anggaran_kegiatan_pd AS e ON d.id_kegiatan_pd = e.id_kegiatan_pd
		INNER JOIN (
			SELECT
				d.Kd_Urusan,
				c.Kd_Bidang,
				b.Kd_Program AS kd_prog,
				a.kd_kegiatan AS kd_keg,
				a.id_kegiatan,
				a.id_program,
				a.nm_kegiatan,
				CASE
			WHEN b.kd_program = 0 THEN
				0
			ELSE
				CONCAT(
					d.kd_urusan,
					RIGHT (CONCAT(0, c.kd_bidang), 2)
				)
			END AS id_prog
			FROM
				ref_kegiatan a
			INNER JOIN ref_program b ON a.id_program = b.id_program
			INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang
			INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan
		) AS f ON e.id_kegiatan_ref = f.id_kegiatan
		INNER JOIN trx_anggaran_program_pd AS g ON e.id_program_pd = g.id_program_pd
		LEFT JOIN trx_anggaran_prog_indikator_pd AS gx ON gx.id_program_pd = g.id_program_pd
		LEFT JOIN ref_satuan xx ON gx.id_satuan_output = xx.id_satuan
		INNER JOIN ref_sub_unit h ON d.id_sub_unit = h.id_sub_unit
		INNER JOIN ref_unit i ON h.id_unit = i.id_unit
		INNER JOIN ref_bidang j ON i.id_bidang = j.id_bidang
		INNER JOIN ref_urusan k ON j.kd_urusan = k.kd_urusan
		INNER JOIN ref_rek_5 l ON l.id_rekening = a.id_rekening_ssh
		WHERE
			a.tahun_anggaran = 2019
		AND g.jenis_belanja IN (1, 2) -- // jenis_belanja    0=BL, 1=pdpt, 2=BTL         
		-- and k.Kd_Urusan like "kode_ur" and j.Kd_Bidang like "kode_bid" and i.Kd_Unit like "kode_unit" and h.Kd_Sub like "kode_sub" 
	) a
WHERE
	a.Kd_Prog > 0
AND a.id_prog > 0
AND a.Kd_Keg > 0
GROUP BY
	a.tahun,
	a.Kd_Urusan,
	a.Kd_Bidang,
	a.kd_unit,
	a.kd_sub,
	a.Kd_Prog,
	a.id_prog,
	a.kd_keg,
    a.nm_kegiatan,
    a.uraian_lokasi,
    a.Pagu_Anggaran,
    a.jenis_kegiatan
   ');
          $indikator=DB::select(' SELECT
	@row_num :=
IF (
	@prev_value = a.id_kegiatan ,@row_num + 1,
	1
) AS No_ID,
 @prev_value := a.id_kegiatan AS id_kegiatanq,
 a.*
FROM
	(
		SELECT
			a.id_forum_skpd,
			a.tahun,
			a.Kd_Urusan,
			a.Kd_Bidang,
			a.kd_unit,
			a.kd_sub,
			a.Kd_Prog,
			a.id_prog,
			a.Kd_Keg,
			3 AS Kd_Indikator,
			XML_Encode (a.tolok_ukur_indikator) AS Tolak_Ukur,
			a.target_renja AS Target_Angka,
			COALESCE (a.uraian_satuan, "-") AS Target_Uraian,
			a.uraian_kegiatan_forum,
			a.id_indikator_kegiatan,
			a.no_urut,
			a.id_kegiatan
		FROM
			(
				SELECT
					ik.id_kegiatan_pd as id_forum_skpd,
					ik.id_indikator_kegiatan,
					ik.kd_indikator,
					ik.uraian_indikator_kegiatan,
					SUBSTRING(
						ik.tolok_ukur_indikator,
						1,
						255
					) AS tolok_ukur_indikator,
					ik.target_renja,
					ik.indikator_output,
					ik.id_satuan_output,
					ik.indikator_input,
					ik.target_input,
					ik.id_satuan_input,
					rs.uraian_satuan,
					b.tahun_anggaran AS tahun,
					k.Kd_Urusan,
					j.Kd_Bidang,
					i.kd_unit,
					h.kd_sub,
					f.Kd_Prog,
					f.id_prog,
					f.Kd_Keg,
					f.nm_kegiatan,
					e.uraian_kegiatan_forum,
					ik.no_urut,
					f.id_kegiatan
				FROM
			trx_anggaran_belanja_pd AS a
		INNER JOIN trx_anggaran_aktivitas_pd AS b ON a.id_aktivitas_pd = b.id_aktivitas_pd
				LEFT OUTER JOIN (
			SELECT
				id_aktivitas_pd,
				GROUP_CONCAT(" ", uraian_lokasi) AS uraian_lokasi
			FROM
				trx_anggaran_lokasi_pd
			GROUP BY
				id_aktivitas_pd
		) AS ab ON ab.id_aktivitas_pd = b.id_aktivitas_pd
INNER JOIN trx_anggaran_pelaksana_pd AS d ON b.id_pelaksana_pd = d.id_pelaksana_pd
		INNER JOIN trx_anggaran_kegiatan_pd AS e ON d.id_kegiatan_pd = e.id_kegiatan_pd
				INNER JOIN trx_anggaran_keg_indikator_pd AS ik ON ik.id_kegiatan_pd = e.id_kegiatan_pd
				LEFT JOIN ref_satuan rs ON ik.id_satuan_output = rs.id_satuan
				INNER JOIN (
					SELECT
						d.Kd_Urusan,
						c.Kd_Bidang,
						b.Kd_Program AS kd_prog,
						a.kd_kegiatan AS kd_keg,
						a.id_kegiatan,
						a.id_program,
						a.nm_kegiatan,
						CASE
					WHEN b.kd_program = 0 THEN
						0
					ELSE
						CONCAT(
							d.kd_urusan,
							RIGHT (CONCAT(0, c.kd_bidang), 2)
						)
					END AS id_prog
					FROM
						ref_kegiatan a
					INNER JOIN ref_program b ON a.id_program = b.id_program
					INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang
					INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan
				) AS f ON e.id_kegiatan_ref = f.id_kegiatan
			INNER JOIN trx_anggaran_program_pd AS g ON e.id_program_pd = g.id_program_pd
		LEFT JOIN trx_anggaran_prog_indikator_pd AS gx ON gx.id_program_pd = g.id_program_pd
				INNER JOIN ref_sub_unit h ON d.id_sub_unit = h.id_sub_unit
				INNER JOIN ref_unit i ON h.id_unit = i.id_unit
				INNER JOIN ref_bidang j ON i.id_bidang = j.id_bidang
				INNER JOIN ref_urusan k ON j.kd_urusan = k.kd_urusan
				WHERE
					b.tahun_anggaran = 2019
				AND g.jenis_belanja = 0
				AND b.status_pelaksanaan = 0
				AND e.status_pelaksanaan = 0 -- // jenis_belanja    0=BL, 1=pdpt, 2=BTL
				-- and k.Kd_Urusan like "kode_ur" and j.Kd_Bidang like "kode_bid" and i.Kd_Unit like "kode_unit" and h.Kd_Sub like "kode_sub" 
			) a
		GROUP BY
			a.tahun,
			a.Kd_Urusan,
			a.Kd_Bidang,
			a.kd_unit,
			a.kd_sub,
			a.Kd_Prog,
			a.id_prog,
			a.kd_keg,
			a.id_forum_skpd,
			a.id_indikator_kegiatan
		ORDER BY
			a.id_kegiatan,
			a.no_urut
	) a,
	(
		SELECT
			@row_num := 0,
			@prev_value := 0
	) yy ');
        $indikatorhasil=DB::select('SELECT
	@row_num :=
IF (
	@prev_value = a.id_kegiatan ,@row_num + 1,
	1
) AS No_ID,
 @prev_value := a.id_kegiatan AS id_kegiatanq,
 a.*
FROM
	(
		SELECT
			a.id_forum_skpd,
			a.tahun,
			a.Kd_Urusan,
			a.Kd_Bidang,
			a.kd_unit,
			a.kd_sub,
			a.Kd_Prog,
			a.id_prog,
			a.Kd_Keg,
			4 AS Kd_Indikator,
			XML_Encode (a.Tolak_Ukur) AS Tolak_Ukur,
			a.Target_Angka,
			/*COALESCE(a.uraian_indikator_sasaran_renstra,"-")*/
			" " AS Target_Uraian,
			a.id_indikator_sasaran_renstra AS id_indikator_program,
			a.no_urut,
			a.id_kegiatan
		FROM
			(
				SELECT
					b.tahun_anggaran AS tahun,
					b.id_pelaksana_pd as id_forum_skpd,
					k.Kd_Urusan,
					j.Kd_Bidang,
					i.kd_unit,
					h.kd_sub,
					f.Kd_Prog,
					f.id_prog,
					f.Kd_Keg,
					gd.no_urut,
					gd.tolok_ukur_indikator AS Tolak_Ukur,
					CASE gd.thn_id
				WHEN 1 THEN
					angka_tahun1
				WHEN 2 THEN
					angka_tahun2
				WHEN 3 THEN
					angka_tahun3
				WHEN 4 THEN
					angka_tahun4
				WHEN 5 THEN
					angka_tahun5
				END AS Target_Angka,
				gd.uraian_indikator_sasaran_renstra,
				gd.id_indikator_sasaran_renstra,
				f.id_kegiatan
			FROM
				trx_anggaran_belanja_pd AS a
		INNER JOIN trx_anggaran_aktivitas_pd AS b ON a.id_aktivitas_pd = b.id_aktivitas_pd
				LEFT OUTER JOIN (
			SELECT
				id_aktivitas_pd,
				GROUP_CONCAT(" ", uraian_lokasi) AS uraian_lokasi
			FROM
				trx_anggaran_lokasi_pd
			GROUP BY
				id_aktivitas_pd
		) AS ab ON ab.id_aktivitas_pd = b.id_aktivitas_pd
INNER JOIN trx_anggaran_pelaksana_pd AS d ON b.id_pelaksana_pd = d.id_pelaksana_pd
		INNER JOIN trx_anggaran_kegiatan_pd AS e ON d.id_kegiatan_pd = e.id_kegiatan_pd
				INNER JOIN trx_anggaran_keg_indikator_pd AS ik ON ik.id_kegiatan_pd = e.id_kegiatan_pd
			INNER JOIN (
				SELECT
					d.Kd_Urusan,
					c.Kd_Bidang,
					b.Kd_Program AS kd_prog,
					a.kd_kegiatan AS kd_keg,
					a.id_kegiatan,
					a.id_program,
					a.nm_kegiatan,
					CASE
				WHEN b.kd_program = 0 THEN
					0
				ELSE
					CONCAT(
						d.kd_urusan,
						RIGHT (CONCAT(0, c.kd_bidang), 2)
					)
				END AS id_prog
				FROM
					ref_kegiatan a
				INNER JOIN ref_program b ON a.id_program = b.id_program
				INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang
				INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan
			) AS f ON e.id_kegiatan_ref = f.id_kegiatan
			INNER JOIN trx_anggaran_program_pd AS g ON e.id_program_pd = g.id_program_pd
		
			LEFT JOIN trx_renstra_program AS gb ON g.id_program_renstra = gb.id_program_renstra
			INNER JOIN trx_renstra_sasaran AS gc ON gb.id_sasaran_renstra = gc.id_sasaran_renstra
			INNER JOIN trx_renstra_sasaran_indikator AS gd ON gc.id_sasaran_renstra = gd.id_sasaran_renstra
			INNER JOIN ref_sub_unit h ON d.id_sub_unit = h.id_sub_unit
			INNER JOIN ref_unit i ON h.id_unit = i.id_unit
			INNER JOIN ref_bidang j ON i.id_bidang = j.id_bidang
			INNER JOIN ref_urusan k ON j.kd_urusan = k.kd_urusan
			WHERE
				b.tahun_anggaran = 2019
			AND g.jenis_belanja = 0
			AND f.Kd_Prog <> 0
			AND b.status_pelaksanaan = 0
			AND e.status_pelaksanaan = 0 -- and k.Kd_Urusan like "kode_ur" and j.Kd_Bidang like "kode_bid" and i.Kd_Unit like "kode_unit" and h.Kd_Sub like "kode_sub" 
			) a
		GROUP BY
			a.tahun,
			a.Kd_Urusan,
			a.Kd_Bidang,
			a.kd_unit,
			a.kd_sub,
			a.Kd_Prog,
			a.id_prog,
			a.kd_keg,
			a.id_forum_skpd,
			a.id_indikator_sasaran_renstra
		ORDER BY
			a.id_kegiatan,
			a.no_urut
	) a,
	(
		SELECT
			@row_num := 0,
			@prev_value := 0
	) yy
');
        $capaianprogram=DB::select('SELECT
	@row_num :=
IF (
	@prev_value = a.id_programq ,@row_num + 1,
	1
) AS No_ID,
 @prev_value := a.id_programq AS id_programq,
 a.*
FROM
	(
		SELECT
			a.id_forum_skpd,
			a.tahun,
			a.Kd_Urusan,
			a.Kd_Bidang,
			a.kd_unit,
			a.kd_sub,
			a.Kd_Prog,
			a.id_prog,
			XML_Encode (a.Tolak_Ukur) AS Tolak_Ukur,
			a.Target_Angka,
			COALESCE (a.Target_Uraian, "-") AS Target_Uraian,
			a.id_indikator_program,
			a.no_urut,
			concat(
				a.id_sub_unit,
				".",
				a.id_program
			) AS id_programq
		FROM
			(
				SELECT
					g.id_program_pd as id_forum_program,
					h.id_sub_unit,
					b.tahun_anggaran AS tahun,
					b.id_pelaksana_pd as id_forum_skpd,
					k.Kd_Urusan,
					j.Kd_Bidang,
					i.kd_unit,
					h.kd_sub,
					f.Kd_Prog,
					f.id_prog,
					gx.no_urut,
					gx.tolok_ukur_indikator AS Tolak_Ukur,
					gx.target_renja AS Target_Angka,
					rs.uraian_satuan AS Target_Uraian,
					gx.id_indikator_program,
					f.id_program
				FROM
					trx_anggaran_belanja_pd AS a
		INNER JOIN trx_anggaran_aktivitas_pd AS b ON a.id_aktivitas_pd = b.id_aktivitas_pd
				LEFT OUTER JOIN (
			SELECT
				id_aktivitas_pd,
				GROUP_CONCAT(" ", uraian_lokasi) AS uraian_lokasi
			FROM
				trx_anggaran_lokasi_pd
			GROUP BY
				id_aktivitas_pd
		) AS ab ON ab.id_aktivitas_pd = b.id_aktivitas_pd
INNER JOIN trx_anggaran_pelaksana_pd AS d ON b.id_pelaksana_pd = d.id_pelaksana_pd
		INNER JOIN trx_anggaran_kegiatan_pd AS e ON d.id_kegiatan_pd = e.id_kegiatan_pd
				INNER JOIN trx_anggaran_keg_indikator_pd AS ik ON ik.id_kegiatan_pd = e.id_kegiatan_pd
			INNER JOIN (
				SELECT
					d.Kd_Urusan,
					c.Kd_Bidang,
					b.Kd_Program AS kd_prog,
					a.kd_kegiatan AS kd_keg,
					a.id_kegiatan,
					a.id_program,
					a.nm_kegiatan,
					CASE
				WHEN b.kd_program = 0 THEN
					0
				ELSE
					CONCAT(
						d.kd_urusan,
						RIGHT (CONCAT(0, c.kd_bidang), 2)
					)
				END AS id_prog
				FROM
					ref_kegiatan a
				INNER JOIN ref_program b ON a.id_program = b.id_program
				INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang
				INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan
			) AS f ON e.id_kegiatan_ref = f.id_kegiatan
			INNER JOIN trx_anggaran_program_pd AS g ON e.id_program_pd = g.id_program_pd

				LEFT JOIN trx_anggaran_prog_indikator_pd gx ON g.id_program_pd = gx.id_program_pd
				LEFT JOIN ref_satuan rs ON gx.id_satuan_output = rs.id_satuan
				INNER JOIN ref_sub_unit h ON d.id_sub_unit = h.id_sub_unit
				INNER JOIN ref_unit i ON h.id_unit = i.id_unit
				INNER JOIN ref_bidang j ON i.id_bidang = j.id_bidang
				INNER JOIN ref_urusan k ON j.kd_urusan = k.kd_urusan
				WHERE
					b.tahun_anggaran = 2019
				AND g.jenis_belanja = 0
				AND b.status_pelaksanaan = 0
				AND e.status_pelaksanaan = 0 --    // jenis_belanja=1 ==> pendapatan    0=BL, 1=pdpt, 2=BTL
				-- and k.Kd_Urusan like "kode_ur" and j.Kd_Bidang like "kode_bid" and i.Kd_Unit like "kode_unit" and h.Kd_Sub like "kode_sub" 
				ORDER BY
					k.Kd_Urusan,
					j.Kd_Bidang,
					i.kd_unit,
					h.kd_sub,
					f.Kd_Prog,
					f.id_prog
			) a
		GROUP BY
            a.id_forum_skpd,
			a.tahun,
			a.Kd_Urusan,
			a.Kd_Bidang,
			a.kd_unit,
			a.kd_sub,
			a.Kd_Prog,
			a.id_prog,
			a.id_indikator_program
		ORDER BY
			a.Kd_Urusan,
			a.Kd_Bidang,
			a.kd_unit,
			a.kd_sub,
			a.Kd_Prog,
			a.id_prog
	) a,
	(
		SELECT
			@row_num := 0,
			@prev_value := 0
	) yy
');
        //-- Where f.Kd_Urusan like ''kode_ur+''' and e.Kd_Bidang like ''kode_bid+''' and d.Kd_Unit like ''kode_unit+''' and c.Kd_Sub like ''kode_sub+'''
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><mydoc></mydoc>');
        
        $xml->addAttribute('Created', 'by Simd@');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $tabel = $xml->addChild('tabel');
        $tabel->addAttribute('tabel', 'Ta_Sub_Unit');
        foreach ($TaSub as $program) {
            $data = $tabel->addChild('data');
            $data->addChild('Tahun', $program->Tahun);
            $data->addChild('kd_urusan', $program->kd_urusan);
            $data->addChild('kd_bidang', $program->kd_bidang);
            $data->addChild('kd_unit', $program->kd_unit);
            $data->addChild('kd_sub', $program->kd_sub);
            $data->addChild('nm_pimpinan', $program->nm_pimpinan);
            $data->addChild('nip_pimpinan', $program->nip_pimpinan);
            $data->addChild('jbt_pimpinan', $program->jbt_pimpinan);
            $data->addChild('Alamat', $program->Alamat);
            $data->addChild('Ur_Visi', $program->Ur_Visi);
        }
        $tabel1 = $xml->addChild('tabel');
        $tabel1->addAttribute('tabel', 'Ta_Misi');
        foreach ($TaMisi as $program) {
            $data = $tabel1->addChild('data');
            $data->addChild('Tahun', $program->Tahun);
            $data->addChild('kd_urusan', $program->kd_urusan);
            $data->addChild('kd_bidang', $program->kd_bidang);
            $data->addChild('kd_unit', $program->kd_unit);
            $data->addChild('kd_sub', $program->kd_sub);
            $data->addChild('no_misi', $program->no_misi);
            $data->addChild('ur_misi', $program->ur_misi);
            
        }
        $tabel2 = $xml->addChild('tabel');
        $tabel2->addAttribute('tabel', 'Ta_Tujuan');
        foreach ($TaTujuan as $program) {
            $data = $tabel2->addChild('data');
            $data->addChild('Tahun', $program->Tahun);
            $data->addChild('kd_urusan', $program->kd_urusan);
            $data->addChild('kd_bidang', $program->kd_bidang);
            $data->addChild('kd_unit', $program->kd_unit);
            $data->addChild('kd_sub', $program->kd_sub);
            $data->addChild('no_misi', $program->no_misi);
            $data->addChild('no_tujuan', $program->no_tujuan);
            $data->addChild('ur_tujuan', $program->ur_tujuan);
            
        }
        $tabel3 = $xml->addChild('tabel');
        $tabel3->addAttribute('tabel', 'Ta_Sasaran');
        foreach ($TaSasaran as  $program) {
            $data = $tabel3->addChild('data');
            $data->addChild('Tahun', $program->Tahun);
            $data->addChild('kd_urusan', $program->kd_urusan);
            $data->addChild('kd_bidang', $program->kd_bidang);
            $data->addChild('kd_unit', $program->kd_unit);
            $data->addChild('kd_sub', $program->kd_sub);
            $data->addChild('no_misi', $program->no_misi);
            $data->addChild('no_tujuan', $program->no_tujuan);
            $data->addChild('no_sasaran', $program->no_sasaran);
            $data->addChild('ur_sasaran', $program->ur_sasaran);
            
        }
        $tabel4 = $xml->addChild('tabel');
        $tabel4->addAttribute('tabel', 'Ta_Program');
        foreach ($TaProgram as $program) {
            $data = $tabel4->addChild('data');
            $data->addChild('tahun', $program->tahun);
            $data->addChild('kd_urusan', $program->Kd_Urusan);
            $data->addChild('kd_bidang', $program->Kd_Bidang);
            $data->addChild('kd_unit', $program->kd_unit);
            $data->addChild('kd_sub', $program->kd_sub);
            $data->addChild('kd_prog', $program->kd_prog);
            $data->addChild('id_prog', $program->id_prog);
            $data->addChild('ket_program', $program->ket_program);
            $data->addChild('Tolak_Ukur', $program->Tolak_Ukur);
            $data->addChild('Target_Angka', $program->Target_Angka);
            $data->addChild('Target_Uraian', $program->Target_Uraian);
            $data->addChild('Kd_Urusan1', $program->Kd_Urusan1);
            $data->addChild('Kd_Bidang1', $program->Kd_Bidang1);
            $data->addChild('jml_belanja_forum', $program->jml_belanja_forum);
        }
        $tabel5 = $xml->addChild('tabel');
        $tabel5->addAttribute('tabel', 'Ta_Kegiatan');
        foreach ($TaKegiatan as $program) {
            $data = $tabel5->addChild('data');
            $data->addChild('tahun', $program->tahun);
            $data->addChild('Kd_Urusan', $program->Kd_Urusan);
            $data->addChild('Kd_Bidang', $program->Kd_Bidang);
            $data->addChild('kd_unit', $program->kd_unit);
            $data->addChild('kd_sub', $program->kd_sub);
            $data->addChild('kd_prog', $program->kd_prog);
            $data->addChild('id_prog', $program->id_prog);
            $data->addChild('kd_keg', $program->kd_keg);
            $data->addChild('ket_kegiatan', $program->ket_kegiatan);
            $data->addChild('Lokasi', $program->Lokasi);
            $data->addChild('Kelompok_Sasaran', $program->Kelompok_Sasaran);
            $data->addChild('Status_Kegiatan', $program->Status_Kegiatan);
            $data->addChild('Pagu_Anggaran', $program->Pagu_Anggaran);
            $data->addChild('Waktu_Pelaksanaan', $program->Waktu_Pelaksanaan);
            $data->addChild('Kd_Sumber', $program->Kd_Sumber);
            
        }
        $tabel6 = $xml->addChild('tabel');
        $tabel6->addAttribute('tabel', 'Ta_Indikator');
        foreach ($indikator as $row) {
            $data = $tabel6->addChild('data');
            $data->addChild('No_ID', $row->No_ID);
            $data->addChild('id_kegiatanq', $row->id_kegiatanq);
            $data->addChild('id_forum_skpd', $row->id_forum_skpd);
            $data->addChild('tahun', $row->tahun);
            $data->addChild('Kd_Urusan', $row->Kd_Urusan);
            $data->addChild('Kd_Bidang', $row->Kd_Bidang);
            $data->addChild('kd_unit', $row->kd_unit);
            $data->addChild('kd_sub', $row->kd_sub);
            $data->addChild('kd_prog', $row->Kd_Prog);
            $data->addChild('id_prog', $row->id_prog);
            $data->addChild('kd_keg', $row->Kd_Keg);
            $data->addChild('Kd_Indikator', $row->Kd_Indikator);
            $data->addChild('Tolak_Ukur', $row->Tolak_Ukur);
            $data->addChild('Target_Angka', $row->Target_Angka);
            $data->addChild('Target_Uraian', $row->Target_Uraian);
            $data->addChild('uraian_kegiatan_forum', $row->uraian_kegiatan_forum);
            $data->addChild('id_indikator_kegiatan', $row->id_indikator_kegiatan);
            $data->addChild('no_urut', $row->no_urut);
            $data->addChild('id_kegiatan', $row->id_kegiatan);
        }

        $tabel7 = $xml->addChild('tabel');
        $tabel7->addAttribute('tabel', 'Ta_Indikator_hasil');
        foreach ($indikatorhasil as $row) {
            $data = $tabel7->addChild('data');
            $data->addChild('No_ID', $row->No_ID);
            $data->addChild('id_kegiatanq', $row->id_kegiatanq);
            $data->addChild('id_forum_skpd', $row->id_forum_skpd);
            $data->addChild('tahun', $row->tahun);
            $data->addChild('Kd_Urusan', $row->Kd_Urusan);
            $data->addChild('Kd_Bidang', $row->Kd_Bidang);
            $data->addChild('kd_unit', $row->kd_unit);
            $data->addChild('kd_sub', $row->kd_sub);
            $data->addChild('kd_prog', $row->kd_prog);
            $data->addChild('id_prog', $row->id_prog);
            $data->addChild('kd_keg', $row->kd_keg);
            $data->addChild('Kd_Indikator', $row->Kd_Indikator);
            $data->addChild('Tolak_Ukur', $row->Tolak_Ukur);
            $data->addChild('Target_Angka', $row->Target_Angka);
            $data->addChild('Target_Uraian', $row->Target_Uraian);
            $data->addChild('id_indikator_program', $row->id_indikator_program);
            $data->addChild('no_urut', $row->no_urut);
            $data->addChild('id_kegiatan', $row->id_kegiatan);
            
            
            
        }
        $tabel8 = $xml->addChild('tabel');
        $tabel8->addAttribute('tabel', 'Ta_Capaian_Program');
        foreach ($capaianprogram as $row) {
            $data = $tabel8->addChild('data');
            $data->addChild('No_ID', $row->No_ID);
            $data->addChild('id_programq', $row->id_programq);
            $data->addChild('id_forum_skpd', $row->id_forum_skpd);
            $data->addChild('tahun', $row->tahun);
            $data->addChild('Kd_Urusan', $row->Kd_Urusan);
            $data->addChild('Kd_Bidang', $row->Kd_Bidang);
            $data->addChild('kd_unit', $row->kd_unit);
            $data->addChild('kd_sub', $row->kd_sub);
            $data->addChild('kd_prog', $row->Kd_Prog);
            $data->addChild('id_prog', $row->id_prog);
            $data->addChild('Tolak_Ukur', $row->Tolak_Ukur);
            $data->addChild('Target_Angka', $row->Target_Angka);
            $data->addChild('Target_Uraian', $row->Target_Uraian);
            $data->addChild('id_indikator_program', $row->id_indikator_program);
            $data->addChild('no_urut', $row->no_urut);
            
            
            
        }
        
        $response = Response::make($xml->asXML(), 200);
        $response->header('Cache-Control', 'public');
        $response->header('Content-Description', 'File Transfer');
        $response->header('Content-Disposition', 'attachment; filename=' . TIME() . '.xml');
        $response->header('Content-Transfer-Encoding', 'binary');
        $response->header('Content-Type', 'text/xml');
        
        return $response;
    }
    
    public function getTaSubUnit(Request $request)
    {
        $unit = DB::SELECT('SELECT DISTINCT YEAR(CURDATE()) as Tahun, f.kd_urusan, e.kd_bidang, d.kd_unit, c.kd_sub, 
 COALESCE(b.nama_pimpinan_skpd,"-") as nm_pimpinan, COALESCE(b.nip_pimpinan_skpd,"-") as nip_pimpinan, 
 COALESCE(b.nama_jabatan_pimpinan_skpd,"-") as jbt_pimpinan, COALESCE(b.alamat_sub_unit,"-") as Alamat, 
 case when COALESCE(g.uraian_visi_renstra,"-") = "" then "-" else COALESCE(g.uraian_visi_renstra,"-") end as Ur_Visi 
 FROM ref_sub_unit c LEFT OUTER JOIN ref_data_sub_unit b on b.id_sub_unit=c.id_sub_unit 
 INNER JOIN ref_unit d on d.id_unit=c.id_unit LEFT OUTER JOIN trx_renstra_visi g ON g.id_unit = d.id_unit 
 INNER JOIN ref_bidang e on e.id_bidang=d.id_bidang inner join ref_urusan f on f.kd_urusan=e.kd_urusan 
 
   ');
        //-- Where f.Kd_Urusan like ''kode_ur+''' and e.Kd_Bidang like ''kode_bid+''' and d.Kd_Unit like ''kode_unit+''' and c.Kd_Sub like ''kode_sub+'''
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><mydoc></mydoc>');
        
        $xml->addAttribute('Created', 'by Simd@');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $tabel = $xml->addChild('tabel');
        $tabel->addAttribute('tabel', 'Ta_Sub_Unit');
        foreach ($unit as $program) {
            $data = $tabel->addChild('data');
            $data->addChild('Tahun', $program->Tahun);
            $data->addChild('kd_urusan', $program->kd_urusan);
            $data->addChild('kd_bidang', $program->kd_bidang);
            $data->addChild('kd_unit', $program->kd_unit);
            $data->addChild('kd_sub', $program->kd_sub);
            $data->addChild('nm_pimpinan', $program->nm_pimpinan);
            $data->addChild('nip_pimpinan', $program->nip_pimpinan);
            $data->addChild('jbt_pimpinan', $program->jbt_pimpinan);
            $data->addChild('Alamat', $program->Alamat);
            $data->addChild('Ur_Visi', $program->Ur_Visi);
            
            
            
        }
        
        $response = Response::make($xml->asXML(), 200);
        $response->header('Cache-Control', 'public');
        $response->header('Content-Description', 'File Transfer');
        $response->header('Content-Disposition', 'attachment; filename=' . TIME() . '.xml');
        $response->header('Content-Transfer-Encoding', 'binary');
        $response->header('Content-Type', 'text/xml');
        
        return $response;
    }
    
    public function getTaMisi(Request $request)
    {
        $unit = DB::SELECT('SELECT distinct YEAR(CURDATE()) as Tahun, i.kd_urusan, i.kd_bidang, h.kd_unit, g.kd_sub, a.no_urut as no_misi, 
XML_Encode(GantiEnter(a.uraian_misi_renstra)) as ur_misi
 FROM trx_renstra_misi AS a 
 INNER JOIN trx_renstra_tujuan AS b ON b.id_misi_renstra = a.id_misi_renstra 
 INNER JOIN trx_renstra_sasaran AS c ON c.id_tujuan_renstra = b.id_tujuan_renstra 
 INNER JOIN trx_renstra_program AS d ON d.id_sasaran_renstra = c.id_sasaran_renstra 
 INNER JOIN trx_renstra_kegiatan AS e ON e.id_program_renstra = d.id_program_renstra 
 INNER JOIN trx_renstra_kegiatan_pelaksana AS f ON f.id_kegiatan_renstra = e.id_kegiatan_renstra 
 INNER JOIN ref_sub_unit AS g ON f.id_sub_unit = g.id_sub_unit 
 INNER JOIN ref_unit AS h ON g.id_unit = h.id_unit 
 INNER JOIN ref_bidang AS i ON h.id_bidang = i.id_bidang 
 WHERE NOT a.no_urut IN (98,99) 
   ');
   
        //-- Where f.Kd_Urusan like ''kode_ur+''' and e.Kd_Bidang like ''kode_bid+''' and d.Kd_Unit like ''kode_unit+''' and c.Kd_Sub like ''kode_sub+'''
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><mydoc></mydoc>');
        
        $xml->addAttribute('Created', 'by Simd@');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $tabel1 = $xml->addChild('tabel');
        $tabel1->addAttribute('tabel', 'Ta_Misi');
        foreach ($unit as $program) {
            $data = $tabel1->addChild('data'); 
            $data->addChild('Tahun', $program->Tahun);
            $data->addChild('kd_urusan', $program->kd_urusan);
            $data->addChild('kd_bidang', $program->kd_bidang);
            $data->addChild('kd_unit', $program->kd_unit);
            $data->addChild('kd_sub', $program->kd_sub);
            $data->addChild('no_misi', $program->no_misi); 
            $data->addChild('ur_misi', $program->ur_misi);
            
        }
        
        $response = Response::make($xml->asXML(), 200);
        $response->header('Cache-Control', 'public');
        $response->header('Content-Description', 'File Transfer');
        $response->header('Content-Disposition', 'attachment; filename=' . TIME() . '.xml');
        $response->header('Content-Transfer-Encoding', 'binary');
        $response->header('Content-Type', 'text/xml');
        
        return $response;
    }
    
    public function getTaTujuan(Request $request)
    {
        $unit = DB::SELECT('SELECT DISTINCT YEAR(CURDATE()) as Tahun, k.kd_urusan, j.kd_bidang, i.kd_unit, 
 h.kd_sub, x.no_urut as no_misi, y.no_urut as no_tujuan, y.uraian_tujuan_renstra as ur_tujuan 
 from trx_renja_rancangan a INNER JOIN trx_renja_rancangan_pelaksana g on a.id_renja=g.id_renja 
 INNER JOIN ref_sub_unit h on g.id_sub_unit=h.id_sub_unit 
 INNER JOIN ref_unit i on h.id_unit=i.id_unit 
 INNER JOIN ref_bidang j on i.id_bidang=j.id_bidang 
 INNER JOIN ref_urusan k ON j.kd_urusan = k.kd_urusan 
 INNER JOIN trx_renstra_misi x on a.id_misi_renstra = x.id_misi_renstra 
 INNER JOIN trx_renstra_tujuan y on a.id_tujuan_renstra = y.id_tujuan_renstra 
where a.tahun_renja = 2019 and x.no_urut not in (98,99) 
   ');
        //-- Where f.Kd_Urusan like ''kode_ur+''' and e.Kd_Bidang like ''kode_bid+''' and d.Kd_Unit like ''kode_unit+''' and c.Kd_Sub like ''kode_sub+'''
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><mydoc></mydoc>');
        
        $xml->addAttribute('Created', 'by Simd@');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $tabel2 = $xml->addChild('tabel');
        $tabel2->addAttribute('tabel', 'Ta_Tujuan');
        foreach ($unit as $program) {
            $data = $tabel2->addChild('data');
            $data->addChild('Tahun', $program->Tahun);
            $data->addChild('kd_urusan', $program->kd_urusan);
            $data->addChild('kd_bidang', $program->kd_bidang);
            $data->addChild('kd_unit', $program->kd_unit);
            $data->addChild('kd_sub', $program->kd_sub);
            $data->addChild('no_misi', $program->no_misi);
            $data->addChild('no_tujuan', $program->no_tujuan);
            $data->addChild('ur_tujuan', $program->ur_tujuan);
            
        }
        
        $response = Response::make($xml->asXML(), 200);
        $response->header('Cache-Control', 'public');
        $response->header('Content-Description', 'File Transfer');
        $response->header('Content-Disposition', 'attachment; filename=' . TIME() . '.xml');
        $response->header('Content-Transfer-Encoding', 'binary');
        $response->header('Content-Type', 'text/xml');
        
        return $response;
    }
    
    public function getTaSasaran(Request $request)
    {
        $unit = DB::SELECT('SELECT DISTINCT YEAR(CURDATE()) as Tahun, k.kd_urusan, j.kd_bidang, i.kd_unit, h.kd_sub, 
 x.no_urut as no_misi, y.no_urut as no_tujuan, z.no_urut as no_sasaran, 
 XML_Encode(z.uraian_sasaran_renstra) as ur_sasaran from trx_renja_rancangan a 
 INNER JOIN trx_renja_rancangan_pelaksana g on a.id_renja=g.id_renja 
 INNER JOIN ref_sub_unit h on g.id_sub_unit=h.id_sub_unit 
 INNER JOIN ref_unit i on h.id_unit=i.id_unit 
 INNER JOIN ref_bidang j on i.id_bidang=j.id_bidang 
 INNER JOIN ref_urusan k ON j.kd_urusan = k.kd_urusan 
 INNER JOIN trx_renstra_misi x on a.id_misi_renstra = x.id_misi_renstra 
 INNER JOIN trx_renstra_tujuan y on a.id_tujuan_renstra = y.id_tujuan_renstra 
 INNER JOIN trx_renstra_sasaran z on a.id_sasaran_renstra = z.id_sasaran_renstra 
 WHERE a.tahun_renja = 2019 and x.no_urut not in (98,99) 
   ');
        //-- Where f.Kd_Urusan like ''kode_ur+''' and e.Kd_Bidang like ''kode_bid+''' and d.Kd_Unit like ''kode_unit+''' and c.Kd_Sub like ''kode_sub+'''
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><mydoc></mydoc>');
        
        $xml->addAttribute('Created', 'by Simd@');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $tabel3 = $xml->addChild('tabel');
        $tabel3->addAttribute('tabel', 'Ta_Sasaran');
        foreach ($unit as $program) {
            $data = $tabel3->addChild('data');
            $data->addChild('Tahun', $program->Tahun);
            $data->addChild('kd_urusan', $program->kd_urusan);
            $data->addChild('kd_bidang', $program->kd_bidang);
            $data->addChild('kd_unit', $program->kd_unit);
            $data->addChild('kd_sub', $program->kd_sub);
            $data->addChild('no_misi', $program->no_misi);
            $data->addChild('no_tujuan', $program->no_tujuan);
            $data->addChild('no_sasaran', $program->no_sasaran);
            $data->addChild('ur_sasaran', $program->ur_sasaran);
            
        }
        
        $response = Response::make($xml->asXML(), 200);
        $response->header('Cache-Control', 'public');
        $response->header('Content-Description', 'File Transfer');
        $response->header('Content-Disposition', 'attachment; filename=' . TIME() . '.xml');
        $response->header('Content-Transfer-Encoding', 'binary');
        $response->header('Content-Type', 'text/xml');
        
        return $response;
    }
    
    public function getTaProgram(Request $request)
    {
        $unit = DB::SELECT('SELECT a.tahun, a.Kd_Urusan, a.Kd_Bidang, a.kd_unit, a.kd_sub, a.Kd_Prog,	a.id_prog, case when a.Kd_Prog = 0  
 then "Non Program" else min(a.ket_program) end as ket_program, coalesce(min(a.Tolak_Ukur),"-") as Tolak_Ukur, COALESCE(max(a.Target_Angka),0) AS Target_Angka, 
 coalesce(min(a.Target_Uraian),"-") as Target_Uraian, a.Kd_Urusan1, a.Kd_Bidang1, SUM(COALESCE(a.jml_belanja_forum,0)) as jml_belanja_forum 
 FROM ( 
 SELECT a.tahun_forum as tahun, k.Kd_Urusan, j.Kd_Bidang, i.kd_unit, h.kd_sub, f.Kd_Prog, f.id_prog, f.Kd_Keg, 
 l.kd_rek_1, l.kd_rek_2, l.kd_rek_3, l.kd_rek_4, l.kd_rek_5, 
 a.id_belanja_forum, a.id_lokasi_forum, a.id_zona_ssh, a.id_belanja_renja, a.sumber_belanja, 
 concat_WS(" ", XML_Encode(GantiEnter(b.uraian_aktivitas_kegiatan)), ab.uraian_lokasi) as AKTIVITAS_LOKASI, 
 a.id_aktivitas_asb, a.id_item_ssh, a.id_rekening_ssh, l.nama_kd_rek_5, 
 a.uraian_belanja, a.volume_1, a.id_satuan_1, a.volume_2, a.id_satuan_2, 
 a.harga_satuan, a.jml_belanja, a.volume_1_forum, a.id_satuan_1_forum, a.volume_2_forum, a.id_satuan_2_forum, a.harga_satuan_forum, 
 COALESCE(a.jml_belanja_forum,0) as jml_belanja_forum, a.status_data, a.sumber_data, 
 f.Kd_Urusan AS Kd_Urusan1, f.Kd_Bidang AS Kd_Bidang1, g.uraian_program_renstra as ket_program, 
 gx.tolok_ukur_indikator AS Tolak_Ukur, gx.target_renja as Target_Angka, xx.uraian_satuan AS Target_Uraian, 
 COALESCE(e.pagu_forum) AS Pagu_Anggaran 
 FROM trx_forum_skpd_belanja a 
 INNER JOIN trx_forum_skpd_aktivitas b ON a.id_lokasi_forum = b.id_aktivitas_forum 
 LEFT OUTER JOIN (select id_pelaksana_forum, GROUP_CONCAT(" ",uraian_lokasi) as uraian_lokasi from trx_forum_skpd_lokasi 
 GROUP BY id_pelaksana_forum) AS ab ON ab.id_pelaksana_forum = b.id_aktivitas_forum 
 INNER JOIN trx_forum_skpd_pelaksana d ON b.id_forum_skpd = d.id_pelaksana_forum 
 INNER JOIN trx_forum_skpd e ON d.id_aktivitas_forum = e.id_forum_skpd 
 INNER JOIN ( 
Select d.Kd_Urusan, c.Kd_Bidang, b.Kd_Program as kd_prog, a.kd_kegiatan as kd_keg, a.id_kegiatan, a.id_program, a.nm_kegiatan, 
CASE WHEN b.kd_program = 0 then 0 else CONCAT(d.kd_urusan,RIGHT(CONCAT(0,c.kd_bidang),2)) end as id_prog 
FROM ref_kegiatan a 
INNER JOIN ref_program b ON a.id_program=b.id_program 
INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang 
INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan) as f 
 ON e.id_kegiatan_ref = f.id_kegiatan 
 INNER JOIN trx_forum_skpd_program g ON e.id_forum_program = g.id_forum_program 
 LEFT JOIN trx_forum_skpd_program_indikator gx on g.id_forum_program = gx.id_forum_program 
 LEFT JOIN ref_satuan xx on gx.id_satuan_ouput = xx.id_satuan 
 INNER JOIN ref_sub_unit h on d.id_sub_unit = h.id_sub_unit 
 INNER JOIN ref_unit i on h.id_unit=i.id_unit 
 INNER JOIN ref_bidang j on i.id_bidang=j.id_bidang 
 INNER JOIN ref_urusan k ON j.kd_urusan = k.kd_urusan 
 INNER JOIN ref_rek_5 l on l.id_rekening = a.id_rekening_ssh 
 where a.tahun_forum = 2019 AND g.jenis_belanja = 0 
 -- and k.Kd_Urusan like "kode_ur" and j.Kd_Bidang like "kode_bid" and i.Kd_Unit like "kode_unit" and h.Kd_Sub like "kode_sub" 
 ) a 
 where a.Kd_Prog>0 and a.id_prog>0
 GROUP BY a.tahun, a.Kd_Urusan, a.Kd_Bidang, a.kd_unit, a.kd_sub, a.Kd_Prog, a.id_prog, a.Kd_Urusan1, a.Kd_Bidang1 
 UNION 
 SELECT
			Tahun,
			Kd_Urusan,
			Kd_Bidang,
			Kd_Unit,
			Kd_Sub,
			0 AS kd_prog,
			0 AS id_prog,
			("Non Program") AS ket_program,
			("-") AS tolak_ukur,
			0 AS target_angka,
			("-") AS target_uraian,
			0 AS kd_urusan1,
			0 AS kd_bidang1
		FROM
			ta_sub_unit
		GROUP BY
			Tahun,
			Kd_Urusan,
			Kd_Bidang,
			Kd_Unit,
			Kd_Sub
   ');
        //-- Where f.Kd_Urusan like ''kode_ur+''' and e.Kd_Bidang like ''kode_bid+''' and d.Kd_Unit like ''kode_unit+''' and c.Kd_Sub like ''kode_sub+'''
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><mydoc></mydoc>');
        
        $xml->addAttribute('Created', 'by Simd@');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $tabel4 = $xml->addChild('tabel');
        $tabel4->addAttribute('tabel', 'Ta_Program');
        foreach ($unit as $program) {
            $data = $tabel4->addChild('data');
            $data->addChild('tahun', $program->tahun);
            $data->addChild('kd_urusan', $program->Kd_Urusan);
            $data->addChild('kd_bidang', $program->Kd_Bidang);
            $data->addChild('kd_unit', $program->kd_unit);
            $data->addChild('kd_sub', $program->kd_sub);
            $data->addChild('kd_prog', $program->kd_prog);
            $data->addChild('id_prog', $program->id_prog);
            $data->addChild('ket_program', $program->ket_program);
            $data->addChild('Tolak_Ukur', $program->Tolak_Ukur);
            $data->addChild('Target_Angka', $program->Target_Angka);
            $data->addChild('Target_Uraian', $program->Target_Uraian);
            $data->addChild('Kd_Urusan1', $program->Kd_Urusan1);
            $data->addChild('Kd_Bidang1', $program->Kd_Bidang1);
            $data->addChild('jml_belanja_forum', $program->jml_belanja_forum);
        }
        
        $response = Response::make($xml->asXML(), 200);
        $response->header('Cache-Control', 'public');
        $response->header('Content-Description', 'File Transfer');
        $response->header('Content-Disposition', 'attachment; filename=' . TIME() . '.xml');
        $response->header('Content-Transfer-Encoding', 'binary');
        $response->header('Content-Type', 'text/xml');
        
        return $response;
    }

    public function getTaKegiatan(Request $request)
    {
        $unit = DB::SELECT(' SELECT a.tahun, a.Kd_Urusan, a.Kd_Bidang, a.kd_unit, a.kd_sub, a.Kd_Prog, a.id_prog, a.Kd_Keg, 
 case when a.Kd_Keg = 0 then "Non Kegiatan" else min(XML_Encode(a.nm_kegiatan)) end as ket_kegiatan, 
 ifnull(min(a.uraian_lokasi),"-") as Lokasi, "-" as Kelompok_Sasaran, 1 as Status_Kegiatan, 
 sum(COALESCE(a.Pagu_Anggaran,0)) AS Pagu_Anggaran, Null as Waktu_Pelaksanaan, null as Kd_Sumber  
 FROM ( 
 SELECT a.tahun_forum as tahun, k.Kd_Urusan, j.Kd_Bidang, i.kd_unit, h.kd_sub, f.Kd_Prog, f.id_prog, f.Kd_Keg, f.nm_kegiatan, 
 l.kd_rek_1, l.kd_rek_2, l.kd_rek_3, l.kd_rek_4, l.kd_rek_5, 
 a.id_belanja_forum, a.id_lokasi_forum, a.id_zona_ssh, a.id_belanja_renja, a.sumber_belanja, 
 ab.uraian_lokasi, concat_WS(" ", XML_Encode(GantiEnter(b.uraian_aktivitas_kegiatan)), ab.uraian_lokasi) as AKTIVITAS_LOKASI, 
 a.id_aktivitas_asb, a.id_item_ssh, a.id_rekening_ssh, l.nama_kd_rek_5, 
 a.uraian_belanja, a.volume_1, a.id_satuan_1, a.volume_2, a.id_satuan_2, 
 a.harga_satuan, a.jml_belanja, a.volume_1_forum, a.id_satuan_1_forum, a.volume_2_forum, a.id_satuan_2_forum, a.harga_satuan_forum, 
 COALESCE(a.jml_belanja_forum,0) as jml_belanja_forum, a.status_data, a.sumber_data, 
 f.Kd_Urusan AS Kd_Urusan1, f.Kd_Bidang AS Kd_Bidang1, g.uraian_program_renstra as ket_program, 
 gx.tolok_ukur_indikator AS Tolak_Ukur, gx.target_renja as Target_Angka, xx.uraian_satuan AS Target_Uraian, b.jenis_kegiatan, 
 COALESCE(e.pagu_tahun_kegiatan,0) as pagu_tahun_kegiatan, b.sumber_dana, 
 COALESCE(e.pagu_forum) AS Pagu_Anggaran 
 FROM trx_forum_skpd_belanja a 
 INNER JOIN trx_forum_skpd_aktivitas b ON a.id_lokasi_forum = b.id_aktivitas_forum 
 LEFT OUTER JOIN (select a.id_pelaksana_forum, min(b.id_lokasi) as id_lokasi, GROUP_CONCAT(" ",b.nama_lokasi) as uraian_lokasi from trx_forum_skpd_lokasi a inner join ref_lokasi b on a.id_lokasi=b.id_lokasi 
 GROUP BY id_pelaksana_forum) AS ab ON ab.id_pelaksana_forum = b.id_aktivitas_forum 
 INNER JOIN trx_forum_skpd_pelaksana d ON b.id_forum_skpd = d.id_pelaksana_forum 
 INNER JOIN trx_forum_skpd e ON d.id_aktivitas_forum = e.id_forum_skpd 
 INNER JOIN ( 
Select d.Kd_Urusan, c.Kd_Bidang, b.Kd_Program as kd_prog, a.kd_kegiatan as kd_keg, a.id_kegiatan, a.id_program, a.nm_kegiatan, 
CASE WHEN b.kd_program = 0 then 0 else CONCAT(d.kd_urusan,RIGHT(CONCAT(0,c.kd_bidang),2)) end as id_prog  
FROM ref_kegiatan a  
INNER JOIN ref_program b ON a.id_program=b.id_program  
INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang  
INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan) as f  
 ON e.id_kegiatan_ref = f.id_kegiatan  
 INNER JOIN trx_forum_skpd_program g ON e.id_forum_program = g.id_forum_program 
 LEFT JOIN trx_forum_skpd_program_indikator gx on g.id_forum_program = gx.id_forum_program 
 LEFT JOIN ref_satuan xx on gx.id_satuan_ouput = xx.id_satuan 
 INNER JOIN ref_sub_unit h on d.id_sub_unit = h.id_sub_unit 
 INNER JOIN ref_unit i on h.id_unit=i.id_unit  
 INNER JOIN ref_bidang j on i.id_bidang=j.id_bidang  
 INNER JOIN ref_urusan k ON j.kd_urusan = k.kd_urusan  
 INNER JOIN ref_rek_5 l on l.id_rekening = a.id_rekening_ssh 
 where a.tahun_forum = 2019 AND g.jenis_belanja = 0           
  -- and k.Kd_Urusan like "kode_ur" and j.Kd_Bidang like "kode_bid" and i.Kd_Unit like "kode_unit" and h.Kd_Sub like "kode_sub" 
 ) a 
 GROUP BY a.tahun, a.Kd_Urusan, a.Kd_Bidang, a.kd_unit, a.kd_sub, a.Kd_Prog, a.id_prog, a.kd_keg 
 UNION 
 SELECT a.tahun, a.Kd_Urusan, a.Kd_Bidang, a.kd_unit, a.kd_sub, a.Kd_Prog, a.id_prog, a.Kd_Keg, 
 case when a.Kd_Keg = 0 then "Non Kegiatan" else min(a.nm_kegiatan) end as ket_kegiatan, 
 ifnull(min(a.uraian_lokasi),"-") as Lokasi, "-" as Kelompok_Sasaran, a.jenis_kegiatan+1 as Status_Kegiatan, 
 sum(COALESCE(a.Pagu_Anggaran,0)) AS Pagu_Anggaran, Null as Waktu_Pelaksanaan, null as Kd_Sumber  
 FROM ( 
 SELECT a.tahun_forum as tahun, k.Kd_Urusan, j.Kd_Bidang, i.kd_unit, h.kd_sub, f.Kd_Prog, f.id_prog, f.Kd_Keg, f.nm_kegiatan, 
 l.kd_rek_1, l.kd_rek_2, l.kd_rek_3, l.kd_rek_4, l.kd_rek_5, 
 a.id_belanja_forum, a.id_lokasi_forum, a.id_zona_ssh, a.id_belanja_renja, a.sumber_belanja, 
 ab.uraian_lokasi, concat_WS(" ", XML_Encode(GantiEnter(b.uraian_aktivitas_kegiatan)), ab.uraian_lokasi) as AKTIVITAS_LOKASI, 
 a.id_aktivitas_asb, a.id_item_ssh, a.id_rekening_ssh, l.nama_kd_rek_5, 
 a.uraian_belanja, a.volume_1, a.id_satuan_1, a.volume_2, a.id_satuan_2, 
 a.harga_satuan, a.jml_belanja, a.volume_1_forum, a.id_satuan_1_forum, a.volume_2_forum, a.id_satuan_2_forum, a.harga_satuan_forum, 
 COALESCE(a.jml_belanja_forum,0) as jml_belanja_forum, a.status_data, a.sumber_data, 
 f.Kd_Urusan AS Kd_Urusan1, f.Kd_Bidang AS Kd_Bidang1, g.uraian_program_renstra as ket_program, 
 gx.tolok_ukur_indikator AS Tolak_Ukur, gx.target_renja as Target_Angka, xx.uraian_satuan AS Target_Uraian, b.jenis_kegiatan, 
 COALESCE(e.pagu_tahun_kegiatan,0) as pagu_tahun_kegiatan, b.sumber_dana, 
 COALESCE(e.pagu_forum) AS Pagu_Anggaran 
 FROM trx_forum_skpd_belanja a 
 INNER JOIN trx_forum_skpd_aktivitas b ON a.id_lokasi_forum = b.id_aktivitas_forum 
 LEFT OUTER JOIN (select id_pelaksana_forum, GROUP_CONCAT(" ",uraian_lokasi) as uraian_lokasi from trx_forum_skpd_lokasi 
 GROUP BY id_pelaksana_forum) AS ab ON ab.id_pelaksana_forum = b.id_aktivitas_forum 
 INNER JOIN trx_forum_skpd_pelaksana d ON b.id_forum_skpd = d.id_pelaksana_forum 
 INNER JOIN trx_forum_skpd e ON d.id_aktivitas_forum = e.id_forum_skpd 
 INNER JOIN ( 
Select d.Kd_Urusan, c.Kd_Bidang, b.Kd_Program as kd_prog, a.kd_kegiatan as kd_keg, a.id_kegiatan, a.id_program, a.nm_kegiatan, 
CASE WHEN b.kd_program = 0 then 0 else CONCAT(d.kd_urusan,RIGHT(CONCAT(0,c.kd_bidang),2)) end as id_prog  
FROM ref_kegiatan a  
INNER JOIN ref_program b ON a.id_program=b.id_program  
INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang  
INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan) as f  
 ON e.id_kegiatan_ref = f.id_kegiatan  
 INNER JOIN trx_forum_skpd_program g ON e.id_forum_program = g.id_forum_program 
 LEFT JOIN trx_forum_skpd_program_indikator gx on g.id_forum_program = gx.id_forum_program 
 LEFT JOIN ref_satuan xx on gx.id_satuan_ouput = xx.id_satuan 
 INNER JOIN ref_sub_unit h on d.id_sub_unit = h.id_sub_unit 
 INNER JOIN ref_unit i on h.id_unit=i.id_unit  
 INNER JOIN ref_bidang j on i.id_bidang=j.id_bidang  
 INNER JOIN ref_urusan k ON j.kd_urusan = k.kd_urusan  
 INNER JOIN ref_rek_5 l on l.id_rekening = a.id_rekening_ssh 
 where a.tahun_forum = 2019 AND g.jenis_belanja in (1,2)    -- // jenis_belanja    0=BL, 1=pdpt, 2=BTL         
  -- and k.Kd_Urusan like "kode_ur" and j.Kd_Bidang like "kode_bid" and i.Kd_Unit like "kode_unit" and h.Kd_Sub like "kode_sub" 
 ) a 
 where a.kd_prog>0 and a.id_prog>0
 GROUP BY a.tahun, a.Kd_Urusan, a.Kd_Bidang, a.kd_unit, a.kd_sub, a.Kd_Prog, a.id_prog, a.kd_keg, a.jenis_kegiatan 



   ');
        //-- Where f.Kd_Urusan like ''kode_ur+''' and e.Kd_Bidang like ''kode_bid+''' and d.Kd_Unit like ''kode_unit+''' and c.Kd_Sub like ''kode_sub+'''
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><mydoc></mydoc>');
        
        $xml->addAttribute('Created', 'by Simd@');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $tabel5 = $xml->addChild('tabel');
        $tabel5->addAttribute('tabel', 'Ta_Kegiatan');
        foreach ($unit as $program) {
            $data = $tabel5->addChild('data');
            $data->addChild('tahun', $program->tahun);
            $data->addChild('Kd_Urusan', $program->Kd_Urusan);
            $data->addChild('Kd_Bidang', $program->Kd_Bidang);
            $data->addChild('kd_unit', $program->kd_unit);
            $data->addChild('kd_sub', $program->kd_sub);
            $data->addChild('kd_prog', $program->kd_prog);
            $data->addChild('id_prog', $program->id_prog);
            $data->addChild('kd_keg', $program->kd_keg);
            $data->addChild('ket_kegiatan', $program->ket_kegiatan);
            $data->addChild('Lokasi', $program->Lokasi);
            $data->addChild('Kelompok_Sasaran', $program->Kelompok_Sasaran);
            $data->addChild('Status_Kegiatan', $program->Status_Kegiatan);
            $data->addChild('Pagu_Anggaran', $program->Pagu_Anggaran);
            $data->addChild('Waktu_Pelaksanaan', $program->Waktu_Pelaksanaan);
            $data->addChild('Kd_Sumber', $program->Kd_Sumber);
            
        }
        
        $response = Response::make($xml->asXML(), 200);
        $response->header('Cache-Control', 'public');
        $response->header('Content-Description', 'File Transfer');
        $response->header('Content-Disposition', 'attachment; filename=' . TIME() . '.xml');
        $response->header('Content-Transfer-Encoding', 'binary');
        $response->header('Content-Type', 'text/xml');
        
        return $response;
    }
    
    public function getTaBelanja(Request $request,$unit)
    {
  //      $unit='1.1.1';
    $Belanja = DB::SELECT('SELECT a.tahun, a.Kd_Urusan, a.Kd_Bidang, a.kd_unit, a.kd_sub, a.Kd_Prog, a.id_prog, a.Kd_Keg, a.kd_rek_1,
	a.kd_rek_2, a.kd_rek_3, a.kd_rek_4, a.kd_rek_5, 2 AS Kd_Ap_Pub,
	CASE max(sumber_dana) WHEN 0 THEN 1 ELSE max(sumber_dana) END AS Kd_Sumber
	FROM (
		SELECT a.tahun_anggaran AS tahun, k.Kd_Urusan, j.Kd_Bidang, i.kd_unit, h.kd_sub, f.Kd_Prog, f.id_prog, f.Kd_Keg, f.nm_kegiatan,
		l.kd_rek_1, l.kd_rek_2, l.kd_rek_3, l.kd_rek_4, l.kd_rek_5, a.id_belanja_rkpd_final, a.id_aktivitas_pd, a.id_zona_ssh, a.id_belanja_pd,
		a.sumber_belanja, ab.uraian_lokasi, concat_WS( " ", XML_Encode(GantiEnter(b.uraian_aktivitas_kegiatan)), ab.uraian_lokasi ) AS AKTIVITAS_LOKASI,
		a.id_aktivitas_asb, a.id_item_ssh, a.id_rekening_ssh, l.nama_kd_rek_5, a.uraian_belanja, a.volume_1, a.id_satuan_1, a.volume_2, a.id_satuan_2,
		a.harga_satuan, a.jml_belanja, a.volume_1 as volume_1_forum, a.id_satuan_1 as id_satuan_1_forum, a.volume_2 as volume_2_forum, a.id_satuan_2 as id_satuan_2_forum,
		a.harga_satuan as harga_satuan_forum, COALESCE (a.jml_belanja, 0) AS jml_belanja_forum, a.status_data, a.sumber_data, f.Kd_Urusan AS Kd_Urusan1, f.Kd_Bidang AS Kd_Bidang1,
		g.uraian_program_renstra AS ket_program, gx.tolok_ukur_indikator AS Tolak_Ukur, gx.target_renja AS Target_Angka, xx.uraian_satuan AS Target_Uraian, b.jenis_kegiatan,
		COALESCE (e.pagu_tahun_kegiatan, 0) AS pagu_tahun_kegiatan, b.sumber_dana, COALESCE (e.pagu_forum) AS Pagu_Anggaran
		FROM trx_anggaran_belanja_pd AS a
		INNER JOIN trx_anggaran_aktivitas_pd AS b ON a.id_aktivitas_pd = b.id_aktivitas_pd
		LEFT OUTER JOIN ( SELECT id_aktivitas_pd, GROUP_CONCAT(" ", uraian_lokasi) AS uraian_lokasi FROM trx_anggaran_lokasi_pd GROUP BY id_aktivitas_pd ) AS ab ON ab.id_aktivitas_pd = b.id_aktivitas_pd
		INNER JOIN trx_anggaran_pelaksana_pd AS d ON b.id_pelaksana_pd = d.id_pelaksana_pd
		INNER JOIN trx_anggaran_kegiatan_pd AS e ON d.id_kegiatan_pd = e.id_kegiatan_pd
		INNER JOIN ( SELECT d.Kd_Urusan, c.Kd_Bidang, b.Kd_Program AS kd_prog, a.kd_kegiatan AS kd_keg, a.id_kegiatan, a.id_program, a.nm_kegiatan, 
			CASE WHEN b.kd_program = 0 THEN 0 ELSE CONCAT( d.kd_urusan, RIGHT (CONCAT(0, c.kd_bidang), 2) ) END AS id_prog
			FROM ref_kegiatan a
			INNER JOIN ref_program b ON a.id_program = b.id_program
			INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang
			INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan
			) AS f ON e.id_kegiatan_ref = f.id_kegiatan
		INNER JOIN trx_anggaran_program_pd AS g ON e.id_program_pd = g.id_program_pd
		LEFT JOIN trx_anggaran_prog_indikator_pd AS gx ON gx.id_program_pd = g.id_program_pd
		LEFT JOIN ref_satuan xx ON gx.id_satuan_output = xx.id_satuan
		INNER JOIN ref_sub_unit h ON d.id_sub_unit = h.id_sub_unit
		INNER JOIN ref_unit i ON h.id_unit = i.id_unit
		INNER JOIN ref_bidang j ON i.id_bidang = j.id_bidang
		INNER JOIN ref_urusan k ON j.kd_urusan = k.kd_urusan
		INNER JOIN ref_rek_5 l ON l.id_rekening = a.id_rekening_ssh
		WHERE b.status_pelaksanaan = 0 AND e.status_pelaksanaan NOT IN (2, 3, 5)
		AND a.tahun_anggaran = 2019 and g.id_dokumen_keu=2 ) a  
	WHERE concat(a.Kd_Urusan,".", a.Kd_Bidang,".", a.kd_unit)="'.$unit.'"
	GROUP BY a.tahun, a.Kd_Urusan, a.Kd_Bidang, a.kd_unit, a.kd_sub, a.Kd_Prog, a.id_prog, a.kd_keg, a.kd_rek_1, a.kd_rek_2, a.kd_rek_3, a.kd_rek_4, a.kd_rek_5
	HAVING a.kd_rek_1 = 5 ');
	
    $BelanjaHistory=DB::SELECT('SELECT a.tahun, a.Kd_Urusan, a.Kd_Bidang, a.kd_unit, a.kd_sub, a.Kd_Prog, a.id_prog, a.Kd_Keg, a.kd_rek_1,
		a.kd_rek_2, a.kd_rek_3, a.kd_rek_4, a.kd_rek_5, 0 AS Nilai_Lalu, sum(pagu_plus1_forum) AS Nilai_YAD, 0 AS Nilai_YAD_2, 0 AS Nilai_YAD_3
		FROM ( SELECT a.tahun_anggaran AS tahun, k.Kd_Urusan, j.Kd_Bidang, i.kd_unit, h.kd_sub, f.Kd_Prog, f.id_prog, f.Kd_Keg, f.nm_kegiatan, l.kd_rek_1,
		l.kd_rek_2, l.kd_rek_3, l.kd_rek_4, l.kd_rek_5, e.pagu_plus1_forum, a.id_belanja_pd as id_belanja_forum, a.id_aktivitas_pd, a.id_zona_ssh, a.sumber_belanja,
		ab.uraian_lokasi, concat_WS( " ", XML_Encode(GantiEnter(b.uraian_aktivitas_kegiatan)), ab.uraian_lokasi ) AS AKTIVITAS_LOKASI, a.id_aktivitas_asb, a.id_item_ssh,
		a.id_rekening_ssh, l.nama_kd_rek_5, a.uraian_belanja, a.volume_1, a.id_satuan_1, a.volume_2, a.id_satuan_2, a.harga_satuan, a.jml_belanja, a.volume_1 as volume_1_forum,
		a.id_satuan_1 as id_satuan_1_forum, a.volume_2 as volume_2_forum, a.id_satuan_2 as id_satuan_2_forum, a.harga_satuan as harga_satuan_forum, COALESCE (a.jml_belanja, 0) AS jml_belanja_forum,
		a.status_data, a.sumber_data, f.Kd_Urusan AS Kd_Urusan1, f.Kd_Bidang AS Kd_Bidang1, g.uraian_program_renstra AS ket_program, gx.tolok_ukur_indikator AS Tolak_Ukur, gx.target_renja AS Target_Angka,
		xx.uraian_satuan AS Target_Uraian, b.jenis_kegiatan, COALESCE (e.pagu_tahun_kegiatan, 0) AS pagu_tahun_kegiatan, b.sumber_dana, COALESCE (e.pagu_forum) AS Pagu_Anggaran
		FROM trx_anggaran_belanja_pd AS a
		INNER JOIN trx_anggaran_aktivitas_pd AS b ON a.id_aktivitas_pd = b.id_aktivitas_pd
		LEFT OUTER JOIN ( SELECT id_aktivitas_pd, GROUP_CONCAT(" ", uraian_lokasi) AS uraian_lokasi FROM trx_anggaran_lokasi_pd GROUP BY id_aktivitas_pd ) AS ab ON ab.id_aktivitas_pd = b.id_aktivitas_pd
		INNER JOIN trx_anggaran_pelaksana_pd AS d ON b.id_pelaksana_pd = d.id_pelaksana_pd
		INNER JOIN trx_anggaran_kegiatan_pd AS e ON d.id_kegiatan_pd = e.id_kegiatan_pd
		INNER JOIN ( SELECT d.Kd_Urusan, c.Kd_Bidang, b.Kd_Program AS kd_prog, a.kd_kegiatan AS kd_keg, a.id_kegiatan, a.id_program, a.nm_kegiatan,
			CASE WHEN b.kd_program = 0 THEN 0 ELSE CONCAT( d.kd_urusan, RIGHT (CONCAT(0, c.kd_bidang), 2) ) END AS id_prog
			FROM ref_kegiatan a
			INNER JOIN ref_program b ON a.id_program = b.id_program
			INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang
			INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan ) AS f ON e.id_kegiatan_ref = f.id_kegiatan
		INNER JOIN trx_anggaran_program_pd AS g ON e.id_program_pd = g.id_program_pd
		LEFT JOIN trx_anggaran_prog_indikator_pd AS gx ON gx.id_program_pd = g.id_program_pd
		LEFT JOIN ref_satuan xx ON gx.id_satuan_output = xx.id_satuan
		INNER JOIN ref_sub_unit h ON d.id_sub_unit = h.id_sub_unit
		INNER JOIN ref_unit i ON h.id_unit = i.id_unit
		INNER JOIN ref_bidang j ON i.id_bidang = j.id_bidang
		INNER JOIN ref_urusan k ON j.kd_urusan = k.kd_urusan
		INNER JOIN ref_rek_5 l ON l.id_rekening = a.id_rekening_ssh
		WHERE b.status_pelaksanaan = 0 AND e.status_pelaksanaan NOT IN (2, 3, 5) AND a.tahun_anggaran = 2019  and g.id_dokumen_keu=2 ) a  
		WHERE concat(a.Kd_Urusan,".", a.Kd_Bidang,".", a.kd_unit)="'.$unit.'"
		GROUP BY a.tahun, a.Kd_Urusan, a.Kd_Bidang, a.kd_unit, a.kd_sub, a.Kd_Prog, a.id_prog, a.kd_keg, a.kd_rek_1, a.kd_rek_2, a.kd_rek_3, a.kd_rek_4, a.kd_rek_5
		HAVING a.kd_rek_1 = 5');

 	$BelanjaItem=DB::Select('SELECT a.id_belanja_pd AS id_belanja_renja, b.group_keu, substring(XML_Encode(GantiEnter(b.uraian_aktivitas_kegiatan)),1,255) AS uraian_aktivitas_kegiatan,
		a.tahun_anggaran AS tahun,k.Kd_Urusan,j.Kd_Bidang,i.kd_unit,h.kd_sub,f.Kd_Prog,f.id_prog,f.Kd_Keg,l.kd_rek_1,l.kd_rek_2,l.kd_rek_3,l.kd_rek_4,l.kd_rek_5,l.nama_kd_rek_5 AS nm_rek_5,
		b.id_aktivitas_pd as id_aktivitas_forum,substring(XML_Encode(GantiEnter(b.uraian_aktivitas_kegiatan)),1,255) AS Keterangan_rinc,b.sumber_dana AS Kd_Sumber,
		substring(cc.uraian_satuan, 1, 10) AS Sat_1,a.volume_1 AS Nilai_1,substring(dd.uraian_satuan, 1, 10) AS Sat_2,a.volume_2 AS Nilai_2,NULL AS Sat_3,0 AS Nilai_3,
		CASE WHEN substring(dd.uraian_satuan, 1, 10) IS NULL THEN CONCAT(COALESCE (substring(cc.uraian_satuan, 1, 10),"-"),COALESCE(substring(dd.uraian_satuan, 1, 10),""))
		ELSE CONCAT(COALESCE(substring(cc.uraian_satuan, 1, 10),"-"),"/",COALESCE (substring(dd.uraian_satuan, 1, 10),"")) END AS Satuan123,
		CASE WHEN a.volume_2 = 0 THEN a.volume_1 ELSE (a.volume_1 * a.volume_2) END AS Jml_Satuan, a.harga_satuan AS Nilai_Rp, a.jml_belanja AS Total,
		substring(XML_Encode(GantiEnter( CASE WHEN aa.uraian_tarif_ssh = a.uraian_belanja THEN aa.uraian_tarif_ssh WHEN a.uraian_belanja = "-" THEN aa.uraian_tarif_ssh
		WHEN COALESCE (a.uraian_belanja, " ") = " " THEN aa.uraian_tarif_ssh ELSE aa.uraian_tarif_ssh END )),1,255) AS Keterangan,
		b.sumber_aktivitas, b.id_aktivitas_pd as id_aktivitas_renja, a.id_aktivitas_asb, aa.id_tarif_ssh
		FROM trx_anggaran_belanja_pd AS a
		INNER JOIN ref_ssh_tarif aa on a.id_item_ssh=aa.id_tarif_ssh          
		LEFT OUTER JOIN ref_satuan cc ON (a.id_satuan_1 = cc.id_satuan)            
		LEFT OUTER JOIN ref_satuan dd ON (a.id_satuan_2 = dd.id_satuan)            
		INNER JOIN trx_anggaran_aktivitas_pd AS b ON a.id_aktivitas_pd = b.id_aktivitas_pd
		LEFT OUTER JOIN (SELECT id_aktivitas_pd,GROUP_CONCAT(" ", uraian_lokasi) AS uraian_lokasi
		FROM trx_anggaran_lokasi_pd GROUP BY id_aktivitas_pd) AS ab ON ab.id_aktivitas_pd = b.id_aktivitas_pd
		INNER JOIN trx_anggaran_pelaksana_pd AS d ON b.id_pelaksana_pd = d.id_pelaksana_pd
		INNER JOIN trx_anggaran_kegiatan_pd AS e ON d.id_kegiatan_pd = e.id_kegiatan_pd
		INNER JOIN (SELECT d.Kd_Urusan, c.Kd_Bidang, b.Kd_Program AS kd_prog, a.kd_kegiatan AS kd_keg, a.id_kegiatan, a.id_program, a.nm_kegiatan,
		CASE WHEN b.kd_program = 0 THEN 0 ELSE CONCAT( d.kd_urusan, RIGHT (CONCAT(0, c.kd_bidang), 2)) END AS id_prog
		FROM ref_kegiatan a
		INNER JOIN ref_program b ON a.id_program = b.id_program
		INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang
		INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan ) AS f ON e.id_kegiatan_ref = f.id_kegiatan
		INNER JOIN trx_anggaran_program_pd AS g ON e.id_program_pd = g.id_program_pd
		INNER JOIN ref_sub_unit h ON d.id_sub_unit = h.id_sub_unit
		INNER JOIN ref_unit i ON h.id_unit = i.id_unit
		INNER JOIN ref_bidang j ON i.id_bidang = j.id_bidang
		INNER JOIN ref_urusan k ON j.kd_urusan = k.kd_urusan
		INNER JOIN ref_rek_5 l ON l.id_rekening = a.id_rekening_ssh
		WHERE b.status_pelaksanaan = 0 AND a.tahun_anggaran = 2019  and g.id_dokumen_keu=2 
		AND l.kd_rek_1 = 5  AND concat(k.Kd_Urusan,".", j.Kd_Bidang,".", i.kd_unit)="'.$unit.'"
		ORDER BY k.Kd_Urusan,j.Kd_Bidang,i.kd_unit,h.kd_sub,f.Kd_Prog,f.id_prog,f.Kd_Keg,l.kd_rek_1,l.kd_rek_2,l.kd_rek_3,l.kd_rek_4,l.kd_rek_5,b.id_aktivitas_pd');

	$BelanjaItem2=DB::Select(' SELECT a.id_belanja_pd AS id_belanja_renja, b.group_keu,substring(XML_Encode(GantiEnter(b.uraian_aktivitas_kegiatan)),1,255) AS uraian_aktivitas_kegiatan,
		a.tahun_anggaran AS tahun,k.Kd_Urusan,j.Kd_Bidang,i.kd_unit,h.kd_sub,f.Kd_Prog,f.id_prog,f.Kd_Keg,l.kd_rek_1,l.kd_rek_2,l.kd_rek_3,l.kd_rek_4,l.kd_rek_5,l.nama_kd_rek_5 AS nm_rek_5,
		b.id_aktivitas_pd as id_aktivitas_forum,substring(XML_Encode(GantiEnter(b.uraian_aktivitas_kegiatan)),1,255) AS Keterangan_rinc,
		b.sumber_dana AS Kd_Sumber,	substring(cc.uraian_satuan, 1, 10) AS Sat_1,a.volume_1 AS Nilai_1,substring(dd.uraian_satuan, 1, 10) AS Sat_2,
		a.volume_2 AS Nilai_2,NULL AS Sat_3,0 AS Nilai_3,
		CASE WHEN substring(dd.uraian_satuan, 1, 10) IS NULL THEN
		CONCAT(COALESCE (substring(cc.uraian_satuan, 1, 10),"-"), COALESCE(substring(dd.uraian_satuan, 1, 10),""))
		ELSE CONCAT( COALESCE (substring(cc.uraian_satuan, 1, 10),"-"),"/",COALESCE (substring(dd.uraian_satuan, 1, 10),"")) END AS Satuan123,
		CASE WHEN a.volume_2 = 0 THEN a.volume_1 ELSE (a.volume_1 * a.volume_2) END AS Jml_Satuan, a.harga_satuan AS Nilai_Rp, a.jml_belanja AS Total,
		substring(XML_Encode ( GantiEnter ( CASE WHEN aa.uraian_tarif_ssh = a.uraian_belanja THEN aa.uraian_tarif_ssh WHEN a.uraian_belanja = "-" THEN aa.uraian_tarif_ssh
		WHEN COALESCE (a.uraian_belanja, " ") = " " THEN aa.uraian_tarif_ssh ELSE CONCAT( aa.uraian_tarif_ssh, " (", COALESCE (a.uraian_belanja, " "),")") END )),1,255) AS Keterangan,
		b.sumber_aktivitas,b.id_aktivitas_pd as id_aktivitas_renja,a.id_aktivitas_asb, aa.id_tarif_ssh
		FROM trx_anggaran_belanja_pd AS a
		INNER JOIN ref_ssh_tarif aa on a.id_item_ssh=aa.id_tarif_ssh          
		LEFT OUTER JOIN ref_satuan cc ON (a.id_satuan_1 = cc.id_satuan)            
		LEFT OUTER JOIN ref_satuan dd ON (a.id_satuan_2 = dd.id_satuan)            
		INNER JOIN trx_anggaran_aktivitas_pd AS b ON a.id_aktivitas_pd = b.id_aktivitas_pd
		LEFT OUTER JOIN ( SELECT id_aktivitas_pd, GROUP_CONCAT(" ", uraian_lokasi) AS uraian_lokasi FROM trx_anggaran_lokasi_pd GROUP BY id_aktivitas_pd ) AS ab ON ab.id_aktivitas_pd = b.id_aktivitas_pd
		INNER JOIN trx_anggaran_pelaksana_pd AS d ON b.id_pelaksana_pd = d.id_pelaksana_pd
		INNER JOIN trx_anggaran_kegiatan_pd AS e ON d.id_kegiatan_pd = e.id_kegiatan_pd
		INNER JOIN ( SELECT d.Kd_Urusan, c.Kd_Bidang, b.Kd_Program AS kd_prog,a.kd_kegiatan AS kd_keg, a.id_kegiatan,a.id_program,a.nm_kegiatan,
		CASE WHEN b.kd_program = 0 THEN 0 ELSE CONCAT( d.kd_urusan, RIGHT (CONCAT(0, c.kd_bidang), 2))END AS id_prog
		FROM ref_kegiatan a
		INNER JOIN ref_program b ON a.id_program = b.id_program
		INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang
		INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan ) AS f ON e.id_kegiatan_ref = f.id_kegiatan
		INNER JOIN trx_anggaran_program_pd AS g ON e.id_program_pd = g.id_program_pd
		INNER JOIN ref_sub_unit h ON d.id_sub_unit = h.id_sub_unit
		INNER JOIN ref_unit i ON h.id_unit = i.id_unit
		INNER JOIN ref_bidang j ON i.id_bidang = j.id_bidang
		INNER JOIN ref_urusan k ON j.kd_urusan = k.kd_urusan
		INNER JOIN ref_rek_5 l ON l.id_rekening = a.id_rekening_ssh
		WHERE b.status_pelaksanaan = 0 AND a.tahun_anggaran = 2019  and g.id_dokumen_keu=2 AND l.kd_rek_1 = 5  AND concat(k.Kd_Urusan,".", j.Kd_Bidang,".", i.kd_unit)="'.$unit.'"
		ORDER BY k.Kd_Urusan, j.Kd_Bidang, i.kd_unit, h.kd_sub, f.Kd_Prog, f.id_prog, f.Kd_Keg, l.kd_rek_1, l.kd_rek_2, l.kd_rek_3, l.kd_rek_4,	l.kd_rek_5, b.id_aktivitas_pd');
	
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><mydoc></mydoc>');
        
        $xml->addAttribute('Created', 'by Simd@');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $tabel = $xml->addChild('tabel');
        $tabel->addAttribute('tabel', 'Ta_Belanja');
        foreach ($Belanja as $unit) {
            $data = $tabel->addChild('data');
            $data->addChild('tahun', $unit->tahun);
            $data->addChild('Kd_Urusan', $unit->Kd_Urusan);
            $data->addChild('Kd_Bidang', $unit->Kd_Bidang);
            $data->addChild('kd_unit', $unit->kd_unit);
            $data->addChild('kd_sub', $unit->kd_sub);
            $data->addChild('Kd_Prog', $unit->kd_prog);
            $data->addChild('id_prog', $unit->id_prog);
            $data->addChild('Kd_Keg', $unit->kd_keg);
            $data->addChild('kd_rek_1', $unit->kd_rek_1);
            $data->addChild('kd_rek_2', $unit->kd_rek_2);
            $data->addChild('kd_rek_3', $unit->kd_rek_3);
            $data->addChild('kd_rek_4', $unit->kd_rek_4);
            $data->addChild('kd_rek_5', $unit->kd_rek_5);
            $data->addChild('Kd_Ap_Pub', $unit->Kd_Ap_Pub);
            $data->addChild('Kd_Sumber', $unit->Kd_Sumber);
        }
        
        $tabel2 = $xml->addChild('tabel');
        $tabel2->addAttribute('tabel', 'Ta_Belanja_History');
        foreach ($BelanjaHistory as $unit) {
            $data = $tabel2->addChild('data');
            $data->addChild('tahun', $unit->tahun);
            $data->addChild('Kd_Urusan', $unit->Kd_Urusan);
            $data->addChild('Kd_Bidang', $unit->Kd_Bidang);
            $data->addChild('kd_unit', $unit->kd_unit);
            $data->addChild('kd_sub', $unit->kd_sub);
            $data->addChild('Kd_Prog', $unit->kd_prog);
            $data->addChild('id_prog', $unit->id_prog);
            $data->addChild('Kd_Keg', $unit->kd_keg);
            $data->addChild('kd_rek_1', $unit->kd_rek_1);
            $data->addChild('kd_rek_2', $unit->kd_rek_2);
            $data->addChild('kd_rek_3', $unit->kd_rek_3);
            $data->addChild('kd_rek_4', $unit->kd_rek_4);
            $data->addChild('kd_rek_5', $unit->kd_rek_5);
            $data->addChild('Nilai_Lalu', $unit->Nilai_Lalu);
            $data->addChild('Nilai_YAD_2', $unit->Nilai_YAD_2);
            $data->addChild('Nilai_YAD_3', $unit->Nilai_YAD_3);
        }
        $tabel3 = $xml->addChild('tabel');
        $tabel3->addAttribute('tabel', 'Ta_Belanja_Item');
		$prev1=1;
		$prev2=1;
		$prev3=1;
		$pembanding1='';
		$strprev1='';
		$strprev11='';
		$strprev2=0;
		$strprev3='';
		//$norinc=1;
        foreach ($BelanjaItem as $unit) {
            $pembanding1=(string)$unit->Kd_Urusan.'.'.(string)$unit->Kd_Bidang.'.'.(string)$unit->kd_unit.'.'.(string)$unit->kd_sub.'.'.(string)$unit->kd_prog.'.'.(string)$unit->id_prog.'.'.(string)$unit->kd_keg.'.'.(string)$unit->kd_rek_1.'.'.(string)$unit->kd_rek_2.'.'.(string)$unit->kd_rek_3.'.'.(string)$unit->kd_rek_4.'.'.(string)$unit->kd_rek_5.'.';
            if($strprev11==$pembanding1.'.'.(string) $unit->id_aktivitas_forum)
			{
			    $prev1=$prev1;
			
			}
			else
			{
			    if($strprev1==$pembanding1)
			    {
			        $prev1=$prev1+1;
			    }
			    else 
			    {
			        $prev1=1;
			    }
				
			}
			$strprev1=$pembanding1;
			$strprev11=$pembanding1.'.'.(string) $unit->id_aktivitas_forum;
			if($strprev2==$unit->id_aktivitas_forum)
			{
			$prev2=$prev2+1;
			}
			else
			{
			$prev2=1;	
			}
			$strprev2=$unit->id_aktivitas_forum;
			if($strprev3==$strprev1 and $unit->group_keu==1)
			{
				$prev3=$prev3;
			}
			else
			{
				$prev3=$prev3+1;
			}
			$strprev3=$strprev1;
            $data = $tabel3->addChild('data');
            
            $data->addChild('id_item_can', $prev3);
            $data->addChild('id_belanja_renja', $unit->id_belanja_renja);
            $data->addChild('group_keu', $unit->group_keu);
            $data->addChild('uraian_aktivitas_kegiatan', $unit->uraian_aktivitas_kegiatan);
            $data->addChild('tahun', $unit->tahun);
            $data->addChild('Kd_Urusan', $unit->Kd_Urusan);
            $data->addChild('Kd_Bidang', $unit->Kd_Bidang);
            $data->addChild('kd_unit', $unit->kd_unit);
            $data->addChild('kd_sub', $unit->kd_sub);
            $data->addChild('Kd_Prog', $unit->kd_prog);
            $data->addChild('id_prog', $unit->id_prog);
            $data->addChild('Kd_Keg', $unit->kd_keg);
            $data->addChild('kd_rek_1', $unit->kd_rek_1);
            $data->addChild('kd_rek_2', $unit->kd_rek_2);
            $data->addChild('kd_rek_3', $unit->kd_rek_3);
            $data->addChild('kd_rek_4', $unit->kd_rek_4);
            $data->addChild('kd_rek_5', $unit->kd_rek_5);
            $data->addChild('Nm_Rek_5', $unit->nm_rek_5);
            $data->addChild('No_Rinc', $prev1);
            $data->addChild('strprev1', $strprev1);
            $data->addChild('pembanding', $pembanding1);
            $data->addChild('No_ID', $prev2);
			$data->addChild('id_aktivitas_forum', $unit->id_aktivitas_forum);
            $data->addChild('Keterangan_rinc', $unit->Keterangan_rinc);
            $data->addChild('Kd_Sumber', $unit->Kd_Sumber);
            
            //$data->addChild('ID_RINC_BLJ', $unit->ID_RINC_BLJ);
            //$data->addChild('ID_RINC_BLJ1', $unit->ID_RINC_BLJ1);
            $data->addChild('Sat_1', $unit->Sat_1);
            $data->addChild('Nilai_1', $unit->Nilai_1);
            $data->addChild('Sat_2', $unit->Sat_2);
            $data->addChild('Nilai_2', $unit->Nilai_2);
            $data->addChild('Sat_3', $unit->Sat_3);
            $data->addChild('Nilai_3', $unit->Nilai_3);
            $data->addChild('Satuan123', $unit->Satuan123);
            $data->addChild('Jml_Satuan', $unit->Jml_Satuan);
            $data->addChild('Nilai_Rp', $unit->Nilai_Rp);
            $data->addChild('Total', $unit->Total);
            $data->addChild('Keterangan', $unit->Keterangan);
            $data->addChild('sumber_aktivitas', $unit->sumber_aktivitas);
            $data->addChild('id_aktivitas_renja', $unit->id_aktivitas_renja);
            $data->addChild('id_aktivitas_asb', $unit->id_aktivitas_asb);
            $data->addChild('id_tarif_ssh', $unit->id_tarif_ssh);
            
        }
        
        $response = Response::make($xml->asXML(), 200);
        $response->header('Cache-Control', 'public');
        $response->header('Content-Description', 'File Transfer');
        $response->header('Content-Disposition', 'attachment; filename=' . TIME() . '.xml');
        $response->header('Content-Transfer-Encoding', 'binary');
        $response->header('Content-Type', 'text/xml');
        
        return $response;
    }
	
	public function getTaBelanjaItem(Request $request)
    {
       
/* $BelanjaItem=DB::Select('  SELECT CONVERT(@row_num1 := IF((@prev_value1 = a.ID_RINC_BLJ) and (a.group_keu = 1), @row_num1, @row_num1+1 ),SIGNED) AS id_item_can,            
  a.id_belanja_renja, a.group_keu, substring(XML_Encode(GantiEnter(a.uraian_aktivitas_kegiatan)),1,255) as uraian_aktivitas_kegiatan, a.Tahun, a.Kd_Urusan,           
  a.Kd_Bidang, a.Kd_Unit, a.Kd_Sub, a.Kd_Prog, a.ID_Prog, a.Kd_Keg, a.Kd_Rek_1, a.Kd_Rek_2, a.Kd_Rek_3, a.Kd_Rek_4, a.Kd_Rek_5, a.Nm_Rek_5,           
  a.No_Rinc, substring(XML_Encode(GantiEnter(a.Keterangan_rinc)),1,255) as Keterangan_rinc, a.sumber_dana AS Kd_Sumber,          
  @row_num := IF(@prev_value = a.ID_RINC_BLJ,@row_num+1,1) AS No_ID,          
  @prev_value := a.ID_RINC_BLJ as ID_RINC_BLJ,          
  @prev_value1 := a.ID_RINC_BLJ as ID_RINC_BLJ1,          
  a.Sat_1, a.Nilai_1, a.Sat_2, a.Nilai_2, a.Sat_3, a.Nilai_3, a.Satuan123, a.Jml_Satuan, a.Nilai_Rp, a.Total, substring(XML_Encode(GantiEnter(a.Keterangan)),1,255 ) as Keterangan,           
  a.sumber_aktivitas, a.id_aktivitas_renja, a.id_aktivitas_asb, a.id_tarif_ssh      
  FROM ( 
  SELECT a.id_pelaksana_forum, a.id_aktivitas_renja, a.id_rekening,           
  a.id_belanja_renja, a.group_keu, a.uraian_aktivitas_kegiatan, a.Tahun, a.Kd_Urusan,           
  a.Kd_Bidang, a.Kd_Unit, a.Kd_Sub, a.Kd_Prog, a.ID_Prog, a.Kd_Keg, a.Kd_Rek_1, a.Kd_Rek_2, a.Kd_Rek_3, a.Kd_Rek_4, a.Kd_Rek_5, a.Nm_Rek_5,           
  a.No_Rinc, a.Keterangan_rinc, a.sumber_dana, a.Sat_1, a.Nilai_1, a.Sat_2, a.Nilai_2, a.Sat_3, a.Nilai_3, a.Satuan123, a.Jml_Satuan, a.Nilai_Rp, a.Total, a.Keterangan,           
  a.sumber_aktivitas, a.id_aktivitas_forum, a.id_aktivitas_asb, a.id_tarif_ssh,           

  concat(a.NoRincq,"00000000", a.no_rinc) as ID_RINC_BLJ, NoRincq           

  FROM (          
-- //  2 
  SELECT a.id_pelaksana_forum, a.id_aktivitas_renja, a.id_rekening,           
  a.id_belanja_renja, a.group_keu, a.uraian_aktivitas_kegiatan, a.Tahun, a.Kd_Urusan,           
  a.Kd_Bidang, a.Kd_Unit, a.Kd_Sub, a.Kd_Prog, a.ID_Prog, a.Kd_Keg, a.Kd_Rek_1, a.Kd_Rek_2, a.Kd_Rek_3, a.Kd_Rek_4, a.Kd_Rek_5, a.Nm_Rek_5, 
-- //  @row_num := IF(@prev_value=a.NoRincq,@row_num+1,1) AS No_Rinc, @prev_value := a.NoRincq as NoRincq,   
  @row_num := IF(@prev_value=a.NoRincq,@row_num,@row_num+1) AS No_Rinc, @prev_value := a.NoRincq as NoRincq,   
   
  a.Sat_1, a.Nilai_1, a.Sat_2, a.Nilai_2, a.Sat_3, a.Nilai_3, a.Satuan123, a.Jml_Satuan, a.Nilai_Rp, a.Total, a.Keterangan,           
  a.sumber_aktivitas, a.id_aktivitas_forum, a.id_aktivitas_asb, a.id_tarif_ssh, a.AKTIVITAS_LOKASI as Keterangan_rinc, a.sumber_dana            
  FROM ( 
-- //  3 
  SELECT a.id_pelaksana_forum, a.id_aktivitas_renja, a.id_rekening, a.id_belanja_forum as id_belanja_renja, a.group_keu, a.uraian_aktivitas_kegiatan,           
  a.Tahun, a.Kd_Urusan, a.Kd_Bidang, a.Kd_Unit, a.Kd_Sub, a.Kd_Prog, a.ID_Prog, a.Kd_Keg, a.Kd_Rek_1, a.Kd_Rek_2, a.Kd_Rek_3, a.Kd_Rek_4, a.Kd_Rek_5,  
   a.id_aktivitas_renja AS NoRincq,  
  case when a.uraian_lokasi <> " " then concat_WS("",a.uraian_aktivitas_kegiatan, " (",a.uraian_lokasi,")") else a.uraian_aktivitas_kegiatan end as AKTIVITAS_LOKASI, a.sumber_dana, 
   
  a.nama_kd_rek_5 AS Nm_Rek_5, a.Sat_1, a.volume_1_forum as Nilai_1, a.Sat_2, a.volume_2_forum as Nilai_2,            
  Null as Sat_3, 0 as Nilai_3,            
  case when a.sat2 is NULL then CONCAT(COALESCE(a.sat1,"-"),COALESCE(a.sat2,""))            
  else CONCAT(COALESCE(a.sat1,"-"),"/",COALESCE(a.sat2,"")) end as Satuan123,            
  case when a.volume_2_forum = 0 then a.volume_1_forum else (a.volume_1_forum * a.volume_2_forum) end as Jml_Satuan,            
  a.harga_satuan_forum as Nilai_Rp, a.jml_belanja_forum as Total,           
  case when a.uraian_tarif_ssh = a.uraian_belanja then a.uraian_tarif_ssh           
  when a.uraian_belanja = "-" then a.uraian_tarif_ssh           
  when COALESCE(a.uraian_belanja," ") = " " then a.uraian_tarif_ssh           
  else substring(CONCAT(a.uraian_tarif_ssh," (",COALESCE(a.uraian_belanja," "),")"),1,255) end as Keterangan           
  , a.sumber_aktivitas, a.id_aktivitas_forum, a.id_aktivitas_asb, a.id_tarif_ssh           
  FROM ( 
-- //  4 
  SELECT f.id_kegiatan,  ab.id_pelaksana_forum, b.id_aktivitas_renja, l.id_rekening, b.group_keu, b.id_aktivitas_forum, b.uraian_aktivitas_kegiatan, a.tahun_forum as tahun, 
  k.Kd_Urusan, j.Kd_Bidang, i.kd_unit, h.kd_sub, f.Kd_Prog, f.id_prog, f.Kd_Keg,            
  l.kd_rek_1, l.kd_rek_2, l.kd_rek_3, l.kd_rek_4, l.kd_rek_5, b.id_aktivitas_forum as No_Rinc,            
  a.id_belanja_forum, a.id_lokasi_forum, a.id_zona_ssh, a.id_belanja_renja, a.sumber_belanja,           
  concat_WS(" ", b.uraian_aktivitas_kegiatan, ab.uraian_lokasi) as AKTIVITAS_LOKASI, ab.uraian_lokasi,          
  a.id_aktivitas_asb, a.id_item_ssh, a.id_rekening_ssh, l.nama_kd_rek_5, aa.uraian_tarif_ssh,            
  a.uraian_belanja, a.volume_1, a.id_satuan_1, a.volume_2, a.id_satuan_2,           
  a.harga_satuan, a.jml_belanja, a.volume_1_forum, a.id_satuan_1_forum,           
  COALESCE(a.volume_2_forum ,0) as volume_2_forum,           
  a.id_satuan_2_forum, a.harga_satuan_forum,           
  a.jml_belanja_forum, a.status_data, a.sumber_data,            
  f.Kd_Urusan AS Kd_Urusan1, f.Kd_Bidang AS Kd_Bidang1, g.uraian_program_renstra as ket_program, b.jenis_kegiatan,           
  COALESCE(e.pagu_tahun_kegiatan,0) as pagu_tahun_kegiatan, b.sumber_dana,           
  COALESCE(e.pagu_forum,0) AS Pagu_Anggaran,           
  substring(cc.uraian_satuan,1,10) as Sat_1, substring(cc.singkatan_satuan,1,15) as Sat1,           
  substring(dd.uraian_satuan,1,10) as Sat_2, substring(dd.singkatan_satuan,1,15) as Sat2           
  , b.sumber_aktivitas, aa.id_tarif_ssh, cc.uraian_satuan as uraian_satuan_1, dd.uraian_satuan as uraian_satuan_2         
  FROM trx_forum_skpd_belanja a INNER JOIN ref_ssh_tarif aa on a.id_item_ssh=aa.id_tarif_ssh          
  LEFT OUTER JOIN ref_satuan cc ON (a.id_satuan_1_forum = cc.id_satuan)            
  LEFT OUTER JOIN ref_satuan dd ON (a.id_satuan_2_forum = dd.id_satuan)            
  INNER JOIN trx_forum_skpd_aktivitas b ON a.id_lokasi_forum = b.id_aktivitas_forum           
  LEFT OUTER JOIN (select a.id_pelaksana_forum,  GROUP_CONCAT(" ",b.nama_lokasi) as uraian_lokasi from trx_forum_skpd_lokasi a  
  inner join ref_lokasi b on a.id_lokasi=b.id_lokasi           
  GROUP BY id_pelaksana_forum) AS ab ON ab.id_pelaksana_forum = b.id_aktivitas_forum           
  INNER JOIN trx_forum_skpd_pelaksana d ON b.id_forum_skpd = d.id_pelaksana_forum           
  INNER JOIN trx_forum_skpd e ON d.id_aktivitas_forum = e.id_forum_skpd           
  INNER JOIN (            
        Select d.Kd_Urusan, c.Kd_Bidang, b.Kd_Program as kd_prog, a.kd_kegiatan as kd_keg, a.id_kegiatan, a.id_program, a.nm_kegiatan,       
        CASE WHEN b.kd_program = 0 then 0 else CONCAT(d.kd_urusan,RIGHT(CONCAT(0,c.kd_bidang),2)) end as id_prog         
   
        FROM ref_kegiatan a      
        INNER JOIN ref_program b ON a.id_program=b.id_program      
        INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang      
        INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan) f      
  ON e.id_kegiatan_ref = f.id_kegiatan            
  INNER JOIN trx_forum_skpd_program g ON e.id_forum_program = g.id_forum_program           
  INNER JOIN ref_sub_unit h on d.id_sub_unit = h.id_sub_unit            
  INNER JOIN ref_unit i on h.id_unit=i.id_unit            
  INNER JOIN ref_bidang j on i.id_bidang=j.id_bidang            
  INNER JOIN ref_urusan k ON j.kd_urusan = k.kd_urusan            
  INNER JOIN ref_rek_5 l on l.id_rekening = a.id_rekening_ssh           

 WHERE
				b.status_pelaksanaan = 0
			AND a.tahun_forum = 2019
			AND l.kd_rek_1 = 5
  ORDER BY k.Kd_Urusan, j.Kd_Bidang, i.kd_unit, h.kd_sub, f.Kd_Prog, f.id_prog, f.Kd_Keg,           
  l.kd_rek_1, l.kd_rek_2, l.kd_rek_3, l.kd_rek_4, l.kd_rek_5, no_rinc    

-- //  4 
  ) a
          
  GROUP BY a.tahun, a.Kd_Urusan, a.Kd_Bidang, a.kd_unit, a.kd_sub, a.Kd_Prog, a.id_prog, a.kd_keg,           
-- //  a.kd_rek_1, a.kd_rek_2, a.kd_rek_3, a.kd_rek_4, a.kd_rek_5 , a.AKTIVITAS_LOKASI, a.sumber_dana, a.id_belanja_forum    
  a.kd_rek_1, a.kd_rek_2, a.kd_rek_3, a.kd_rek_4, a.kd_rek_5 , a.No_Rinc, a.AKTIVITAS_LOKASI, a.id_belanja_forum    
  ORDER BY a.tahun, a.Kd_Urusan, a.Kd_Bidang, a.kd_unit, a.kd_sub, a.Kd_Prog, a.id_prog, a.kd_keg,           
  a.kd_rek_1, a.kd_rek_2, a.kd_rek_3, a.kd_rek_4, a.kd_rek_5, a.No_Rinc     
-- //  3 
  ) a  , (SELECT @row_num := 0, @prev_value := 0) yy   
  ) a ORDER BY a.tahun, a.Kd_Urusan, a.Kd_Bidang, a.kd_unit, a.kd_sub, a.Kd_Prog, a.id_prog, a.kd_keg,   
      a.kd_rek_1, a.kd_rek_2, a.kd_rek_3, a.kd_rek_4, a.kd_rek_5 , a.No_Rinc 
  ) a, (SELECT @row_num := 0, @prev_value := 0) zz , (SELECT @row_num1 := 0, @prev_value1 := 0) yz  order by 6,7,8,9,10,11,12,13,14,15,16,18,21 
-- LIMIT 4000,1000

 ');*/
$BelanjaItem=DB::Select(' SELECT a.id_belanja_forum as id_belanja_renja,b.group_keu,substring(XML_Encode(GantiEnter(b.uraian_aktivitas_kegiatan)),1,255) as uraian_aktivitas_kegiatan,a.tahun_forum as tahun,
k.Kd_Urusan,j.Kd_Bidang, i.kd_unit, h.kd_sub, f.Kd_Prog, f.id_prog, f.Kd_Keg,l.kd_rek_1, l.kd_rek_2, l.kd_rek_3, l.kd_rek_4, l.kd_rek_5,l.nama_kd_rek_5 as nm_rek_5,
b.id_aktivitas_forum, 
substring(XML_Encode(GantiEnter(case when ab.uraian_lokasi <> " " then concat_WS("",XML_Encode(GantiEnter(b.uraian_aktivitas_kegiatan)), " (",ab.uraian_lokasi,")") else XML_Encode(GantiEnter(b.uraian_aktivitas_kegiatan)) end)),1,255) as Keterangan_rinc,              
b.sumber_dana as Kd_Sumber,
substring(cc.uraian_satuan,1,10) as Sat_1, a.volume_1_forum as Nilai_1,  substring(dd.uraian_satuan,1,10) as Sat_2, a.volume_2_forum as Nilai_2,            
  Null as Sat_3, 0 as Nilai_3,            
  case when substring(dd.uraian_satuan,1,10) is NULL then CONCAT(COALESCE(substring(cc.uraian_satuan,1,10),"-"),COALESCE(substring(dd.uraian_satuan,1,10),""))            
  else CONCAT(COALESCE(substring(cc.uraian_satuan,1,10),"-"),"/",COALESCE(substring(dd.uraian_satuan,1,10),"")) end as Satuan123,            
  case when a.volume_2_forum = 0 then a.volume_1_forum else (a.volume_1_forum * a.volume_2_forum) end as Jml_Satuan,
a.harga_satuan_forum AS Nilai_Rp,           
  a.jml_belanja_forum AS Total,
substring(XML_Encode(GantiEnter(case when aa.uraian_tarif_ssh = a.uraian_belanja then aa.uraian_tarif_ssh           
  when a.uraian_belanja = "-" then aa.uraian_tarif_ssh           
  when COALESCE(a.uraian_belanja," ") = " " then aa.uraian_tarif_ssh           
  else CONCAT(aa.uraian_tarif_ssh," (",COALESCE(a.uraian_belanja," "),")") end )),1,255)  as Keterangan,
b.sumber_aktivitas,b.id_aktivitas_renja,a.id_aktivitas_asb,aa.id_tarif_ssh        
  FROM trx_forum_skpd_belanja a INNER JOIN ref_ssh_tarif aa on a.id_item_ssh=aa.id_tarif_ssh          
  LEFT OUTER JOIN ref_satuan cc ON (a.id_satuan_1_forum = cc.id_satuan)            
  LEFT OUTER JOIN ref_satuan dd ON (a.id_satuan_2_forum = dd.id_satuan)            
  INNER JOIN trx_forum_skpd_aktivitas b ON a.id_lokasi_forum = b.id_aktivitas_forum           
  LEFT OUTER JOIN (select a.id_pelaksana_forum,  GROUP_CONCAT(" ",b.nama_lokasi) as uraian_lokasi from trx_forum_skpd_lokasi a  
  inner join ref_lokasi b on a.id_lokasi=b.id_lokasi           
  GROUP BY id_pelaksana_forum) AS ab ON ab.id_pelaksana_forum = b.id_aktivitas_forum           
  INNER JOIN trx_forum_skpd_pelaksana d ON b.id_forum_skpd = d.id_pelaksana_forum           
  INNER JOIN trx_forum_skpd e ON d.id_aktivitas_forum = e.id_forum_skpd           
  INNER JOIN (            
        Select d.Kd_Urusan, c.Kd_Bidang, b.Kd_Program as kd_prog, a.kd_kegiatan as kd_keg, a.id_kegiatan, a.id_program, a.nm_kegiatan,       
        CASE WHEN b.kd_program = 0 then 0 else CONCAT(d.kd_urusan,RIGHT(CONCAT(0,c.kd_bidang),2)) end as id_prog         
   
        FROM ref_kegiatan a      
        INNER JOIN ref_program b ON a.id_program=b.id_program      
        INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang      
        INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan) f      
  ON e.id_kegiatan_ref = f.id_kegiatan            
  INNER JOIN trx_forum_skpd_program g ON e.id_forum_program = g.id_forum_program           
  INNER JOIN ref_sub_unit h on d.id_sub_unit = h.id_sub_unit            
  INNER JOIN ref_unit i on h.id_unit=i.id_unit            
  INNER JOIN ref_bidang j on i.id_bidang=j.id_bidang            
  INNER JOIN ref_urusan k ON j.kd_urusan = k.kd_urusan            
  INNER JOIN ref_rek_5 l on l.id_rekening = a.id_rekening_ssh           

 WHERE
				b.status_pelaksanaan = 0
			AND a.tahun_forum = 2019
			AND l.kd_rek_1 = 5
  ORDER BY k.Kd_Urusan, j.Kd_Bidang, i.kd_unit, h.kd_sub, f.Kd_Prog, f.id_prog, f.Kd_Keg,           
  l.kd_rek_1, l.kd_rek_2, l.kd_rek_3, l.kd_rek_4, l.kd_rek_5, id_aktivitas_forum   ');
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><mydoc></mydoc>');
        
        $xml->addAttribute('Created', 'by Simd@');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
       
        $tabel3 = $xml->addChild('tabel');
        $tabel3->addAttribute('tabel', 'Ta_Belanja_Item');
		$prev1=1;
		$prev2=1;
		$prev3=1;
		$strprev1=0;
		$strprev2=0;
		$strprev3=0;
		//$norinc=1;
        foreach ($BelanjaItem as $unit) {
			if($strprev1<>$unit->id_aktivitas_forum)
			{
			$prev1=$prev1+1;
			}
			else
			{
			$prev1=$prev1;	
			}
			$strprev1=$unit->id_aktivitas_forum;
			if($strprev2==$prev1)
			{
			$prev2=$prev2+1;
			}
			else
			{
			$prev2=1;	
			}
			$strprev2=$prev1;
			if($strprev3==$strprev1 and $unit->group_keu==1)
			{
				$prev3=$prev3;
			}
			else
			{
				$prev3=$prev3+1;
			}
			$strprev3=$strprev1;
            $data = $tabel3->addChild('data');
            $data->addChild('id_item_can', $prev3);
            $data->addChild('id_belanja_renja', $unit->id_belanja_renja);
            $data->addChild('group_keu', $unit->group_keu);
            $data->addChild('uraian_aktivitas_kegiatan', $unit->uraian_aktivitas_kegiatan);
            $data->addChild('Tahun', $unit->tahun);
            $data->addChild('Kd_Urusan', $unit->Kd_Urusan);
            $data->addChild('Kd_Bidang', $unit->Kd_Bidang);
            $data->addChild('Kd_Unit', $unit->kd_unit);
            $data->addChild('Kd_Sub', $unit->kd_sub);
            $data->addChild('Kd_Prog', $unit->kd_prog);
            $data->addChild('ID_Prog', $unit->id_prog);
            $data->addChild('Kd_Keg', $unit->kd_keg);
            $data->addChild('Kd_Rek_1', $unit->kd_rek_1);
            $data->addChild('Kd_Rek_2', $unit->kd_rek_2);
            $data->addChild('Kd_Rek_3', $unit->kd_rek_3);
            $data->addChild('Kd_Rek_4', $unit->kd_rek_4);
            $data->addChild('Kd_Rek_5', $unit->kd_rek_5);
            $data->addChild('Nm_Rek_5', $unit->nm_rek_5);
            $data->addChild('No_Rinc', $prev1);
			$data->addChild('No_ID', $prev2);
			$data->addChild('id_aktivitas_forum', $unit->id_aktivitas_forum);
            $data->addChild('Keterangan_rinc', $unit->Keterangan_rinc);
            $data->addChild('Kd_Sumber', $unit->Kd_Sumber);
            
            //$data->addChild('ID_RINC_BLJ', $unit->ID_RINC_BLJ);
            //$data->addChild('ID_RINC_BLJ1', $unit->ID_RINC_BLJ1);
            $data->addChild('Sat_1', $unit->Sat_1);
            $data->addChild('Nilai_1', $unit->Nilai_1);
            $data->addChild('Sat_2', $unit->Sat_2);
            $data->addChild('Nilai_2', $unit->Nilai_2);
            $data->addChild('Sat_3', $unit->Sat_3);
            $data->addChild('Nilai_3', $unit->Nilai_3);
            $data->addChild('Satuan123', $unit->Satuan123);
            $data->addChild('Jml_Satuan', $unit->Jml_Satuan);
            $data->addChild('Nilai_Rp', $unit->Nilai_Rp);
            $data->addChild('Total', $unit->Total);
            $data->addChild('Keterangan', $unit->Keterangan);
            $data->addChild('sumber_aktivitas', $unit->sumber_aktivitas);
            $data->addChild('id_aktivitas_renja', $unit->id_aktivitas_renja);
            $data->addChild('id_aktivitas_asb', $unit->id_aktivitas_asb);
            $data->addChild('id_tarif_ssh', $unit->id_tarif_ssh);
            
        }
        
        $response = Response::make($xml->asXML(), 200);
        $response->header('Cache-Control', 'public');
        $response->header('Content-Description', 'File Transfer');
        $response->header('Content-Disposition', 'attachment; filename=' . TIME() . '.xml');
        $response->header('Content-Transfer-Encoding', 'binary');
        $response->header('Content-Type', 'text/xml');
        
        return $response;
    }
	
	public function getTaPendapatan(Request $request)
    {
        $unit = DB::SELECT('SELECT a.tahun, a.Kd_Urusan, a.Kd_Bidang, a.kd_unit, a.kd_sub, a.Kd_Prog, a.id_prog, a.Kd_Keg, a.kd_rek_1,
			a.kd_rek_2, a.kd_rek_3, a.kd_rek_4, a.kd_rek_5, a.kd_rek_2 AS Kd_Pendapatan,
			CASE max(sumber_dana) WHEN 0 THEN 1 ELSE max(sumber_dana) END AS Kd_Sumber
			FROM (
			SELECT a.tahun_anggaran AS tahun, k.Kd_Urusan, j.Kd_Bidang, i.kd_unit, h.kd_sub, f.Kd_Prog, f.id_prog, f.Kd_Keg, f.nm_kegiatan,
				l.kd_rek_1, l.kd_rek_2, l.kd_rek_3, l.kd_rek_4, l.kd_rek_5, a.id_belanja_pd as id_belanja_forum, a.id_aktivitas_pd as id_lokasi_forum,
				a.id_zona_ssh, a.id_belanja_pd as id_belanja_renja, a.sumber_belanja, ab.uraian_lokasi,
				concat_WS(" ", XML_Encode(GantiEnter(b.uraian_aktivitas_kegiatan)), ab.uraian_lokasi) AS AKTIVITAS_LOKASI,
				a.id_aktivitas_asb, a.id_item_ssh, a.id_rekening_ssh, l.nama_kd_rek_5, a.uraian_belanja, a.volume_1, a.id_satuan_1, a.volume_2,
				a.id_satuan_2, a.harga_satuan, a.jml_belanja, a.volume_1 as volume_1_forum, a.id_satuan_1 as id_satuan_1_forum,
				a.volume_2 as volume_2_forum, a.id_satuan_2 as id_satuan_2_forum, a.harga_satuan as harga_satuan_forum,
				COALESCE (a.jml_belanja, 0) AS jml_belanja_forum, a.status_data, a.sumber_data, f.Kd_Urusan AS Kd_Urusan1, f.Kd_Bidang AS Kd_Bidang1,
				g.uraian_program_renstra AS ket_program, gx.tolok_ukur_indikator AS Tolak_Ukur, gx.target_renja AS Target_Angka, xx.uraian_satuan AS Target_Uraian,
				b.jenis_kegiatan, COALESCE (e.pagu_tahun_kegiatan, 0) AS pagu_tahun_kegiatan, b.sumber_dana, COALESCE (e.pagu_forum) AS Pagu_Anggaran
			FROM trx_anggaran_belanja_pd AS a
			INNER JOIN trx_anggaran_aktivitas_pd AS b ON a.id_aktivitas_pd = b.id_aktivitas_pd
			LEFT OUTER JOIN ( SELECT id_aktivitas_pd, GROUP_CONCAT(" ", uraian_lokasi) AS uraian_lokasi
			FROM trx_anggaran_lokasi_pd GROUP BY id_aktivitas_pd ) AS ab ON ab.id_aktivitas_pd = b.id_aktivitas_pd
		INNER JOIN trx_anggaran_pelaksana_pd AS d ON b.id_pelaksana_pd = d.id_pelaksana_pd
		INNER JOIN trx_anggaran_kegiatan_pd AS e ON d.id_kegiatan_pd = e.id_kegiatan_pd
		INNER JOIN (SELECT d.Kd_Urusan, c.Kd_Bidang, b.Kd_Program AS kd_prog, a.kd_kegiatan AS kd_keg, a.id_kegiatan, a.id_program, a.nm_kegiatan,
				CASE WHEN b.kd_program = 0 THEN 0 ELSE CONCAT( d.kd_urusan, RIGHT (CONCAT(0, c.kd_bidang), 2)) END AS id_prog
			FROM ref_kegiatan a
			INNER JOIN ref_program b ON a.id_program = b.id_program
			INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang
			INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan
		) AS f ON e.id_kegiatan_ref = f.id_kegiatan
		INNER JOIN trx_anggaran_program_pd AS g ON e.id_program_pd = g.id_program_pd
		LEFT JOIN trx_anggaran_prog_indikator_pd AS gx ON gx.id_program_pd = g.id_program_pd

		LEFT JOIN ref_satuan xx ON gx.id_satuan_output = xx.id_satuan
		INNER JOIN ref_sub_unit h ON d.id_sub_unit = h.id_sub_unit
		INNER JOIN ref_unit i ON h.id_unit = i.id_unit
		INNER JOIN ref_bidang j ON i.id_bidang = j.id_bidang
		INNER JOIN ref_urusan k ON j.kd_urusan = k.kd_urusan
		INNER JOIN ref_rek_5 l ON l.id_rekening = a.id_rekening_ssh
		WHERE
			a.tahun_anggaran = 2019 and g.id_dokumen_keu=4 -- +jika+
	) a
GROUP BY
	a.tahun,
	a.Kd_Urusan,
	a.Kd_Bidang,
	a.kd_unit,
	a.kd_sub,
	a.Kd_Prog,
	a.id_prog,
	a.kd_keg,
	a.kd_rek_1,
	a.kd_rek_2,
	a.kd_rek_3,
	a.kd_rek_4,
	a.kd_rek_5
HAVING
	a.kd_rek_1 = 4
 ');
 
 $PendRinc = DB::SELECT('SELECT
	a.Tahun,
	a.Kd_Urusan,
	a.Kd_Bidang,
	a.Kd_Unit,
	a.id_sub_unit,
	a.Kd_Sub,
	a.Kd_Prog,
	a.ID_Prog,
	a.Kd_Keg,
	a.Kd_Rek_1,
	a.Kd_Rek_2,
	a.Kd_Rek_3,
	a.Kd_Rek_4,
	a.Kd_Rek_5,
	a.id_rekening,
	a.Sat_1,
	a.volume_1_forum AS Nilai_1,
	a.Sat_2,
	COALESCE (a.volume_2_forum, 0) AS Nilai_2,
	NULL AS Sat_3,
	1 AS Nilai_3,
	CASE
WHEN a.sat2 IS NULL THEN
	CONCAT(
		COALESCE (a.sat1, "-"),
		COALESCE (a.sat2, "")
	)
ELSE
	CONCAT(
		COALESCE (a.sat1, "-"),
		"/",
		COALESCE (a.sat2, "")
	)
END AS Satuan123,
 CASE
WHEN a.volume_2_forum = 0 THEN
	a.volume_1_forum
ELSE
	(
		a.volume_1_forum * a.volume_2_forum
	)
END AS Jml_Satuan,
 a.harga_satuan_forum AS Nilai_Rp,
 a.jml_belanja_forum AS Total,
 XML_Encode (
	GantiEnter (a.uraian_tarif_ssh)
) AS Keterangan
FROM
	(
		SELECT
			a.tahun_anggaran AS tahun,
			k.Kd_Urusan,
			j.Kd_Bidang,
			i.kd_unit,
			h.id_sub_unit,
			h.kd_sub,
			f.Kd_Prog,
			f.id_prog,
			f.Kd_Keg,
			l.kd_rek_1,
			l.kd_rek_2,
			l.kd_rek_3,
			l.kd_rek_4,
			l.kd_rek_5,
			l.id_rekening,
			a.id_belanja_pd as id_belanja_forum,
			a.id_aktivitas_pd as id_lokasi_forum,
			a.id_zona_ssh,
			a.id_belanja_rkpd_final as id_belanja_renja,
			a.sumber_belanja,
			a.id_aktivitas_asb,
			a.id_item_ssh,
			a.id_rekening_ssh,
			l.nama_kd_rek_5,
			aa.uraian_tarif_ssh,
			a.uraian_belanja,
			a.volume_1,
			a.id_satuan_1,
			a.volume_2,
			a.id_satuan_2,
			a.harga_satuan,
			a.jml_belanja,
			COALESCE (a.volume_1, 0) AS volume_1_forum,
			a.id_satuan_1 as id_satuan_1_forum,
			COALESCE (a.volume_2, 0) AS volume_2_forum,
			a.id_satuan_2 as id_satuan_2_forum,
			a.harga_satuan as harga_satuan_forum,
			a.jml_belanja as jml_belanja_forum,
			a.status_data,
			a.sumber_data,
			f.Kd_Urusan AS Kd_Urusan1,
			f.Kd_Bidang AS Kd_Bidang1,
			g.uraian_program_renstra AS ket_program,
			b.jenis_kegiatan,
			COALESCE (e.pagu_tahun_kegiatan, 0) AS pagu_tahun_kegiatan,
			b.sumber_dana,
			COALESCE (e.pagu_forum, 0) AS Pagu_Anggaran,
			substring(cc.uraian_satuan, 1, 10) AS Sat_1,
			substring(cc.singkatan_satuan, 1, 15) AS Sat1,
			substring(dd.uraian_satuan, 1, 10) AS Sat_2,
			substring(dd.singkatan_satuan, 1, 15) AS Sat2
		FROM
				trx_anggaran_belanja_pd AS a
INNER JOIN ref_ssh_tarif aa on a.id_item_ssh=aa.id_tarif_ssh          
  LEFT OUTER JOIN ref_satuan cc ON (a.id_satuan_1 = cc.id_satuan)            
  LEFT OUTER JOIN ref_satuan dd ON (a.id_satuan_2 = dd.id_satuan)            
		INNER JOIN trx_anggaran_aktivitas_pd AS b ON a.id_aktivitas_pd = b.id_aktivitas_pd
		LEFT OUTER JOIN (
			SELECT
				id_aktivitas_pd,
				GROUP_CONCAT(" ", uraian_lokasi) AS uraian_lokasi
			FROM
				trx_anggaran_lokasi_pd
			GROUP BY
				id_aktivitas_pd
		) AS ab ON ab.id_aktivitas_pd = b.id_aktivitas_pd
		INNER JOIN trx_anggaran_pelaksana_pd AS d ON b.id_pelaksana_pd = d.id_pelaksana_pd
		INNER JOIN trx_anggaran_kegiatan_pd AS e ON d.id_kegiatan_pd = e.id_kegiatan_pd
		INNER JOIN (
			SELECT
				d.Kd_Urusan,
				c.Kd_Bidang,
				b.Kd_Program AS kd_prog,
				a.kd_kegiatan AS kd_keg,
				a.id_kegiatan,
				a.id_program,
				a.nm_kegiatan,
				CASE
			WHEN b.kd_program = 0 THEN
				0
			ELSE
				CONCAT(
					d.kd_urusan,
					RIGHT (CONCAT(0, c.kd_bidang), 2)
				)
			END AS id_prog
			FROM
				ref_kegiatan a
			INNER JOIN ref_program b ON a.id_program = b.id_program
			INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang
			INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan
		) AS f ON e.id_kegiatan_ref = f.id_kegiatan
		INNER JOIN trx_anggaran_program_pd AS g ON e.id_program_pd = g.id_program_pd
		INNER JOIN ref_sub_unit h ON d.id_sub_unit = h.id_sub_unit
		INNER JOIN ref_unit i ON h.id_unit = i.id_unit
		INNER JOIN ref_bidang j ON i.id_bidang = j.id_bidang
		INNER JOIN ref_urusan k ON j.kd_urusan = k.kd_urusan
		INNER JOIN ref_rek_5 l ON l.id_rekening = a.id_rekening_ssh
		WHERE
			a.tahun_anggaran = 2019 and g.id_dokumen_keu=4 -- +jika+
		AND l.kd_rek_1 = 4
	) a
GROUP BY
	a.tahun,
	a.Kd_Urusan,
	a.Kd_Bidang,
	a.kd_unit,
	a.kd_sub,
	a.Kd_Prog,
	a.id_prog,
	a.kd_keg,
	a.kd_rek_1,
	a.kd_rek_2,
	a.kd_rek_3,
	a.kd_rek_4,
	a.kd_rek_5,
	a.id_rekening,
	a.id_belanja_forum,
	a.id_sub_unit
 ');
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><mydoc></mydoc>');
        
        $xml->addAttribute('Created', 'by Simd@');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $tabel = $xml->addChild('tabel');
        $tabel->addAttribute('tabel', 'Ta_Pendapatan');
        foreach ($unit as $unit) {
            $data = $tabel->addChild('data');
            $data->addChild('tahun', $unit->tahun);
            $data->addChild('Kd_Urusan', $unit->Kd_Urusan);
            $data->addChild('Kd_Bidang', $unit->Kd_Bidang);
            $data->addChild('kd_unit', $unit->kd_unit);
            $data->addChild('kd_sub', $unit->kd_sub);
            $data->addChild('Kd_Prog', $unit->kd_prog);
            $data->addChild('id_prog', $unit->id_prog);
            $data->addChild('Kd_Keg', $unit->kd_keg);
            $data->addChild('kd_rek_1', $unit->kd_rek_1);
            $data->addChild('kd_rek_2', $unit->kd_rek_2);
            $data->addChild('kd_rek_3', $unit->kd_rek_3);
            $data->addChild('kd_rek_4', $unit->kd_rek_4);
            $data->addChild('kd_rek_5', $unit->kd_rek_5);
            $data->addChild('Kd_Pendapatan', $unit->Kd_Pendapatan);
            $data->addChild('Kd_Sumber', $unit->Kd_Sumber);
        }
        $tabel1 = $xml->addChild('tabel');
        $tabel1->addAttribute('tabel', 'Ta_Pendapatan_Rinc');
		 $prev="";
	$count=1;
	foreach ($PendRinc as $unit) {
	 
           $data = $tabel1->addChild('data');
		   if($prev==$unit->id_sub_unit.'.'.$unit->id_prog.'.'.$unit->kd_keg.'.'.$unit->id_rekening){
            $data->addChild('no_id', $count);
		   }
			else 
			{	$data->addChild('no_id', 1);
				$count=1;
			}
			$count++;
			$data->addChild('Tahun', $unit->tahun);
            $data->addChild('Kd_Urusan', $unit->Kd_Urusan);
            $data->addChild('Kd_Bidang', $unit->Kd_Bidang);
            $data->addChild('kd_unit', $unit->kd_unit);
            $data->addChild('kd_sub', $unit->kd_sub);
            $data->addChild('Kd_Prog', $unit->kd_prog);
            $data->addChild('id_prog', $unit->id_prog);
            $data->addChild('Kd_Keg', $unit->kd_keg);
            $data->addChild('kd_rek_1', $unit->kd_rek_1);
            $data->addChild('kd_rek_2', $unit->kd_rek_2);
            $data->addChild('kd_rek_3', $unit->kd_rek_3);
            $data->addChild('kd_rek_4', $unit->kd_rek_4);
            $data->addChild('kd_rek_5', $unit->kd_rek_5);
            $data->addChild('Sat_1', $unit->Sat_1);
            $data->addChild('Nilai_1', $unit->Nilai_1);
			$data->addChild('Sat_2', $unit->Sat_2);
			$data->addChild('Nilai_2', $unit->Nilai_2);
			$data->addChild('Sat_3', $unit->Sat_3);
			$data->addChild('Nilai_3', $unit->Nilai_3);
			$data->addChild('Satuan123', $unit->Satuan123);
			$data->addChild('Jml_Satuan', $unit->Jml_Satuan);
			$data->addChild('Nilai_Rp', $unit->Nilai_Rp);
			$data->addChild('Total', $unit->Total);
			$data->addChild('Keterangan', $unit->Keterangan);
			$prev=$unit->id_sub_unit.'.'.$unit->id_prog.'.'.$unit->kd_keg.'.'.$unit->id_rekening;
        }
        $response = Response::make($xml->asXML(), 200);
        $response->header('Cache-Control', 'public');
        $response->header('Content-Description', 'File Transfer');
        $response->header('Content-Disposition', 'attachment; filename=' . TIME() . '.xml');
        $response->header('Content-Transfer-Encoding', 'binary');
        $response->header('Content-Type', 'text/xml');
        
        return $response;
    }
	
	public function getTaPembiayaan(Request $request)
    {
        $Pemb = DB::SELECT('SELECT
	a.tahun,
	a.Kd_Urusan,
	a.Kd_Bidang,
	a.kd_unit,
	a.kd_sub,
	a.Kd_Prog,
	a.id_prog,
	a.Kd_Keg,
	a.kd_rek_1,
	a.kd_rek_2,
	a.kd_rek_3,
	a.kd_rek_4,
	a.kd_rek_5,
	
	CASE max(sumber_dana)
WHEN 0 THEN
	1
ELSE
	max(sumber_dana)
END AS Kd_Sumber
FROM
	(
		SELECT
			a.tahun_anggaran AS tahun,
			k.Kd_Urusan,
			j.Kd_Bidang,
			i.kd_unit,
			h.kd_sub,
			f.Kd_Prog,
			f.id_prog,
			f.Kd_Keg,
			f.nm_kegiatan,
			l.kd_rek_1,
			l.kd_rek_2,
			l.kd_rek_3,
			l.kd_rek_4,
			l.kd_rek_5,
			a.id_belanja_pd as id_belanja_forum,
			a.id_aktivitas_pd as id_lokasi_forum,
			a.id_zona_ssh,
			a.id_belanja_pd as id_belanja_renja,
			a.sumber_belanja,
			ab.uraian_lokasi,
			concat_WS(
				" ",
				XML_Encode(GantiEnter(b.uraian_aktivitas_kegiatan)),
				ab.uraian_lokasi
			) AS AKTIVITAS_LOKASI,
			a.id_aktivitas_asb,
			a.id_item_ssh,
			a.id_rekening_ssh,
			l.nama_kd_rek_5,
			a.uraian_belanja,
			a.volume_1,
			a.id_satuan_1,
			a.volume_2,
			a.id_satuan_2,
			a.harga_satuan,
			a.jml_belanja,
			a.volume_1 as volume_1_forum,
			a.id_satuan_1 as id_satuan_1_forum,
			a.volume_2 as volume_2_forum,
			a.id_satuan_2 as id_satuan_2_forum,
			a.harga_satuan as harga_satuan_forum,
			COALESCE (a.jml_belanja, 0) AS jml_belanja_forum,
			a.status_data,
			a.sumber_data,
			f.Kd_Urusan AS Kd_Urusan1,
			f.Kd_Bidang AS Kd_Bidang1,
			g.uraian_program_renstra AS ket_program,
			gx.tolok_ukur_indikator AS Tolak_Ukur,
			gx.target_renja AS Target_Angka,
			xx.uraian_satuan AS Target_Uraian,
			b.jenis_kegiatan,
			COALESCE (e.pagu_tahun_kegiatan, 0) AS pagu_tahun_kegiatan,
			b.sumber_dana,
			COALESCE (e.pagu_forum) AS Pagu_Anggaran
		FROM
						trx_anggaran_belanja_pd AS a
		INNER JOIN trx_anggaran_aktivitas_pd AS b ON a.id_aktivitas_pd = b.id_aktivitas_pd
		LEFT OUTER JOIN (
			SELECT
				id_aktivitas_pd,
				GROUP_CONCAT(" ", uraian_lokasi) AS uraian_lokasi
			FROM
				trx_anggaran_lokasi_pd
			GROUP BY
				id_aktivitas_pd
		) AS ab ON ab.id_aktivitas_pd = b.id_aktivitas_pd
		INNER JOIN trx_anggaran_pelaksana_pd AS d ON b.id_pelaksana_pd = d.id_pelaksana_pd
		INNER JOIN trx_anggaran_kegiatan_pd AS e ON d.id_kegiatan_pd = e.id_kegiatan_pd
		INNER JOIN (
			SELECT
				d.Kd_Urusan,
				c.Kd_Bidang,
				b.Kd_Program AS kd_prog,
				a.kd_kegiatan AS kd_keg,
				a.id_kegiatan,
				a.id_program,
				a.nm_kegiatan,
				CASE
			WHEN b.kd_program = 0 THEN
				0
			ELSE
				CONCAT(
					d.kd_urusan,
					RIGHT (CONCAT(0, c.kd_bidang), 2)
				)
			END AS id_prog
			FROM
				ref_kegiatan a
			INNER JOIN ref_program b ON a.id_program = b.id_program
			INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang
			INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan
		) AS f ON e.id_kegiatan_ref = f.id_kegiatan
		INNER JOIN trx_anggaran_program_pd AS g ON e.id_program_pd = g.id_program_pd
		LEFT JOIN trx_anggaran_prog_indikator_pd AS gx ON gx.id_program_pd = g.id_program_pd

		LEFT JOIN ref_satuan xx ON gx.id_satuan_output = xx.id_satuan
		INNER JOIN ref_sub_unit h ON d.id_sub_unit = h.id_sub_unit
		INNER JOIN ref_unit i ON h.id_unit = i.id_unit
		INNER JOIN ref_bidang j ON i.id_bidang = j.id_bidang
		INNER JOIN ref_urusan k ON j.kd_urusan = k.kd_urusan
		INNER JOIN ref_rek_5 l ON l.id_rekening = a.id_rekening_ssh
		WHERE
			a.tahun_anggaran = 2019 -- +jika+
	) a
GROUP BY
	a.tahun,
	a.Kd_Urusan,
	a.Kd_Bidang,
	a.kd_unit,
	a.kd_sub,
	a.Kd_Prog,
	a.id_prog,
	a.kd_keg,
	a.kd_rek_1,
	a.kd_rek_2,
	a.kd_rek_3,
	a.kd_rek_4,
	a.kd_rek_5
HAVING
	a.kd_rek_1 = 6
 ');
 
 $PembRinc = DB::SELECT('SELECT
	a.Tahun,
	a.Kd_Urusan,
	a.Kd_Bidang,
	a.Kd_Unit,
	a.id_sub_unit,
	a.Kd_Sub,
	a.Kd_Prog,
	a.ID_Prog,
	a.Kd_Keg,
	a.Kd_Rek_1,
	a.Kd_Rek_2,
	a.Kd_Rek_3,
	a.Kd_Rek_4,
	a.Kd_Rek_5,
	a.id_rekening,
	a.Sat_1,
	a.volume_1_forum AS Nilai_1,
	a.Sat_2,
	COALESCE (a.volume_2_forum, 0) AS Nilai_2,
	NULL AS Sat_3,
	1 AS Nilai_3,
	CASE
WHEN a.sat2 IS NULL THEN
	CONCAT(
		COALESCE (a.sat1, "-"),
		COALESCE (a.sat2, "")
	)
ELSE
	CONCAT(
		COALESCE (a.sat1, "-"),
		"/",
		COALESCE (a.sat2, "")
	)
END AS Satuan123,
 CASE
WHEN a.volume_2_forum = 0 THEN
	a.volume_1_forum
ELSE
	(
		a.volume_1_forum * a.volume_2_forum
	)
END AS Jml_Satuan,
 a.harga_satuan_forum AS Nilai_Rp,
 a.jml_belanja_forum AS Total,
 XML_Encode (
	GantiEnter (a.uraian_tarif_ssh)
) AS Keterangan
FROM
	(
		SELECT
			a.tahun_anggaran AS tahun,
			k.Kd_Urusan,
			j.Kd_Bidang,
			i.kd_unit,
			h.id_sub_unit,
			h.kd_sub,
			f.Kd_Prog,
			f.id_prog,
			f.Kd_Keg,
			l.kd_rek_1,
			l.kd_rek_2,
			l.kd_rek_3,
			l.kd_rek_4,
			l.kd_rek_5,
			l.id_rekening,
			a.id_belanja_pd as id_belanja_forum,
			a.id_aktivitas_pd as id_lokasi_forum,
			a.id_zona_ssh,
			a.id_belanja_rkpd_final as id_belanja_renja,
			a.sumber_belanja,
			a.id_aktivitas_asb,
			a.id_item_ssh,
			a.id_rekening_ssh,
			l.nama_kd_rek_5,
			aa.uraian_tarif_ssh,
			a.uraian_belanja,
			a.volume_1,
			a.id_satuan_1,
			a.volume_2,
			a.id_satuan_2,
			a.harga_satuan,
			a.jml_belanja,
			COALESCE (a.volume_1, 0) AS volume_1_forum,
			a.id_satuan_1 as id_satuan_1_forum,
			COALESCE (a.volume_2, 0) AS volume_2_forum,
			a.id_satuan_2 as id_satuan_2_forum,
			a.harga_satuan as harga_satuan_forum,
			a.jml_belanja as jml_belanja_forum,
			a.status_data,
			a.sumber_data,
			f.Kd_Urusan AS Kd_Urusan1,
			f.Kd_Bidang AS Kd_Bidang1,
			g.uraian_program_renstra AS ket_program,
			b.jenis_kegiatan,
			COALESCE (e.pagu_tahun_kegiatan, 0) AS pagu_tahun_kegiatan,
			b.sumber_dana,
			COALESCE (e.pagu_forum, 0) AS Pagu_Anggaran,
			substring(cc.uraian_satuan, 1, 10) AS Sat_1,
			substring(cc.singkatan_satuan, 1, 15) AS Sat1,
			substring(dd.uraian_satuan, 1, 10) AS Sat_2,
			substring(dd.singkatan_satuan, 1, 15) AS Sat2
		FROM
				trx_anggaran_belanja_pd AS a
INNER JOIN ref_ssh_tarif aa on a.id_item_ssh=aa.id_tarif_ssh          
  LEFT OUTER JOIN ref_satuan cc ON (a.id_satuan_1 = cc.id_satuan)            
  LEFT OUTER JOIN ref_satuan dd ON (a.id_satuan_2 = dd.id_satuan)            
		INNER JOIN trx_anggaran_aktivitas_pd AS b ON a.id_aktivitas_pd = b.id_aktivitas_pd
		LEFT OUTER JOIN (
			SELECT
				id_aktivitas_pd,
				GROUP_CONCAT(" ", uraian_lokasi) AS uraian_lokasi
			FROM
				trx_anggaran_lokasi_pd
			GROUP BY
				id_aktivitas_pd
		) AS ab ON ab.id_aktivitas_pd = b.id_aktivitas_pd
		INNER JOIN trx_anggaran_pelaksana_pd AS d ON b.id_pelaksana_pd = d.id_pelaksana_pd
		INNER JOIN trx_anggaran_kegiatan_pd AS e ON d.id_kegiatan_pd = e.id_kegiatan_pd
		INNER JOIN (
			SELECT
				d.Kd_Urusan,
				c.Kd_Bidang,
				b.Kd_Program AS kd_prog,
				a.kd_kegiatan AS kd_keg,
				a.id_kegiatan,
				a.id_program,
				a.nm_kegiatan,
				CASE
			WHEN b.kd_program = 0 THEN
				0
			ELSE
				CONCAT(
					d.kd_urusan,
					RIGHT (CONCAT(0, c.kd_bidang), 2)
				)
			END AS id_prog
			FROM
				ref_kegiatan a
			INNER JOIN ref_program b ON a.id_program = b.id_program
			INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang
			INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan
		) AS f ON e.id_kegiatan_ref = f.id_kegiatan
		INNER JOIN trx_anggaran_program_pd AS g ON e.id_program_pd = g.id_program_pd
		INNER JOIN ref_sub_unit h ON d.id_sub_unit = h.id_sub_unit
		INNER JOIN ref_unit i ON h.id_unit = i.id_unit
		INNER JOIN ref_bidang j ON i.id_bidang = j.id_bidang
		INNER JOIN ref_urusan k ON j.kd_urusan = k.kd_urusan
		INNER JOIN ref_rek_5 l ON l.id_rekening = a.id_rekening_ssh
		WHERE
			a.tahun_anggaran = 2019 -- +jika+
		AND l.kd_rek_1 = 6
	) a
GROUP BY
	a.tahun,
	a.Kd_Urusan,
	a.Kd_Bidang,
	a.kd_unit,
	a.kd_sub,
	a.Kd_Prog,
	a.id_prog,
	a.kd_keg,
	a.kd_rek_1,
	a.kd_rek_2,
	a.kd_rek_3,
	a.kd_rek_4,
	a.kd_rek_5,
	a.id_rekening,
	a.id_belanja_forum,
	a.id_sub_unit
 ');
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><mydoc></mydoc>');
        
        $xml->addAttribute('Created', 'by Simd@');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $tabel = $xml->addChild('tabel');
        $tabel->addAttribute('tabel', 'Ta_Pembiayaan');
        foreach ($Pemb as $unit) {
            $data = $tabel->addChild('data');
            $data->addChild('tahun', $unit->tahun);
            $data->addChild('Kd_Urusan', $unit->Kd_Urusan);
            $data->addChild('Kd_Bidang', $unit->Kd_Bidang);
            $data->addChild('kd_unit', $unit->kd_unit);
            $data->addChild('kd_sub', $unit->kd_sub);
            $data->addChild('Kd_Prog', $unit->kd_prog);
            $data->addChild('id_prog', $unit->id_prog);
            $data->addChild('Kd_Keg', $unit->kd_keg);
            $data->addChild('kd_rek_1', $unit->kd_rek_1);
            $data->addChild('kd_rek_2', $unit->kd_rek_2);
            $data->addChild('kd_rek_3', $unit->kd_rek_3);
            $data->addChild('kd_rek_4', $unit->kd_rek_4);
            $data->addChild('kd_rek_5', $unit->kd_rek_5);
            
            $data->addChild('Kd_Sumber', $unit->Kd_Sumber);
        }
        $tabel1 = $xml->addChild('tabel');
        $tabel1->addAttribute('tabel', 'Ta_Pembiayaan_Rinc');
		 $prev="";
	$count=1;
	foreach ($PembRinc as $unit) {
	 
           $data = $tabel1->addChild('data');
		   if($prev==$unit->id_sub_unit.'.'.$unit->id_prog.'.'.$unit->kd_keg.'.'.$unit->id_rekening){
            $data->addChild('no_id', $count);
		   }
			else 
			{	$data->addChild('no_id', 1);
				$count=1;
			}
			$count++;
			$data->addChild('Tahun', $unit->tahun);
            $data->addChild('Kd_Urusan', $unit->Kd_Urusan);
            $data->addChild('Kd_Bidang', $unit->Kd_Bidang);
            $data->addChild('kd_unit', $unit->kd_unit);
            $data->addChild('kd_sub', $unit->kd_sub);
            $data->addChild('Kd_Prog', $unit->kd_prog);
            $data->addChild('id_prog', $unit->id_prog);
            $data->addChild('Kd_Keg', $unit->kd_keg);
            $data->addChild('kd_rek_1', $unit->kd_rek_1);
            $data->addChild('kd_rek_2', $unit->kd_rek_2);
            $data->addChild('kd_rek_3', $unit->kd_rek_3);
            $data->addChild('kd_rek_4', $unit->kd_rek_4);
            $data->addChild('kd_rek_5', $unit->kd_rek_5);
            $data->addChild('Sat_1', $unit->Sat_1);
            $data->addChild('Nilai_1', $unit->Nilai_1);
			$data->addChild('Sat_2', $unit->Sat_2);
			$data->addChild('Nilai_2', $unit->Nilai_2);
			$data->addChild('Sat_3', $unit->Sat_3);
			$data->addChild('Nilai_3', $unit->Nilai_3);
			$data->addChild('Satuan123', $unit->Satuan123);
			$data->addChild('Jml_Satuan', $unit->Jml_Satuan);
			$data->addChild('Nilai_Rp', $unit->Nilai_Rp);
			$data->addChild('Total', $unit->Total);
			$data->addChild('Keterangan', $unit->Keterangan);
			$prev=$unit->id_sub_unit.'.'.$unit->id_prog.'.'.$unit->kd_keg.'.'.$unit->id_rekening;
        }
        $response = Response::make($xml->asXML(), 200);
        $response->header('Cache-Control', 'public');
        $response->header('Content-Description', 'File Transfer');
        $response->header('Content-Disposition', 'attachment; filename=' . TIME() . '.xml');
        $response->header('Content-Transfer-Encoding', 'binary');
        $response->header('Content-Type', 'text/xml');
        
        return $response;
    }
	
    public function getTaPendapatan_u(Request $request,$unit)
    {
        $unit = DB::SELECT('SELECT
	a.tahun,
	a.Kd_Urusan,
	a.Kd_Bidang,
	a.kd_unit,
	a.kd_sub,
	a.Kd_Prog,
	a.id_prog,
	a.Kd_Keg,
	a.kd_rek_1,
	a.kd_rek_2,
	a.kd_rek_3,
	a.kd_rek_4,
	a.kd_rek_5,
	a.kd_rek_2 AS Kd_Pendapatan,
	CASE max(sumber_dana)
WHEN 0 THEN
	1
ELSE
	max(sumber_dana)
END AS Kd_Sumber
FROM
	(
		SELECT
			a.tahun_anggaran AS tahun,
			k.Kd_Urusan,
			j.Kd_Bidang,
			i.kd_unit,
			h.kd_sub,
			f.Kd_Prog,
			f.id_prog,
			f.Kd_Keg,
			f.nm_kegiatan,
			l.kd_rek_1,
			l.kd_rek_2,
			l.kd_rek_3,
			l.kd_rek_4,
			l.kd_rek_5,
			a.id_belanja_pd as id_belanja_forum,
			a.id_aktivitas_pd as id_lokasi_forum,
			a.id_zona_ssh,
			a.id_belanja_pd as id_belanja_renja,
			a.sumber_belanja,
			ab.uraian_lokasi,
			concat_WS(
				" ",
				XML_Encode(GantiEnter(b.uraian_aktivitas_kegiatan)),
				ab.uraian_lokasi
			) AS AKTIVITAS_LOKASI,
			a.id_aktivitas_asb,
			a.id_item_ssh,
			a.id_rekening_ssh,
			l.nama_kd_rek_5,
			a.uraian_belanja,
			a.volume_1,
			a.id_satuan_1,
			a.volume_2,
			a.id_satuan_2,
			a.harga_satuan,
			a.jml_belanja,
			a.volume_1 as volume_1_forum,
			a.id_satuan_1 as id_satuan_1_forum,
			a.volume_2 as volume_2_forum,
			a.id_satuan_2 as id_satuan_2_forum,
			a.harga_satuan as harga_satuan_forum,
			COALESCE (a.jml_belanja, 0) AS jml_belanja_forum,
			a.status_data,
			a.sumber_data,
			f.Kd_Urusan AS Kd_Urusan1,
			f.Kd_Bidang AS Kd_Bidang1,
			g.uraian_program_renstra AS ket_program,
			gx.tolok_ukur_indikator AS Tolak_Ukur,
			gx.target_renja AS Target_Angka,
			xx.uraian_satuan AS Target_Uraian,
			b.jenis_kegiatan,
			COALESCE (e.pagu_tahun_kegiatan, 0) AS pagu_tahun_kegiatan,
			b.sumber_dana,
			COALESCE (e.pagu_forum) AS Pagu_Anggaran
		FROM
						trx_anggaran_belanja_pd AS a
		INNER JOIN trx_anggaran_aktivitas_pd AS b ON a.id_aktivitas_pd = b.id_aktivitas_pd
		LEFT OUTER JOIN (
			SELECT
				id_aktivitas_pd,
				GROUP_CONCAT(" ", uraian_lokasi) AS uraian_lokasi
			FROM
				trx_anggaran_lokasi_pd
			GROUP BY
				id_aktivitas_pd
		) AS ab ON ab.id_aktivitas_pd = b.id_aktivitas_pd
		INNER JOIN trx_anggaran_pelaksana_pd AS d ON b.id_pelaksana_pd = d.id_pelaksana_pd
		INNER JOIN trx_anggaran_kegiatan_pd AS e ON d.id_kegiatan_pd = e.id_kegiatan_pd
		INNER JOIN (
			SELECT
				d.Kd_Urusan,
				c.Kd_Bidang,
				b.Kd_Program AS kd_prog,
				a.kd_kegiatan AS kd_keg,
				a.id_kegiatan,
				a.id_program,
				a.nm_kegiatan,
				CASE
			WHEN b.kd_program = 0 THEN
				0
			ELSE
				CONCAT(
					d.kd_urusan,
					RIGHT (CONCAT(0, c.kd_bidang), 2)
				)
			END AS id_prog
			FROM
				ref_kegiatan a
			INNER JOIN ref_program b ON a.id_program = b.id_program
			INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang
			INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan
		) AS f ON e.id_kegiatan_ref = f.id_kegiatan
		INNER JOIN trx_anggaran_program_pd AS g ON e.id_program_pd = g.id_program_pd
		LEFT JOIN trx_anggaran_prog_indikator_pd AS gx ON gx.id_program_pd = g.id_program_pd
            
		LEFT JOIN ref_satuan xx ON gx.id_satuan_output = xx.id_satuan
		INNER JOIN ref_sub_unit h ON d.id_sub_unit = h.id_sub_unit
		INNER JOIN ref_unit i ON h.id_unit = i.id_unit
		INNER JOIN ref_bidang j ON i.id_bidang = j.id_bidang
		INNER JOIN ref_urusan k ON j.kd_urusan = k.kd_urusan
		INNER JOIN ref_rek_5 l ON l.id_rekening = a.id_rekening_ssh
		WHERE
			a.tahun_anggaran = 2019 and g.id_dokumen_keu=4 
            and concat(a.Kd_Urusan,".", a.Kd_Bidang,".", a.kd_unit)="'.$unit.'" -- +jika+
	) a
GROUP BY
	a.tahun,
	a.Kd_Urusan,
	a.Kd_Bidang,
	a.kd_unit,
	a.kd_sub,
	a.Kd_Prog,
	a.id_prog,
	a.kd_keg,
	a.kd_rek_1,
	a.kd_rek_2,
	a.kd_rek_3,
	a.kd_rek_4,
	a.kd_rek_5
HAVING
	a.kd_rek_1 = 4
 ');
        
        $PendRinc = DB::SELECT('SELECT
	a.Tahun,
	a.Kd_Urusan,
	a.Kd_Bidang,
	a.Kd_Unit,
	a.id_sub_unit,
	a.Kd_Sub,
	a.Kd_Prog,
	a.ID_Prog,
	a.Kd_Keg,
	a.Kd_Rek_1,
	a.Kd_Rek_2,
	a.Kd_Rek_3,
	a.Kd_Rek_4,
	a.Kd_Rek_5,
	a.id_rekening,
	a.Sat_1,
	a.volume_1_forum AS Nilai_1,
	a.Sat_2,
	COALESCE (a.volume_2_forum, 0) AS Nilai_2,
	NULL AS Sat_3,
	1 AS Nilai_3,
	CASE
WHEN a.sat2 IS NULL THEN
	CONCAT(
		COALESCE (a.sat1, "-"),
		COALESCE (a.sat2, "")
	)
ELSE
	CONCAT(
		COALESCE (a.sat1, "-"),
		"/",
		COALESCE (a.sat2, "")
	)
END AS Satuan123,
 CASE
WHEN a.volume_2_forum = 0 THEN
	a.volume_1_forum
ELSE
	(
		a.volume_1_forum * a.volume_2_forum
	)
END AS Jml_Satuan,
 a.harga_satuan_forum AS Nilai_Rp,
 a.jml_belanja_forum AS Total,
 XML_Encode (
	GantiEnter (a.uraian_tarif_ssh)
) AS Keterangan
FROM
	(
		SELECT
			a.tahun_anggaran AS tahun,
			k.Kd_Urusan,
			j.Kd_Bidang,
			i.kd_unit,
			h.id_sub_unit,
			h.kd_sub,
			f.Kd_Prog,
			f.id_prog,
			f.Kd_Keg,
			l.kd_rek_1,
			l.kd_rek_2,
			l.kd_rek_3,
			l.kd_rek_4,
			l.kd_rek_5,
			l.id_rekening,
			a.id_belanja_pd as id_belanja_forum,
			a.id_aktivitas_pd as id_lokasi_forum,
			a.id_zona_ssh,
			a.id_belanja_rkpd_final as id_belanja_renja,
			a.sumber_belanja,
			a.id_aktivitas_asb,
			a.id_item_ssh,
			a.id_rekening_ssh,
			l.nama_kd_rek_5,
			aa.uraian_tarif_ssh,
			a.uraian_belanja,
			a.volume_1,
			a.id_satuan_1,
			a.volume_2,
			a.id_satuan_2,
			a.harga_satuan,
			a.jml_belanja,
			COALESCE (a.volume_1, 0) AS volume_1_forum,
			a.id_satuan_1 as id_satuan_1_forum,
			COALESCE (a.volume_2, 0) AS volume_2_forum,
			a.id_satuan_2 as id_satuan_2_forum,
			a.harga_satuan as harga_satuan_forum,
			a.jml_belanja as jml_belanja_forum,
			a.status_data,
			a.sumber_data,
			f.Kd_Urusan AS Kd_Urusan1,
			f.Kd_Bidang AS Kd_Bidang1,
			g.uraian_program_renstra AS ket_program,
			b.jenis_kegiatan,
			COALESCE (e.pagu_tahun_kegiatan, 0) AS pagu_tahun_kegiatan,
			b.sumber_dana,
			COALESCE (e.pagu_forum, 0) AS Pagu_Anggaran,
			substring(cc.uraian_satuan, 1, 10) AS Sat_1,
			substring(cc.singkatan_satuan, 1, 15) AS Sat1,
			substring(dd.uraian_satuan, 1, 10) AS Sat_2,
			substring(dd.singkatan_satuan, 1, 15) AS Sat2
		FROM
				trx_anggaran_belanja_pd AS a
INNER JOIN ref_ssh_tarif aa on a.id_item_ssh=aa.id_tarif_ssh
  LEFT OUTER JOIN ref_satuan cc ON (a.id_satuan_1 = cc.id_satuan)
  LEFT OUTER JOIN ref_satuan dd ON (a.id_satuan_2 = dd.id_satuan)
		INNER JOIN trx_anggaran_aktivitas_pd AS b ON a.id_aktivitas_pd = b.id_aktivitas_pd
		LEFT OUTER JOIN (
			SELECT
				id_aktivitas_pd,
				GROUP_CONCAT(" ", uraian_lokasi) AS uraian_lokasi
			FROM
				trx_anggaran_lokasi_pd
			GROUP BY
				id_aktivitas_pd
		) AS ab ON ab.id_aktivitas_pd = b.id_aktivitas_pd
		INNER JOIN trx_anggaran_pelaksana_pd AS d ON b.id_pelaksana_pd = d.id_pelaksana_pd
		INNER JOIN trx_anggaran_kegiatan_pd AS e ON d.id_kegiatan_pd = e.id_kegiatan_pd
		INNER JOIN (
			SELECT
				d.Kd_Urusan,
				c.Kd_Bidang,
				b.Kd_Program AS kd_prog,
				a.kd_kegiatan AS kd_keg,
				a.id_kegiatan,
				a.id_program,
				a.nm_kegiatan,
				CASE
			WHEN b.kd_program = 0 THEN
				0
			ELSE
				CONCAT(
					d.kd_urusan,
					RIGHT (CONCAT(0, c.kd_bidang), 2)
				)
			END AS id_prog
			FROM
				ref_kegiatan a
			INNER JOIN ref_program b ON a.id_program = b.id_program
			INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang
			INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan
		) AS f ON e.id_kegiatan_ref = f.id_kegiatan
		INNER JOIN trx_anggaran_program_pd AS g ON e.id_program_pd = g.id_program_pd
		INNER JOIN ref_sub_unit h ON d.id_sub_unit = h.id_sub_unit
		INNER JOIN ref_unit i ON h.id_unit = i.id_unit
		INNER JOIN ref_bidang j ON i.id_bidang = j.id_bidang
		INNER JOIN ref_urusan k ON j.kd_urusan = k.kd_urusan
		INNER JOIN ref_rek_5 l ON l.id_rekening = a.id_rekening_ssh
		WHERE
			a.tahun_anggaran = 2019 and g.id_dokumen_keu=4 -- +jika+
		AND l.kd_rek_1 = 4 and concat(a.Kd_Urusan,".", a.Kd_Bidang,".", a.kd_unit)="'.$unit.'"
	) a
GROUP BY
	a.tahun,
	a.Kd_Urusan,
	a.Kd_Bidang,
	a.kd_unit,
	a.kd_sub,
	a.Kd_Prog,
	a.id_prog,
	a.kd_keg,
	a.kd_rek_1,
	a.kd_rek_2,
	a.kd_rek_3,
	a.kd_rek_4,
	a.kd_rek_5,
	a.id_rekening,
	a.id_belanja_forum,
	a.id_sub_unit
 ');
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><mydoc></mydoc>');
        
        $xml->addAttribute('Created', 'by Simd@');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $tabel = $xml->addChild('tabel');
        $tabel->addAttribute('tabel', 'Ta_Pendapatan');
        foreach ($unit as $unit) {
            $data = $tabel->addChild('data');
            $data->addChild('tahun', $unit->tahun);
            $data->addChild('Kd_Urusan', $unit->Kd_Urusan);
            $data->addChild('Kd_Bidang', $unit->Kd_Bidang);
            $data->addChild('kd_unit', $unit->kd_unit);
            $data->addChild('kd_sub', $unit->kd_sub);
            $data->addChild('Kd_Prog', $unit->kd_prog);
            $data->addChild('id_prog', $unit->id_prog);
            $data->addChild('Kd_Keg', $unit->kd_keg);
            $data->addChild('kd_rek_1', $unit->kd_rek_1);
            $data->addChild('kd_rek_2', $unit->kd_rek_2);
            $data->addChild('kd_rek_3', $unit->kd_rek_3);
            $data->addChild('kd_rek_4', $unit->kd_rek_4);
            $data->addChild('kd_rek_5', $unit->kd_rek_5);
            $data->addChild('Kd_Pendapatan', $unit->Kd_Pendapatan);
            $data->addChild('Kd_Sumber', $unit->Kd_Sumber);
        }
        $tabel1 = $xml->addChild('tabel');
        $tabel1->addAttribute('tabel', 'Ta_Pendapatan_Rinc');
        $prev="";
        $count=1;
        foreach ($PendRinc as $unit) {
            
            $data = $tabel1->addChild('data');
            if($prev==$unit->id_sub_unit.'.'.$unit->id_prog.'.'.$unit->kd_keg.'.'.$unit->id_rekening){
                $data->addChild('no_id', $count);
            }
            else
            {	$data->addChild('no_id', 1);
            $count=1;
            }
            $count++;
            $data->addChild('Tahun', $unit->tahun);
            $data->addChild('Kd_Urusan', $unit->Kd_Urusan);
            $data->addChild('Kd_Bidang', $unit->Kd_Bidang);
            $data->addChild('kd_unit', $unit->kd_unit);
            $data->addChild('kd_sub', $unit->kd_sub);
            $data->addChild('Kd_Prog', $unit->kd_prog);
            $data->addChild('id_prog', $unit->id_prog);
            $data->addChild('Kd_Keg', $unit->kd_keg);
            $data->addChild('kd_rek_1', $unit->kd_rek_1);
            $data->addChild('kd_rek_2', $unit->kd_rek_2);
            $data->addChild('kd_rek_3', $unit->kd_rek_3);
            $data->addChild('kd_rek_4', $unit->kd_rek_4);
            $data->addChild('kd_rek_5', $unit->kd_rek_5);
            $data->addChild('Sat_1', $unit->Sat_1);
            $data->addChild('Nilai_1', $unit->Nilai_1);
            $data->addChild('Sat_2', $unit->Sat_2);
            $data->addChild('Nilai_2', $unit->Nilai_2);
            $data->addChild('Sat_3', $unit->Sat_3);
            $data->addChild('Nilai_3', $unit->Nilai_3);
            $data->addChild('Satuan123', $unit->Satuan123);
            $data->addChild('Jml_Satuan', $unit->Jml_Satuan);
            $data->addChild('Nilai_Rp', $unit->Nilai_Rp);
            $data->addChild('Total', $unit->Total);
            $data->addChild('Keterangan', $unit->Keterangan);
            $prev=$unit->id_sub_unit.'.'.$unit->id_prog.'.'.$unit->kd_keg.'.'.$unit->id_rekening;
        }
        $response = Response::make($xml->asXML(), 200);
        $response->header('Cache-Control', 'public');
        $response->header('Content-Description', 'File Transfer');
        $response->header('Content-Disposition', 'attachment; filename=' . TIME() . '.xml');
        $response->header('Content-Transfer-Encoding', 'binary');
        $response->header('Content-Type', 'text/xml');
        
        return $response;
    }
    
    public function getTaPembiayaan_u(Request $request,$unit)
    {
        $Pemb = DB::SELECT('SELECT
	a.tahun,
	a.Kd_Urusan,
	a.Kd_Bidang,
	a.kd_unit,
	a.kd_sub,
	a.Kd_Prog,
	a.id_prog,
	a.Kd_Keg,
	a.kd_rek_1,
	a.kd_rek_2,
	a.kd_rek_3,
	a.kd_rek_4,
	a.kd_rek_5,
            
	CASE max(sumber_dana)
WHEN 0 THEN
	1
ELSE
	max(sumber_dana)
END AS Kd_Sumber
FROM
	(
		SELECT
			a.tahun_anggaran AS tahun,
			k.Kd_Urusan,
			j.Kd_Bidang,
			i.kd_unit,
			h.kd_sub,
			f.Kd_Prog,
			f.id_prog,
			f.Kd_Keg,
			f.nm_kegiatan,
			l.kd_rek_1,
			l.kd_rek_2,
			l.kd_rek_3,
			l.kd_rek_4,
			l.kd_rek_5,
			a.id_belanja_pd as id_belanja_forum,
			a.id_aktivitas_pd as id_lokasi_forum,
			a.id_zona_ssh,
			a.id_belanja_pd as id_belanja_renja,
			a.sumber_belanja,
			ab.uraian_lokasi,
			concat_WS(
				" ",
				XML_Encode(GantiEnter(b.uraian_aktivitas_kegiatan)),
				ab.uraian_lokasi
			) AS AKTIVITAS_LOKASI,
			a.id_aktivitas_asb,
			a.id_item_ssh,
			a.id_rekening_ssh,
			l.nama_kd_rek_5,
			a.uraian_belanja,
			a.volume_1,
			a.id_satuan_1,
			a.volume_2,
			a.id_satuan_2,
			a.harga_satuan,
			a.jml_belanja,
			a.volume_1 as volume_1_forum,
			a.id_satuan_1 as id_satuan_1_forum,
			a.volume_2 as volume_2_forum,
			a.id_satuan_2 as id_satuan_2_forum,
			a.harga_satuan as harga_satuan_forum,
			COALESCE (a.jml_belanja, 0) AS jml_belanja_forum,
			a.status_data,
			a.sumber_data,
			f.Kd_Urusan AS Kd_Urusan1,
			f.Kd_Bidang AS Kd_Bidang1,
			g.uraian_program_renstra AS ket_program,
			gx.tolok_ukur_indikator AS Tolak_Ukur,
			gx.target_renja AS Target_Angka,
			xx.uraian_satuan AS Target_Uraian,
			b.jenis_kegiatan,
			COALESCE (e.pagu_tahun_kegiatan, 0) AS pagu_tahun_kegiatan,
			b.sumber_dana,
			COALESCE (e.pagu_forum) AS Pagu_Anggaran
		FROM
						trx_anggaran_belanja_pd AS a
		INNER JOIN trx_anggaran_aktivitas_pd AS b ON a.id_aktivitas_pd = b.id_aktivitas_pd
		LEFT OUTER JOIN (
			SELECT
				id_aktivitas_pd,
				GROUP_CONCAT(" ", uraian_lokasi) AS uraian_lokasi
			FROM
				trx_anggaran_lokasi_pd
			GROUP BY
				id_aktivitas_pd
		) AS ab ON ab.id_aktivitas_pd = b.id_aktivitas_pd
		INNER JOIN trx_anggaran_pelaksana_pd AS d ON b.id_pelaksana_pd = d.id_pelaksana_pd
		INNER JOIN trx_anggaran_kegiatan_pd AS e ON d.id_kegiatan_pd = e.id_kegiatan_pd
		INNER JOIN (
			SELECT
				d.Kd_Urusan,
				c.Kd_Bidang,
				b.Kd_Program AS kd_prog,
				a.kd_kegiatan AS kd_keg,
				a.id_kegiatan,
				a.id_program,
				a.nm_kegiatan,
				CASE
			WHEN b.kd_program = 0 THEN
				0
			ELSE
				CONCAT(
					d.kd_urusan,
					RIGHT (CONCAT(0, c.kd_bidang), 2)
				)
			END AS id_prog
			FROM
				ref_kegiatan a
			INNER JOIN ref_program b ON a.id_program = b.id_program
			INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang
			INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan
		) AS f ON e.id_kegiatan_ref = f.id_kegiatan
		INNER JOIN trx_anggaran_program_pd AS g ON e.id_program_pd = g.id_program_pd
		LEFT JOIN trx_anggaran_prog_indikator_pd AS gx ON gx.id_program_pd = g.id_program_pd
            
		LEFT JOIN ref_satuan xx ON gx.id_satuan_output = xx.id_satuan
		INNER JOIN ref_sub_unit h ON d.id_sub_unit = h.id_sub_unit
		INNER JOIN ref_unit i ON h.id_unit = i.id_unit
		INNER JOIN ref_bidang j ON i.id_bidang = j.id_bidang
		INNER JOIN ref_urusan k ON j.kd_urusan = k.kd_urusan
		INNER JOIN ref_rek_5 l ON l.id_rekening = a.id_rekening_ssh
		WHERE
			a.tahun_anggaran = 2019 and concat(a.Kd_Urusan,".", a.Kd_Bidang,".", a.kd_unit)="'.$unit.'" -- +jika+
	) a
GROUP BY
	a.tahun,
	a.Kd_Urusan,
	a.Kd_Bidang,
	a.kd_unit,
	a.kd_sub,
	a.Kd_Prog,
	a.id_prog,
	a.kd_keg,
	a.kd_rek_1,
	a.kd_rek_2,
	a.kd_rek_3,
	a.kd_rek_4,
	a.kd_rek_5
HAVING
	a.kd_rek_1 = 6
 ');
        
        $PembRinc = DB::SELECT('SELECT
	a.Tahun,
	a.Kd_Urusan,
	a.Kd_Bidang,
	a.Kd_Unit,
	a.id_sub_unit,
	a.Kd_Sub,
	a.Kd_Prog,
	a.ID_Prog,
	a.Kd_Keg,
	a.Kd_Rek_1,
	a.Kd_Rek_2,
	a.Kd_Rek_3,
	a.Kd_Rek_4,
	a.Kd_Rek_5,
	a.id_rekening,
	a.Sat_1,
	a.volume_1_forum AS Nilai_1,
	a.Sat_2,
	COALESCE (a.volume_2_forum, 0) AS Nilai_2,
	NULL AS Sat_3,
	1 AS Nilai_3,
	CASE
WHEN a.sat2 IS NULL THEN
	CONCAT(
		COALESCE (a.sat1, "-"),
		COALESCE (a.sat2, "")
	)
ELSE
	CONCAT(
		COALESCE (a.sat1, "-"),
		"/",
		COALESCE (a.sat2, "")
	)
END AS Satuan123,
 CASE
WHEN a.volume_2_forum = 0 THEN
	a.volume_1_forum
ELSE
	(
		a.volume_1_forum * a.volume_2_forum
	)
END AS Jml_Satuan,
 a.harga_satuan_forum AS Nilai_Rp,
 a.jml_belanja_forum AS Total,
 XML_Encode (
	GantiEnter (a.uraian_tarif_ssh)
) AS Keterangan
FROM
	(
		SELECT
			a.tahun_anggaran AS tahun,
			k.Kd_Urusan,
			j.Kd_Bidang,
			i.kd_unit,
			h.id_sub_unit,
			h.kd_sub,
			f.Kd_Prog,
			f.id_prog,
			f.Kd_Keg,
			l.kd_rek_1,
			l.kd_rek_2,
			l.kd_rek_3,
			l.kd_rek_4,
			l.kd_rek_5,
			l.id_rekening,
			a.id_belanja_pd as id_belanja_forum,
			a.id_aktivitas_pd as id_lokasi_forum,
			a.id_zona_ssh,
			a.id_belanja_rkpd_final as id_belanja_renja,
			a.sumber_belanja,
			a.id_aktivitas_asb,
			a.id_item_ssh,
			a.id_rekening_ssh,
			l.nama_kd_rek_5,
			aa.uraian_tarif_ssh,
			a.uraian_belanja,
			a.volume_1,
			a.id_satuan_1,
			a.volume_2,
			a.id_satuan_2,
			a.harga_satuan,
			a.jml_belanja,
			COALESCE (a.volume_1, 0) AS volume_1_forum,
			a.id_satuan_1 as id_satuan_1_forum,
			COALESCE (a.volume_2, 0) AS volume_2_forum,
			a.id_satuan_2 as id_satuan_2_forum,
			a.harga_satuan as harga_satuan_forum,
			a.jml_belanja as jml_belanja_forum,
			a.status_data,
			a.sumber_data,
			f.Kd_Urusan AS Kd_Urusan1,
			f.Kd_Bidang AS Kd_Bidang1,
			g.uraian_program_renstra AS ket_program,
			b.jenis_kegiatan,
			COALESCE (e.pagu_tahun_kegiatan, 0) AS pagu_tahun_kegiatan,
			b.sumber_dana,
			COALESCE (e.pagu_forum, 0) AS Pagu_Anggaran,
			substring(cc.uraian_satuan, 1, 10) AS Sat_1,
			substring(cc.singkatan_satuan, 1, 15) AS Sat1,
			substring(dd.uraian_satuan, 1, 10) AS Sat_2,
			substring(dd.singkatan_satuan, 1, 15) AS Sat2
		FROM
				trx_anggaran_belanja_pd AS a
INNER JOIN ref_ssh_tarif aa on a.id_item_ssh=aa.id_tarif_ssh
  LEFT OUTER JOIN ref_satuan cc ON (a.id_satuan_1 = cc.id_satuan)
  LEFT OUTER JOIN ref_satuan dd ON (a.id_satuan_2 = dd.id_satuan)
		INNER JOIN trx_anggaran_aktivitas_pd AS b ON a.id_aktivitas_pd = b.id_aktivitas_pd
		LEFT OUTER JOIN (
			SELECT
				id_aktivitas_pd,
				GROUP_CONCAT(" ", uraian_lokasi) AS uraian_lokasi
			FROM
				trx_anggaran_lokasi_pd
			GROUP BY
				id_aktivitas_pd
		) AS ab ON ab.id_aktivitas_pd = b.id_aktivitas_pd
		INNER JOIN trx_anggaran_pelaksana_pd AS d ON b.id_pelaksana_pd = d.id_pelaksana_pd
		INNER JOIN trx_anggaran_kegiatan_pd AS e ON d.id_kegiatan_pd = e.id_kegiatan_pd
		INNER JOIN (
			SELECT
				d.Kd_Urusan,
				c.Kd_Bidang,
				b.Kd_Program AS kd_prog,
				a.kd_kegiatan AS kd_keg,
				a.id_kegiatan,
				a.id_program,
				a.nm_kegiatan,
				CASE
			WHEN b.kd_program = 0 THEN
				0
			ELSE
				CONCAT(
					d.kd_urusan,
					RIGHT (CONCAT(0, c.kd_bidang), 2)
				)
			END AS id_prog
			FROM
				ref_kegiatan a
			INNER JOIN ref_program b ON a.id_program = b.id_program
			INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang
			INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan
		) AS f ON e.id_kegiatan_ref = f.id_kegiatan
		INNER JOIN trx_anggaran_program_pd AS g ON e.id_program_pd = g.id_program_pd
		INNER JOIN ref_sub_unit h ON d.id_sub_unit = h.id_sub_unit
		INNER JOIN ref_unit i ON h.id_unit = i.id_unit
		INNER JOIN ref_bidang j ON i.id_bidang = j.id_bidang
		INNER JOIN ref_urusan k ON j.kd_urusan = k.kd_urusan
		INNER JOIN ref_rek_5 l ON l.id_rekening = a.id_rekening_ssh
		WHERE
			a.tahun_anggaran = 2019 -- +jika+
		AND l.kd_rek_1 = 6 and concat(a.Kd_Urusan,".", a.Kd_Bidang,".", a.kd_unit)="'.$unit.'"
	) a
GROUP BY
	a.tahun,
	a.Kd_Urusan,
	a.Kd_Bidang,
	a.kd_unit,
	a.kd_sub,
	a.Kd_Prog,
	a.id_prog,
	a.kd_keg,
	a.kd_rek_1,
	a.kd_rek_2,
	a.kd_rek_3,
	a.kd_rek_4,
	a.kd_rek_5,
	a.id_rekening,
	a.id_belanja_forum,
	a.id_sub_unit
 ');
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><mydoc></mydoc>');
        
        $xml->addAttribute('Created', 'by Simd@');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $tabel = $xml->addChild('tabel');
        $tabel->addAttribute('tabel', 'Ta_Pembiayaan');
        foreach ($Pemb as $unit) {
            $data = $tabel->addChild('data');
            $data->addChild('tahun', $unit->tahun);
            $data->addChild('Kd_Urusan', $unit->Kd_Urusan);
            $data->addChild('Kd_Bidang', $unit->Kd_Bidang);
            $data->addChild('kd_unit', $unit->kd_unit);
            $data->addChild('kd_sub', $unit->kd_sub);
            $data->addChild('Kd_Prog', $unit->kd_prog);
            $data->addChild('id_prog', $unit->id_prog);
            $data->addChild('Kd_Keg', $unit->kd_keg);
            $data->addChild('kd_rek_1', $unit->kd_rek_1);
            $data->addChild('kd_rek_2', $unit->kd_rek_2);
            $data->addChild('kd_rek_3', $unit->kd_rek_3);
            $data->addChild('kd_rek_4', $unit->kd_rek_4);
            $data->addChild('kd_rek_5', $unit->kd_rek_5);
            
            $data->addChild('Kd_Sumber', $unit->Kd_Sumber);
        }
        $tabel1 = $xml->addChild('tabel');
        $tabel1->addAttribute('tabel', 'Ta_Pembiayaan_Rinc');
        $prev="";
        $count=1;
        foreach ($PembRinc as $unit) {
            
            $data = $tabel1->addChild('data');
            if($prev==$unit->id_sub_unit.'.'.$unit->id_prog.'.'.$unit->kd_keg.'.'.$unit->id_rekening){
                $data->addChild('no_id', $count);
            }
            else
            {	$data->addChild('no_id', 1);
            $count=1;
            }
            $count++;
            $data->addChild('Tahun', $unit->tahun);
            $data->addChild('Kd_Urusan', $unit->Kd_Urusan);
            $data->addChild('Kd_Bidang', $unit->Kd_Bidang);
            $data->addChild('kd_unit', $unit->kd_unit);
            $data->addChild('kd_sub', $unit->kd_sub);
            $data->addChild('Kd_Prog', $unit->kd_prog);
            $data->addChild('id_prog', $unit->id_prog);
            $data->addChild('Kd_Keg', $unit->kd_keg);
            $data->addChild('kd_rek_1', $unit->kd_rek_1);
            $data->addChild('kd_rek_2', $unit->kd_rek_2);
            $data->addChild('kd_rek_3', $unit->kd_rek_3);
            $data->addChild('kd_rek_4', $unit->kd_rek_4);
            $data->addChild('kd_rek_5', $unit->kd_rek_5);
            $data->addChild('Sat_1', $unit->Sat_1);
            $data->addChild('Nilai_1', $unit->Nilai_1);
            $data->addChild('Sat_2', $unit->Sat_2);
            $data->addChild('Nilai_2', $unit->Nilai_2);
            $data->addChild('Sat_3', $unit->Sat_3);
            $data->addChild('Nilai_3', $unit->Nilai_3);
            $data->addChild('Satuan123', $unit->Satuan123);
            $data->addChild('Jml_Satuan', $unit->Jml_Satuan);
            $data->addChild('Nilai_Rp', $unit->Nilai_Rp);
            $data->addChild('Total', $unit->Total);
            $data->addChild('Keterangan', $unit->Keterangan);
            $prev=$unit->id_sub_unit.'.'.$unit->id_prog.'.'.$unit->kd_keg.'.'.$unit->id_rekening;
        }
        $response = Response::make($xml->asXML(), 200);
        $response->header('Cache-Control', 'public');
        $response->header('Content-Description', 'File Transfer');
        $response->header('Content-Disposition', 'attachment; filename=' . TIME() . '.xml');
        $response->header('Content-Transfer-Encoding', 'binary');
        $response->header('Content-Type', 'text/xml');
        
        return $response;
    }
    
    
	public function getTaPendapatanRinc(Request $request)
    {
        
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><mydoc></mydoc>');
        
        $xml->addAttribute('Created', 'by Simd@');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $tabel = $xml->addChild('tabel');
        $tabel->addAttribute('tabel', 'Ta_Pendapatan_Rinc');
        
			 
			$PendRinc = DB::SELECT('SELECT a.Tahun, a.Kd_Urusan, a.Kd_Bidang, a.Kd_Unit,a.id_sub_unit, a.Kd_Sub, a.Kd_Prog, a.ID_Prog, a.Kd_Keg, a.Kd_Rek_1, a.Kd_Rek_2, 
a.Kd_Rek_3, a.Kd_Rek_4, a.Kd_Rek_5, a.id_rekening,
 a.Sat_1, a.volume_1_forum as Nilai_1,  
 a.Sat_2, COALESCE(a.volume_2_forum ,0) as Nilai_2,  
 Null as Sat_3, 1 as Nilai_3,  
 case when a.sat2 is NULL then CONCAT(COALESCE(a.sat1,"-"),COALESCE(a.sat2,""))  
 else CONCAT(COALESCE(a.sat1,"-"),"/",COALESCE(a.sat2,"")) end as Satuan123,  
 case when a.volume_2_forum = 0 then a.volume_1_forum else (a.volume_1_forum * a.volume_2_forum) end as Jml_Satuan,  
 a.harga_satuan_forum as Nilai_Rp, a.jml_belanja_forum as Total, XML_Encode(GantiEnter(a.uraian_tarif_ssh)) as Keterangan 
 FROM ( 
 SELECT a.tahun_forum as tahun, k.Kd_Urusan, j.Kd_Bidang, i.kd_unit,h.id_sub_unit, h.kd_sub, f.Kd_Prog, f.id_prog, f.Kd_Keg,  
 l.kd_rek_1, l.kd_rek_2, l.kd_rek_3, l.kd_rek_4, l.kd_rek_5,l.id_rekening,  
 a.id_belanja_forum, a.id_lokasi_forum, a.id_zona_ssh, a.id_belanja_renja, a.sumber_belanja, 
 a.id_aktivitas_asb, a.id_item_ssh, a.id_rekening_ssh, l.nama_kd_rek_5, aa.uraian_tarif_ssh, 
 a.uraian_belanja, a.volume_1, a.id_satuan_1, a.volume_2, a.id_satuan_2, 
 a.harga_satuan, a.jml_belanja, COALESCE(a.volume_1_forum ,0) as volume_1_forum, a.id_satuan_1_forum, 
  COALESCE(a.volume_2_forum ,0) as volume_2_forum, 
  a.id_satuan_2_forum, a.harga_satuan_forum, 
 a.jml_belanja_forum, a.status_data, a.sumber_data,  
 f.Kd_Urusan AS Kd_Urusan1, f.Kd_Bidang AS Kd_Bidang1, g.uraian_program_renstra as ket_program, b.jenis_kegiatan, 
 COALESCE(e.pagu_tahun_kegiatan,0) as pagu_tahun_kegiatan, b.sumber_dana, 
 COALESCE(e.pagu_forum,0) AS Pagu_Anggaran, 
 substring(cc.uraian_satuan,1,10) as Sat_1, substring(cc.singkatan_satuan,1,15) as Sat1, 
  substring(dd.uraian_satuan,1,10) as Sat_2, substring(dd.singkatan_satuan,1,15) as Sat2 
 FROM trx_forum_skpd_belanja a INNER JOIN ref_ssh_tarif aa on a.id_item_ssh=aa.id_tarif_ssh 
 LEFT OUTER JOIN ref_satuan cc ON (a.id_satuan_1_forum = cc.id_satuan)  
 LEFT OUTER JOIN ref_satuan dd ON (a.id_satuan_2_forum = dd.id_satuan)  
 INNER JOIN trx_forum_skpd_aktivitas b ON a.id_lokasi_forum = b.id_aktivitas_forum 
 LEFT OUTER JOIN (select id_pelaksana_forum, GROUP_CONCAT(" ",uraian_lokasi) as uraian_lokasi from trx_forum_skpd_lokasi 
 GROUP BY id_pelaksana_forum) AS ab ON ab.id_pelaksana_forum = b.id_aktivitas_forum 
 INNER JOIN trx_forum_skpd_pelaksana d ON b.id_forum_skpd = d.id_pelaksana_forum 
 INNER JOIN trx_forum_skpd e ON d.id_aktivitas_forum = e.id_forum_skpd 
 INNER JOIN (  
Select d.Kd_Urusan, c.Kd_Bidang, b.Kd_Program as kd_prog, a.kd_kegiatan as kd_keg, a.id_kegiatan, a.id_program, a.nm_kegiatan,   
CASE WHEN b.kd_program = 0 then 0 else CONCAT(d.kd_urusan,RIGHT(CONCAT(0,c.kd_bidang),2)) end as id_prog  
FROM ref_kegiatan a  
INNER JOIN ref_program b ON a.id_program=b.id_program  
INNER JOIN ref_bidang c ON b.id_bidang = c.id_bidang  
INNER JOIN ref_urusan d ON c.kd_urusan = d.kd_urusan) f  
 ON e.id_kegiatan_ref = f.id_kegiatan  
 INNER JOIN trx_forum_skpd_program g ON e.id_forum_program = g.id_forum_program 
 INNER JOIN ref_sub_unit h on d.id_sub_unit = h.id_sub_unit  
 INNER JOIN ref_unit i on h.id_unit=i.id_unit  
 INNER JOIN ref_bidang j on i.id_bidang=j.id_bidang  
 INNER JOIN ref_urusan k ON j.kd_urusan = k.kd_urusan  
 INNER JOIN ref_rek_5 l on l.id_rekening = a.id_rekening_ssh 
 WHERE a.tahun_forum = 2019 -- +jika+
  and l.kd_rek_1 = 5  
 ) a
 
  
 GROUP BY a.tahun, a.Kd_Urusan, a.Kd_Bidang, a.kd_unit, a.kd_sub, a.Kd_Prog, a.id_prog, a.kd_keg, 
 a.kd_rek_1, a.kd_rek_2, a.kd_rek_3, a.kd_rek_4, a.kd_rek_5,a.id_rekening,  a.id_belanja_forum,a.id_sub_unit
 ');
 $prev="";
 $count=1;
 foreach ($PendRinc as $unit) {
	 
           $data = $tabel->addChild('data');
		   if($prev==$unit->id_sub_unit.'.'.$unit->id_prog.'.'.$unit->kd_keg.'.'.$unit->id_rekening){
            $data->addChild('no_id', $count);
		   }
			else 
			{	$data->addChild('no_id', 1);
				$count=1;
			}
			$count++;
			$data->addChild('Tahun', $unit->tahun);
            $data->addChild('Kd_Urusan', $unit->Kd_Urusan);
            $data->addChild('Kd_Bidang', $unit->Kd_Bidang);
            $data->addChild('kd_unit', $unit->kd_unit);
            $data->addChild('kd_sub', $unit->kd_sub);
            $data->addChild('Kd_Prog', $unit->kd_prog);
            $data->addChild('id_prog', $unit->id_prog);
            $data->addChild('Kd_Keg', $unit->kd_keg);
            $data->addChild('kd_rek_1', $unit->kd_rek_1);
            $data->addChild('kd_rek_2', $unit->kd_rek_2);
            $data->addChild('kd_rek_3', $unit->kd_rek_3);
            $data->addChild('kd_rek_4', $unit->kd_rek_4);
            $data->addChild('kd_rek_5', $unit->kd_rek_5);
            $data->addChild('Sat_1', $unit->Sat_1);
            $data->addChild('Nilai_1', $unit->Nilai_1);
			$data->addChild('Sat_2', $unit->Sat_2);
			$data->addChild('Nilai_2', $unit->Nilai_2);
			$data->addChild('Sat_3', $unit->Sat_3);
			$data->addChild('Nilai_3', $unit->Nilai_3);
			$data->addChild('Satuan123', $unit->Satuan123);
			$data->addChild('Jml_Satuan', $unit->Jml_Satuan);
			$data->addChild('Nilai_Rp', $unit->Nilai_Rp);
			$data->addChild('Total', $unit->Total);
			$data->addChild('Keterangan', $unit->Keterangan);
			$prev=$unit->id_sub_unit.'.'.$unit->id_prog.'.'.$unit->kd_keg.'.'.$unit->id_rekening;
        }
		
        
        $response = Response::make($xml->asXML(), 200);
        $response->header('Cache-Control', 'public');
        $response->header('Content-Description', 'File Transfer');
        $response->header('Content-Disposition', 'attachment; filename=' . TIME() . '.xml');
        $response->header('Content-Transfer-Encoding', 'binary');
        $response->header('Content-Type', 'text/xml');
        
        return $response;
    }
////////////////////////////////////////////// fungsi satu2 //////////////////////////////////////////////////////////
    public function getRefRek1(Request $request)
    {
        $unit = DB::SELECT('select kd_rek_1,nama_kd_rek_1 from ref_rek_1 ');
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><mydoc></mydoc>');
        
        $xml->addAttribute('Created', 'by Simd@');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $tabel = $xml->addChild('tabel');
        $tabel->addAttribute('tabel', 'ref_rek_1');
        foreach ($unit as $program) {
            $data = $tabel->addChild('data');
            $data->addChild('kd_rek_1', $program->kd_rek_1);
            $data->addChild('nama_kd_rek_1', $program->nama_kd_rek_1);
            
        }
        
        $response = Response::make($xml->asXML(), 200);
        $response->header('Cache-Control', 'public');
        $response->header('Content-Description', 'File Transfer');
        $response->header('Content-Disposition', 'attachment; filename=' . TIME() . '.xml');
        $response->header('Content-Transfer-Encoding', 'binary');
        $response->header('Content-Type', 'text/xml');
        
        return $response;
    }
    
    
    public function getRefRek2(Request $request)
    {
        $unit = DB::SELECT('select kd_rek_1,kd_rek_2,nama_kd_rek_2 from ref_rek_2');
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><mydoc></mydoc>');
        
        $xml->addAttribute('Created', 'by Simd@');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $tabel = $xml->addChild('tabel');
        $tabel->addAttribute('tabel', 'ref_rek_2');
        foreach ($unit as $program) {
            $data = $tabel->addChild('data');
            $data->addChild('kd_rek_1', $program->kd_rek_1);
            $data->addChild('kd_rek_2', $program->kd_rek_2);
            $data->addChild('nama_kd_rek_2', $program->nama_kd_rek_2);
            
        }
        
        $response = Response::make($xml->asXML(), 200);
        $response->header('Cache-Control', 'public');
        $response->header('Content-Description', 'File Transfer');
        $response->header('Content-Disposition', 'attachment; filename=' . TIME() . '.xml');
        $response->header('Content-Transfer-Encoding', 'binary');
        $response->header('Content-Type', 'text/xml');
        
        return $response;
    }
    
    
    
    public function getRefRek3(Request $request)
    {
        $unit = DB::SELECT('select kd_rek_1,kd_rek_2, kd_rek_3,nama_kd_rek_3,saldo_normal from ref_rek_3');
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><mydoc></mydoc>');
        
        $xml->addAttribute('Created', 'by Simd@');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $tabel = $xml->addChild('tabel');
        $tabel->addAttribute('tabel', 'ref_rek_3');
        foreach ($unit as $program) {
            $data = $tabel->addChild('data');
            $data->addChild('kd_rek_1', $program->kd_rek_1);
            $data->addChild('kd_rek_2', $program->kd_rek_2);
            $data->addChild('kd_rek_3', $program->kd_rek_3);
            $data->addChild('nama_kd_rek_3', $program->nama_kd_rek_3);
            $data->addChild('saldo_normal', $program->saldo_normal);
            
        }
        
        $response = Response::make($xml->asXML(), 200);
        $response->header('Cache-Control', 'public');
        $response->header('Content-Description', 'File Transfer');
        $response->header('Content-Disposition', 'attachment; filename=' . TIME() . '.xml');
        $response->header('Content-Transfer-Encoding', 'binary');
        $response->header('Content-Type', 'text/xml');
        
        return $response;
    }
    
    public function getRefRek4(Request $request)
    {
        $unit = DB::SELECT('select kd_rek_1,kd_rek_2, kd_rek_3,kd_rek_4,nama_kd_rek_4 from ref_rek_4');
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><mydoc></mydoc>');
        
        $xml->addAttribute('Created', 'by Simd@');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $tabel = $xml->addChild('tabel');
        $tabel->addAttribute('tabel', 'ref_rek_4');
        foreach ($unit as $program) {
            $data = $tabel->addChild('data');
            $data->addChild('kd_rek_1', $program->kd_rek_1);
            $data->addChild('kd_rek_2', $program->kd_rek_2);
            $data->addChild('kd_rek_3', $program->kd_rek_3);
            $data->addChild('kd_rek_4', $program->kd_rek_4);
            $data->addChild('nama_kd_rek_4', $program->nama_kd_rek_4);
            
        }
        
        $response = Response::make($xml->asXML(), 200);
        $response->header('Cache-Control', 'public');
        $response->header('Content-Description', 'File Transfer');
        $response->header('Content-Disposition', 'attachment; filename=' . TIME() . '.xml');
        $response->header('Content-Transfer-Encoding', 'binary');
        $response->header('Content-Type', 'text/xml');
        
        return $response;
    }
    
    public function getRefRek5(Request $request)
    {
        $unit = DB::SELECT('SELECT  kd_rek_1,kd_rek_2, kd_rek_3,kd_rek_4,kd_rek_5, min(XML_Encode(nama_kd_rek_5)) as nama_kd_rek_5, min(Peraturan) as peraturan,id_rekening
		FROM Ref_Rek_5
		group by kd_rek_1,kd_rek_2, kd_rek_3,kd_rek_4,kd_rek_5');
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><mydoc></mydoc>');
        
        $xml->addAttribute('Created', 'by Simd@');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $tabel = $xml->addChild('tabel');
        $tabel->addAttribute('tabel', 'ref_rek_5');
        foreach ($unit as $program) {
            $data = $tabel->addChild('data');
            $data->addChild('kd_rek_1', $program->kd_rek_1);
            $data->addChild('kd_rek_2', $program->kd_rek_2);
            $data->addChild('kd_rek_3', $program->kd_rek_3);
            $data->addChild('kd_rek_4', $program->kd_rek_4);
            $data->addChild('kd_rek_5', $program->kd_rek_5);
            $data->addChild('nama_kd_rek_5', $program->nama_kd_rek_5);
            $data->addChild('peraturan', $program->peraturan);
            $data->addChild('id_rekening', $program->id_rekening);
            
            
        }
        
        $response = Response::make($xml->asXML(), 200);
        $response->header('Cache-Control', 'public');
        $response->header('Content-Description', 'File Transfer');
        $response->header('Content-Disposition', 'attachment; filename=' . TIME() . '.xml');
        $response->header('Content-Transfer-Encoding', 'binary');
        $response->header('Content-Type', 'text/xml');
        
        return $response;
    }
    
    public function cekRefRek5(Request $request)
    {
        $unit = DB::SELECT('SELECT  CONCAT(kd_rek_1,".",kd_rek_2,".", kd_rek_3,".",kd_rek_4,".",kd_rek_5) as kode, min(XML_Encode(nama_kd_rek_5)) as nama_kd_rek_5, min(id_rekening) as id_rekening
		FROM Ref_Rek_5
		group by kd_rek_1,kd_rek_2, kd_rek_3,kd_rek_4,kd_rek_5');
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="utf-8"?><mydoc></mydoc>');
        
        $xml->addAttribute('Created', 'by Simd@');
        $xml->addChild('datetime', date('Y-m-d H:i:s'));
        $tabel = $xml->addChild('tabel');
        $tabel->addAttribute('tabel', 'ref_rek_5');
        foreach ($unit as $program) {
            $data = $tabel->addChild('data');
            $data->addChild('kode', $program->kode);
            $data->addChild('nama_kd_rek_5', $program->nama_kd_rek_5);
            $data->addChild('id_rekening', $program->id_rekening);
            
            
        }
        
        $response = Response::make($xml->asXML(), 200);
        $response->header('Cache-Control', 'public');
        $response->header('Content-Description', 'File Transfer');
        $response->header('Content-Disposition', 'attachment; filename=' . TIME() . '.xml');
        $response->header('Content-Transfer-Encoding', 'binary');
        $response->header('Content-Type', 'text/xml');
        
        return $response;
    }
    

}

