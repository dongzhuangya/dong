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

}
