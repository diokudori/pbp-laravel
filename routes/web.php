<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('home', 'UserController@home')->name('home');

Route::get('/', function () {
    return view('welcome');
});

Route::get('/bast', function () {
    return view('layout.bast');
});

Route::get('/undangan', function () {
    return view('layout.undangan');
});

Route::post('/login', 'LoginController@authenticateUser')->name('login');
Route::post('/logout', 'LoginController@logout')->name('logout');
Route::get('/reg', 'LoginController@registerUser')->name('reg');

Route::get('/new/user-login',  function(){
	if (Auth::check()) {
		// echo "string";
		return redirect('/home');
	}


	return view('auth.login');
		
})->name('login');

Route::prefix('new')->group(function(){
		Route::get('/bast', 'UserController@bastForm')->name('bast');
		Route::get('/undangan', 'UserController@undanganForm')->name('undangan');

		Route::get('/generate/bast', 'UserController@generateBast')->name('generate-bast');
		Route::get('/generate/undangan', 'UserController@generateUndangan')->name('generate-undangan');


		
});

Route::get('/kabupaten/list', function (Request $request) {
	if(Auth::user()->admin==0){
		$user = DB::table('users')->where('id', Auth::user()->id)->first();
	}else{
		$user = DB::table('users')->where('name', $request->table)->first();
	}
	
    $data = DB::table($user->name)->select("kabupaten")->groupBy('kabupaten')->get();
    return Response::JSON($data);
});

Route::get('/kecamatan/list', function (Request $request) {
	if(Auth::user()->admin==0){
		$user = DB::table('users')->where('id', Auth::user()->id)->first();
	}else{
		$user = DB::table('users')->where('name', $request->table)->first();
	}
    $data = DB::table($user->name)->select("kecamatan")->where("kabupaten",$request->kab)->groupBy('kecamatan')->get();
    return Response::JSON($data);
});

Route::get('/kelurahan/list', function (Request $request) {
	if(Auth::user()->admin==0){
		$user = DB::table('users')->where('id', Auth::user()->id)->first();
	}else{
		$user = DB::table('users')->where('name', $request->table)->first();
	}
    $data = DB::table($user->name)->select("kelurahan")->where("kecamatan",$request->kec)->groupBy('kelurahan')->get();
    return Response::JSON($data);
});


