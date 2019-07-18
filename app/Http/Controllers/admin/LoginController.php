<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class LoginController extends Controller
{
    public function index()
    {
    	return view('login.index');
    }
    public function dong(Request $request)
    {
    	$name=$request->all()['name'];
    	$pwd=$request->all()['pwd'];
    	$date=DB::table('admin')->where(['name'=>$name])->first();
    	// dd($date);
    	if($pwd!=$date->pwd){
    		echo '密码有误';
    	}else{
    		session('data','info');
    		return redirect('index/index');
    	}

    	
    }
    public function zuce()
    {
    	return view('login.zuce');
    }
    public function zuceTo(Request $request)
    {
    	$data=$request->all();
    	$res=DB::table('admin')->insert([
    		'name'=>$data['name'],
    		'pwd'=>$data['pwd'],
    		'create_time'=>time()
    		]);
    	if($res){
    		return redirect('login/index');
    	}else{
    		return 1;
    	}
    }
}
