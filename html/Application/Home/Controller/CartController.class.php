<?php
namespace Home\Controller;
use Think\Controller;
use Tools\Cart;
class CartController extends CommonController {

  public function check(){
    // dump($_GET);
    //修改订单的支付状态
    //接收订单号
    $order_number = I('get.out_trade_no');

    //修改数据库订单支付状态
    $m = M('order');
    $m->order_id = $m->field('order_id')->where("order_number = '{$order_number}'")->find()['order_id'];
    $m->order_status = '1';

    //以下操作仅供参考   一定要写事务来做
    if($m->save()){
      //跳转到flow3
      $this->redirect('cart/flow3');
    }
  }
  //支付方法 订单号
  public function pay($order_number){
      header("Content-type:text/html;charset=utf-8");
    //查询订单表,取出这个订单的价格
    $order_price = M('order')->field('order_price')->where("order_number = '{$order_number}'")->find()['order_price'];
    //查询订单库中,对应的订单信息
    // 表单提交方式 post
    // 表单提交地址 alipayapi.php
    // 1、 WIDout_trade_no 订单号 要求不能重复
    // 2、 WIDsubject 款项名称
    // 3、 WIDtotal_fee 支付金额
    // 4、 WIDbody 备注信息
    // dump($order_number);
    // dump($order_price);exit;

    //让用户在访问pay方法时,浏览器能发起post请求
    //在网页中响应一个post表单,并且让表单自动提交
    $form = "
      <form id='pay' style='display:none' action='/Application/Tools/alipay/alipayapi.php' method='post'>
        <input name='WIDout_trade_no' value='{$order_number}'/>
        <input name='WIDsubject' value='支付宝接口测试'/>
        <input name='WIDtotal_fee' value='{$order_price}'/>
        <input name='WIDbody' value='黑马程序员'/>
      </form>
      <script>
        document.getElementById('pay').submit();
      </script>
    ";
    echo $form;
  }
  public function _empty(){
    //如果请求了购物车类中的不存在的方法,默认跳转到flow1
    $this->redirect('flow1');
  }
  public function modify(){
    //接收要删除的商品id 和数量
    $id = I('get.id');
    $amount = I('get.amount');
    //实例化购物车类
    $cart = new Cart();
    //调用del方法
    $cart->changeNumber($amount,$id);
  }
  public function delete(){
    //接收要删除的商品id
    $id = I('get.id');
    //实例化购物车类
    $cart = new Cart();
    //调用del方法
    $cart->del($id);
  }
  public function flow1(){
    //获取购物车中的所有数据
    //1、实例化购物车类
    $cart = new Cart();
    //2、获取所有的购物车信息
    $info = $cart->cartInfo;
    //实例化模型对象
    //遍历购物车中的商品,查询商品的小图标访问路径  有多少个商品,就查询了多少次数据库
    // foreach($info as $k=>$v){
    //   $info[$k]['goods_small_logo'] = $m->field('goods_small_logo')->where("goods_id = '{$k}'")->find()['goods_small_logo'];
    // }

    //1、获取$info数组中所有的id
    //2、把包含了索引的数组,转换成where 条件 1,2,3
    //如果购物车为空,显示提示信息,请先购物
    if(!$info){
      $this->error('请先买点东西吧~',U('index/index'));
      exit;
    }

    $ids = implode(',',array_keys($info));
    $result = M('goods')->field('goods_id,goods_small_logo')->where("goods_id in ({$ids})")->select();

    //3、遍历数组,进行数组融合 遍历 foreach($arr as $k => $v)
    //会以$arr创建$v副本 把$arr全部复制拷贝一份 在内存中创建副本
    //如果要对数组进行真实操作 $arr[$k]
    //对$v进行操作 只是对副本的操作 不会影响真实的数组
    foreach($result as $v){
      $info[$v['goods_id']]['goods_small_logo'] = $v['goods_small_logo'];
    }

    $this->assign('infos',$info);
    $this->display();
  }

  public function flow2(){
    if(IS_POST){

      //订单基本信息入库
      $order=array();
      //订单号的规则 PHP7 时间戳
      $order['order_number'] = 'php7'.time();
      $order['user_id'] = session('uid');
      $order['order_pay'] = I('post.pay');
      $order['cgn_id'] = I('post.address');
      $order['add_time'] = time();
      $order['upd_time'] = time();
      //模拟所有订单价格0.01
      $order['order_price'] = 0.01;

      //实例化模型
      $m = M('order');
      $order_id = $m->add($order);

      if($order_id>0){
        //这个订单对应哪些商品
        $order_goods = array();
        //查当前购物车中有哪些商品
        $cart = new Cart();
        $goods = $cart->cartInfo;

        //遍历购物车商品,组装入库的数组
        foreach($goods as $k => $v){
          $data = array();
          $data['goods_id'] = $v['goods_id'];
          $data['goods_price'] = $v['goods_price'];
          $data['goods_number'] = $v['goods_buy_number'];
          $data['goods_total_price'] = $v['goods_total_price'];
          $data['order_id'] = $order_id;

          $order_goods[] = $data;
        }
        // dump($order_goods);exit;
        //把修改过的$goods数组写入数据库
        if(M('order_goods')->addAll($order_goods)){
          // echo '成功';
          //购物车商品数据已经全部写入数据库,清空购物车
          $cart->delall();

          //跳转支付 调用支付方法
          $this->pay($order['order_number']);
        }
      }
    }else{
      //获取购物车中的所有数据
      //1、实例化购物车类
      $cart = new Cart();
      //2、获取所有的购物车信息
      $info = $cart->cartInfo;
      //实例化模型对象

      //如果购物车为空,显示提示信息,请先购物
      if(!$info){
        $this->error('请先买点东西吧~',U('index/index'));
        exit;
      }

      //1、获取$info数组中所有的id
      //2、把包含了索引的数组,转换成where 条件 1,2,3
      $ids = implode(',',array_keys($info));
      $result = M('goods')->field('goods_id,goods_small_logo')->where("goods_id in ({$ids})")->select();

      //3、遍历数组,进行数组融合 遍历 foreach($arr as $k => $v)
      //会以$arr创建$v副本 把$arr全部复制拷贝一份 在内存中创建副本
      //如果要对数组进行真实操作 $arr[$k]
      //对$v进行操作 只是对副本的操作 不会影响真实的数组
      foreach($result as $v){
        $info[$v['goods_id']]['goods_small_logo'] = $v['goods_small_logo'];
      }

      //分配了商品基本信息和图标路径
      $this->assign('infos',$info);

      //通过购物车对象查询商品的数量和总金额
      $total = $cart->getNumberPrice();
      $this->assign('total',$total);
      $this->display();
    }
  }

  public function flow3(){
    $this->display();
  }
  public function _initialize(){
    //验证用户是否已经登录
    if(!session('uid')){
      //让用户先登录
      //ajax调用
      //URL页面请求
      if(IS_AJAX){
        echo false;
      }else{
        $this->error('请先登录',U('user/login'));
      }
    }
  }
  public function test(){
    //1、实例化工具类对象
    $cart = new Cart();
    // $cart->delall();
    dump($cart);
    //2、调用工具类方法
    //3、查看操作的结果
  }
  public function add(){
    //接收数据
    $id = I('get.id');
    $amount = I('get.amount');
    $attrs = I('get.attrs');
    //样板数据   数据库:商品名 商品单价 post传值:购买的数量 商品id
    $result = M('goods')->field('goods_id,goods_name,goods_price')->where("goods_id = '{$id}'")->find();
    //根据用户的商品往购物车内添加商品
    $result['goods_buy_number']=$amount;
    $result['goods_total_price']=$amount*$result['goods_price'];
    $result['goods_attrs']=$attrs;
    //1、实例化购物车类
    $cart = new Cart();
    //2、调用cart类的add方法
    $cart->add($result);

    //3、查看操作结果 获取当前购物车内的商品总数和总价 用于弹窗提示
    echo json_encode($cart->getNumberPrice());
  }
}
