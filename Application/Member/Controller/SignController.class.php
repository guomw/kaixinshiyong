<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Member\Controller;
use Member\Controller\InitController;
class SignController extends InitController {
	public function _initialize() {
		parent::_initialize();
		$this->db = model('member_sign');
	}



	/* 魔力试用签到 */
	public function index() {
		$uid = $this->userid;
		$sqlmap = array();
		$sqlmap['uid'] = $uid;
		$sqlmap['_string'] = "DATE_FORMAT(FROM_UNIXTIME(dateline),'%Y%m%d') = DATE_FORMAT(NOW(),'%Y%m%d')";
		$count = $this->db->where($sqlmap)->count();
		if($count) {
			$this->error('您今日已签到');
		} else {
			$sign_info = array('uid' => $uid);
			$this->db->update($sign_info);
			$sign = model('task')->where(array('type'=>'sign'))->find();
			$only = '3-5-'.$uid.'-'.date('Y-m-d');
			$rs = model('member_finance_log')->where(array('only'=>$only))->find();
			if(!$rs){
			    $result = action_finance_log($uid,$sign['task_reward'],$sign['task_type'],'签到奖励',$only);
			    $this->success('签到成功');
			}else{
			    $this->error('签到失败，重复签到');
			}
		}
	}
}