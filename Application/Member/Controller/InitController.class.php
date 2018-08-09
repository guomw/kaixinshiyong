<?php 
namespace Member\Controller;
use \Common\Controller\BaseController;
class InitController extends BaseController {
	public function _initialize() {
		parent::_initialize();
		$this->db = model('member');
		$this->group_db = model('member_group');
		$this->userid = (int) cookie('_userid');
		if ($this->userid < 1) {
			$this->error('您尚未登录 请登录后操作！', U('Index/login'));
		}
		
		$userinfo = $this->db->getByUserid($this->userid);
		if($userinfo['status'] != 1){
			 //清除当前的登录状态
			 cookie('_userid', NULL);
			 cookie('_groupid', 8);
             $this->error('您的账户未通过审核或已被冻结！', U('Index/login'));
		}

		if (empty($userinfo['nickname'])) {
			$userinfo['nickname'] = '匿名用户_'.$userinfo['userid'];
		}
		runhook('order',array('userid'=>$this->userid));
        $arr2 = array('userid'=>$this->userid);
        runhook('member_login_success_change_membergroup',$arr2);
		$groupid = $userinfo['groupid'];
 		$userinfo['groupname'] = $this->group_db->getFieldByGroupid($groupid,'name');
		$userinfo['avatar'] = getavatar($this->userid);
		$this->userinfo = $userinfo;
		helpers('time');
	}
}
?>