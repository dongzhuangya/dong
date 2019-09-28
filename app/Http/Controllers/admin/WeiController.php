<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WeiController extends Controller
{
    public function caidan()
    {
        $url="https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$this->access_token();
        $data=[
            "button"=>[
                [
                    "type"=>"click",
                    "name"=>"点击",
                    "key"=>"V1001_TODAY_MUSIC"
                ],
                [
                    "name"=>"view",
                    "sub_button"=>[
                        [
                            "type"=>"view",
                            "name"=>"搜索",
                            "url"=>"http://www.baidu.com"
                        ],
                        [
                            "type"=>"click",
                            "name"=>"赞一下我们",
                            "key"=>"V1001_GOOD"
                        ]]
                ]],
            [

                    "type"=> "pic_weixin",
                    "name"=>"微信相册发图",
                    "key"=> "rselfmenu_1_2",
                   
                
            ]
        ];
        $dd=$this->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
        dd($dd);
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
