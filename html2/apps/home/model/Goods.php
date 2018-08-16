<?php
namespace apps\home\model;
use think\Db;
use think\Loader;
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
 * 商品类
 */
class Goods extends Base{
     /**
      *  上架商品列表
      */
	public function saleByPage(){
		$shopId = (int)session('WST_USER.shopId');
		$where = [];
		$where['shopId'] = $shopId;
		$where['goodsStatus'] = 1;
		$where['dataFlag'] = 1;
		$where['isSale'] = 1;
		$c1Id = (int)input('cat1');
		$c2Id = (int)input('cat2');
		$goodsName = input('goodsName');
		if($goodsName != ''){
			$where['goodsName'] = ['like',"%$goodsName%"];
		}
		if($c2Id!=0 && $c1Id!=0){
			$where['shopCatId2'] = $c2Id;
		}else if($c1Id!=0){
			$where['shopCatId1'] = $c1Id;
		}
		$shopId = (int)session('WST_USER.shopId');
		$where['m.shopId'] = $shopId;
		$rs = $this->alias('m')
		    ->where($where)
			->field('goodsId,goodsName,goodsImg,goodsSn,isSale,isBest,isHot,isNew,isRecom,goodsStock,saleNum,shopPrice,isSpec')
			->order('saleTime', 'desc')
			->paginate(input('pagesize/d'))->toArray();
		foreach ($rs['Rows'] as $key => $v){
			$rs['Rows'][$key]['verfiycode'] = WSTShopEncrypt($shopId);
		}
		return $rs;
	}
	/**
	 * 审核中的商品
	 */
    public function auditByPage(){
    	$shopId = (int)session('WST_USER.shopId');
    	$where['shopId'] = $shopId;
    	$where['goodsStatus'] = 0;
		$where['dataFlag'] = 1;
		$where['isSale'] = 1;
		$c1Id = (int)input('cat1');
		$c2Id = (int)input('cat2');
		$goodsName = input('goodsName');
		if($goodsName != ''){
			$where['goodsName'] = ['like',"%$goodsName%"];
		}
		if($c2Id!=0 && $c1Id!=0){
			$where['shopCatId2'] = $c2Id;
		}else if($c1Id!=0){
			$where['shopCatId1'] = $c1Id;
		}

		$rs = $this->alias('m')
		    ->where($where)
			->field('goodsId,goodsName,goodsImg,goodsSn,isSale,isBest,isHot,isNew,isRecom,goodsStock,saleNum,shopPrice,isSpec')
			->order('saleTime', 'desc')
			->paginate(input('pagesize/d'))->toArray();
        foreach ($rs['Rows'] as $key => $v){
			$rs['Rows'][$key]['verfiycode'] =  WSTShopEncrypt($shopId);
		}
		return $rs;
	}
	/**
	 * 仓库中的商品
	 */
    public function storeByPage(){
    	$shopId = (int)session('WST_USER.shopId');
    	$where['shopId']=$shopId;
		$where['dataFlag'] = 1;
		$where['isSale'] = 0;
		$c1Id = (int)input('cat1');
		$c2Id = (int)input('cat2');
		$goodsName = input('goodsName');
		if($goodsName != ''){
			$where['goodsName'] = ['like',"%$goodsName%"];
		}
		if($c2Id!=0 && $c1Id!=0){
			$where['shopCatId2'] = $c2Id;
		}else if($c1Id!=0){
			$where['shopCatId1'] = $c1Id;
		}
		$rs = $this->alias('m')
		    ->where($where)
		    ->where('goodsStatus','<>',-1)
			->field('goodsId,goodsName,goodsImg,goodsSn,isSale,isBest,isHot,isNew,isRecom,goodsStock,saleNum,shopPrice,isSpec')
			->order('saleTime', 'desc')
			->paginate(input('pagesize/d'))->toArray();
        foreach ($rs['Rows'] as $key => $v){
			$rs['Rows'][$key]['verfiycode'] =  WSTShopEncrypt($shopId);
		}
		return $rs;
	}
	/**
	 * 违规的商品 
	 */
	public function illegalByPage(){
		$shopId = (int)session('WST_USER.shopId');
		$where['shopId'] = $shopId;
		$where['goodsStatus'] = -1;
		$where['dataFlag'] = 1;
		$where['isSale'] = 1;
		$c1Id = (int)input('cat1');
		$c2Id = (int)input('cat2');
		$goodsName = input('goodsName');
		if($goodsName != ''){
			$where['goodsName'] = ['like',"%$goodsName%"];
		}
		if($c2Id!=0 && $c1Id!=0){
			$where['shopCatId2'] = $c2Id;
		}else if($c1Id!=0){
			$where['shopCatId1'] = $c1Id;
		}

		$rs = $this->alias('m')
		    ->where($where)
			->field('goodsId,goodsName,goodsSn,isSale,isBest,isHot,isNew,isRecom,illegalRemarks,goodsStock,saleNum,shopPrice,isSpec')
			->order('saleTime', 'desc')
			->paginate(input('pagesize/d'))->toArray();
		foreach ($rs['Rows'] as $key => $v){
			$rs['Rows'][$key]['verfiycode'] = WSTShopEncrypt($shopId);
		}
		return $rs;
	}
	
	/**
	 * 新增商品
	 */
	public function add(){
		$shopId = (int)session('WST_USER.shopId');
		$data = input('post.');
		$specsIds = input('post.specsIds');
		WSTUnset($data,'goodsId,statusRemarks,goodsStatus,dataFlag');
		if(WSTConf("CONF.isGoodsVerify")==1){
			$data['goodsStatus'] = 0;
		}else{
			$data['goodsStatus'] = 1;
		}
		$data['shopId'] = $shopId;
		$data['saleTime'] = date('Y-m-d H:i:s');
		$data['createTime'] = date('Y-m-d H:i:s');
		$goodsCats = model('GoodsCats')->getParentIs($data['goodsCatId']);		
		$data['goodsCatIdPath'] = implode('_',$goodsCats)."_";
		$data['isSpec'] = ($specsIds!='')?1:0;
		Db::startTrans();
        try{
			$result = $this->validate(true)->allowField(true)->save($data);
			if(false !== $result){
				$goodsId = $this->goodsId;
				//商品图片
				WSTUseImages(0, $goodsId, $data['goodsImg']);
				//商品相册
				WSTUseImages(0, $goodsId, $data['gallery']);
				//商品描述图片
				WSTEditorImageRocord(0, $goodsId, '',$data['goodsDesc']);

				//建立商品评分记录
				$gs = [];
				$gs['goodsId'] = $goodsId;
				$gs['shopId'] = $shopId;
				model('GoodsScores')->save($gs);
				//如果有销售规格则保存销售和规格值
    	        if($specsIds!=''){
	    	        $specsIds = explode(',',$specsIds);
			    	$specsArray = [];
			    	foreach ($specsIds as $v){
			    		$vs = explode('-',$v);
			    		foreach ($vs as $vv){
			    		   if(!in_array($vv,$specsArray))$specsArray[] = $vv;
			    		}
			    	}
		    		//保存规格名称
		    		$m = model('SpecItems');
		    		$specMap = [];
		    		foreach ($specsArray as $v){
		    			$vv = explode('_',$v);
		    			$sitem = [];
		    			$sitem['shopId'] = $shopId;
		    			$sitem['catId'] = (int)$vv[0];
		    			$sitem['goodsId'] = $goodsId;
		    			$sitem['itemName'] = input('post.specName_'.$vv[0]."_".$vv[1]);
		    			$sitem['itemImg'] = input('post.specImg_'.$vv[0]."_".$vv[1]);
		    			$sitem['dataFlag'] = 1;
		    			$sitem['createTime'] = date('Y-m-d H:i:s');
		    			$spec = $m->create($sitem);
		    			if($sitem['itemImg']!='')WSTUseImages(0, $spec['itemId'], $sitem['itemImg']);
		    			$specMap[$v] = $spec['itemId'];
		    		}
		    		//保存销售规格
		    		$defaultPrice = 0;//最低价
		    		$totalStock = 0;//总库存
		    		$gspecArray = [];
		    		$isFindDefaultSpec = false;
		    		$defaultSpec = Input('post.defaultSpec');
		    		foreach ($specsIds as $v){
		    			$vs = explode('-',$v);
		    			$goodsSpecIds = [];
		    			foreach ($vs as $gvs){
		    				$goodsSpecIds[] = $specMap[$gvs];
		    			}
		    			$gspec = [];
		    			$gspec['specIds'] = implode(':',$goodsSpecIds);
		    			$gspec['shopId'] = $shopId;
		    			$gspec['goodsId'] = $goodsId;
		    			$gspec['productNo'] = Input('productNo_'.$v);
		    			$gspec['marketPrice'] = (float)Input('marketPrice_'.$v);
		    			$gspec['specPrice'] = (float)Input('specPrice_'.$v);
		    			$gspec['specStock'] = (int)Input('specStock_'.$v);
		    			$gspec['warnStock'] = (int)Input('warnStock_'.$v);
		    			//设置默认规格
		    			if($defaultSpec==$v){
		    				$isFindDefaultSpec = true;
		    				$defaultPrice = $gspec['specPrice'];
		    				$gspec['isDefault'] = 1;
		    			}
                        $gspecArray[] = $gspec;
                        //获取总库存
                        $totalStock = $totalStock + $gspec['specStock'];
		    		}
		    		if(!$isFindDefaultSpec)return WSTReturn("请选择推荐规格");
		    		if(count($gspecArray)>0){
		    		    model('GoodsSpecs')->saveAll($gspecArray);
		    		    //更新默认价格和总库存
    	                $this->where('goodsId',$goodsId)->update(['isSpec'=>1,'shopPrice'=>$defaultPrice,'goodsStock'=>$totalStock]);
		    		}
    	        }
    	        //保存商品属性
		    	$attrsArray = [];
		    	$attrRs = model('Attributes')->where(['goodsCatId'=>['in',$goodsCats],'isShow'=>1,'dataFlag'=>1,'attrType'=>['<>',0]])
		    		            ->field('attrId')->select();
		    	foreach ($attrRs as $key =>$v){
		    		$attrs = [];
		    		$attrs['attrVal'] = input('attr_'.$v['attrId']);
		    		if($attrs['attrVal']=='')continue;
		    		$attrs['shopId'] = $shopId;
		    		$attrs['goodsId'] = $goodsId;
		    		$attrs['attrId'] = $v['attrId'];
		    		$attrs['createTime'] = date('Y-m-d H:i:s');
		    		$attrsArray[] = $attrs;
		    	}
		    	if(count($attrsArray)>0)model('GoodsAttributes')->saveAll($attrsArray);
    	        Db::commit();
				return WSTReturn("新增成功", 1);
			}else{
				return WSTReturn($this->getError(),-1);
			}
        }catch (\Exception $e) {
            Db::rollback();
            return WSTReturn('新增失败',-1);
        }
	}
	
	/**
	 * 编辑商品资料
	 */
	public function edit(){
		$shopId = (int)session('WST_USER.shopId');
	    $goodsId = input('post.goodsId/d');
	    $specsIds = input('post.specsIds');
		$data = input('post.');
		WSTUnset($data,'goodsId,dataFlag,statusRemarks,goodsStatus,createTime');
		$ogoods = $this->where('goodsId',$goodsId)->field('goodsStatus')->find();
		//违规商品不能直接上架
		if($ogoods['goodsStatus']!=1){
			$data['goodsStatus'] = 0;
		}
		$data['saleTime'] = date('Y-m-d H:i:s');
		$goodsCats = model('GoodsCats')->getParentIs($data['goodsCatId']);
		$data['goodsCatIdPath'] = implode('_',$goodsCats)."_";
		$data['isSpec'] = ($specsIds!='')?1:0;
		Db::startTrans();
        try{
        	//商品图片
			WSTUseImages(0, $goodsId, $data['goodsImg'],'goods','goodsImg');
			//商品相册
			WSTUseImages(0, $goodsId, $data['gallery'],'goods','gallery');
			// 商品描述图片
	        $desc = $this->where('goodsId',$goodsId)->value('goodsDesc');
			WSTEditorImageRocord(0, $goodsId, $desc, $data['goodsDesc']);


			$result = $this->validate(true)->allowField(true)->save($data,['goodsId'=>$goodsId]);
			if(false !== $result){
				/**
				 * 编辑的时候如果不想影响商品销售规格的销量，那么就要在保存的时候区别对待已经存在的规格和销售规格记录。
				 * $specNameMap的保存关系是：array('页面上生成的规格值ID'=>数据库里规则值的ID)
				 * $specIdMap的保存关系是:array('页面上生成的销售规格ID'=>数据库里销售规格ID)
				 */
				$specNameMapTmp = explode(',',input('post.specmap'));
				$specIdMapTmp = explode(',',input('post.specidsmap'));
				$specNameMap = [];//规格值对应关系
				$specIdMap = [];//规格和表对应关系
				foreach ($specNameMapTmp as $key =>$v){
					if($v=='')continue;
					$v = explode(':',$v);
					$specNameMap[$v[1]] = $v[0];   //array('页面上的规则值ID'=>数据库里规则值的ID)
				}
				foreach ($specIdMapTmp as $key =>$v){
					if($v=='')continue;
					$v = explode(':',$v);
					$specIdMap[$v[1]] = $v[0];     //array('页面上的销售规则ID'=>数据库里销售规格ID)
				}
				//如果有销售规格则保存销售和规格值
    	        if($specsIds!=''){
    	        	//把之前之前的销售规格
	    	        $specsIds = explode(',',$specsIds);
			    	$specsArray = [];
			    	foreach ($specsIds as $v){
			    		$vs = explode('-',$v);
			    		foreach ($vs as $vv){
			    		   if(!in_array($vv,$specsArray))$specsArray[] = $vv;//过滤出不重复的规格值
			    		}
			    	}
			    	$m = model('SpecItems');
			    	//先标记作废之前的规格值
			    	$m->where(['shopId'=>$shopId,'goodsId'=>$goodsId])->update(['dataFlag'=>-1]);
		    		//保存规格名称
		    		$specMap = [];
		    		foreach ($specsArray as $v){
		    			$vv = explode('_',$v);
		    			$specNumId = $vv[0]."_".$vv[1];
		    			$sitem = [];
		    			$sitem['itemName'] = input('post.specName_'.$specNumId);
		    			$sitem['itemImg'] = input('post.specImg_'.$specNumId);
		    			//如果已经存在的规格值则修改，否则新增
		    			if(isset($specNameMap[$specNumId]) && (int)$specNameMap[$specNumId]!=0){
		    				$sitem['dataFlag'] = 1;
		    				WSTUseImages(0, (int)$specNameMap[$specNumId], $sitem['itemImg'],'spec_items','itemImg');
		    				$m->where(['shopId'=>$shopId,'itemId'=>(int)$specNameMap[$specNumId]])->update($sitem);
		    				$specMap[$v] = (int)$specNameMap[$specNumId];
		    			}else{
		    				$sitem['goodsId'] = $goodsId;
		    				$sitem['shopId'] = $shopId;
		    			    $sitem['catId'] = (int)$vv[0];
		    				$sitem['dataFlag'] = 1;
		    			    $sitem['createTime'] = date('Y-m-d H:i:s');
		    			    $spec = $m->create($sitem);
		    			    if($sitem['itemImg']!='')WSTUseImages(0, $spec['itemId'], $sitem['itemImg']);
		    			    $specMap[$v] = $spec['itemId'];
		    			}
		    		}
		    		//删除已经作废的规格值
		    		$m->where(['shopId'=>$shopId,'goodsId'=>$goodsId,'dataFlag'=>-1])->delete();
		    		//保存销售规格
		    		$defaultPrice = 0;//默认价格
		    		$totalStock = 0;//总库存
		    		$gspecArray = [];
		    		$m = model('GoodsSpecs');
		    		//把之前的销售规格值标记删除
		    		$m->where(['goodsId'=>$goodsId,'shopId'=>$shopId])->update(['dataFlag'=>-1,'isDefault'=>0]);
		    		$isFindDefaultSpec = false;
		    		$defaultSpec = Input('post.defaultSpec');
		    		foreach ($specsIds as $v){
		    			$vs = explode('-',$v);
		    			$goodsSpecIds = [];
		    			foreach ($vs as $gvs){
		    				$goodsSpecIds[] = $specMap[$gvs];
		    			}
		    			$gspec = [];
		    			$gspec['specIds'] = implode(':',$goodsSpecIds);
		    			$gspec['productNo'] = Input('productNo_'.$v);
			    		$gspec['marketPrice'] = (float)Input('marketPrice_'.$v);
			    		$gspec['specPrice'] = (float)Input('specPrice_'.$v);
			    		$gspec['specStock'] = (int)Input('specStock_'.$v);
			    		$gspec['warnStock'] = (int)Input('warnStock_'.$v);
			    		//设置默认规格
			    		if($defaultSpec==$v){
			    			$gspec['isDefault'] = 1;
			    			$isFindDefaultSpec = true;
		    				$defaultPrice = $gspec['specPrice'];
			    		}
			    		//如果是已经存在的值就修改内容，否则新增
		    			if(isset($specIdMap[$v]) && $specIdMap[$v]!=''){
		    				$gspec['dataFlag'] = 1;
		    				
		    				$m->where(['shopId'=>$shopId,'id'=>(int)$specIdMap[$v]])->update($gspec);
		    			}else{
			    			$gspec['shopId'] = $shopId;
			    			$gspec['goodsId'] = $goodsId;
			    			$gspecArray[] = $gspec;
		    			}
                        //获取总库存
                        $totalStock = $totalStock + $gspec['specStock'];
		    		}
		    		if(!$isFindDefaultSpec)return WSTReturn("请选择推荐规格");
		    		//删除作废的销售规格值
		    		$m->where(['goodsId'=>$goodsId,'shopId'=>$shopId,'dataFlag'=>-1])->delete();
		    		if(count($gspecArray)>0){
		    		    $m->saveAll($gspecArray);
		    		}
		    		//更新推荐规格和总库存
    	            $this->where('goodsId',$goodsId)->update(['isSpec'=>1,'shopPrice'=>$defaultPrice,'goodsStock'=>$totalStock]);
    	        }
    	        //保存商品属性
    	        $m = model('GoodsAttributes');
    	        //删除之前的商品属性
    	        $m->where(['goodsId'=>$goodsId,'shopId'=>$shopId])->delete();
    	        //新增商品属性
		    	$attrsArray = [];
		    	$attrRs = model('Attributes')->where(['goodsCatId'=>['in',$goodsCats],'isShow'=>1,'dataFlag'=>1])
		    		            ->field('attrId')->select();
		    	foreach ($attrRs as $key =>$v){
		    		$attrs = [];
		    		$attrs['attrVal'] = input('attr_'.$v['attrId']);
		    		if($attrs['attrVal']=='')continue;
		    		$attrs['shopId'] = $shopId;
		    		$attrs['goodsId'] = $goodsId;
		    		$attrs['attrId'] = $v['attrId'];
		    		$attrs['createTime'] = date('Y-m-d H:i:s');
		    		$attrsArray[] = $attrs;
		    	}
		    	if(count($attrsArray)>0)$m->saveAll($attrsArray);
				Db::commit();
				return WSTReturn("编辑成功", 1);
			}else{
				return WSTReturn($this->getError(),-1);
			}
	    }catch (\Exception $e) {
	    	print_r($e);
        	Db::rollback();
            return WSTReturn('编辑失败',-1);
        }
	}
	
	/**
	 * 获取商品资料方便编辑
	 */
	public function getById($goodsId){
		$rs = $this->where(['shopId'=>(int)session('WST_USER.shopId'),'goodsId'=>$goodsId])->find();
		if(!empty($rs)){
			if($rs['gallery']!='')$rs['gallery'] = explode(',',$rs['gallery']);
			//获取规格值
			$specs = Db::table('__SPEC_CATS__')->alias('gc')->join('__SPEC_ITEMS__ sit','gc.catId=sit.catId','inner')
			                      ->where(['sit.goodsId'=>$goodsId,'gc.isShow'=>1,'sit.dataFlag'=>1])
			                      ->field('gc.isAllowImg,sit.catId,sit.itemId,sit.itemName,sit.itemImg')
			                      ->order('gc.isAllowImg desc,gc.catSort asc,gc.catId asc')->select();
			$spec0 = [];
			$spec1 = [];                      
			foreach ($specs as $key =>$v){
				if($v['isAllowImg']==1){
					$spec0[] = $v;
				}else{
					$spec1[] = $v;
				}
			}
			$rs['spec0'] = $spec0;
			$rs['spec1'] = $spec1;
			//获取销售规格
			$rs['saleSpec'] = model('goodsSpecs')->where('goodsId',$goodsId)->field('id,isDefault,productNo,specIds,marketPrice,specPrice,specStock,warnStock,saleNum')->select();
			//获取属性值
			$rs['attrs'] = model('goodsAttributes')->where('goodsId',$goodsId)->field('attrId,attrVal')->select();
		}
		return $rs;
	}
	/**
	 * 获取商品资料在前台展示
	 */
     public function getBySale($goodsId){
     	$key = input('key');
     	// 浏览量
     	$this->where('goodsId',$goodsId)->setInc('visitNum',1);
		$rs = Db::table('__GOODS__')->where(['goodsId'=>$goodsId,'dataFlag'=>1])->find();
		if(!empty($rs)){
			$rs['read'] = false;
			//判断是否可以公开查看
			$viKey = WSTShopEncrypt($rs['shopId']);
			if(($rs['isSale']==0 || $rs['goodsStatus']==0) && $viKey != $key)return [];
			if($key!='')$rs['read'] = true;
			//获取店铺信息
			$rs['shop'] = model('shops')->getBriefShop((int)$rs['shopId']);

			if(empty($rs['shop']))return [];
			$gallery = [];
			$gallery[] = $rs['goodsImg'];
			if($rs['gallery']!=''){
				$tmp = explode(',',$rs['gallery']);
				$gallery = array_merge($gallery,$tmp);
			}
			$rs['gallery'] = $gallery;
			//获取规格值
			$specs = Db::table('__SPEC_CATS__')->alias('gc')->join('__SPEC_ITEMS__ sit','gc.catId=sit.catId','inner')
			                      ->where(['sit.goodsId'=>$goodsId,'gc.isShow'=>1,'sit.dataFlag'=>1])
			                      ->field('gc.isAllowImg,gc.catName,sit.catId,sit.itemId,sit.itemName,sit.itemImg')
			                      ->order('gc.isAllowImg desc,gc.catSort asc,gc.catId asc')->select();                     
			foreach ($specs as $key =>$v){
				$rs['spec'][$v['catId']]['name'] = $v['catName'];
				$rs['spec'][$v['catId']]['list'][] = $v;
			}
			//获取销售规格
			$sales = Db::table('__GOODS_SPECS__')->where('goodsId',$goodsId)->field('id,isDefault,productNo,specIds,marketPrice,specPrice,specStock')->select();
			if(!empty($sales)){
				foreach ($sales as $key =>$v){
					$str = explode(':',$v['specIds']);
					sort($str);
					unset($v['specIds']);
					$rs['saleSpec'][implode(':',$str)] = $v;
				}
			}
			//获取商品属性
			$rs['attrs'] = Db::table('__ATTRIBUTES__')->alias('a')->join('__GOODS_ATTRIBUTES__ ga','a.attrId=ga.attrId','inner')
			                   ->where(['a.isShow'=>1,'dataFlag'=>1,'goodsId'=>$goodsId])->field('a.attrName,ga.attrVal')
			                   ->order('attrSort asc')->select();
			//获取商品评分
			$rs['scores'] = model('goods_scores')->where('goodsId',$goodsId)->field('totalScore,totalUsers')->find();
			$rs['scores']['totalScores'] = ($rs['scores']['totalScore']==0)?5:WSTScore($rs['scores']['totalScore'],$rs['scores']['totalUsers'],5,0,3);
			WSTUnset($rs, 'totalUsers');
			//关注
			$f = new Favorites();
			$rs['favShop'] = $f->checkFavorite($rs['shopId'],1);
			$rs['favGood'] = $f->checkFavorite($goodsId,0);
		}
		return $rs;
	}
	
	/**
	 * 删除商品
	 */
	public function del(){
	    $id = input('post.id/d');
		$data = [];
		$data['dataFlag'] = -1;
		Db::startTrans();
		try{
		    $result = $this->update($data,['goodsId'=>$id]);
	        if(false !== $result){
	        	WSTUnuseImage('goods','goodsImg',$id);
	        	WSTUnuseImage('goods','gallery',$id);
	        	// 商品描述图片
	        	$desc = $this->where('goodsId',$id)->value('goodsDesc');
				WSTEditorImageRocord(0, $id, $desc,'');
				Db::commit();
	        	//标记删除购物车
	        	return WSTReturn("删除成功", 1);
	        }
		}catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('删除失败',-1);
	}
	/**
	  * 批量删除商品
	  */
	 public function batchDel(){
	 	$shopId = (int)session('WST_USER.shopId');
	   	$ids = input('post.ids/a');
	   	Db::startTrans();
		try{
		   	$rs = $this->where(['goodsId'=>['in',$ids],
		   						'shopId'=>$shopId])->setField('dataFlag',-1);
			if(false !== $rs){
				//标记删除购物车
				foreach ($ids as $v){
					WSTUnuseImage('goods','goodsImg',(int)$v);
	        	    WSTUnuseImage('goods','gallery',(int)$v);
	        	    // 商品描述图片
		        	$desc = $this->where('goodsId',(int)$v)->value('goodsDesc');
					WSTEditorImageRocord(0, (int)$v, $desc,'');
				}
				Db::commit();
	        	return WSTReturn("删除成功", 1);
	        }
		}catch (\Exception $e) {
            Db::rollback();
        }
        return WSTReturn('删除失败',-1);
	 }
	
	/**
	 * 批量上架商品
	 */
	public function changeSale(){
		$ids = input('post.ids/a');
		$isSale = (int)input('post.isSale',1);
		//判断商品是否满足上架要求
		if($isSale==1){
			$shopId = (int)session('WST_USER.shopId');
			//0.核对店铺状态
	 		$shopRs = model('shops')->find($shopId);
	 		if($shopRs['shopStatus']!=1){
	 			return 	WSTReturn('上架商品失败!您的店铺权限不能出售商品，如有疑问请与商城管理员联系。',-3);
	 		}
	 		//直接设置上架 返回受影响条数
	 		$where = [];
	 		$where['g.goodsId'] = ['in',$ids];
	 		$where['gc.dataFlag'] = 1;
	 		$where['gc.isShow'] = 1;
	 		$where['g.goodsImg'] = ['<>',""];
			$rs = $this->alias('g')
				  ->join('__GOODS_CATS__ gc','g.goodsCatId=gc.CatId','inner')
				  ->where($where)->setField('isSale',1);
			if($rs!==false){
				$status = ($rs==count($ids))?1:2;
				if($status==1){
					return WSTReturn('商品上架成功', 1,['num'=>$rs]);
				}else{
					return WSTReturn('已成功上架商品'.$rs.'件，请核对未能上架的商品信息是否完整。', 2,['num'=>$rs]);
				}
			}else{
	 			return WSTReturn('上架失败，请核对商品信息是否完整!', -2);
	 		}

		}else{
			$rs = $this->where(['goodsId'=>['in',$ids]])->setField('isSale',$isSale);
			if($rs !== false){
				return WSTReturn('商品上架成功', 1);
			}else{
				return WSTReturn($this->getError(), -1);
			}
		}
	}
	/**
	* 修改商品状态
	*/
	public function changSaleStatus(){
		$is = input('post.is');
		$status = (input('post.status',1)==1)?0:1;
		$id = (int)input('post.id');
		$rs = $this->setField([$is=>$status,'goodsId'=>$id]);
		if($rs!==false){
			return WSTReturn('设置成功',1);
		}else{
			return WSTReturn($this->getError(),-1);
		}
	}
	/**
	 * 批量修改商品状态
	 */
	public function changeGoodsStatus(){
		//设置为什么 hot new best rec
		$allowArr = ['isHot','isNew','isBest','isRecom'];
		$is = input('post.is');
		if(!in_array($is,$allowArr))return WSTReturn('非法操作',-1);
		//设置哪一个状态
		$status = input('post.status',1);
		$ids = input('post.ids/a');
		$rs = $this->where(['goodsId'=>['in',$ids]])->setField($is, $status);
		if($rs!==false){
			return WSTReturn('设置成功',1);
		}else{
			return WSTReturn($this->getError(),-1);
		}

	}
	/**
	 * 获取商品规格属性
	 */
	public function getSpecAttrs(){
		$goodsCatId = Input('post.goodsCatId/d');
		$goodsCatIds = model('GoodsCats')->getParentIs($goodsCatId);
		$data = [];
		$specs = model('SpecCats')->where(['dataFlag'=>1,'isShow'=>1,'goodsCatId'=>['in',$goodsCatIds]])->field('catId,catName,isAllowImg')->order('isAllowImg desc,catSort asc,catId asc')->select();
		$spec0 = null;
		$spec1 = [];
		foreach ($specs as $key => $v){
			if($v['isAllowImg']==1){
				$spec0 = $v;
			}else{
				$spec1[] = $v;
			}
		}
		$data['spec0'] = $spec0;
		$data['spec1'] = $spec1;
		$data['attrs'] = model('Attributes')->where(['dataFlag'=>1,'isShow'=>1,'goodsCatId'=>['in',$goodsCatIds]])->field('attrId,attrName,attrType,attrVal')->order('attrSort asc,attrId asc')->select();
	    return WSTReturn("", 1,$data);
	}
	
	/**
	 * 检测商品主表的货号或者商品编号
	 */
	public function checkExistGoodsKey($key,$val,$id = 0){
		if(!in_array($key,array('goodsSn','productNo')))return WSTReturn("非法的查询字段");
		$conditon = [$key=>$val];
		if($id>0)$conditon['goodsId'] = ['<>',$id];
		$rs = $dbo = $this->where($conditon)->count();
		return ($rs==0)?false:true;
	}
	
	/**
	 * 获取分页商品记录
	 */
	public function pageQuery($goodsCatIds = []){
		//查询条件
		$isStock = input('isStock/d');
		$isNew = input('isNew/d');
		$keyword = input('keyword');
		$where = $where2 = $where3 = [];
		$where['goodsStatus'] = 1;
		$where['g.dataFlag'] = 1;
		$where['isSale'] = 1;
		$where['goodsName'] = ['like','%'.$keyword.'%'];

		// 发货地
		$areaId = (int)input('areaId');
		if($areaId>0){
			$where['areaId'] = $areaId;
		}

		//排序条件
		$orderBy = input('orderBy/d',0);
		$orderBy = ($orderBy>=0 && $orderBy<=4)?$orderBy:0;
		$order = (input('order/d',0)==1)?1:0;
		$pageBy = ['saleNum','shopPrice','appraiseNum','visitNum','saleTime'];
		$pageOrder = ['asc','desc'];
		if($isStock==1)$where['goodsStock'] = ['>',0];
		if($isNew==1)$where['isNew'] = ['=',1];
		if(!empty($goodsCatIds))$where['goodsCatIdPath'] = ['like',implode('_',$goodsCatIds).'_%'];
	    $sprice = input("param.sprice");//开始价格
	    $eprice = input("param.eprice");//结束价格
		if($sprice!='')$where2 = "g.shopPrice >= ".(int)$sprice;
		if($eprice!='')$where3 = "g.shopPrice <= ".(int)$eprice;
		$list = Db::table("__GOODS__")->alias('g')->join("__SHOPS__ s","g.shopId = s.shopId")
			->where($where)->where($where2)->where($where3)
			->field('goodsId,goodsName,goodsSn,goodsStock,saleNum,shopPrice,marketPrice,isSpec,goodsImg,appraiseNum,visitNum,s.shopId,shopName')
			->order($pageBy[$orderBy]." ".$pageOrder[$order].",goodsId asc")
			->paginate(input('pagesize/d'))->toArray();

		return $list;
	}
	/**
	 * 获取价格范围
	 */
	public function getPriceGrade($goodsCatIds = []){
		$isStock = input('isStock/d');
		$isNew = input('isNew/d');
		$keyword = input('keyword');
		$where = $where2 = $where3 = [];
		$where['goodsStatus'] = 1;
		$where['g.dataFlag'] = 1;
		$where['isSale'] = 1;
		$where['goodsName'] = ['like','%'.$keyword.'%'];
		$where['areaId'] = (int)input('areaId');

		//排序条件
		$orderBy = input('orderBy/d',0);
		$orderBy = ($orderBy>=0 && $orderBy<=4)?$orderBy:0;
		$order = (input('order/d',0)==1)?1:0;
		$pageBy = ['saleNum','shopPrice','appraiseNum','visitNum','saleTime'];
		$pageOrder = ['asc','desc'];
		if($isStock==1)$where['goodsStock'] = ['>',0];
		if($isNew==1)$where['isNew'] = ['=',1];
		if(!empty($goodsCatIds))$where['goodsCatIdPath'] = ['like',implode('_',$goodsCatIds).'_%'];
		$sprice = input("param.sprice");//开始价格
	    $eprice = input("param.eprice");//结束价格
		if($sprice!='')$where2 = "g.shopPrice >= ".(int)$sprice;
		if($eprice!='')$where3 = "g.shopPrice <= ".(int)$eprice;
		$rs = Db::table("__GOODS__")->alias('g')->join("__SHOPS__ s","g.shopId = s.shopId",'inner')
			->where($where)->where($where2)->where($where3)
			->field('min(shopPrice) minPrice,max(shopPrice) maxPrice')->find();
		
		if($rs['maxPrice']=='')return;
		$minPrice = 0;
		$maxPrice = $rs['maxPrice'];
		$pavg5 = ($maxPrice/5);
		$prices = array();
    	$price_grade = 0.0001;
        for($i=-2; $i<= log10($maxPrice); $i++){
            $price_grade *= 10;
        }
    	//区间跨度
        $span = ceil(($maxPrice - $minPrice) / 8 / $price_grade) * $price_grade;
        if($span == 0){
            $span = $price_grade;
        }
		for($i=1;$i<=8;$i++){
			$prices[($i-1)*$span."_".($span * $i)] = ($i-1)*$span."-".($span * $i);
			if(($span * $i)>$maxPrice) break;
		}

		return $prices;
	}

	/**
	 * 修改商品库存/价格
	 */
	public function editGoodsBase(){
		$goodsId = (int)Input("goodsId");
		$post = input('post.');
		$data = [];
		if(isset($post['goodsStock'])){
			$data['goodsStock'] = (int)input('post.goodsStock',0);
		}elseif(isset($post['shopPrice'])){
			$data['shopPrice'] = (int)input('post.shopPrice',0);
		}else{
			return WSTReturn('操作失败',-1);
		}
		$rs = $this->update($data,['goodsId'=>$goodsId]);
		if($rs!==false){
			return WSTReturn('操作成功',1);
		}else{
			return WSTReturn('操作失败',-1);
		}
	}

	/**
	 * 获取店铺商品列表
	 */
	public function shopGoods($shopId){
		$msort = input("param.msort/d");
		$mdesc = input("param.mdesc/d");
		$order = array('g.saleTime'=>'desc');
		$orderFile = array('1'=>'g.isHot','2'=>'g.saleNum','3'=>'g.shopPrice','4'=>'g.shopPrice','5'=>'(gs.totalScore/gs.totalUsers)','6'=>'g.saleTime');
	   	$orderSort = array('0'=>'asc','1'=>'desc');
		if($msort>0){
			$order = array($orderFile[$msort]=>$orderSort[$mdesc]);
		}
		$goodsName = input("param.goodsName");//搜索店鋪名
		$words = $where = $where2 = $where3 = [];
		if($goodsName!=""){
			$words = explode(" ",$goodsName);
		}
		if(!empty($words)){
			$sarr = array();
			foreach ($words as $key => $word) {
				if($word!=""){
					$sarr[] = "g.goodsName like '%$word%'";
				}
			}
			$where = implode(" or ", $sarr);
		}
		$sprice = input("param.sprice");//开始价格
		$eprice = input("param.eprice");//结束价格
		if($sprice!="")$where2 = "g.shopPrice >= ".(float)$sprice;
		if($eprice!="")$where3 = "g.shopPrice <= ".(float)$eprice;
		$ct1 = input("param.ct1/d");
		$ct2 = input("param.ct2/d");
		if($ct1>0)$where['shopCatId1'] = $ct1;
		if($ct2>0)$where['shopCatId2'] = $ct2;
		$goods = Db::table('__GOODS__')->alias('g')
		->join('__GOODS_SCORES__ gs','gs.goodsId = g.goodsId','left')
		->where(['g.shopId'=>$shopId,'g.isSale'=>1,'g.goodsStatus'=>1,'g.dataFlag'=>1])
		->where($where)->where($where2)->where($where3)
		->field('g.goodsId,g.goodsName,g.goodsImg,g.shopPrice,g.marketPrice,g.saleNum,g.appraiseNum,g.goodsStock')
		->order($order)
		->paginate(20)->toArray();
		return  $goods;
	}
	/**
	 * 上传商品数据
	 */
	public function importGoods($data){
		Loader::import('phpexcel.phpexcel.PHPExcel.IOFactory');
		$objReader = \PHPExcel_IOFactory::load(WSTRootPath().json_decode($data)->route.json_decode($data)->name);
		$objReader->setActiveSheetIndex(0);
		$sheet = $objReader->getActiveSheet();
		$rows = $sheet->getHighestRow();
		$cells = $sheet->getHighestColumn();
		//数据集合
        $readData = [];
        $shopId = (int)session('WST_USER.shopId');
        $importNum = 0;
        $goodsCatMap = []; //记录最后一级商品分类
        $goodsCatPathMap = [];//记录商品分类路径
        $shopCatMap = [];//记录店铺分类
        $goodsCat1Map = [];//记录最后一级商品分类对应的一级分类
        $tmpGoodsCatId = 0;
        $goodsCatBrandMap = [];//商品分类和品牌的对应关系
        //生成订单
		Db::startTrans();
		try{
	        //循环读取每个单元格的数据
	        for ($row = 3; $row <= $rows; $row++){//行数是以第3行开始
	        	$tmpGoodsCatId = 0;
	        	$goods = [];
	            $goods['shopId'] = $shopId;
	            $goods['goodsName'] = trim($sheet->getCell("A".$row)->getValue());
	            if($goods['goodsName']=='')break;//如果某一行第一列为空则停止导入
	            $goods['goodsSn'] = trim($sheet->getCell("B".$row)->getValue());
	            $goods['productNo'] = trim($sheet->getCell("C".$row)->getValue());
	            $goods['marketPrice'] = trim($sheet->getCell("D".$row)->getValue());
	            $goods['shopPrice'] = trim($sheet->getCell("E".$row)->getValue());
	            $goods['goodsStock'] = trim($sheet->getCell("F".$row)->getValue());
	            $goods['warnStock'] = trim($sheet->getCell("G".$row)->getValue());
	            $goods['goodsUnit'] = trim($sheet->getCell("H".$row)->getValue());
	            $goods['goodsSeoKeywords'] = trim($sheet->getCell("I".$row)->getValue());
	            $goods['goodsTips'] = trim($sheet->getCell("J".$row)->getValue());
	            $goods['isRecom'] = (trim($sheet->getCell("K".$row)->getValue())!='')?1:0;
	            $goods['isBest'] = (trim($sheet->getCell("L".$row)->getValue())!='')?1:0;
	            $goods['isNew'] = (trim($sheet->getCell("M".$row)->getValue())!='')?1:0;
	            $goods['isHot'] = (trim($sheet->getCell("N".$row)->getValue())!='')?1:0;
	            //查询商城分类
	            $goodsCat = trim($sheet->getCell("O".$row)->getValue());
	            if(!empty($goodsCat)){
	            	//先判断集合是否存在，不存在的时候才查数据库
	            	if(isset($goodsCatMap[$goodsCat])){
	            		$goods['goodsCatId'] = $goodsCatMap[$goodsCat];
		            	$goods['goodsCatIdPath'] = $goodsCatPathMap[$goodsCat];
		            	$tmpGoodsCatId = $goodsCat1Map[$goodsCat];
	            	}else{
		            	$goodsCatId = Db::table('__GOODS_CATS__')->where(['catName'=>$goodsCat,'dataFlag'=>1])->field('catId')->find();
		            	if(!empty($goodsCatId['catId'])){
		            		$goodsCats = model('GoodsCats')->getParentIs($goodsCatId['catId']);
		            		$goods['goodsCatId'] = $goodsCatId['catId'];
		            		$goods['goodsCatIdPath'] = implode('_',$goodsCats)."_";
		            		//放入集合
		            		$goodsCatMap[$goodsCat] = $goodsCatId['catId'];
		            		$goodsCatPathMap[$goodsCat] = implode('_',$goodsCats)."_";
		            		$goodsCat1Map[$goodsCat] = $goodsCats[0];
		            		$tmpGoodsCatId = $goodsCats[0];
		            	}
	            	}
	            }
	            //查询商城分类
	            $shopGoodsCat = trim($sheet->getCell("P".$row)->getValue());
	            if(!empty($shopGoodsCat)){
	            	//先判断集合是否存在，不存在的时候才查数据库
	            	if(isset($shopCatMap[$shopGoodsCat])){
	            		$goods['shopCatId1'] = $shopCatMap[$shopGoodsCat]['s1'];
		            	$goods['shopCatId2'] = $shopCatMap[$shopGoodsCat]['s2'];
	            	}else{
		            	$shopCat= Db::table("__SHOP_CATS__")->alias('sc1')
		            	->join('__SHOP_CATS__ sc2','sc2.parentId=sc1.catId','left')
		            	->field('sc1.catId catId1,sc2.catId catId2,sc2.catName')
		            	->where(['sc1.shopId'=> $shopId,'sc1.dataFlag'=>1,'sc2.catName'=>$shopGoodsCat])
		            	->find();
		            	if(!empty($shopCat)){
		            		$goods['shopCatId1'] = $shopCat['catId1'];
		            		$goods['shopCatId2'] = $shopCat['catId2'];
		            		//放入集合
		            		$shopCatMap[$shopGoodsCat] = [];
		            		$shopCatMap[$shopGoodsCat]['s1'] = $goods['shopCatId1'];
		            		$shopCatMap[$shopGoodsCat]['s2'] = $goods['shopCatId2'];
		            	}
	            	}
	            }
	            //查询品牌
	            $brand = trim($sheet->getCell("Q".$row)->getValue());
	            if(!empty($brand)){
	            	if(isset($goodsCatBrandMap[$brand])){
		            	$goods['brandId'] = $goodsCatBrandMap[$brand];
	            	}else{
	            	    $brands = Db::table('__BRANDS__')->alias('a')->join('__CAT_BRANDS__ cb','a.brandId=cb.brandId','inner')
		            	            ->where(['catId'=>$tmpGoodsCatId,'brandName'=>$brand,'dataFlag'=>1])->field('a.brandId')->find();
		            	if(!empty($brands)){
		            		$goods['brandId'] = $brands['brandId'];
		            		$goodsCatBrandMap[$brand] = $brands['brandId'];
		            	}
	            	}
	            }
	            $goods['goodsDesc'] = trim($sheet->getCell("R".$row)->getValue());
	            $goods['isSale'] = 0;
	            $goods['goodsStatus'] = (WSTConf("CONF.isGoodsVerify")==1)?0:1;
	            $goods['dataFlag'] = 1;
	            $goods['saleTime'] = date('Y-m-d H:i:s');
	            $goods['createTime'] = date('Y-m-d H:i:s');
	            $readData[] = $goods;
	            $importNum++;
	        }
            if(count($readData)>0){
            	$list = $this->saveAll($readData);
            	//建立商品评分记录
            	$goodsScores = [];
            	foreach ($list as $key =>$v){
					$gs = [];
					$gs['goodsId'] = $v['goodsId'];
					$gs['shopId'] = $shopId;
					$goodsScores[] = $gs;
            	}
            	if(count($goodsScores)>0)model('GoodsScores')->saveAll($goodsScores);
            }
            Db::commit();
            return json_encode(['status'=>1,'importNum'=>$importNum]);
		}catch (\Exception $e) {
            Db::rollback();
            print_r($e);
            return json_encode(WSTReturn('导入商品失败',-1));
        }
	}
	/**
	 *  预警库存列表
	 */
	public function stockByPage(){
		$where = [];
		$c1Id = (int)input('cat1');
		$c2Id = (int)input('cat2');
		$shopId = (int)session('WST_USER.shopId');
		if($c1Id!=0)$where[] = " shopCatId1=".$c1Id;
		if($c2Id!=0)$where[] = " shopCatId2=".$c2Id;
		$where[] = " g.shopId = ".$shopId;
		$prefix = config('database.prefix');
		$sql1 = 'SELECT g.goodsId,g.goodsName,g.goodsImg,gs.specStock goodsStock ,gs.warnStock warnStock,g.isSpec,gs.productNo,gs.id,gs.specIds,g.isSale
                    FROM '.$prefix.'goods g inner JOIN '.$prefix.'goods_specs gs ON gs.goodsId=g.goodsId and gs.specStock <= gs.warnStock and gs.warnStock>0
                    WHERE g.dataFlag = 1 and '.implode(' and ',$where);
		
		$sql2 = 'SELECT g.goodsId,g.goodsName,g.goodsImg,g.goodsStock,g.warnStock,g.isSpec,g.productNo,0 as id,"" as specIds,g.isSale
                    FROM '.$prefix.'goods g 
                    WHERE g.dataFlag = 1  and isSpec=0 and g.goodsStock<=g.warnStock 
                    and g.warnStock>0 and '.implode(' and ',$where);
		$page = (int)input('post.'.config('paginate.var_page'));
		$page = ($page<=0)?1:$page;
		$pageSize = 15;
		$start = ($page-1)*$pageSize;
		$sql = $sql1." union ".$sql2;
		$sqlNum = 'select count(*) wstNum from ('.$sql.") as c";
		$sql = 'select * from ('.$sql.') as c order by isSale desc limit '.$start.','.$pageSize;
		$rsNum = Db::query($sqlNum);
		$rsRows = Db::query($sql);
		$rs = WSTPager((int)$rsNum[0]['wstNum'],$rsRows,$page,$pageSize);
		if(empty($rs['Rows']))return $rs;
		$specIds = [];
		foreach ($rs['Rows'] as $key =>$v){
			$specIds[$key] = explode(':',$v['specIds']);
			$rss = Db::table('__SPEC_ITEMS__')->alias('si')
			->join('__SPEC_CATS__ sc','sc.catId=si.catId','left')
			->where('si.shopId = '.$shopId.' and si.goodsId = '.$v['goodsId'])
			->where('si.itemId','in',$specIds[$key])
			->field('si.itemId,si.itemName,sc.catId,sc.catName')
			->select();
			$rs['Rows'][$key]['spec'] = $rss;
		}
		return $rs;
	}
	/**
	 *  预警修改预警库存
	 */
	public function editwarnStock(){
		$id = input('post.id/d');
		$type = input('post.type/d');
		$number = (int)input('post.number');
		$shopId = (int)session('WST_USER.shopId');
		$data = $data2 = [];
		$data['shopId'] =  $data2['shopId'] = $shopId;
		$datat=array('1'=>'specStock','2'=>'warnStock','3'=>'goodsStock','4'=>'warnStock');
		if(!empty($type)){
			$data[$datat[$type]] = $number;
			if($type==1 || $type==2){
				$data['goodsId'] = $goodsId = input('post.goodsId/d');
				$gs = new GoodsSpecs();
				$rss = $gs->update($data,['id'=>$id]);
				//更新商品库存
				$goodsStock = 0;
				if($rss!==false){
					$specStocks = $gs->where(['shopId'=>$shopId,'goodsId'=>$goodsId,'dataFlag'=>1])->field('specStock')->select();
					foreach ($specStocks as $key =>$v){
						$goodsStock = $goodsStock+$v['specStock'];
					}
					$data2['goodsStock'] = $goodsStock;
					$rs = $this->update($data2,['goodsId'=>$goodsId]);
				}else{
					return WSTReturn('操作失败',-1);
				}
			}
			if($type==3 || $type==4){
				$rs = $this->update($data,['goodsId'=>$id]);
			}
			if($rs!==false){
				return WSTReturn('操作成功',1);
			}else{
				return WSTReturn('操作失败',-1);
			}
		}
		return WSTReturn('操作失败',1);
	}
}
