<?php
namespace Home\Controller;

use Think\Controller;

class MsgController extends Controller
{

    public function index()
    {
        var_dump(get_access_token());
    }

    public function valid()
    {
        $echoStr = $_GET["echostr"];

        //valid signature , option
        if ($echoStr) {
            if ($this->checkSignature()) {
                echo $echoStr;
                exit;
            }
        } else {
            $this->responseMsg();

        }

    }

//    public function responseMsg()
//    {
//        //get post data, May be due to the different environments
//        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
//
//        if (empty($postStr)) {
//            echo '';
//            exit;
//        }
//
//        //extract post data
//
//        /* libxml_disable_entity_loader is to prevent XML eXternal Entity Injection,
//           the best way is to check the validity of xml by yourself */
//        libxml_disable_entity_loader(true);
//        //$postObjwei 为传输的xml格式对象 ， 此对象下所有传输的数据
//        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
//        //具体发送信息的微信用户
//        $fromUsername = $postObj->FromUserName;
//        //公众平台标识
//        $toUsername = $postObj->ToUserName;
//
//        $keyword = trim($postObj->Content);
//        $time = time();
//        //回复文本消息模版
//        $textTpl = "<xml>
//                            <ToUserName><![CDATA[%s]]></ToUserName>
//                            <FromUserName><![CDATA[%s]]></FromUserName>
//                            <CreateTime>%s</CreateTime>
//                            <MsgType><![CDATA[%s]]></MsgType>
//                            <Content><![CDATA[%s]]></Content>
//                            <FuncFlag>0</FuncFlag>
//                        </xml>";
//        //回复图片消息的模板
//        $picTpl = "<xml>
//                        <ToUserName><![CDATA[%s]]></ToUserName>
//                        <FromUserName><![CDATA[%s]]></FromUserName>
//                        <CreateTime>%s</CreateTime>
//                        <MsgType><![CDATA[%s]]></MsgType>
//                        <Image>
//                        <MediaId><![CDATA[%s]]></MediaId>
//                        </Image>
//                        </xml>";
//        // 回复语音消息的模板
//        $voiceTpl = "<xml>
//                        <ToUserName><![CDATA[%s]]></ToUserName>
//                        <FromUserName><![CDATA[%s]]></FromUserName>
//                        <CreateTime>%s</CreateTime>
//                        <MsgType><![CDATA[%s]]></MsgType>
//                        <Voice>
//                        <MediaId><![CDATA[%s]]></MediaId>
//                        </Voice>
//                        </xml>";
//        $musicTpl = "<xml>
//                        <ToUserName><![CDATA[%s]]></ToUserName>
//                        <FromUserName><![CDATA[%s]]></FromUserName>
//                        <CreateTime>%s</CreateTime>
//                        <MsgType><![CDATA[%s]]></MsgType>
//                        <Music>
//                        <Title><![CDATA[%s]]></Title>
//                        <Description><![CDATA[%s]]></Description>
//                        <MusicUrl><![CDATA[%s]]></MusicUrl>
//                        <HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
//                        <ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
//                        </Music>
//                        </xml>";
//        $newsTpl = "<xml>
//                    <ToUserName><![CDATA[%s]]></ToUserName>
//                    <FromUserName><![CDATA[%s]]></FromUserName>
//                    <CreateTime>%s</CreateTime>
//                    <MsgType><![CDATA[%s]]></MsgType>
//                    <ArticleCount>%s</ArticleCount>
//                    <Articles>%s</Articles>
//                    </xml>";
//        //获取接受消息的类型
//        $MsgType = $postObj->MsgType;
//
////        if ($MsgType=='text'){
////}
//        $contentStr = $postObj->Content;
//        $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, 'text', $MsgType);
//        echo $resultStr;
//
//        $imageMediaId = 'oR4aMsL0ERZo5dFK7oT5NVpfsOJ9nhn0Pwz_NuqjQw6ek4O1bpq76mdM2FSZ1amF';
//        $voiceMediaId ='pSoMsA21sSispcYSaoG-k-qYrCfZfeBm8-6dnCA9fKLMi9-JGCwz4-I-wfP1qla6';
//        $videoMediaId ='Kq-2YMeZ1UPiB6_w7R_BigXlOyRvrEx8crs5rt3_BHKTkYCniybqd8j34LrK8A9R';
//
//
//    }


    public function responseMsg()
    {
        //get post data, May be due to the different environments
        $postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
        if (empty($postStr)) {
            echo "";
            exit;
        }
        libxml_disable_entity_loader(true);
        // $postObj为传输的xml格式对象。此对象下有所有传输的数据
        $postObj = simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
        file_put_contents('log.txt', json_encode($postObj));
        // 具体发送信息的微信用户
        $fromUsername = $postObj->FromUserName;
        // 公众平台标识
        $toUsername = $postObj->ToUserName;
        $time = time();
        $textTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<Content><![CDATA[%s]]></Content>
					<FuncFlag>0</FuncFlag>
					</xml>";
        //回复图片消息的模板
        $picTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<Image>
					<MediaId><![CDATA[%s]]></MediaId>
					</Image>
					</xml>";
        // 回复语音消息的模板
        $voiceTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<Voice>
					<MediaId><![CDATA[%s]]></MediaId>
					</Voice>
					</xml>";
        $musicTpl = "<xml>
					<ToUserName><![CDATA[%s]]></ToUserName>
					<FromUserName><![CDATA[%s]]></FromUserName>
					<CreateTime>%s</CreateTime>
					<MsgType><![CDATA[%s]]></MsgType>
					<Music>
					<Title><![CDATA[%s]]></Title>
					<Description><![CDATA[%s]]></Description>
					<MusicUrl><![CDATA[%s]]></MusicUrl>
					<HQMusicUrl><![CDATA[%s]]></HQMusicUrl>
					<ThumbMediaId><![CDATA[%s]]></ThumbMediaId>
					</Music>
					</xml>";
        $newsTpl = "<xml>
				<ToUserName><![CDATA[%s]]></ToUserName>
				<FromUserName><![CDATA[%s]]></FromUserName>
				<CreateTime>%s</CreateTime>
				<MsgType><![CDATA[%s]]></MsgType>
				<ArticleCount>%s</ArticleCount>
				<Articles>%s</Articles>
				</xml>";
        $videoTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Video>
                        <MediaId><![CDATA[%s]]></MediaId>
                        <Title><![CDATA[%s]]></Title>
                        <Description><![CDATA[%s]]></Description>
                        </Video> 
				    </xml>";

        $linkTpl = "<xml>
                        <ToUserName><![CDATA[%s]]></ToUserName>
                        <FromUserName><![CDATA[%s]]></FromUserName>
                        <CreateTime>%s</CreateTime>
                        <MsgType><![CDATA[%s]]></MsgType>
                        <Event><![CDATA[%s]]></Event>
                        <EventKey><![CDATA[%s]]></EventKey>
                    </xml>";
        //获取接受消息的类型
        $MsgType = $postObj->MsgType;

        if ($MsgType == 'text') {
            $keyword = $postObj->Content;
            if ($keyword == '图片') {
                //回复图片消息
                //此为一个图片的MediaId。测试时通过接受图片消息获取到MediaId在使用
                $MediaId = 'oR4aMsL0ERZo5dFK7oT5NVpfsOJ9nhn0Pwz_NuqjQw6ek4O1bpq76mdM2FSZ1amF';
                //将第一个变量使用后续的变量逐个替换掉“占位符”
                $resultStr = sprintf($picTpl, $fromUsername, $toUsername, $time, 'image', $MediaId);
            } elseif ($keyword == '语音') {
                // 回复语音消息
                $MediaId = 'pSoMsA21sSispcYSaoG-k-qYrCfZfeBm8-6dnCA9fKLMi9-JGCwz4-I-wfP1qla6';
                //将第一个变量使用后续的变量逐个替换掉“占位符”
                $resultStr = sprintf($voiceTpl, $fromUsername, $toUsername, $time, 'voice', $MediaId);
            } elseif ($keyword == '音乐') {
                // 回复语音消息
                $MediaId = 'oR4aMsL0ERZo5dFK7oT5NVpfsOJ9nhn0Pwz_NuqjQw6ek4O1bpq76mdM2FSZ1amF';
                //将第一个变量使用后续的变量逐个替换掉“占位符”
                $resultStr = sprintf($musicTpl, $fromUsername, $toUsername, $time, 'music', 'トルコ行進曲', 'トルコ行進曲', 'http://sunxuejia.xin/Public/music/miaomiao.mp3', 'http://sunxuejia.xin/Public/music/miaomiao.mp3', $MediaId);
                file_put_contents('log.txt', $resultStr);
            } elseif ($keyword == '图文') {
                //模拟数据库中获取到的数据
                $data = array(
                    array('Title' => '音乐之声加图文', 'Description' => '音乐之声加图文', 'PicUrl' => 'http://39.106.115.140/1.jpg', 'Url' => 'http://baidu.com'),
                    array('Title' => '音乐之声加图文', 'Description' => '音乐之声加图文', 'PicUrl' => 'http://39.106.115.140/1.jpg', 'Url' => 'http://baidu.com'),
                );
                $ArticleCount = count($data);
                foreach ($data as $v) {
                    $items .= "<item>
						<Title><![CDATA[{$v['Title']}]]></Title> 
						<Description><![CDATA[{$v['Description']}]]></Description>
						<PicUrl><![CDATA[{$v['PicUrl']}]]></PicUrl>
						<Url><![CDATA[{$v['Url']}]]></Url>
						</item>";
                }
                //将第一个变量使用后续的变量逐个替换掉“占位符”
                $resultStr = sprintf($newsTpl, $fromUsername, $toUsername, $time, 'news', $ArticleCount, $items);
                file_put_contents('log.txt', $resultStr);
            } elseif ($keyword == '视频') {
                $MediaId = 'sPqvL35wwievdtC7TjYM0yrcewds7gi9oWMWkFbki0U';
                $resultStr = sprintf($videoTpl, $fromUsername, $toUsername, $time, 'video', $MediaId, '雷神3', '诸神黄昏');
                file_put_contents('log1.txt', $resultStr);
            } else {
                //接受的为文本消息
                $contentStr = $postObj->Content;//接受的文本消息内容
                //将第一个变量使用后续的变量逐个替换掉“占位符”
                $contentStr = $this->tuling($contentStr,$fromUsername);
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, 'text', $contentStr['text']);
                file_put_contents('log.txt', $resultStr);
            }
            echo $resultStr;
        } elseif ($MsgType == 'image') {
            //表示用户发送的为图片消息
            $contentStr = '图片地址为：' . $postObj->PicUrl . '图片的MediaId为：' . $postObj->MediaId;
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, 'text', $contentStr);
            echo $resultStr;
        } elseif ($MsgType == 'voice') {
            // 表示为语音消息
            $contentStr = '发送的内容为：' . $postObj->Recognition . '语音消息' . $postObj->MediaId;
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, 'text', $contentStr);
            echo $resultStr;
        } elseif ($MsgType == 'video') {
            // 表示为视频消息
            $contentStr = '高清大片地址在：' . $postObj->MediaId;
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, 'text', $contentStr);
            echo $resultStr;
        } elseif ($MsgType == 'location') {
            // 表示地理位置消息
            $contentStr = '坐标点X' . $postObj->Location_X . 'Y:' . $postObj->Location_Y . '具体地点：' . $postObj->Label;
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, 'text', $contentStr);
            echo $resultStr;
        } elseif ($MsgType == 'link') {
            // 表示地理位置消息
            $contentStr = '标题' . $postObj->Title . '描述:' . $postObj->Description . '连接地址：' . $postObj->Url;
            $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, 'text', $contentStr);
            echo $resultStr;
        }elseif ($MsgType == 'event'){

            $Event = $postObj ->Event;
            if ($Event == 'CLICK'){
                $EventKey = $postObj->EventKey;
                if ($EventKey == 'info'){
                    $MediaId = 'bdc8XzFSDzsB-MzzyKY9o8db2UVMIW1oNDGEkGjB42OJic1GfRc25b_8RTx2_xN0';
                    $resultStr = sprintf($picTpl, $fromUsername, $toUsername, $time, 'image', $MediaId);
                    echo $resultStr;
                }
//                elseif ($EventKey == 'local'){
//                    $x = $postObj -> Location_X;
//                    $y = $postObj -> Location_Y;
//                    $ak ='FVjIwAZh4LfRuFmQr4GbUBeyGBfNvohM';
//                    $url = 'http://api.map.baidu.com/geocoder/v2/?location='.$x.','.$y.'&output=json&pois=1&ak='.$ak;
//                    $res = file_get_contents($url);
//                    $res = json_decode($res,true);
//                    $contentStr = '位置：' . $res['result']['formatted_address'] . '邮编:' . $res['result']['addressComponent']['adcode'] . '坐标：' . $res['result']['location']['lng'].','.$res['result']['location']['lat'];
//                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, 'text', '1'.$x);
//                    echo $resultStr;
//                    file_put_contents('log1.txt',$resultStr);
//                }

            }elseif ($Event == 'subscribe'){//关注回复消息
                $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, 'text', '你的操作666');
                echo $resultStr;
            }elseif ($Event == 'SCAN'){
                $EventKey = $postObj->EventKey;
                if($EventKey == 'paymoney'){
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, 'text', '你的操作999');
                    echo $resultStr;
                }else{
                    $resultStr = sprintf($textTpl, $fromUsername, $toUsername, $time, 'text', '你的操作888');
                    echo $resultStr;
                }
            }
        }


    }

    private function checkSignature()
    {
        // you must define TOKEN by yourself

        $signature = $_GET["signature"];
        $timestamp = $_GET["timestamp"];
        $nonce = $_GET["nonce"];

        $token = 'weixin';
        $tmpArr = array($token, $timestamp, $nonce);
        // use SORT_STRING rule
        sort($tmpArr, SORT_STRING);
        $tmpStr = implode($tmpArr);
        $tmpStr = sha1($tmpStr);

        if ($tmpStr == $signature) {
            return true;
        } else {
            return false;
        }
    }


    public function createMenus()
    {
        $accessToken = get_access_token();
//        dump($accessToken);exit();
        // 接口的请求地址
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/create?access_token=' . $accessToken;
//        dump($url);exit;
//        // 直接拼接json格式的参数
//         	$data = '{
//         		"button":[
//         			{
//         				"name":"PHP",
//         				"type":"click",
//         				"key":"friends"
//         },
//         {
//         	"name":"语言排行",
//         	"sub_button":[
//         		{
//         			"name":"PHP",
//         			"type":"view",
//         			"url":"http://www.php.com"
//         		},
//         		{
//         			"name":"Python",
//         			"type":"click",
//         			"key":"python"
//         		}
//         	]
//         },{
//         	"name":"个人介绍",
//         	"type":"click",
//         	"key":"me"
//         }
//         		]
//         	}';
//         dump(json_decode($data,true)) ;
        //拼接数组格式然后在转换为json格式

        $data = array(
            'button' => array(
                array(
                    'type' => 'click',
                    'name' => '个人介绍',
                    'key' => 'info'
                ),
                array(
                    'name' => '语言',
                    'sub_button' => array(
                        array(
                            'type' => 'view',
                            'name' => 'PHP',
                            'url' => 'http://www.php.net'
                        ),
                        array(
                            'type' => 'view',
                            'name' => 'JAVA',
                            'url' => 'http://www.java.net'
                        )
                    )
                ),
                array(
                    'type' => 'view',
                    'name' => '商城',
                    'url' => 'http://sunxuejia.xin/Home/Wxtest/index'
                )
            )
        );

//        // JSON_UNESCAPED_UNICODE选项是针对中文字符不做处理
//        // 如果为5.4版本以下的 选项不能使用 需要对中文进行转换urldecode(str) urlencode(str)
        $data = json_encode($data, JSON_UNESCAPED_UNICODE);

//        var_dump($data);exit;
        $res = http_curl($url, $data, 'post');
        dump($res);
    }

    //获取目前自定义菜单信息
    public function getMenus()
    {
        $accessToken = get_access_token();
        dump($accessToken);
        // 接口的请求地址
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/get';
        $res = http_curl($url, array('access_token' => $accessToken));
        dump($res);
    }

    public function delMenus()
    {
        $accessToken = get_access_token();
        dump($accessToken);
        // 接口的请求地址
        $url = 'https://api.weixin.qq.com/cgi-bin/menu/delete';
        $res = http_curl($url, array('access_token' => $accessToken));
        dump($res);
    }


    //上传临时文件(图片)
    public function lsupload()
    {
        $accessToken = get_access_token();
        $url = 'https://api.weixin.qq.com/cgi-bin/media/upload?access_token=' . $accessToken . '&type=image';
        $file = dirname(THINK_PATH) . '/Public/image/1.jpg';
        $data = array(
            'media' => '@' . $file
        );
//        dump(file_exists($file));
//        dump($data);exit();
        $res = http_curl($url, $data, 'post');
        dump($res);
    }

    //上传永久素材(视频)
    public function yjupload()
    {
        $accessToken = get_access_token();
        $url = 'https://api.weixin.qq.com/cgi-bin/material/add_material?access_token=' . $accessToken . '&type=video';
        $file = dirname(THINK_PATH) . '/Public/video/1.mp4';
        $data = array(
            'media' => '@' . $file,
            'description' => '{
                "title":"小视频",
                "introduction":"小视频"
            }'
        );
        $res = http_curl($url, $data, 'post');
        dump($res);
    }


    //获取地理位置信息
    public function getAddress(){

        $ak ='FVjIwAZh4LfRuFmQr4GbUBeyGBfNvohM';
        $url = 'http://api.map.baidu.com/geocoder/v2/?location=39.934,116.329&output=json&pois=1&ak='.$ak;
        $res = file_get_contents($url);

//        dump($res);
        dump(json_decode($res,true));

    }


    //图灵机器人
    public function tuling($info,$userid){
        $apikey = '997d65e265764a46810153ff5e7f5eb4';
//        $url = 'http://www.tuling123.com/openapi/api';
        $url = 'http://www.tuling123.com/openapi/api?key='.$apikey.'&info='.$info.'&userid='.$userid;
//    $data = array(
//        'key'=>$apikey,
//        'info'=>I('get.info'),
//        'userid'=>123
//    );
//    $res= http_curl($url,$data);
        $res = http_curl($url);
        return $res ;
    }

    //创建二维码
    public function createQR(){
        $accessToken = get_access_token();
        $url = 'https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.$accessToken;
//        $data = array(
//            "expire_seconds"=>30,
//            "action_name"=>"QR_STR_SCENE",
//            "action_info"=>array(
//                "scene"=>array(
//                    "scene_str"=>123
//                )
//            )
//        );
        $data = '{"expire_seconds": 3000, "action_name": "QR_LIMIT_STR_SCENE", "action_info": {"scene": {"scene_str": "test"}}}';
        $res = http_curl($url,$data,'post');
//        dump($res);

        $url = 'https://mp.weixin.qq.com/cgi-bin/showqrcode';
        $data = array('ticket'=>$res['ticket']);
        $res = http_curl($url,$data,'get','string');
        file_put_contents('code.jpg',$res);

    }
}

