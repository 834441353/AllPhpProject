<?php if (!defined('THINK_PATH')) exit();?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title>无标题文档</title>
    <link href="/Public/Admin/css/style.css" rel="stylesheet" type="text/css" />
    <script type="text/javascript" src="/Public/Admin/js/jquery.js"></script>
    <script type="text/javascript">
    $(document).ready(function() {
        $(".click").click(function() {
            $(".tip").fadeIn(200);
        });

        $(".tiptop a").click(function() {
            $(".tip").fadeOut(200);
        });

        $(".sure").click(function() {
            $(".tip").fadeOut(100);
        });

        $(".cancel").click(function() {
            $(".tip").fadeOut(100);
        });

        $("#checkAll").click(function(){
          if(!$(this).data('flag')){
            $(this).data('flag',true);
            $(':checkbox').not($(this)).attr('checked','checked');
          }else{
            $(this).data('flag',false);
            $(':checkbox').not($(this)).removeAttr('checked');
          }
        })

        $("#add").click(function(){
          window.location="<?php echo U('add');?>";
        })

        $(".reset").click(function(){
          //要重置的用户id
          var id=$(this).parent().siblings().eq(1).html().trim();
          //要重置的用户name
          var name=$(this).parent().siblings().eq(2).html().trim();

          var confirm = window.confirm('确定要重置　['+name+']　的密码吗?');

          if(confirm){
            //ajax重置
            $.get("<?php echo U('manager/resetPwd');?>",'id='+id,function(msg){
              alert('重置后的密码为:\r\n'+msg);
            },'text');
          }else{
            //提示
            alert('请谨慎操作!');
          }
        })

        $(".setRole").click(function(){
          //要分配角色的用户id
          var id=$(this).parent().siblings().eq(1).html().trim();
          window.location = "/index.php/Admin/Manager/setRole/id/"+id;
        })

        $("#delete").click(function(){
          //提醒管理员要删除大量账号,请输入面
          var pwd = window.prompt('您将删除已选账号,请输入密码:');
          //发起ajax请求,验证密码 用户名和密码
          $.post("<?php echo U('Public/verify');?>",'username=<?php echo (session('mg_name')); ?>&password='+pwd,function(msg){
            if(msg){
              //真的要删除吗
              var confirm = window.confirm('您确认要删除账号吗?');
              if(confirm){
                //真的删除
                //获取所有的选中的账号id  12345 1,2,3,4,5   where mg_id in (1,2,3,4,5)
                var str = ''
                $(":checked").each(function(){
                  str+=$(this).val()+',';
                })
                str = str.substr(0,str.length-1);
                //向数据库发起ajax请求,要求删除 str当中的id
                $.get("<?php echo U('manager/delete');?>",'id='+str,function(msg){
                  alert(msg);
                  window.location = "<?php echo U('showlist');?>";
                },'text')
              }else{
                alert('下次请想好再来!');
              }
            }else{
              alert('密码错误!');
            }
          },'text');
        })

    });
    </script>
</head>

<body>
    <div class="place">
        <span>位置：</span>
        <ul class="placeul">
            <li><a href="#">首页</a></li>
            <li><a href="#">数据表</a></li>
            <li><a href="#">基本内容</a></li>
        </ul>
    </div>
    <div class="rightinfo">
        <div class="tools">
            <ul class="toolbar">
                <li id="add"><span><img src="/Public/Admin/images/t01.png" /></span>添加</li>
                <li><span><img src="/Public/Admin/images/t02.png" /></span>修改</li>
                <li id="delete"><span><img src="/Public/Admin/images/t03.png" /></span>删除</li>
                <li><span><img src="/Public/Admin/images/t04.png" /></span>统计</li>
            </ul>
        </div>
        <table class="tablelist">
            <thead>
                <tr>
                    <th>
                        <input name="" type="checkbox" value="0" id="checkAll" />
                    </th>
                    <th>编号</th>
                    <th>用户名</th>
                    <th>上次登录</th>
                    <th>角色</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
              
              <?php if(is_array($data)): $i = 0; $__LIST__ = $data;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$mg): $mod = ($i % 2 );++$i;?><tr>
                    <td>
                        <input name="" type="checkbox" value="<?php echo ($mg["mg_id"]); ?>"/>
                    </td>
                    <td><?php echo ($mg["mg_id"]); ?></td>
                    <td><?php echo ($mg["mg_name"]); ?></td>
                    <td>
                      <?php if($mg["mg_time"] == 0 ): ?>新用户<?php else: echo (date('Y-m-d H:i',$mg["mg_time"])); endif; ?>
                    </td>
                    <td>
                      <?php if($mg["role_name"] == null ): ?>空闲<?php else: echo ($mg["role_name"]); endif; ?>
                    </td>
                    <td>
                      <a href="#" class="reset">重置密码</a>
                      <a href="#" class="setRole">分配角色</a>
                    </td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
              
            </tbody>
        </table>
    </div>
    <script type="text/javascript">
        $('.tablelist tbody tr:odd').addClass('odd');
    </script>
</body>

</html>