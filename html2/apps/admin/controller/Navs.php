<?php
namespace apps\admin\controller;
use apps\admin\model\Navs as M;
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
 * 导航控制器
 */
class Navs extends Base{

    public function index(){
    	return $this->fetch("list");
    }

    /**
     * 获取分页
     */
    public function pageQuery(){
        $m = new M();
        $rs = $m->pageQuery();
        return $rs;
    }
    /**
     * 跳去编辑页面
     */
    public function toEdit(){
        //获取省级信息
        $this->assign('area1',model('admin/areas')->listQuery(0));
        $m = new M();
        $rs = $m->getById((int)Input("get.id"));
        $this->assign("data",$rs);
        return $this->fetch("edit");
    }
    /*
    * 获取数据
    */
    public function get(){
        $m = new M();
        $rs = $m->getById((int)Input("id"));
        return $rs;
    }
    /**
     * 新增
     */
    public function add(){
        $m = new M();
        $rs = $m->add();
        return $rs;
    }
    /**
    * 修改
    */
    public function edit(){
        $m = new M();
        $rs = $m->edit();
        return $rs;
    }
    /**
     * 删除
     */
    public function del(){
        $m = new M();
        $rs = $m->del();
        return $rs;
    }
    /**
    * 显示隐藏
    */
    public function editiIsShow(){
        $m = new M();
        $rs = $m->editiIsShow();
        return $rs;
    }

    
}
