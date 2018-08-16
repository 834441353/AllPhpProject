<?php
namespace Admin\Controller;
class AttrController extends CommonController {
  public function showList(){
    //查询分类记录,模板变量分配
    $result = M()->query('select a.*,t.type_name from sp_attribute as a left join sp_type as t on a.type_id = t.type_id;');
    $this->assign('attrs',$result);
    $this->display();
  }

  public function add(){
    if(IS_POST){
      //实例化自定义模型
      $m = D('Attr');
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
      //查询可用的商品分类
      $result = M('type')->select();
      $this->assign('types',$result);
      $this->display();
    }
  }
}
