<?php
namespace Admin\Controller;
use Think\Controller;
class RoleController extends CommonController {
  public function showList(){
    //查询角色数据
    $m = M('role');

    $result = $m->select();

    $this->assign('data',$result);

    $this->display();
  }

  public function add(){
    if(IS_POST){
      //用create方法接收数据
      $m = D('role');
      if($m->create()){
        //数据处理
        $m->role_auth_ids = implode(',',$m->role_auth_ids);
        //根据当前角色拥有的权限,查出auth_c 和 auth_a这两个字段
        $result = M('auth')->field('auth_c,auth_a')->where("auth_id in ({$m->role_auth_ids}) && auth_pid != 0")->select();
        $m->role_auth_ac = '';
        //遍历控制器和方法名,拼接出权限文本
        foreach($result as $auth){
          $m->role_auth_ac .= $auth['auth_c'].'-'.$auth['auth_a'].',';
        }

        $m->role_auth_ac = rtrim($m->role_auth_ac,',');

        if($m->add()){
          $this->success('添加成功',U('showlist'));
        }else{
          $this->error('添加失败,请联系管理员!');
        }
      }else{
        //验证报错
        $this->error($m->getError());
      }

    }else{
      //查询权限数据
      $m = M('auth');

      $result = $m->field('auth_id,auth_name,auth_pid')->select();
      $this->assign('auths',$result);
      //显示模板
      $this->display();
    }
  }

  public function setAuth(){
    if(IS_POST){
      $ids = implode(',',I('post.ids'));
      //根据ids查权限表中的auth_c和auth_a
      $result = M('auth')->field('auth_c,auth_a')->where("auth_id in ({$ids}) && auth_pid != 0")->select();

      //接收数据
      $m = M('role');
      $m->role_auth_ac = '';
      //遍历控制器和方法名,拼接出权限文本
      foreach($result as $auth){
        $m->role_auth_ac .= $auth['auth_c'].'-'.$auth['auth_a'].',';
      }

      $m->role_auth_ac = rtrim($m->role_auth_ac,',');
      $m->role_id = I('post.id');
      //把接收到的ids数组,转换成字符串
      $m->role_auth_ids = $ids;

      //修改成功 数据没有改变 false
      $result = $m->save();

      if($result){
        $this->success('分配成功',U('showlist'));
      }elseif($result===false){
        $this->error('修改失败,请联系管理员!');
      }else{
        $this->error('权限未改变');
      }
    }else{
      //查询权限数据
      $m = M('auth');
      //获取所有权限选项
      $result = $m->field('auth_id,auth_name,auth_pid')->select();
      $this->assign('auths',$result);

      //获取当前角色已有的权限
      $id = I('get.id');
      $result = M('role')->field('role_auth_ids')->where("role_id = '{$id}'")->find()["role_auth_ids"];

      $this->assign('hasId',$result);
      //显示模板
      $this->display();
    }
  }

  public function delete(){
    //接收数据
    $id = I('get.id');

    //AR模式
    $m = M('manager');
    $m->role_id = 0;
    $result = $m->where("role_id ='{$id}'")->save();
    if($result){
      //重置用户成功
      //删除角色记录
      $m = M('role');
      $m->role_id = $id;
      $result=$m->delete();
      //删除1条 删除0条 false
      if($result){
        echo '删除成功';
      }elseif($result===false){
        echo '删除失败,请联系管理员!';
      }else{
        echo '角色不存在';
      }
    }else{
      echo '删除失败,请稍后再试!';
    }
    //如果用户重置成功了,才允许删除角色
  }
}
