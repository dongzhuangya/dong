<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use DB;
class AaaController extends Controller
{
    public function index()
    {
        return view('aaa.index');
    }
    public function list(Request $request)
    {
        $name=$request->all()['name'];
        $pwd=$request->all()['pwd'];
        $res=DB::table('admin')->where('name',$name)->first();
        if(!$res){
            echo '没有这个账号';
        }
        if($pwd!=$res->pwd){
            echo '密码错误';
        }else{
            return redirect('aaa/listaaa');
        }
    }
    public function listaaa()
    {
        $data=DB::table('aaa')->paginate(2);

        return view('aaa.listaaa',['data'=>$data]);
    }
    public function add()
    {
        return  view('aaa.add');
    }
    public function addo(Request $request)
    {
        $data=$request->all();
//        dd($data);
        $pic=$request->file('pic');
//        dd($pic);
        $path = $pic->store('aaa');

        $res=DB::table('aaa')->insert([
            'title'=>$data['title'],
            'author'=>$data['author'],
            'details'=>$data['details'],
            'pic'=>'/storage'.'/'.$path,
            'time'=>time()

        ]);

        if($res){
            return redirect('/aaa/listaaa');
        }else{
            echo '添加失败';
        }
    }
    public function delect(Request $request)
    {
        $id=$request->all()['id'];
//        dd($id);
        $res=DB::table('aaa')->where('id',$id)->delete();
        if($res){
            return redirect('aaa/listaaa');
        }else{
            return 1;
        }
    }
    public function adc(Request $request)
    {



        $redis = new \Redis();
        $redis->connect('127.0.0.1','6379');
        $redis->incr('aa');
        $num = $redis->get('aa');
        echo '你的访问次数：'.$num;


        $id=$request->all()['id'];
        $data=DB::table('aaa')->where('id',$id)->first();
        return view('aaa.adc',['data'=>$data]);
    }
}
