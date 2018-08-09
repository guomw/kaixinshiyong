<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Comment\Controller;
use \Admin\Controller\InitController;
/**
 * 后台短信平台
 */
class CommentController extends InitController {
	public function _initialize() {
		parent::_initialize();
		$this->db = D('Comment');
		$this->module_db = D('Module');
		$this->module_info = $this->module_db->getByModule(MODULE_NAME);
	}

	public function manage() {
		$sms_num = $this->db->select();
		include $this->admin_tpl('manage');
	}

	/* 模块配置 */
	public function setting() {
		if (submitcheck('dosubmit')) {
			$info = array();
			$info['guest'] = (int) $_POST['guest'];
			$info['check'] = (int) $_POST['check'];
			$info['code'] = (int) $_POST['code'];
			$info['add_point'] = (int) $_POST['add_point'];
			$info['del_point'] = (int) $_POST['del_point'];
			$this->module_db->where(array('module' => MODULE_NAME))->setField('setting', serialize($info));
			$this->success('操作成功');
		} else {
			$show_header = FALSE;
			$setting = unserialize($this->module_info['setting']);
			include $this->admin_tpl('comment_setting');
		}
	}
}