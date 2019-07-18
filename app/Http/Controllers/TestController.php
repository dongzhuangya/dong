<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
class TestController extends Controller
{
    public function index(Request $request)
    {	
    	$cc=$request->all();
    	// var_dump($cc);die;
    	$sss='';
    	if(!empty($cc['sss'])){
    		$sss=$cc['sss'];
    		$res=DB::table('cate')->where('cate_name','like','%'.$cc['sss'].'%')->paginate(5);
    	}else{
    		$res=DB::table('cate')->paginate(5);
    	}
 
    	return view('test',['ccc'=>$res,'sss'=>$sss]);
    }
    public function add()
    {
    	return view('add');
    }
    public function addTo(Request $request){
    	$data=$request->all();
    	 $validatedData = $request->validate([
	        'cate_name' => 'required',
    	],[
    		'cate_name.required'=>'m名字必填'
    	]);
    	$res=DB::table('cate')->insert([
    		'cate_name'=>$data['cate_name']
    		]);
    	if($res){
    		return redirect('/test/index');
    	}else{
    		return "fail";
    	}	
    }
    public function updata(Request $request)
    {
    	$res=$request->all();
    	// dd($res);die;
    	$dd=DB::table('cate')->where(['cate_id'=>$res['id']])->first();
    	// dd($dd);die;
    	return view('updata',['data'=>$dd]);
    }
    public function updataTo(Request $request)
    {
    	$res=$request->all();
    	// dd($res);
    	$dd=DB::table('cate')->where(['cate_id'=>$res['cate_id']])->update(['cate_name'=>$res['cate_name']]);
    	if($dd){
    		return redirect('test/index');
    	}else{
    		return '1';
    	}
    }
    public function delete(Request $request)
    {
    	$res=$request->all();
    	// dd($res);die;
    	$dd=DB::table('cate')->where(['cate_id'=>$res['id']])->delete();
    	if($dd){
    		return redirect('test/index');
    	}else{
    		return ;
    	}
    }
    public function login()
    {
    	return view('login');
    }

}
