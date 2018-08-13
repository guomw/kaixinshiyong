<?php
namespace Pay\Controller;
use Admin\Controller\InitController;
use Pay\Library\Method;
if (!defined('PAY_CONF')) define('PAY_CONF', APP_PATH.'Pay/conf/');
class PayController extends InitController{
	public function _initialize(){
		parent::_initialize();
		$this->db  =  D('Order');
		$this->payment_db = D('Pay/PayPayment');
		$this->member_db = model('member');
		$this->modules_path = MODULE_PATH;
		$this->method = new Method($this->modules_path);
		$this->pay_db = model('pay_order');
	}

	/**
	 * 在线充值列表
	 */
	public function pay_list(){
		$sqlMap = array();
		if(submitcheck('dosubmit')){
			$info = I('info');
			$keyword = $info['keywords'];
			$start_addtime = $info['start_addtime'];
			$end_addtime = $info['end_addtime'];
			$type = $info['type'];
			if($type){
				switch ($type) {
					case '1':
						$uids = model('member')->where(array('nickname'=>array("LIKE", "%".$keyword."%")))->getfield('userid',true);
						$sqlMap['userid'] = array("IN", $uids);
						break;

					case '2':
						$sqlMap['trade_sn'] = array("like","%".$keyword."%");
						break;
				}

			}
			/*if(!empty($trade_sn)){$sqlMap['trade_sn'] = array("like","%$trade_sn%");}
			if(!empty($username)) {
				//根据输入的名称查出id
				$uids = model('member')->where(array('nickname'=>array("LIKE", "%".$username."%")))->getfield('userid',true);
				$sqlMap['userid'] = array("in",$uids);
			}*/
			if(!empty($start_addtime) && !empty($end_addtime)) {
				$start = strtotime($start_addtime.' 00:00:00');
				$end = strtotime($end_addtime.' 23:59:59');
				$sqlMap[]= "AND `ordtime` >= '$start' AND  `ordtime` <= '$end'";
			}
		}
		
		$pagecurr = max(1,I('page',0,'intval'));
		$pagesize = 10;
		$pay_count = $this->pay_db->where($sqlMap)->count();
		$pay_list = $this->pay_db->where($sqlMap)->page($pagecurr,$pagesize)->order('dateline DESC, id DESC')->select();
		foreach ($pay_list as $k=>$v) {
			$rs = $this->member_db->getByUserid($v['userid']);
			$pay_list[$k]['modelid'] = $rs['modelid'];
			//用户的真实姓名
			$identify = model('member_attesta')->where(array('userid'=>$v['userid'],'type'=>'identity'))->find();
			$infos = string2array($identify['infos']);
			$pay_list[$k]['real_name'] = $infos['name'];
		}
		$pages = page($pay_count,$pagesize);
		$form = new \Common\Library\form();
		include $this->admin_tpl('pay_list');
	}
	/**
	 * 查询订单信息
	 */
	public function public_pay_detail(){
		$orderid = I('id');
		$order_list = $this->pay_db->where(array('trade_sn'=>$orderid))->find();
		foreach ($order_list as $k=>$v) {
			$rs = $this->member_db->getByUserid($v['userid']);
			$order_list['username'] = $rs['nickname'];
			$order_list['email'] = $rs['email'];
			$order_list['telephone'] = $rs['phone'];
		}
		$show_header = true;
		include $this->admin_tpl('pay_detail');
	}
	/**
	 * 支付打折
	 */
	public function discount(){
		if(submitcheck('dosubmit')){
			$order = array();
			$orderid = I('id');
			$order['discount'] =  floatval($_POST['discount']);
			$infos = $this->pay_db->where("trade_sn = '$orderid'")->save($order);
			if(!$infos){
				$this->error($this->pay_db->getError());
			}
			$this->success('打折成功', 'javascript:close_dialog();');
		}else{
			$show_header = true;
			$show_validator = true;
			$orderid = I('id');
			$pay_discount = $this->pay_db->where(array('trade_sn'=>$orderid))->find();
			include $this->admin_tpl('pay_discount');
		}
	}

	/**
	 * 支付取消
	 */
	public function pay_cancel(){
		$id = intval($_GET['id']);
		$order['status'] = 2;
		$result = $this->pay_db->where("trade_sn = '$id'")->save($order);
		if(!$result){
			$this->error($this->pay_db->getError());
		}
		$this->success('交易取消成功', 'javascript:close_dialog();');
	}

	/**
	 * 删除支付记录
	 */
	public function pay_del(){
		$id = intval($_GET['id']);
		$result = $this->pay_db->where("trade_sn = '$id'")->delete();
		if(!$result){
			$this->error($this->pay_db->getError());
		}
		$this->success('删除成功',U('pay_list'));
	}
	/**
	 * 支付模块列表
	 */
	public function init(){
		$infos = $this->method->get_list();
		$show_dialog = true;
		include $this->admin_tpl('payment_list');
	}
	/**
	 * 安装支付模块
	 */
	public function add(){
		if(submitcheck('dosubmit')){
			$info = $infos = array();
			$pay_code =trim($_POST['pay_code']);//支付方法
			//根据支付方法查出该方法所有字段
			// $infos = $this->payment_db->where("pay_code = '$pay_code'")->find();
			$infos = $this->method->get_payment($pay_code);
			$config = $infos['config'];
			// $config = string2array($config);
			$cache_info = array();
			$config_value = (array)$_POST['config_value'];
			foreach ($config_value as $key => $value) {
				if(empty($key) || empty($value) || !isset($config[$key]))  continue;
				$config[$key]['value'] = $value;
				$cache_info[$key] = $value;
			}
			$info['config'] = array2string($config);
			$info['name'] = $_POST['name'];
			$info['pay_name'] = $_POST['pay_name'];
			$info['pay_desc'] = $_POST['description'];
			$info['pay_id'] = $_POST['pay_id'];
			$info['pay_code'] = $_POST['pay_code'];
			$info['is_cod'] = $_POST['is_cod'];
			$info['logo'] = $_POST['logo'];
			$info['is_online'] = $_POST['is_online'];
			if($_POST['pay_method'] == 1) {
				$info['pay_fee'] = $_POST['pay_fix'];
			} else {
				$info['pay_fee'] = $_POST['pay_rate'];
			}
			$info['pay_fee'] = intval($_POST['pay_fee']);
			$info['pay_method'] = intval($_POST['pay_method']);
			$info['pay_order'] = intval($_POST['pay_order']);
			$info['enabled'] = '1';
			$info['author'] = $infos['author'];
			$info['website'] = $infos['website'];
			$info['version'] = $infos['version'];
			
			setcache(strtolower($pay_code), $cache_info, 'pay');
			$result = $this->payment_db->add($info);
			if(!$result){
				$this->error($this->payment_db->getError());
			}
			$this->payment_db->build_cache();
			$this->success('支付接口安装成功', 'javascript:close_dialog();');
		}else{
			$pay_code = I('code');
			$infos = $this->method->get_payment($pay_code);
			extract($infos);
			$show_header = true;
			$show_validator = true;
			$form = new  \Common\Library\form();
			include $this->admin_tpl('payment_detail');
		}
	}
	/**
	 * 卸载支付模块
	 */
	public function delete(){
		$id = I('id');
		$result = $this->payment_db->where("pay_id = '$id'")->delete();
		if(!$result){
			$this->error($this->payment_db->getError());
		}
		$this->payment_db->build_cache();
		$this->success(L('delete_succ'),U('pay_list'));
	}
	/**
	 * 配置支付模块
	 */
	public function edit(){
		if(submitcheck('dosubmit')){
			$infos = $this->method->get_payment($_POST['pay_code']);
			$config = $infos['config'];
			$cache_info = array();
			$config_value = (array)$_POST['config_value'];
			foreach ($config_value as $key => $value) {
				if(empty($key) || empty($value) || !isset($config[$key]))  continue;
				$config[$key]['value'] = $value;
				$cache_info[$key] = $value;
			}
			$info['config'] = array2string($config);
			$info['name'] = trim($_POST['name']);
			$info['pay_name'] = trim($_POST['pay_name']);
			$info['pay_desc'] = trim($_POST['description']);
			$info['pay_id'] = $_POST['pay_id'];
			$info['pay_code'] = trim($_POST['pay_code']);
			$info['pay_order'] = intval($_POST['pay_order']);
			$info['pay_method'] = intval($_POST['pay_method']);	
			$info['pay_fee']  = (intval($_POST['pay_method'])==0) ? round($_POST['pay_rate'],2) : intval($_POST['pay_fix']);		
			$info['is_cod'] = trim($_POST['is_cod']);
			$info['is_online'] = trim($_POST['is_online']);
			$info['enabled'] = $_POST['is_open'];
			$info['author'] =  $infos['author'];
			$info['website'] = $infos['website'];
			$info['version'] = $infos['version'];
			$info['is_open'] = $_POST['is_open'];
			$this->payment_db->save($info);
			setcache(strtolower($_POST['pay_code']), $cache_info, 'pay');
			$this->payment_db->build_cache();
			$this->success('支付接口配置变更成功', 'javascript:close_dialog();');
		}else{
			$pay_id = I('id');
			$infos = $this->payment_db->where("pay_id = '$pay_id'")->find();
			extract($infos);
			$config = string2array($config);
			$show_header = true;
			$show_validator = true;
			$form = new  \Common\Library\form();
			include $this->admin_tpl('payment_detail');
		}
	}

	/**
	 * 后台充值入账
	 */
	public function modify_deposit(){
		if(submitcheck('dosubmit')){
			$email = I('email');
			$time = I('time');
			if(isemail($email) || is_mobile($email)){
				//交易备注
				$usernote = isset($_POST['usernote']) && trim($_POST['usernote']) ? addslashes(trim($_POST['usernote'])) :$this->error(L('usernote').L('error'));
				//根据用户名查出用户id 
				$userinfo = $this->member_db->where(array('email|phone' => $email))->field('userid')->find();
				if($userinfo){
	              
					$sqlMap = array();
					$pay_unit = I('pay_unit');//充值额度  有增加 ‘1’  减少 ‘0’ 增加及添加一个订单，减少及在用户的账户上相应的减少
					$userid = $sqlMap['userid'] = $userinfo['userid'];
					$sqlMap['email'] = $email;
					$sqlMap['inputtime'] = NOW_TIME;
					$sqlMap['cause'] = $usernote;
					$num = $sqlMap['money'] = ($pay_unit == 1) ? I('unit') : -I('unit');
					$sqlMap['ip'] = get_client_ip();
					$sqlMap['admin'] = get_admin_name(cookie('userid'));
					//新增记录
					$sign = '4-5-'.$userid.'-'.$num.'-'.$time;
					$rs = model('member_finance_log')->where(array('only'=>$sign))->find();
					if(!$rs){
					    $result = action_finance_log($userid,$num,'money','后台充值（'.$usernote.'）',$sign,array());
					    if($result){
					    	  $rs = model('pay_records')->add($sqlMap);
								if(!$rs){
									$this->error($this->db->getError());
								}
					    }
					    $this->success('交易成功');
					}else{
					    $this->error('交易失败,请重试');
					}
				}else{
					    $this->error('用户不存在');
				}
			}else{
				 $this->error('手机或者邮箱格式错误');
			}

		}else{
			$show_validator = true;
			include $this->admin_tpl('modify_deposit');
		}
	}	

	/*充值记录*/
	public function pay_deposit(){
		$pagecurr = max(1,I('page',0,'intval'));
		$pagesize = 10;
		$sqlMap = array();
		if(submitcheck('dosubmit')){
			$keyword = I('keyword');
			if(!empty($keyword)){
				$sqlMap['email'] = array("LIKE","%$keyword%");
			}
		}
		$count = model('pay_records')->where($sqlMap)->count();
		$lists = model('pay_records')->where($sqlMap)->page($pagecurr,$pagesize)->order('inputtime DESC')->select();
		$pages = page($count,$pagesize);
		foreach ($lists as $k=>$v){
			$lists[$k]['nickname'] = model('member')->getFieldByUserid($v['userid'],'nickname');
		}		
		$show_validator = true;
		$form = new  \Common\Library\form();
		include $this->admin_tpl('pay_deposite_list');
	}
	
	/**
	 * 检测用户名是否存在
	 */
	public function public_checkname_ajax(){
		$username = isset($_GET['username']) && trim($_GET['username']) ? trim($_GET['username']) : exit(0);
		if(CHARSET != 'utf-8') {
			$username = iconv('utf-8', CHARSET, $username);
			$username = addslashes($username);
		}
		$result = $this->member_db->where("username = '$username'")->find();
		if ($result ){
			//显示用户余额多少 点数
			exit(L('point').'  '.$result['point']);
		} else {
			exit('FALSE');
		}
	}
	/**
	*检测邮箱或者手机是否存在
	*/
	public function public_checkemail_ajax($email = '') {
		if(isemail($email) || is_mobile($email)) {
			$sqlmap = array();
	        $sqlmap['email|phone'] = $email;
			$rs = model('member')->where($sqlmap)->count();
	        if($rs > 0) {
	           $this->success('可用');
	        } else {
	           $this->error('没有此用户');
	        }
        }else{
            $this->error('格式错误');
        }

	}
	
}