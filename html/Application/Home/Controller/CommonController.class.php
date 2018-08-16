<?php
namespace Home\Controller;
use Think\Controller;
class CommonController extends Controller {
  protected function _empty(){
    //跳转到首页
    // $this->redirect('index/index');

    //显示404页面
    $this->display('Error/404');
  }
}
