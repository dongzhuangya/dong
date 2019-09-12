<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
class WechaController extends Controller
{
    //获取access_token
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
    //
    public function event(){
        echo $_GET['echostr'];
    }
    public function user_list()
    {
        $url=file_get_contents('https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$this->access_token().'&next_openid=');
        $data=json_decode($url,1);
        foreach ($data['data']['openid'] as $v){
            $data=\DB::table('asdd')->where('openid','=','$v')->get();

            if(empty($data)){
                $url=file_get_contents('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->access_token().'&openid='.$v.'&lang=zh_CN');
                $data=json_decode($url,1);
                \DB::table('asdd')->insert([
                    'name'=>$data['nickname'],
                    'city'=>$data['city'],
                    'img'=>$data['headimgurl']
                ]);
            }
        }
        $data=\DB::table('asdd')->get();
        return view('wecha.user_list',['info'=>$data]);

    }
    public function ddd()
    {
        $redirect_uri="http://www.dong.com/wecha/code";
        $hao="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".env('APPID')."&redirect_uri=".urlencode($redirect_uri)."&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
        header('Location:'.$hao);
    }
    public function code(Request $request)
    {

        $ccc=$request->all();
        $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".env('APPID')."&secret=".env('APPSECRET')."&code=".$ccc['code']."&grant_type=authorization_code";
        $data=json_decode(file_get_contents($url),1);
        $urll="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->access_token()."&openid=".$data['openid']."&lang=zh_CN";
        $openids=json_decode(file_get_contents($urll),1);
//        dd($openid);
        $open=$openids['openid'];
        $data=\DB::table('user_wechat')->where(['openid'=>$open])->first();
//        dd($data);
        if(!empty($data)){
            //存在
            $request->session()->put('uid',$openids);
            echo 111;
        }else{
            //不存在
            DB::beginTransaction();
            $openid=\DB::table('user')->insertGetId([
                'name'=>$openids['nickname'],
                'pwd'=>'',
                'add_time'=>time()
            ]);
//            dd($openid);
            $data=\DB::table('user_wechat')->insert([
                'uid'=>$openid,
                'name'=>$openids['nickname'],
                'openid'=>$openids['openid'],
                'add_time'=>time()
            ]);
            DB::rollBack();
            $request->session()->put('uid',$openid);
            echo 'ok';
//

        }


    }
    public function noss(){
        return view('wecha.noss');
    }
    public function do_noss(Request $request)
    {
        $name = 'file_name';
        if(!empty($request->hasFile($name)) && request()->file($name)->isValid()){
            //$size = $request->file($name)->getClientSize() / 1024 / 1024;
            $ext = $request->file($name)->getClientOriginalExtension();  //文件类型

            $file_name = time().rand(1000,9999).'.'.$ext;

            $path = request()->file($name)->storeAs('wechat\voice',$file_name);
//dd($path);
            $path = realpath('./storage/'.$path);
            $url = 'https://api.weixin.qq.com/cgi-bin/material/add_news?access_token='.$this->access_token().'&type=image';
            $result = $this->curl_upload($url,$path);
            dd($result);
        }
    }
    public function curl_upload($url,$path)
    {
        $curl = curl_init($url);
        curl_setopt($curl,CURLOPT_RETURNTRANSFER,true);
        curl_setopt($curl,CURLOPT_POST,true);  //发送post
        $form_data = [
            'meida' => new \CURLFile($path)
        ];
        curl_setopt($curl,CURLOPT_POSTFIELDS,$form_data);
        $data = curl_exec($curl);
        //$errno = curl_errno($curl);  //错误码
        //$err_msg = curl_error($curl); //错误信息
        curl_close($curl);
        return $data;
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
}
