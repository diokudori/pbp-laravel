<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use PDF;
use DB;
use Illuminate\Support\Facades\Auth;
class UserController extends Controller
{
	public $table;
	function __construct(){
        
        $this->middleware('auth');
        
     }

     function getUser(){
     	$user = DB::table('users')->where('id', Auth::user()->id)->first();
        return $user;
    }

    public function home(){
    	if(Auth::user()->admin==1){
    		return view('user/dashboard');
    	}else{
    		return redirect('new/bast');
    	}
        
    }

    public function bastForm(){
    	$data['wilayah'] = DB::table('users')->where('id','!=','34')->get(); 
    	$data['wil'] = Auth::user()->name;
    	return view('user/bast-form')->with($data);
    }

    public function generateBast(Request $request){

    	$enabled = DB::table('settings')->where('name','enable_generate_bast')->first();
    	if($enabled->value=='0'){
    		die();
    	}

    	if(Auth::user()->admin==0){
			$user = DB::table('users')->where('id', Auth::user()->id)->first();
		}else{
			$user = DB::table('users')->where('name', $request->wilayah)->first();
		}
    		
    		$list = DB::table($user->name)
    		->where("kabupaten", $request->kabupaten)
    		->where("kecamatan", $request->kecamatan)
    		->where("kelurahan", $request->kelurahan)
    		->orderBy("nama","asc")
    		// ->limit("35")
    		->get();
         $provinsi = DB::table($user->name)->first()->provinsi;
    		$chunk = array_chunk($list->toArray(), 15);
    		$data = [
    			"provinsi"=> $provinsi,
            "kabupaten"=> $request->kabupaten,
    			"kecamatan"=> $request->kecamatan,
    			"kelurahan"=> $request->kelurahan,
    			"list" => $chunk,
    			"kprk" => $list[0]->kprk,
    			"prefik" => $list[0]->prefik
    		];

    	$pdf = PDF::chunkLoadView('<html-separator/>','layout.bast-new', $data, [], [
		    'title' => 'Another Title',
		    'margin_top' => 0
		]);
		// $pdf->getMpdf()->SetFooter('|{PAGENO} of {nbpg}|');

		$pdf->showImageErrors = true;


        return $pdf->stream(time().'.pdf');
    }

    public function undanganForm(){
    	$data['wilayah'] = DB::table('users')->where('id','!=','34')->get();
    	$data['wil'] = Auth::user()->name;
    	return view('user/undangan-form')->with($data);
    }

    public function generateUndangan(Request $request){
    	$enabled = DB::table('settings')->where('name','enable_generate_undangan')->first();
    	if($enabled->value=='0'){
    		die();
    	}
    	if(Auth::user()->admin==0){
			$user = DB::table('users')->where('id', Auth::user()->id)->first();
		}else{
			$user = DB::table('users')->where('name', $request->wilayah)->first();
		}
    		$list = DB::table($user->name)
         ->where("kabupaten", $request->kabupaten)
    		->where("kecamatan", $request->kecamatan)
    		->where("kelurahan", $request->kelurahan)
    		->orderBy("nama","asc")
    		// ->limit("6")
    		->get();

         $provinsi = DB::table($user->name)->first()->provinsi;

    		// print_r($list->count());
    		// die();
    		$chunk = array_chunk($list->toArray(), 3);
    		$data = [
    			"provinsi"=> $provinsi,
            "kabupaten"=> $request->kabupaten,
    			"kecamatan"=> $request->kecamatan,
    			"kelurahan"=> $request->kelurahan,
    			"list" => $chunk,
    			"kprk" => $list[0]->kprk,
    			"prefik" => $list[0]->prefik
    		];
    		// return view("layout.undangan")->with($data);
    	$pdf = PDF::chunkLoadView('<html-separator/>','layout.undangan', $data, [], [
		    'title' => 'Another Title',
		    'margin_top' => 0
		]);

		$pdf->showImageErrors = true;


        return $pdf->stream(time().'.pdf');
    }
}
