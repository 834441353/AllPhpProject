<?php
  //声明当前命名空间
  namespace Admin\Model;

  //引入父类
  use Think\Model;

  //创建模型类
  class GoodsModel extends Model{
    protected $_map=array(
      'name'=>'goods_name',
      'price'=>'goods_price',
      'amount'=>'goods_number',
      'weight'=>'goods_weight',
      'show'=>'is_del',
      'desc'=>'goods_introduce',
      'logo'=>'goods_big_logo',
      'type'=>'type_id'
    );

    protected $_validate=array(
      //商品名必须
      array('goods_name','require','商品名不能为空'),
      array('goods_name','','商品名已存在',0,'unique'),
      //商品价格必须
      array('goods_price','require','商品价格不能为空'),
      array('goods_price','number','商品价格必须为正数'),
      //商品数量必须为数字
      array('goods_number','number','商品库存必须为正数'),
      //重量必须为数字
      array('goods_weight','number','商品重量必须为正数'),
      //是否显示必须为数字
      array('is_del','number','非法操作!'),
      //商品图片可以不上传
      //分类id为数字
      array('type_id','number','非法操作!'),
    );

    //保存商品图片的方法
    public function saveGoodsLogo(){
      //判断图片上传是否成功
      if($_FILES['logo']['error']=='0'){
        //图片上传和处理
        //所谓的上传处理 就是 移动临时文件保存
        //1、设置上传文件的根目录
        $cfg = array('rootPath'=>'./Public/Admin/upload/');
        //2、实例化上传类对象
        $upload = new \Think\Upload($cfg);
        //3、上传单个文件
        $result = $upload->uploadOne($_FILES['logo']);
        //4、数据处理 根据上传移动文件的情况 拼接出原图的访问路径
        $this->goods_big_logo = './Public/Admin/upload/'.$result['savepath'].$result['savename'];
        $this->goods_small_logo = './Public/Admin/upload/'.$result['savepath'].'thumb_'.$result['savename'];

        //根据原图,制作出缩略图
        //TP有图形处理类 基于GD2
        //1、实例化
        $image = new \Think\Image;
        //2、打开一个图片 参数:原图路径
        $result = $image->open($this->goods_big_logo);
        //3、制作缩略图 thumb 大拇指,缩略图,点赞 参数:缩略图容器的尺寸 缩略图不改变长宽比
        $image->thumb(100,100);
        //4、把内存中的图像保存到磁盘上 参数:保存路径
        $result = $image->save($this->goods_small_logo);

        //把图片保存路径前面的点去掉 ltrim

        //把大小图的数据入库即可
        return true;
      }else{
        return false;
      }
    }

    public function saveGoodsAttr($goods_id){
      $attr = I('post.attr');
      //遍历attr数据,往goodsattr表中写入记录
      //goods_id
      $data = array();
      foreach($attr as $k=>$v){
        $data['goods_id']=$goods_id;
        $data['attr_id']=$k;
        $data['attr_value'] = rtrim(implode(',',$v),',');

        if(!M('goodsattr')->add($data)){
          return false;
        }
      }
      return true;
    }
  }
