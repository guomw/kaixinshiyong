<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Order\Library;
class hook {
	public function system_init() {
		/*订单完成对邀请用户赠送相应的奖励*/
		//查看当前用户有多少成功的订单
		$userid = cookie('_userid');
		//查看当前用户是否为推荐用户
		$agent_ids = model('member')->where(array('agent_id' => $userid))->getField('userid',true);
		$invite = getcache('friend_setting','member');
		$setting = $invite['setting'];
		foreach ($agent_ids as $agent_id) {
			$agent_id = (int) $agent_id;
			$sqlMap = array();
			$sqlMap['status'] = 7;
			$sqlMap['buyer_id'] = $agent_id;
			//邀请用户完成的订单总数
			$count = model('order')->where($sqlMap)->count();
			if($count){
				if($agent_id > 0){
					$a = 0;
					$type = '';
					$num = 0;
					$cost = 0;
					foreach ($setting as $k=>$v) {
						if($a == 1){
							break;
						}
						//当前用户完成次数的最大满足要求
						if($count >= (int) $v['num']){
						    $cost = $v['cost'];
							$type = $v['type'];
						     $num = $v['num'];
						}else{
							$a = 1;
						}
					}
					//检测该奖励是否已经完成
					if($num > 0){
						//2015-5-8 12:00之后
						$sign = '3-6-'.$agent_id.'-'.$userid.'-'.$num;
						$is_insert = model('member_finance_log')->where(array('only'=>$sign))->find();
						if(!$is_insert){
							if (C('sso_is_open')== 1) {
								$info = array();
								$info['userid'] = $userid;
								$info['type'] = $type;
								$info['type_id'] = 1202;
								$info['num'] = $cost;
								$info['order_count'] = $num;
								$info['agent_id'] = $agent_id;
								$ret = _ps_send($type,$info);
								$data = php_data($ret);
								if ($data['status'] == 1) {
									action_finance_log($userid,$cost,$type,'您邀请的好友(会员id:)累积完成'.$num.'笔订单',$sign,array('recommend_status'=>'1'));
								}
							}else{
								    action_finance_log($userid,$cost,$type,'您邀请的好友(会员id:'.$agent_id.')累积完成'.$num.'笔订单',$sign,array('recommend_status'=>'1'));
							}
						}
					}
				}
			}

		}
        return FALSE;
	}
}