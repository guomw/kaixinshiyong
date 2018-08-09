<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Member\Controller;
use \Admin\Controller\InitController;
/**
 * 后台->管理会员等级
 */
class MemberGroupController extends InitController {
	public function _initialize() {
		parent::_initialize();
		$this->db = D('MemberGroup');
	}

	/**
	 * 会员组管理 [Adalways试用系统]
	 * @author   <jason>
	 */
	public function manage() {
		$pagecurr = max(1, I('page', 0, 'intval'));
		$pagesize = 10;
		$sqlMap = array();
		$member_group_count = $this->db->where($sqlMap)->count();
		$member_group_list = $this->db->where($sqlMap)->page($pagecurr, $pagesize)->select();
		$pages = page($member_group_count, $pagesize);
		/* 拓展菜单 */
		$big_menu = array('javascript:art.dialog({id:\'add\',iframe:\''.U('add').'\', title:\''.L('member_group_add').'\', width:\'700\', height:\'500\', lock:true}, function(){var d = art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){return true;});void(0);', L('添加会员类型'));
		$show_dialog = TRUE;
		include $this->admin_tpl('member_group_list');
	}

	/**
	 * 添加会员组
	 * @author   <jason>
	 */
	public function add() {
		if (submitcheck('dosubmit')) {
			$info = $_POST['info'];
			$result = $this->db->update($info);
			if (!$result) {
				$this->error($this->db->getError());
			}
			$this->db->build_cache();
			$this->success('操作成功', 'javascript:close_dialog();');
		} else {
			$show_dialog = TRUE;
			include $this->admin_tpl('member_group_add');
		}
	}

	/**
	 * 编辑会员组
	 * @author   <jason>
	 */
	public function edit($groupid = 0) {
		$groupid = (int) $groupid;
		$groupinfo = $this->db->getByGroupid($groupid);
		if (empty($groupinfo)) $this->error('参数错误');
		if (submitcheck('dosubmit')) {
			$post = I('post.');
			$info = $post['info'];
			$info['groupid'] = $groupid;
			$info['allowpost'] = (int) $info['allowpost'];
			$info['allowpostverify'] = (int) $info['allowpostverify'];
			$info['allowupgrade'] = (int) $info['allowupgrade'];
			$info['allowsendmessage'] = (int) $info['allowsendmessage'];
			$info['allowattachment'] = (int) $info['allowattachment'];
			$info['allowsearch'] = (int) $info['allowsearch'];
			$result = $this->db->update($info);
			if (!$result) {
				$this->error($this->db->getError());
			}
			$this->db->build_cache();
			$this->success('操作成功', 'javascript:close_dialog();');
		} else {
			$show_dialog = TRUE;
			include $this->admin_tpl('member_group_edit');			
		}
	}

	/**
	 * 删除会员组
	 * @author   <jason>
	 */
	public function delete() {
		$groupids = (array) I('groupid');
		if (submitcheck('dosubmit')) {
			if (empty($groupids)) {
				$this->error('参数错误');
			}
			foreach ($groupids as $groupid) {
				$groupid = (int) $groupid;
				$issystem = $this->db->getFieldByGroupid($groupid);
				if ($issystem == 1) continue;
				$this->db->where(array('groupid' => $groupid))->delete();
			}
			$this->success('操作成功');
		} else {
			$this->error('请勿非法访问');
		}
	}

	/**
	 * 数组排序
	 * @author   <jason>
	 */
	public function public_sort() {
		$listorders = (array) $_POST['sort'];
		if (submitcheck('dosubmit')) {
			if (empty($listorders)) {
				$this->error('参数错误');
			}
			foreach ($listorders as $groupid => $sort) {
				if(!is_numeric($groupid) || !is_numeric($sort)) continue;
				$this->db->where(array('groupid' => $groupid))->setField('sort', $sort);
			}
			$this->db->build_cache();
			$this->success('操作成功');
		} else {
			$this->error('请勿非法访问'.ACTION_NAME);
		}
	}

	public function public_checkname_ajax() {
		echo "1";
		exit();
	}
}