<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use App\Http\Requests;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;
use Response;
use Session;
use PDF;
use App\Models\InformationSchema;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;


class CetakDiffDBController extends Controller
{

	public function printDiffDB($DBSource,$DBTarget)
	{
		//$DBSource='simcan_18082017';
		//$DBTarget='simcan_29082017';
		//return $id_aktiv; 
		// set document information
		PDF::SetCreator('BPKP');
		PDF::SetAuthor('BPKP');
		PDF::SetTitle('Simd@Perencanaan');
		PDF::SetSubject('Diff DB');

		// set default header data
		PDF::SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE.' 011', PDF_HEADER_STRING);

		// set header and footer fonts
		PDF::setHeaderFont(Array(PDF_FONT_NAME_MAIN, "", PDF_FONT_SIZE_MAIN));
		PDF::setFooterFont(Array(PDF_FONT_NAME_DATA, "", PDF_FONT_SIZE_DATA));

		// set default monospaced font
		PDF::SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		PDF::SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		PDF::SetHeaderMargin(PDF_MARGIN_HEADER);
		PDF::SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		PDF::SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		// PDF::setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		// if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
		//     require_once(dirname(__FILE__).'/lang/eng.php');
		//     $pdf->setLanguageArray($l);
		// }

		// ---------------------------------------------------------

		// set font
		PDF::SetFont('helvetica', "", 6);

		// add a page
		PDF::AddPage('P');

		// column titles
		$header = array('Komponen Belanja','koef. 1',"",'koef. 2',"",'koef. 3');

		// Colors, line width and bold font
		PDF::SetFillColor(200, 200, 200);
		PDF::SetTextColor(0);
		PDF::SetDrawColor(255, 255, 255);
		PDF::SetLineWidth(0.3);

		//Header
	
		PDF::SetFont("", 'B');
		PDF::SetFont('helvetica', 'B', 8);
		PDF::SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0.1, 'color' => array(0, 0, 0)));
		// Header Column
	
		$w = array(40,40,15,10,10,40,15,10,10);
		
		//PDF::Cell($wh[$num_headers-1], 7, $header[$num_headers-1], 'LR', 0, 'C', 1);
		
			// Color and font restoration

		PDF::SetFillColor(224, 235, 255);
		PDF::SetTextColor(0);
		PDF::SetFont('helvetica', "", 7);
			// Data
				$DiffDB1 = DB::table('information_schema.COLUMNS')
				->select(DB::raw(' TABLE_NAME,"" as COLUMN_NAME,"" as COLUMN_TYPE,"" as IS_NULLABLE,"" as COLUMN_KEY, COLUMN_NAME as COLUMN_NAME1, COLUMN_TYPE as COLUMN_TYPE1, IS_NULLABLE as IS_NULLABLE1, COLUMN_KEY as COLUMN_KEY1'))
				->whereRaw('TABLE_SCHEMA="'.$DBTarget.'" and TABLE_NAME not in (select TABLE_NAME FROM `INFORMATION_SCHEMA`.`COLUMNS` where TABLE_SCHEMA="'.$DBSource.'" )');
				
				$DiffDB3 = DB::table('information_schema.COLUMNS as a')
				->select(DB::raw(' distinct a.TABLE_NAME,a.COLUMN_NAME, a.COLUMN_TYPE, a.IS_NULLABLE, a.COLUMN_KEY,"" as COLUMN_NAME1, "" as COLUMN_TYPE1, "" as IS_NULLABLE1, "" as COLUMN_KEY1'))
				->join(DB::raw('(Select TABLE_NAME,COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_KEY From information_schema.COLUMNS where TABLE_SCHEMA ="'.$DBTarget.'" )b'), function($join)
				{
					$join->on('a.TABLE_NAME','=','b.TABLE_NAME');
					
				})
				->whereRaw('a.TABLE_SCHEMA = "'.$DBSource.'"and a.COLUMN_NAME not in (select COLUMN_NAME FROM `INFORMATION_SCHEMA`.`COLUMNS` where TABLE_SCHEMA="'.$DBTarget.'" )');
				
				$DiffDB3B = DB::table('information_schema.COLUMNS as a')
				->select(DB::raw(' distinct a.TABLE_NAME,"" as COLUMN_NAME,"" as COLUMN_TYPE,"" as IS_NULLABLE,"" as COLUMN_KEY,a.COLUMN_NAME as COLUMN_NAME1, a.COLUMN_TYPE as COLUMN_TYPE1, a.IS_NULLABLE as IS_NULLABLE1, a.COLUMN_KEY as COLUMN_KEY1'))
				->join(DB::raw('(Select TABLE_NAME,COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_KEY From information_schema.COLUMNS where TABLE_SCHEMA ="'.$DBSource.'" )b'), function($join)
				{
					$join->on('a.TABLE_NAME','=','b.TABLE_NAME');
					
				})
				->whereRaw('a.TABLE_SCHEMA = "'.$DBTarget.'"and a.COLUMN_NAME not in (select COLUMN_NAME FROM `INFORMATION_SCHEMA`.`COLUMNS` where TABLE_SCHEMA="'.$DBSource.'" )');
				
				$DiffDB2 = DB::table('information_schema.COLUMNS')
				->select(DB::raw('TABLE_NAME, COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_KEY,"" as COLUMN_NAME1,"" as COLUMN_TYPE1,"" as IS_NULLABLE1,"" as COLUMN_KEY1'))
				->whereRaw('TABLE_SCHEMA="'.$DBSource.'" and TABLE_NAME not in (select TABLE_NAME FROM `INFORMATION_SCHEMA`.`COLUMNS` where TABLE_SCHEMA="'.$DBTarget.'" )')
				->union($DiffDB1)
				->union($DiffDB3)
				->union($DiffDB3B)
				->get();
				
				$DiffDB4 = DB::table('information_schema.COLUMNS as a')
				->select(DB::raw(' a.TABLE_NAME,a.COLUMN_NAME, a.COLUMN_TYPE, a.IS_NULLABLE, a.COLUMN_KEY,b.COLUMN_NAME as COLUMN_NAME1, b.COLUMN_TYPE as COLUMN_TYPE1, b.IS_NULLABLE as IS_NULLABLE1, b.COLUMN_KEY as COLUMN_KEY1'))
				->join(DB::raw('(Select TABLE_NAME,COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_KEY From information_schema.COLUMNS where TABLE_SCHEMA ="'.$DBTarget.'" )b'), function($join)
						{
							$join->on('a.TABLE_NAME','=','b.TABLE_NAME');
							$join->on('a.COLUMN_NAME','=','b.COLUMN_NAME');
							$join->on('a.COLUMN_TYPE','<>','b.COLUMN_TYPE');
				})
				->whereRaw('a.TABLE_SCHEMA = "'.$DBSource.'"')
				->get();
				
				$DiffDB5 = DB::table('information_schema.COLUMNS as a')
				->select(DB::raw(' a.TABLE_NAME,a.COLUMN_NAME, a.COLUMN_TYPE, a.IS_NULLABLE, a.COLUMN_KEY,b.COLUMN_NAME as COLUMN_NAME1, b.COLUMN_TYPE as COLUMN_TYPE1, b.IS_NULLABLE as IS_NULLABLE1, b.COLUMN_KEY as COLUMN_KEY1'))
				->join(DB::raw('(Select TABLE_NAME,COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_KEY From information_schema.COLUMNS where TABLE_SCHEMA ="'.$DBTarget.'" )b'), function($join)
				{
					$join->on('a.TABLE_NAME','=','b.TABLE_NAME');
					$join->on('a.COLUMN_NAME','=','b.COLUMN_NAME');
					$join->on('a.COLUMN_TYPE','=','b.COLUMN_TYPE');
					$join->on('a.IS_NULLABLE','<>','b.IS_NULLABLE');
				})
				->whereRaw('a.TABLE_SCHEMA = "'.$DBSource.'"')
				->get();
				
				$DiffDB6 = DB::table('information_schema.COLUMNS as a')
				->select(DB::raw(' a.TABLE_NAME,a.COLUMN_NAME, a.COLUMN_TYPE, a.IS_NULLABLE, a.COLUMN_KEY,b.COLUMN_NAME as COLUMN_NAME1, b.COLUMN_TYPE as COLUMN_TYPE1, b.IS_NULLABLE as IS_NULLABLE1, b.COLUMN_KEY as COLUMN_KEY1'))
				->join(DB::raw('(Select TABLE_NAME,COLUMN_NAME, COLUMN_TYPE, IS_NULLABLE, COLUMN_KEY From information_schema.COLUMNS where TABLE_SCHEMA ="'.$DBTarget.'" )b'), function($join)
				{
					$join->on('a.TABLE_NAME','=','b.TABLE_NAME');
					$join->on('a.COLUMN_NAME','=','b.COLUMN_NAME');
					$join->on('a.COLUMN_TYPE','=','b.COLUMN_TYPE');
					$join->on('a.IS_NULLABLE','=','b.IS_NULLABLE');
					$join->on('a.COLUMN_KEY','<>','b.COLUMN_KEY');
				})
				->whereRaw('a.TABLE_SCHEMA = "'.$DBSource.'"')
				->get();
				
				PDF::SetFont('helvetica', "B", 12);
				PDF::MultiCell(190, 7, 'View/Table/Column Tidak Ada', 1, 'L', 0, 0);
				PDF::Ln();
				PDF::SetFont('helvetica', "B", 7);
				PDF::MultiCell(40, 7, '', 0, 'L', 0, 0);
				PDF::MultiCell(75, 7, $DBSource, 1, 'C', 0, 0);
				PDF::MultiCell(75, 7, $DBTarget, 1, 'C', 0, 0);
				PDF::Ln();
				PDF::MultiCell($w[0], 7, 'TABLE', 1, 'L', 0, 0);
				PDF::MultiCell($w[1], 7, 'FIELD', 1, 'L', 0, 0);
				PDF::MultiCell($w[2], 7, 'TYPE', 1, 'L', 0, 0);
				PDF::MultiCell($w[3], 7, 'NULL', 1, 'L', 0, 0);
				PDF::MultiCell($w[4], 7, 'PK', 1, 'L', 0, 0);
				PDF::MultiCell($w[5], 7, 'FIELD', 1, 'L', 0, 0);
				PDF::MultiCell($w[6], 7, 'TYPE', 1, 'L', 0, 0);
				PDF::MultiCell($w[7], 7, 'NULL', 1, 'L', 0, 0);
				PDF::MultiCell($w[8], 7, 'PK', 1, 'L', 0, 0);
				PDF::Ln();
				$i=0;
				foreach($DiffDB2 as $row)
				{
					PDF::SetFont('helvetica', "", 7);
					PDF::MultiCell($w[0], 7, $row->TABLE_NAME, 1, 'L', 0, 0);
					PDF::MultiCell($w[1], 7, $row->COLUMN_NAME, 1, 'L', 0, 0);
					PDF::MultiCell($w[2], 7, $row->COLUMN_TYPE, 1, 'L', 0, 0);
					PDF::MultiCell($w[3], 7, $row->IS_NULLABLE, 1, 'L', 0, 0);
					PDF::MultiCell($w[4], 7, $row->COLUMN_KEY, 1, 'L', 0, 0);
					PDF::MultiCell($w[5], 7, $row->COLUMN_NAME1, 1, 'L', 0, 0);
					PDF::MultiCell($w[6], 7, $row->COLUMN_TYPE1, 1, 'L', 0, 0);
					PDF::MultiCell($w[7], 7, $row->IS_NULLABLE1, 1, 'L', 0, 0);
					PDF::MultiCell($w[8], 7, $row->COLUMN_KEY1, 1, 'L', 0, 0);
					PDF::Ln();
					$i++;
					if($i>30)
					{
						PDF::AddPage('P');
						PDF::SetFont('helvetica', "B", 7);
						PDF::MultiCell(40, 7, '', 0, 'L', 0, 0);
						PDF::MultiCell(75, 7, $DBSource, 1, 'C', 0, 0);
						PDF::MultiCell(75, 7, $DBTarget, 1, 'C', 0, 0);
						PDF::Ln();
						PDF::MultiCell($w[0], 7, 'TABLE', 1, 'L', 0, 0);
						PDF::MultiCell($w[1], 7, 'FIELD', 1, 'L', 0, 0);
						PDF::MultiCell($w[2], 7, 'TYPE', 1, 'L', 0, 0);
						PDF::MultiCell($w[3], 7, 'NULL', 1, 'L', 0, 0);
						PDF::MultiCell($w[4], 7, 'PK', 1, 'L', 0, 0);
						PDF::MultiCell($w[5], 7, 'FIELD', 1, 'L', 0, 0);
						PDF::MultiCell($w[6], 7, 'TYPE', 1, 'L', 0, 0);
						PDF::MultiCell($w[7], 7, 'NULL', 1, 'L', 0, 0);
						PDF::MultiCell($w[8], 7, 'PK', 1, 'L', 0, 0);
						PDF::Ln();
						$i=0;
					}
					
				}
				PDF::AddPage('P');
				PDF::Ln();
				PDF::SetFont('helvetica', "B", 12);
				PDF::MultiCell(190, 7, 'Tipe Column Tidak Sesuai', 1, 'L', 0, 0);
				PDF::Ln();
				PDF::SetFont('helvetica', "B", 7);
				PDF::MultiCell(40, 7, '', 0, 'L', 0, 0);
				PDF::MultiCell(75, 7, $DBSource, 1, 'C', 0, 0);
				PDF::MultiCell(75, 7, $DBTarget, 1, 'C', 0, 0);
				PDF::Ln();
				PDF::MultiCell($w[0], 7, 'TABLE', 1, 'L', 0, 0);
				PDF::MultiCell($w[1], 7, 'FIELD', 1, 'L', 0, 0);
				PDF::MultiCell($w[2], 7, 'TYPE', 1, 'L', 0, 0);
				PDF::MultiCell($w[3], 7, 'NULL', 1, 'L', 0, 0);
				PDF::MultiCell($w[4], 7, 'PK', 1, 'L', 0, 0);
				PDF::MultiCell($w[5], 7, 'FIELD', 1, 'L', 0, 0);
				PDF::MultiCell($w[6], 7, 'TYPE', 1, 'L', 0, 0);
				PDF::MultiCell($w[7], 7, 'NULL', 1, 'L', 0, 0);
				PDF::MultiCell($w[8], 7, 'PK', 1, 'L', 0, 0);
				PDF::Ln();
				$i=0;
				foreach($DiffDB4 as $row)
				{
					PDF::SetFont('helvetica', "", 7);
					PDF::MultiCell($w[0], 7, $row->TABLE_NAME, 1, 'L', 0, 0);
					PDF::MultiCell($w[1], 7, $row->COLUMN_NAME, 1, 'L', 0, 0);
					PDF::MultiCell($w[2], 7, $row->COLUMN_TYPE, 1, 'L', 0, 0);
					PDF::MultiCell($w[3], 7, $row->IS_NULLABLE, 1, 'L', 0, 0);
					PDF::MultiCell($w[4], 7, $row->COLUMN_KEY, 1, 'L', 0, 0);
					PDF::MultiCell($w[5], 7, $row->COLUMN_NAME1, 1, 'L', 0, 0);
					PDF::MultiCell($w[6], 7, $row->COLUMN_TYPE1, 1, 'L', 0, 0);
					PDF::MultiCell($w[7], 7, $row->IS_NULLABLE1, 1, 'L', 0, 0);
					PDF::MultiCell($w[8], 7, $row->COLUMN_KEY1, 1, 'L', 0, 0);
					PDF::Ln();
					$i++;
					if($i>30)
					{
						PDF::AddPage('P');
						PDF::SetFont('helvetica', "B", 7);
						PDF::MultiCell(40, 7, '', 0, 'L', 0, 0);
						PDF::MultiCell(75, 7, $DBSource, 1, 'C', 0, 0);
						PDF::MultiCell(75, 7, $DBTarget, 1, 'C', 0, 0);
						PDF::Ln();
						PDF::MultiCell($w[0], 7, 'TABLE', 1, 'L', 0, 0);
						PDF::MultiCell($w[1], 7, 'FIELD', 1, 'L', 0, 0);
						PDF::MultiCell($w[2], 7, 'TYPE', 1, 'L', 0, 0);
						PDF::MultiCell($w[3], 7, 'NULL', 1, 'L', 0, 0);
						PDF::MultiCell($w[4], 7, 'PK', 1, 'L', 0, 0);
						PDF::MultiCell($w[5], 7, 'FIELD', 1, 'L', 0, 0);
						PDF::MultiCell($w[6], 7, 'TYPE', 1, 'L', 0, 0);
						PDF::MultiCell($w[7], 7, 'NULL', 1, 'L', 0, 0);
						PDF::MultiCell($w[8], 7, 'PK', 1, 'L', 0, 0);
						PDF::Ln();
						$i=0;
					}
					
				}
				PDF::AddPage('P');
				PDF::Ln();
				PDF::SetFont('helvetica', "B", 12);
				PDF::MultiCell(190, 7, 'Nullable Tidak Sesuai', 1, 'L', 0, 0);
				PDF::Ln();
				PDF::SetFont('helvetica', "B", 7);
				PDF::MultiCell(40, 7, '', 0, 'L', 0, 0);
				PDF::MultiCell(75, 7, $DBSource, 1, 'C', 0, 0);
				PDF::MultiCell(75, 7, $DBTarget, 1, 'C', 0, 0);
				PDF::Ln();
				PDF::MultiCell($w[0], 7, 'TABLE', 1, 'L', 0, 0);
				PDF::MultiCell($w[1], 7, 'FIELD', 1, 'L', 0, 0);
				PDF::MultiCell($w[2], 7, 'TYPE', 1, 'L', 0, 0);
				PDF::MultiCell($w[3], 7, 'NULL', 1, 'L', 0, 0);
				PDF::MultiCell($w[4], 7, 'PK', 1, 'L', 0, 0);
				PDF::MultiCell($w[5], 7, 'FIELD', 1, 'L', 0, 0);
				PDF::MultiCell($w[6], 7, 'TYPE', 1, 'L', 0, 0);
				PDF::MultiCell($w[7], 7, 'NULL', 1, 'L', 0, 0);
				PDF::MultiCell($w[8], 7, 'PK', 1, 'L', 0, 0);
				PDF::Ln();
				$i=0;
				foreach($DiffDB5 as $row)
				{
					PDF::SetFont('helvetica', "", 7);
					PDF::MultiCell($w[0], 7, $row->TABLE_NAME, 1, 'L', 0, 0);
					PDF::MultiCell($w[1], 7, $row->COLUMN_NAME, 1, 'L', 0, 0);
					PDF::MultiCell($w[2], 7, $row->COLUMN_TYPE, 1, 'L', 0, 0);
					PDF::MultiCell($w[3], 7, $row->IS_NULLABLE, 1, 'L', 0, 0);
					PDF::MultiCell($w[4], 7, $row->COLUMN_KEY, 1, 'L', 0, 0);
					PDF::MultiCell($w[5], 7, $row->COLUMN_NAME1, 1, 'L', 0, 0);
					PDF::MultiCell($w[6], 7, $row->COLUMN_TYPE1, 1, 'L', 0, 0);
					PDF::MultiCell($w[7], 7, $row->IS_NULLABLE1, 1, 'L', 0, 0);
					PDF::MultiCell($w[8], 7, $row->COLUMN_KEY1, 1, 'L', 0, 0);
					PDF::Ln();
					$i++;
					if($i>30)
					{
						PDF::AddPage('P');
						PDF::SetFont('helvetica', "B", 7);
						PDF::MultiCell(40, 7, '', 0, 'L', 0, 0);
						PDF::MultiCell(75, 7, $DBSource, 1, 'C', 0, 0);
						PDF::MultiCell(75, 7, $DBTarget, 1, 'C', 0, 0);
						PDF::Ln();
						PDF::MultiCell($w[0], 7, 'TABLE', 1, 'L', 0, 0);
						PDF::MultiCell($w[1], 7, 'FIELD', 1, 'L', 0, 0);
						PDF::MultiCell($w[2], 7, 'TYPE', 1, 'L', 0, 0);
						PDF::MultiCell($w[3], 7, 'NULL', 1, 'L', 0, 0);
						PDF::MultiCell($w[4], 7, 'PK', 1, 'L', 0, 0);
						PDF::MultiCell($w[5], 7, 'FIELD', 1, 'L', 0, 0);
						PDF::MultiCell($w[6], 7, 'TYPE', 1, 'L', 0, 0);
						PDF::MultiCell($w[7], 7, 'NULL', 1, 'L', 0, 0);
						PDF::MultiCell($w[8], 7, 'PK', 1, 'L', 0, 0);
						PDF::Ln();
						$i=0;
					}
					
				}
				PDF::AddPage('P');
				PDF::Ln();
				PDF::SetFont('helvetica', "B", 12);
				PDF::MultiCell(190, 7, 'Primary Key Tidak Sesuai', 1, 'L', 0, 0);
				PDF::Ln();
				PDF::SetFont('helvetica', "B", 7);
				PDF::MultiCell(40, 7, '', 0, 'L', 0, 0);
				PDF::MultiCell(75, 7, $DBSource, 1, 'C', 0, 0);
				PDF::MultiCell(75, 7, $DBTarget, 1, 'C', 0, 0);
				PDF::Ln();
				PDF::MultiCell($w[0], 7, 'TABLE', 1, 'L', 0, 0);
				PDF::MultiCell($w[1], 7, 'FIELD', 1, 'L', 0, 0);
				PDF::MultiCell($w[2], 7, 'TYPE', 1, 'L', 0, 0);
				PDF::MultiCell($w[3], 7, 'NULL', 1, 'L', 0, 0);
				PDF::MultiCell($w[4], 7, 'PK', 1, 'L', 0, 0);
				PDF::MultiCell($w[5], 7, 'FIELD', 1, 'L', 0, 0);
				PDF::MultiCell($w[6], 7, 'TYPE', 1, 'L', 0, 0);
				PDF::MultiCell($w[7], 7, 'NULL', 1, 'L', 0, 0);
				PDF::MultiCell($w[8], 7, 'PK', 1, 'L', 0, 0);
				PDF::Ln();
				$i=0;
				foreach($DiffDB6 as $row)
				{
					PDF::SetFont('helvetica', "", 7);
					PDF::MultiCell($w[0], 7, $row->TABLE_NAME, 1, 'L', 0, 0);
					PDF::MultiCell($w[1], 7, $row->COLUMN_NAME, 1, 'L', 0, 0);
					PDF::MultiCell($w[2], 7, $row->COLUMN_TYPE, 1, 'L', 0, 0);
					PDF::MultiCell($w[3], 7, $row->IS_NULLABLE, 1, 'L', 0, 0);
					PDF::MultiCell($w[4], 7, $row->COLUMN_KEY, 1, 'L', 0, 0);
					PDF::MultiCell($w[5], 7, $row->COLUMN_NAME1, 1, 'L', 0, 0);
					PDF::MultiCell($w[6], 7, $row->COLUMN_TYPE1, 1, 'L', 0, 0);
					PDF::MultiCell($w[7], 7, $row->IS_NULLABLE1, 1, 'L', 0, 0);
					PDF::MultiCell($w[8], 7, $row->COLUMN_KEY1, 1, 'L', 0, 0);
					PDF::Ln();
					$i++;
					if($i>30)
					{
						PDF::AddPage('P');
						PDF::SetFont('helvetica', "B", 7);
						PDF::MultiCell(40, 7, '', 0, 'L', 0, 0);
						PDF::MultiCell(75, 7, $DBSource, 1, 'C', 0, 0);
						PDF::MultiCell(75, 7, $DBTarget, 1, 'C', 0, 0);
						PDF::Ln();
						PDF::MultiCell($w[0], 7, 'TABLE', 1, 'L', 0, 0);
						PDF::MultiCell($w[1], 7, 'FIELD', 1, 'L', 0, 0);
						PDF::MultiCell($w[2], 7, 'TYPE', 1, 'L', 0, 0);
						PDF::MultiCell($w[3], 7, 'NULL', 1, 'L', 0, 0);
						PDF::MultiCell($w[4], 7, 'PK', 1, 'L', 0, 0);
						PDF::MultiCell($w[5], 7, 'FIELD', 1, 'L', 0, 0);
						PDF::MultiCell($w[6], 7, 'TYPE', 1, 'L', 0, 0);
						PDF::MultiCell($w[7], 7, 'NULL', 1, 'L', 0, 0);
						PDF::MultiCell($w[8], 7, 'PK', 1, 'L', 0, 0);
						PDF::Ln();
						$i=0;
					}
					
				}

		// ---------------------------------------------------------

		// close and output PDF document
		PDF::Output('DiffDB.pdf', 'I');
	}

}
