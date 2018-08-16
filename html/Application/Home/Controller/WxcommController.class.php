<?php
/**
 * Created by PhpStorm.
 * User: Sunxue Jia
 * Date: 2017/11/21
 * Time: 19:59
 */

namespace Home\Controller;
use Think\Controller;


class WxcommController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $action_name = ACTION_NAME;
        if($action_name != 'getAccessToken'){
            $this ->auth();
        }

    }

    public function auth(){
        //用户授权后的回调地址
        $redirect_uri = urlencode('http://sunxuejia.xin/Home/Wxcomm/getAccessToken');
        //用户的请求地址
        $url = 'https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxb420d2cb53856d89&redirect_uri='.$redirect_uri.'&response_type=code&scope=snsapi_userinfo&state='.time().'#wechat_redirect';
        header('location:'.$url);

    }

    public function getAccessToken(){
        $code = I('get.code');
        if(!$code){
            echo 'fail';
            exit;
        }

        $url = 'https://api.weixin.qq.com/sns/oauth2/access_token?appid=wxb420d2cb53856d89&secret=733090c37cdaedbbea7814350de0fc03&code='.$code.'&grant_type=authorization_code';
//dump(123);exit;
        $json = http_curl($url,array(),'get','json');dump($json);exit;
        $url = 'https://api.weixin.qq.com/sns/userinfo?access_token='.$json['access_token'].'&openid='.$json['openid'].'&lang=zh_CN ';

        $data = http_curl($url);
        dump($json);
    }
}