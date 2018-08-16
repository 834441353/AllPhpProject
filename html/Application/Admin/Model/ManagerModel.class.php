<?php
  //声明当前命名空间
  namespace Admin\Model;

  //引入父类
  use Think\Model;

  //创建模型类
  class ManagerModel extends Model{
    //字段映射
    protected $_map = array(
      'username'=>'mg_name',
      'password'=>'mg_pwd',
      'role'=>'role_id'
    );
    //验证规则
    protected $_validate = array(
      //用户名不能为空
      array('mg_name','require','用户名不能为空'),
      //用户名唯一
      array('mg_name','','用户名已存在',0,'unique'),
      //密码不能为空
      array('mg_pwd','require','非法操作!'),
      //角色id必须为数字
      array('role_id','number','非法操作!'),
    );
  }
