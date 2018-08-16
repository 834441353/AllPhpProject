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
 */
class News extends Base{
	/*public function index(){
		//获取左侧列表
		$m = model('home/Articles');
		$list = $m->NewsList();
		$this->assign('list',$list);
		return view('default/articles/news_list');
	}*/
	/**
	*	根据分类id获取文章列表
	*/
	public function nList(){
		$m = model('home/articles');
		$pageObj = $m->nList();
		$news = $pageObj->toArray();
		// 分页页码
		$page = $pageObj->render();
		$this->assign('page',$page);
		//获取左侧列表
		$leftList = $m->NewsList();
		$this->assign('list',$leftList);
		$this->assign('newsList',$news['Rows']);
		$this->assign('catId',(int)input('catId'));
		//面包屑导航
		$bcNav = $this->bcNav();
		// 防止用户取出帮助中心分类
		foreach($bcNav as $k=>$v){
			if($v['catId']==7){
				$bcNav = [];
				break;
			}
		}
		$this->assign('bcNav',$bcNav);
		return view('default/articles/news_list');
	}

	public function view(){
		//获取左侧列表
		$m = model('home/Articles');
		$list = $m->NewsList();
		//当前分类id
		$content = $m->getNewsById();
		$this->assign('catId',(int)$content['catId']);
		$this->assign('list',$list);
		$this->assign('content',$content);


		//面包屑导航
		$bcNav = [];
		if(!empty($content)){
			$bcNav = $this->bcNav();
		}
		$this->assign('bcNav',$bcNav);


		if((int)input('id')==0){
			// 资讯列表下的新闻
			$pageObj = $m->getArticles();
			$news = $pageObj->toArray();
			// 分页页码
			$page = $pageObj->render();
			$this->assign('page',$page);
			$this->assign('index',$news['Rows']);
		}


		return view('default/articles/news_list');
	}
	public function bcNav(){
		$m = model('home/Articles');
		return $m->bcNav();
	}
}