<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class HorseController extends Controller
{
    public function add()
    {
    	return view('horse.add');
    }
    public function addTo(Request $request)
    {
    	$data=$request->all();
    	// dd($data);
    	$res=DB::table('host')->insert([
    		'train'=>$data['train'],
    		'place'=>$data['place'],
    		'arrival'=>$data['arrival'],
    		'price'=>$data['price'],
    		'amount'=>$data['amount'],
    		'goofftime'=>$data['goofftime'],
    		'arrivaltime'=>$data['arrivaltime']
    		]);
    	if($res){
    		return redirect('horse/index');
    	}else{
    		return ;
    	}
    }
    public function index(Request $request)
    {
    	$redis = new \Redis();
        $redis->connect('127.0.0.1','6379');
        // dd($redis);
        $redis->incr('666');
        $num = $redis->get('666');
        echo '你的访问次数：'.$num;
    	$dd=$request->all();
    	$sss='';
    	if(!empty($dd['sss'])){
    		$sss=$dd['sss'];
    		$data=DB::table('host')->where('place','like','%'.$dd['sss'].'%')->Orwhere('arrival','like','%'.$dd['sss'].'%')->get();
    	}else{
    		$data=DB::table('host')->get();
    	}
    	

    	return view('horse.index',['data'=>$data,'sss'=>$sss]);
    }
    public function aa()
    {
    	return view('horse.aa');
    }
    public function aaTo(Request $request)
    {
    	$name=$request->all()['name'];
    	$pwd=$request->all()['pwd'];
    	$data=DB::table('admin')->where('name',$name)->first();
    	// dd($data);
    	if($pwd=$data->pwd){
    		return redirect('horse/list');
    	}else{
    		return '密码错误';
    	}

    }
    public function list()
    {
    	return view('horse.list');
    }
    public function aT(Request $request)
    {
    	$xxx=$request->all();
    	$cc=$xxx['xxx'];
    	return view('horse.aT',['xxx'=>$cc]);
    }
    public function qq(Request $request)
    {
    	$data=$request->all();

    	dd($data);
    }

}
