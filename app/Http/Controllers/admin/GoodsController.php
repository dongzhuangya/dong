<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class GoodsController extends Controller
{
    public function index(Request $request)
    {
    	$redis = new \Redis();
        $redis->connect('127.0.0.1','6379');
        $redis->incr('num');
        $num = $redis->get('num');
        echo '你的访问次数：'.$num;

        $dd=$request->all();
        // dd($dd);
        $ss='';
        if(!empty($dd['ss'])){
        	$ss=$dd['ss'];
        	$data=DB::table('goodss')->where('goods_name','like','%'.$dd['ss'].'%')->paginate(6);
        }else{
        	$data=DB::table('goodss')->paginate(6);
        }
    	// $data=DB::table('goods')->get();
    	// dd($data);die;
    	return view('goods.index',['data'=>$data,'ss'=>$ss]);
    }
    public function add()
    {
    	return view('goods.add');
    }
    public function addTo(Request $request)
    {	
    	$data=$request->all();
    	$pic=$request->file('pic');
    	$path = $pic->store('goods');
    	$validatedData = $request->validate([
            'goods_name' => 'required',
            'pic' => 'required',
            'goods_kc' => 'required',
        ],[
            'goods_name.required' => '名字不能为空',
            'pic.required' => '图片不能为空',
            'goods_kc.required' => '库存不能为空',
        ]);
    	
    	$res=DB::table('goodss')->insert([
    		'goods_name'=>$data['goods_name'],
    		'goods_kc'=>$data['goods_kc'],
    		'goods_price'=>$data['goods_price'],
    		'goods_pic'=>'/storage'.'/'.$path,
    		'add_time'=>time()

    		]);

    	if($res){
    		return redirect('/goods/index');
    	}else{
    		echo '添加失败';
    	}
    }
    public function delete(Request $request)
    {
    	$data=$request->all();
    	$res=DB::table('goodss')->where(['id'=>$data['id']])->delete();
    	if($res){
    		return redirect('goods/index');
    	}else{
    		return 删除失败;
    	}
    }
    public function update(Request $request){
    	$data=$request->all();
    	$res=DB::table('goodss')->where(['id'=>$data['id']])->first();
    	return view('goods/update',['data'=>$res]);
    }
    public function updateTo(Request $request)
    {
    	$data=$request->all();
    	$file=$request->file('pic');
    	 if (empty($file)){
            $res = DB::table('goodss')->where(['id'=>$data['id']])->update([
                'goods_name' => $data['goods_name'],
                'goods_kc' => $data['goods_kc'],
                'goods_price'=>$data['goods_price'],
                'add_time' => time()
            ]);
            if ($res){
                return redirect('/goods/index');
            }else{
                echo '修改失败';
            }
            die();
        }
           $path = $file->store('goodss');
            $res = DB::table('goods')->where(['id'=>$data['goods_id']])->update([
                'goods_name' => $data['goods_name'],
                'pic' => '/storage'.'/'.$path,
                'goods_kc' => $data['goods_kc'],
                'goods_price'=>$data['goods_price'],
                'add_time' => time()
            ]);


            if ($res){
                return redirect('/goods/index');
            }else{
                echo '修改失败';
            }
    }
}
