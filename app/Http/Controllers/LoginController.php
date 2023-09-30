<?php

namespace App\Http\Controllers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;

use Illuminate\Http\Request;

class LoginController extends Controller
{
    public function authenticateUser(Request $request){
    	if(Auth::attempt(['name' => $request->name, 'password' => $request->password])){
    		$user = Auth::user();
			return redirect('/home');
		}else{
			return Redirect::back()->withErrors(['message' => 'User not found']);
		}
    }

    public function logout(Request $request){
    	Auth::logout();
 
	    $request->session()->invalidate();
	 
	    $request->session()->regenerateToken();
	    return redirect('new/user-login');
    }

    public function registerUser(){
    	$user = new \App\User();
		$user->password = \Hash::make('posyasa');
		$user->email = '';
		$user->name = '65100';
		$user->save();
    }
}
