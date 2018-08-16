<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>无标题文档</title>
    <link href="/Public/Admin/css/style.css" rel="stylesheet" type="text/css" />
    <script language="JavaScript" src="/Public/Admin/js/jquery.js"></script>
</head>

<body>
    <div class="place">
        <span>位置：</span>
        <ul class="placeul">
            <li><a href="#">首页</a></li>
            <li><a href="#">表单</a></li>
        </ul>
    </div>
    <div class="formbody">
        <div class="formtitle"><span>商品相册</span></div>
        <input type="hidden" name="id" value="<?php echo ($_GET['id']); ?>">
        <li style="border: 1px solid grey;margin-bottom: 20px;">
          
          <?php if(is_array($pics)): $i = 0; $__LIST__ = $pics;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$pic): $mod = ($i % 2 );++$i;?><font>
              <img src="<?php echo (ltrim($pic["pics_mid"],'.')); ?>" alt="图片已丢失"/>
              [<a href="javascript:;" data="<?php echo ($pic["pics_id"]); ?>" class="delete">－</a>]
            </font><?php endforeach; endif; else: echo "" ;endif; ?>
        </li>
        <form action="" method="post" enctype="multipart/form-data">
            <ul class="forminfo">
                <li>
                    <label>商品图片[<a href="javascript:;" class="add">＋</a>]</label>
                    <input name="pics[]" type="file" />
                </li>
                <li>
                    <label>&nbsp;</label>
                    <input name="" id="btnSubmit" type="button" class="btn" value="确认保存" />
                </li>
            </ul>
        </form>
    </div>
</body>
<script type="text/javascript">
$(function(){
    $('#btnSubmit').on('click',function(){
        $('form').submit();
    });

    //给商品图片+号绑定点击事件
    $(".add").click(function(){
      //把add的父元素的父元素li 克隆一份 放在 后面
      $(this).parent().parent().clone().insertAfter($(this).parent().parent()).find('a').html('－').click(function(){
        //带减号-的a标签,父元素的父元素li 删除掉 remove
        $(this).parent().parent().remove();
      });
    })

    //为-号绑定点击事件
    $(".delete").click(function(){
      var _font = $(this).parent();//font标签
      //获取当前图片的id
      var pics_id = $(this).attr('data');
      //ajax请求删除图片
      $.get("/index.php/Admin/Goods/deletePic",'id='+pics_id,function(msg){
        //true 1 false ''
        if(msg=='1'){
          //说明数据库中图片已删除
          _font.remove();
        }else{
          alert('删除失败,请稍后再试!');
        }
        // console.log(msg);
      },'json');
    })
});
</script>
</html>