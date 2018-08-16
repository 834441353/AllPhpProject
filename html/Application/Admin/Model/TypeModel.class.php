<?php
  //声明当前命名空间
  namespace Admin\Model;

  //引入父类
  use Think\Model;

  //创建模型类
  class TypeModel extends Model{
    protected $_map=array(
      'name' => 'type_name'
    );

    protected $_validate = array(
      //分类名必须
      array('type_name','require','分类名不能为空!'),
      //分类名唯一
      array('type_name','','分类名已存在!',0,'unique'),
    );
  }
