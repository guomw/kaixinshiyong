<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Gbook\Controller;
use \Admin\Controller\InitController;
/* 留言薄后台管理 */
class GbookController extends InitController
{
	public function _initialize() {
		parent::_initialize();
		$this->db = D('Gbook');
		$this->module_db = D('Module');
		$this->moduleinfo = $this->module_db->getByModule('Gbook');
	}

	/* 模块配置 */
	public function setting() {
		if (submitcheck('dosubmit')) {
			$info = $_POST['info'];
			$this->module_db->where(array('module' => 'Gbook'))->setField('setting', serialize($info));
			setcache('gbook_setting', $info, 'module');
			$this->success('模块配置成功', HTTP_REFERER);
		} else {
			$setting = unserialize($this->moduleinfo['setting']);
			include $this->admin_tpl('setting');
		}
	}

	/* 留言管理 */
	public function manage() {
		$page = max(1, (int) I('page'));
		$count = $this->db->count();
		$infos = $this->db->page($page, 10)->select();
		$pages = page($count, 10);
		include $this->admin_tpl('manage');
	}

	/* 留言编辑 */
	public function edit() {
		
	}

	/* 留言删除 */
	public function delete() {
		$ids = (array) I('id');
		if (!empty($ids)) {
			$sql = array();
			$sql['id'] = array("IN", $ids);
			$this->db->where($sql)->delete();
			$this->success('操作成功');
		} else {
			$this->error('参数错误');
		}
	}

}