<?php
namespace Admin\Controller;
use Think\Controller;
class AuthController extends CommonController {
  public function showList(){
    //实例化模型,查询所有权限数据
    $result = M('auth')->select();
    //根据层级关系,进行无限极排序
    // dump(authLevel($result));exit;

    $this->assign('auths',authLevel($result));
    $this->display();
  }

  public function add(){
    if(IS_POST){
      //实例化模型
      $m = D('auth');
      if($m->create()){
        //数据入库
        if($m->add()){
          $this->success('添加成功',U('showList'));
        }else{
          $this->error('添加失败,请联系管理员!');
        }
      }else{
        //验证报错
        $this->error($m->getError());
      }
    }else{
      //在权限表中,查询所有顶级权限,准备作为父级候选
      $result = M('auth')->field('auth_name,auth_id')->where('auth_pid=0')->select();
      $this->assign('auths',$result);
      $this->display();
    }
  }

  public function edit(){
    if(IS_POST){
      //接收表单数据
      $post = I('post.');

      //验证权限名是否与其他权限重复
      if(M('auth')->where("auth_name = '{$post[name]}' && auth_id !={$post[id]}")->find()){
        $this->error('权限名重复');exit;
      }


      //验证该权限下是否有二级权限,如果有,禁止修改pid
      //查询新pid是否与旧pid不一样
      $result = M('auth')->field("count(*) as num")->where("auth_id = '{$post[id]}' && auth_pid = '{$post[pid]}'")->find();
      if(!($result['num']*1)){
        //修改pid
        // 验证该权限下是否有二级权限
        $result = M('auth')->field("count(*) as num")->where("auth_pid = '{$post[id]}'")->select();
        if($result[0]['num']*1){
          //有孩子,不能改pid
          $this->error('当前权限含有二级权限!');
        }
      }
      //查自身有没有二级权限


      //实例化模型
      $m = M('auth');
      //AR模式
      $m->auth_id = $post['id'];
      $m->auth_name = $post['name'];
      $m->is_menu = $post['menu'];
      $m->auth_c = $post['controller'];
      $m->auth_a = $post['action'];
      $m->auth_pid = $post['pid'];

      //1、受影响行数为1  2、受影响行数为0 3、返回false
      $result = $m->save();
      if($result){
        $this->success('修改成功',U('showList'));
      }elseif($result===false){
        $this->error('修改失败');
      }else{
        $this->error('数据未改变');
      }
    }else{
      //根据要修改的权限id,查询权限表,取得这条权限原有的数据
      //接收id
      $id = I('get.id');
      $result = M('auth')->where("auth_id = {$id}")->find();
      $this->assign('auth',$result);

      //在权限表中,查询所有顶级权限,准备作为父级候选,除了自己
      $result = M('auth')->field('auth_name,auth_id')->where("auth_pid=0 && auth_id != {$id}")->select();
      $this->assign('tops',$result);

      $this->display();
    }
  }

  public function delete(){
    //接收get传送的id,删除数据库记录
    $id = I('get.id');
    //判断要删除的这一条id,是否具有二级权限
    $result = M('auth')->field('count(*) as num')->where("auth_pid = '{$id}'")->select();

    if($result[0]['num']){
      echo '该权限有二级权限!';
      exit;
    }

    //实例化模型
    $m = M('auth');
    $m->auth_id = $id;

    //delete有三种 返回删除了1条  返回删除了0条  返回false
    $result = $m->delete();
    if($result){
      echo '删除成功';
    }elseif($result===false){
      echo '删除失败,请联系管理员!';
    }else{
      echo '权限不存在';
    }
  }
}
