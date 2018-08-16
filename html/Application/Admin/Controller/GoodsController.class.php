<?php
namespace Admin\Controller;
use Think\Controller;
class GoodsController extends CommonController {
  public function showList(){
    //接收关键字信息
    $keyword = I('get.keyword');

    //实例化模型
    $m = M('Goods');
    //查询商品表,分配给模板
    if(!empty($keyword)){
      $m->where("goods_name like '%{$keyword}%'");
    }

    //1、count 统计记录数 只要执行了任何的查询或者统计函数 那么之前的sql语句条件就会被清空
    $count = $m->count();
    //2、new 实例化分页类 要求参数 总记录数,每页显示条数
    $page = new \Think\Page($count,3);
    //3、show TP分页类经过运算后,会得出页码html文本
    //4、生成页码之前,设置页码显示文字
    $page->setConfig('prev','');
    $page->setConfig('next','');
    //把第1页的页码文字改成首页
    $page->setConfig('first','首页');
    $page->lastSuffix = false;
    $page->setConfig('last','末页');
    $show = $page->show();

    //把页码分配到模板变量中
    $this->assign('pages',$show);

    //查询商品表,分配给模板
    if(!empty($keyword)){
      $m->where("goods_name like '%{$keyword}%'");
    }

    //SQL
    // $sql = "select g.*,t.type_name from sp_goods as g left join sp_type as t on g.type_id = t.type_id;";
    //field 负责 字段列表 select之后 from之前的sql语句
    //table 负责 数据源 from之后 where之前的都可以放在table里
    //join alias 完全可以用table替代

    //根据页码信息,查询数据库
    $result = $m->field('g.*,t.type_name')
    ->table('sp_goods as g left join sp_type as t on g.type_id = t.type_id')
    ->limit($page->firstRow,3)->select();
    //要进行分页处理
    $this->assign('goods',$result);
    //分配总记录数
    $this->assign('count',$count);
    $this->display();
  }

  public function add(){
    if(IS_POST){
      //实例化模型
      $m = D('Goods');
      if($m->create()){
        //验证成功,准备入库
        //数据处理]
        $m->add_time = time();
        $m->upd_time = time();
        //富文本编辑器内容,专门用$_POST接收
        // dump($_POST['desc']);
        $m->goods_introduce = filterXSS($_POST['desc']);

        //上传图片的处理过程 比较复杂的数据操作,应该写在模型中
        // $m->goods_big_logo = '图片访问路径';
        // $m->goods_small_logo = '图片访问路径';
        if(!empty($_FILES['logo'])){
          $result = $m->saveGoodsLogo();
          if(!$result){
            //图形上传失败,希望用户重新添加商品
            $this->error('添加失败,请稍后再试!');
          }
        }

        //必须在商品基本信息进入goods表,产生goods_id后,才能根据这个id,把专有属性数据,写入goodsattr表
        $goods_id = $m->add();//返回自增长id
        if($goods_id){
          //说明商品基本信息已经入库完毕,写入商品属性信息,写入到goodsattr表
          //调用自定义模型中 保存商品属性方法
          $result = $m->saveGoodsAttr($goods_id);
          if($result){
            $this->success('添加成功',U('showlist'));
          }else{
            $this->error('添加失败,请稍后再试!');
          }
        }
      }else{
        $this->error($m->getError());
      }

      //原始的post提交数据的处理
      // dump($_POST);exit;
      //实例化模型
      // $m=M('goods');
      // $m->goods_name=$_POST['name'];
      // $m->goods_price=$_POST['price'];
      // $m->goods_number=$_POST['amount'];
      // $m->goods_weight=$_POST['weight'];
      // $m->is_del=$_POST['show'];
      // $m->goods_introduce=$_POST['desc'];
      // $m->add_time = time();
      // $m->upd_time = time();
      // $m->add();
    }else{
      //查询可用的商品分类
      $result = M('type')->select();
      $this->assign('types',$result);

      //
      $this->display();
    }
  }

  public function photos(){
    if(IS_POST){
      //实例化模型
      $m = D('goodspics');
      $goods_id = I('get.id');

      //判断用户是否没有上传图片
      if(empty($_FILES['pics'])){
        $this->error('没有上传图片');
      }else{
        if($m->saveGoodsPics($goods_id)){
          $this->success('上传成功');
        }else{
          $this->error('上传失败,请稍后再试!');
        }
      }
      //生成原图大图中图小图四种字段
    }else{
      //查询已有相册图片,分配给模板变量
      $id = I('get.id');
      $result = M('goodspics')->field("pics_id,pics_mid")->where("goods_id = '{$id}'")->select();
      // dump($result);exit;
      $this->assign('pics',$result);
      $this->display();
    }
  }

  public function deletePic(){
    $id = I('get.id');

    //删除数据库记录之前,要把磁盘上的图片全部清除
    //如果磁盘文件删除失败,将来还可以再删
    //如果数据库记录先删了,那么将来就不知道磁盘文件在哪里了
    $result = M('goodspics')->where("pics_id='{$id}'")->find();

    $flag = array();
    $flag[] = unlink($result['pics_ori']);
    $flag[] = unlink($result['pics_big']);
    $flag[] = unlink($result['pics_mid']);
    $flag[] = unlink($result['pics_sma']);

    if(in_array(false,$flag)){
      echo false;
      exit;
    }

    //实例化模型
    $m = M('goodspics');

    $m->pics_id=$id;
    $result = $m->delete();
    if($result===false){
      //删除成功
      echo false;
    }else{
      echo true;
    }
  }

  public function getAttr(){
    //根据分类id查询属性
    $id = I('get.id');
    //查询attr表中,type_id == $id 的属性
    $result = M('attribute')->where("type_id = '{$id}'")->select();
    echo json_encode($result);
  }
}
