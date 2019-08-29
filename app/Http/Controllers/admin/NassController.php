<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class NassController extends Controller
{
    public function  access_token()
    {
        $redis=new \Redis();
        $redis->connect('127.0.0.1','6379');
        $access_token_key='$access_token';
        if($redis->exists($access_token_key)){
            $access_token=$redis->get($access_token_key);
        }else{
            $acc=file_get_contents("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".env('APPID')."&secret=".env('APPSECRET'));
            $aaa=json_decode($acc,1);
            $access_token=$aaa['access_token'];
            $expires_time=$aaa['expires_in'];
            $redis->set($access_token_key,$access_token,$expires_time);
        }

        return $access_token;
    }
    public function biaodan()
    {
        $url="https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$this->access_token();
        $data = [
            'button'=>[
                [
                    'name'=>'查看课程',
                    'type'=>'click',
                    'key'=>'item1'
                ],//第一个一级菜单

                [
                    'name'=>'管理课程',
                    'type'=>'view',
                    'url'=>'http://www.dongxzhuang.com/nass/add',
                ],//第三个一级菜单
            ]
        ];
        $res=json_encode($data,JSON_UNESCAPED_UNICODE);
        $aa=$this->post($url,$res);
        dd($aa);
    }
    public function add()
    {
        return view('nass.add');
    }
    public function post($url, $data = []){
        //初使化init方法
        $ch = curl_init();
        //指定URL
        curl_setopt($ch, CURLOPT_URL, $url);
        //设定请求后返回结果
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //声明使用POST方式来进行发送
        curl_setopt($ch, CURLOPT_POST, 1);
        //发送什么数据
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        //忽略证书
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        //忽略header头信息
        curl_setopt($ch, CURLOPT_HEADER, 0);
        //设置超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        //发送请求
        $output = curl_exec($ch);
        //关闭curl
        curl_close($ch);
        //返回数据
        return $output;
    }
}
