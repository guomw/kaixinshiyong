<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Message\Controller;
use \Common\Controller\BaseController;
/* 短消息前台 */
class IndexController extends BaseController
{
	public function _initialize() {
		header("content-type:text/html;charset=utf-8");
		parent::_initialize();
		$this->userid = (int) cookie('_userid');
		if ($this->userid < 1) {
			$this->error('您尚未登录', U('Member/Index/login'));
		}
		$userinfo = model('member')->getByUserid($this->userid);
		if (empty($userinfo['nickname'])) {
			$userinfo['nickname'] = '匿名用户_'.$userinfo['userid'];
		}
		$userinfo['avatar'] = getavatar($this->userid);
		$this->userinfo = $userinfo;
		helpers('time');
		$this->db = model('message');
		$this->message_data_db = model('message_data');
		$this->message_group_db = model('message_group');
		$this->MessageService = D('Message', 'Service');
		$this->newpm = $this->MessageService->getNewMessage($this->userid, $this->userinfo['groupid']);
	}

	/* 私信管理 */
	public function index() {
		$Ip = new \Common\Library\IpLocation();
		$Location = $Ip->getlocation();
		$status = I('status', '-1');
		$page = max(1, (int) I('page'));
		$pagesize = 12;
		$sqlMap = array();
		$sqlMap['send_to_id'] = $this->userid;
		if ($status > -1) {
			$sqlMap['status'] = $status;
		}
		$count = $this->db->where($sqlMap)->count();
		$infos = $this->db->where($sqlMap)->page($page, $pagesize)->select();
		foreach ($infos as $key => $info) {
			//$info['username'] = getUsername($info['send_from_id']);
			$info['username'] = D('admin')->getFieldByUserid($info['send_from_id'],'username');//发件人名称
			$infos[$key] = $info;
		}
		$pages = page($count, $pagesize);
		include template('index');
	}

	/**
	 * 私信标注已读
	 * @return [type] [description]
	 */
	public function detail() {
		$id = (array) I('id');
		if (empty($id)) {
			$this->error('参数错误');
		}
		$sqlMap = array();
		$sqlMap['messageid'] = array("IN", $id);
		$result = $this->db->where($sqlMap)->setField('status', 1);
		if (!$result) {
			$this->error('操作失败');	
		}
		$this->success('操作成功');
	}
	
	public function detail_message(){
		$id = (int)I('id');
		$rs = $this->db->where(array('messageid'=>$id))->setField('status', 1);
		if($rs){
			echo "1";
		}else{
			echo "0";
		}
	}

	/* 私信删除 */
	public function delete() {
		$id = (array) I('id');
		if (empty($id)) {
			$this->error('参数错误');
		}
		$sqlMap = array();
		$sqlMap['messageid'] = array("IN", $id);
		$result = $this->db->where($sqlMap)->delete();
		if (!$result) {
			$this->error('私信删除失败');
		}
		$this->success('操作成功'); 
	}

	/* 公共消息查阅 */
	public function group_detail() {
		$id = (array) I('id');
		if (empty($id)) {
			$this->error('参数错误');
		}
		$info = array();
		$info['userid'] = $this->userid;
		if (is_array($id)) {
			foreach ($id as $message_id) {
				$info['group_message_id'] = $message_id;
				$count = $this->message_data_db->where($info)->count();
				if ($count) continue;
				$this->message_data_db->add($info);					
			}
		}
		$this->success('操作成功');				
	}

	/* 公共消息删除 */
	public function group_delete() {
		$id = (array) I('id');
		if (empty($id)) {
			$this->error('参数错误');
		}
		$this->group_detail();		
	}

	/* 系统消息 */
	public function group() {
		$Ip = new \Common\Library\IpLocation();
		$Location = $Ip->getlocation();
		$group_message_ids = $this->message_data_db->where(array('userid' => $this->userid))->getField('group_message_id', TRUE);
		$sqlMap = array();
		$sqlMap['groupid'] = $this->userinfo['groupid'];
		$sqlMap['status'] = 1;
		if (!empty($group_message_ids)) {
			$sqlMap['id'] = array("NOT IN", $group_message_ids);
		}
		$page = max(1, (int) I('page'));
		$count = $this->message_group_db->where($sqlMap)->count();
		$infos = $this->message_group_db->where($sqlMap)->page($page, 12)->select();
		$pages = pages($count, 12, $page);
		include template('index');
	}
}
?>