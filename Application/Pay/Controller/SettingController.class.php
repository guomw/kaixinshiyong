<?php
namespace Pay\Controller;
use \Admin\Controller\InitController;
class SettingController extends InitController{
	public function _initialize(){
		parent::_initialize();
		$this->typeConfig = array(
            'quick'   => '快速提现',
            'small'   => '普通提现',
        );
		$this->type = (isset($this->typeConfig['quick'])) ? $this->typeConfig['quick'] : 'small';
	}
	public function init(){
		$pay_setting = getcache('deposite_setting','pay');
        extract($pay_setting);
		$form = new \Common\Library\form();
		include $this->admin_tpl('deposite_setting');
	}
	
	public function update(){
		if(submitcheck('dosubmit')){
		  	$info = $_POST['setting'];
            if (empty($info)) { $this->error('参数错误');}
            setcache('deposite_setting',$info,'pay');
            $this->success('操作成功');
		}else{
			$this->error('请勿非法提交');
		}
	}
}