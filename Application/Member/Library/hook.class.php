<?php
namespace Member\Library;
class hook {
	public function system_init() {	/* 所有订单相关的超时 */
		// 时间内未下单自动关闭订单	[购物返利]
		$write_order = C_READ('buyer_write_order_time','rebate')* 60;	// 填写订单号时间
		$ids = model('order')->where(array('act_mod' => 'rebate','status'=>2,'create_time' => array("LT", NOW_TIME - $write_order)))->limit(10)->getfield('id',TRUE);
		if(is_array($ids)){
			foreach ($ids as $id) {
				$factory = new \Product\Factory\order($id);
				$factory->set_status(0,'下单时间超时，订单自动关闭');
			}
		}


		/*未填写试用报告,写入数据库*/
/*		$rebate_two_order = model('order')->where(array('act_mod'=>'rebate','status'=>2,'two_notice'=> array('NEQ',3)))->select();
		foreach ($rebate_two_order as $key => $value) {
			$_rebate_time = $value['create_time'] + C_READ('buyer_write_order_time','rebate')*60 - C('two_notice.time')*60;

			if (NOW_TIME >= $_rebate_time  && $_rebate_time > 0) {
					$queue = A('Queue/Index') ;
				    $queue->queue($value['id'],3);
					model('order')->where(array('id'=>$value['id']))->save(array('two_notice'=>3));
			}
				
			
		}*/
		
		// 商家未审核订单号; [购物返利]
		$check_order = C_READ('seller_check_time','rebate')*86400;	// 商家审核时间
		$check_type = (int)C_READ('seller_no_check_order','rebate');	// 操作方式
		if ($check_type == 1 || $check_type == 2){		//当后台设置为自动审核时才执行
			$ids = model('order')->where(array('act_mod' => 'rebate','status'=>3,'check_time' => array("LT", NOW_TIME - $check_order)))->limit(10)->getfield('id',TRUE);
			
			if(is_array($ids)){
				foreach ($ids as $id) {
					$factory = new \Product\Factory\order($id);
					if ($check_type === 1){	//  自动审核成功
					    $sql = "select order_id,userid,num,dateline,Count(*) from ".C('DB_PREFIX')."member_finance_log where order_id = ".$factory->order_info['id']." and num>0 group by order_id,userid,num having Count(*)>=1 order by id desc";
					    $r = model()->query($sql);
					    if(!$r){
					        $factory->pay();
					    }
					}elseif($check_type === 2){	//	自动审核失败
						$cause = '审核时间超时，订单自动审核失败';
						$factory->refuse($cause);
					}
				}
			}

			$ids_s = model('order')->where(array('act_mod' => 'rebate','status'=>5,'check_time' => array("LT", NOW_TIME - $check_order)))->limit(10)->getfield('id',TRUE);
				if (is_array($ids_s)) {
					foreach ($ids_s as $s) {
						$factory = new \Product\Factory\order($s);
						if ($check_type === 1){	//  自动审核成功
						    $sql = "select order_id,userid,num,dateline,Count(*) from ".C('DB_PREFIX')."member_finance_log where order_id = ".$factory->order_info['id']." and num>0 group by order_id,userid,num having Count(*)>=1 order by id desc";
						    $r = model()->query($sql);
						    if(!$r){
						        $factory->pay();
						    }
						}
					}
				}

		}



		//  订单审核失败后会员未在时间内进行申诉：关闭该订单; [购物返利]
		$check_appeal = (int)C_READ('buyer_check_update_order_sn','rebate')*3600;	//	失败后可以申诉的时间
		$rebate_ids = model('order')->where(array('act_mod' => 'rebate','status'=>4,'check_time' => array("LT", NOW_TIME - $check_appeal)))->limit(10)->getfield('id',TRUE);
		$cause = '申诉时间超时，订单自动关闭';
		if(is_array($rebate_ids)){
			foreach ($rebate_ids as $r_id) {
				$factory = new \Product\Factory\order($r_id);
				$factory->set_status(0,$cause);
			}
		}
		
		// 商家所有超时的申诉
		$seller_get_appeal = string2array(C_READ('seller_get_appeal','rebate'));
		if($seller_get_appeal['time']) {
			$seller_get_appeal['time'] = $seller_get_appeal['time'] * 3600;
			if($seller_get_appeal['check'] == 2) {
				model('appeal')->where(array('appeal_status' => 0, 'buyer_time' => array("LT", NOW_TIME - $seller_get_appeal['time'])))->setField('appeal_status', 1);
			} else {
				$appeals = model('appeal')->where(array('appeal_status' => 0, 'buyer_time' => array("LT", NOW_TIME - $seller_get_appeal['time'])))->limit(50)->select();
				if(is_array($appeals)){
					foreach ($appeals as $appeal) {
						$factory = new \Product\Factory\order($appeal['order_id']);
						switch ($appeal['appeal_type']) {
							/* 修改单号 */
							case '1':
								$result = $factory->set_status(2,'商家超时，系统自动审核通过');
								break;
							case '2':
								$result = $factory->set_status(5,'商家超时，系统自动审核通过');
								$result = $factory->pay();
								break;
							case '3':
								$result = $factory->set_status(0,'商家超时，系统自动审核通过');
								break;
							default:
								break;
						}
						if($result) model('appeal')->where(array('id'=>$appeal['id']))->setfield('appeal_status',4);
					}
				}
			}
		}
		
		// 申请资格审核时间,过期自动关闭订单 [免费试用]
		$seller_check_time = C_READ('seller_check_time')*86400;
		$ids = model('order')->where(array('act_mod' => 'trial','status'=>1,'create_time' => array("LT", NOW_TIME - $seller_check_time)))->limit(10)->getfield('id',TRUE);
		$cause = '资格审核超时，系统自动关闭';
		if(is_array($ids)){
			foreach ($ids as $id) {
				$factory = new \Product\Factory\order($id);
				$factory->set_status(0,$cause);
			}
		}
		// 未填写订单号,过期自动关闭订单 [免费试用]
		$buyer_write_order_time = C_READ('buyer_write_order_time')*3600;
		$ids = model('order')->where(array('order_sn'=>'','act_mod' => 'trial','status'=>2,'check_time' => array("LT", NOW_TIME - $buyer_write_order_time)))->limit(10)->getfield('id',TRUE);
		$cause = '填写订单号超时，系统自动关闭';
		if(is_array($ids)){
			foreach ($ids as $id) {
				$factory = new \Product\Factory\order($id);
				$factory->set_status(0,$cause);   
			}
		}


		/*用户获得试用资格之后，未下单提醒,写入数据库*/
/*		$trial_pass_order = model('order')->where(array('act_mod'=>'trial','status'=>2,'order_sn'=>'','two_notice'=> 0))->select();
		foreach ($trial_pass_order as $key => $value) {
			$_pass_time = $value['check_time'] + C_READ('buyer_write_order_time')*3600 - C('two_notice.time')*60;

			if (NOW_TIME >= $_pass_time  && $_pass_time > 0) {
					$queue = A('Queue/Index') ;
				    $result_pass_order = $queue->queue($value['id'],1);
					model('order')->where(array('id'=>$value['id']))->save(array('two_notice'=>1));
			}
				
			
		}*/
		
		// 填写试用报告倒计时,过期自动关闭订单 [免费试用]
		$buyer_write_talk_time = C_READ('buyer_write_talk_time')*86400;
		$ids = model('order')->where(array('trial_report'=>'','act_mod' => 'trial','status'=>8,'check_time' => array("LT", NOW_TIME - $buyer_write_talk_time)))->limit(10)->getfield('id',TRUE);
		$cause = '填写试用报告超时，系统自动关闭';
		if(is_array($ids)){
			foreach ($ids as $id) {
				$factory = new \Product\Factory\order($id);
				$factory->set_status(0,$cause);
			}
		}

		/*未填写试用报告,写入数据库*/
/*		$trial_report_order = model('order')->where(array('act_mod'=>'trial','status'=>8,'two_notice'=> array('NEQ',2)))->select();
		foreach ($trial_report_order as $key => $value) {
			$_report_time = $value['check_time'] + C_READ('buyer_write_talk_time')*86400 - C('two_notice.time')*60;

			if (NOW_TIME >= $_report_time  && $_report_time > 0) {
					$queue = A('Queue/Index') ;
				    $result_report_order = $queue->queue($value['id'],2);
					model('order')->where(array('id'=>$value['id']))->save(array('two_notice'=>2));
			}
				
			
		}*/
		
		//  订单审核失败后会员未在时间内进行申诉：关闭该订单; [免费试用]
		$check_appeal = (int)C_READ('buyer_check_update_order_sn','trial')*3600;	//	失败后可以申诉的时间
		$ids = model('order')->where(array('act_mod' => 'trial','status'=>4,'check_time' => array("LT", NOW_TIME - $check_appeal)))->limit(10)->getfield('id',TRUE);
		$cause = '申诉时间超时，订单自动关闭';
		if(is_array($ids)){
			foreach ($ids as $id) {
				$factory = new \Product\Factory\order($id);
				$factory->set_status(0,$cause);
			}
		}
		
		// 商家未审核订单号,过期自动通过 [免费试用]
		if (C_READ('seller_order_check_time')) {
			$seller_order_check_time = C_READ('seller_order_check_time')*3600;
			$ids = model('order')->where(array('order_sn'=>array('NEQ',''),'act_mod' => 'trial','status'=>2,'check_time' => array("LT", NOW_TIME - $seller_order_check_time)))->limit(10)->getfield('id',TRUE);
			$cause = '商家审核订单号超时，系统自动通过审核';
			if(is_array($ids)){
				foreach ($ids as $id) {
					$factory = new \Product\Factory\order($id);
					$factory->set_status(8,$cause);
				}
			}
		}		
		// 待审核试用报告,(自动审核/平台审核)	[免费试用] 1429753320
		$seller_trialtalk_check = string2array(C_READ('seller_trialtalk_check'));
		if ($seller_trialtalk_check['chose'] == '1') {
			$ids = model('order')->where(array('act_mod' => 'trial','status'=>3,'check_time' => array("LT", NOW_TIME -  ($seller_trialtalk_check['value'] * 86400 ))))->limit(10)->getfield('id',TRUE);
			
			if(is_array($ids)){
				foreach ($ids as $id) {
					$factory = new \Product\Factory\order($id);
					$sql = "select order_id,userid,num,dateline,Count(*) from ".C('DB_PREFIX')."member_finance_log where order_id = ".$factory->order_info['id']." and num>0 group by order_id,userid,num having Count(*)>=1 order by id desc";
					$r = model()->query($sql);
					if(!$r){
				        $factory->pay($factory->product_info['goods_price'],'false',0,'true');
					}else{
					    return FALSE;
					}
				}
			}
		}
		// 时间内未下单自动关闭订单	[闪电试用]
		$write_order = C_READ('buyer_write_order_time','commission')* 60;	// 填写订单号时间
		$ids = model('order')->where(array('act_mod' => 'commission','status'=>2,'order_sn'=>array('EQ',''),'create_time' => array("LT", NOW_TIME - $write_order)))->limit(10)->getfield('id',TRUE);
		
		if(is_array($ids)){
			foreach ($ids as $id) {
				$factory = new \Product\Factory\order($id);
				$factory->set_status(0,'下单时间超时，系统自动关闭订单');
			}
		}

		// 商家未审核订单号,过期自动通过 [闪电试用]
		$check_order = C_READ('seller_check_time','commission')*3600;	// 商家审核时间
		$ids = model('order')->where(array('act_mod' => 'commission','status'=>3,'check_time' => array("LT", NOW_TIME - $check_order)))->limit(10)->getfield('id',TRUE);
		if(is_array($ids)){
			foreach ($ids as $id) {
				$factory = new \Product\Factory\order($id);
				$factory->set_status(5,'商家审核订单时间超时，系统自动通过');
				
			}
		}
		
		// 商家返款时间,过期自动返款 [闪电试用]
		$pay_order = C_READ('seller_pay_time','commission')*3600;	// 商家审核时间
		$ids = model('order')->where(array('act_mod' => 'commission','status'=>5,'check_time' => array("LT", NOW_TIME - $pay_order)))->limit(10)->getfield('id',TRUE);
			
			if(is_array($ids)){
				foreach ($ids as $id) {
					$factory = new \Product\Factory\order($id);
				    $sql = "select order_id,userid,num,dateline,Count(*) from ".C('DB_PREFIX')."member_finance_log where order_id = ".$factory->order_info['id']." and num>0 group by order_id,userid,num having Count(*)>=1 order by id desc";
				    $r = model()->query($sql);
				    if(!$r){
						$factory->pay();
				    }
					
				}
			}
		










	}

	/**
	 * 商家等级转换   根据商家购买的时间不同自动转为普通商家
	 * @param unknown_type $param
	 */
	public function member_login_success_change_membergroup(&$param){
		//时间到后自动转为普通商家 成为xx的时间
		$userid = $param['userid'];
		$status = $param['status'];
		
        $group_endtime = model('member')->getFieldByUserid($userid,'group_endtime');
        if(NOW_TIME >= $group_endtime){
            model('member')->where(array('userid'=>$userid))->save(array('groupid'=>1,'grouptime'=>0,'group_endtime'=>0));
        }

		return true;
	}
}