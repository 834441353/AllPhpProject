<?php
//获取微信的token
/**
 * @return mixed  返回一个获取的access_token
 */
function get_access_token()
{
    $appid = 'wxb420d2cb53856d89';
    $secret = '733090c37cdaedbbea7814350de0fc03';
    $url = 'https://api.weixin.qq.com/cgi-bin/token';
    $data = array(
        'grant_type' => 'client_credential',
        'appid' => $appid,
        'secret' => $secret
    );

    //首先读取memcache中的内容，
    //注意当前的环境，在window环境中memcache拓展实例化时memcache类
    //              在linux环境中memcached拓展实例化是memcached类
    $obj = new \Memcached();
    //建立链接
    $obj->addServer('127.0.0.1',11211);
    //获取数据
    $accessToken = $obj ->get($appid);
    if (!$accessToken){
        //当前的memcache中没有相应的数据
        $res = http_curl($url,$data);
        $accessToken = $res['access_token'];
        //将得到的access_token 数据存入memcache中
        $obj -> set($appid,$accessToken,7000);
    }

    return $accessToken;

}

//实现curl的get或者post请求
/**
 * @param $url                  代表请求的url地址
 * @param array $data 代表请求的携带的参数
 * @param string $method 代表请求的方式
 * @param string $returnType 代表请求返回的数据格式
 */
//function http_curl($url, $data = array(), $method = 'get', $returnType = 'json')
//{
//    //开启会话
//    $ch = curl_init();
//    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//
//    //设置https方式的请求
//    //禁用https证书
//    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
//
//    if ($method == 'get') {
//        $url .= '?' . http_build_query($data);
//    } else {
//        //表示设置为post请求，
////        echo 123;
//        curl_setopt($ch, CURLOPT_POST, 1);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
//    }
//    curl_setopt($ch, CURLOPT_URL, $url);
//
//    $json = curl_exec($ch);
//
//    curl_close($ch);
////    dump($json);exit;
//    if ($returnType == 'json') {
//        return json_decode($json, true);
//    }
//    return $json;
//}
function http_curl($url,$data=array(),$method='get',$returnType='json'){
    // 1、打开会话
    $ch = curl_init();
    // 2、设置参数 请求方式、请求参数、是否直接输出结果、设置请求的URL地址
    //

    // 返回结果不直接输出
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    // 设置为0表示禁止证书的检查
    curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,0);
    if($method=='get'){
        $url .= '?'.http_build_query($data);

    }else{
        //表示设置为post请求  设置请在前面后设置参数
        curl_setopt ( $ch, CURLOPT_SAFE_UPLOAD, false);
        curl_setopt($ch,CURLOPT_POST ,TRUE);
        curl_setopt($ch,CURLOPT_POSTFIELDS,$data);
    }
    curl_setopt($ch ,CURLOPT_URL,$url);
    // 设置使用https方式的请求

    // 3、执行请求
    $json = curl_exec($ch);
    // 4、关闭会话
    curl_close($ch);
    if($returnType=='json'){
        return json_decode($json,true);
    }
    return $json;
}
