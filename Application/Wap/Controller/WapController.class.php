<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Wap\Controller;
use \Admin\Controller\InitController;
class WapController extends InitController
{
	public function _initialize() {
		parent::_initialize();
		$this->db = D('Module');
	}

	public function setting() {
		if (submitcheck('dosubmit')) {
			$setting = serialize($_POST['setting']);
			$this->db->where(array('module' => MODULE_NAME))->setField('setting', $setting);
			setcache('setting', $_POST['setting'], strtolower(MODULE_NAME));
			$this->success('操作成功', U('setting'));
		} else {
			$setting = $this->db->getFieldByModule(MODULE_NAME, 'setting');
			$setting = unserialize($setting);
			$show_header = FALSE;
			include $this->admin_tpl('setting');
		}
	}
}
?>