<?php
/**
 * 购物返利产品驱动
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Product\Library\Driver;
class commission_product extends \Product\Library\ProductInterface {
    /**
     * 获取活动设置
     * @return array
     */
    public function getConfig() {
        $result =  model('activity_set')->where(array('activity_type' => $this->product_info['mod']))->getField('key,value', TRUE);
        $result['commisson_name'] = (string) $result['commisson_name'];
        $result['commission_type'] = string2array($result['commission_type']);
        $result['seller_join_condition'] = string2array($result['seller_join_condition']);
        $result['seller_check_time'] = (int) $result['seller_check_time'];
        $result['seller_pay_time'] = (int) $result['seller_pay_time'];
        $result['buyer_join_condition'] = string2array($result['buyer_join_condition']);
        $result['buyer_write_order_time'] = (int) $result['buyer_write_order_time'];
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
     * $talk : 对商家说点什么
     * $bind_id : 选择购买的淘宝帐号
     */
    public function pay_submit($bind_id = 0) {
        $ischk = $this->pay_check();
        if($ischk === TRUE) {
            $info = array();
            $info['buyer_id'] = $this->user_info['userid'];
            $info['seller_id'] = $this->product_info['company_id'];
            $info['goods_id'] = $this->product_info['id'];
            $info['act_mod'] = $this->product_info['mod'];
            $info['trade_sn'] = date('YmdHis').random(6,1);
            $info['inputtime'] = $info['create_time'] = NOW_TIME;
            $info['status'] = 2;
            $info['ip']   = get_client_ip();
            $info['bind_id']   = $bind_id;

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

        if ($this->product_info['allow_groupid'] != '') {
             $allow_groupid = string2array($this->product_info['allow_groupid']);
             $member_name = model('member_group')->where(array('groupid'=>$this->user_info['groupid']))->getField('name');
             if (!in_array($this->user_info['groupid'], $allow_groupid)) {
                 $this->error = '您目前是'.$member_name.'没有权限参与该活动';
                 return FALSE;
             }

        }

   


        $order_count = model('order')->where(array('buyer_id'=>$this->user_info['userid'],'goods_id'=>$this->product_info['id'],'act_mod'=>'commission'))->count();
       
        if ($order_count > 0) {
           $this->error = '您已参与过该活动';
            return FALSE;
        }

        //获得用户已完成订单数量
        /*$tiral_num= model('order')->where(array('buyer_id'=>$this->user_info['userid'],'act_mod'=>'trial','status'=>7))->count();
       
         if ((int)$trial_num - (int)$config['buyer_join_condition']['num_trial_art'] < 0) {
              $this->error = '您还要完成'.$config['buyer_join_condition']['num_trial_art']-$trial_num.'笔订单才能参与该活动';
            return FALSE;
         }*/

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
                action_finance_log($this->product_info['company_id'], $this->product_info['goods_deposit'], 'money', '活动审核失败，保证金退还',$sign, array('goods_id' => $this->product_info['id']));
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
        //$sqlmap['status'] = array(array('GT',0),array('LT',7));
        $sqlmap['status'] = array('IN','1,2,3,4,5,6,8');
        $sqlmap['goods_id'] = $this->product_info['id'];
        if (model('order')->where($sqlmap)->count()) {
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

         //-----第一重防御--获取财务日志当中商家已缴纳的活动费用 进行核对确认--------//
         
            //目前已存在数据库当中的活动总保证金
              
              $new_goods_deposit = $this->product_info['goods_deposit'];
           
                //获取财务日志表的当中的活动总保证金
                $conditions = array();
                $conditions['goods_id'] = $this->product_info['id'];
                $conditions['userid'] = $this->product_info['company_id'];
                $conditions['order_id'] = 0;
                $conditions['num'] = array('LT',0);
                $total = model('member_finance_log')->where($conditions)->SUM('num');
                $total = explode('-', $total);
                $total = $total[1];


            // 核对金额是否一致 
            
            if($total != $new_goods_deposit || $total < 0  ){
              $this->error = '亲，活动保证金缴纳核对不准确,请联系平台客服手动结算，错误码 -102';
              return false;

            }

        //----------- 第二重防御 获取数据库当中已完成订单数量 进行核对确认--------------//
        
          //数据库统计已完成订单数量
          $state_order_mum = get_over_trial_by_gid($this->product_info['id']);
    
          // 查询商品表已记录的字段
           
          $state_order_goods_mum = $this->product_info['already_num'];


          // 比对订单数量 如果不一致返回错误
           
           if( $state_order_mum != $state_order_goods_mum ){
            
            $this->error = '亲，活动订单核对不正确,请联系平台客服手动结算 错误码 -101';
            return false;

           }


         // 计算公式   （下单价格 +  平台推广费  + 红包 ）* (商品份数)    = 总保证金

        $result['total'] = ($this->product_info['goods_price'] + $this->product_info['goods_service'] + $this->product_info['bonus_price']) * $this->product_info['goods_number'];
        
        // 已返还总额
        $result['goods_member_total'] = sprintf('%.2f',($this->product_info['goods_price'] + $this->product_info['bonus_price']) * $state_order_mum);
     
        $goods_member_total = $result['goods_member_total'];
           

        $result = $this->product_info;


        // 平台总推广费  单份服务费 * 已完成订单数量 
        
        $result['goods_service_total'] = sprintf('%.2f', $this->product_info['goods_service'] * $state_order_mum);
        $result['goods_company_return'] = sprintf('%.2f',$this->product_info['goods_deposit'] - (($this->product_info['goods_price'] + $this->product_info['goods_service'] + $this->product_info['bonus_price']) * $state_order_mum));
        

        //----------- 第三重防御 总保证金-已返还会员-平台推广费 是否返还商家 进行核对确认--------------//
        
        $yz =sprintf('%.2f',$this->product_info['goods_deposit']-$goods_member_total-$result['goods_service_total']);
        if( $yz != $result['goods_company_return']){
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
            action_finance_log($this->product_info['company_id'], $over_info['goods_company_return'], 'money', '（商品id:'.$this->product_info['id'].'）活动结束，保证金结算并退还。',$sign, array('goods_id' => $this->product_info['id']));
            //扣除商家冻结中的保证金
            model('member_merchant')->where(array('userid' => $this->product_info['company_id']))->setDec('frozen_deposit',$over_info['goods_company_return']);
            $this->set_status(3, '活动结算完成');
            return TRUE;
        }else{
            $this->error = '活动结算失败，重复操作,错误代码 -104 ';
            return FALSE;
        }
    }

    /**
     * 闪电平台确认付款 
     */
   public function admin_pay(){
        if(!defined('IN_ADMIN')) {
            $this->error = '您无权确认付款';
            return FALSE;
        }
        $mod = $this->product_info['mod'];
        $userid =  $this->product_info['company_id'];
        //总保证金 = （下单价 +红包 + 平台佣金（自动算）） * 商品数量
        $total = sprintf('%.2f',($this->product_info['goods_price'] + $this->product_info['bonus_price'] + $this->product_info['goods_service']) * $this->product_info['goods_number']);
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
        $result = model('product_'.$mod)->where(array('id'=>$this->product_info['id']))->save($sqlMap);
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
}
