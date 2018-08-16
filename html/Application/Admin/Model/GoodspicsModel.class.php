<?php
  //声明当前命名空间
  namespace Admin\Model;

  //引入父类
  use Think\Model;

  //创建模型类
  class GoodspicsModel extends Model{
    public function saveGoodsPics($id){
      //遍历$_FILES中的错误信息
      foreach($_FILES['pics']['error'] as $error){
        if($error !=0){
          return false;
        }
      }

      //上传文件的处理
      //TP中的上传类
      //1、实例化
      $upload = new \Think\Upload(array('rootPath'=>'./Public/Admin/upload/'));
      //2、进行移动文件操作
      $result = $upload->upload($_FILES);

      //遍历上传操作结果集
      foreach($result as $pic){
        //拼接原图的保存路径
        $this->goods_id = $id;
        $this->pics_ori = './Public/Admin/upload/'.$pic['savepath'].$pic['savename'];
        $this->pics_big = './Public/Admin/upload/'.$pic['savepath'].'big_'.$pic['savename'];
        $this->pics_mid = './Public/Admin/upload/'.$pic['savepath'].'mid_'.$pic['savename'];
        $this->pics_sma = './Public/Admin/upload/'.$pic['savepath'].'sma_'.$pic['savename'];

        //实例化图形处理类,制作不同尺寸的缩略图
        //1、实例化
        $image = new \Think\Image;
        //2、打开一个图片
        $image->open($this->pics_ori);
        //3、制作缩略图 thumb
        $image->thumb(800,800);
        //4、把内存中的图像保存到磁盘中
        $image->save($this->pics_big);

        //3、制作缩略图 thumb
        $image->thumb(350,350);
        //4、把内存中的图像保存到磁盘中
        $image->save($this->pics_mid);

        //3、制作缩略图 thumb
        $image->thumb(50,50);
        //4、把内存中的图像保存到磁盘中
        $image->save($this->pics_sma);

        //数据入库
        if(!$this->add()){
          return false;
        }
      }

      return true;
    }
  }
