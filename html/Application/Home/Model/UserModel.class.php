<?php

  namespace Home\Model;
  use Think\Model;

  class UserModel extends Model{
    //字段映射
    protected $_map=array(
      'username'=>'user_name',
      'password'=>'user_pwd'
    );
    //验证规则
    protected $_validate=array(
      //用户名必须
      array('user_name','require','用户名不能为空'),
      //用户名唯一
      array('user_name','','用户名已存在',0,'unique'),
      //密码必须
      array('user_pwd','require','密码不能为空'),
    );
  }
