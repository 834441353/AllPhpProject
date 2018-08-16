<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html;charset=UTF-8">
	<title>购物车页面</title>
	<link rel="stylesheet" href="/Public/Home/style/base.css" type="text/css">
	<link rel="stylesheet" href="/Public/Home/style/global.css" type="text/css">
	<link rel="stylesheet" href="/Public/Home/style/header.css" type="text/css">
	<link rel="stylesheet" href="/Public/Home/style/footer.css" type="text/css">

	<script type="text/javascript" src="/Public/Home/js/jquery-1.8.3.min.js"></script>

</head>

<script type="text/javascript">
	$(function(){
		$("#logout").click(function(){
			$.get("<?php echo U('user/logout');?>",'',function(msg){
				//刷新网页
				window.location = "<?php echo U('user/login');?>";
			},'text')
		})
	})
</script>
<body>
	<!-- 顶部导航 start -->
	<div class="topnav">
		<div class="topnav_bd w990 bc">
			<div class="topnav_left">

			</div>
			<div class="topnav_right fr">
				
				<ul>
					<?php if($_SESSION['uid']> 0 ): ?><li><?php echo (session('uname')); ?>，欢迎来到京西！[<a id="logout" href="javascript:;">注销</a>] </li>
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
	</div>
	<!-- 顶部导航 end -->

	<div style="clear:both;"></div>

	<!-- 页面头部 start -->
	<div class="header w990 bc mt15">
		<div class="logo w990">
			<h2 class="fl"><a href="index.html"><img src="/Public/Home/images/logo.png" alt="京西商城"></a></h2>

      
      <?php if(strtolower(ACTION_NAME)== 'flow1' ): ?><div class="flow fr">
          <ul>
            <li class="cur">1.我的购物车</li>
            <li>2.填写核对订单信息</li>
            <li>3.成功提交订单</li>
          </ul>
        </div>
      <?php elseif(strtolower(ACTION_NAME)== 'flow2' ): ?>
        <div class="flow fr flow2">
          <ul>
            <li>1.我的购物车</li>
            <li class="cur">2.填写核对订单信息</li>
            <li>3.成功提交订单</li>
          </ul>
        </div>
      <?php elseif(strtolower(ACTION_NAME)== 'flow3' ): ?>
        <div class="flow fr flow3">
          <ul>
            <li>1.我的购物车</li>
            <li>2.填写核对订单信息</li>
            <li class="cur">3.成功提交订单</li>
          </ul>
        </div><?php endif; ?>



		</div>
	</div>
	<!-- 页面头部 end -->



	
	<link rel="stylesheet" href="/Public/Home/style/cart.css" type="text/css">
	<script type="text/javascript" src="/Public/Home/js/cart1.js"></script>
	<script type="text/javascript">
	$(function(){
		$(".delete").click(function(){
			var _this = $(this);
			//获取商品id,ajax删除商品信息
			$.get('<?php echo U("cart/delete");?>','id='+$(this).attr('data'),function(msg){
				_this.parent().parent().remove();
				//重新计价
				var total = 0;
				$(".col5 span").each(function(){
					total += parseFloat($(this).text());
				});

				$("#total").text(total.toFixed(2));
			},'text');
		})

		//绑定blur事件 +事件 -事件 但是调用同样的函数
		//减少
		$(".reduce_num").click(modify);

		//增加
		$(".add_num").click(modify);

		//直接输入
		$(".amount").blur(modify);

		function modify(){
			//获取修改的商品id
			var id = $(this).parent().find('.amount').attr('data');
			//获取修改后的数量
			var amount = $(this).parent().find('.amount').val();
			//ajax修改购物车中的商品数量
			$.get("<?php echo U('cart/modify');?>",'id='+id+'&amount='+amount,function(msg){
				//响应后修改当前页面的价格
			},'text');
		}
	})
	</script>

	<div style="clear:both;"></div>

	<!-- 主体部分 start -->
	<div class="mycart w990 mt10 bc">
		<h2><span>我的购物车</span></h2>
		<table>
			<thead>
				<tr>
					<th class="col1">商品名称</th>
					<th class="col2">商品信息</th>
					<th class="col3">单价</th>
					<th class="col4">数量</th>
					<th class="col5">小计</th>
					<th class="col6">操作</th>
				</tr>
			</thead>
			<tbody>
				
				<?php if(is_array($infos)): $i = 0; $__LIST__ = $infos;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$info): $mod = ($i % 2 );++$i;?><tr>
						<td class="col1"><a href=""><img src="<?php echo (ltrim($info["goods_small_logo"],'.')); ?>" alt="暂无图片" /></a>  <strong><a href=""><?php echo ($info["goods_name"]); ?></a></strong></td>
						<td class="col2"><?php echo ($info["goods_attrs"]); ?></td>
						<td class="col3">￥<span><?php echo ($info["goods_price"]); ?></span></td>
						<td class="col4">
							<a href="javascript:;" class="reduce_num"></a>
							<input type="text" name="amount" data="<?php echo ($info["goods_id"]); ?>" value="<?php echo ($info["goods_buy_number"]); ?>" class="amount"/>
							<a href="javascript:;" class="add_num"></a>
						</td>
						<td class="col5">￥<span><?php echo ($info["goods_total_price"]); ?></span></td>
						<td class="col6"><a class="delete" data="<?php echo ($info["goods_id"]); ?>" href="javascript:;">删除</a></td>
					</tr><?php endforeach; endif; else: echo "" ;endif; ?>
			</tbody>
			<tfoot>
				<tr>
					<td colspan="6">购物金额总计： <strong>￥ <span id="total">1870.00</span></strong></td>
				</tr>
			</tfoot>
		</table>
		<div class="cart_btn w990 bc mt10">
			<a href="<?php echo U('index/index');?>" class="continue">继续购物</a>
			<a href="<?php echo U('flow2');?>" class="checkout">结 算</a>
		</div>
	</div>
	<!-- 主体部分 end -->

	<div style="clear:both;"></div>


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