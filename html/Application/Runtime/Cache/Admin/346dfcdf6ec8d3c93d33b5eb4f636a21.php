<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>无标题文档</title>
    <link href="/Public/Admin/css/style.css" rel="stylesheet" type="text/css" />

    <!-- 加载UE插件必要文件 -->
    <script type="text/javascript" charset="utf-8" src="/Public/Admin/ueditor/ueditor.config.js"></script>
    <script type="text/javascript" charset="utf-8" src="/Public/Admin/ueditor/ueditor.all.min.js"> </script>
    <script type="text/javascript" charset="utf-8" src="/Public/Admin/ueditor/lang/zh-cn/zh-cn.js"></script>
    <!-- 加载jQuery -->
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
        <div class="formtitle"><span>基本信息</span></div>
        <form action="" method="post" enctype="multipart/form-data">
            <ul class="forminfo">
                <li>
                    <label>商品名称</label>
                    <input name="name" placeholder="请输入商品名称" type="text" class="dfinput" /><i>名称不能超过30个字符</i></li>

                <li>
                    <label>商品类型</label>
                    <select name="type" class="dfinput">
                        <option value="" selected="selected" disabled="disabled">--请选择--</option>
                        
                        <?php if(is_array($types)): $i = 0; $__LIST__ = $types;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$type): $mod = ($i % 2 );++$i;?><option value="<?php echo ($type["type_id"]); ?>"><?php echo ($type["type_name"]); ?></option><?php endforeach; endif; else: echo "" ;endif; ?>
                    </select>
                    <i></i>
                </li>

                <li>
                    <label>商品价格</label>
                    <input name="price" placeholder="请输入商品价格" type="text" class="dfinput" /><i></i></li>
                <li>
                    <label>商品数量</label>
                    <input name="amount" placeholder="请输入商品数量" type="text" class="dfinput" />
                </li>
                <li>
                    <label>商品重量(kg)</label>
                    <input name="weight" placeholder="请输入商品重量" type="text" class="dfinput" />
                </li>
                <li>
                    <label>商品图标</label>
                    <input name="logo" type="file" />
                </li>

                <li><label>是否显示</label><cite><input name="show" type="radio" value="1" checked="checked" />是&nbsp;&nbsp;&nbsp;&nbsp;<input name="show" type="radio" value="0" />否</cite></li>

                <li>
                    <label>商品介绍
                      <textarea name="desc" style="width:660px;height:350px" id="myueditor" placeholder="请输入商品描述" cols="" rows=""></textarea>
                    </label>
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
    //实例化编辑器
    var ue = UE.getEditor('myueditor');


    $(function(){
      $("#btnSubmit").click(function(){
        $('form').submit();
      })

      $("[name=type]").change(function(){
        var _type = $(this);
        //获取管理员选择的商品分类
        var type_id = $(this).val();
        //向服务器发起ajax请求,查询这个分类下支持哪些属性
        $.get("/index.php/Admin/Goods/getAttr",'id='+type_id,function(msg){
          //遍历json格式数据,进行DOM操作,添加新的属性选项
          // console.log(msg);
          //for in
          $(msg).each(function(){
            var element = $("<li></li>");
            element.append($('<label></label>').html(this.attr_name));
            //attr_sel是否唯一   attr_write是否手工录入
            //0                   0                     允许手工录入,但只能写一个值 可写input
            //0                   1                     不能手工录入,系统中也只有一个值 不可写input
            //1                   0                     允许手工录入,但可以写多个值 可写input
            //1                   1                     不能手工录入,系统提供了多个值 checkbox
            if(this.attr_write == '0'){
              element.append($('<input type="text" name="attr['+this.attr_id+'][]" value="" class="dfinput"/>'));
            }

            if(this.attr_write == '1' && this.attr_sel == '0'){
              element.append($('<input type="text" name="" disabled value="" class="dfinput"/>').val(this.attr_vals));
              element.append($('<input type="hidden" name="attr['+this.attr_id+'][]" value=""/>').val(this.attr_vals));
            }

            if(this.attr_write == '1' && this.attr_sel == '1'){
              //遍历attr_vals,根据可选值,追加复选框
              var vals = this.attr_vals.split(',');
              for(var i in vals){
                element.append($('<input type="checkbox" name="attr['+this.attr_id+'][]" value="'+vals[i]+'">'));
                element.append(vals[i]);
              }
            }
            //完整的li元素要放在
            _type.parent().after(element);
          })
        },'json');
      })
    })
</script>

</html>