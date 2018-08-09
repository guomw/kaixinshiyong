<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Sms\Controller;
use \Admin\Controller\InitController;
/**
 * 后台短信平台
 */
class SmsController extends InitController {
	public function _initialize() {
		parent::_initialize();
		$this->db = D('Module');
		$this->sms_report_db = D('SmsReport');
		$this->pagecurr = max(1, I('page', 0, 'intval'));
		$this->pagesize = 10;
		$this->SmsApi = new \Sms\Api\SmsApi();
	}

	public function manage() {
		$page = max(1, (int) I('page'));
		$sms_num = $this->SmsApi->get_num();
		$count = $this->sms_report_db->count();
		$infos = $this->sms_report_db->page($page, 10)->order("id DESC")->select();
		$pages = page($count, 10);
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('sms_send').'\', title:\''.L('all_send_message').'\', width:\'550\', height:\'300\'}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('all_send_message'));
		$setting = $this->db->getFieldByModule(MODULE_NAME, 'setting');
		$sms_setting = unserialize($setting);

		include $this->admin_tpl('manage');
	}

	/* 模块配置 */
	public function setting() {
		if (submitcheck('dosubmit')) {
			$setting = serialize($_POST['setting']);
			$this->db->where(array('module' => MODULE_NAME))->setField('setting', $setting);
			setcache('setting', $_POST['setting'], strtolower(MODULE_NAME));
			$this->success('操作成功', U('manage'));
		} else {
			$setting = $this->db->getFieldByModule(MODULE_NAME, 'setting');
			$sms_setting = unserialize($setting);
			$slist = glob(MODULE_PATH.'Api/Driver/*.class.php');
			$files = array();
			if ($slist) {

				//$file = $slist[1];
				//if (!is_file($file) || !file_exists($file)) continue;
				//$filename = basename($file, '.class.php');
			//	$files[] = $filename;
				foreach ($slist as $file) {
					if (!is_file($file) || !file_exists($file)) continue;
					$filename = basename($file, '.class.php');
					$files[] = $filename;
				}
			}
			include $this->admin_tpl('setting');
		}
	}

	/* 群发短消息 */
	public function sms_send() {
		if (submitcheck('dosubmit')) {
			$info = $_POST['info'];
			$sqlMap = array();
			
			if (empty($info['content'])) {
				$this->error('标题和内容不能为空');
			}
			
			if ($info['type'] == 1) {
				$sqlMap['modelid'] = 1;
				$sqlMap['groupid'] = $info['groupid'];
				# code...
			}elseif($info['type'] == 2){
				$sqlMap['modelid'] = 2;
				$sqlMap['groupid'] = $info['roleid'];

			}
			//$info['inputtime'] = NOW_TIME;
			/*$result_id = $this->group_db->add($info);
			if (!$result_id) {
				$this->error('群发消息失败');
			}*/

			$userinfo = model('Member')->where($sqlMap)->field('userid,phone')->select();
			if (!$userinfo) {
				$this->error('暂无符合相关条件信息');
			}
			foreach ($userinfo as $k => $v) {
				if ($v['phone'] != '') {
					$SmsApi = new \Sms\Api\SmsApi();
           			$result = $SmsApi->send($v['phone'], $info['content']);
           			$infos = array();
		            $infos['mobile'] = $v['phone'];
		            $infos['posttime'] = NOW_TIME;
		            $infos['userid'] =$v['userid'];
		            $infos['msg'] = $info['content'];
		            $infos['ip'] = get_client_ip();
		            $infos['enum'] = 'notice';
		            model('sms_report')->update($infos);
				}
				
			}
			
			$this->success('操作成功','javascript:close_dialog();');
		} else {
			$show_validator = $show_scroll = $show_header = true;
			$member_group = getcache('member_group','member');
			$merchant_group = getcache('merchant_group','member');
			include $this->admin_tpl('sms_send');
		}
	}

	/* 针对发某个人的消息 */
	public function send_one() {
		if(submitcheck('dosubmit')) {
			$info = $_POST['info'];
			$mobile =  D('Member')->where(array('username|email|phone' => $info['send_to_id']))->getField('phone');
			$info['send_from_id'] = session('userid');
			$info['send_to_id'] = D('Member')->where(array('username|email|phone' => $info['send_to_id']))->getField('userid');
			$info['subject'] = dhtmlspecialchars($info['subject']);
			$info['content'] = dhtmlspecialchars($info['content']);
			$info['message_time'] = NOW_TIME;
			if (defined('IN_ADMIN')) {
				$info['issystem'] = 1;
			}
			//$this->db->update($info);
			$SmsApi = new \Sms\Api\SmsApi();
            $result = $SmsApi->send($mobile, $info['content']);
           // if ($result) {
	            $infos = array();
	            $infos['mobile'] = $mobile;
	            $infos['posttime'] = NOW_TIME;
	            $infos['id_code'] ='';
	            $infos['msg'] = $info['content'];
	            $infos['ip'] = get_client_ip();
	            $infos['enum'] = 'notice';
	            model('sms_report')->update($infos);
	           // }
			$this->success('操作成功',HTTP_REFERER);
		} else {
			$show_validator = $show_scroll =  true;
			$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('sms_send').'\', title:\''.L('all_send_message').'\', width:\'550\', height:\'300\'}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('all_send_message'));
			include $this->admin_tpl('sms_send_one');
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