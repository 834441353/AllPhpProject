<?php
  //声明当前命名空间
  namespace Admin\Model;

  //引入父类
  use Think\Model;

  //创建模型类
  class AttrModel extends Model{
    protected $_map=array(
      'name'=>'attr_name',
      'type'=>'type_id',
      'sel'=>'attr_sel',
      'write'=>'attr_write',
      'vals'=>'attr_vals'
    );

    protected $_validate=array(
      //属性名必须
      array('attr_name','require','属性名不能为空!'),
      //属性名可以唯一!!! 手机、平板、衣服等都可以用颜色
      //分类id为数字
      array('type_id','number','非法操作!'),
      //写入方式为数字
      array('attr_write','number','非法操作!'),
      //vals没有明确要求
    );

    //表映射
    protected $tableName = 'attribute';
  }
