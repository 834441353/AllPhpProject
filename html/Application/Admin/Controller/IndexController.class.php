<?php
namespace Admin\Controller;
use Think\Controller;
class IndexController extends CommonController {
    public function index(){
      //后台首页方法
      $this->display();
    }

    public function left(){
      //left主要负责显示功能菜单
      //根据当前登录者的权限显示对应的菜单
      //根据ids查auth表
      $authM = M('auth');
      if(session('role_id')!=1){
        //非超管,查数据库权限
        //实例化父类模型
        $roleM = M('role');

        //根据session中的角色id查询权限信息
        $ids =  $roleM->field('role_auth_ids')->where('role_id = '.session('role_id'))->select()[0];
        $result = $authM->where("auth_id in ({$ids['role_auth_ids']}) && is_menu = '1' ")->select();
      }else{
        //超级管理员,默认拥有全部权限
        $result = $authM->where("is_menu = '1'")->select();
      }
      //分配模板变量
      $this->assign('auths',$result);
      //显示模板
      $this->display();
    }
}
