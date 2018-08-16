<?php
  //声明当前命名空间
  namespace Admin\Model;

  //引入父类
  use Think\Model;

  //创建模型类
  class AuthModel extends Model{
    //字段映射
    protected $_map = array(
      // 表单属性名   字段名
      'name'=>'auth_name',
      'pid'=>'auth_pid',
      'controller'=>'auth_c',
      'action'=>'auth_a',
      'menu'=>'is_menu'
    );

    //验证规则
    protected $_validate = array(
      //权限名称必须
      array('auth_name','require','权限名不能为空'),
      //权限名称唯一
      array('auth_name','','权限名已存在',0,'unique'),
      //父级id必须为数字
      array('auth_pid','number','非法操作!'),
      //is_menu必须
      array('is_menu','require','非法操作!'),
      //可以没有控制器或方法名
    );
  }
