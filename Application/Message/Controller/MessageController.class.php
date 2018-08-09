<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Message\Controller;
use \Admin\Controller\InitController;
/* 后台短消息管理 */
class MessageController extends InitController
{
	public function _initialize() {
		parent::_initialize();
		$this->db = D('Message');
		$this->group_db = D('MessageGroup');
 		foreach(L('select') as $key=>$value) {//L获取语言变量
			$trade_status[$key] = $value;
		}
		$this->trade_status = $trade_status;
	}

	/* 私信管理 */
	public function manage() {
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('message_send').'\', title:\''.L('all_send_message').'\', width:\'550\', height:\'300\'}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('all_send_message'));
		$form = new \Common\Library\form();
		$trade_status = $this->trade_status;
		$page = max(1, (int) I('page'));
		$count = $this->db->count();
		$infos = $this->db->page($page, 12)->order('messageid DESC')->select();
		foreach ($infos as $key => $v) {
			 $lists =  model('member')->find($v['send_to_id']);
			 $userinfo = model('admin')->find($v['send_from_id']);
			 $infos[$key]['nickname'] = $lists['nickname'];
			 $infos[$key]['username'] = $userinfo['username'];

		}
	
		$pages = page($count, 12);
		include $this->admin_tpl('message_list');
	}

	/* 私信删除 */
	public function delete() {
		$messageid = I('messageid');
		if (empty($messageid)) {
			$this->error('参数错误');
		}
		$sqlMap = array();
		if (is_array($messageid)) {
			$sqlMap['messageid'] = array("LIKE", $messageid);
		} else {
			$sqlMap['messageid'] = $messageid;
		}
		$this->db->where($sqlMap)->delete();
		$this->success('操作成功');		
	}

	/* 私信搜索 */
	public function search_message() {
		if(submitcheck('dosubmit')){
			$search = I('search');
			extract($search);
			$where = '';
			if(!$username && !$start_time && !$end_time){
				$where = "";
			}

			if ($status == 'send_from_id') {
				$send_from = model('admin')->where(array('username'=>array("LIKE", "%".$username."%")))->getfield('userid',true);
				$where['send_from_id'] =array("IN", $send_from);
			}

			if ($status == 'send_to_id') {
				$send_to = model('member')->where(array('nickname'=>array("LIKE", "%".$username."%")))->getfield('userid',true);
				$where['send_to_id'] =array("IN", $send_to);
			}

			/*if($username) {
				$where['subject|content'] = array("LIKE", "%".$username."%");
			}*/
			if ($start_time) {
				$start = strtotime($start_time);
				$where['message_time'] = array("EGT", $start);
			}
			if ($end_time) {
				$end = strtotime($end_time);
				$where['message_time'] = array("ELT", $end);
			}
  		} 
  		$page = isset($_GET['page']) && intval($_GET['page']) ? intval($_GET['page']) : 1;
  		$count = $this->db->where($where)->count();
		$infos = $this->db->where($where)->page($page, 12)->order('messageid DESC')->select();
		foreach ($infos as $key => $v) {
			 $lists =  model('member')->find($v['send_to_id']);
			 $userinfo = model('admin')->find($v['send_from_id']);
			 $infos[$key]['nickname'] = $lists['nickname'];
			 $infos[$key]['username'] = $userinfo['username'];

		}
		$pages = page($count, 12);
 		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('message_send').'\', title:\''.L('all_send_message').'\', width:\'550\', height:\'300\'}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('all_send_message'));
 		$form = new \Common\Library\form();
		$trade_status = $this->trade_status;
 		include $this->admin_tpl('message_list');
	}


	/* 群发消息管理 */
	public function message_group_manage() {
		$page = max(1, (int) I('page'));
		$count = $this->group_db->count();
		$infos = $this->group_db->page($page, 12)->select();
		$pages = page($count, 12);
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('message_send').'\', title:\''.L('all_send_message').'\', width:\'550\', height:\'300\'}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('all_send_message'));
 		include $this->admin_tpl('message_group_list');		
	}

	/* 删除群发消息 */
	public function delete_group() {
		$message_group_id = I('message_group_id');
		if (empty($message_group_id)) {
			$this->error('参数错误');
		}
		$sqlMap = array();
		if (is_array($message_group_id)) {
			$sqlMap['id'] = array("LIKE", $message_group_id);
		} else {
			$sqlMap['id'] = $message_group_id;
		}
		$this->group_db->where($sqlMap)->delete();
		$this->success('操作成功');
	}

	/* 群发短消息 */
	public function message_send() {
		if (submitcheck('dosubmit')) {
			$info = $_POST['info'];
			if (empty($info['subject']) || empty($info['content'])) {
				$this->error('标题和内容不能为空');
			}
			$info['typeid'] = 1;
			if ($info['type'] == 'groupid') {
				$info['groupid'] = $info['groupid'];
			} else {
				$info['groupid'] = $info['roleid'];
			}
			$info['inputtime'] = NOW_TIME;
			$result_id = $this->group_db->add($info);
			if (!$result_id) {
				$this->error('群发消息失败');
			}
			$this->success('操作成功');
		} else {
			$show_validator = $show_scroll = $show_header = true;
			$member_group = getcache('member_group','member');
			$role_infos = getcache('role');
			include $this->admin_tpl('message_send');
		}
	}

	/* 针对发某个人的消息 */
	public function send_one() {
		if(submitcheck('dosubmit')) {
			$info = $_POST['info'];
			$info['send_from_id'] = session('userid');
			$info['send_to_id'] = D('Member')->where(array('username|email|phone' => $info['send_to_id']))->getField('userid');
			$info['subject'] = dhtmlspecialchars($info['subject']);
			$info['content'] = dhtmlspecialchars($info['content']);
			$info['message_time'] = NOW_TIME;
			if (defined('IN_ADMIN')) {
				$info['issystem'] = 1;
			}
			$this->db->update($info);
			$this->success(L('operation_success'),HTTP_REFERER);
		} else {
			$show_validator = $show_scroll =  true;
			$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('message_send').'\', title:\''.L('all_send_message').'\', width:\'550\', height:\'300\'}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('all_send_message'));
			include $this->admin_tpl('message_send_one');
		}		
	}

	public function public_name() {
		$tousername = I('tousername');
		if (empty($tousername)) $this->error('收件人不能为空');
		$sqlMap = array();
		$sqlMap['username|email|phone'] = $tousername;
		$count = D('Member')->where($sqlMap)->count();
		if (!$count) {
			$this->error('不能为空');
		}
		$this->success('可以使用');
	}
}