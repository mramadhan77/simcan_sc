<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use Illuminate\Support\Facades\Input;
use Yajra\Datatables\Datatables;
use DB;
use Response;
use Session;
use Auth;
use CekAkses;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;

class TrxCetakTahunanController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    public function dash_cetak_rkpd()
    {

    //   if(Auth::check()){  
            if(Session::has('tahun')){ 
                return view('errors.999');
            } else {
                return redirect('home');
            }
        // } else {
            // return view ( 'errors.401' );
        // }

    }

    public function dash_cetak_renja()
    {

    //   if(Auth::check()){  
            if(Session::has('tahun')){ 
                return view('report.cetak_renja');
            } else {
                return redirect('home');
            }
        // } else {
            // return view ( 'errors.401' );
        // }

    }

    public function dash_cetak_ranwalrenja()
    {

    //   if(Auth::check()){  
            if(Session::has('tahun')){ 
                return view('report.cetak_renja_ranwal');
            } else {
                return redirect('home');
            }
        // } else {
            // return view ( 'errors.401' );
        // }

    }

    public function dash_cetak_musren()
    {

    //   if(Auth::check()){  
            if(Session::has('tahun')){ 
                return view('report.cetak_musren');
            } else {
                return redirect('home');
            }
        // } else {
            // return view ( 'errors.401' );
        // }

    }

    public function dash_cetak_forum()
    {

    //   if(Auth::check()){  
            if(Session::has('tahun')){ 
                return view('report.cetak_forum');
            } else {
                return redirect('home');
            }
        // } else {
            // return view ( 'errors.401' );
        // }

    }

    public function dash_cetak_pokir()
    {

    //   if(Auth::check()){  
            if(Session::has('tahun')){ 
                return view('report.cetak_pokir');
            } else {
                return redirect('home');
            }
        // } else {
            // return view ( 'errors.401' );
        // }

    }
    public function dash_cetak_pra_rka()
    {

    //   if(Auth::check()){  
            if(Session::has('tahun')){ 
                return view('report.cetak_pra_rka');
            } else {
                return redirect('home');
            }
        // } else {
            // return view ( 'errors.401' );
        // }

    }

    public function dash_cetak_rkpd_final()
    {
        
        // if(Auth::check()){
            if(Session::has('tahun')){
                return view('report.cetak_rkpd_final');
            } else {
                return redirect('home');
            }
        // } else {
            // return view ( 'errors.401' );
        // }
        
    }
    
    public function dash_cetak_apbd()
    {
        
        // if(Auth::check()){
            if(Session::has('tahun')){
                return view('report.cetak_apbd');
            } else {
                return redirect('home');
            }
        // } else {
            // return view ( 'errors.401' );
        // }
        
    }

}
