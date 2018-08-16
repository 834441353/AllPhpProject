<?php
namespace Admin\Controller;
use Think\Controller;
class PublicController extends Controller {
    public function verify($username='',$password=''){
      //1、项目代码中手动调用
      //2、前端ajax请求 echo

      //接收post请求
      $username = empty($username)? I('post.username') : $username;
      $password = empty($password)? getPwd(I('post.password')) : getPwd($password);

      //根据username和password查询数据库,返回记录数
      $result = M('manager')->where("mg_name = '{$username}' && mg_pwd = '{$password}'")->find();

      if(IS_AJAX){
        if($result){
          echo true;
        }else{
          echo false;
        }
      }else{
        return $result;
      }
    }
    public function login(){
      if(IS_POST){
        $result = $this->verify();
        //判断查询结果
        if($result){
          //先读取上一次的登录时间
          session('mg_time',$result['mg_time']);
          session('mg_id',$result['mg_id']);
          session('mg_name',$result['mg_name']);
          session('role_id',$result['role_id']);
          //为了控制功能入口的显示,在session中保存当前用户的权限
          $result = M('role')->field('role_auth_ids')->where("role_id =".session('role_id'))->find()['role_auth_ids'];
          session('role_auth_ids',$result);
          $m = M('manager');
          //更新数据库的登录时间字段
          $m->mg_time = time();
          $m->mg_id = session('mg_id');
          //AR模式更新
          $m->save();
          //登录成功
          $this->success('欢迎登录',U('index/index'));
        }else{
          //登录失败
          $this->error('用户名或密码有误');
        }
      }else{
        //登录页面方法
        $this->display();
      }
    }
}
