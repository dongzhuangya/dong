<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class SongController extends Controller
{
    public function add(){
        return view('song.add');
    }
    public function do_add(Request $request)
    {
        $name=$request->all();

        $data=[
            'tag'=>[
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
    public function ddd()
    {
        $redirect_uri="http://www.dong.com/song/code";
        $hao="https://open.weixin.qq.com/connect/oauth2/authorize?appid=".env('APPID')."&redirect_uri=".urlencode($redirect_uri)."&response_type=code&scope=snsapi_base&state=123#wechat_redirect";
        header('Location:'.$hao);
    }
    public function code(Request $request)
    {

        $ccc=$request->all();
        $url="https://api.weixin.qq.com/sns/oauth2/access_token?appid=".env('APPID')."&secret=".env('APPSECRET')."&code=".$ccc['code']."&grant_type=authorization_code";
        $data=json_decode(file_get_contents($url),1);
//        dd($data);
//        $urll="https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->access_token()."&openid=".$data['openid']."&lang=zh_CN";
//        $openids=json_decode(file_get_contents($urll),1);
////        dd($openid);
//        $open=$openids['openid'];
        return redirect('song/sess');

    }
    public function sess()
    {
        $url=file_get_contents('https://api.weixin.qq.com/cgi-bin/user/get?access_token='.$this->access_token().'&next_openid=');
        $data=json_decode($url,1);

        foreach ($data['data']['openid'] as $v){
            $data=\DB::table('asdd')->where('openid','=','$v')->get();
//
            if(empty($data)){
                $url=file_get_contents('https://api.weixin.qq.com/cgi-bin/user/info?access_token='.$this->access_token().'&openid='.$v.'&lang=zh_CN');
                $data=json_decode($url,1);

                \DB::table('asdd')->insert([
                    'name'=>$data['nickname'],
                    'city'=>$data['city'],
                    'img'=>$data['headimgurl'],
                    'openid'=>$data['openid']
                ]);

            }
        }

        $dat=\DB::table('asdd')->get();

        return view('song.sess',['info'=>$dat]);
    }
    public function add_list()
    {
        $dataa=\DB::table('user_wechat')->get();
        return view('song.addlist',['data'=>$dataa]);
    }
    public function mm(Request $request)
    {

        $url="https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$this->access_token();
        $data=[
            'expire_seconds'=>30 * 24 * 3600,
            'action_name'=>'QR_SCENE',
            'action_info'=>[
                'scene'=>['scene_id'=>$request->all()['uid']]
            ]
        ];

        $re=$this->curl_post($url,json_encode($data));
        $rr=json_decode($re,1);
        $qrcode_info = file_get_contents('https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.urlencode($rr['ticket']));
        $path='/wechat/qrcode/'.time().rand(1000,9999).'.jpg';
//        dd($path)
        Storage::put($path, $qrcode_info);
        \DB::table('user_wechat')->where(['uid'=>$request->all()['uid']])->update([

            'img'=> '/storage'.$path

        ]);

        return redirect('/song/add_list');

    }

}