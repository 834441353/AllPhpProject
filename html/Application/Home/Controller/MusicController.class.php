<?php
/**
 * Created by PhpStorm.
 * User: Sunxue Jia
 * Date: 2017/11/17
 * Time: 9:34
 */

namespace Home\Controller;
use Think\Controller;

class MusicController extends Controller
{
    public function loadlist(){
        $model = D('Music');
        $data =  $model ->select();
        $this -> ajaxReturn($data);
    }
}