<?php
namespace Admin\Controller;
use Think\Controller;
class MusicController extends Controller {
	//实现音乐的增加功能
	public function add()
	{
		if(IS_GET){
			$this->display();
		}else{
			$model = D('Music');
			$data = $model->create();
//			dump($data);exit;
			if(!$data){
				$this->error($model->getError());
			}
			$music_id = $model->add($data);
			if(!$music_id){
				$this->error($model->getError());
			}
			$this->success('ok');
		}
	}

	public function test()
	{
		D('Music')->where(array('id'=>1))->setField('author','leo');
	}

	public function test2()
	{
		$res = D('Music')->userinfo();
		dump($res);
	}

	public function upload()
	{
		$upload = new \Think\Upload();
		$info = $upload -> uploadOne($_FILES['file']);
		if(!$info){
			$this->ajaxReturn(array('status'=>0,'msg'=>$upload->getError()));
		}
		$path = 'Uploads/'.$info['savepath'].$info['savename'];
		$this->ajaxReturn(array('status'=>1,'path'=>$path));

	}
}