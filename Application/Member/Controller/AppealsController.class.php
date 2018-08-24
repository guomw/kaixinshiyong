<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Member\Controller;
use \Admin\Controller\InitController;
if (!defined('MODULE_CACHE')) define('MODULE_CACHE', DATA_PATH.'caches_model/');
/**
 * 后台晒单、试用报告管理
 */
class AppealsController extends InitController {
	public function _initialize() {
		parent::_initialize();
		$this->db = model('report');
		$this->order = model('order');
		$this->member = model('member');
		$this->pagecurr = max(1, I('page', 0, 'intval'));
		$this->pagesize = 10;
		$this->status = array('已关闭','已抢购', '已确认','待审核', '审核失败', '审核通过', '申诉中', '已完成');
	}

	/* 购物返利 -> 晒单管理 [Adalways试用系统] */
	public function manage() {
		$show_header = false;
		// 读取购物返利晒单开关 1:开启；0：关闭
		$enabled = model('activity_set')->where(array('activity_type' => 'rebate','key'=>'buyer_artificial_check'))->getfield('value');		
		$sqlMap = array();
		if (submitcheck('search','G')) {
			$info = I('get.');
			$info['start_time'] = (!empty($info['start_time'])) ? strtotime($info['start_time']) : 0;
			$info['end_time'] = (!empty($info['end_time'])) ? strtotime($info['end_time']) : 0;
			/* 晒单时间 */
			if ($info['start_time'] && $info['end_time']){
				$sqlMap['reporttime'] = array("BETWEEN",array($info['start_time'],$info['end_time']));
			}else{
				if ($info['start_time'] > 0) {
					$sqlMap['reporttime'] = array("EGT", $info['start_time']);
				}
				if ($info['end_time'] > 0) {
					$sqlMap['reporttime'] = array("ELT", $info['end_time']);
				}
			}
			/* 当前状态 */
			$info['status'] = (int) $info['status'];
			if ($info['status'] != -99) {
				$sqlMap['status'] = $info['status'];
			}
			/* 关键字搜索类型 */
			$info['type'] = (int) $info['type'];
			$info['keyword'] = trim($info['keyword']);
			if ($info['keyword']) {
				switch ($info['type']) {
					case '0': //商品标题
						$pids = model('product')->where(array('title'=>array("LIKE", "%".$info['keyword']."%")))->getField("id",true);
						$sqlMap['goods_id'] = array('IN',$pids);
						break;
					case '1': // 用户ID
						$sqlMap['userid'] = $info['keyword'];
						break;
					case '2': // 注册邮箱
						$uids = $this->member->where(array('email'=>array("LIKE", "%".$info['keyword']."%")))->getField("userid",true);
						$sqlMap['userid'] = array('IN',$uids);
						break;
					case '3': // 昵称
						$uids = $this->member->where(array('nickname'=>array("LIKE", "%".$info['keyword']."%")))->getField("userid",true);
						$sqlMap['userid'] = array('IN',$uids);
						break;
				}
			}
		}
		$count = $this->db->where($sqlMap)->count();
		$reports = $this->db->where($sqlMap)->order('id DESC')->page($this->pagecurr, $this->pagesize)->select();
		$pages = page($count, $this->pagesize);
		foreach ($reports as $key => $val) {
			// 商品信息
			$val['product'] = getGoodsInfo($val['goods_id']);
			// 商家信息
			$val['seller'] = $this->member->find($val['product']['company_id']);
			// 晒单会员信息
			$val['buyer'] = $this->member->find($val['userid']);
			$reports[$key] = $val;
		}
		$form =  new \Common\Library\form();
		include $this->admin_tpl('report_list');
	}

	/**
	 * 晒单额外奖励
	 */
	public function aword(){
		$userid = I('userid');
		if(submitcheck('dosubmit')){
			//将该信息加入账户明细表
			$info = I('info');
			$result = action_finance_log($info['userid'],$info['value'],'point',$info['cause']);
			if($result){
			$message = array();
            $message['send_from_id'] = 1;
            $message['send_to_id'] = $info['userid'];
            $message['subject'] = '晒单额外给予奖励';
            $message['content'] = str_replace('<br/>','\r\n', $info['cause']);
            $api = new \Message\Library\api();
            $api->send_mess($message);
				$this->success('操作成功','javascript:close_dialog();');
			}else{
				$this->success('操作失败');
			}
		}else{
			include $this->admin_tpl('report_aword');
		}
	}
	/* 查看晒单 [Adalways试用系统] */
	public function report_read($id){
		if ((int) $id < 1) $this->error('该晒单不存在');
		$report = $this->db->find($id);
		// 商品信息
		$report['product'] = getGoodsInfo($report['goods_id']);
		// 商家信息
		$report['seller'] =  $this->member->find($report['product']['company_id']);
		// 晒单会员信息
		$report['buyer'] = $this->member->find($report['userid']);
		include $this->admin_tpl('report_read');
	}
	/* 操作晒单 [Adalways试用系统] */
	public function report_do(){
		$ids  = (array)I('ids');
		$type = I('type');
		if ($type === 'pass') {
			foreach ($ids as $id) {
				$this->db->where(array('id'=>$id))->setField('status','1');
			}
		}elseif ($type === 'shield') {
			foreach ($ids as $id) {
				$this->db->where(array('id'=>$id))->setField('status','2');
			}
		}elseif ($type === 'delete'){	
			foreach ($ids as $id) {
				$this->db->delete($id);
			}
		}else{
			$this->error('您的操作不存在');
		}
		$this->success('操作成功',U('manage'));
	}

// -------------------------------------------试用报告管理--------------------------------------------

	/* 免费试用 -> 试用报告管理 [Adalways试用系统] */
	public function trial_manage() {
		$show_header = false;
		$state = $this->status;
		$sqlMap = array();
		if (submitcheck('search','G')) {
			$info = I('get.');
			$info['start_time'] = (!empty($info['start_time'])) ? strtotime($info['start_time']) : 0;
			$info['end_time'] = (!empty($info['end_time'])) ? strtotime($info['end_time']) : 0;
			/* 晒单时间 */
			if ($info['start_time'] && $info['end_time']){
				$sqlMap['complete_time'] = array("BETWEEN",array($info['start_time'],$info['end_time']));
			}else{
				if ($info['start_time'] > 0) {
					$sqlMap['complete_time'] = array("EGT", $info['start_time']);
				}
				if ($info['end_time'] > 0) {
					$sqlMap['complete_time'] = array("ELT", $info['end_time']);
				}
			}
			/* 当前状态 */
			/*$info['status'] = (int) $info['status'];
			if ($info['status'] != -99) {
				$pids_1 = model('order')->where(array('status'=>array("EQ",$info['keyword'])))->getField("id",true);
				$sqlMap['order_id'] = $pids_1;
			}*/
			/* 关键字搜索类型 */
			$info['type'] = (int) $info['type'];
			$info['keyword'] = trim($info['keyword']);
			if ($info['keyword']) {
				switch ($info['type']) {
					case '0': //商品标题
						$pids = model('product')->where(array('title'=>array("LIKE", "%".$info['keyword']."%")))->getField("id",true);
						$sqlMap['goods_id'] = array('IN',$pids);
						break;
					case '1': // 订单ID
						$sqlMap['id'] = $info['keyword'];
						break;
					case '2': // 亚马逊订单号
					    $pidss = model('order')->where(array('order_sn'=>array("LIKE", "%".$info['keyword']."%")))->getField("id",true);
						$sqlMap['order_id'] = array('IN',$pidss);
						break;
					case '3': // 昵称
						$uids = $this->member->where(array('nickname'=>array("LIKE", "%".$info['keyword']."%")))->getField("userid",true);
						$sqlMap['userid'] = array('IN',$uids);
						break;
				}
			}
		}
		$count = model('trial_report')->where($sqlMap)->count();
		$reports = model('trial_report')->where($sqlMap)->order('id DESC')->page($this->pagecurr, $this->pagesize)->select();
	//	echo model('trial_report')->getLastSql();
		$pages = page($count, $this->pagesize);
		foreach ($reports as $key => $val) {
			$factory = new \Product\Factory\product($val['goods_id']);
			// 商品信息
			$val['product'] = $factory->product_info;
			$val['buyer'] = nickname($val['userid']);

			// 商家信息
			$val['seller'] = $this->member->find($val['product']['company_id']);
			// 晒单会员信息
			$reports[$key] = $val;
		}
		$form =  new \Common\Library\form();
		include $this->admin_tpl('trial_list');
	}
	/* 查看试用报告 [Adalways试用系统] */
	public function trial_read($id){
		if ((int) $id < 1) $this->error('该报告不存在');
		//$state = $this->status;
		//$trial = $this->order->find($id);
		$trials = model('trial_report')->find($id);
		$factory = new \Product\Factory\order($trials['order_id']);
		$trial = $factory->order_info;
		$trial['trial_report'] = string2array($trials['base_info']);
		$trial['content'] = $trials['content'];
		// 商品信息
		$trial['product'] = getGoodsInfo($trial['goods_id']);
		// 商家信息
		$trial['seller'] =  $this->member->find($trial['product']['company_id']);
		// 晒单会员信息
		$trial['buyer'] = $this->member->find($trials['userid']);
		include $this->admin_tpl('trial_read');
	}
}