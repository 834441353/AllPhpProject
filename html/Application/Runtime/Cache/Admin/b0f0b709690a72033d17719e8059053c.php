<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>无标题文档</title>
    <link href="/Public/Admin/css/style.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/Public/Admin/js/jquery.js"></script>
</head>

<body>
    <div class="place">
        <span>位置：</span>
        <ul class="placeul">
            <li><a href="#">首页</a></li>
        </ul>
    </div>
    <div class="mainindex">
        <div class="welinfo">
            <span><img src="/Public/Admin/images/sun.png" alt="天气" /></span>
            <b><?php echo (session('mg_name')); ?>您好，欢迎使用商品管理系统</b>
            <a href="/index.php/Admin/Manager/setPwd">修改密码</a>
        </div>
        <div class="welinfo">
            <span><img src="/Public/Admin/images/time.png" alt="时间" /></span>
            <i>您上次登录的时间：<?php echo (date("Y-m-d H:i",session('mg_time'))); ?></i>
        </div>
        <div class="xline"></div>
        <div class="box"></div>
        <div class="welinfo">
            <span><img src="/Public/Admin/images/dp.png" alt="提醒" /></span>
            <b>环境信息</b>
        </div>
        <ul class="infolist">
            <li><span>服务器软件：</span><?php echo ($_SERVER['SERVER_SOFTWARE']); ?></li>
            <li><span>当前访问ip：</span><?php echo ($_SERVER['REMOTE_ADDR']); ?></li>
        </ul>
        <div class="xline"></div>
        <div class="uimakerinfo"><b>最新订单信息</b></div>
        <ul class="infolist">
            <li><a href="#">如何发布文章</a></li>
            <li><a href="#">如何访问网站</a></li>
            <li><a href="#">如何管理广告</a></li>
        </ul>
    </div>
</body>

</html>