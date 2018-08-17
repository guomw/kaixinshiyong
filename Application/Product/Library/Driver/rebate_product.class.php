<?php
/**
 * 购物返利产品驱动
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Product\Library\Driver;
class rebate_product extends \Product\Library\ProductInterface { 
    /**
     * 获取活动设置
     * @return array
     */
    public function getConfig() {
        $result =  model('activity_set')->where(array('activity_type' => $this->product_info['mod']))->getField('key,value', TRUE);
        $result['rebate_name'] = (string) $result['REBATE_NAME'];
        $result['single_mode'] = string2array($result['single_mode']);
        $result['seller_join_condition'] = string2array($result['seller_join_condition']);
        $result['seller_discount_range'] = string2array($result['seller_discount_range']);
        $result['seller_check_time'] = (int) $result['seller_check_time'];
        $result['seller_no_check_order'] = (int) $result['seller_no_check_order'];
        $result['seller_get_appeal'] = string2array($result['seller_get_appeal']);
        $result['seller_start_time'] = (string) $result['seller_start_time'];
        $result['seller_end_time'] = (string) $result['seller_end_time'];
        $result['buyer_artificial_check'] = (int) $result['buyer_artificial_check'];
        $result['buyer_join_condition'] = string2array($result['buyer_join_condition']);
        $result['buyer_good_buy_times'] = (int) $result['buyer_good_buy_times'];
        $result['buyer_day_buy_times'] = (int) $result['buyer_day_buy_times'];
        $result['buyer_write_order_time'] = (int) $result['buyer_write_order_time'];
        $result['buyer_update_order_type'] = (int) $result['buyer_update_order_type'];
        $result['buyer_check_update_order_sn'] = (int) $result['buyer_check_update_order_sn'];
        return $result;
    }

    /* 抢购用户列表 */
    public function buyer_list($condition = array()) {
        $condition['goods_id'] = $this->product_info['id'];
        return model('order')->where($condition)->order("id DESC")->group('buyer_id')->getField('buyer_id', TRUE);
    }
    
    /**
     * 用户晒单列表
     * @param array $condition 查询条件
     * @return array
     */
    public function report_list($condition = array()) {
        $condition['goods_id'] = $this->product_info['id'];
        return model('report')->where($condition)->order("id DESC")->select();
    }
    
    /**
     * 用户抢购
     */
    public function pay_submit() {
        $ischk = $this->pay_check();
        if($ischk === TRUE) {
            $info = array();
            $info['buyer_id'] = $this->user_info['userid'];
            $info['seller_id'] = $this->product_info['company_id'];
            $info['goods_id'] = $this->product_info['id'];
            $info['act_mod'] = $this->product_info['mod'];
            $info['trade_sn'] = date('YmdHis').random(6,1);
            $info['inputtime'] = $info['create_time'] = NOW_TIME;
            $info['ip']   = get_client_ip();

            $info['status'] = 2;
            $order_id = model('order')->update($info);
            if($order_id) {
                $Factory = new \Product\Factory\order($order_id);
                $Factory->write_log('用户抢购资格');
                model('product')->where(array('id' => $this->product_info['id']))->setInc('already_num');
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
        /*---- 判断认证设置 ----*/
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
        // 统计实名认证
        $identity_count = model('member_attesta')->where(array('userid'=>$this->user_info['userid'],'type'=>'identity'))->count();
        /* 实名认证 */
        if ($config['buyer_join_condition']['realname'] && $identity_count != 1) {
            $this->error = '请先进行实名认证';
            return FALSE;
        }

        /* 绑定淘宝账号 */
        $tb_count = model('member_bind')->where(array('userid'=>$this->user_info['userid'],'status'=>array('NEQ',2)))->count();
        if ($config['buyer_join_condition']['bind_taobao'] && $tb_count < 1) {
            $this->error = '请先绑定淘宝账号';
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
        /*购物返利限定抢购次数*/
        $count = C_READ('buyer_good_buy_times','rebate');
        if($wait_fill_num >= $count) {
            $this->error = '您已抢购了该订单'.$count.'次，请勿重复抢购。';
            return FALSE;
        }
        // 若设置为可以抢购多次，若有订单未完成的则不允许下单
        if ($count > 1) {
            $o_map['status'] = array('BETWEEN',array('1','6'));
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
            	$this->error = '超出每日活动次数限制';
                return FALSE;
            }
        }
        //3.商品抢购时间间隔
        if($config['buyer_buy_time_limit'] > 0) {
            $o_map = array();
            $o_map['goods_id'] = $this->product_info['id'];
            $last_create_time = model('order')->where($o_map)->getField('create_time');
            if($last_create_time > 0 && $last_create_time < $config['buyer_buy_time_limit']) {
            	$this->error = '同时间段内抢购人数过多';
                return FALSE;
            }
        }
        //4.商品会员抢购次数
        //@todu:需求无法确认是只记录已成功的还是所有的抢购都计算
        if($config['buyer_good_buy_times'] > 0) {
            $o_map = array();
            $o_map['buyer_id'] = $this->user_info['userid'];
            $o_map['goods_id'] = $this->product_info['id'];
            $o_map['status'] = 7;
            $buyer_good_buy_times = model('order')->where($o_map)->count();
            if($buyer_good_buy_times >= $config['buyer_good_buy_times']) {
            	$this->error = '您参与该商品抢购次数过多';
                return FALSE;
            }
        }
        return TRUE;
    }
    
    /**
     * 更新库存
     * @param int       $num    库存数量
     * @param string    $msg    操作理由
     */
    public function quantity_update($num = 0, $msg = '') {
        $num = (int)$num;
        if(empty($num)) return FALSE;
        $model = model('product_'.$this->product_info['mod'])->where(array('id' => $this->product_info['id']));
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
        $result = model('product')->save($info);     
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
            $sign = '2-'.$this->product_info['mod'].'-'.$this->product_info['company_id'].'-'.$this->product_info['id'].'-6';
            $rs = model('member_finance_log')->where(array('only'=>$sign))->find();
            if(!$rs){
                action_finance_log($this->product_info['company_id'], $this->product_info['goods_deposit'], 'money', '活动审核失败，保证金退还', $sign,array('goods_id' => $this->product_info['id']));
                model('member_merchant')->where(array('userid'=>$this->product_info['company_id']))->setDec('frozen_deposit', $this->product_info['goods_deposit']);

                


            }else{
                return FALSE;
            }
        }
        return TRUE;
    }
    
    /**
     * 抢购用户列表
     * @param array $sqlmap 查询条件
     * @param int   $limit  查询数量
     * @param int   $page   当前页码
     */
    public function get_buyer_uids($sqlmap = array(), $limit = 20, $page = 1) {
        $sqlmap['goods_id'] = $this->product_info['id'];
        return model('order')->where($sqlmap)->page($page, $limit)->getField('buyer_id', TRUE);
    }

    /**
     * 活动商品结算计算
     * @param array $sqlmap 查询条件
     */
    public function get_over_info() {
        if($this->product_info['status'] != 2) {
            $this->error = '当前产品不是待结算状态';
            return false;
        }
        //---- 获取订单数据 ------
        $sqlmap = array();
        $sqlmap['status'] = array(array("GT", 0), array("LT", 7));
        $sqlmap['goods_id'] = $this->product_info['id'];
        if(model('order')->where($sqlmap)->count()) {
            $this->error = '当前商品尚有进行中的订单';
            return false;

        }

         $sqlmap1 = array();
         $sqlmap1['status'] = 2;
         $sqlmap1['product_id'] = $this->product_info['id'];
        //查询是否还有已付款未审核的追加活动
        if(model('product_complement')->where($sqlmap1)->count()){
            $this->error = '您还有追加的活动等待审核！ 审核通过之后才能结算！';
            return false;
        }
       

        //目前已存在数据库当中的活动总保证金
        $new_goods_deposit = $this->product_info['goods_deposit'];
        
        //获取财务日志表的当中的活动总保证金
        $conditions = array();
        $conditions['goods_id'] = $this->product_info['id'];
        $conditions['userid'] = $this->product_info['company_id'];
        $conditions['order_id'] = 0;
        $conditions['num'] = array('LT',0);
        $conditions['type'] = 'money';
        $total = model('member_finance_log')->where($conditions)->SUM('num');
        $total = explode('-', $total);
        $total = $total[1];

        // 核对金额是否一致 
        if($total != $new_goods_deposit || $total < 0  ){
          $this->error = '亲，活动保证金缴纳核对不准确,请联系平台客服手动结算，错误码 -102';
          return false;

        }

        //判断保证金缴纳方式

        //获得已完成的订单数量
        $state_order_mum = get_over_trial_by_gid($this->product_info['id']);

        //数据库统计已完成订单数量
        $state_order_goods_mum = $this->product_info['already_num'];


        // 比对订单数量 如果不一致返回错误
         
         if( $state_order_mum != $state_order_goods_mum ){
          
          $this->error = '亲，活动订单核对不正确,请联系平台客服手动结算 错误码 -101';
          return false;

         }


        //---- 取出结算数据
        $result = $this->product_info;
        // 平台损耗费总额
        $result['goods_service_total'] = sprintf('%.2f', $this->product_info['goods_price'] * $this->product_info['already_num'] * $this->product_info['goods_service'] / 100);
        // 已返还会员总额
        $result['goods_member_total'] = sprintf('%.2f', ($this->product_info['goods_price'] - $this->product_info['goods_price'] * $this->product_info['goods_discount'] / 10) * $this->product_info['already_num']);
        // 应返还商家总额  冻结中保证金 
        $result['goods_company_return'] = sprintf('%.2f', $this->product_info['goods_deposit'] - ($result['goods_service_total'] + $result['goods_member_total']));


        //----------- 第三重防御 总保证金-已返还会员-平台推广费 是否返还商家 进行核对确认--------------//
        $yz  = round($this->product_info['goods_deposit'] - $result['goods_member_total'] - $result['goods_service_total'],2);

        if($yz != $result['goods_company_return'] ){
           
          $this->error = '活动结算失败，请联系平台客服处理，错误码 -100 ';
           return false;
        }

        return $result;
    }
 
    /* 执行活动商品结算 */
    public function action_over_info() {
        $over_info = $this->get_over_info();
        if(!$over_info) return FALSE;
        $sign = '2-'.$this->product_info['mod'].'-'.$this->product_info['company_id'].'-'.$this->product_info['id'].'-2';
        $rs = model('member_finance_log')->where(array('only'=>$sign))->find();
        if(!$rs){
            action_finance_log($this->product_info['company_id'], $over_info['goods_company_return'], 'money', '活动结束，保证金结算并退还。',$sign, array('goods_id' => $this->product_info['id']));
            //扣除商家冻结中的保证金
            model('member_merchant')->where(array('userid' => $this->product_info['company_id']))->setDec('frozen_deposit',$over_info['goods_company_return']);
            $this->set_status(3, '活动结算完成');
            return TRUE;
        }else{
            $this->error = '活动结算失败，重复操作';
            return FALSE;
        }
    }
    /* 平台确认付款 */
    public function admin_pay(){
        if(!defined('IN_ADMIN')) {
            $this->error = '您无权确认付款';
            return FALSE;
        }
    	$mod = $this->product_info['mod'];
    	$userid =  $this->product_info['company_id'];
    	//查出该商家的groupid
    	$groupid = model('member')->getFieldByUserid($userid,'groupid');
    	$groups = getcache('merchant_group','member');
    	$config = unserialize($groups[$groupid]['config']);
    	$service = $config[$mod]['service'];//平台服务费率
    	//单笔服务费  = 下单价 * 平台服务费率 / 100；
    	//总保证金 = (下单价 + 单笔服务费) * 商品数量

        if($this->product_info['service_type'] == 1){
          $total = sprintf('%.2f',($this->product_info['goods_price'] + ($this->product_info['goods_price'] * $service /100)) * $this->product_info['goods_number']);
        }
        // 如果为部分缴纳方式
        if($this->product_info['service_type'] == 2){
            $total = sprintf("%.2f",(($this->product_info['goods_price']-($this->product_info['goods_price'] / 10 * $this->product_info['goods_discount'] ) )+ ($this->product_info['goods_price'] * $this->product_info['goods_service'] / 100)) * $this->product_info['goods_number']);
        } 
    	//扣除商家的余额（活动保证金）
    	//用户当前余额
		$money = model('member')->getFieldByUserid($userid,'money');
		if ($money < $total) {
		    $this->error = '商家账户余额不足，请充值！当前余额'.$money.'美元,<span style="color:red;">还需充值'.($total-$money).'美元</span>';
		    return FALSE;
		}
		$sign = '2-'.$this->product_info['mod'].'-'.$this->product_info['company_id'].'-'.$this->product_info['id'].'-3';
		$rs = model('member_finance_log')->where(array('only'=>$sign))->find();
		if(!$rs){
		    action_finance_log($this->product_info['company_id'],-$total,'money',"后台专员代扣除活动（".$this->product_info['title']."）保证金：".$total."。",$sign,array('goods_id'=>$this->product_info['id']));
		}else{
		    $this->error = '平台确认付款失败，重复操作';
		    return FALSE;
		}
	    $sqlMap = array();
	    $sqlMap['goods_service'] = $service;//平台服务费
	    $sqlMap['goods_deposit'] = $total;//添加商品的保证金
	    $result = model('product_'.$mod)->where(array('id'=>$this->product_info['id']))->save($sqlMap);
	    if ($result === false) {
	        $this->error = '平台确认付款失败';
	        return FALSE;
	    }
	    //将保证金写入商家保证金字段
	    model('member_merchant')->where(array('userid'=>$userid))->setInc('frozen_deposit',$total);
	    return TRUE;
    }
}
