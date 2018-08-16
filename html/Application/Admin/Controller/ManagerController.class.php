<?php
namespace Admin\Controller;
use Think\Controller;
class ManagerController extends CommonController {
  public function setPwd(){
    if(IS_POST){
      $oldPwd = I('post.oldPwd');
      $newPwd = I('post.newPwd');

      //先验证旧密码
      $result = A('public')->verify(session('mg_name'),$oldPwd);

      if($result){
        //将新密码加密后更新到对应字段
        $m = M('manager');
        $m->mg_id = session('mg_id');
        $m->mg_pwd = getPwd($newPwd);
        $result = $m->save();

        if($result){
          $this->success('修改成功',U('index/main'));
        }else{
          $this->error('修改失败,请联系管理员');
        }
      }else{
        $this->error('旧密码不正确');
      }
    }else{
      $this->display();
    }
  }
  public function showList(){
    //查询管理员数据
    $m = M();

    $sql = 'select m.*,r.role_name from sp_manager as m left join sp_role as r on m.role_id = r.role_id';

    $result = $m->query($sql);

    $this->assign('data',$result);

    $this->display();
  }

  public function add(){
    if(IS_POST){
      //实例化模型
      $m = D('manager');
      if($m->create()){
        //数据处理与入库
        //密码加密
        $m->mg_pwd = getPwd($m->mg_pwd);

        //入库
        if($m->add()){
          $this->success('添加成功',U('showlist'));
        }else{
          $this->error('添加失败,请联系管理员!');
        }
      }else{
        $this->error($m->getError());
      }
    }else{
      //查询可用的角色信息,显示对应的角色选项
      $result = M('role')->field('role_id,role_name')->select();
      $this->assign('roles',$result);
      $this->display();
    }
  }

  public function resetPwd(){
    //接收用户的id
    $id = I('get.id');

    //生成一个随机 8位 密码 大小写英文字母+数字 65~90 97~122
    $pwd = '';
    for($i=0;$i<8;$i++){
      switch (mt_rand(0,2)) {
        case 1:
          $pwd.=chr(mt_rand(65,90));
          break;
        case 2:
          $pwd.=chr(mt_rand(97,122));
          break;
        default:
          $pwd .= mt_rand(0,9);
          break;
      }
    }
    //密码加密
    //尝试入库
    $m = M('manager');
    $m->mg_id = $id;
    $m->mg_pwd = getPwd($pwd);
    //入库成功的情况下,才发送密码,否则返回重置失败请稍后再试
    if($m->save()){
      //保存成功
      echo $pwd;
    }else{
      echo '重置失败,请稍后再试!';
    }
  }

  public function setRole(){
    if(IS_POST){
      //接收
      //实例化模型
      $m = M('manager');
      $m->mg_id = I('post.id');
      $m->role_id = I('post.role');

      //入库更新 修改1条 修改0条 修改失败
      $result = $m->save();
      if($result){
        $this->success('分配成功',U('showlist'));
      }elseif($result===false){
        $this->error('分配失败,请联系管理员!');
      }else{
        $this->error('角色未改变');
      }
    }else{
      //接收用户id
      $id = I('get.id');
      //读取当前修改的用户名称
      //读取当前修改的用户role_id
      $result =  M('manager')->field('mg_name,role_id')->where("mg_id = '{$id}'")->find();

      $this->assign("user",$result);
      //读取可用的角色 制作option
      $roles = M('role')->field('role_name,role_id')->select();
      $this->assign("roles",$roles);
      $this->display();
    }
  }

  public function delete(){
    //接收要删除的id 无论是一个还是多个
    $id = I('get.id');
    //实例化模型
    $m = M('manager');
    $m->mg_id = $id;
    //删除了几条 删除了0条 返回false
    $result = $m->delete();
    if($result){
      echo '删除成功';
    }elseif($result===false){
      echo '删除失败,请稍后再试!';
    }else{
      echo '用户不存在';
    }
  }

}
