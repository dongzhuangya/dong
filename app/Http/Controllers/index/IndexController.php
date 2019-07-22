<?php

namespace App\Http\Controllers\index;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class IndexController extends Controller
{
    public function index()
    {  
    	$data=DB::table('goodss')->paginate(6);
    	return view('index.index',['data'=>$data]);
    }
    public function liebiao(Request $request){
    	$id=$request->all()['id'];
    	// dd($id);die;

    	$data=DB::table('goodss')->where('id',$id)->first();
    
    	return view('index.goods',['data'=>$data]);
     }
    public function ding()
    {
        return view('index.ding');
    }
    public function dan(Request $request)
    {

        if(!session('name')){
            return redirect('login/index',);
        }
        $id=$request->all();
        // dd($id);
        $data=DB::table('goodss')->where(['id'=>$id])->first();
        // dd($data);
        $res=DB::table('cart')->insert([
            'uid'=>session('name')->id,
            'gname'=>$data->goods_name,
            'img'=>$data->goods_pic,
            'price'=>$data->goods_price,
            'buy_num'=>1,
            'create_time'=>$data->add_time
            ]);
        if($res){
            return redirect('order/index');
        }else{
            return ;
        }
    }
}
