<?php if (!defined('THINK_PATH')) exit();?>

<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>京西商城</title>
	<link rel="stylesheet" href="/Public/Home/style/base.css" type="text/css">
	<link rel="stylesheet" href="/Public/Home/style/global.css" type="text/css">
	<link rel="stylesheet" href="/Public/Home/style/header.css" type="text/css">
	<link rel="stylesheet" href="/Public/Home/style/login.css" type="text/css">
	<link rel="stylesheet" href="/Public/Home/style/footer.css" type="text/css">
	<script type="text/javascript" src="/Public/Home/js/jquery-1.8.3.min.js"></script>
</head>
<body>
	<!-- 顶部导航 start -->
	<!-- <div class="topnav">
		<div class="topnav_bd w990 bc">
			<div class="topnav_left">

			</div>
			<div class="topnav_right fr">
				
				<ul>
					<?php if($_SESSION['uid']> 0 ): ?><li><?php echo (session('uname')); ?>，欢迎来到京西！[<a href="<?php echo U('User/logout');?>">注销</a>] </li>
							<li class="line">|</li>
							<li>我的订单</li>
							<li class="line">|</li>
							<li>客户服务</li>
						<?php else: ?>
							<li>您好，欢迎来到京西！[<a href="<?php echo U('User/login');?>">登录</a>] [<a href="<?php echo U('User/register');?>">免费注册</a>] </li>
							<li class="line">|</li>
							<li>我的订单</li>
							<li class="line">|</li>
							<li>客户服务</li><?php endif; ?>
				</ul>
			</div>
		</div>
	</div> -->
	<!-- 顶部导航 end -->

	<div style="clear:both;"></div>

	<!-- 页面头部 start -->
	<div class="header w990 bc mt15">
		<div class="logo w990">
			<h2 class="fl"><a href="index.html"><img src="/Public/Home/images/logo.png" alt="京西商城"></a></h2>
		</div>
	</div>
	<!-- 页面头部 end -->

  <hr>




	<!-- 登录主体部分start -->
	<div class="login w990 bc mt10">
		<div class="login_hd">
			<h2>用户登录</h2>
			<b></b>
		</div>
		<div class="login_bd">
			<div class="login_form fl">
				<form action="" method="post">
					<ul>
						<li>
							<label for="">用户名：</label>
							<input type="text" class="txt" name="username" />
						</li>
						<li>
							<label for="">密码：</label>
							<input type="password" class="txt" name="password" />
							<a href="">忘记密码?</a>
						</li>
						<li class="checkcode">
							<label for="">验证码：</label>
							<input type="text"  name="checkcode" />
							<img src="/Home/User/captcha" alt="" />
							<span>看不清？<a id="refresh" href="javascript:;">换一张</a></span>
						</li>
						<li>
							<label for="">&nbsp;</label>
							<input type="checkbox" class="chb"  /> 保存登录信息
						</li>
						<li>
							<label for="">&nbsp;</label>
							<input type="button" value="" class="login_btn" />
							<input type="hidden" name="history" value=""/>
						</li>
					</ul>
				</form>

				<div class="coagent mt15">
					<dl>
						<dt>使用合作网站登录商城：</dt>
						<dd class="qq"><a href=""><span></span>QQ</a></dd>
						<dd class="weibo"><a href=""><span></span>新浪微博</a></dd>
						<dd class="yi"><a href=""><span></span>网易</a></dd>
						<dd class="renren"><a href=""><span></span>人人</a></dd>
						<dd class="qihu"><a href=""><span></span>奇虎360</a></dd>
						<dd class=""><a href=""><span></span>百度</a></dd>
						<dd class="douban"><a href=""><span></span>豆瓣</a></dd>
					</dl>
				</div>
			</div>

			<div class="guide fl">
				<h3>还不是商城用户</h3>
				<p>现在免费注册成为商城用户，便能立刻享受便宜又放心的购物乐趣，心动不如行动，赶紧加入吧!</p>

				<a href="<?php echo U('User/register');?>" class="reg_btn">免费注册 >></a>
			</div>

		</div>
	</div>
	<script type="text/javascript">
		$(function(){
			//打印上一个历史页面url
			// alert(document.referrer);只能获取通过a标签超链接跳转的上一页url 不会获取通过浏览器地址栏访问的url
			//当页面就绪时,为表单中的history域添加一个url数据
			$("[name=history]").val(document.referrer);

			$("#refresh").click(function(){
				//删除原先的验证码图
				$(this).parent().prev().remove();
				$('<img src="/Home/User/captcha/rand/'+Math.random()+'" alt="" />').insertBefore($(this).parent());
			})

			$('[name=checkcode]').blur(function(){
				$.get('/Home/User/check','code='+$(this).val(),function(msg){
					if(msg){
						$('form').data('captcha',true);
					}else{
						$('form').data('captcha',false);
						alert('验证码错误');
						//自动刷新验证码
						$("#refresh").trigger('click');
					}
				},'text')
			})

			$(".login_btn").click(function(){
				//手动触发验证码框的blur事件
				$('[name=checkcode]').trigger('blur');
				//检查验证码是否正确
				if($('form').data('captcha')){
					//验证码正确
					$('form').submit();
				}
			})
		})
	</script>
	<!-- 登录主体部分end -->

  <hr>



<!-- 底部版权 start -->
<div class="footer w1210 bc mt15">
  <p class="links">
    <a href="">关于我们</a> |
    <a href="">联系我们</a> |
    <a href="">人才招聘</a> |
    <a href="">商家入驻</a> |
    <a href="">千寻网</a> |
    <a href="">奢侈品网</a> |
    <a href="">广告服务</a> |
    <a href="">移动终端</a> |
    <a href="">友情链接</a> |
    <a href="">销售联盟</a> |
    <a href="">京西论坛</a>
  </p>
  <p class="copyright">
     © 2005-2013 京东网上商城 版权所有，并保留所有权利。  ICP备案证书号:京ICP证070359号
  </p>
  <p class="auth">
    <a href=""><img src="/Public/Home/images/xin.png" alt="" /></a>
    <a href=""><img src="/Public/Home/images/kexin.jpg" alt="" /></a>
    <a href=""><img src="/Public/Home/images/police.jpg" alt="" /></a>
    <a href=""><img src="/Public/Home/images/beian.gif" alt="" /></a>
  </p>
</div>
<!-- 底部版权 end -->

</body>
</html>