<?php 
namespace Admin\Model;
use Think\Model;

class MusicModel extends Model
{

	//数据添加的前置钩子函数
	// public function _before_insert(&$data,$option)
	// {
	// 	$upload = new \Think\Upload();
	// 	$info = $upload -> uploadOne($_FILES['smallimg']);
	// 	if(!$info){
	// 		$this->error = $upload->getError();
	// 		return false;
	// 	}
	// 	$path = 'Uploads/'.$info['savepath'].$info['savename'];
	// 	$data['smallimg']=$path;
	// }

	public function _before_update(&$data,$option)
	{
		dump($data);dump($option);exit();
	}

	public function userinfo()
	{
		// 代表需要写入到user表中的内容 具体内容应该是从表单中接受到的
		$user_data=array();
		//开启事物
		$flag = true;
		$this->startTrans();
		$user_id = M('User')->add($user_data);
		if(!$user_id){
			$flag = false;
		}
		$info_data = array(
			'user_id'=>$user_id,
			'age'=>18
		);
		$info_id = M('Info')->add($info_data);
		if(!$info_id){
			$flag = false;
		}

		if($flag){
			$this->commit();
			return 1;
		}else{
			$this->rollback();
			return 0;
		}
	}

}

?>