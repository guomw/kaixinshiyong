<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Member\Controller;
use \Member\Controller\InitController;
class FinancialController extends InitController {
	public function _initialize() {
		parent::_initialize();
		$this->db = model("member_finance_log");
		$this->pay = model('pay_check');
		$this->cash = model('cash_records');
		$this->pagesize = 10;
		$this->pagecurr = max(1,I('page',1,'intval'));
	}
	/* 账单明细 */
	public function index() {
		// 冻结中的保证金
		$frozen_deposit = model('member_merchant')->where(array('userid' => $this->userid))->getField("frozen_deposit");
		$sqlMap = array();
		if (IS_GET) {
			$info = I('get.');
			$start_time = $info['start_time'];
			$end_time = $info['end_time'];
			$info['start_time'] = (!empty($info['start_time'])) ? strtotime($info['start_time']) : 0;
			$info['end_time'] = (!empty($info['end_time'])) ? strtotime($info['end_time']) : 0;

			/* 注册时间 */
			if ($info['start_time'] && $info['end_time']){
				$sqlMap['dateline'] = array("BETWEEN",array($info['start_time'],$info['end_time']));
			}else{
				if ($info['start_time'] > 0) {
				$sqlMap['dateline'] = array("EGT", $info['start_time']);
				}
				if ($info['end_time'] > 0) {
					$sqlMap['dateline'] = array("ELT", $info['end_time']);
				}
			}
		}
		$userinfo = getUserInfo($this->userid);
		$sqlMap['userid'] = $this->userid;
		$sqlMap['type'] = 'money';
		$count = $this->db->where($sqlMap)->count();	
		$account = $this->db->where($sqlMap)->page($this->pagecurr, $this->pagesize)->order("dateline DESC")->select();
		$pages = showPage($count,$this->pagecurr,$this->pagesize);
		$v2_pages = v2_page_3($count,$this->pagesize);

		$SEO = seo(0,"账单明细");
		include template('buyer/financial');
	}

	/*充值记录*/
	public function pay_log(){
		if (IS_GET) {
			$info = I('get.');
			$info['start_time'] = (!empty($info['start_time'])) ? strtotime($info['start_time']) : 0;
			$info['end_time'] = (!empty($info['end_time'])) ? strtotime($info['end_time']) : 0;
			/* 注册时间 */
			if ($info['start_time'] && $info['end_time']){
				$sqlMap['inputtime'] = array("BETWEEN",array($info['start_time'],$info['end_time']));
			}else{
				if ($info['start_time'] > 0) {
				$sqlMap['inputtime'] = array("EGT", $info['start_time']);
				}
				if ($info['end_time'] > 0) {
					$sqlMap['inputtime'] = array("ELT", $info['end_time']);
				}
			};
		}
		$sqlMap['userid'] = $this->userid;	
		$type = I('type',1);		
		if ($type == 1) {
			$count = $this->pay->where($sqlMap)->count();
			$pay_log = $this->pay->where($sqlMap)->page($this->pagecurr, $this->pagesize)->order("check_time DESC,inputtime DESC")->select();
		}else{
			$count = model('pay_order')->where($sqlMap)->count();
			$pay_log = model('pay_order')->where($sqlMap)->page($this->pagecurr, $this->pagesize)->order("id DESC,notify_time DESC")->select();
		}
		$userinfo = getUserInfo($this->userid);		
		$pages = showPage($count,$this->pagecurr,$this->pagesize);
		$SEO = seo(0,"充值记录");
		include template('pay_log');
	}

	/*提现记录*/
	public function cash_log(){
		if (IS_GET) {
			$info = I('get.');
			$start_time = $info['start_time'];
			$end_time = $info['end_time'];
			$info['start_time'] = (!empty($info['start_time'])) ? strtotime($info['start_time']) : 0;
			$info['end_time'] = (!empty($info['end_time'])) ? strtotime($info['end_time']) : 0;
			/* 注册时间 */
			if ($info['start_time'] && $info['end_time']){
				$sqlMap['inputtime'] = array("BETWEEN",array($info['start_time'],$info['end_time']));
			}else{
				if ($info['start_time'] > 0) {
				$sqlMap['inputtime'] = array("EGT", $info['start_time']);
				}
				if ($info['end_time'] > 0) {
					$sqlMap['inputtime'] = array("ELT", $info['end_time']);
				}
			}
		}
		$sqlMap['userid'] = $this->userid;
		$count = $this->cash->where($sqlMap)->count();
		$cash = $this->cash->where($sqlMap)->page($this->pagecurr, $this->pagesize)->order("check_time DESC,inputtime DESC")->select();
		$userinfo = getUserInfo($this->userid);
		$pages = showPage($count,$this->pagecurr,$this->pagesize);
		$SEO = seo(0,"提现记录");
		include template('cash_log');
	}

	/* 积分明细 */
	public function point_log(){
		if ($this->userinfo['modelid'] != 1) $this->error('请登录买家会员',U('member/index/login'));
		$userinfo = model('member')->find($this->userid);
		$sqlMap = array();
		$sqlMap['id'] = array('NEQ',2);
        $task = model('task')->where($sqlMap)->select(); 
        unset($sqlMap);
        $sqlmap = array();
        $sqlmap['userid'] = $this->userid;
		$sqlmap['type'] = 'point';
		$count = model('member_finance_log')->where($sqlmap)->count();	
		$point = model('member_finance_log')->where($sqlmap)->page($this->pagecurr,10)->order("id DESC")->select();
		$pages = showPage($count,$this->pagecurr,10);
		$v2_pages = v2_page_3($count,10);
		$SEO = seo(0,'积分明细记录');
		include template('buyer/integration');
	}

	


	public function convert_log(){
		$sqlMap['userid'] = $this->userid;
		$count = model('shop_log')->where($sqlMap)->count();
        $lists = model('shop_log')->where($sqlMap)->page($this->pagecurr, $this->pagesize)->order("apply_time DESC,id DESC")->select();
        foreach ($lists as $k => $v) {
            $shop = model('shop')->where(array('id'=>$v['shop_id']))->select();
	        foreach ($shop as $key => $s) {
	            $lists[$k]['title'] = $s['title'];
	            $lists[$k]['images'] = $s['images'];
	        }
        }       
        $pages = showPage($count,$this->pagecurr,$this->pagesize);
		$SEO = seo(0,'积分兑换记录');
		include template('buyer/convert');
	}

	/* 保证金明细 */
	public function capital_log(){
		if ($this->userinfo['modelid'] != 2) $this->error('请登录商家会员',U('member/index/login'));
		$sqlMap = array();
		if (IS_GET) {
			$info = I('get.');
			$info['start_time'] = (!empty($info['start_time'])) ? strtotime($info['start_time']) : 0;
			$info['end_time'] = (!empty($info['end_time'])) ? strtotime($info['end_time']) : 0;
			/* 出账时间 */
			if ($info['start_time'] && $info['end_time']){
				$sqlMap['dateline'] = array("BETWEEN",array($info['start_time'],$info['end_time']));
			}else{
				if ($info['start_time'] > 0) {
				$sqlMap['dateline'] = array("EGT", $info['start_time']);
				}
				if ($info['end_time'] > 0) {
					$sqlMap['dateline'] = array("ELT", $info['end_time']);
				}
			}
		}
		$userinfo = model('member')->find($this->userid);
		$sqlMap['userid'] = $this->userid;
		$sqlMap['type'] = 'deposit';
		$count = $this->db->where($sqlMap)->count();
		$capital = $this->db->where($sqlMap)->page($this->pagecurr, $this->pagesize)->order('dateline DESC')->select();
		foreach ($capital as $k => $v) {
			$factory = new \Product\Factory\product($v['goods_id']);
			$capital[$k]['title'] = $factory->product_info['title'];
			$capital[$k]['url'] = $factory->product_info['url'];
		}
		$pages = showPage($count,$this->pagecurr,$this->pagesize);
		$SEO = seo(0,'保证金流动记录');
		// 总保证金
		$sqlmap = array();
		$sqlmap['company_id'] = $this->userid;
		$ids = model('product')->where($sqlmap)->getField("id",TRUE);
		foreach ($ids as $id) {
			$deposit_all += model('product_rebate')->where(array('id'=>$id))->getField("goods_deposit");
			$deposit_all += model('product_trial')->where(array('id'=>$id))->getField("goods_deposit");
		}
		if (C('DEFAULT_STYLE') == 'cloud2') {
			$deposit_all += model('task_day')->where(array('status'=>1,'company_id'=>$this->userid))->sum('totalmoney');
		}
		// 冻结中的保证金
		$frozen_deposit = model('member_merchant')->where(array('userid' => $this->userid))->getField("frozen_deposit");
		include template('merchant/capital');		
	}

    /*提现记录 json*/
    public function cash_log_json(){
        $param = I('param.');
        if(empty($param)) exit(0);
        extract($param);
        $page = max(1, (int) $page);
        $num = (isset($num) && is_numeric($num)) ? abs($num) : 20;
        $sqlmap = array();
        //查处购物返利的2状态和申请试用的1状态
        if($userid == 1) $sqlmap['userid'] = $this->userid;
        $count = $this->cash->where($sqlmap)->count();
        $lists = $this->cash->where($sqlmap)->page($page, $num)->order($orderby.' '.$orderway)->select();
        if($lists == ""){
            $result['status'] = 0;
            echo json_encode($result);
            exit;
        }
        foreach($lists as $k=>$v){
            $lists[$k]['inputtime2'] = date("Y-m-d",$v['inputtime']);
        }
        $pages = page($count, $num);
        $result = array();
        $result['status'] = 1;
        $result['data'] = array(
            'count' => $count,
            'lists' => $lists,
            'pages' => $pages
        );
        echo json_encode($result);
    }
	/*日赚任务记录*/
	public function work_log(){
		$pagecurr = max(1,I('page',0,'intval'));
		$pagesize = 10;
		$sqlMap = array();
		$sqlMap['userid'] = $this->userid;
		$count = model('task_records')->where($sqlMap)->count();
		$task_log = model('task_records')->where($sqlMap)->page($pagecurr,$pagesize)->order('id DESC')->select();

		foreach ($task_log as $k=>$v) {
			$factory = new \Task\Factory\task($v['tid']);
			$r = $factory->task_info;
			$task_log[$k]['task'] = $r;
		}
		$pages = page($count,$pagesize);
		$v2_pages = v2_page_3($count,$pagesize);

		$SEO = seo(0, '日赚任务记录');
		//总金额
		$money = model('task_records')->where($sqlMap)->sum('price');
		include template('buyer/task_log');
	}
}