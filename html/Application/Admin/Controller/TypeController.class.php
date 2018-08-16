<?php
namespace Admin\Controller;
class TypeController extends CommonController {
  public function showList(){
    //查询分类记录,模板变量分配
    $result = M('type')->select();
    $this->assign('types',$result);
    $this->display();
  }
  public function add(){
    if(IS_POST){
      //实例化自定义模型
      $m = D('type');
      if($m->create()){
        if($m->add()){
          $this->success('添加成功',U('showlist'));
        }else{
          $this->error('添加失败,请联系管理员!');
        }
      }else{
        $this->error($m->getError());
      }
    }else{
      $this->display();
    }
  }
}
