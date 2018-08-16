<?php

    //Common 一般的 常规的 普通的
    //Common sense 常识

    namespace Admin\Controller;

    use Think\Controller;

    class CommonController extends Controller{
      //登录验证
      //权限验证
      //只要实例化某个构造器就要检查的工作 应该在构造方法中做
      public function _initialize(){
        //登录验证
        if(!session('mg_id')){
          //跳转到登录页面
          $this->redirect('admin/public/login');
        }

        //权限防火墙 超管直接翻墙
        if(session('role_id')!=1){
          //权限验证 判断 当前用户是由具有请求控制器和方法的权限
          //用户请求了什么控制器?
          // echo CONTROLLER_NAME;
          // echo ACTION_NAME;
          //获取当前请求的控制器和方法
          $currentAC = strtolower(CONTROLLER_NAME.'-'.ACTION_NAME);

          //根据当前用户的角色信息,取得他有什么权限
          $result = M('role')->field('role_auth_ac')->where("role_id =".session('role_id'))->find();
          //格式化权限数据 全部小写 添加一些默认主页权限
          $ac = strtolower($result['role_auth_ac']).',index-index,index-main,index-top,index-left,manager-setpwd';
          //把权限数据转换成数组,便于 in_array判断
          $ac = explode(',',$ac);
          //判断用户请求的权限是否在他角色允许范围内
          if(!in_array($currentAC,$ac)){
            // $this->redirect('index/index');
            if(IS_AJAX){
              echo '尊贵的用户,您没有权限';
              exit;
            }else{
              echo "<script>top.location='http://www.eshop.com/Admin'</script>";
              exit;
            }
          }
        }
      }
    }
