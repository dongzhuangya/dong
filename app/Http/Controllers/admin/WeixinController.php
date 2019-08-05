<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WeixinController extends Controller
{
    public function index()
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
    public function list()
    {
        $access_token=$this->index();
//        dd($access_token);
        $user=file_get_contents("https://api.weixin.qq.com/cgi-bin/user/get?access_token={$access_token}&next_openid=");
//        dd($user);
        $info=json_decode($user,1);
        foreach($info['data']['openid'] as $v){
            $openid_info=\DB::table('openid')->where(['openud'=>$v])->value('subscribe');
            if(empty($openid_info)){
                $access_token=$this->index();
                $openid=$v;
                $user=file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token={$access_token}&openid={$v}&lang=zh_CN");
                $info=json_decode($user,1);
//            var_dump($info);
                \DB::table('openid')->insert([
                    'openid'=>$v,
                    'add_time'=>time(),
                    'subscribe'=>$info['subscribe']
                ]);
            }else{
                $access_token=$this->index();
                $openid=$v;
                $user=file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token={$access_token}&openid={$v}&lang=zh_CN");
                $getInfo=json_decode($user,1);
                if($info!=$getInfo['subscribe']){
                    \DB::table('openid')->where(['openid'=>$v])->update([

                        'subscribe'=>$info['subscribe']
                    ]);
                }
            }

        }
//        var_dump($info);
    }
    public function list_index()
    {
        $redirect_uri='http://www.dong.com/weixin/code';
        $url="https://open.weixin.qq.com/connect/oauth2/authorize?appid='.env('APPID').'&redirect_uri='.urlencode($redirect_uri).'&response_type=code&scope=SCOPE&state=STATE#wechat_redirect";
        header('location'.$url);
    }
    public function code(Request $request){
        $req = $request->all();
        $code = $req['code'];
        //获取access_token
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".env("APPID")."&secret=".env("APPSECRET")."&code=".$code."&grant_type=authorization_code";
        $re = file_get_contents($url);
        $result = json_decode($re,1);
        $access_token = $result['access_token'];
        $openid = $result['openid'];
        //去user_openid 表查 是否有数据 openid = $openid
        //有数据 在网站有用户 user表有数据[ 登陆 ]
        //没有数据 注册信息  insert user  user_openid   生成新用户
    }
}