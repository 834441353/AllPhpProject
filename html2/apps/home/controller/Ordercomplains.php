<?php
namespace apps\home\controller;
/**
 * ============================================================================
 * WSTMart多用户商城
 * 版权所有 2016-2066 广州商淘信息科技有限公司，并保留所有权利。
 * 官网地址:http://www.wstmart.net
 * 交流社区:http://bbs.shangtaosoft.com
 * 联系QQ:153289970
 * ----------------------------------------------------------------------------
 * 这不是一个自由软件！未经本公司授权您只能在不用于商业目的的前提下对程序代码进行修改和使用；
 * 不允许对程序代码以任何形式任何目的的再发布。
 * ============================================================================
 * 订单投诉控制器
 */
class OrderComplains extends Base{
    /******************************** 用户 ******************************************/
    /**
    * 查看投诉列表
    */
	public function index(){
		return view('default/users/orders/list_complain');
	}
    /**
    * 获取用户投诉列表
    */    
    public function queryUserComplainByPage(){
        $m = model('home/OrderComplains');
        return $m->queryUserComplainByPage();
        
    }
    /**
     * 订单投诉页面
     */
    public function complain(){
        $data = model('Home/OrderComplains')->getOrderInfo();
        $this->assign("data",$data);
        return $this->fetch("default/users/orders/complain");
    }
    /**
     * 保存订单投诉信息
     */
    public function saveComplain(){
        return model('Home/OrderComplains')->saveComplain();
    }
    /**
     * 用户查投诉详情
     */
    public function getUserComplainDetail(){
        $data = model('Home/OrderComplains')->getComplainDetail(0);
        $this->assign("data",$data);
        return $this->fetch("default/users/orders/complain_detail");
    }


    /******************************* 商家  ****************************************/
    /**
    * 商家-查看投诉列表
    */
    public function shopComplain(){
        return $this->fetch("default/shops/orders/list_complain");
    }

    /**
     * 获取商家被投诉订单列表
     */
    public function queryShopComplainByPage(){
        return model('Home/OrderComplains')->queryShopComplainByPage();
    }

    /**
     * 查投诉详情
     */
    public function getShopComplainDetail(){
        $data = model('Home/OrderComplains')->getComplainDetail(1);
        $this->assign("data",$data);
        return $this->fetch("default/shops/orders/complain_detail");
    }

     /**
     * 订单应诉页面
     */
    public function respond(){
        $data = model('Home/OrderComplains')->getComplainDetail(1);
        $this->assign("data",$data);
        return $this->fetch("default/shops/orders/respond");
    }
    /**
     * 保存订单应诉
     */
    public function saveRespond(){
        return model('Home/OrderComplains')->saveRespond();
    }


    


}