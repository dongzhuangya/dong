<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class HhooController extends Controller
{
    public function add()
    {
        return view('hhoo.add');
    }
    public function addTo(Request $request)
    {
        $name=$request->all()['name'];
        $namm=$request->all()['namm'];
        $atime=$request->all()['atime'];
        $atime=strtotime($atime);
        if($name==$namm){
            echo '竞猜球队不可以同名';
        }
        $res=\DB::table('ddd')->insert([
           'name'=>$name,
           'namm'=>$namm,
           'atime'=>$atime
        ]);
        if($res){
            return redirect('hhoo/list');
        }else{
            return 2;
        }
    }
    public function list()
    {
        $data=\DB::table('ddd')->get();
        return view('hhoo.list',['data'=>$data]);
    }
    public function app(Request $request)
    {
        $id=$request->all()['id'];
        $data=\DB::table('ddd')->where('id',$id)->first();
        return view('hhoo/app',['data'=>$data]);

    }
    public function appTo(Request $request)
    {
        $data=$request->all();
        $id=$data['id'];
        $rend=mt_rand(1,3);
        $res=\DB::table('ddd')->where('id',$id)->update([
            'name'=>$data['name'],
            'namm'=>$data['namm'],
            'sss'=>$rend,
            'aaa'=>$data['sex']
        ]);
        if($res){
            return redirect()->action('admin\HhooController@ccc',['id'=>$id]);
        }else{
            return ;
        }
    }
    public function ccc(Request $request)
    {
        $id=$request->all()['id'];
        $data=\DB::table('ddd')->where('id',$id)->first();
        return view('hhoo/ccc',['data'=>$data]);
    }
    public function ass(Request $request)
    {
        $id=$request->all()['id'];
        $data=\DB::table('ddd')->where('id',$id)->first();
        return view('hhoo/ass',['data'=>$data]);
    }
}
