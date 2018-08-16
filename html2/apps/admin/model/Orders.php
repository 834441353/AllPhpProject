<?php
namespace apps\admin\model;
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
 * 订单业务处理类
 */
use think\Db;
class Orders extends Base{
	/**
	 * 获取用户订单列表
	 */
	public function pageQuery($orderStatus = 10000,$isAppraise = -1){
		$where = ['o.dataFlag'=>1];
		if($orderStatus!=10000){
			$where['orderStatus'] = $orderStatus;
		}
		$orderNo = input('orderNo');
		$shopName = input('shopName');
		$payType = (int)input('payType',-1);
		$deliverType = (int)input('deliverType',-1);
		if($isAppraise!=-1)$where['isAppraise'] = $isAppraise;
		if($orderNo!='')$where['orderNo'] = ['like','%'.$orderNo.'%'];
		if($shopName!='')$where['shopName|shopSn'] = ['like','%'.$shopName.'%'];

		$areaId1 = (int)input('areaId1');

		if($areaId1>0){
			$where['s.areaIdPath'] = ['like',"$areaId1%"];
			$areaId2 = (int)input("areaId1_".$areaId1);
			if($areaId2>0)$where['s.areaIdPath'] = ['like',$areaId1."_"."$areaId2%"];
			$areaId3 = (int)input("areaId1_".$areaId1."_".$areaId2);
			if($areaId3>0)$where['s.areaId'] = $areaId3;
		}

		if($deliverType!=-1)$where['o.deliverType'] = $deliverType;
		if($payType!=-1)$where['o.payType'] = $payType;
		$page = $this->alias('o')->join('__SHOPS__ s','o.shopId=s.shopId','left')->where($where)
		     ->field('o.orderId,o.orderNo,s.shopName,s.shopId,s.shopQQ,s.shopWangWang,o.goodsMoney,o.totalMoney,o.realTotalMoney,
		              o.orderStatus,o.userName,o.deliverType,payType,payFrom,o.orderStatus,orderSrc,o.createTime')
			 ->order('o.createTime', 'desc')
			 ->paginate(input('pagesize/d'))->toArray();
	    if(count($page['Rows'])>0){
	    	 foreach ($page['Rows'] as $key => $v){
	    	 	 $page['Rows'][$key]['payType'] = WSTLangPayType($v['payType']);
	    	 	 $page['Rows'][$key]['deliverType'] = WSTLangDeliverType($v['deliverType']==1);
	    	 	 $page['Rows'][$key]['status'] = WSTLangOrderStatus($v['orderStatus']);
	    	 }
	    }
	    //echo $this->getLastSql();die;
	    return $page;
	}
	
    /**
	 * 获取用户订单列表
	 */
	public function refundPageQuery(){
		$where = ['o.dataFlag'=>1];
		$where['orderStatus'] = ['in',[-1,-3,-4,-5]];
		$where['o.payType'] = 1;
		$orderNo = input('orderNo');
		$shopName = input('shopName');
		$deliverType = (int)input('deliverType',-1);
		$areaId1 = (int)input('areaId1');
		$areaId2 = (int)input('areaId2');
		$areaId3 = (int)input('areaId3');
		$isRefund = (int)input('isRefund',-1);
		if($orderNo!='')$where['orderNo'] = ['like','%'.$orderNo.'%'];
		if($shopName!='')$where['shopName|shopSn'] = ['like','%'.$shopName.'%'];
		if($areaId1>0)$where['s.areaId1'] = $areaId1;
		if($areaId2>0)$where['s.areaId2'] = $areaId2;
		if($areaId3>0)$where['s.areaId3'] = $areaId3;
		if($deliverType!=-1)$where['o.deliverType'] = $deliverType;
		if($isRefund!=-1)$where['o.isRefund'] = $isRefund;
		$page = $this->alias('o')->join('__SHOPS__ s','o.shopId=s.shopId','left')->where($where)
		     ->field('o.orderId,o.orderNo,s.shopName,s.shopId,s.shopQQ,s.shopWangWang,o.goodsMoney,o.totalMoney,o.realTotalMoney,
		              o.orderStatus,o.userName,o.deliverType,payType,payFrom,o.orderStatus,orderSrc,refundRemark,isRefund,o.createTime')
			 ->order('o.createTime', 'desc')
			 ->paginate(input('pagesize/d'))->toArray();
	    if(count($page['Rows'])>0){
	    	 foreach ($page['Rows'] as $key => $v){
	    	 	 $page['Rows'][$key]['payType'] = WSTLangPayType($v['payType']);
	    	 	 $page['Rows'][$key]['deliverType'] = WSTLangDeliverType($v['deliverType']==1);
	    	 	 $page['Rows'][$key]['status'] = WSTLangOrderStatus($v['orderStatus']);
	    	 }
	    }
	    return $page;
	}
	
	
	/**
	 * 获取订单详情
	 */
	public function getByView($orderId){
		$orders = $this->alias('o')->join('__EXPRESS__ e','o.expressId=e.expressId','left')
		               ->join('__SHOPS__ s','o.shopId=s.shopId','left')
		               ->where('o.dataFlag=1 and o.orderId='.$orderId)
		               ->field('o.*,e.expressName,s.shopName,s.shopQQ,s.shopWangWang')->find();
		if(empty($orders))return WSTReturn("无效的订单信息");
		//获取订单信息
		$orders['log'] = model('LogOrders')->where('orderId',$orderId)->order('logId asc')->select();
		//获取订单商品
		$orders['goods'] = model('OrderGoods')->where('orderId',$orderId)->order('id asc')->select();
		return $orders;
	}
}
