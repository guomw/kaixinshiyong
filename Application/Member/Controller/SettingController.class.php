<?php
namespace Member\Controller;
use \Admin\Controller\InitController;
class SettingController extends InitController{
	public function _initialize(){
		parent::_initialize();
		$this->module_db = model('module');
	}

	public function register() {
		$setting = $this->module_db->where(array('module' => MODULE_NAME))->getField('setting');
		$setting = unserialize($setting);
		if (submitcheck('dosubmit')) {
			$info = $_POST['setting'];
			$info['setting_register_enable'] = (array) $info['setting_register_enable'];
			$info['setting_register_email_enable'] = (array) $info['setting_register_email_enable'];
			$info['setting_register_sms_enable'] = (array) $info['setting_register_sms_enable'];
			$info['setting_register_verify_enable'] = (array) $info['setting_register_verify_enable'];
			$info['setting_register_v2_phone'] = (array) $info['setting_register_v2_phone'];
			$info['setting_register_v2_email'] = (array) $info['setting_register_v2_email'];

			//$info = array_merge($setting, $info);
			$result = $this->module_db->where(array('module' => 'Member'))->setField('setting', serialize($info));
			if (!$result) {
				$this->error('模块配置失败');
			}
			setcache('setting', $info, strtolower(MODULE_NAME));
			$this->success('操作成功');
		} else {
			$form = new \Common\Library\form();
			$models = model('model')->where(array('module' => 'member', 'disabled' => 0))->getField('modelid, name', TRUE);
			include $this->admin_tpl('setting_register');			
		}
	}
}