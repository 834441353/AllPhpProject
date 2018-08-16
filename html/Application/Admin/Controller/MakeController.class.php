<?php
/**
 * Created by PhpStorm.
 * User: Sunxue Jia
 * Date: 2017/10/21
 * Time: 21:08
 */

namespace Admin\Controller;


class MakeController extends CommonController{
    public function makehome(){
        $this ->buildHtml('index.html','./','Home@index:index');
        echo 'ok';
    }
}