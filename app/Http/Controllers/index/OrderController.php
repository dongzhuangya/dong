<?php

namespace App\Http\Controllers\index;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class OrderController extends Controller
{
    public function index(){
    	$date=DB::table('cart')->where(['uid'=>session('name')->id])->get();
    	// dd($date);
    	$table=0;
    	foreach($date as $v){
    		$table+=$v->price*$v->buy_num;
    		// dd($v->price);
    		// dd($v->buy_num);
    	}
    	// dd($table);
    	return view('order.index',['data'=>$date,'date'=>$table]);
    }
}