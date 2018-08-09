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
 * 后台会员模型管理
 */
class MemberController extends InitController {
	public function _initialize() {
		parent::_initialize();
		$this->db = model('member');
		$this->module_db = model('module');
		$this->model_db = model('model');
		$this->setting = model('setting');
		$this->pagecurr = max(1, I('page', 0, 'intval'));
		$this->pagesize = 10;
		api('Cache/run', 'member_group');
	}

	/**
	 * 会员管理
	 * @author   <jason>
	 */
	public function manage() {
		$sqlMap = array();		
		$t = I('t');
		if($t == 2){//商家
			$grouplist = getcache('merchant_group', 'member');
		}else{
			$grouplist = getcache('member_group', 'member');
			foreach ($grouplist as $k => $v) $grouplist[$k] = $v['name'];	
			
		}
		$modellists = getcache('model', 'commons');
		$modellist = array();
		foreach ($modellists as $k => $v) {
			if($v['module'] != 'member' || $v['disabled'] == 1) continue;
			$modellist[$k] = $v['name'];
		}	
		if ($t) {
			if ($t == 3) {//会员推广
				$sqlMap['agent_id'] = array('NEQ','');
			}elseif($t == 4){
				$sqlMap['status'] = array('EQ',0);
			}else{
				$sqlMap['modelid'] = $t;
			}
		}
		if (IS_GET) {
			$info = I('get.');
			$info['start_time'] = (!empty($info['start_time'])) ? strtotime($info['start_time']) : 0;
			$info['end_time'] = (!empty($info['end_time'])) ? strtotime($info['end_time']) : 0;
			/* 注册时间 */
			if ($info['start_time'] && $info['end_time']){
				$sqlMap['regdate'] = array("BETWEEN",array($info['start_time'],$info['end_time']));
			}else{
				if ($info['start_time'] > 0) {
				$sqlMap['regdate'] = array("EGT", $info['start_time']);
				}
				if ($info['end_time'] > 0) {
					$sqlMap['regdate'] = array("ELT", $info['end_time']);
				}
			}
			/* 当前状态 */
			$info['status'] = (int) $info['status'];
			if ($info['status'] > 0) {
				$sqlMap['islock'] = $info['status'];
			}
			/* 会员组 */
			$info['groupid'] = (int) $info['groupid'];
			if ($info['groupid'] > 0) {
				$sqlMap['groupid'] = $info['groupid'];
			}
			/* 关键字搜索类型 */
			$info['type'] = (int) $info['type'];
			if (trim($info['keyword'])) {
				switch ($info['type']) {
					case '1': //用户名
						$sqlMap['username'] = array("LIKE", "%".$info['keyword']."%");
						break;
					case '2': // 用户ID
						$sqlMap['userid'] = array("LIKE", "%".intval($info['keyword'])."%");
						break;
					case '3': // 注册邮箱
						$sqlMap['email'] = array("LIKE", "%".$info['keyword']."%");
						break;
					case '4': // 注册IP
						$sqlMap['regip'] = array("LIKE", "%".$info['keyword']."%");
						break;
					case '6': //用户名
						$sqlMap['phone'] = array("LIKE", "%".$info['keyword']."%");
						break;
					case '7': //用户名
						$sqlMap['userid'] = array("EQ",$info['keyword']);
						break;								
					default:
						$sqlMap['nickname'] = array("LIKE", "%".$info['keyword']."%");
						break;
				}
			}
		}
		$membercount = $this->db->where($sqlMap)->count();
		$memberlist = $this->db->where($sqlMap)->page($this->pagecurr, $this->pagesize)->order('userid DESC ,regdate DESC')->select();
		foreach ($memberlist as $k=>$v) {
			//参与活动的次数/总次数
			$memberlist[$k]['order_count'] = order_count($v['userid'],1);
			$memberlist[$k]['success_order_count'] = order_count($v['userid'],1,7);
		}
		$pages = page($membercount, $this->pagesize);
		/* 附加菜单 */
		if($t == 1 || $t == 4){
			$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('add',array('t'=>1)).'\', title:\''.L('member_add').'\', width:\'700\', height:\'500\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('member_add'));
		}elseif($t == 3){

		}else{
			$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('add',array('t'=>2)).'\', title:\''.L('merchant_add').'\', width:\'700\', height:\'500\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('merchant_add'));
		}
		$form =  new \Common\Library\form();
		include $this->admin_tpl('member_list');
	}

	/**
	 * 添加会员
	 * @author   <jason>
	 */
	public function add() {
		//会员模型
		$models = getcache('model', 'commons');
		$modelid = I('modelid', '1', 'intval');
		$groupid = I('groupid', '1', 'intval');
		$groups = ($modelid == 1) ? getcache('member_group','member') : getcache('merchant_group','member');
		$setting = getcache('setting', 'member');
	    $setting['register_groupid'];
		if (IS_POST) {
			$info = $_POST['info'];
			$info['modelid'] = $modelid;
			$MemberLogic = D('Member', 'Logic');
			$result = $MemberLogic->register($info);
			if (!$result) {
				$this->error($MemberLogic->getError());
			}
			$this->success('操作成功');
		} else {
			$form =  new \Common\Library\form();		
			$show_header = true;
			$show_validator = true;
			require MODULE_CACHE.'member_form.class.php';
			$member_form = new \member_form($modelid);
			//$forminfos = $member_form->get();
			include $this->admin_tpl('member_add');
		}
	}
	/**
	 * 判断用户名是否存在
	 */
	public function public_checkname_ajax(){
		$username = isset($_GET['username']) && trim($_GET['username']) ? trim($_GET['username']) : exit(0);
		if(CHARSET != 'utf-8') {
			$username = iconv('utf-8', CHARSET, $username);
			$username = addslashes($username);
		}
		$status = $this->db->where(array('username'=>$username))->count();
		if($status > 0) {
			exit('0');
		} else {
			exit('1');
		}
	}
	/**
	 * 判断昵称是否存在
	 */
	public function public_checknickname_ajax(){
		$nickname = isset($_GET['nickname']) && trim($_GET['nickname']) ? trim($_GET['nickname']) : exit(0);
		if(CHARSET != 'utf-8') {
			$nickname = iconv('utf-8', CHARSET, $nickname);
			$nickname = addslashes($nickname);
		}
		$status = $this->db->where(array('nickname'=>$nickname))->count();
		if($status > 0){
			exit('0');
		}else{
			exit('1');
		}
	}
	/**
	 * 判断邮箱是否存在
	 */
	public function public_checkemail_ajax(){
		$email = isset($_GET['email']) && trim($_GET['email']) ? trim($_GET['email']) : exit(0);
		$userid = $_GET['userid'];
		$data = array();
		if ($userid) {
			$data['userid'] = array('NEQ',$userid);
		}

		$data['email'] = array('EQ',$email);
		
		$status = $this->db->where($data)->count();
		if($status > 0){
			exit('0');
		}else{
			exit('1');
		}
	}
	/* 检测手机号是否可用 */
	public function public_checkphone_ajax(){
		$mobile = $_GET['phone'];
		if(!is_mobile($mobile)) {
			$this->error('手机号码格式错误');
		}
		$sqlmap = array();
		$sqlmap['phone'] = $mobile;
		if(model('member')->where($sqlmap)->count() > 0) {
			$this->error('该手机号已被占用');
		} else {
			$this->success('该手机号可用');
		}
	}
	
	/**
	 * 编辑会员
	 * @author   <masater@ .com>
	 */
	public function edit($userid = 0) {
	    //所属专员
	    $roleid = model('admin_role')->getFieldByRolename('招商专员','roleid');
	    $attract_lists = model('admin')->where(array('roleid'=>$roleid))->field('userid,username')->select();
	    //查出后台用户的角色名称
	    $admin_userid = session('userid');
	    $r = model('admin')->alias('a')->join(C('DB_PREFIX').'admin_role AS r ON a.roleid = r.roleid')->where(array('userid'=>$admin_userid))->field('rolename,a.roleid')->find();
	    $rolename = $r['rolename'];
        
	    $userid = (int) $userid;
		if($userid < 0 ){$this->error('参数错误');}
		$member_info = $this->db->where(array('userid'=>$userid))->find();

		$modelid = $this->db->getFieldByUserid($userid,'modelid');
		$groupid = $this->db->getFieldByUserid($userid,'groupid');
		$grouplist = model('merchant_group')->getField('groupid,name');
		//会员模型
		$models = getcache('model','commons');
		$tablename = $models[$modelid]['tablename'];
		$member_infos = M($tablename)->where(array('userid'=>$userid))->find();
		if(empty($member_info)){
			$memberinfo = $member_infos;
		}else if(empty($member_infos)){
			$memberinfo = $member_info;
		}else{
			$memberinfo = array_merge($member_info,$member_infos);
		}

		$moreinfo = member_info($userid);

		//会员组
		$member_group = getcache('member_group','member');
		if (submitcheck('dosubmit')) {
			$info = I('post.');	
			$info_group = $info['info'];
			$infos = $info['infos'];
			if ($infos) {
				if (!empty($infos['alipay_account'])) {
					$arr = array('username'=>$moreinfo['a_username'],'alipay_account'=>$infos['alipay_account'],'alipay_code'=>$moreinfo['alipay_code']);
					$sqlmap = array();
					$sqlmap['infos'] = array2string($arr);
					$alipay = model('member_attesta')->where(array('userid'=>$userid,'type'=>'alipay'))->save($sqlmap);
					
				}

				if (!empty($infos['bank_account'])) {
					$arr = array('bank_name'=>$moreinfo['bank_name'],'account'=>$infos['bank_account'],'province'=>$moreinfo['province'],'city'=>$moreinfo['city'],'area'=>$moreinfo['area'],'sub_branch'=>$moreinfo['sub_branch']);
					$sqlMap = array();
					$sqlMap['infos'] = array2string($arr);
					$bank = model('member_attesta')->where(array('userid'=>$userid,'type'=>'bank'))->save($sqlMap);		

				}


				if (!empty($infos['id_number'])) {
					$arr = array('id'=>$userid,'name'=>$infos['name'],'id_number'=>$infos['id_number']);
					$sqlMap = array();
					$sqlMap['infos'] = array2string($arr);
					$bank = model('member_attesta')->where(array('userid'=>$userid,'type'=>'identity'))->save($sqlMap);		

				}


			}

			if($modelid == 1){
				//把上传的文件移入文件
				$avatar = $info_group['avatar'];
				//需移入的文件夹目录
				$suid = sprintf("%09d", $userid);
				$dir1 = substr($suid, 0, 3);
				$dir2 = substr($suid, 3, 2);
				$dir3 = substr($suid, 5, 2);
				$rootDir = SITE_PATH.'/uploadfile/avatar/';
				$userDir = $dir1.'/'.$dir2.'/'.$dir3.'/';
				
			 	//头像新文件名
		        $filename = $rootDir.$userDir.$userid.'_avatar.jpg';

		        // 调取150*150的缩略图
		        $list = explode('.', $avatar);
		        $avatars = $list['0'].'_150.'.$list['1'];
		      
		        if (file_exists(SITE_PATH.$avatars)) {
		        	 $result = $this->moveFile(SITE_PATH.$avatars,$filename);
		        }
				
			}

			

			if($info_group['password'] != ''){
				$info_group['password'] = md5(md5($info_group['password'].$member_info['encrypt']));
			}else{
				$info_group['password'] = $member_info['password'];
			}

			if (isset($info_group['group_endtime'])) {
				$info_group['group_endtime'] = strtotime($info_group['group_endtime']);

			}


			if($tablename){
				$rs = M($tablename)->where(array('userid'=>$userid))->save($info_group);
			}

			

			$result = $this->db->where(array('userid'=>$userid))->save($info_group);
		
			if(!$result && $rs){
				$this->error($this->db->getError());
			}
			$this->success('操作成功','javascript:close_dialog();');
		} else {
            $form = new \Common\Library\form(); 
			$form_overdudate = $form::date('lastdate', date('Y-m-d H:i:s',$memberinfo['lastdate']), 1);
			include $this->admin_tpl('member_edit');
		}				
	}

	public function delete() {
		$models = getcache('model','commons');
		if (IS_POST && I('fromhash') == session('FROMHASH')) {
			$userid = (array) $_POST['userid'];
			if (!empty($userid)) {
				foreach ($userid as $uid) {
					$modelid = $this->db->getFieldByUserid($uid, 'modelid');
					if ($models[$modelid]['module'] == 'member') {
						$tablename = $models[$modelid]['tablename'];
						if ($tablename) {
							M($tablename)->where(array('userid' => $uid))->delete();
						}
					}
					$this->db->where(array('userid' => $uid))->delete();
				}
			}
			$this->success('操作成功');
		} else {
			$this->error('请勿非法访问');
		}
	}

	/**
	 * 会员锁定
	 * @author   <jason>
	 */
	public function lock() {
		$userid = (int) I('userid');
		if($userid < 1) $this->error('请勿非法访问');
		if (IS_POST && I('fromhash') == session('FROMHASH')) {
			$cause = I('cause');
			if (!empty($userid)) {
				$result = $this->db->where(array('userid' => $userid))->setField(array('islock'=>'1','cause'=>$cause));
			}
			if(!$result){
				$this->error('冻结会员失败');
			}
			$this->success('已将指定会员冻结','javascript:close_dialog();');
		} else {
			include $this->admin_tpl('member_lock');
		}				
	}

	/**
	 * 会员解锁
	 * @author   <jason>
	 */
	public function unlock() {
		$userid = (int) $_GET['userid'];
		if($userid < 1)$this->error('请勿非法访问');
		if (!empty($userid)) {
			$this->db->where(array('userid' => $userid))->setField('islock','0');
		}
		$this->success('已将指定会员解锁');
	}

    /**
     * 赠送vip会员
     * @author   <jason>
     */
    public function addvip() {
        $userid = (int) I('userid');
        if($userid < 1) $this->error('请勿非法访问');

        $sqlmap = array();
        $sqlmap['groupid'] = array('neq',1);
        $merchant_group = M('MerchantGroup')->field('groupid,name')->where($sqlmap)->select();

        if (IS_POST && I('fromhash') == session('FROMHASH')) {
            $info = I('info');
            $groups = getcache('merchant_group','member');

            $groupid = $this->db->getFieldByUserid($userid, 'groupid');
            $name = $groups[$groupid]['name'];//商家级别
            $name2 = $groups[$info['groupid']]['name'];
            if($groupid != $info['groupid'] && $groupid != 1){
                $this->error('该商家已经是'.$name.'了,不能赠送'.$name2);
            }

            $grouptime = $this->db->getFieldByUserid($userid, 'grouptime');
            $group_endtime = $this->db->getFieldByUserid($userid, 'group_endtime');

            if($group_endtime != 0){
                $group_endtime = strtotime($info['groupday']." days",$group_endtime);
            }else{
                $group_endtime = strtotime($info['groupday']." days",NOW_TIME);
            }
            $info['grouptime'] = NOW_TIME;
            $info['userid'] = $userid;
            $info['group_endtime'] = $group_endtime;
            unset($info['groupday']);

            $result = $this->db->update($info);

            if(!$result){
                $this->error('赠送VIP会员失败');
            }

            runhook('zeng_vip_message',array('userid'=>$userid,'type'=>'message,email,sms','name'=>$name2));
            $this->success('已将指定会员赠送VIP','javascript:close_dialog();');
        } else {
            include $this->admin_tpl('member_addvip');
        }
    }

	/**
	 * 会员审核
	 * @author   <jason>
	 */
	public function verify() {
		if (submitcheck('dosubmit')) {
			$params = I('request.');
			$uids = (array) $params['userid'];
			$type_var = array('pass' => '1', 'reject' => '-1', 'ignore' => '-2');
			$sqlMap = array();
			$sqlMap['userid'] = array("IN", $uids);
			if ($params['type'] != 'delete') {
				$status_val = $type_var[$params['type']];
				$this->db->where($sqlMap)->setField('status', $status_val);
				/* 发送邮件通知 */
				if ($params['sendemail'] && $params['message']) {
					foreach ($uids as $key => $uid) {
						$email = $this->db->getFieldByUserid($uid, 'email');
						helpers('mail');
						sendemail($email, '会员审核通知', $params['message']);
					}
				}
			} else {
				$this->db->where($sqlMap)->delete();
			}
			$this->success('操作成功');
		} else {
			$member_model = getcache('model', 'commons');
			$verify_status = array('-2' => '忽略', '-1' => '拒绝', '0' => '待审', '1' => '通过');
			$sqlMap = array();
			$sqlMap['modelid'] = I('t');
			$sqlMap['status'] = array("NEQ", 1);
			$membercount = $this->db->where($sqlMap)->count();
			$memberlist = $this->db->where($sqlMap)->page($this->pagecurr, $this->pagesize)->select();
			$pages = page($membercount, $this->pagesize);
			include $this->admin_tpl('member_verify');
		}	
	}

	/**
	 * 查询会员资料
	 * @author   <jason>
	 */
	public function memberinfo($userid = 0) {
		$userid = (int) $userid;
		if($userid < 0){$this->error('参数错误');}
		$memberinfo = member_info($userid);

        /*获取已绑定的淘宝帐号信息*/
		if($memberinfo['modelid'] == 1){
          $taobaoinfo = model('member_bind')->where(array('userid' =>$userid ))->select();
		}

		$show_header = true;
		$manage_lists = model('merchant_store')->where(array('userid'=>$userid))->select();
		if (!$manage_lists) {
		 	$manage_lists = model('member_merchant')->where(array('userid'=>$userid))->select();
		 } 
		foreach ($manage_lists as $k=>$v) {
			$sqlmap['userid'] = $v['userid'];
			$rs = M($this->tablename)->where($sqlmap)->find();
			foreach ($rs as $_k=>$_v) {
				$manage_lists[$k][$_k] = $_v;
			}

		if ($v['industry']) {
			$manage_lists[$k]['type'] = model('product_category')->where(array('catid'=>$v['industry']))->getField('catname');

			}
		}
		include $this->admin_tpl('member_moreinfo');
	}

	public function setting() {
		$models = getcache('model', 'commons');
		foreach ($models as $k => $v) {
			if($v['module'] != 'member' || $v['disabled'] == 1) unset($models[$k]);
		}
		$_grouplist = getcache('member_group', 'member');
		$grouplist = array();
		foreach ($_grouplist as $k => $v) {
			// if($k < 2) continue;
			$grouplist[$k] = $v['name'];
		}
		$member_setting = $this->module_db->getFieldByModule('Member', 'setting');
		$member_setting = unserialize($member_setting);
		if (submitcheck('dosubmit')) {
			$info = $_POST['info'];
			$result = $this->module_db->where(array('module' => 'Member'))->setField('setting', serialize($info));
			if (!$result) {
				$this->error('模块配置失败');
			}
			setcache('setting', $info, strtolower(MODULE_NAME));
			$this->success('操作成功');
		} else {
			$form = new \Common\Library\form();
			include $this->admin_tpl('member_setting');			
		}
	}
	/**
	 * 用户奖励
	 */
	public function aword(){
		$userid = I('userid');
		if(submitcheck('dosubmit')){
			//将该信息加入账户明细表
			$info = I('info');
			if($info['type'] == 0){
				$type = 'exp';
			}elseif($info['type'] == 1){
				$type = 'point';
			}else{
				$type = 'money';
			}
			$sign = '6-1-'.$info['userid'].'-'.$type.'-'.$info['value'].'-'.dgmdate(time());

			if (C('sso_is_open') == 1) {
				  unset($infos);
				  $infos = array();
				  $infos['userid'] = $info['userid'];
		          $infos['type'] = $type;
		          $infos['type_id'] = '1303' ;
		          $infos['num'] = $info['value'];
		          $infos['admin_id'] = cookie('userid');
		          $ret =  _ps_send($type,$infos);
		          $data = php_data($ret);
		          if ($data['status'] == 1) {
		          	$result = action_finance_log($info['userid'],$info['value'],$type,$info['cause'],$sign,'',TRUE);

					if($result){
						$this->success('操作成功');
					}else{
						$this->success('操作失败');
					}
		          }


			}else{

				$result = action_finance_log($info['userid'],$info['value'],$type,$info['cause'],$sign,'',TRUE);

				if($result){
					$this->success('操作成功','javascript:close_dialog();');
				}else{
					$this->success('操作失败');
				}

			}


			
		}else{
			include $this->admin_tpl('member_aword');
		}
	}
	/**
	 * 账户明细
	 */
	public function detail($userid = 0){
		$userid = (int) $userid;
		$modelid = I('modelid');
		//账户余额
		$money = model('member')->getFieldByUserid($userid,'money');
		//提现中的金额
		$deposite = model('cash_records')->where(array('userid'=>$userid,'status'=>0))->sum('money');
		//历史总收入
		$sql = array();
		$sql['userid'] = $userid;
		$sql['type'] = 'money';
		$sql['num'] = array("GT",0);
		$money_total = model('member_finance_log')->where($sql)->sum('num');
		//历史总支出
		$expend = array();
		$expend['userid'] = $userid;
		$expend['type'] = 'money';
		$expend['num'] = array("LT",0);
		$expend_money_total = model('member_finance_log')->where($expend)->sum('num');
		//异常资金 历史总收入- 历史总支出 是否等于= 账户余额）
		$a = sprintf('%2f',$money_total - abs($expend_money_total));
		$b = $money;
		if($a == $b){
            $anomaly = 0;
		}else{
		    $anomaly = $a - $b;
		}
		//累积成功提现
		$success_deposite = model('cash_records')->where(array('userid'=>$userid,'status'=>1))->sum('money');
		$pagecurr = max(1,I('page',0,'intval'));
		$pagesize = 20;
		$sqlMap = array();
		$sqlMap['userid'] = $userid;
		$sqlMap['type'] = 'money';
		$param = I('param.');
		$search = $param['search'];
		$type = (isset($search['type'])) ? $search['type'] : -99;
		if(IS_GET){
		    $start_time = (!empty($search['start_time'])) ? strtotime($search['start_time']) : 0;
		    $end_time = (!empty($search['end_time'])) ? strtotime($search['end_time']) : 0;
		    if ($start_time && $end_time){
		        $sqlMap['dateline'] = array("BETWEEN",array($start_time,$end_time));
		    }else{
		        if ($start_time > 0) {
		            $sqlMap['dateline'] = array("EGT", $start_time);
		        }
		        if ($end_time > 0) {
		            $sqlMap['dateline'] = array("ELT", $end_time);
		        }
		    }
		    if($type > -99){
		        switch($type){
		            case 1://提现
		                $sqlMap['cause'] = array("LIKE","%提现%");
		                break;
		            case 2://充值
		                $sqlMap['cause'] = array("LIKE","%充值%");
		                break;
		            case 3://订单结算
		                $sqlMap['cause'] = array("LIKE",array("%已完成%",'%订单结算%'),"or");
		                break;
		            case 4://活动结算
		                $sqlMap['cause'] = array("LIKE",array("%保证金%","%担保金%"),'or');
		                break;
		            case 5://vip费用
		                $sqlMap['cause'] = array("LIKE",array("%vip%","%成为%"),"or");
		                break;
		        }
		    }
		}
		$count = model('member_finance_log')->where($sqlMap)->count();
		$lists = model('member_finance_log')->where($sqlMap)->page($pagecurr,$pagesize)->order('id DESC')->select();
// 		echo model('member_finance_log')->getLastSql();
		foreach ($lists as $k=>$v) {
			$lists[$k]['username'] = $this->db->getFieldByUserid($v['userid'],'nickname');
		}
		$pages = page($count,$pagesize);
		$form = new \Common\Library\form();
		include $this->admin_tpl('member_detail');
	} 
	/**
	 * 删除账户记录
	 */
	public function detail_delete($ids = array()){
		$ids = (array) $ids;
		if(empty($ids)){$this->error('参数错误');}
		foreach ($ids as $k=>$id){
			$id = (int) $id;
			$this->account_db->where(array('id'=>$id))->delete();			
		}
		$this->success('操作成功');
	}

	public function dialog() {
		$sqlmap = array();
		if (submitcheck('search','G')) {
			$post_info = I('param.');
			$type = $post_info['field'];
			switch ($type) {
				case '1':
					$sqlmap['store_name'] = array("LIKE", "%".$post_info['keywords']."%");
					break;
				case '2':
					$sqlmap['userid'] = array("IN",$post_info['keywords']);
					break;
				default:
					
					break;
			}
			
		}


		$count = model('merchant_store')->where($sqlmap)->count();
		$infos = model('merchant_store')->where($sqlmap)->page($this->pagecurr, 10)->select();
		$pages = page($count, 10);
		$form =  new \Common\Library\form();
		$show_header = TRUE;
		include $this->admin_tpl('member_company_dialog');
	}


	/* 绑定淘宝设置 */
	public function set_bind_tb() {
        $setting = $this->setting->getField('key,value');
        $setting['bind_safe_grade'] = string2array($setting['bind_safe_grade']);

		if (submitcheck('dosubmit')) {
            $info = $_POST['setting'];
            if (empty($info)) $this->error('参数错误');


            foreach ($info as $k => $v) {
                if(is_array($v)) $v = array2string($v);
                $this->setting->where(array('key' => $k))->setField('value', $v);
            }
            $info = $this->setting->getField('key, value', TRUE);
            @file_put_contents(CONF_PATH.'setting.php', "<?php \n return ".array2string(array_change_key_case($info,CASE_UPPER)).";\n?>");
            $this->success('操作成功');
        } else {
            include $this->admin_tpl('set_bind_tb');
        }		
	}


	/* 绑定淘宝设置 */
	public function set_bind_store() {
        $setting = $this->setting->getField('key,value');
		if (submitcheck('dosubmit')) {
            $info = $_POST['setting'];
            if (empty($info)) $this->error('参数错误');
            foreach ($info as $k => $v) {
                if(is_array($v)) $v = array2string($v);
                $this->setting->where(array('key' => $k))->setField('value', $v);
            }
            $info = $this->setting->getField('key, value', TRUE);
            @file_put_contents(CONF_PATH.'setting.php', "<?php \n return ".array2string(array_change_key_case($info,CASE_UPPER)).";\n?>");
            $this->success('操作成功');
        } else {
            include $this->admin_tpl('set_bind_store');
        }		
	}


	public function createDir($aimUrl) {
        $aimUrl = str_replace('', '/', $aimUrl);
        $aimDir = '';
        $arr = explode('/', $aimUrl);
        $result = true;
        foreach ($arr as $str) {
            $aimDir .= $str . '/';
            if (!file_exists($aimDir)) {
                $result = mkdir($aimDir);
            }
        }
        return $result;
    }
    /**
     * 移动文件
     *
     * @param string $fileUrl
     * @param string $aimUrl
     * @param boolean $overWrite 该参数控制是否覆盖原文件
     * @return boolean
     */
    function moveFile($fileUrl, $aimUrl, $overWrite = false) {
        if (!file_exists($fileUrl)) {
            return false;
        }
        if (file_exists($aimUrl) && $overWrite = false) {
            return false;
        } elseif (file_exists($aimUrl) && $overWrite = true) {
            $this->unlinkFile($aimUrl);
        }
        $aimDir = dirname($aimUrl);
        $this->createDir($aimDir);
        // exit($fileUrl);
        copy($fileUrl, $aimUrl);
        return true;
    }

    /**
     * 删除文件
     *
     * @param string $aimUrl
     * @return boolean
     */
    function unlinkFile($aimUrl) {
        if (file_exists($aimUrl)) {
            unlink($aimUrl);
            return true;
        } else {
            return false;
        }
    }

    /*淘宝绑定*/
    function bind(){
    	if (IS_GET) {
			$info = I('get.');
			if ($info['type'] == 1) {
				$sqlMap['account'] = array("LIKE", "%".$info['keyword']."%");
			}elseif ($info['type'] == 2) {
				$emails = model('member')->where(array('email'=>array("LIKE", "%".$info['keyword']."%")))->getfield('userid',true);
				$sqlMap['userid'] = array("IN", $emails);
				
			}
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
    	$count = model('member_bind')->where($sqlMap)->count();
		$lists = model('member_bind')->where($sqlMap)->page($this->pagecurr, $this->pagesize)->order('id DESC')->select();
		foreach ($lists as $k => $v) {
		 	  $lists[$k]['userinfo'] = getUserInfo($v['userid']);
		 }

		$pages = page($count, $this->pagesize); 
    	$form =  new \Common\Library\form();
    	include $this->admin_tpl('bind_list');
    }

    public function bind_delete($ids = array()){

            $ids = (array) I('ids');
            if (empty($ids)) {
                $this->error('参数错误');
            }
            foreach ($ids as $id) {
                $id = (int) $id;
              
                model('member_bind')->where(array('id' => $id))->delete();
               
            }
            $this->success('操作成功');
    }
	

    //临时进入商家会员的空间
    public function user_jinru($uid){
         $userinfo = $this->db->find($uid);
         cookie('_userid', $userinfo['userid'], 3600);
         cookie('_groupid', $userinfo['groupid'], 3600);
         cookie('_modelid', $userinfo['modelid'], 3600);
    }
    

}