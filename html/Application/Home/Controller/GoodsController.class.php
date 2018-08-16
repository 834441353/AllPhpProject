<?php
namespace Home\Controller;
use Think\Controller;
class GoodsController extends CommonController {
  public function showList(){
    //显示商品数据前,查询可显示的商品记录
    $result = M('goods')->where("is_del = '1'")->select();
    //分配模板变量
    $this->assign('goods',$result);
    $this->display();
  }

  public function detail(){
    //商品详情方法 根据商品id查数据
    //接收商品id
    $id = I('get.id');

    //商品基本信息、商品介绍
    $result = M('goods')->where("goods_id = '{$id}'")->find();
    $this->assign('info',$result);

    //商品可选属性
    $result = M()->query("select ga.attr_value,ga.attr_id,a.attr_name,a.attr_sel from sp_goodsattr as ga left join sp_attribute as a on ga.attr_id = a.attr_id where goods_id = '{$id}';");
    //遍历$result,将可选属性的属性值,以,号炸开转换成数组
    foreach($result as $k=>$v){
      //判断当前属性是否为可选属性
      if($v['attr_sel']=='1'){
        $result[$k]['attr_value'] = explode(',',$result[$k]['attr_value']);
      }
    }
    $this->assign('attrs',$result);

    //商品相册信息
    $result = M()->query("select pics_big,pics_mid,pics_sma from sp_goodspics where goods_id = '{$id}';");
    $this->assign('pics',$result);
    // dump($result);exit;
    $this->display();
  }
}
