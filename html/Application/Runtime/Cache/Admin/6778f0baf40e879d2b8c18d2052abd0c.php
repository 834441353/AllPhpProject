<?php if (!defined('THINK_PATH')) exit();?>﻿<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>无标题文档</title>
    <link href="/Public/Admin/css/style.css" rel="stylesheet" type="text/css" />
    <script language="JavaScript" src="/Public/Admin/js/jquery.js"></script>
    <script type="text/javascript">
    $(function() {

        $('dd').find('ul').slideUp();
        //导航切换
        $(".menuson li").click(function() {
            $(".menuson li.active").removeClass("active")
            $(this).addClass("active");
        });

        $('.title').click(function() {
            var $ul = $(this).next('ul');
            $('dd').find('ul').slideUp();
            if ($ul.is(':visible')) {
                $(this).next('ul').slideUp();
            } else {
                $(this).next('ul').slideDown();
            }
        });
    })
    </script>
</head>

<body style="background:#f0f9fd;">
    <div class="lefttop"><span></span>※ MENU ※</div>
    
    <?php if(is_array($auths)): $i = 0; $__LIST__ = $auths;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$auth): $mod = ($i % 2 );++$i;?><dl class="leftmenu">
        <?php if($auth["auth_pid"] == 0 ): ?><dd>
              <div class="title">
                  <span><img src="/Public/Admin/images/leftico01.png" /></span><?php echo ($auth["auth_name"]); ?>
              </div>
              <ul class="menuson">
                
                <?php if(is_array($auths)): $i = 0; $__LIST__ = $auths;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$at): $mod = ($i % 2 );++$i; if($at["auth_pid"] == $auth["auth_id"] ): ?><li>
                      <cite></cite><a href="/index.php/Admin/<?php echo ($at["auth_c"]); ?>/<?php echo ($at["auth_a"]); ?>" target="rightFrame"><?php echo ($at["auth_name"]); ?>
                      </a><i></i>
                  </li><?php endif; endforeach; endif; else: echo "" ;endif; ?>
                
              </ul>
          </dd><?php endif; ?>
      </dl><?php endforeach; endif; else: echo "" ;endif; ?>
    
</body>

</html>