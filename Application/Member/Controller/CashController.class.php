<?php
namespace Member\Controller;
use \Admin\Controller\InitController;
/* 后台财务管理 */
class CashController extends InitController{
	public function _initialize(){
		parent::_initialize();
		$this->db = D('CashRecords');
		$this->member = D('Member');
		$this->pagecurr = max(1, I('page', 0, 'intval'));
		$this->pagesize = 10;
	}
	/* 财务管理->会员提现 [Adalways试用系统] */
	public function init(){
		$sqlMap = array();
		if (submitcheck('search','G')) {
			$info = I('param.');
			$info['start_time'] = (!empty($info['start_time'])) ? strtotime($info['start_time']) : 0;
			$info['end_time'] = (!empty($info['end_time'])) ? strtotime($info['end_time']) : 0;
			/* 提交申请时间 */
			if ($info['start_time'] && $info['end_time']){
				$sqlMap['date_time'] = array("BETWEEN",array($info['start_time'],$info['end_time']));
			}else{
				if ($info['start_time'] > 0) {
					$sqlMap['date_time'] = array("EGT", $info['start_time']);
				}
				if ($info['end_time'] > 0) {
					$sqlMap['date_time'] = array("ELT", $info['end_time']);
				}
			}
			/* 关键字搜索类型 */
			$info['type'] = (int) $info['type'];
			if (trim($info['keyword'])) {
				switch ($info['type']) {
					case '1': //用户名
						$userids = $this->member->where(array("username" => array("like","%".$info['keyword']."%")))->getField("userid",true);
						break;
					case '2': // 支付宝账号
						$userids = $this->db->where(array("cash_alipay_username" => array("like","%".$info['keyword']."%")))->getField("buyer_id",true);
						break;
					default:
						$userids = $this->member->where(array("username" => array("like","%".$info['keyword']."%")))->getField("userid");
						break;
				}
				$sqlMap['buyer_id'] = array('IN',$userids);
			}
		}
		$sqlMap['type'] = '0';
		$cash_count = 0;
		$cash_list = array();
		//分页
		$cash_count = $this->db->where($sqlMap)->count();
		$cash_list = $this->db->where($sqlMap)->page($this->pagecurr, $this->pagesize)->order("cashid DESC")->select();
		/* 查出相关会员信息 */
		foreach ($cash_list as $key => $v) {
			$cash_list[$key]['username'] = $this->member->where(array("userid" => $v['buyer_id']))->getfield('username');
		}
		$pages = page($cash_count, $this->pagesize);
		$form =  new \Common\Library\form();
		include $this->admin_tpl('cash_member_lists');
	}
}