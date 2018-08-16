<?php

  //后台可用的函数库

  //密码加密函数 getPwd
  //@param1 str pwd 原始密码
  function getPwd($pwd){
    //先md5加密 加盐
    $pwd = md5($pwd).'eshop';
    //后sha1加密
    $pwd = sha1($pwd);
    return $pwd;
  }

  //封装无限极分类函数 对权限进行排序authLevel
    //输入一个权限记录数组 返回一个排序好的权限记录数组
    // @param1 $auths arr 未排序的权限记录
    // @param2 $pid   int 寻找子元素的权限id
    // @param3 $level int 说明当前递归的层级
  function authLevel($auths,$pid=0,$level=0){
    static $result = [];
    //遍历数据,进行排序
    foreach($auths as $auth){
      //如果正在遍历的权限的pid等于
      if($auth['auth_pid']==$pid){
        $auth['level'] = $level;
        $result[] = $auth;
        //根据这个元素,查找有没有二级权限
        authLevel($auths,$auth['auth_id'],$level+1);
      }
    }
    return $result;
  }

  //过滤XSS 能够检查传入$string 把其中的script标签过滤掉 然后返回一个过滤后的字符串
  function filterXSS($string){
      //相对index.php入口文件，引入HTMLPurifier.auto.php核心文件
      require_once './Public/Admin/htmlpurifier/library/HTMLPurifier.auto.php';
      // 生成配置对象
      $cfg = HTMLPurifier_Config::createDefault();
      // 以下就是配置：
      $cfg -> set('Core.Encoding', 'UTF-8');
      // 设置允许使用的HTML标签  不允许出现script
      $cfg -> set('HTML.Allowed','div,b,strong,i,em,a[href|title],ul,ol,li,br,p[style],span[style],img[width|height|alt|src]');
      // 设置允许出现的CSS样式属性
      $cfg -> set('CSS.AllowedProperties', 'font,font-size,font-weight,font-style,font-family,text-decoration,padding-left,color,background-color,text-align');
      // 设置a标签上是否允许使用target="_blank"
      $cfg -> set('HTML.TargetBlank', TRUE);
      // 使用配置生成过滤用的对象
      $obj = new HTMLPurifier($cfg);
      // 过滤字符串
      return $obj -> purify($string);
  }
