<?php
/**
 * 佣金返利驱动
 */
namespace Commission\Library\Driver;
class commission extends \Commission\Library\CommissionInterface {
	/**
	 * 获取活动设置
	 *  @return array
	 */
	public function getConfig(){
		$result =  model('activity_set')->where(array('activity_type' => 'commission'))->getField('key,value', TRUE);
		$result['commission_name'] = (string) $result['commission_name'];
		$result['single_mode'] = string2array($result['single_mode']);
		$result['seller_price'] = string2array($result['seller_price']);
		$result['seller_join_condition'] = string2array($result['seller_join_condition']);
		$result['seller_discount_range'] = string2array($result['seller_discount_range']);
		$result['seller_check_time'] = (int) $result['seller_check_time'];
		$result['seller_no_check_order'] = (int) $result['seller_no_check_order'];
		$result['seller_get_appeal'] = string2array($result['seller_get_appeal']);
		$result['seller_start_time'] = (string) $result['seller_start_time'];
		$result['seller_end_time'] = (string) $result['seller_end_time'];
		$result['seller_push_days'] = (int) $result['seller_push_days'];
		$result['seller_push_nums'] = (int) $result['seller_push_nums'];
		$result['buyer_artificial_check'] = (int) $result['buyer_artificial_check'];
		$result['buyer_join_condition'] = string2array($result['buyer_join_condition']);
		$result['buyer_good_buy_times'] = (int) $result['buyer_good_buy_times'];
		$result['buyer_day_buy_times'] = (int) $result['buyer_day_buy_times'];
		$result['buyer_buy_time_limit'] = (int) $result['buyer_buy_time_limit'];
		$result['buyer_write_order_time'] = (int) $result['buyer_write_order_time'];
		$result['buyer_update_order_type'] = (int) $result['buyer_update_order_type'];
		$result['buyer_check_update_order_sn'] = (int) $result['buyer_check_update_order_sn'];
		return $result;
	}
	/**
	 * 更新库存
	 * @param int       $num    库存数量
	 * @param string    $msg    操作理由
	 */
	public function quantity_update($num = 0, $msg = '') {
		$num = (int)$num;
		if(empty($num)) return FALSE;
		$model = model('commission_product')->where(array('id' => $this->product_info['id']));
		if($num > 0) {
			$result = $model->setInc('goods_number', $num);
		} else {
			$result = $model->setDec('goods_number', abs($num));
		}
		if($msg) $this->write_log ($this->product_info['status'], $msg);
		return TRUE;
	}
	/**
	 * 审核通过
	 * @param int       $start_time 商品上线时间
	 * @param string    $msg        操作理由
	 * @return boolean
	 */
	public function pass($start_time = 0, $msg = '商品审核成功') {
		$start_time = (!is_numeric($start_time)) ? strtotime($start_time) : (int) $start_time;
		if($start_time < NOW_TIME) {
			$this->error = '上线时间只能在当前时间之后';
			return FALSE;
		}
		$info = array();
		$info['id'] = $this->product_info['id']; 
		$info['status'] = -1;
		$info['start_time'] = $start_time;
		$info['end_time'] = $start_time + ($this->product_info['activity_days'] * 86400);
		$info['updatetime'] = NOW_TIME;
		$result = model('commission')->save($info);
		if(!$result) {
			$this->error = '产品处理失败，请稍后重试';
			return FALSE;
		}
		$this->write_log(-1, $msg);
		return TRUE;
	}
	
	/**
	 * 审核失败
	 * @param string $msg 操作理由
	 * @return boolean
	 */
	public function refuse($msg = '') {
		if($this->product_info['status'] != -2) {
			$this->error = '该商品非待审核状态';
			return FALSE;
		}
		$result = $this->set_status(0, '后台管理员审核失败');
		if(!$result) {
			$this->error = '处理失败，请稍后重试';
			return FALSE;
		}
		// 退还保证金给商家
		if($this->product_info['goods_deposit']) {
			action_finance_log($this->product_info['company_id'], $this->product_info['goods_deposit'], 'money', '活动审核失败，保证金退还', array('goods_id' => $this->product_info['id']));
		}
		return TRUE;
	}
	/* 平台确认付款 */
	public function admin_pay(){
	    if(!defined('IN_ADMIN')) {
	        $this->error = '您无权确认付款';
	        return FALSE;
	    }
	    $userid =  $this->product_info['company_id'];
	    //总保证金 = （下单价 +红包 + 平台佣金（自动算）） * 商品数量
	    $total = sprintf('%.2f',($this->product_info['goods_price'] + $this->product_info['goods_bonus'] + $this->product_info['goods_service']) * $this->product_info['goods_number']);
        //扣除商家的余额（活动保证金）
	    //用户当前余额
	    $money = model('member')->getFieldByUserid($userid,'money');
	    if ($money < $total){
	        $this->error = '商家账户余额不足，请充值！当前余额'.$money.'美元,<span style="color:red;">还需充值'.($total-$money).'美元</span>';
	        return FALSE;
	    }
	    $sign = '2-commission-'.$this->product_info['company_id'].'-'.$this->product_info['id'].'-3';
	    $rs = model('member_finance_log')->where(array('only'=>$sign))->find();
	    if(!$rs){
	        action_finance_log($this->product_info['company_id'],-$total,'money',"后台专员代扣除活动（".$this->product_info['title']."）保证金：".$total."。", $sign, array('goods_id'=>$this->product_info['id']));
	    }else{
	        $this->error = '平台确认付款失败,重复操作';
	        return FALSE;
	    }
	    $sqlMap = array();
	    $sqlMap['goods_deposit'] = $total;
	    $result = model('commission_product')->where(array('id'=>$this->product_info['id']))->save($sqlMap);
	    if (!$result) {
	        $this->error = '平台确认付款失败';
            $sign = '2-commission-'.$this->product_info['company_id'].'-'.$this->product_info['id'].'-3-1';
            action_finance_log($this->product_info['company_id'],$total,'money',"后台专员代扣除活动失败（".$this->product_info['title']."），退还保证金：".$total."。", $sign, array('goods_id'=>$this->product_info['id']));
	        return FALSE;
	    }
	    // 将保证金写入商家保证金字段
	    model('member_merchant')->where(array('userid'=>$userid))->setInc('frozen_deposit',$total);
	    return TRUE;
	}
	/**
	 * 用户抢购
	 * $talk : 对商家说点什么
	 * $bind_id : 选择购买的亚马逊帐号
	 */
	public function pay_submit($talk = '',$bind_id = 0) {
		// 检测用户权限
		$ischk = $this->pay_check();
		if($ischk === TRUE) { // 权限通过时
			$info = array();
			$info['buyer_id']  = $this->user_info['userid'];
			$info['seller_id'] = $this->product_info['company_id'];
			$info['goods_id']  = $this->product_info['id'];
			$info['act_mod']   = 'commission';
			$info['trade_sn']  = date('YmdHis').random(6,1);
			$info['inputtime'] = $info['create_time'] = NOW_TIME;
			$info['status'] = 1;
			$info['talk']      = trim($talk);
			$info['bind_id']   = $bind_id;
			$order_id = model('order')->update($info);
			if($order_id) {
				$Factory = new \Commission\Factory\order($order_id);
				$Factory->write_log('用户抢购资格');
				return $order_id;
			} else {
				$this->error = '用户抢购失败';
				return FALSE;
			}
		} else {
			return FALSE;
		}
	}
	
	/**
	 * 抢购检测
	 */
	public function pay_check() {
		$config = $this->getConfig();
		/*---- 限制买家参与 ----*/
		if(empty($this->user_info)) {
			$this->error = '尚未登录';
			return FALSE;
		}
		if($this->user_info['modelid'] != 1) {
			$this->error = '暂时只限买家参与';
			return FALSE;
		}
		/*---- 判断后台活动设置 ----*/
		/* 手机认证 */
		if($config['buyer_join_condition']['phone'] && !$this->user_info['phone_status']) {
			$this->error = '请先进行手机认证';
			return FALSE;
		}
		/* 邮箱认证 */
		if ($config['buyer_join_condition']['email'] && !$this->user_info['email_status']) {
			$this->error = '请先进行邮箱认证';
			return FALSE;
		}
		/* 实名认证 */
		$identity_count = model('member_attesta')->where(array('userid'=>$this->user_info['userid'],'type'=>'identity'))->count();
		if ($config['buyer_join_condition']['realname'] && $identity_count != 1) {
			$this->error = '请先进行实名认证';
			return FALSE;
		}
	
		/* 绑定亚马逊账号 */
		$tb_count = model('member_bind')->where(array('userid'=>$this->user_info['userid'],'status'=>array('NEQ',2)))->count();
		if ($config['buyer_join_condition']['bind_taobao'] && $tb_count < 1) {
			$this->error = '请先绑定亚马逊账号';
			return FALSE;
		}
	
		/* 是否绑定支付宝 */
		$account = model('member_attesta')->where(array('userid'=>$this->user_info['userid'],'type'=>'alipay'))->count();
		if ($config['buyer_join_condition']['bind_alipay'] && $account != 1) {
			$this->error = '请先绑定支付宝账号';
			return FALSE;
		}
	
		/*---- 检测商品状态 ----*/
		/* 上架状态 */
		if($this->product_info['status'] != 1) {
			$this->error = '该商品尚未上架';
			return FALSE;
		}
		if($this->product_info['start_time'] > NOW_TIME) {
			$this->error = '该活动尚未开始';
			return FALSE;
		}
		if($this->product_info['end_time'] < NOW_TIME) {
			$this->error = '该活动已经结束';
			return FALSE;
		}
		/* 检测商品库存 */
		if($this->product_info['goods_number'] - $this->product_info['already_num'] < 1) {
			$this->error = '该商品已售罄';
			return FALSE;
		}
		/*---- 检测活动设置 ----*/
		//1.是否有抢购未下单的
		$o_map = array();
		$o_map['buyer_id'] = $this->user_info['userid'];
		$o_map['goods_id'] = $this->product_info['id'];
		$o_map['status'] = array('NEQ',0);
		$wait_fill_num = model('order')->where($o_map)->count();
		/*佣金返利限定抢购次数*/
		$count = C_READ('buyer_good_buy_times','commission');
		if($wait_fill_num >= $count) {
			$this->error = '您已抢购了该订单'.$count.'次，请勿重复抢购。';
			return FALSE;
		}
		// 若设置为可以抢购多次，若有订单未完成的则不允许下单
		if ($count > 1) {
			$o_map['status'] = array('NOT IN',array('0','7'));
			$is_over = model('order')->where($o_map)->count();
			if ($is_over > 0) {
				$this->error = '当前还有未完成的订单，请订单完成后再继续下单';
				return FALSE;
			}
		}
		//2.每天参与总次数
		if($config['buyer_day_buy_times'] > 0) {
			$o_map = array();
			$o_map['buyer_id'] = $this->user_info['userid'];
			$o_map['_string'] = "DATE_FORMAT(FROM_UNIXTIME(create_time),'%Y%m%d') = DATE_FORMAT(NOW(),'%Y%m%d')";
			$buyer_day_buy_times = model('order')->where($o_map)->count();
			if($buyer_day_buy_times >= $config['buyer_day_buy_times']) {
				$this->error = '每天只能参与'.$buyer_day_buy_times.'次抢购哦~,超出每日活动次数限制';
				return false;
			}
		}
		//3.单品抢购时间间隔
		if($config['buyer_buy_time_limit'] > 0) {
			$o_map = array();
			$o_map['goods_id'] = $this->product_info['id'];
			$last_create_time = model('order')->where($o_map)->getField('create_time');
			if($last_create_time > 0 && $last_create_time < $config['buyer_buy_time_limit']) {
				$this->error = '同时间段内抢购人数过多';
				return FALSE;
			}
		}
		//4.单商品会员抢购次数
		if($config['buyer_good_buy_times'] > 0) {
			$o_map = array();
			$o_map['buyer_id'] = $this->user_info['userid'];
			$o_map['goods_id'] = $this->product_info['id'];
			$o_map['status'] = array('GT',1);
			$buyer_good_buy_times = model('order')->where($o_map)->count();
			if($buyer_good_buy_times >= $config['buyer_good_buy_times']) {
				$this->error = '您已参与该商品'.$config['buyer_good_buy_times'].'次抢购,请抢购其它商品吧';
				return FALSE;
			}
		}
		return TRUE;
	}
}