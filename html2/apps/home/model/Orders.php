<?php
namespace apps\home\model;
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
	 * 提交订单
	 */
	public function submit(){
		$addressId = (int)input('post.s_addressId');
		$deliverType = ((int)input('post.deliverType')!=0)?1:0;
		$isInvoice = ((int)input('post.isInvoice')!=0)?1:0;
		$invoiceClient = ($isInvoice==1)?input('post.invoiceClient'):'';
		$payType = ((int)input('post.payType')!=0)?1:0;
		$userId = (int)session('WST_USER.userId');
		//检测购物车
		$carts = model('carts')->getCarts(true);
		if(empty($carts['carts']))return WSTReturn("请选择要购买的商品");
		//检测地址是否有效
		$address = Db::table('__USER_ADDRESS__')->where(['userId'=>$userId,'addressId'=>$addressId,'dataFlag'=>1])->find();
		if(empty($address)){
			return WSTReturn("无效的用户地址");
		}
	    $areaIds = [];
        $areaMaps = [];
        $tmp = explode('_',$address['areaIdPath']);
        $address['areaId2'] = $tmp[1];//记录配送城市
        foreach ($tmp as $vv){
         	if($vv=='')continue;
         	if(!in_array($vv,$areaIds))$areaIds[] = $vv;
        }
        if(!empty($areaIds)){
	         $areas = Db::table('__AREAS__')->where(['dataFlag'=>1,'areaId'=>['in',$areaIds]])->field('areaId,areaName')->select();
	         foreach ($areas as $v){
	         	 $areaMaps[$v['areaId']] = $v['areaName'];
	         }
	         $tmp = explode('_',$address['areaIdPath']);
	         $areaNames = [];
		     foreach ($tmp as $vv){
	         	 if($vv=='')continue;
	         	 $areaNames[] = $areaMaps[$vv];
	         	 $address['areaName'] = implode('',$areaNames);
	         }
         }
		$address['userAddress'] = $address['areaName'].$address['userAddress'];
		WSTUnset($address, 'isDefault,dataFlag,createTime,userId');
		//生成订单
		Db::startTrans();
		try{
			$ogModel = model('OrderGoods');
			$loModel = model('LogOrders');
			$gModel = model('Goods');
			$gsModel = model('GoodsSpecs');
			$mModel = model('messages');
			$orderunique = WSTOrderQnique();
			foreach ($carts['carts'] as $ckey =>$shopOrder){
				$orderNo = WSTOrderNo(); 
				$orderScore = 0;
				//创建订单
				$order = [];
				$order = array_merge($order,$address);
				$order['orderNo'] = $orderNo;
				$order['userId'] = $userId;
				$order['shopId'] = $shopOrder['shopId'];
				$order['payType'] = $payType;
				if($payType==1){
				    $order['orderStatus'] = -2;//待付款
				    $order['isPay'] = 0;
				}else{
					$order['orderStatus'] = 0;//待发货
				}
				$order['goodsMoney'] = $shopOrder['goodsMoney'];
				$order['deliverType'] = $deliverType;
				$order['deliverMoney'] = ($deliverType==1)?0:WSTOrderFreight($shopOrder['shopId'],$order['areaId2']);
				$order['totalMoney'] = $order['goodsMoney']+$order['deliverMoney'];
				$order['realTotalMoney'] = $order['totalMoney'];
				$order['needPay'] = $order['realTotalMoney'];
				//积分
				$orderScore = round($order['totalMoney']-$order['deliverMoney'],0);
				$order['orderScore'] = $orderScore;
				$order['isInvoice'] = $isInvoice;
				$order['invoiceClient'] = $invoiceClient;
				$order['orderRemarks'] = input('post.remark_'.$shopOrder['shopId']);
				$order['orderunique'] = $orderunique;
				$order['orderSrc'] = 0;
				$order['dataFlag'] = 1;
				$order['createTime'] = date('Y-m-d H:i:s');
				$result = $this->data($order,true)->isUpdate(false)->allowField(true)->save($order);
				if(false !== $result){
					$orderId = $this->orderId;
					$orderTotalGoods = [];
					foreach ($shopOrder['list'] as $gkey =>$goods){
						//创建订单商品记录
						$orderGgoods = [];
						$orderGoods['orderId'] = $orderId;
						$orderGoods['goodsId'] = $goods['goodsId'];
						$orderGoods['goodsNum'] = $goods['cartNum'];
						$orderGoods['goodsPrice'] = $goods['shopPrice'];
						$orderGoods['goodsSpecId'] = $goods['goodsSpecId'];
						if(!empty($goods['specNames'])){
							$specNams = [];
							foreach ($goods['specNames'] as $pkey =>$spec){
								$specNams[] = $spec['catName'].'：'.$spec['itemName'];
							}
							$orderGoods['goodsSpecNames'] = implode('@@_@@',$specNams);
						}
						$orderGoods['goodsName'] = $goods['goodsName'];
						$orderGoods['goodsImg'] = $goods['goodsImg'];
						$orderTotalGoods[] = $orderGoods;
						//修改库存
						if($goods['goodsSpecId']>0){
					        $gsModel->where('id',$goods['goodsSpecId'])->setDec('specStock',$goods['cartNum']);
						}
						$gModel->where('goodsId',$goods['goodsId'])->setDec('goodsStock',$goods['cartNum']);
					}
					$ogModel->saveAll($orderTotalGoods);
					//建立订单记录
					$logOrder = [];
					$logOrder['orderId'] = $orderId;
					$logOrder['orderStatus'] = $order['orderStatus'];
					$logOrder['logContent'] = ($payType==1)?"下单成功，等待用户支付":"下单成功";
					$logOrder['logUserId'] = $userId;
					$logOrder['logType'] = 0;
					$logOrder['logTime'] = date('Y-m-d H:i:s');
					$loModel->create($logOrder);
					//给店铺增加提示消息
					$message = [];
					$message['msgType'] = 0;
					$message['sendUserId'] = 1;
					$message['receiveUserId'] = $shopOrder['userId'];
					$message['msgContent'] = "您有一笔新的订单【".$orderNo."】待处理。";
					$message['createTime'] = date('Y-m-d H:i:s');
					$message['msgStatus'] = 0;
					$message['dataFlag'] = 1;
					$message['msgJson'] = json_encode(['from'=>1,'dataId'=>$orderId]);
					$mModel->create($message);
				}
			}
			//删除已选的购物车商品
			model('Carts')->where(['userId'=>$userId,'isCheck'=>1])->delete();
			Db::commit();
			return WSTReturn("提交订单成功", 1,$orderunique);
		}catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('提交订单失败',-1);
        }
	}
	
	/**
	 * 根据订单唯一流水获取订单信息
	 */
	public function getByUnique(){
		$id = input('id');
		$isBatch = input('isBatch/d',1);
		$userId = (int)session('WST_USER.userId');
		if($isBatch==1){
			$rs = $this->where(['userId'=>$userId,'orderunique'=>$id])->field('orderId,orderNo,payType,needPay,orderunique,deliverMoney')->select();
		}else{
			$rs = $this->where(['userId'=>$userId,'orderId'=>$id])->field('orderId,orderNo,payType,needPay,orderunique,deliverMoney')->select();
		}
		
		$data = [];
		$data['orderunique'] = $id;
		$data['list'] = [];
		$payType = 0;
		$totalMoney = 0;
		$orderIds = [];
		foreach ($rs as $key =>$v){
			if($v['payType']==1)$payType = 1;
			$totalMoney = $totalMoney + $v['needPay'];
			$orderIds[] = $v['orderId'];
			$data['list'][] = $v;
		}
		$data['totalMoney'] = $totalMoney;
		$data['payType'] = $payType;
		//如果是在线支付的话就要加载商品信息和支付信息
		if($data['payType']==1){
			//获取商品信息
			$goods = model('OrderGoods')->where(['orderId'=>['in',$orderIds]])->select();
			foreach ($goods as $key =>$v){
				if($v['goodsSpecNames']!=''){
				    $v['goodsSpecNames'] = explode('@@_@@',$v['goodsSpecNames']);
				}else{
					$v['goodsSpecNames'] = [];
				}
				$data['goods'][$v['orderId']][] = $v;
			}
			//获取支付信息
			$payments = model('payments')->where(['isOnline'=>1,'enabled'=>1])->order('payOrder asc')->select();
			$data['payments'] = $payments;
		}
		return $data;
	}
	
	/**
	 * 获取用户订单列表
	 */
	public function userOrdersByPage($orderStatus,$isAppraise = -1){
		$userId = (int)session('WST_USER.userId');
		$orderNo = (int)input('post.orderNo');
		$shopName = input('post.shopName');
		$isRefund = (int)input('post.isRefund');
		$where = ['o.userId'=>$userId,'o.dataFlag'=>1];
		if(is_array($orderStatus)){
			$where['orderStatus'] = ['in',$orderStatus];
		}else{
			$where['orderStatus'] = $orderStatus;
		}
		if($isAppraise!=-1)$where['isAppraise'] = $isAppraise;
		if($orderNo>0){
			$where['o.orderNo'] = ['like',"%$orderNo%"];
		}
		if($shopName != ''){
			$where['s.shopName'] = ['like',"%$shopName%"];
		}
		if($isRefund>0){
			$where['isRefund'] = $isRefund;
		}

		$page = $this->alias('o')->join('__SHOPS__ s','o.shopId=s.shopId','left')->join('__ORDER_COMPLAINS__ oc','oc.orderId=o.orderId','left')->where($where)
		     ->field('o.orderId,o.orderNo,s.shopName,s.shopId,s.shopQQ,s.shopWangWang,o.goodsMoney,o.totalMoney,o.realTotalMoney,
		              o.orderStatus,o.deliverType,deliverMoney,payType,payFrom,o.orderStatus,needPay,isAppraise,isRefund,orderSrc,o.createTime,oc.complainId')
			 ->order('o.createTime', 'desc')
			 ->paginate(input('pagesize/d'))->toArray();
	    if(count($page['Rows'])>0){
	    	 $orderIds = [];
	    	 foreach ($page['Rows'] as $v){
	    	 	 $orderIds[] = $v['orderId'];
	    	 }
	    	 $goods = model('OrderGoods')->where('orderId','in',$orderIds)->select();
	    	 $goodsMap = [];
	    	 foreach ($goods as $v){
	    	 	 $v['goodsSpecNames'] = str_replace('@@_@@','、',$v['goodsSpecNames']);
	    	 	 $goodsMap[$v['orderId']][] = $v;
	    	 }
	    	 foreach ($page['Rows'] as $key => $v){
	    	 	 $page['Rows'][$key]['list'] = $goodsMap[$v['orderId']];
	    	 	 $page['Rows'][$key]['isComplain'] = 1;
	    	 	 if(($v['complainId']=='') && ($v['payType']==0 || ($v['payType']==1 && $v['orderStatus']!=2))){
	    	 	 	$page['Rows'][$key]['isComplain'] = '';
	    	 	 }
	    	 	 $page['Rows'][$key]['payType'] = WSTLangPayType($v['payType']);
	    	 	 $page['Rows'][$key]['deliverType'] = WSTLangDeliverType($v['deliverType']==1);
	    	 	 $page['Rows'][$key]['status'] = WSTLangOrderStatus($v['orderStatus']);
	    	 }
	    }
	    return $page;
	}
	
	/**
	 * 获取商家订单
	 */
	public function shopOrdersByPage($orderStatus){
		$orderNo = (int)input('post.orderNo');
		$shopName = input('post.shopName');
		$payType = (int)input('post.payType');
		$deliverType = (int)input('post.deliverType');

		$shopId = (int)session('WST_USER.shopId');
		$where = ['shopId'=>$shopId,'dataFlag'=>1];
		if(is_array($orderStatus)){
			$where['orderStatus'] = ['in',$orderStatus];
		}else{
			$where['orderStatus'] = $orderStatus;
		}
		if($orderNo>0){
			$where['orderNo'] = ['like',"%$orderNo%"];
		}
		if($shopName!=''){
			$where['shopName'] = ['like',"%$shopName%"];
		}
		if($payType > -1){
			$where['payType'] =  $payType;
		}
		if($deliverType > -1){
			$where['deliverType'] =  $deliverType;
		}
		$page = $this->where($where)
		     ->field('orderId,orderNo,goodsMoney,totalMoney,realTotalMoney,orderStatus,deliverType,deliverMoney,isAppraise
		              ,payType,payFrom,userAddress,orderStatus,isPay,isAppraise,userName,orderSrc,createTime')
			 ->order('createTime', 'desc')
			 ->paginate()->toArray();
	    if(count($page['Rows'])>0){
	    	 $orderIds = [];
	    	 foreach ($page['Rows'] as $v){
	    	 	 $orderIds[] = $v['orderId'];
	    	 }
	    	 $goods = model('OrderGoods')->where('orderId','in',$orderIds)->select();
	    	 $goodsMap = [];
	    	 foreach ($goods as $v){
	    	 	 $v['goodsSpecNames'] = str_replace('@@_@@','、',$v['goodsSpecNames']);
	    	 	 $goodsMap[$v['orderId']][] = $v;
	    	 }
	    	 foreach ($page['Rows'] as $key => $v){
	    	 	 $page['Rows'][$key]['list'] = $goodsMap[$v['orderId']];
	    	 	 $page['Rows'][$key]['payTypeName'] = WSTLangPayType($v['payType']);
	    	 	 $page['Rows'][$key]['deliverType'] = WSTLangDeliverType($v['deliverType']==1);
	    	 	 $page['Rows'][$key]['status'] = WSTLangOrderStatus($v['orderStatus']);
	    	 }
	    }
	    return $page;
	}
	/**
	 * 商家发货
	 */
	public function deliver(){
		$orderId = (int)input('post.id');
		$expressId = (int)input('post.expressId');
		$expressNo = (int)input('post.expressNo');
		$shopId = (int)session('WST_USER.shopId');
		$userId = (int)session('WST_USER.userId');
		$order = $this->where(['shopId'=>$shopId,'orderId'=>$orderId,'orderStatus'=>0])->field('orderId,orderNo,userId')->find();
		if(!empty($order)){
			Db::startTrans();
		    try{
				$data = ['orderStatus'=>1,'expressId'=>$expressId,'expressNo'=>$expressNo,'deliveryTime'=>date('Y-m-d H:i:s')];
			    $result = $this->where('orderId',$order['orderId'])->update($data);
				if(false != $result){
					//新增订单日志
					$logOrder = [];
					$logOrder['orderId'] = $orderId;
					$logOrder['orderStatus'] = 1;
					$logOrder['logContent'] = "商家已发货".(($expressNo!='')?"，快递号为：".$expressNo:"");
					$logOrder['logUserId'] = $userId;
					$logOrder['logType'] = 0;
					$logOrder['logTime'] = date('Y-m-d H:i:s');
					model('logOrders')->save($logOrder);
					//发送一条用户信息
					$message = [];
					$message['msgType'] = 0;
					$message['sendUserId'] = 1;
					$message['receiveUserId'] = $order['userId'];
					$message['msgContent'] = "您的订单【".$order['orderNo']."】已发货啦".(($expressNo!='')?"，快递号为：".$expressNo:"")."，请做好收货准备哦~";
					$message['createTime'] = date('Y-m-d H:i:s');
					$message['msgStatus'] = 0;
					$message['dataFlag'] = 1;
					$message['msgJson'] = json_encode(['from'=>1,'dataId'=>$orderId]);
					model('Messages')->save($message);
					Db::commit();
					return WSTReturn('操作成功',1);
				}
			}catch (\Exception $e) {
	            Db::rollback();
	            return WSTReturn('操作失败',-1);
	        }
		}
		return WSTReturn('操作失败，请检查订单状态是否已改变');
	}
	/**
	 * 用户收货
	 */
	public function receive(){
		$orderId = (int)input('post.id');
		$userId = (int)session('WST_USER.userId');
		$order = $this->alias('o')->join('__SHOPS__ s','o.shopId=s.shopId','left')
		              ->where(['o.userId'=>$userId,'o.orderId'=>$orderId,'o.orderStatus'=>1])
		              ->field('o.orderId,o.orderNo,s.userId,o.orderScore')->find();
		if(!empty($order)){
			Db::startTrans();
		    try{
				$data = ['orderStatus'=>2,'receiveTime'=>date('Y-m-d H:i:s')];
			    $result = $this->where('orderId',$order['orderId'])->update($data);
				if(false != $result){
					//新增订单日志
					$logOrder = [];
					$logOrder['orderId'] = $orderId;
					$logOrder['orderStatus'] = 2;
					$logOrder['logContent'] = "用户已收货";
					$logOrder['logUserId'] = $userId;
					$logOrder['logType'] = 0;
					$logOrder['logTime'] = date('Y-m-d H:i:s');
					model('logOrders')->save($logOrder);
					//发送一条商家信息
					$message = [];
					$message['msgType'] = 0;
					$message['sendUserId'] = 1;
					$message['receiveUserId'] = $order['userId'];
					$message['msgContent'] = "您的订单【".$order['orderNo']."】，用户已签收";
					$message['createTime'] = date('Y-m-d H:i:s');
					$message['msgStatus'] = 0;
					$message['dataFlag'] = 1;
					$message['msgJson'] = json_encode(['from'=>1,'dataId'=>$orderId]);
					model('Messages')->save($message);
					//给用户增加积分
					if(WSTConf("CONF.isOrderScore")==1){
						$score = [];
						$score['userId'] = $userId;
						$score['score'] = $order['orderScore'];
						$score['dataSrc'] = 1;
						$score['dataId'] = $orderId;
						$score['dataRemarks'] = "交易订单【".$order['orderNo']."】获得积分".$order['orderScore']."个";
						$score['scoreType'] = 1;
						$score['createTime'] = date('Y-m-d H:i:s');
						model('UserScores')->save($score);
					}
					Db::commit();
					return WSTReturn('操作成功',1);
				}
		    }catch (\Exception $e) {
	            Db::rollback();
	            return WSTReturn('操作失败',-1);
	        }
		}
		return WSTReturn('操作失败，请检查订单状态是否已改变');
	}
	/**
	 * 用户取消订单
	 */
	public function cancel(){
		$orderId = (int)input('post.id');
		$reason = (int)input('post.reason');
		$userId = (int)session('WST_USER.userId');
		$order = $this->alias('o')->join('__SHOPS__ s','o.shopId=s.shopId','left')
		              ->where(['o.userId'=>$userId,'o.orderId'=>$orderId,'o.orderStatus'=>['in',[-2,0]]])
		              ->field('o.orderId,o.orderNo,s.userId')->find();
		$reasonData = WSTDatas(1,$reason);
		if(empty($reasonData))return WSTReturn("无效的取消原因");
		if(!empty($order)){
			Db::startTrans();
		    try{
				$data = ['orderStatus'=>-1,'cancelReason'=>$reason];
			    $result = $this->where('orderId',$order['orderId'])->update($data);
				if(false != $result){
					//返还商品库存
					$goods = Db::table('__ORDER_GOODS__')->alias('og')->join('__GOODS__ g','og.goodsId=g.goodsId','inner')
					           ->where('orderId',$orderId)->field('og.*,g.isSpec')->select();
					foreach ($goods as $key => $v){
						//修改库存
						if($v['isSpec']>0){
					        Db::table('__GOODS_SPECS__')->where('id',$v['goodsSpecId'])->setInc('specStock',$v['goodsNum']);
						}
						Db::table('__GOODS__')->where('goodsId',$v['goodsId'])->setInc('goodsStock',$v['goodsNum']);
					}
					//新增订单日志
					$logOrder = [];
					$logOrder['orderId'] = $orderId;
					$logOrder['orderStatus'] = -1;
					$logOrder['logContent'] = "用户取消订单，取消原因：".$reasonData['dataName'];
					$logOrder['logUserId'] = $userId;
					$logOrder['logType'] = 0;
					$logOrder['logTime'] = date('Y-m-d H:i:s');
					model('logOrders')->save($logOrder);
					//发送一条商家信息
					$message = [];
					$message['msgType'] = 0;
					$message['sendUserId'] = 1;
					$message['receiveUserId'] = $order['userId'];
					$message['msgContent'] = "订单【".$order['orderNo']."】用户已取消，取消原因：".$reasonData['dataName'];
					$message['createTime'] = date('Y-m-d H:i:s');
					$message['msgStatus'] = 0;
					$message['dataFlag'] = 1;
					$message['msgJson'] = json_encode(['from'=>1,'dataId'=>$orderId]);
					model('Messages')->save($message);
					Db::commit();
					return WSTReturn('订单取消成功',1);
				}
			}catch (\Exception $e) {
		        Db::rollback();
		        print_r($e);
	            return WSTReturn('操作失败',-1);
	        }
		}
		return WSTReturn('操作失败，请检查订单状态是否已改变');
	}
	/**
	 * 用户拒收订单
	 */
	public function reject(){
		$orderId = (int)input('post.id');
		$reason = (int)input('post.reason');
		$content = input('post.content');
		$userId = (int)session('WST_USER.userId');
		$order = $this->alias('o')->join('__SHOPS__ s','o.shopId=s.shopId','left')
		              ->where(['o.userId'=>$userId,'o.orderId'=>$orderId,'o.orderStatus'=>1])
		              ->field('o.orderId,o.orderNo,s.userId')->find();
		$reasonData = WSTDatas(2,$reason);
		if(empty($reasonData))return WSTReturn("无效的拒收原因");
		if($reason==10000 && $content=='')return WSTReturn("请输入拒收原因");
		if(!empty($order)){
			Db::startTrans();
		    try{
				$data = ['orderStatus'=>-3,'rejectReason'=>$reason];
				if($reason==10000)$data['rejectOtherReason'] = $content;
			    $result = $this->where('orderId',$order['orderId'])->update($data);
				if(false != $result){
					//新增订单日志
					$logOrder = [];
					$logOrder['orderId'] = $orderId;
					$logOrder['orderStatus'] = -3;
					$logOrder['logContent'] = "用户拒收订单，拒收原因：".$reasonData['dataName'].(($reason==10000)?"-".$content:"");
					$logOrder['logUserId'] = $userId;
					$logOrder['logType'] = 0;
					$logOrder['logTime'] = date('Y-m-d H:i:s');
					model('logOrders')->save($logOrder);
					//发送一条商家信息
					$message = [];
					$message['msgType'] = 0;
					$message['sendUserId'] = 1;
					$message['receiveUserId'] = $order['userId'];
					$message['msgContent'] = "订单【".$order['orderNo']."】用户拒收，拒收原因：".$reasonData['dataName'].(($reason==10000)?"-".$content:"");
					$message['createTime'] = date('Y-m-d H:i:s');
					$message['msgStatus'] = 0;
					$message['dataFlag'] = 1;
					$message['msgJson'] = json_encode(['from'=>1,'dataId'=>$orderId]);
					model('Messages')->save($message);
					Db::commit();
					return WSTReturn('操作成功',1);
				}
			}catch (\Exception $e) {
		        Db::rollback();
	            return WSTReturn('操作失败',-1);
	        }
		}
		return WSTReturn('操作失败，请检查订单状态是否已改变');
	}
	/**
	 * 获取订单价格
	 */
	public function getMoneyByOrder(){
		$orderId = (int)input('post.id');
		return $this->where('orderId',$orderId)->field('orderId,goodsMoney,deliverMoney,totalMoney,realTotalMoney')->find();
	}
	/**
	 * 修改订单价格
	 */
	public function editOrderMoney(){
		$orderId = input('post.id');
		$orderMoney = (float)input('post.orderMoney');
		$userId = (int)session('WST_USER.userId');
		$shopId = (int)session('WST_USER.shopId');
		if($orderMoney<0)return WSTReturn("订单价格不能小于0");
		Db::startTrans();
		try{
			$result = $this->where(['orderId'=>$orderId,'shopId'=>$shopId,'orderStatus'=>-2])->update(['realTotalMoney'=>$orderMoney]);
			if(false !== $result){
				//新增订单日志
				$logOrder = [];
				$logOrder['orderId'] = $orderId;
				$logOrder['orderStatus'] = -2;
				$logOrder['logContent'] = "商家修改订单价格为：".$orderMoney;
				$logOrder['logUserId'] = $userId;
				$logOrder['logType'] = 0;
				$logOrder['logTime'] = date('Y-m-d H:i:s');
				model('logOrders')->save($logOrder);
				Db::commit();
				return WSTReturn('操作成功',1);
			}
		}catch (\Exception $e) {
		    Db::rollback();
	        return WSTReturn('操作失败',-1);
	    }
	}
	/**
	 * 商家同意/不同意拒收
	 */
	public function confer(){
		$orderId = (int)input('post.id');
		$content = input('post.content');
		$status = ((int)input('post.status')==1)?1:0;
		$userId = (int)session('WST_USER.userId');
		$shopId = (int)session('WST_USER.shopId');
		$order = $this->where(['shopId'=>$shopId,'orderId'=>$orderId,'orderStatus'=>-3])
		              ->field('orderId,orderNo,userId')->find();
		if($status==0 && $content=='')return WSTReturn("请输入不同意原因");
		if(!empty($order)){
			Db::startTrans();
		    try{
				$data = ['orderStatus'=>(($status==1)?-4:-5)];
				if($status==0)$data['shopRejectReason'] = $content;
			    $result = $this->where('orderId',$order['orderId'])->update($data);
				if(false != $result){
					//新增订单日志
					$logOrder = [];
					$logOrder['orderStatus'] = (($status==1)?-4:-5);
					$logOrder['orderId'] = $orderId;
					$logOrder['logContent'] = ($status==1)?"商家同意拒收订单":"商家不同意拒收订单，原因：".$content;
					$logOrder['logUserId'] = $userId;
					$logOrder['logType'] = 0;
					$logOrder['logTime'] = date('Y-m-d H:i:s');
					model('logOrders')->save($logOrder);
					//发送一条用户信息
					$message = [];
					$message['msgType'] = 0;
					$message['sendUserId'] = 1;
					$message['receiveUserId'] = $order['userId'];
					$message['msgContent'] = "您的订单【".$order['orderNo']."】".(($status==1)?"商家同意拒收订单":"商家不同意拒收订单，原因：".$content);
					$message['createTime'] = date('Y-m-d H:i:s');
					$message['msgStatus'] = 0;
					$message['dataFlag'] = 1;
					$message['msgJson'] = json_encode(['from'=>1,'dataId'=>$orderId]);
					model('Messages')->save($message);
					Db::commit();
					return WSTReturn('操作成功',1);
				}
			}catch (\Exception $e) {
		        Db::rollback();
	            return WSTReturn('操作失败',-1);
	        }
		}
		return WSTReturn('操作失败，请检查订单状态是否已改变');
	}
	/**
	 * 获取订单详情
	 */
	public function getByView($orderId){
		$userId = (int)session('WST_USER.userId');
		$shopId = (int)session('WST_USER.shopId');
		$orders = $this->alias('o')->join('__EXPRESS__ e','o.expressId=e.expressId','left')
		               ->join('__SHOPS__ s','o.shopId=s.shopId','left')
		               ->where('o.dataFlag=1 and o.orderId='.$orderId.' and ( o.userId='.$userId.' or o.shopId='.$shopId.')')
		               ->field('o.*,e.expressName,s.shopName,s.shopQQ,s.shopWangWang')->find();
		if(empty($orders))return WSTReturn("无效的订单信息");
		//获取订单信息
		$orders['log'] = model('LogOrders')->where('orderId',$orderId)->order('logId asc')->select();
		//获取订单商品
		$orders['goods'] = model('OrderGoods')->where('orderId',$orderId)->order('id asc')->select();
		return $orders;
	}



	/**
	* 根据订单id获取 商品信息跟商品评价
	*/
	public function getOrderInfoAndAppr(){
		$orderId = (int)input('oId');

		$goodsInfo = model('orderGoods')
					->field('id,orderId,goodsName,goodsId,goodsSpecNames,goodsImg,goodsSpecId')
					->where(['orderId'=>$orderId])
					->paginate()->toArray();
		//根据商品id 与 订单id 取评价
		$alreadys = 0;// 已评价商品数
		$count = count($goodsInfo['Rows']);//订单下总商品数
		if(($count)>0){
			foreach($goodsInfo['Rows'] as $k=>$v){
				$goodsInfo['Rows'][$k]['goodsSpecNames'] = str_replace('@@_@@', ';', $v['goodsSpecNames']);
				$appraise = Db::table('__GOODS_APPRAISES__')
							->field('goodsScore,serviceScore,timeScore,content,images,createTime')
							->where(['goodsId'=>$v['goodsId'],
									 'orderId'=>$orderId,
									 'dataFlag'=>1,
									 'isShow'=>1,
									 ])->find();
				if(!empty($appraise)){
					++$alreadys;
					$appraise['images'] = explode(',', $appraise['images']);
				}
				$goodsInfo['Rows'][$k]['appraise'] = $appraise;
			}
		}
		$goodsInfo['count'] = $count;
		$goodsInfo['alreadys'] = $alreadys;
		return $goodsInfo;

	}
	
	/**
	 * 检查订单是否已支付
	 */
	public function checkOrderPay (){
		$userId = (int)session('WST_USER.userId');
		$orderId = input("id");
		$isBatch = (int)input("isBatch");
		$rs = array();
		if($isBatch==1){
			$rs = $this->field('orderId,orderNo')->where(["userId"=>$userId,"orderunique"=>$orderId,"dataFlag"=>1,"orderStatus"=>-2,"isPay"=>0,"payType"=>1])->select();
		}else{
			$rs = $this->field('orderId,orderNo')->where(["userId"=>$userId,"orderId"=>$orderId,"dataFlag"=>1,"orderStatus"=>-2,"isPay"=>0,"payType"=>1])->select();
		}
		if(count($rs)>0){
			return WSTReturn('',1);
		}else{
			return WSTReturn('订单已支付',-1);
		}
	}
}
