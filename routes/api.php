<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::any('/login', function(Request $request){
    $data = $request->json()->all();
    // print_r($data);
    $password = \Hash::make($data['password']);
    $user = DB::table('users')->where('name',$data['username'])->get();
    if($user->count()>0){
        $pass = Hash::check($data['password'], $user[0]->password);
        if(!$pass){
            $user = [];
        }
    }
    return Response::JSON($user);

});

Route::any('/create-pass', function(Request $request){
    // $data = $request->json()->all();
    // print_r($data);
    $password = \Hash::make($request->pass);
    
    
    return Response::JSON([$password]);

});

Route::any('/data/dashboard/all', function(Request $request){
    // $data = $request->json()->all();
    // print_r($data);
    $user = DB::table('users')->where('admin','0')->get();
    $totalAll = 0;
    $totalReal = 0;
    $totalNotReal = 0;
    $totalPercent = 0;
    // die();
    // echo "string";
    foreach ($user as $key => $value) {
        try{
            $tmpAll = DB::table($value->name)->select(DB::RAW("count(*)as total"))->first();
            $tmpReal = DB::table($value->name)->select(DB::RAW("count(*)as total"))->where('tgl_serah','!=','')->first();
            $totalAll += $tmpAll->total;
            $totalReal += $tmpReal->total;
        }catch(\Illuminate\Database\QueryException $ex){ 
          // dd($ex->getMessage()); 
          // Note any method of class PDOException can be called on $ex.
            continue;
        }
        
        
        // echo $tmpAll->total;
    }

    $totalNotReal = $totalAll-$totalReal;
    $totalPercent = ($totalReal/$totalAll)*100;
    
    $totalAll = number_format($totalAll,0,",",".");
    $totalReal = number_format($totalReal,0,",",".");
    $totalNotReal = number_format($totalNotReal,0,",",".");
    $totalPercent = number_format($totalPercent,2,",",".");
    return Response::JSON(["totalAll"=>$totalAll, "totalReal"=> $totalReal, "totalNotReal"=>$totalNotReal, "totalPercent"=> $totalPercent]);

});


Route::any('/data/dashboard/wilayah', function(Request $request){
    // $data = $request->json()->all();
    // print_r($data);
    $user = DB::table('users')->where('admin','0')->get();
    $totalAll = 0;
    $totalReal = 0;
    $totalNotReal = 0;
    $totalPercent = 0;
    // die();
    // echo "string";
    $arr = [];
    foreach ($user as $key => $value) {
        try{
            $tmpAll = DB::table($value->name)->select(DB::RAW("count(*)as total"))->first();
            $tmpReal = DB::table($value->name)->select(DB::RAW("count(*)as total"))->where('tgl_serah','!=','')->first();
            $totalAll = $tmpAll->total;
            $totalReal = $tmpReal->total;
            $totalNotReal = $totalAll-$totalReal;
            $totalPercent = ($totalReal/$totalAll)*100;
            $totalAll = number_format($totalAll,0,",",".");
            $totalReal = number_format($totalReal,0,",",".");
            $totalNotReal = number_format($totalNotReal,0,",",".");
            $totalPercent = number_format($totalPercent,2,",",".");
            $tmpArr = ["name"=>$value->name, "email"=>$value->email, "totalAll"=>$totalAll, "totalReal"=> $totalReal, "totalNotReal"=>$totalNotReal, "totalPercent"=> $totalPercent];
            array_push($arr, $tmpArr);
        }catch(\Illuminate\Database\QueryException $ex){ 
          // dd($ex->getMessage()); 
          // Note any method of class PDOException can be called on $ex.
            continue;
        }
        
    }

    $chunk = array_chunk($arr, ceil(count($arr) / 2));
    
    
    return Response::JSON($chunk);

});

function cmp($a, $b) {

   return $a["value"] - $b["value"];
}

function build_sorter($key) {
    return function ($a, $b) use ($key) {
        return strnatcmp($a[$key], $b[$key]);
    };
}

Route::get('/settings/enabled/{param}', function($param){
    DB::table('settings')->where('name','enable_generate_bast')->update(['value'=>$param]);
    DB::table('settings')->where('name','enable_generate_undangan')->update(['value'=>$param]);
    return Response::JSON(["status"=>"true"]);
});

Route::any('/data/dashboard/wilayah/filter', function(Request $request){
    $data = $request->all();
    $arr = $data['dataAll'];
    $tmp = [];
    $filter = $data['filter'];
    foreach ($arr as $key => $value) {
        // echo $filter;
        $val =  $arr[$key][$filter];

        array_push($tmp, ["key"=>$key, "value"=>$val]);
    }

    $tmp2 = [];
    usort($tmp, build_sorter("value"));

    foreach ($tmp as $item) {
        // echo $item['key'] . ', ' . $item['value'] . "\n";
        array_push($tmp2, $arr[$item['key']]);
    }
    if($data['order_by']=='desc'){
        $tmp2 = array_reverse($tmp2);
    }
    // print_r($arr);
    $chunk = array_chunk($tmp2, ceil(count($tmp2) / 2));
    
    
    return Response::JSON($chunk);

});

Route::any('/kabupaten/list', function (Request $request) {
    $data = $request->json()->all();
    $user = DB::table('users')->where('id', $data['user_id'])->first();
    $data = DB::table($user->name)->select("kabupaten")->groupBy('kabupaten')->get();
    return Response::JSON($data);
});

Route::any('/kecamatan/list', function (Request $request) {
    $data = $request->json()->all();
    $user = DB::table('users')->where('id', $data['user_id'])->first();
    $data = DB::table($user->name)->select("kecamatan")->where("kabupaten",$data['kab'])->groupBy('kecamatan')->get();
    return Response::JSON($data);
});

Route::any('/kelurahan/list', function (Request $request) {
    $data = $request->json()->all();
    $user = DB::table('users')->where('id', $data['user_id'])->first();
    $data = DB::table($user->name)->select("kelurahan")->where("kecamatan",$data['kec'])->groupBy('kelurahan')->get();
    return Response::JSON($data);
});

Route::any('/data/list', function (Request $request) {
    $data = $request->json()->all();
    $user = DB::table('users')->where('id', $data['user_id'])->first();
    $data = DB::table($user->name)->select("*")
    ->where("kabupaten",$data['kab'])
    ->where("kecamatan",$data['kec'])
    ->where("kelurahan",$data['kel']);
    // $data_belum = DB::table($user->name)->where("tgl_serah","")->get();
    $total = $data->get();
    $belum_foto = $data->where('tgl_serah','')->orderBy('no_urut','asc')->get();
    $sudah_foto = $total->count()-$belum_foto->count();

    

    $resp = ["total"=>$total->count(), "sudah_foto" => $sudah_foto, "belum_foto" => $belum_foto->count(), "data_belum"=>$belum_foto, "data_total"=>$total];

    return Response::JSON([$resp]);
});

Route::any('/data/nomor', function (Request $request) {
    $data = $request->json()->all();
    if($data['nomor']!=""){
        $user = DB::table('users')->where('id', $data['user_id'])->first();


        $resp = DB::table($user->name)->select("*")
        ->where("kabupaten",$data['kab'])
        ->where("kecamatan",$data['kec'])
        ->where("kelurahan",$data['kel']);

        $tmp = $resp->where("no_urut",$data['nomor'])->get();


        if($tmp->count()==0){
            $resp = $resp->where("nama","like","%".$data['nomor']."%")->get();
        }else{
            $resp = $tmp;
        }


    }else{
        $resp = [];
    }
    

    return Response::JSON($resp);
});

Route::any('/data/update', function (Request $request) {
    $data = $request->json()->all();
    $user = DB::table('users')->where('id', $data['user_id'])->first();
    $date = date('Y-m-d H:i:s');
    $resp = DB::table($user->name)->where("id", $data['id'])->update(
        ["tgl_serah"=>$date, "transactor"=>$data['user_id'], "path_ktp"=>$data['path_ktp'], "path_pbp"=>$data['path_pbp']]);

    return Response::JSON(["status"=>$resp, "tgl_serah"=>$date]);
});



Route::any('/data/tgl_upload', function (Request $request) {
    die();
    $table = DB::table("users")->whereNotIn('id', ['34'])->get();
    foreach($table as $t => $tv){
        DB::statement("ALTER TABLE `".$tv->name."` ADD `tgl_upload` VARCHAR(25) NOT NULL DEFAULT '' AFTER `path_pbp`");
    }
    

});

Route::any('/data/urutkan', function (Request $request) {

    die();

    if(isset($request->kprk)){
        $table = DB::table("users")->where("name",$request->kprk)->get();
    }else{
        $table = DB::table("users")
        // ->where("id",">","18")
        ->whereNotIn('id', ['1','4','15','18'])
        // ->where("name","!=","65100")
        // ->orWhere("name","!=","64100")
        ->get();
    }
   

   foreach($table as $t => $tv){
        // $check = DB::table('INFORMATION_SCHEMA_COLUMNS')->select('')
        // DB::statement("ALTER TABLE `".$tv->name."` ADD `tgl_serah` VARCHAR(25) NOT NULL DEFAULT '' AFTER `prefik`, ADD `transactor` INT NULL DEFAULT NULL AFTER `tgl_serah`, ADD `no_urut` INT NOT NULL AFTER `id`");
        DB::statement("ALTER TABLE `".$tv->name."` ADD `path_pbp` TEXT NULL DEFAULT NULL AFTER `transactor`, ADD `path_ktp` TEXT NULL DEFAULT NULL AFTER `path_pbp`");
        // $kec = DB::table($tv->name)->select("kecamatan")->groupBy("kecamatan")->get();
        // foreach($kec as $k => $v){
        //     $kel = DB::table($tv->name)->select("kelurahan")->where("kecamatan", $v->kecamatan)->groupBy("kelurahan")->get();
        //     foreach($kel as $k1 => $v1){
        //         $d = DB::table($tv->name)->select("*")
        //         ->where("kecamatan", $v->kecamatan)
        //         ->where("kelurahan", $v1->kelurahan)
        //         ->orderBy("nama","asc")->get();
        //        $counter = 1;
        //        foreach($d as $k2 => $v2){
        //             DB::table($tv->name)->where("id",$v2->id)->update(["no_urut"=>$counter]);
        //             $counter++;
        //        }
        //     }

           
        // }
   }
    // $table = "65100";
    


    return Response::JSON(["status"=>true]);
});

Route::any('/data/offline/wilayah', function (Request $request) {
    $data = $request->json()->all();
    // $data = $request->all();
    $user = DB::table('users')->where('id', $data['user_id'])->first();
    $kab = DB::table($user->name)->select("kabupaten")->groupBy("kabupaten")->get();
    $resp = [];
    foreach($kab as $k => $v){
        $resp[] = ['name'=>$v->kabupaten,'data'=>[]];
        $kec = DB::table($user->name)->select("kecamatan")->where('kabupaten',$v->kabupaten)->groupBy("kecamatan")->get();
        foreach($kec as $k2 => $v2){
            $resp[$k]['data'][$k2] = ['name'=>$v2->kecamatan,'data'=>[]];
            $kel = DB::table($user->name)->select("kelurahan")->where('kecamatan',$v2->kecamatan)->groupBy("kelurahan")->get();
            foreach($kel as $k3 => $v3){
                $resp[$k]['data'][$k2]['data'][$k3] = ['name'=>$v3->kelurahan];
                
            }
        }
    }

    return Response::JSON($resp);
});


Route::any('/data/offline/list', function (Request $request) {
    // $data = $request->json()->all();
    $data = $request->all();
    $user = DB::table('users')->where('id', $data['user_id'])->first();
    $resp = DB::table($user->name)->select("*")->get();

    return Response::JSON($resp);
});

Route::any('/data/offline/upload', function (Request $request) {
    $data = $request->json()->all();
    // $data = $request->all();
    $user = DB::table('users')->where('id', $data['user_id'])->first();
    $total = 0;
    foreach($data['data'] as $k => $v){
        $resp = DB::table($user->name)->where('id',$v['id'])->update(['tgl_serah'=>$v['tgl_serah'], 'path_pbp'=>$v['path_pbp'], 'transactor'=>$data['user_id'], 'tgl_upload'=>date('Y-m-d H:i:s')]);
        if($resp){
            $total++;
        }
    }
    

    return Response::JSON(["status"=>true, "total"=>$total]);
});

