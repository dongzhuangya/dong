<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

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
                    "name"=>"view事件",
                    "sub_button"=>[
                        [
                            "type"=>"view",
                            "name"=>"搜索",
                            "url"=>"http://www.baidu.com"
                        ],
                        [

                            "type"=> "pic_weixin",
                            "name"=>"图片",
                            "key"=> "rselfmenu_1_2",


                        ]]
                ]],

        ];
        $dd=$this->curl_post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
//        dd($dd);
    }
    public function event()
    {

        $xml_string = file_get_contents('php://input');  //获取
//        dd($xml_string);

//        $wechat_log_psth = '/data/wwwroot/default/dong/storage/logs/wechat/'.date('Y-m-d').'.log';

//        print_r($wechat_log_psth);die;


//        file_put_contents($wechat_log_psth,"<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<\n",FILE_APPEND);
//
//        file_put_contents($wechat_log_psth,$xml_string,FILE_APPEND);
//
//        file_put_contents($wechat_log_psth,"\n<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<<\n\n",FILE_APPEND);

        //dd($xml_string);
        $xml_obj = simplexml_load_string($xml_string,'SimpleXMLElement',LIBXML_NOCDATA);//解析文字代码;



        $xml_arr = json_decode(json_encode($xml_obj),true);
//        \Log::Info(json_encode($xml_arr,JSON_UNESCAPED_UNICODE));
//        dd($xml_arr);die;
        //被动回复消息
        if($xml_arr['MsgType']=='event'&& $xml_arr['Event']=='CLICK'){
            $url=file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->access_token()."&openid=".$xml_arr['FromUserName']."&lang=zh_CN");
            $ss=json_decode($url,1);
//            dd($ss);

            $message="hello".$ss['nickname'];
            $xml_str = "<xml>
                            <ToUserName><![CDATA[".$xml_arr['FromUserName']."]]></ToUserName>
                            <FromUserName><![CDATA[".$xml_arr['ToUserName']."]]></FromUserName>
                            <CreateTime>".time()."</CreateTime>
                            <MsgType><![CDATA[text]]></MsgType>
                            <Content><![CDATA\".$message.\"]]></Content>
                        </xml>";


//            echo $resultStr;

            echo $xml_str;
        }


        if ($xml_arr['MsgType'] == 'text' ){
            $data = [
               'openid' => $xml_arr['FromUserName'],
                'add_time' => time(),
                'content' => $xml_arr['Content']
            ];
            $res = \DB::table('wechat_openid')->insert($data);

            //回复
            $xml ="<xml>
              <ToUserName><![CDATA[".$xml_arr['FromUserName']."]]></ToUserName>
              <FromUserName><![CDATA[".$xml_arr['ToUserName']."]]></FromUserName>
              <CreateTime>".time()."</CreateTime>
              <MsgType><![CDATA[text]]></MsgType>
              <Content><![CDATA[".time().$xml_arr['Content']."]]></Content>
            </xml>
            ";

            echo $xml;
        }

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

