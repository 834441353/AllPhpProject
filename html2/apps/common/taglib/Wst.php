<?php
namespace apps\common\taglib;
use think\template\TagLib;
class Wst extends TagLib{
    /**
     * 定义标签列表
     */
    protected $tags   =  [
        'friendlink' => ['attr' => 'num,key,id,cache'],
        'ads' => ['attr' => 'code,num,key,id,cache'],
        'article' => ['attr' => 'cat,num,key,id,cache'],
        'goods' => ['attr' => 'type,cat,num,key,id,cache'],
        'shopgoods' => ['attr' => 'type,shop,num,key,id,cache'],
        'shopfloorgoods' => ['attr' => 'cat,shop,num,key,id,cache']
    ];

    /**
     * 商品数据调用    
     *  type:推荐/新品/热销/精品/浏览历史/看了又看  - recom/new/hot/best/history/visit
     *   cat:商品分类
     *   num:获取记录数量
     * cache:缓存时间
     *   key:序号
     *    id:循环中定义的元素变量
     * {wst:goods name='hot' cat='1' num='6'}{/wst:goods}
     */
    public function tagGoods($tag, $content){
    	$type   = $tag['type'];
    	$catId  = isset($tag['cat'])?$tag['cat']:0;
        $flag     = substr($catId, 0, 1);
        if (':' == $flag) {
            $catId = $this->autoBuildVar($catId);
            $parseStr .= '$_result=' . $catId . ';';
            $catId = '$_result';
        } else {
            $catId = $this->autoBuildVar($catId);
        }
    	
    	$id     = isset($tag['id'])?$tag['id']:'vo';
        $num    = isset($tag['num'])?(int)$tag['num']:0;
        $cache  = isset($tag['cache'])?$tag['cache']:0;
        $key    = isset($tag['key'])?$tag['key']:'key';
        $parse  = '<?php ';
        $parse .= '$wstTagGoods =  model("Tags")->listGoods("'.$type.'",'.$catId.','.$num.','.$cache.'); ';
        $parse .= 'foreach($wstTagGoods as $'.$key.'=>$'.$id.'){';
        $parse .= '?>';
        $parse .= $content;
        $parse .= '<?php } ?>';
        return $parse;
    }
    /**
     * 广告数据调用
     *   num:获取记录数量
     * cache:缓存时间
     *   key:序号
     *    id:循环中定义的元素变量
     * {wst:friendlink num='6'}{/wst:ads} 
     */
    public function tagFriendlink($tag, $content){
    	$id     = isset($tag['id'])?$tag['id']:'vo';
        $num    = isset($tag['num'])?(int)$tag['num']:99;
        $cache  = isset($tag['cache'])?$tag['cache']:0;
        $key    = isset($tag['key'])?$tag['key']:'key';
        $parse  = '<?php ';
        $parse .= '$wstTagFriendlink =  model("Tags")->listFriendlink('.$num.','.$cache.'); ';
        $parse .= 'foreach($wstTagFriendlink as $'.$key.'=>$'.$id.'){';
        $parse .= '?>';
        $parse .= $content;
        $parse .= '<?php } ?>';
        return $parse;
    }
    
    /**
     * 广告数据调用
     *  code:广告代码
     *   num:获取记录数量
     * cache:缓存时间
     *   key:序号
     *    id:循环中定义的元素变量
     * {wst:ads code='1' cat='1' num='6'}{/wst:ads} 
     */
    public function tagAds($tag, $content){
    	$code   = $tag['code'];
    	$id     = isset($tag['id'])?$tag['id']:'vo';
        $num    = isset($tag['num'])?(int)$tag['num']:99;
        $cache  = isset($tag['cache'])?$tag['cache']:0;
        $key    = isset($tag['key'])?$tag['key']:'key';
        $parse  = '<?php ';
        $parse .= '$wstTagAds =  model("Tags")->listAds("'.$code.'",'.$num.','.$cache.'); ';
        $parse .= 'foreach($wstTagAds as $'.$key.'=>$'.$id.'){';
        $parse .= '?>';
        $parse .= $content;
        $parse .= '<?php } ?>';
        return $parse;
    }
    
    /**
     * 文章数据调用
     *   cat:文章分类ID 或者 'new'
     *   num:获取记录数量
     * cache:缓存时间
     *   key:序号
     *    id:循环中定义的元素变量
     * {wst:article cat='1' num='6'}{/wst:article} 
     */
    public function tagArticle($tag, $content){
    	$cat   = $tag['cat'];
    	$id     = isset($tag['id'])?$tag['id']:'vo';
        $num    = isset($tag['num'])?(int)$tag['num']:99;
        $cache  = isset($tag['cache'])?$tag['cache']:0;
        $key    = isset($tag['key'])?$tag['key']:'key';
        $parse  = '<?php ';
        $parse .= '$wstTagArticle =  model("Tags")->listArticle("'.$cat.'",'.$num.','.$cache.'); ';
        $parse .= 'foreach($wstTagArticle as $'.$key.'=>$'.$id.'){';
        $parse .= '?>';
        $parse .= $content;
        $parse .= '<?php } ?>';
        return $parse;
    }
    
    /**
     * 店铺商品数据调用    
     *  type:推荐/新品/热销/精品  - recom/new/hot/best
     *   shop:店铺ID
     *   num:获取记录数量
     * cache:缓存时间
     *   key:序号
     *    id:循环中定义的元素变量
     * {wst:shopgoods name='hot' cat='1' num='6'}{/wst:goods}
     */
    public function tagShopGoods($tag, $content){
    	$type   = $tag['type'];
    	$shopId  = isset($tag['shop'])?$tag['shop']:0;
        $flag     = substr($shopId, 0, 1);
        if (':' == $flag) {
            $shopId = $this->autoBuildVar($shopId);
            $parseStr .= '$_result=' . $shopId . ';';
            $shopId = '$_result';
        } else {
            $shopId = $this->autoBuildVar($shopId);
        }
    	
    	$id     = isset($tag['id'])?$tag['id']:'vo';
        $num    = isset($tag['num'])?(int)$tag['num']:0;
        $cache  = isset($tag['cache'])?$tag['cache']:0;
        $key    = isset($tag['key'])?$tag['key']:'key';
        $parse  = '<?php ';
        $parse .= '$wstTagShopGoods =  model("Tags")->listShopGoods("'.$type.'",'.$shopId.','.$num.','.$cache.'); ';
        $parse .= 'foreach($wstTagShopGoods as $'.$key.'=>$'.$id.'){';
        $parse .= '?>';
        $parse .= $content;
        $parse .= '<?php } ?>';
        return $parse;

    }

    /**
     * 自营店铺楼层商品数据调用    
     *   shop:店铺ID
     *   num:获取记录数量
     * cache:缓存时间
     *   key:序号
     *    id:循环中定义的元素变量
     * {wst:shopfloorgoods cat='1' num='6'}{/wst:shopfloorgoods}
     */
    public function tagShopFloorGoods($tag, $content){
        $catId  = isset($tag['cat'])?$tag['cat']:0;
        $flag     = substr($catId, 0, 1);
        if (':' == $flag) {
            $catId = $this->autoBuildVar($catId);
            $parseStr .= '$_result=' . $catId . ';';
            $catId = '$_result';
        } else {
            $catId = $this->autoBuildVar($catId);
        }


        $shopId  = isset($tag['shop'])?$tag['shop']:0;
        $flag     = substr($shopId, 0, 1);
        if (':' == $flag) {
            $shopId = $this->autoBuildVar($shopId);
            $parseStr .= '$_result=' . $shopId . ';';
            $shopId = '$_result';
        } else {
            $shopId = $this->autoBuildVar($shopId);
        }

        $id     = isset($tag['id'])?$tag['id']:'vo';
        $num    = isset($tag['num'])?(int)$tag['num']:0;
        $cache  = isset($tag['cache'])?$tag['cache']:0;
        $key    = isset($tag['key'])?$tag['key']:'key';
        $parse  = '<?php ';
        $parse .= '$wstTagShopFloorGoods =  model("Tags")->listShopFloorGoods('.$catId.','.$shopId.','.$num.','.$cache.'); ';
        $parse .= 'foreach($wstTagShopFloorGoods as $'.$key.'=>$'.$id.'){';
        $parse .= '?>';
        $parse .= $content;
        $parse .= '<?php } ?>';
        return $parse;

    }
    
}