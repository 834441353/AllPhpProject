<?php
namespace Home\Controller;
use Think\Controller;
class UserController extends CommonController {
  public function _empty(){
    //跳转到首页
    //显示404页面
  }
  public function logout(){
    session(null);
    //清空cookie
    cookie('PHPSESSID',null);
  }
  public function login(){
    if(IS_POST){
          //验证码是否正确
          if($this->validate(I('post.checkcode'))){
              //接收用户名和密码进行验证
              $username = I('post.username');
              $password = getPwd(I('post.password'));
              //查user表记录
              $result = M('user')->field('user_id,user_name')->where("user_name='{$username}' && user_pwd='{$password}'")->find();

              //判断是否存在这么一条记录
              if($result){
                  //session数据的处理
                  session('uid',$result['user_id']);
                  session('uname',$result['user_name']);
                  //登录成功后的跳转 默认去首页 返回登录前一页
                  //获取可能存在的上一页url
                  $url = empty(I('post.history')) ? U('index/index') : I('post.history');
                  $this->success('欢迎光临',$url);
              }else{
                  $this->error('用户名或密码错误!');
              }
          }else{
              $this->error('验证码错误');
          }
      }else{
          $this->display();
      }
  }
  public function register(){
    if(IS_POST){
      //服务端验证码的验证
      $code = I('post.checkcode');
      //调用当前控制器中的validate方法
      if($this->validate($code)){
        //验证码正确,正式开始注册功能
        $m = D('user');
        if($m->create()){
          //数据准备
          $m->add_time = time();
          $m->user_pwd = getPwd($m->user_pwd);

          if($m->add()){
            $this->success('注册成功',U('user/login'));
          }else{
            $this->error('注册失败,请稍后再试!');
          }
        }else{
          $this->error($m->getError());
        }
      }else{
        $this->error('验证码错误');
      }
    }else{
      $this->display();
    }
  }

  //制作验证码的方法
  public function captcha(){
    //该方法的响应类型是图片
    //在TP中有专门的验证码类
    //1、设置配置项:
    $cfg = array(
      'seKey'     =>  'szphp',   // 验证码加密密钥
      'fontSize'  =>  25,              // 验证码字体大小(px)
      'useCurve'  =>  true,            // 是否画混淆曲线
      'useNoise'  =>  true,            // 是否添加杂点
      'length'    =>  4,               // 验证码位数
      'fontttf'   =>  '4.ttf',              // 验证码字体，不设置随机获取
      'bg'        =>  array(243, 251, 254),  // 背景颜色
    );

    //2、实例化验证码类
    $verify = new \Think\Verify($cfg);

    //3、制作验证码
    $verify->entry();
  }

  //ajax验证 图片不会立刻刷新
  public function check(){
    //获取ajax get传值的验证码
    $code = I('get.code');
    //调用验证方法
    echo $this->validate($code,false);
  }
  //封装一个通用的验证码检验函数 validate
  //@param1 $code str 要检查的填写验证码
  //@param2 $reset bool 设置验证成功后是否需要重置 默认要重置
  public function validate($code,$reset=true){
    //1、配置
    $cfg = array(
      'seKey'     =>  'szphp',   // 验证码加密密钥
      //ajax验证不需要重置,因为成功后页面上的验证码图不会更新
      //代码调用进行验证,可以重置
      'reset'     =>  $reset,     // 验证成功后是否重置
    );
    //2、实例化
    $verify = new \Think\Verify($cfg);
    //3、调用check方法
    return $verify->check($code);
  }
}
