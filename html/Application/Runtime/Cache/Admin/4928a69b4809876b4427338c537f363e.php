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

        $("#add").click(function(){
          window.location = "<?php echo U('add');?>";
        })

        $(".delete").click(function(){
          var id = $(this).find('input').eq(0).val();
          var name = $(this).find('input').eq(1).val();
          //提醒用户,你将删除...权限,请输入密码
          var pwd = window.prompt('您将删除:　'+name+'\r\n请输入密码');
          //发起ajax请求,验证密码 用户名和密码
          $.post("<?php echo U('Public/verify');?>",'username=<?php echo (session('mg_name')); ?>&password='+pwd,function(msg){
            if(msg){
              //允许删除
              var confirm = window.confirm('请确认删除:　['+name+']　权限!');
              if(confirm){
                //ajax删除
                //向服务器发起请求,传递要删除的权限id
                $.get("<?php echo U('Auth/delete');?>",'id='+id,function(msg){
                  alert(msg);
                  //刷新页面
                  window.location="<?php echo U('showlist');?>";
                },'text');
              }else{
                alert('下次想好再来!');
              }
            }else{
              alert('密码错误');
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
                <li><span><img src="/Public/Admin/images/t03.png" /></span>删除</li>
                <li><span><img src="/Public/Admin/images/t04.png" /></span>统计</li>
            </ul>
        </div>
        <table class="tablelist">
            <thead>
                <tr>
                    <th>
                        <input name="" type="checkbox" value="" id="checkAll" />
                    </th>
                    <th>编号</th>
                    <th>权限名称</th>
                    <th>父级id</th>
                    <th>菜单导航</th>
                    <th>控制器</th>
                    <th>方法</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
              
              <?php if(is_array($auths)): $i = 0; $__LIST__ = $auths;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$auth): $mod = ($i % 2 );++$i;?><tr class="auth pid<?php echo ($auth["auth_pid"]); ?>" data="<?php echo ($auth["auth_id"]); ?>">
                    <td>
                        <input name="" type="checkbox" value="<?php echo ($auth["auth_id"]); ?>" />
                    </td>
                    <td><?php echo ($auth["auth_id"]); ?></td>
                    <td><?php echo (str_repeat('&emsp;',$auth["level"]*2)); echo ($auth["auth_name"]); ?></td>
                    <td><?php echo ($auth["auth_pid"]); ?></td>
                    <td><?php if($auth["is_menu"] == '1'): ?>显示<?php else: ?>不显示<?php endif; ?></td>
                    <td><?php echo ($auth["auth_c"]); ?></td>
                    <td><?php echo ($auth["auth_a"]); ?></td>
                    <td><a href="/index.php/Admin/Auth/edit/id/<?php echo ($auth["auth_id"]); ?>" class="tablelink">修改</a>
                      
                      <a class="delete" href="#" class="tablelink">
                        <input type="hidden" value="<?php echo ($auth["auth_id"]); ?>"><input type="hidden" value="<?php echo ($auth["auth_name"]); ?>">删除
                      </a>
                    </td>
                </tr><?php endforeach; endif; else: echo "" ;endif; ?>
              
            </tbody>
        </table>
        <div class="tip">
            <div class="tiptop"><span>提示信息</span>
                <a></a>
            </div>
            <div class="tipinfo">
                <span><img src="/Public/Admin/images/ticon.png" /></span>
                <div class="tipright">
                    <p>是否确认对信息的修改 ？</p>
                    <cite>如果是请点击确定按钮 ，否则请点取消。</cite>
                </div>
            </div>
            <div class="tipbtn">
                <input name="" type="button" class="sure" value="确定" />&nbsp;
                <input name="" type="button" class="cancel" value="取消" />
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $('.tablelist tbody tr:odd').addClass('odd');
        $(function(){
          $(".pid0").click(function(){
            //根据自身id,把pid = 自身id的tr全部 hide
            var id = $(this).attr('data');
            $("tr.pid"+id).toggle();
          }).css('backgroundColor','#CCCCCC');
        })
    </script>
</body>

</html>