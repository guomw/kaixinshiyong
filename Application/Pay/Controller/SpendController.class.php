<?php
namespace Pay\Controller;
use Admin\Controller\InitController;
class SpendController extends InitController{
	public function _initialize(){
		parent::_initialize();
		$this->db = model('member_finance_log');
		$this->member_db = model('member');
	}
	public function init(){
		$pagecurr = max(1,I('page',0,'intval'));
		$pagesize  = 20;
		$sqlMap = "";
		$sqlMap['num'] = array('LT',0);
		if(submitcheck('dosubmit')){
			$username = isset($_POST['username']) && trim($_POST['username']) ? trim($_POST['username']) : '';
			$user_type = isset($_POST['user_type']) && intval($_POST['user_type']) ? intval($_POST['user_type']) : '';
			$endtime = isset($_POST['endtime'])  &&  trim($_POST['endtime']) ? strtotime(trim($_POST['endtime'])) : '';
			$starttime = isset($_POST['starttime']) && trim($_POST['starttime']) ? strtotime(trim($_POST['starttime'])) : '';
			/*if(!empty($username) && $user_type == 1){
				$rs = $this->member_db->where(array('username'=>$username))->find();
				$userid = $rs['userid'];
				$sqlMap .= $sqlMap? " AND `userid` = '$userid'" : " `userid` = '$userid'";
			}
			
			if (!empty($username) && $user_type == 2) {
				$sqlMap .= $sqlMap ? " AND `userid` = '$username'" : " `userid` = '$username'";
			}*/



			if($user_type){
				switch ($user_type) {
					case '1':
						$uids = model('member')->where(array('nickname'=>array("LIKE", "%".$username."%")))->getfield('userid',true);
						$sqlMap['userid'] = array("IN", $uids);
						break;

					case '2':
						$sqlMap['id'] = $username;
						break;
				}

			}

			if ($starttime && $endtime){
				$sqlMap['dateline'] = array("BETWEEN",array($starttime,$endtime));
			}else{
				if ($starttime > 0) {
				$sqlMap['dateline'] = array("EGT", $starttime);
				}
				if ($endtime > 0) {
					$sqlMap['dateline'] = array("ELT", $endtime);
				}
			}
			
			/*if(!empty($starttime)){
				$sqlMap['dateline'] = $sqlMap ? " AND `dateline` >= '$starttime'" : "`dateline` >= '$starttime'";
			}
			if(!empty($endtime)){
				$sqlMap['dateline'] = $sqlMap ? " AND `dateline` <= '$endtime'" : "`dateline` <= '$endtime'";
			}
			if(!empty($starttime) && !empty($endtime)){
				$sqlMap['dateline'] = $sqlMap ? " AND `dateline` between '$starttime' and '$endtime'" : "`dateline` between '$starttime' and '$endtime'";
			}*/
		}
		$count = $this->db->where($sqlMap)->count();
		$lists = $this->db->where($sqlMap)->page($pagecurr,$pagesize)->order('id DESC ,dateline DESC')->select();
		
		foreach ($lists as $k=>$v) {
			$lists[$k]['username'] = $this->member_db->getFieldByUserid($v['userid'],'nickname');
			$lists[$k]['modelid'] = $this->member_db->getFieldByUserid($v['userid'],'modelid');
		}
		$pages = page($count,$pagesize);
		$form = new \Common\Library\form();
		$format = new \Common\Library\format();
		include $this->admin_tpl('spend_list');
	}
}
?>