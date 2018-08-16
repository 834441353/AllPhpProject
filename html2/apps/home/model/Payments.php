<?php
namespace apps\home\model;
use think\Db;
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
 * 支付管理业务处理
 */
class Payments extends Base{
	/**
	 * 获取支付方式种类
	 */
	public function getByGroup(){
		$payments = ['0'=>[],'1'=>[]];
		$rs = $this->where(['enabled'=>1])->order('payOrder asc')->select();
		foreach ($rs as $key =>$v){
			if($v['payConfig']!='')$v['payConfig'] = json_decode($v['payConfig'], true);
			$payments[$v['isOnline']][] = $v;
		}
		return $payments;
	}
	

	
	
	
	/**
	 * 获取支付信息
	 * @return unknown
	 */
	public function getPayment($payCode){
		$payment = $this->where("enabled=1 AND payCode='$payCode' AND isOnline=1")->find();
		$payConfig = json_decode($payment["payConfig"]) ;
		foreach ($payConfig as $key => $value) {
			$payment[$key] = $value;
		}
		return $payment;
	}
	
	/**
	 * 获取支付订单信息
	 */
	public function getPayOrders ($obj){
		$userId = (int)$obj["userId"];
		$orderId = $obj["orderId"];
		$isBatch = (int)$obj["isBatch"];
		$needPay = 0;
		if($isBatch==1){
			$needPay = model('orders')->where(["userId"=>$userId,"orderunique"=>$orderId,"dataFlag"=>1,"orderStatus"=>-2,"isPay"=>0,"payType"=>1,"needPay"=>[">",0]])->sum('needPay');
		}else{
			$needPay = model('orders')->where(["userId"=>$userId,"orderId"=>$orderId,"dataFlag"=>1,"orderStatus"=>-2,"isPay"=>0,"payType"=>1,"needPay"=>[">",0]])->sum('needPay');
		}
		return $needPay;
	}
	
	/**
	 * 完成支付订单
	 */
	public function complatePay ($obj){
		$trade_no = $obj["trade_no"];
		$isBatch = (int)$obj["isBatch"];
		$orderId = $obj["out_trade_no"];
		$userId = (int)$obj["userId"];
		$payFrom = (int)$obj["payFrom"];
		$payMoney = (float)$obj["total_fee"];
		if($payFrom>0){
			$cnt = model('orders')
						->where(['payFrom'=>$payFrom,"userId"=>$userId,"tradeNo"=>$trade_no])
						->count();
			if($cnt>0){
				return WSTReturn('订单已支付',-1);
			}
		}
		
		$needPay = 0;
		//$goodslist = array();
		if($isBatch==1){
			$needPay = model('orders')->where(["userId"=>$userId,"orderunique"=>$orderId,"dataFlag"=>1,"orderStatus"=>-2,"isPay"=>0,"payType"=>1,"needPay"=>[">",0]])->sum('needPay');
			//$goodslist = model('orders')->alias('o')->join('__ORDER_GOODS__ og','og.orderId=o.orderId')
			//			->field("og.orderId,og.goodsId,og.goodsNum,og.goodsSpecId")
			//			->where(["userId"=>$userId,"orderunique"=>$orderId,"dataFlag"=>1,"orderStatus"=>-2,"isPay"=>0,"payType"=>1,"needPay"=>[">",0]])->select();
		}else{
			$needPay = model('orders')->where(["userId"=>$userId,"orderId"=>$orderId,"dataFlag"=>1,"orderStatus"=>-2,"isPay"=>0,"payType"=>1,"needPay"=>[">",0]])->sum('needPay');
			//$goodslist = model('orders')->alias('o')->join('__ORDER_GOODS__ og','og.orderId=o.orderId')
			//			->field("og.orderId,og.goodsId,og.goodsNum,og.goodsSpecId")
			//			->where(["userId"=>$userId,"o.orderId"=>$orderId,"dataFlag"=>1,"orderStatus"=>-2,"isPay"=>0,"payType"=>1,"needPay"=>[">",0]])->select();
		}
		if($needPay>$payMoney){
			return WSTReturn('支付金额不正确',-1);
		}
		Db::startTrans();
		try{
			$data = array();
			$data["needPay"] = 0;
			$data["isPay"] = 1;
			$data["orderStatus"] = 0;
			$data["tradeNo"] = $trade_no;
			$data["payFrom"] = $payFrom;
			$rs = false;
			if($isBatch==1){
				model('orders')->where(["orderunique"=>$orderId,"payType"=>1,"dataFlag"=>1,"orderStatus"=>-2,"needPay"=>[">",0]])->update($data);
			}else{
				model('orders')->where(["orderId"=>$orderId,"payType"=>1,"dataFlag"=>1,"orderStatus"=>-2,"needPay"=>[">",0]])->update($data);
			
			}
			if($needPay>0){
				//修改库存
				/*foreach ($goodslist as $key=> $goods){
					$goodsId = $goods['goodsId'];
					$goodsNum = $goods['goodsNum'];
					$goodsSpecId = $goods['goodsSpecId'];
					$vgoods = model('goods')->field("isSpec")->where("goodsId",$goodsId)->find();
					//修改库存
					if($vgoods['isSpec']>0){
						Db::table('__GOODS_SPECS__')->where('id',$goodsSpecId)->setDec('specStock',$goodsNum);
					}
					Db::table('__GOODS__')->where('goodsId',$goodsId)->setDec('goodsStock',$goodsNum);
				}*/
				
				$list = array();
				if($isBatch==1){
					$list = model('orders')->field('orderId,orderNo')->where(["userId"=>$userId,"orderunique"=>$orderId])->select();
				}else{
					$list = model('orders')->field('orderId,orderNo')->where(["userId"=>$userId,"orderId"=>$orderId])->select();
				}
				echo	$this->getLastSql();
				for($i=0;$i<count($list);$i++) {
					$orderId = $list[$i]["orderId"];
					//新增订单日志
					$logOrder = [];
					$logOrder['orderId'] = $orderId;
					$logOrder['orderStatus'] = 0;
					$logOrder['logContent'] = "订单已支付,下单成功";;
					$logOrder['logUserId'] = $userId;
					$logOrder['logType'] = 0;
					$logOrder['logTime'] = date('Y-m-d H:i:s');
					model('logOrders')->save($logOrder);
				}
			}
			Db::commit();
			return WSTReturn('支付成功',1);
		}catch (\Exception $e) {
			Db::rollback();
			return WSTReturn('操作失败',-1);
		}
	}
	
}
