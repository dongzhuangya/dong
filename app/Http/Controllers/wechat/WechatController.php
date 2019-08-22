<?php

namespace App\Http\Controllers\wechat;

use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;
use App\Http\Tools\Wechat;
use Illuminate\Support\Facades\Storage;

class WechatController extends Controller
{
    public $request;
    public $wechat;

    public function __construct(Request $request,Wechat $wechat)
    {
        $this->request = $request;
        $this->wechat = $wechat;
    }

    public function get_user_list()
    {
       //获得access_token
       $access_token = $this->wechat->get_access_token();
        //获得所有用户
       $wechat_user  = file_get_contents("https://api.weixin.qq.com/cgi-bin/user/get?access_token={$access_token}&next_openid=");
       $user_info    = json_decode($wechat_user,1);
       $openid =  $user_info['data']['openid'];
            $i = 0;
            for ($i=0;$i<count($openid);$i++){
                //获取subscribe
                $access_token = $this->wechat->get_access_token();
                $wechat_info = file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token={$access_token}&openid={$openid[$i]}&lang=zh_CN");
                $user_info = json_decode($wechat_info,1);
                //获取openid
                $user_openid = DB::table('wechat_openid')->where('openid',$user_info['openid'])->first();
                if(empty($user_openid)){
                    $openid = $openid[$i];
                    DB::table('wechat_openid')->insert([
                        'openid' => $openid,
                        'add_time' => time(),
                        'subscribe' =>$user_info['subscribe']
                    ]);
                }
            }
        return redirect('wechat/user_list');
    }

    public function get_user_info()
    {
        $openid = "oNESkw1iY09qZujedFTAPP-nc8QI";
        $access_token = $this->wechat->get_access_token();
        $wechat_info = file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token={$access_token}&openid={$openid}&lang=zh_CN");
        $user_info = json_decode($wechat_info,1);
        dd($user_info);
    }



    public function user_list()
    {
        $tag_id = !empty($this->request->all()['id'])?$this->request->all()['id']:'';
       $user = DB::table('wechat_openid')->get()->toArray();
        return view('admin.wechat.user_list',['user'=>$user,'tag_id'=>$tag_id]);
    }
    //用户详情
    public function user_details(Request $request)
    {
        $openid = $request->all()['openid'];
        dd($openid);
       $user = DB::table('wechat_openid')->get()->toArray();

    }

    public function login()
    {
        $redirect_url = 'http://w3.shop.com/wechat/code';
        $url = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=".env('WECHAT_APPID')."&redirect_uri=".urlencode($redirect_url)."&response_type=code&scope=snsapi_base&state=STATE#wechat_redirect";
        header('Location:'.$url);
    }
    public function code(Request $request)
    {
        $data = $request->all();
        $code = $data['code'];
        $res = file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=".env('WECHAT_APPID')."&secret=".env('WECHAT_APPSECRET')."&code=$code&grant_type=authorization_code");
        $result = json_decode($res,1);
        $access_token = $result['access_token'];
        $openid = $result['openid'];
        //获取用户基本信息
        $wechat_user_info = $this->wechat->wechat_user_info($openid);

        //去user_wechat查
        $user_openid = DB::table('user_wechat')->where(['openid'=>$openid])->first();
        if (!empty($user_openid)) {
            $user_info = DB::table('user')->where(['id'=>$user_openid->uid])->first();
            $request->session()->put('username',$user_info->name);
            header('Location:www.shop.com/admin/index/index');
        }else{
            //没有数据 注册信息
            DB::connection("mysql4")->beginTransaction();
            $uid = DB::table('user')->insertGetId([
                'pwd' =>'',
                'name' => $wechat_user_info['nickname'],
                'add_time' => time()
            ]);
            $openid_res = DB::table('user_wechat')->insert([
                'uid' => $uid,
                'openid' =>$openid
            ]);
            DB::commit();
            //登陆操作
            $user_info = DB::table('user')->where(['id' => $uid])->first();
            $request->session()->put('username',$user_info->name);
            header('Location:www.shop.com/admin/index/index');
        }
    }
    
    //模板列表
    public function template_list()
    {
       $res = file_get_contents('https://api.weixin.qq.com/cgi-bin/template/get_all_private_template?access_token='.$this->wechat->get_access_token());
       $arr = json_decode($res,1);

       return view('admin.wechat.template',['data'=>$arr]);
    }
    
    //删除模板
    public function del_template()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/template/del_private_template?access_token='.$this->wechat->get_access_token();
        $id = $this->request->all()['id'];
        $data = [
            'template_id' =>$id
        ];
       $res = $this->wechat->post($url,json_encode($data));
      $res = json_decode($res,1);
       if ($res['errcode'] == 0){
           echo '删除成功';
       }else{
            echo '删除失败';
       }
    }

//发送模板消息
    public function send_template()
    {
       $template_id = $this->request->all()['id'];
        $openid = DB::table('wechat_openid')->select('openid')->limit(10)->get()->toArray();
        foreach ($openid as $val) {

            $res = $this->wechat->push_template($val->openid,$template_id);
        }
        dd($res);
    }
//素材管理
    public function upload()
    {
        return view('admin.wechat.upload');
    }
    public function do_upload()
    {
       $upload_type = $this->request->all()['up_type'];

        if ($this->request->hasFile('image')) {
            $res = $this->wechat->upload_source($upload_type,'image');
        } elseif ($this->request->hasFile('voice')) {
            $res = $this->wechat->upload_source($upload_type, 'vice');
        } elseif ($this->request->hasFile('video')) {
            $res = $this->wechat->upload_source($upload_type,'video','视频标题','视频描述');

        } elseif ($this->request->hasFile('thumb')) {
            //缩略图
            $res = $this->wechat->upload_source($upload_type, 'thumb');
        }
        echo $res;
        die();
    }

    public function get_source()
    {

        $media_id = $this->request->all()['id'];
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->wechat->get_access_token().'&media_id='.$media_id;
        //保存图片
        $client = new Client();
        $response = $client->get($url);
//        $h = $response->getHeaders();
//        echo '<pre>';print_r($h);echo '</pre>';die;
        $file_info = $response->getHeader('Content-disposition');
        $file_name = substr(rtrim($file_info[0],'"'),-20);
        //保存图片
        $path = 'wechat/image/'.$file_name;
        $res = Storage::disk('local')->put($path, $response->getBody());
        echo env('APP_URL').'/storage/'.$path;
        dd($res);
    }
//获得视频
    public function get_video_source()
    {
//      $media_id = 'Hidj0QYd420g66U_zr-NefRJMfYMUJy__S5zrq6RJ6Sb3GaEmoMXUMMveiJLF6yL';
        $media_id = $this->request->all()['id'];
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->wechat->get_access_token().'&media_id='.$media_id;
        $client = new client();
        $response = $client->get($url);
//        echo $response->getBody();
//        $res = file_get_contents($url);
//        file_put_cont|ents('./uploads/1.mp3',$res);
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
        $contents = stream_context_create($opts);
        //$url请求的地址：例如
        $read = file_get_contents($video_url,false,$contents);
        $res = file_put_contents('./storage/wechat/video/' . $file_name, $read);

        var_dump($res);
        die();
    }
    //音频
    public function get_voice_source()
    {
        $media_id = $this->request->all()['id'];
        $url = 'https://api.weixin.qq.com/cgi-bin/media/get?access_token='.$this->wechat->get_access_token().'&media_id='.$media_id;
        //保存图片
        $client = new Client();
        $response = $client->get($url);
        //获取文件名
//        $h = $response->getHeaders();
//        echo '<pre>';print_r($h);echo '</pre>';die;
        $file_info = $response->getHeader('Content-disposition');
        $file_name = substr(rtrim($file_info[0],'"'),-20);
//        $wx_image_path = 'wx/images'.$file_name;
        //保存音频
        $path = 'wechat/voice/'.$file_name;
        $res = Storage::put($path,$response->getBody());
        echo env('APP_URL').'/storage/'.$path;
        dd($res);
    }
    //素材列表
    public function source_list()
    {
        $data = DB::table('source')->get()->toArray();
        return view('admin.wechat.source_list',['data'=>$data]);
    }
//获取永久素材
    public function upload_source()
    {
        $type = $this->request->all()['type'];
        $url = 'https://api.weixin.qq.com/cgi-bin/material/batchget_material?access_token='.$this->wechat->get_access_token();
        $data = ['type'=>$type,'offset'=>0,'count'=>20];
        $res = $this->wechat->post($url,json_encode($data));
        $data = json_decode($res, 1);

        $data = json_decode($res,1);
        return view('admin.wechat.upload_source',['data'=>$data]);
    }
    //删除永久素材
    public function del_source()
    {
        $media_id = $this->request->all();
        $url = 'https://api.weixin.qq.com/cgi-bin/material/del_material?access_token='.$this->wechat->get_access_token();
        $this->wechat->post($url,json_encode($media_id));
    }

    public function create_tag()
    {
        return view('admin.wechat.create_tag');
    }

    public function save_tag()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/tags/create?access_token='.$this->wechat->get_access_token();
        $data = [
            'tag' => ['name'=>$this->request->all()['name']]
        ];
        $res = $this->wechat->post($url,json_encode($data,JSON_UNESCAPED_UNICODE));
        dd(json_decode($res,1));
    }
    //标签列表
    public function tag_list()
    {
       $data = $this->wechat->tag_list();
        $data = $data['tags'];
       return view('admin.wechat.tag_list',['data'=>$data]);
    }

    //删除标签
    public function del_tag()
    {
        $url = 'https://api.weixin.qq.com/cgi-bin/tags/delete?access_token='.$this->wechat->get_access_token();
        $data = [
          'tag' => ['id' =>$this->request->all()['id']]
        ];
       $res =  $this->wechat->post($url, json_encode($data));
       $res = json_decode($res, 1);
        if ($res['errcode'] == 45058) {
            return '不能删除';
        }elseif ($res['errcode'] == 45057){
            return '该标签下粉丝数超过10w，不允许直接删除';
        } elseif ($res['errcode'] == 0) {
            return redirect('wechat/tag_list');
        }else{
            return '删除失败';
        }
    }
//为用户添加标签
    public function add_user_tag()
    {
        if (empty($this->request->all()['tag_id'])){
            return redirect('wechat/tag_list');
        }
        //接受选择的id
       $id = $this->request->all()['id_list'];
        $openid = DB::table('wechat_openid')->whereIn('id', $id)->select('openid')->get()->toArray();
        $openid_list = [];
        foreach ($openid as $v) {
            $openid_list = $v->openid;
        }
        $url = 'https://api.weixin.qq.com/cgi-bin/tags/members/batchtagging?access_token='.$this->wechat->get_access_token();
        $data = [
            'openid_list' =>$openid_list,
            'tagid' => $this->request->all()['tag_id']
        ];
        $res = $this->wechat->post($url, json_encode($data));
        $res = json_decode($res, 1);
        if ($res['errcode'] == 0) {
            return redirect('wechat/tag_list');
        }
    }
    //标签下的粉丝列表
    public function tag_fans()
    {
        $tag_id = $this->request->all()['id'];
        $res = $this->wechat->tag_fans($tag_id);
        if ($res['count'] == 0) {
            return '该标签下暂无粉丝';
        }
        $openid = $res['data']['openid'];
        return view('admin.wechat.tag_fans',['openid'=>$openid]);
    }
//获取粉丝下的标签
    public function get_user_tag()
    {
      $openid = $this->request->all()['openid'];
      //得到用户下的标签
      $user_tag_info = $this->wechat->get_user_tag($openid);
      //获取所有标签信息
       $tag_info = $this->wechat->tag_list();
       $tags = $tag_info['tags'];
        foreach ($tags as $val) {
            foreach ($user_tag_info['tagid_list'] as $v) {
                if ($v == $val['id']) {
                    echo $val['name']."<a href=".env('APP_RUL').'/wechat/del_user_tag'.'?tag_id='.$v.'&openid='.$openid.">删除</a><p>";
                }
            }
        }
    }
    //取消用户下的标签
    public function del_user_tag()
    {
       $url = 'https://api.weixin.qq.com/cgi-bin/tags/members/batchuntagging?access_token='.$this->wechat->get_access_token();
        if (is_array($this->request->all()['openid'])) {
            $openid_list = [$this->request->all()['openid']];
        }else{
            $openid_list = $this->request->all()['openid'];
        }
        $data = [
            'openid_list' =>$openid_list,
            'tagid'       =>$this->request->all()['tag_id']
        ];
        $res = $this->wechat->post($url, json_encode($data));
        $res = json_decode($res, 1);
        if ($res['errcode'] ==0){
            return redirect('wechat/user_list');
        }else{
            return '删除失败';
        }
    }

    public function edit_tag()
    {
      $data = $this->request->all();
      return view('admin.wechat.edit_tag',['data'=>$data]);
    }
    public function update_tag()
    {
        $tag_id = $this->request->all()['id'];
        $url = 'https://api.weixin.qq.com/cgi-bin/tags/update?access_token='.$this->wechat->get_access_token();
        $data = [
            'tag' => ['id'=>$tag_id,'name'=>$this->request->all()['name']]
        ];
        $res = $this->wechat->post($url, json_encode($data,JSON_UNESCAPED_UNICODE));
        if (json_decode($res, 1)['errcode'] == 0) {
            return redirect('wechat/tag_list');
        }else{
            echo '修改失败';
        }
    }
    //根据标签推送消息
    public function push_tag_message()
    {
        if (empty($this->request->all()['id'])) {
            return redirect('wechat/tag_list');
        }
        //接受标签id
        $tag_id = $this->request->all()['id'];
        //查询标签下的粉丝
        $res = $this->wechat->tag_fans($tag_id);
        if ($res['count'] == 0) {
            return '该标签下暂无粉丝';
        }
        $openid = $res['data']['openid'];
        return view('admin.wechat.push_tag_message',['tag_id'=>$tag_id,'openid'=>json_encode($openid)]);
    }
    //执行标签群发消息
    public function do_push_tag_message()
    {
        //接受
        $push_type= $this->request->all()['push_type'];
        $tag_id= $this->request->all()['tag_id'];
        $media_id= $this->request->all()['media_id'];
        $message= $this->request->all()['message'];
        $res = $this->wechat->do_push_tag_message($push_type,$tag_id,$message,$media_id);
        dd($res);
    }
}
