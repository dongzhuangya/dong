<?php

namespace App\Http\Controllers\admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use GuzzleHttp\Client;
class WeixinController extends Controller
{
    //token
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
    //添加粉丝
    public function list()
    {
        $access_token=$this->index();
//        dd($access_token);
        $user=file_get_contents("https://api.weixin.qq.com/cgi-bin/user/get?access_token={$access_token}&next_openid=");
//        dd($user);
        $info=json_decode($user,1);
        foreach($info['data']['openid'] as $v){
            $openid_info=\DB::table('openid')->where(['openid'=>$v])->value('subscribe');
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

                        'subscribe'=>$getInfo['subscribe']
                    ]);
                }
            }
        }
//        var_dump($info);
        echo "<script>history.go(-1);</script>";
    }
    //获取code
    public function login()
    {
        $redirect_uri='http://www.dong.com/weixin/code';
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".env('APPID')."&secret=".env("APPSECRET")."&redirect_uri=".urlencode($redirect_uri)."&response_type=code&scope=snsapi_base#wechat_redirect";
//        echo $url;die;
        header('Location:'.$url);
    }
    //处理code
    public function code(Request $request){
        $req = $request->all();
        $code = $req['code'];
        //获取access_token
        $url = "https://api.weixin.qq.com/sns/oauth2/access_token?appid=".env("APPID")."&secret=".env("APPSECRET")."&code=".$code."&grant_type=authorization_code";
        $re = file_get_contents($url);
        $result = json_decode($re,1);
        $access_token = $result['access_token'];
        $openid = $result['openid'];
        dd($openid);
    }
    //模板列表
    public function adc(){
        $url='https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token='.$this->index();
        $qq=file_get_contents($url);
        $aa=json_decode($qq,1);
        dd($aa);
    }
    //发送模板消息
    public function add()
    {
        $data=\DB::table('openid')->select('openid')->get()->toArray();
        foreach($data as $v){
            $url='https://api.weixin.qq.com/cgi-bin/message/template/send?access_token='.$this->index();
            $data=[
                'touser'=>$v->openid,
                'template_id'=>'UuNurQYPUj8r0Wo1sPO4ZeeqLE6xEFL_BUCjAYWWOhA',
                'url'=>'http://www.baidu.com',
                'data'=>[
                    'keyword'=>[
                        'value'=>'商品名称'
                    ]
                ]
            ];
            $re=$this->post($url,json_encode($data));
        }
        dd($re);
    }
    //不知道
    public function event()
    {

    }
    public function establish()
    {
        $url="https://api.weixin.qq.com/cgi-bin/menu/create?access_token=".$this->index();
        $data = [
            'button'=>[
                [
                    'name'=>'菜单一',
                    'type'=>'click',
                    'key'=>'item1'
                ],//第一个一级菜单
                [
                    'name'=>'菜单二',
                    'sub_button'=>[
                        [
                            'name'=>'音乐',
                            'type'=>'click',
                            'key'=>'music'
                        ], // 第二个二级菜单
                        [
                            'name'=>'电影',
                            'type'=>'view',
                            'url'=>'http://www.iqiyi.com/',
                        ], // 第二个二级菜单

                    ],
                ],//第二个一级菜单
                [
                    'name'=>'菜单三',
                    'type'=>'view',
                    'url'=>'http://www.qq.com',
                ],//第三个一级菜单
            ]
        ];
        $res=json_encode($data,JSON_UNESCAPED_UNICODE);
        $aa=$this->post($url,$res);
        dd($aa);
    }
    //上传
    public function en()
    {
        return view('weixin.en');
    }
    //上传素材图片音频
    public function en_do(Request $request)
    {
        $client=new Client();
        if($request->hasFile('image')){
            $path=$request->file('image')->store('wechat/image');
            $path='./storage/'.$path;
            $url='https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.$this->index().'&type=TYPE';
            $response=$client->request('post',$url,[
                'multipart'=>[
                 [  'name'=>'username',
                    'contents'=>'xiaodong'
                ],
                [
                    'name'=>'media',
                    'contents'=>fopen(realpath($path),'r')
                ]
                ],
            ]);
            $body=$response->getBody();
            unlink($path);
            echo $body;
        }elseif($request->hasFile('voice')){
            $img_file=$request->file('voice');
            $file_ext=$img_file->getClientOriginalExtension();
            $new_file_name=time().rend(1000,9999).'.'.$file_ext;
            $seva_path=$img_file->storeAs('wechat/voice',$new_file_name);
            $path='./storage/'.$seva_path;
            $url='https://api.weixin.qq.com/cgi-bin/media/upload?access_token='.$this->index().'&type=TYPE';
            $response=$client->request('post',$url,[
                'multipart'=>[

                    [
                        'name'=>'media',
                        'contents'=>fopen(realpath($path),'r')
                    ]
                ],
            ]);
            $body=$response->getBody();
            unlink(realpath($path));
            echo $body;
        }

    }
    public function get_voice_source()
    {
        $media_id = 'UKml31rzRRlr8lYfWgAno9mGe-meph0BKmVtZugAHQTqZIxOhUoBvCnqfJMRMKTG';
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->wechat->get_access_token().'&media_id='.$media_id;
        //echo $url;echo '</br>';
        //保存图片
        $client = new Client();
        $response = $client->get($url);
        //$h = $response->getHeaders();
        //echo '<pre>';print_r($h);echo '</pre>';die;
        //获取文件名
        $file_info = $response->getHeader('Content-disposition');
        $file_name = substr(rtrim($file_info[0],'"'),-20);
        //$wx_image_path = 'wx/images/'.$file_name;
        //保存图片
        $path = 'wechat/voice/'.$file_name;
        $re = Storage::put($path, $response->getBody());
        echo env('APP_URL').'/storage/'.$path;
        dd($re);
    }
    public function get_video_source(){
        $media_id = 'f9-GxYnNAinpu3qY4oFadJaodRVvB6JybJOhdjdbh7Z0CR0bm8nO4uh8bqSaiS_d'; //视频
        $url = 'http://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->wechat->get_access_token().'&media_id='.$media_id;
        $client = new Client();
        $response = $client->get($url);
        $video_url = json_decode($response->getBody(),1)['video_url'];
        $file_name = explode('/',parse_url($video_url)['path'])[2];
        //设置超时参数
        $opts=array(
            "http"=>array(
                "method"=>"GET",
                "timeout"=>3  //单位秒
            ),
        );
        //创建数据流上下文
        $context = stream_context_create($opts);
        //$url请求的地址，例如：
        $read = file_get_contents($video_url,false, $context);
        $re = file_put_contents('./storage/wechat/video/'.$file_name,$read);
        var_dump($re);
        die();
    }
    public function get_source()
    {
        $media_id = 'pREe_hxV86zjyFsmSlMNnewpYTFf5x6NuckIDkOTLgcF58FhejU-DNDucyme6x_n'; //图片
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->wechat->get_access_token().'&media_id='.$media_id;
        //echo $url;echo '</br>';
        //保存图片
        $client = new Client();
        $response = $client->get($url);
        //$h = $response->getHeaders();
        //echo '<pre>';print_r($h);echo '</pre>';die;
        //获取文件名
        $file_info = $response->getHeader('Content-disposition');
        $file_name = substr(rtrim($file_info[0],'"'),-20);
        //$wx_image_path = 'wx/images/'.$file_name;
        //保存图片
        $path = 'wechat/image/'.$file_name;
        $re = Storage::disk('local')->put($path, $response->getBody());
        echo env('APP_URL').'/storage/'.$path;
        dd($re);
        //return $file_name;
    }
    //粉丝列表
    public function fen()
    {
        $url='https://api.weixin.qq.com/cgi-bin/user/tag/get?access_token='.$this->index();
        $qq=file_get_contents($url);
        $q=json_decode($qq,1);
        dd($q);

//        return view('weixin.fen');
    }
    /**
     * 上传资源
     * @param Request $request
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function do_upload(Request $request)
    {
        $upload_type = $request['up_type'];
        $re = '';
        if($request->hasFile('image')){
            //图片类型
            $re = $this->wechat->upload_source($upload_type,'image');
        }elseif($request->hasFile('voice')){
            //音频类型
            //保存文件
            $re = $this->wechat->upload_source($upload_type,'voice');
        }elseif($request->hasFile('video')){
            //视频
            //保存文件
            $re = $this->wechat->upload_source($upload_type,'video','视频标题','视频描述');
        }elseif($request->hasFile('thumb')){
            //缩略图
            $path = $request->file('thumb')->store('wechat/thumb');
        }
        echo $re;
        dd();
    }
    //删除模板
    public function del_dd(){
        $url='https://api.weixin.qq.com/cgi-bin/template/del_private_template?access_token='.$this->index();
        $data=[
            'template_id'=>'XiTRxVkoZtCr8ByLS81V7sGi0OgjOsCrzZX4KbRqVrM'
        ];
        $qq=$this->post($url,json_encode($data));
        dd($qq);
    }
    //post curl处理
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
    //
    public function asd()
    {

//        $url="https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token=".$this->index();
        @header('Content-type: text/plain;charset=UTF-8');
        $data = array(                //需要POST传输的数据
            'action_name'        => 'QR_LIMIT_SCENE',
            'action_info'        => array(
                'scene'    => array(
                    'scene_id'    => 1,
                ),
            ),
        );

        $url = "https://api.weixin.qq.com/cgi-bin/qrcode/create?"
            ."access_token=".$this->index();
        $ticket  = $this->httpdata($url, $data);
        $ticket=json_decode($ticket,1);

        $url1="https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket=".$ticket['ticket'];

        $add=file_get_contents($url1);
        echo $add;
//        $dir = '/storage/1_wx.jpg';
//        if(!empty($img)){
//            @file_put_contents($dir,$img);
//        }
    }
    private function httpdata($url,$data)
    {
        $data = json_encode($data);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (compatible; MSIE 5.01; Windows NT 5.0)');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt($ch, CURLOPT_AUTOREFERER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $status = curl_exec($ch);
        curl_close($ch);
        $res = json_decode($status, true);
        return $status;
    }
}
