<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Auth;
use Datatables;
use Session;
use Artisan;
use Yajra\Datatables\Html\Builder;
use Yajra\Datatables\Services\DataTable;
use Illuminate\Support\Facades\File;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Database\QueryException;
use Carbon\Carbon;
use App\MenuForm;
use App\CekAkses;

class UpdateController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }
    
    protected $client;

	public function index(Request $request, MenuForm $a){
        // $versionJson = @file_get_contents(realpath(base_path().'/composer.json'));
        // $version = json_decode($versionJson);

        // $app = require(base_path().'/app/rilis.php');        
        // $x = $app['appVersi'];
        // $y = $app['rilis'];
        // check here
        // $menu = require(base_path().'/config/menu.php');        
        // $b = $a->reveal($menu['state']);
        // $c = $a->getApp();
        // $check = json_decode(@file_get_contents($b."get/version?id=smdspr&version=".$version->version."&kd=".$menu['li']));
        // $result = ($version->version == $check->version);
        // $result = true;
        // $kronologi = json_decode(@file_get_contents($b."get/versionupdate?id=smdspr&version=".$version->version."&kd=".$menu['li']));
        // return view('update.index', [
        //     'currentVersion' => $version,
        //     'available' => $check,
        //     'result' => $x,
        //     'kronologi' => $y,
        //     'alamat' => $b 
        // ]);

        // $valueX = request()->server('PATH_INFO');

        // $results = DB::select( DB::raw("select version()") );
        // $mysql_version =  $results[0]->{'version()'};
        // $mariadb_version = '';

        // if (strpos($mysql_version, 'Maria') !== false) {
        //     $mariadb_version = $mysql_version;
        //     $mysql_version = '';
        // }

        // $DBVersion = DB::SELECT('SELECT * FROM ref_version a INNER JOIN (SELECT max(id) as id, max(updated_at) from ref_version WHERE type = 0) b ON a.id = b.id');

        // if ($DBVersion != null){
        //     $infoX = 'Database Versi :'.$DBVersion[0]->version.' diupdate pada :'. $DBVersion[0]->updated_at;
        // } else {
            
        // }

        $infoX = 'Data Versi Database tidak ada, Silahkan Update DB terlebih dahulu rilis tgl 25 Maret 2018';
        $results = DB::SELECT('SELECT @@version_comment as jns_server, @@version as versi_server');
        $dataVersion = $results[0]->jns_server.' versi : ('.$results[0]->versi_server.')';
        $namaDB = env('DB_DATABASE', 'forge');

        return view('update.index')->with(compact('dataVersion','infoX', 'namaDB')) ;
        
    }

    public function getApi(){
        $menu = require(base_path().'/config/menu.php');
        $getApp = $menu['li'];
        $getUrl = json_decode(@file_get_contents('http://simda-online.com/scapi2/api/get/0&'.$getApp));

        $cekLog = DB::SELECT('SELECT * FROM ref_log_akses WHERE `id_log`= "'.$getApp.'"');

        if($cekLog != null){
                $addLog = DB::UPDATE('UPDATE ref_log_akses SET `id_log`= "'.$getApp.'",`fd1`="'.$getUrl[0]->nm.'", `fp2`="'.$getUrl[0]->fu.'", 
                    `fu3`="'.$getUrl[0]->fu.'", `fr4`="'.$getUrl[0]->fn.'" WHERE `id_log`= "'.$getApp.'"');
        } else {
                $addLog = DB::INSERT('INSERT INTO ref_log_akses (`id_log`, `fd1`, `fp2`, `fu3`, `fr4`) VALUES 
                    ("'.$getApp.'","'.$getUrl[0]->nm.'","'.$getUrl[0]->fu.'","'.$getUrl[0]->fu.'","'.$getUrl[0]->fn.'")');
        }
        
        if($addLog != 0){
            return response ()->json (['pesan'=>'Data Berhasil Disimpan','status_pesan'=>'1']); 
        } else {
            return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
        } 
    }

    public function testGetFacadeAccessor()
    {
        $accessor = 'updater';
        $class = UpdaterFacade::class;

        $reflection = new ReflectionClass($class);

        $method = $reflection->getMethod('getFacadeAccessor');
        $method->setAccessible(true);

        $msg = "Expected class '$class' to have an accessor of '$accessor'.";

        $this->assertSame($accessor, $method->invoke(null), $msg);
    }

    protected function getEnvironmentSetUp($app)
    {
        $app['config']->set('self-update', [
            'default' => 'github',
            'version_installed' => '',
            'repository_types' => [
                'github' => [
                    'type' => 'github',
                    'repository_vendor' => 'laravel',
                    'repository_name' => 'laravel',
                    'repository_url' => '',
                    'download_path' => '/tmp',
                ],
            ],
            'log_events' => false,
            'mail_to' => [
                'address' => '',
                'name' => '',
            ],
        ]);
    }

    protected function getPackageProviders($app)
    {
        return [
            UpdaterServiceProvider::class,
        ];
    }

    protected function getPackageAliases($app)
    {
        return [
            'Updater' => UpdaterFacade::class,
        ];
    }

    public function updateDB(Request $req){

        // $file  = $req->file('updatedb');
        // $yyy = decrypt(file_get_contents($file));


        $tabel = parse_ini_file($req->file('updatedb'));
        $source = $tabel['update'];
        // $update=DB::unprepared($source); 

        if ($source != null) {
            try {
                $update=DB::unprepared($source);

                if($update != 0){
                    return back()->with('pesan','Database Berhasil Diupdate');
                } else {
                    return back()->with('pesan','Database Gagal Diupdate');
                }
            } 
            catch(QueryException $e){
                $error_code = $e->getMessage() ;
                return response ()->json (['pesan'=>'Data Gagal Disimpan ('.$error_code.')','status_pesan'=>'0']);
            }
        } else {
            return response ()->json (['pesan'=>'File Update Null','status_pesan'=>'0']);
        };

    }

    public function encryptDB(Request $req){
        $file  = $req->file('updatedb');
        $dir = base_path().'/database/';
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).'_'.TIME().'.simcan.php';
        $xxx = encrypt(file_get_contents($file));

        if ($file != null) {
            $update = file_put_contents($dir.$filename,$xxx, FILE_APPEND);

            if($update != 0){
                return back()->with('pesan','Script Berhasil Di-Encrypt');
            } else {
                return back()->with('pesan','Script Gagal Di-Encrypt');
            }
        } else {
            return response ()->json (['pesan'=>'File Script Null','status_pesan'=>'0']);
        };
    }

    public function decryptDB(Request $req){
        $file  = $req->file('updatedb');
        $dir = base_path().'/database/';
        $filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME).'_'.TIME().'.simcan.php';
        $xxx = decrypt(file_get_contents($file));

        if ($file != null) {
            $update = file_put_contents($dir.$filename,$xxx, FILE_APPEND);

            if($update != 0){
                return back()->with('pesan','Script Berhasil Di-Decrypt');
            } else {
                return back()->with('pesan','Script Gagal Di-Decrypt');
            }
        } else {
            return response ()->json (['pesan'=>'File Script Null','status_pesan'=>'0']);
        };
    }

    public function getUpdate(Request $request)
    {
        $yyy = file_get_contents(base_path().'/database/'.$request->file);

        $dataupdate=DB::SELECT('SELECT a.TABLE_NAME,
        SUM((SELECT COUNT(x.TABLE_NAME) FROM (SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="'.env('DB_DATABASE', 'forge').'" GROUP BY TABLE_NAME) x WHERE x.TABLE_NAME = a.TABLE_NAME)) AS table_add,
        SUM((SELECT COUNT(COLUMN_NAME) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="'.env('DB_DATABASE', 'forge').'" AND TABLE_NAME = a.TABLE_NAME AND COLUMN_NAME = a.COLUMN_NAME)) AS column_add,
        SUM((SELECT COUNT(COLUMN_NAME) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="'.env('DB_DATABASE', 'forge').'" AND TABLE_NAME = a.TABLE_NAME AND COLUMN_NAME = a.COLUMN_NAME AND 
        (COLUMN_TYPE <> a.COLUMN_TYPE OR IS_NULLABLE <> a.IS_NULLABLE OR COLUMN_KEY <> a.COLUMN_KEY ))) AS column_modif
        FROM ('.$yyy.') a GROUP BY a.TABLE_NAME' );

        return DataTables::of($dataupdate)       
          ->addIndexColumn()        
          ->addColumn('action', function ($dataupdate) {
              return '
                <button id="prosesUpdate" type="button" class="btn btn-info btn-sm btn-labeled prosesUpdate"><span class="btn-label"><i class="fa fa-list-alt fa-fw fa-lg"></i></span> Edit Usulan</button>
              ';})
          ->make(true);
    }

    public function getJmlTable(Request $request)
    {
        
        $cekTemp = DB::SELECT('SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="'.env('DB_DATABASE', 'forge').'" AND TABLE_NAME="temp_table_info"  GROUP BY TABLE_NAME');

            if($cekTemp != null){
                $deleteTableSqlString = "DELETE FROM temp_table_info";                
                $query = DB::statement($deleteTableSqlString);
            } else {   
                $createTableSqlString =
                    "CREATE TABLE `temp_table_info` (
                          `TBL_INDEX` VARCHAR(255) NULL DEFAULT NULL,
                          `TABLE_SCHEMA` VARCHAR(255) NULL DEFAULT NULL,
                          `TABLE_NAME` VARCHAR(255) NULL DEFAULT NULL,
                          `COLUMN_NAME` VARCHAR(255) NULL DEFAULT NULL,
                          `COLUMN_TYPE` VARCHAR(255) NULL DEFAULT NULL,
                          `IS_NULLABLE` VARCHAR(255) NULL DEFAULT NULL,
                          `COLUMN_KEY` VARCHAR(255) NULL DEFAULT NULL,
                          `COLUMN_DEFAULT` VARCHAR(255) NULL DEFAULT NULL,
                          `EXTRA` VARCHAR(255) NULL DEFAULT NULL,
                          `INDEX_NAME` VARCHAR(255) NULL DEFAULT NULL,
                          `SEQ_IN_INDEX` INT(11) NULL DEFAULT NULL,
                          `NON_UNIQUE` INT(11) NULL DEFAULT NULL,
                          `FLAG` INT(11) NULL DEFAULT NULL,
                          INDEX `TBL_INDEX` (`TBL_INDEX`, `TABLE_NAME`, `COLUMN_NAME`, `IS_NULLABLE`, `COLUMN_KEY`, `INDEX_NAME`,`FLAG`) USING BTREE
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1;";
                $query = DB::statement($createTableSqlString);
            };   

            if($query != 0) {
                $tabel = parse_ini_file(base_path().'/app/update/update1');
                $source = $tabel['entry_info'];
                $update=DB::unprepared($source); 
                if($update != 0) {
                   $getJmlTable=DB::SELECT('SELECT (SELECT COUNT(b.TABLE_NAME) FROM (SELECT y.TABLE_NAME FROM temp_table_info AS y WHERE y.FLAG = 0 GROUP BY y.TABLE_NAME) b) AS jml_table1, 
                        (SELECT COUNT(x.TABLE_NAME) FROM (SELECT a.TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS AS a
                            INNER JOIN INFORMATION_SCHEMA.TABLES AS b ON a.TABLE_SCHEMA=b.TABLE_SCHEMA AND a.TABLE_NAME = b.TABLE_NAME 
                            WHERE a.TABLE_SCHEMA="'.env('DB_DATABASE', 'forge').'" 
                            AND b.TABLE_TYPE <> "VIEW" GROUP BY a.TABLE_NAME, a.TABLE_SCHEMA, b.TABLE_TYPE) x) AS jml_table0, 
                        (SELECT COUNT(b.COLUMN_NAME) AS jml_table1 FROM temp_table_info b WHERE b.FLAG = 0 GROUP BY b.TABLE_SCHEMA) AS jml_kolom1, 
                        (SELECT COUNT(a.COLUMN_NAME) FROM INFORMATION_SCHEMA.COLUMNS AS a
                            INNER JOIN INFORMATION_SCHEMA.TABLES AS b ON a.TABLE_SCHEMA=b.TABLE_SCHEMA AND a.TABLE_NAME = b.TABLE_NAME 
                            WHERE a.TABLE_SCHEMA="'.env('DB_DATABASE', 'forge').'"  AND b.TABLE_TYPE LIKE "%TABLE%" 
                            GROUP BY a.TABLE_SCHEMA, b.TABLE_TYPE ) AS jml_kolom0, 
                        (SELECT SUM((SELECT COUNT(COLUMN_NAME) FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="'.env('DB_DATABASE', 'forge').'" AND TABLE_NAME = a.TABLE_NAME 
                        AND COLUMN_NAME = a.COLUMN_NAME AND (COLUMN_TYPE <> a.COLUMN_TYPE OR IS_NULLABLE <> a.IS_NULLABLE))) 
                        AS column_modif FROM temp_table_info a WHERE a.FLAG = 0) AS column_modif,
                        (SELECT COUNT(a.TRIGGER_NAME) FROM ('.$tabel['update2'].') a GROUP BY TRIGGER_SCHEMA) AS jml_trigger1,
                        (SELECT COUNT(x.TRIGGER_NAME) FROM (SELECT TRIGGER_NAME FROM INFORMATION_SCHEMA.TRIGGERS WHERE TRIGGER_SCHEMA="'.env('DB_DATABASE', 'forge').'") x ) AS jml_trigger0,
                        (SELECT COUNT(a.ROUTINE_NAME) FROM ('.$tabel['update3'].') a GROUP BY ROUTINE_SCHEMA) AS jml_prosedur1,
                        (SELECT COUNT(x.ROUTINE_NAME) FROM (SELECT ROUTINE_NAME FROM INFORMATION_SCHEMA.ROUTINES WHERE ROUTINE_SCHEMA="'.env('DB_DATABASE', 'forge').'") x ) AS jml_prosedur0 '
                        );
                        return json_encode($getJmlTable);  
                } else {
                    return response ()->json(['pesan'=>'Gagal Load Database Master','status_pesan'=>'0']); 
                }
            } else {
                return response ()->json(['pesan'=>'Gagal Menyiapkan Database Master','status_pesan'=>'0']); 
            }        
    }

    public function BuatTable(Request $request)
    {
        // $tabel = parse_ini_file(base_path().'/app/update/update1a');
        $buattable = parse_ini_file(base_path().'/app/update/update4');

        $query = DB::SELECT('SELECT a.TBL_INDEX, a.TABLE_NAME 
            FROM (SELECT x.TBL_INDEX, x.TABLE_NAME FROM temp_table_info x WHERE x.FLAG = 0 GROUP BY x.TBL_INDEX, x.TABLE_NAME ORDER BY x.TBL_INDEX ASC) a 
            WHERE a.TABLE_NAME NOT IN (SELECT TABLE_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="'.env('DB_DATABASE', 'forge').'" 
            GROUP BY TABLE_NAME) GROUP BY a.TBL_INDEX, a.TABLE_NAME ORDER BY a.TBL_INDEX ASC');

        if(count($query)>0){
            foreach ($query as $q) {
                $test = $buattable[$q->TABLE_NAME];
                $update=DB::unprepared($test);
            };
            if($update != 0) {
                return response ()->json(['pesan'=>'Sejumlah Tabel Telah Berhasil Ditambahkan','status_pesan'=>'1']); 
            } else {
                return response ()->json(['pesan'=>'Sejumlah Tabel Gagal Ditambahkan','status_pesan'=>'0']); 
            }
        } else {
            return response ()->json(['pesan'=>'Tidak Ada Script Tambah Tabel Yang Dijalankan','status_pesan'=>'0']);
        };
    }

    public function BuatTrigger(Request $request)
    {
        $tabel = parse_ini_file(base_path().'/app/update/update1');
        $buatTrigger = parse_ini_file(base_path().'/app/update/update4');

        $query = DB::SELECT('SELECT TRIGGER_NAME, CONCAT("DROP TRIGGER IF EXISTS ", TRIGGER_NAME, ";") AS Query_Script
        FROM INFORMATION_SCHEMA.TRIGGERS WHERE TRIGGER_SCHEMA="'.env('DB_DATABASE', 'forge').'"');

        $queryExe = DB::SELECT('SELECT a.TRIGGER_NAME FROM ('.$tabel['update2'].') a');

        if(count($query)>0){
            foreach ($query as $q) {
                $test = $q->Query_Script;
                $Kosong=DB::unprepared($test);
            };
            foreach ($queryExe as $p) {
                $source = $buatTrigger[$p->TRIGGER_NAME];
                $update=DB::unprepared($source);
            };
            if($update != 0) {
                 return response ()->json(['pesan'=>'Sejumlah Trigger Telah Berhasil Ditambahkan','status_pesan'=>'1']);   
            } else {
                return response ()->json(['pesan'=>'Sejumlah Trigger Gagal Ditambahkan','status_pesan'=>'0']); 
            }
        } else {
            foreach ($queryExe as $p) {
                $source = $buatTrigger[$p->TRIGGER_NAME];
                $update=DB::unprepared($source);
            };
            if($update != 0) {
                 return response ()->json(['pesan'=>'Sejumlah Trigger Telah Berhasil Ditambahkan','status_pesan'=>'1']);   
            } else {
                return response ()->json(['pesan'=>'Sejumlah Trigger Gagal Ditambahkan','status_pesan'=>'0']); 
            }
        };
    }

    public function BuatFungsi(Request $request)
    {
        // $tabel = parse_ini_file(base_path().'/app/update/update1a');
        $buatFungsi = parse_ini_file(base_path().'/app/update/update4');

        $query = DB::SELECT('SELECT ROUTINE_NAME, CONCAT("DROP FUNCTION IF EXISTS ", ROUTINE_NAME, ";") AS Query_Script
            FROM INFORMATION_SCHEMA.ROUTINES WHERE ROUTINE_TYPE="FUNCTION" AND ROUTINE_SCHEMA="'.env('DB_DATABASE', 'forge').'"');

        if(count($query)>0){
            foreach ($query as $q) {
                $test = $q->Query_Script;
                $Kosong=DB::unprepared($test);
            };
            $source = $buatFungsi['buatFungsi'];
            $update=DB::unprepared($source);
            if($update != 0) {
                 return response ()->json(['pesan'=>'Sejumlah Fungsi/Prosedur Telah Berhasil Ditambahkan','status_pesan'=>'1']); 
            } else {
                return response ()->json(['pesan'=>'Sejumlah Fungsi/Prosedur Gagal Ditambahkan','status_pesan'=>'0']); 
            }
        } else {
            $source = $buatFungsi['buatFungsi'];
            $update=DB::unprepared($source);
            if($update != 0) {
                 return response ()->json(['pesan'=>'Sejumlah Fungsi/Prosedur Telah Berhasil Ditambahkan','status_pesan'=>'1']);   
            } else {
                return response ()->json(['pesan'=>'Sejumlah Fungsi/Prosedur Gagal Ditambahkan','status_pesan'=>'0']); 
            }
        };
    }

    public function BuatForeignKey(Request $request)
    {
        // $tabel = parse_ini_file(base_path().'/app/update/update1a');
         $query1 =DB::SELECT('SELECT CONCAT( "UPDATE ", a.TABLE_NAME, " SET ", a.COLUMN_NAME, "=NOW() WHERE ",a.COLUMN_NAME,"=0;" ) AS query_update 
            FROM INFORMATION_SCHEMA.COLUMNS AS a
            INNER JOIN INFORMATION_SCHEMA.TABLES AS b ON a.TABLE_SCHEMA=b.TABLE_SCHEMA AND a.TABLE_NAME = b.TABLE_NAME 
            WHERE a.TABLE_SCHEMA = "'.env('DB_DATABASE', 'forge').'"  AND a.COLUMN_NAME = "updated_at"  AND b.TABLE_TYPE <> "VIEW"
            AND a.TABLE_NAME NOT IN ( "users", "migrations", "password_resets", "ref_group" )');
        
        if(count($query1)>0){
            foreach ($query1 as $qa) {
                $update1=DB::unprepared($qa->query_update);      
            };        
            if($update1 != 0) {
                $buatKey = parse_ini_file(base_path().'/app/update/update4');
                $query = DB::SELECT('SELECT TABLE_NAME, CONSTRAINT_NAME, CONCAT("ALTER TABLE ", TABLE_NAME, " DROP FOREIGN KEY ", CONSTRAINT_NAME, ";") AS Query_Script
                    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE CONSTRAINT_NAME <> "PRIMARY" AND REFERENCED_TABLE_NAME IS NOT NULL AND CONSTRAINT_SCHEMA="'.env('DB_DATABASE', 'forge').'"
                    GROUP BY TABLE_NAME, CONSTRAINT_NAME');

                    if(count($query)>0){
                        foreach ($query as $q) {
                            $test = $q->Query_Script;
                            $Kosong=DB::unprepared($test);
                        };            
                        $source = $buatKey['tambahKey'];
                        $update=DB::unprepared($source);
                        if($update != 0) {
                            return response ()->json(['pesan'=>'Sejumlah ForeignKey Telah Berhasil Ditambahkan','status_pesan'=>'1']);   
                        } else {
                            return response ()->json(['pesan'=>'Sejumlah ForeignKey Gagal Ditambahkan','status_pesan'=>'0']); 
                        }
                    } else {
                        $source = $buatKey['tambahKey'];
                        $update=DB::unprepared($source);
                        if($update != 0) {
                            return response ()->json(['pesan'=>'Sejumlah ForeignKey Telah Berhasil Ditambahkan','status_pesan'=>'1']);   
                        } else {
                            return response ()->json(['pesan'=>'Sejumlah ForeignKey Gagal Ditambahkan','status_pesan'=>'0']); 
                        }
                    };
            } else {
                return response ()->json(['pesan'=>'Finalisasi Normalisasi Tanggal Tidak Berhasil (Gagal) ','status_pesan'=>'0']); 
            }
        } else {
            
                $buatKey = parse_ini_file(base_path().'/app/update/update4');
                $query = DB::SELECT('SELECT TABLE_NAME, CONSTRAINT_NAME, CONCAT("ALTER TABLE ", TABLE_NAME, " DROP FOREIGN KEY ", CONSTRAINT_NAME, ";") AS Query_Script
                    FROM INFORMATION_SCHEMA.KEY_COLUMN_USAGE WHERE CONSTRAINT_NAME <> "PRIMARY" AND REFERENCED_TABLE_NAME IS NOT NULL AND CONSTRAINT_SCHEMA="'.env('DB_DATABASE', 'forge').'"
                    GROUP BY TABLE_NAME, CONSTRAINT_NAME');

                    if(count($query)>0){
                        foreach ($query as $q) {
                            $test = $q->Query_Script;
                            $Kosong=DB::unprepared($test);
                        };            
                        $source = $buatKey['tambahKey'];
                        $update=DB::unprepared($source);
                        if($update != 0) {
                            return response ()->json(['pesan'=>'Sejumlah ForeignKey Telah Berhasil Ditambahkan','status_pesan'=>'1']);   
                        } else {
                            return response ()->json(['pesan'=>'Sejumlah ForeignKey Gagal Ditambahkan','status_pesan'=>'0']); 
                        }
                    } else {
                        $source = $buatKey['tambahKey'];
                        $update=DB::unprepared($source);
                        if($update != 0) {
                            return response ()->json(['pesan'=>'Sejumlah ForeignKey Telah Berhasil Ditambahkan','status_pesan'=>'1']);   
                        } else {
                            return response ()->json(['pesan'=>'Sejumlah ForeignKey Gagal Ditambahkan','status_pesan'=>'0']); 
                        }
                    };
        };
        
    }

    public function BuatKolom(Request $request)
    {
        // $tabel = parse_ini_file(base_path().'/app/update/update1a');

        $query = DB::SELECT('SELECT a.TBL_INDEX, a.TABLE_NAME, a.COLUMN_NAME, 
            CONCAT("ALTER TABLE ", a.TABLE_NAME," ADD COLUMN ", a.COLUMN_NAME," ", a.COLUMN_TYPE, IF(a.IS_NULLABLE="YES"," NULL "," NOT NULL  "),
            IF(a.COLUMN_DEFAULT="", " DEFAULT NULL ", CONCAT(" DEFAULT  ",a.COLUMN_DEFAULT," ")), IF(a.EXTRA="","", a.EXTRA), ";") AS Query_Script 
            FROM (SELECT x.* FROM temp_table_info x WHERE x.FLAG = 0) a
            LEFT OUTER JOIN (SELECT TABLE_NAME, COLUMN_NAME FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="'.env('DB_DATABASE', 'forge').'" 
            GROUP BY TABLE_NAME, COLUMN_NAME) b ON a.TABLE_NAME=b.TABLE_NAME AND a.COLUMN_NAME = b.COLUMN_NAME
            WHERE b.TABLE_NAME IS NULL AND b.COLUMN_NAME IS NULL ORDER BY a.TBL_INDEX ASC');

        if(count($query)>0){
            foreach ($query as $q) {
                $test = $q->Query_Script;
                $update=DB::unprepared($test);
            };
            if($update != 0) {
                return response ()->json(['pesan'=>'Sejumlah Field Telah Berhasil Ditambahkan','status_pesan'=>'1']); 
            } else {
                return response ()->json(['pesan'=>'Sejumlah Field Gagal Ditambahkan','status_pesan'=>'0']); 
            }
        } else {
            return response ()->json(['pesan'=>'Tidak Ada Script Tambah Kolom Yang Dijalankan','status_pesan'=>'0']);
        };
    }

    public function UpdateAtribut(Request $request)
    {
        // $tabel = parse_ini_file(base_path().'/app/update/update1a');

        $query1 = DB::SELECT('SELECT CONCAT( "ALTER TABLE ", a.TABLE_NAME, " MODIFY COLUMN `updated_at` datetime(0) NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP(0); " ) AS query_update 
            FROM INFORMATION_SCHEMA.COLUMNS AS a
            INNER JOIN INFORMATION_SCHEMA.TABLES AS b ON a.TABLE_SCHEMA=b.TABLE_SCHEMA AND a.TABLE_NAME = b.TABLE_NAME 
            WHERE a.TABLE_SCHEMA = "'.env('DB_DATABASE', 'forge').'"  AND a.COLUMN_NAME = "updated_at"  AND b.TABLE_TYPE <> "VIEW"');

        if(count($query1)>0){
            foreach ($query1 as $q1) {
                $test1 = $q1->query_update;
                $update1=DB::unprepared($test1);
            };
            if($update1 != 0) {
                $query = DB::SELECT('SELECT b.TABLE_SCHEMA, b.TABLE_NAME, b.COLUMN_NAME, b.COLUMN_TYPE AS tipe_tujuan, a.COLUMN_TYPE AS tipe_asal, b.IS_NULLABLE AS null_tujuan, 
                    a.IS_NULLABLE AS null_asal, b.COLUMN_KEY AS key_tujuan, a.COLUMN_KEY AS key_asal,            
                    CONCAT("ALTER TABLE ", b.TABLE_NAME, " MODIFY COLUMN ", b.COLUMN_NAME, " ", a.COLUMN_TYPE,  " ",
                    IF(a.IS_NULLABLE = "YES", "NULL", CONCAT("NOT NULL DEFAULT ", IF(LENGTH(a.COLUMN_DEFAULT)< 1, 0,a.COLUMN_DEFAULT ))), ";") AS Query_Script
                    FROM (SELECT x.* FROM temp_table_info x WHERE x.FLAG = 0) a 
                    INNER JOIN (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="'.env('DB_DATABASE', 'forge').'") b ON b.TABLE_NAME = a.TABLE_NAME AND b.COLUMN_NAME = a.COLUMN_NAME 
                    WHERE (b.COLUMN_TYPE <> a.COLUMN_TYPE OR b.IS_NULLABLE <> a.IS_NULLABLE)');
                if(count($query)>0){
                    foreach ($query as $q) {
                        $test = $q->Query_Script;
                        $update=DB::unprepared($test);
                    };
                    if($update != 0) {
                        return response ()->json(['pesan'=>'Sejumlah Atribut (1) Telah Berhasil Dimodifikasi','status_pesan'=>'1']); 
                    } else {
                        return response ()->json(['pesan'=>'Sejumlah Atribut (1) Gagal Dimodifikasi','status_pesan'=>'0']); 
                    }
                } else {
                    return response ()->json(['pesan'=>'Tidak Ada Script Modifikasi Atribut  (1) Yang Dijalankan','status_pesan'=>'0']);
                };
            } else {
                return response ()->json(['pesan'=>'Sejumlah Atribut (0) Gagal Dimodifikasi','status_pesan'=>'0']); 
            }
        } else {
                $query = DB::SELECT('SELECT b.TABLE_SCHEMA, b.TABLE_NAME, b.COLUMN_NAME, b.COLUMN_TYPE AS tipe_tujuan, a.COLUMN_TYPE AS tipe_asal, b.IS_NULLABLE AS null_tujuan, 
                    a.IS_NULLABLE AS null_asal, b.COLUMN_KEY AS key_tujuan, a.COLUMN_KEY AS key_asal,            
                    CONCAT("ALTER TABLE ", b.TABLE_NAME, " MODIFY COLUMN ", b.COLUMN_NAME, " ", a.COLUMN_TYPE,  " ",
                    IF(a.IS_NULLABLE = "YES", "NULL", CONCAT("NOT NULL DEFAULT ", IF(LENGTH(a.COLUMN_DEFAULT)< 1, 0,a.COLUMN_DEFAULT ))), ";") AS Query_Script
                    FROM (SELECT x.* FROM temp_table_info x WHERE x.FLAG = 0) a 
                    INNER JOIN (SELECT * FROM INFORMATION_SCHEMA.COLUMNS WHERE TABLE_SCHEMA="'.env('DB_DATABASE', 'forge').'") b ON b.TABLE_NAME = a.TABLE_NAME AND b.COLUMN_NAME = a.COLUMN_NAME 
                    WHERE (b.COLUMN_TYPE <> a.COLUMN_TYPE OR b.IS_NULLABLE <> a.IS_NULLABLE)');
                if(count($query)>0){
                    foreach ($query as $q) {
                        $test = $q->Query_Script;
                        $update=DB::unprepared($test);
                    };
                    if($update != 0) {
                        return response ()->json(['pesan'=>'Sejumlah Atribut (1) Telah Berhasil Dimodifikasi','status_pesan'=>'1']); 
                    } else {
                        return response ()->json(['pesan'=>'Sejumlah Atribut (1) Gagal Dimodifikasi','status_pesan'=>'0']); 
                    }
                } else {
                    return response ()->json(['pesan'=>'Tidak Ada Script Modifikasi Atribut  (1) Yang Dijalankan','status_pesan'=>'0']);
                };
        }
    }

    public function TambahAtributUnik(Request $request)
    {
        // $tabel = parse_ini_file(base_path().'/app/update/update1a');

        $query = DB::SELECT('SELECT a.TBL_INDEX, a.INDEX_NAME, COALESCE(a.jml_column_max,0) AS jml_kolom_update, COALESCE(b.jml_column_min,0) AS jml_kolom_lama,
            CONCAT("ALTER TABLE ",a.TBL_INDEX," DROP INDEX ",a.INDEX_NAME,"; ALTER TABLE ",a.TBL_INDEX," ADD UNIQUE INDEX ", a.INDEX_NAME," (",a.name_column,");") AS Query_Drop,
            CONCAT("ALTER TABLE ",a.TBL_INDEX," ADD UNIQUE INDEX ", a.INDEX_NAME," (", a.name_column,");") AS Query_Script
            FROM (SELECT x.TBL_INDEX, x.INDEX_NAME,  COUNT(x.COLUMN_NAME) AS jml_column_max, GROUP_CONCAT(x.COLUMN_NAME) AS name_column
            FROM (SELECT x.* FROM temp_table_info x WHERE x.FLAG = 1 AND x.NON_UNIQUE = 0 AND x.INDEX_NAME = "PRIMARY") AS x GROUP BY x.TBL_INDEX, x.INDEX_NAME) AS a
            LEFT OUTER JOIN (SELECT y.TABLE_NAME, y.INDEX_NAME, COUNT(y.COLUMN_NAME) AS jml_column_min
            FROM INFORMATION_SCHEMA.STATISTICS AS y WHERE y.TABLE_SCHEMA = "'.env('DB_DATABASE', 'forge').'" AND y.NON_UNIQUE = 0 AND y.INDEX_NAME = "PRIMARY"
            GROUP BY y.TABLE_NAME, y.INDEX_NAME) AS b 
            ON a.TBL_INDEX = b.TABLE_NAME AND a.INDEX_NAME = b.INDEX_NAME
            WHERE COALESCE(b.jml_column_min,0)=0');

        if(count($query)>0){
            foreach ($query as $q) {
                if ($q->jml_kolom_lama == 0){
                    $test = $q->Query_Script;
                    $update=DB::unprepared($test);
                } else {
                    $test = $q->Query_Drop;
                    $update=DB::unprepared($test);
                }
            };
            if($update != 0) {  
                return response ()->json(['pesan'=>'Sejumlah Atribut Unik Telah Berhasil Ditambahkan','status_pesan'=>'1']); 
            } else {
                return response ()->json(['pesan'=>'Sejumlah Atribut Unik Gagal Ditambahkan','status_pesan'=>'0']); 
            }
        } else {
            return response ()->json(['pesan'=>'Tidak Ada Script Penambahan Atribut Unik Yang Dijalankan','status_pesan'=>'0']);
        };
    }

    public function UpdateAtributUnik(Request $request)
    {
        // $tabel = parse_ini_file(base_path().'/app/update/update1');

        $query = DB::SELECT('SELECT a.TBL_INDEX, a.INDEX_NAME,COALESCE(b.COLUMN_NAME,0) AS COLUMN_NAME,
            CONCAT("ALTER TABLE ",a.TBL_INDEX," DROP INDEX ",a.INDEX_NAME,"; ALTER TABLE ",a.TBL_INDEX," ADD UNIQUE INDEX ", a.INDEX_NAME," (", GROUP_CONCAT(a.COLUMN_NAME),");") AS Query_Script
            FROM (SELECT x.* FROM temp_table_info x WHERE x.FLAG = 1 AND x.NON_UNIQUE = 0 AND x.INDEX_NAME = "PRIMARY")  AS a
            LEFT OUTER JOIN (SELECT a.TABLE_NAME, a.INDEX_NAME, a.SEQ_IN_INDEX, a.COLUMN_NAME, a.NON_UNIQUE
            FROM INFORMATION_SCHEMA.STATISTICS AS a WHERE a.TABLE_SCHEMA = "'.env('DB_DATABASE', 'forge').'" 
            AND a.NON_UNIQUE = 0 AND a.INDEX_NAME = "PRIMARY" ) AS b 
            ON a.TBL_INDEX = b.TABLE_NAME AND a.INDEX_NAME = b.INDEX_NAME AND a.COLUMN_NAME = b.COLUMN_NAME
            WHERE b.COLUMN_NAME IS NULL 
            GROUP BY a.TBL_INDEX, a.INDEX_NAME, b.COLUMN_NAME;');

        if(count($query)>0){
            foreach ($query as $q) {
                $test = $q->Query_Script;
                $update=DB::unprepared($test);
            };
            if($update != 0) {  
                return response ()->json(['pesan'=>'Sejumlah Atribut Unik Telah Berhasil Dimodifikasi','status_pesan'=>'1']); 
            } else {
                return response ()->json(['pesan'=>'Sejumlah Atribut Unik Gagal Dimodifikasi','status_pesan'=>'0']); 
            }
        } else {
            return response ()->json(['pesan'=>'Tidak Ada Script Modifikasi Atribut Unik Yang Dijalankan','status_pesan'=>'0']);
        };
    }

    public function TambahRefLog(Request $request)
    {
        $createTableSqlString =
            "CREATE TABLE IF NOT EXISTS `ref_log_akses` (
                `id_log` varchar(255) NOT NULL,
                `fl1` varchar(255) DEFAULT NULL,
                `fd1` varchar(255) DEFAULT NULL,
                `fp2` varchar(255) DEFAULT NULL,
                `fu3` varchar(255) DEFAULT NULL,
                `fr4` varchar(255) DEFAULT NULL,
                PRIMARY KEY (`id_log`) USING BTREE
              ) ENGINE=InnoDB DEFAULT CHARSET=latin1 ROW_FORMAT=DYNAMIC;";

        $query = statement($createTableSqlString);
    }

    public function UpdateEnter(Request $request)
    {
        $query =DB::SELECT('SELECT b.TABLE_TYPE, CONCAT( "UPDATE ", a.TABLE_NAME, " SET ", a.COLUMN_NAME, "= GantiEnter(", a.COLUMN_NAME, ");" ) AS query_update 
            FROM INFORMATION_SCHEMA.COLUMNS AS a
            INNER JOIN INFORMATION_SCHEMA.TABLES AS b ON a.TABLE_SCHEMA=b.TABLE_SCHEMA AND a.TABLE_NAME = b.TABLE_NAME 
            WHERE a.TABLE_SCHEMA = "'.env('DB_DATABASE', 'forge').'"  AND a.DATA_TYPE = "VARCHAR"  AND b.TABLE_TYPE <> "VIEW"
            AND a.TABLE_NAME NOT IN ( "users", "migrations", "password_resets", "ref_group" )');
        
        if(count($query)>0){
            foreach ($query as $q) {
                $update=DB::unprepared($q->query_update);      
            };        
            if($update != 0) {
                return response ()->json(['pesan'=>'Finalisasi Update Database Sukses ','status_pesan'=>'1']); 
            } else {
                return response ()->json(['pesan'=>'Finalisasi Update Database Tidak Berhasil (Gagal) ','status_pesan'=>'0']); 
            }
        } else {
            return response ()->json(['pesan'=>'Tidak Ada Finalisasi Update Database  Yang Dijalankan','status_pesan'=>'0']);
        };
    }

    public function NormalisasiTanggal(Request $request)
    {
        $query =DB::SELECT('SELECT CONCAT( "UPDATE ", TABLE_NAME, " SET ", COLUMN_NAME, "=NOW() WHERE ",COLUMN_NAME,"=0;" ) AS query_update 
            FROM INFORMATION_SCHEMA.COLUMNS 
            WHERE TABLE_SCHEMA = "'.env('DB_DATABASE', 'forge').'"  AND COLUMN_NAME = "updated_at" 
            AND TABLE_NAME NOT IN ( "users", "migrations", "password_resets", "ref_group" )');
        
        if(count($query)>0){
            foreach ($query as $q) {
                $update=DB::unprepared($q->query_update);      
            };        
            if($update != 0) {
                return response ()->json(['pesan'=>'Finalisasi Normalisasi Tanggal Sukses ','status_pesan'=>'1']); 
            } else {
                return response ()->json(['pesan'=>'Finalisasi Normalisasi Tanggal Tidak Berhasil (Gagal) ','status_pesan'=>'0']); 
            }
        } else {
            return response ()->json(['pesan'=>'Tidak Ada Normalisasi Tanggal Database  Yang Dijalankan','status_pesan'=>'0']);
        };
    }

    public function TestApiSimda(Request $request)
    {
        // $getUrl = json_decode(@file_get_contents('http://193.168.97.20:8080/datasnap/rest/scapi/KirimUrusan/'.$request->KodeMinta));
        // return response ()->json ($getUrl); 
    }

}