<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class ASscController extends Controller
{
    public function access_token()
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
    public function index()
    {
        $url="http://www.dong.com/assc/code";
        $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".env('APPID')."&redirect_uri=".urlencode($url)."&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect";
        header('Location:'.$url);
    }
    public function code(Request $request){
        $data=$request->all();
//        var_dump($data);
        $code=$data['code'];
        $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".env('APPID')."&secret=".env('APPSECRET')."&code=".$code."&grant_type=authorization_code";
        $ll=file_get_contents($url);
        $dd=json_decode($ll,1);

        $access_token=$dd['access_token'];
//        dd($access_token);die;
        $openid=$dd['openid'];
//        dd($openid);die;
        $url="https://api.weixin.qq.com/sns/userinfo?access_token=".$access_token."&openid=".$openid."&lang=zh_CN";
        $as=file_get_contents($url);
        dd($as);
    }

}
