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
 * 社区类
 */
class Communitys extends Base{
	
	/**
	 * 判断一个点是否在多边开内部
	 * @param $points 多边形的坐标点集合
	 * @param $lnglat 当前坐标点
	 * @return boolean
	 */
	function isPointInPolygon($points,$lnglat){
		$count = count($points);
		$px = $lnglat['lat'];
		$py = $lnglat['lng'];
		$flag = FALSE;
		for ($i = 0, $j = $count - 1; $i < $count; $j = $i, $i++) {
			$sx = $points[$i]['lat'];
			$sy = $points[$i]['lng'];
			$tx = $points[$j]['lat'];
			$ty = $points[$j]['lng'];
			
			if ($px == $sx && $py == $sy || $px == $tx && $py == $ty)
				return TRUE;
			if ($sy < $py && $ty >= $py || $sy >= $py && $ty < $py) {
				$x = $sx + ($py - $sy) * ($tx - $sx) / ($ty - $sy);
				if ($x == $px)
					return TRUE;
				if ($x > $px)
					$flag = !$flag;
			}
		}
		return $flag;
	}

    /**
     * 查找当前坐标所在的社区
     */
    public function getCommunityByPoint(){
    	$areaId2 = input('post.areaId2/d',0);
    	$lt = input('post.lnglat');
    	$lt = str_replace(" ", "", $lt);
    	$pxy = explode(",", $lt);
    	$px = $pxy[0];
    	$py = $pxy[1];
    	$areaId2 = 440100;
 
    	$lnglat["lat"] = $px;
    	$lnglat["lng"] = $py;
    	$community = array();
    	$rs = $this->where(['areaId2'=>$areaId2,'dataFlag'=>1])
    			   ->field('communityId,communityName,polygon')
    	           ->select();
    	if(count($rs)>0){
    		foreach ($rs as $key =>$v){
    			$polygon = $v["polygon"];
    			$pps = explode(",", $polygon);
    			$points = array();
    			$k = 0;
    			for($i=0; $i<count($pps); $i++){
    				if($i%2==0){
    					$points[$k]["lat"] = $pps[$i];
    				}else{
    					$points[$k]["lng"] = $pps[$i];
    					$k++;
    				}
    			}
    			
    			$flag = self::isPointInPolygon($points, $lnglat);
    			if($flag){
    				$community["communityId"] = $v["communityId"];
    				$community["communityName"] = $v["communityName"];
    				break;
    			}
    		}
    	}
    	return $community;
    }
    
    
    /**
     * 定位所在社区
     */
    public function getCurrCommunity(){
    	$communityId = input("get.communityId/d",0);
		if($communityId==0){
			$communityId = (int)session('communityId');
		}
		//检验城市有效性
		if($communityId>0){
			$rs = $this->where(["isShow"=>1,"dataFlag"=>1,"communityId"=>$communityId])
					   ->field('communityId,communityName')->find();
			if($rs['communityId']=='')$communityId = 0;
		}else{
			$communityId = (int)cookie("communityId");
		}
		
		session('communityId',$communityId);
		cookie("communityId", $communityId, time()+3600*24*90);
		return $communityId;
    		
    }
    
    /**
     * 查询社区信息
     */
    public function getCommunityById($communityId){
    	
    	$rs = $this->where(["isShow"=>1,"dataFlag"=>1,"communityId"=>$communityId])
    			   ->field('communityId,communityName')->find();
    	return $rs;
    }

    /**
    * 获取社区信息
    */
    public function getCommunity(){
        $areaId3 = (int)input('post.areaId3');
        $rs = $this->where(['areaId3'=>$areaId3,'isShow'=>1])->select();
        return $rs;
    }
    
}
