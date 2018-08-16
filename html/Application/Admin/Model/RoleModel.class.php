<?php
  //声明当前命名空间
  namespace Admin\Model;

  //引入父类
  use Think\Model;

  //创建模型类
  class RoleModel extends Model{
    //设置角色信息表单的字段映射
    protected $_map = array(
      'name' => 'role_name',
      'ids' => 'role_auth_ids'
    );

    //设置角色信息表单的验证规则
    protected $_validate = array(
      //角色名不能为空
      array('role_name','require','角色名不能为空'),
      //角色名唯一
      array('role_name','','角色名已存在',0,'unique'),
      //权限不能为空
      // array('role_auth_ids','require','权限不能为空'),
      array('role_auth_ids','is_array','权限不能为空',1,'function'),
    );
  }
