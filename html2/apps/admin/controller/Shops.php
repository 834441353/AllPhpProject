<?php
namespace apps\admin\controller;
use apps\admin\model\Shops as M;
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
 * 店铺控制器
 */
class Shops extends Base{
    public function index(){
    	return $this->fetch("list");
    }
    public function stopIndex(){
    	return $this->fetch("list_stop");
    }
    /**
     * 获取分页
     */
    public function pageQuery(){
    	$m = new M();
    	$rs = $m->pageQuery(1);
    	return $rs;
    }
    /**
     * 停用店铺列表
     */
    public function pageStopQuery(){
    	$m = new M();
    	$rs = $m->pageQuery(-1);
    	return $rs;
    }
    /**
     * 获取菜单
     */
    public function get(){
    	$m = new M();
    	$rs = $m->get((int)Input("post.id"));
    	return $rs;
    }
    /**
     * 跳去编辑页面
     */
    public function toEdit(){
    	$m = new M();
    	$id = (int)Input("get.id");
    	if($id>0){
    	    $object = $m->getById((int)Input("get.id"));
    	    $object['applyId'] = 0;
    	    $data['object']=$object;
    	}else{
    		$object = $m->getEModel('shops');
    		$object['catshops'] = [];
    		$object['accreds'] = [];
    		$object['applyId'] = 0;
    		$object['loginName'] = '';
    		$data['object']=$object;
    	}
    	$data['goodsCatList'] = model('goodsCats')->listQuery(0);
    	$data['accredList'] = model('accreds')->listQuery(0);
    	$data['bankList'] = model('banks')->listQuery();
    	$data['areaList'] = model('areas')->listQuery(0);   	
    	return $this->fetch("edit",$data);
    }
    /**
     * 跳去新增页面
     */
    public function toAddByApply(){
    	$apply = model('ShopApplys')->checkOpenShop((int)Input("get.id"));
    	if($apply['shopId']!=''){
    		$this->assign("msg",'对不起，该开店申请已处理！');
    		return $this->fetch("./message");
    	}else{
    		$object = model('ShopApplys')->getEModel('shops');
    		$object['userId'] = (int)$apply['userId'];
    		$object['applyId'] = (int)Input("get.id");
    		$object['loginName'] = '';
    		$object['catshops'] = [];
    		$object['accreds'] = [];
    		$data = [
    		   'object'=>$object,
    		   'goodsCatList'=>model('goodsCats')->listQuery(0),
    		   'accredList'=>model('accreds')->listQuery(0),
    		   'bankList'=>model('banks')->listQuery(),
    		   'areaList'=>model('areas')->listQuery(0)
    		];
	    	return $this->fetch("edit",$data);
    	}
    }
    /**
     * 新增菜单
     */
    public function add(){
    	$m = new M();
    	$rs = $m->add();
    	return $rs;
    }
    /**
     * 编辑菜单
     */
    public function edit(){
    	$m = new M();
    	$rs = $m->edit();
    	return $rs;
    }
    /**
     * 删除菜单
     */
    public function del(){
    	$m = new M();
    	$rs = $m->del();
    	return $rs;
    }
    
    /**
     * 检测店铺编号是否存在
     */
    public function checkShopSn(){
    	$m = new M();
    	$isChk = $m->checkShopSn(input('post.shopSn'),input('shopId/d'));
        if(!$isChk){
    		return ['ok'=>'该店铺编号可用'];
    	}else{
    		return ['error'=>'对不起，该店铺编号已存在'];
    	}
    }
}
