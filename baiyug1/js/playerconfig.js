var mac_flag=1;         //播放器版本
var mac_second=5;       //播放前预加载广告时间 1000表示1秒
var mac_width="100%";      //播放器宽度0自适应
var mac_height=595;     //播放器高度
var mac_widthmob="100%";      //手机播放器宽度0自适应
var mac_heightmob=260;     //手机播放器高度
var mac_widthpop=704;   //弹窗窗口宽度
var mac_heightpop=566;  //弹窗窗口高度
var mac_showtop=1;     //美化版播放器是否显示头部
var mac_showlist=0;     //美化版播放器是否显示列表
var mac_autofull=0;     //是否自动全屏,0否,1是
var mac_buffer="";     //缓冲广告地址
var mac_prestrain="";  //预加载提示地址
var mac_colors="000000,F6F6F6,F6F6F6,333333,666666,FFFFF,FF0000,2c2c2c,ffffff,a3a3a3,2c2c2c,adadad,adadad,48486c,fcfcfc";   //背景色，文字颜色，链接颜色，分组标题背景色，分组标题颜色，当前分组标题颜色，当前集数颜色，集数列表滚动条凸出部分的颜色，滚动条上下按钮上三角箭头的颜色，滚动条的背景颜色，滚动条空白部分的颜色，滚动条立体滚动条阴影的颜色 ，滚动条亮边的颜色，滚动条强阴影的颜色，滚动条的基本颜色
var mac_show={},mac_show_server={}; //播放器名称,服务器组地址
//缓存开始
mac_show["youku"]="优酷";mac_show["mysohu"]="搜狐云/自媒体";mac_show["ykyun"]="优酷云";mac_show["migu"]="咪咕";mac_show["qzone"]="QQ空间";mac_show["douyu"]="斗鱼";mac_show["longzhu"]="龙珠";mac_show["huya"]="虎牙";mac_show["tangdou"]="糖豆";mac_show["vlook"]="微录客";mac_show["lecloud"]="乐视云计算";mac_show["m1905"]="m1905网";mac_show["cntv"]="CNTV央视";mac_show["youmi"]="优米网";mac_show["miaopai"]="秒拍网";mac_show["aipai"]="爱拍网";mac_show["kuaishou"]="快手";mac_show["cloud"]="cloud";mac_show["funshion"]="funshion";mac_show["xp"]="xp";mac_show["mp4"]="mp4";mac_show["xigua"]="西瓜";mac_show["2mm"]="恋恋影视";mac_show["ifeng"]="凤凰";mac_show["tudou"]="土豆";mac_show["pptv"]="PPTV";mac_show["tdyun"]="土豆云";mac_show["mgtv"]="芒果TV";mac_show["letv"]="乐视";mac_show["leyun"]="乐视云";mac_show["sohu"]="搜狐";mac_show["qq"]="腾讯";mac_show["iqiyi"]="爱奇艺";mac_show["qiyi"]="爱奇艺②";mac_show["56"]="56";mac_show["pptvyun"]="PPTV云";mac_show["sina"]="新浪";mac_show["m3u8"]="m3u8";mac_show["baiduyun"]="百度云";mac_show["baiyun"]="百云播";mac_show["kankan"]="看看";mac_show["yinyuetai"]="音悦台";mac_show["yy"]="YY神曲";mac_show["wasu"]="华数网";mac_show["bilibili"]="哔哩哔哩";mac_show["meipai"]="美拍";mac_show["xunlei"]="讯雷";mac_show_server["webplay"]="maccmsc.com";
//缓存结束