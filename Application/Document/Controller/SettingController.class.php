<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Document\Controller;
use Admin\Controller\InitController;
/* 文档模块配置 */

class SettingController extends InitController
{
	public function _initialize() {
		parent::_initialize();
		$this->db = D('Module');
	}

	public function init() {
		if (submitcheck('dosubmit')) {
			$setting = (array) $_POST['setting'];
			$this->db->where(array('module' => 'Document'))->setField('setting', serialize($setting));
			setcache('setting', $setting, 'document');
			$this->success('配置保持成功');
		} else {
			$setting = $this->db->getFieldByModule('Document', 'setting');
			$setting = unserialize($setting);
			$show_header = FALSE;
			include $this->admin_tpl('setting');
		}
	}

}