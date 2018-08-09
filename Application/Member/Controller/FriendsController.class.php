<?php
namespace Member\Controller;
use \Admin\Controller\InitController;
class FriendsController extends InitController{
	public function _initialize(){
		parent::_initialize();
	}
	
	public function setting(){
		if(IS_POST){
			$info = I('post.');
			setcache('friend_setting', $info,strtolower(MODULE_NAME));
			$this->success('操作成功');
		}else{
            $form = new \Common\Library\form();
            $format = new \Common\Library\format();
 			$setting = getcache('friend_setting','member');
			$count  = count($setting['setting']); 
			include $this->admin_tpl('friend_setting');
		}
	}
}