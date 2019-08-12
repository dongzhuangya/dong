<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2019/8/6
 * Time: 9:18
 */

namespace App\Http\Tools;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;

class Wechat
{
    public  $request;
    public  $client;
    public function __construct(Request $request,Client $client)
    {
        $this->request = $request;
        $this->client = $client;
    }
    //微信关注的用户信息
    public function wechat_user_info($openid)
    {
        $access_token = $this->get_access_token();
        $wechat_info = file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token={$access_token}&openid={$openid}&lang=zh_CN");
        $user_info = json_decode($wechat_info,1);
        return $user_info;
    }

    //获取access_token
    public function get_access_token()
    {
        $redis = new \Redis;
        $redis->connect('127.0.0.1','6379');
        $access_token_key = 'wechat_access_token';
        if ($redis->exists($access_token_key)){
            $access_token = $redis->get($access_token_key);

        }else{
            $acc = file_get_contents("https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid=".env('WECHAT_APPID')."&secret=".env('WECHAT_APPSECRET'));
            $access_result = json_decode($acc,1);
            $access_token = $access_result['access_token'];
            $expire_time = $access_result['expires_in'];
            //加入缓存
            $redis->set($access_token_key,$access_token,$expire_time);
        }
        return $access_token;
    }

    //post请求
    public function post($url, $data=[]){
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

    /**
     * 根据openid发送模板消息
     * @param $openid
     * @return bool|string
     */
    public function push_template($openid,$template_id)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$this->get_access_token();
        $data = [
            'touser'=>$openid,
            'template_id'=>$template_id,
            'url'=>'http://www.baidu.com',
            'data' => [
                'first' => [
                    'value' => '商品名称',
                    'color' => ''
                ],
                'keyword1' => [
                    'value' => '低价',
                    'color' => ''
                ],
                'keyword2' => [
                    'value' => '是低价',
                    'color' => ''
                ],
                'remark' => [
                    'value' => '备注',
                    'color' => ''
                ]
            ]
        ];
        $re = $this->post($url,json_encode($data));
        return $re;
    }

//上传微信素材
    public function upload_source($up_type,$type,$title='',$desc='')
    {
        $file = $this->request->file($type);
        $file_ext = $file->getClientOriginalExtension();
        //重命名
        $new_file_name = time().rand(1000,9999).'.'.$file_ext;
        //文件保存路径
        //保存文件
        $save_file_path = $file->storeAs('wechat/'.$type,$new_file_name);  //返回保存成功之后的路径
        $path = './storage/'.$save_file_path;
        if ($up_type == 1) {
            $url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.$this->get_access_token().'&type='.$type;
        }elseif ($up_type == 2) {
            $url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token='.$this->get_access_token().'&type='.$type;
        }
        $multipart = [
            [
                'name' => 'media',
                'contents' =>fopen(realpath($path),'r')
            ]
        ];
        if ($type == 'video' && $up_type ==2){
            $multipart[] = [
              'name' => 'description',
              'contents' => json_encode(['title'=>$title,'introduction'=>$desc])
            ];
        }
        $response = $this->client->request('POST',$url,[
            'multipart' =>$multipart
        ]);
        //获取返回
        $body = $response->getBody();
        unlink($path);
        //入库
        $body = json_decode($body,1);
        if ($up_type ==1){
            $res = DB::connection('mysql4')->table('source')->insert([
                'up_type' =>$up_type,
                'media_id' =>$body['media_id'],
                'add_time' => $body['created_at'],
                'type' => $type
            ]);
            if ($res){
                return '上传成功';
            }else{
                return '上传失败';
            }
        }
    }

    //获取标签列表
    public function tag_list()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/tags/get?access_token='.$this->get_access_token();
        $res = file_get_contents($url);
        $tag_info = json_decode($res,1);
        return $tag_info;
    }
    //根据id获取标签粉丝列表
    public function tag_fans($tag_id,$next_openid='')
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/user/tag/get?access_token='.$this->get_access_token();
        $data = [
            'tagid' =>$tag_id,
            'next_openid' =>$next_openid
        ];
       $res = $this->post($url, json_encode($data));
        return json_decode($res, 1);

    }
    //获取用户下的标签
    public function get_user_tag($openid)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/tags/getidlist?access_token='.$this->get_access_token();
        $data = [
            'openid' => $openid
        ];
        $res = $this->post($url, json_encode($data));
        return json_decode($res, 1);
    }
    //执行标签群发消息
    public function do_push_tag_message($push_type,$tag_id,$message,$media_id)
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token='.$this->get_access_token();
        if ($push_type == 1) {
//            文本消息
            $data = [
                'filter' =>  [
                    'is_to_all'=>false,
                    'tag_id' =>$tag_id
                ],
                'text'=>[
                    'content'=> $message
                ],
                'msgtype'    => 'text'
            ];
        }else{
            //图片  素材消息
            $data = [
                'filter' =>  [
                    'is_to_all'=>false,
                    'tag_id' =>$tag_id
                ],
                'mpnews'  =>[
                    'media_id'=>$media_id
                ],
                'msgtype' =>'image'
            ];
        }
        $res = $this->post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
        return json_decode($res, true);
    }
}