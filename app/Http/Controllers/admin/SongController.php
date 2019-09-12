<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class SongController extends Controller
{
    public function add(){
        return view('song.add');
    }
    public function do_add(Request $request)
    {
        $name=$request->all();

        $data=['tag'=>[
            'name'=>$name['name']
        ]];
        $url='https://api.weixin.qq.com/cgi-bin/tags/create?access_token='.$this->access_token();
        $cc=$this->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
        $dd=json_decode($cc,1);
        dd($dd);
    }
    public function index()
    {
        $url="https://api.weixin.qq.com/cgi-bin/tags/create?access_token=".$this->access_token();
        $re=file_get_contents($url);
        $asc=json_decode($re,1);
        dd($asc);
    }
    public function curl_post($url,$data)
    {
        $curl = curl_init($url);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl,CURLOPT_POST,true);  //发送post
        curl_setopt($curl,CURLOPT_POSTFIELDS,$data);
        $data = curl_exec($curl);
        $errno = curl_errno($curl);  //错误码
        $err_msg = curl_error($curl); //错误信息
        curl_close($curl);
        return $data;
    }
    public function access_token()
    {
        $redis=new \Redis();
        $redis->connect('127.0.0.1','6379');
        $access_token_k='$access_token';
        if($redis->exists($access_token_k)){
            return $redis->get($access_token_k);
        }else{
            $url=file_get_contents('https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('APPID').'&secret='.env('APPSECRET'));
            $access_token=json_decode($url,1);
            $redis->set('$access_token',$access_token['access_token'],$access_token['expires_in']);
            return $access_token['access_token'];
        }
    }
}
