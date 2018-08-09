<?php
/**
 * 购物返利产品驱动
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Product\Library\Driver;
class trial_product extends \Product\Library\ProductInterface {
    /**
     * 获取活动设置
     * @return array
     */
    public function getConfig() {
        $result =  model('activity_set')->where(array('activity_type' => $this->product_info['mod']))->getField('key,value', TRUE);
        $result['trial_name'] = (string) $result['trial_name'];
        $result['single_mode'] = string2array($result['single_mode']);
        $result['seller_give_back'] = (int) $result['seller_give_back'];
        $result['seller_ordersn_check'] = (int) $result['seller_ordersn_check'];
        $result['seller_check_time'] = (int) $result['seller_check_time'];
        $result['seller_charge_money'] = (int) $result['seller_charge_money'];
        $result['seller_trialtalk_check'] = string2array($result['seller_trialtalk_check']);
        $result['buyer_join_condition'] = string2array($result['buyer_join_condition']);
        $result['buyer_write_order_time'] = (int) $result['buyer_write_order_time'];
        $result['buyer_good_buy_times'] = (int) $result['buyer_good_buy_times'];
        $result['buyer_day_buy_times'] = (int) $result['buyer_day_buy_times'];
        $result['buyer_write_talk_time'] = (int) $result['buyer_write_talk_time'];
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
    public function pay_submit($talk = '',$bind_id = 0,$data_type=0) {
        // 检测用户权限
        $ischk = $this->pay_check();
        if($ischk === TRUE) { // 权限通过时
            $info = array();
            $info['buyer_id']  = $this->user_info['userid'];
            $info['seller_id'] = $this->product_info['company_id'];
            $info['goods_id']  = $this->product_info['id'];
            $info['act_mod']   = $this->product_info['mod'];
            $info['ip']   = get_client_ip();
            $info['trade_sn']  = date('YmdHis').random(6,1);
            $info['inputtime'] = $info['create_time'] = NOW_TIME;
           $groupid = model('member')->where(array('userid'=>$this->user_info['userid']))->getField('groupid');
         
            $info['status'] = 1;
            if($data_type ==1){
                if ((int)$groupid != 2) {
                     $this->error = '当前用户不是vip会员，请先申请vip会员';
                     return FALSE;
                }
                if($this->product_info['goods_vipfree'] == 1){ //这个商品没有VIP免审
                    $groups = getcache('member_group','member');
                    $level = $groups[2];

                    $day_count = $level['day_count'];
                    $month_count = $level['month_count'];
                    if(!empty($day_count) || !empty($month_count)){ //没有缓存
                        //查询今日这个用户试用次数
                        $sql_where = array();
                        $sql_where['is_vip_shi'] = 1;
                        $sql_where['buyer_id'] = $this->user_info['userid'];
                        $time = strtotime(date("Y-m-d"));
                        $sql_where['create_time'] = array('gt',$time);
                        $day_count_mysql = model('order')->where($sql_where)->count();
                        //查询这月这个用户试用次数
                        $sql_where2 = array();
                        $sql_where2['is_vip_shi'] = 1;
                        $time2 = strtotime(date("Y-m"));
                        $sql_where2['buyer_id'] = $this->user_info['userid'];
                        $sql_where2['create_time'] = array('gt',$time2);
                        $month_count_mysql = model('order')->where($sql_where2)->count();
                        if($day_count>$day_count_mysql && $month_count>$month_count_mysql){ //每日 和 每月比较
                            $info['status'] = 2;
                            $info['is_vip_shi'] = 1;
                            $info['check_time'] = NOW_TIME;
                        }
                    }
                }
            }

             $point = model('member')->where(array('userid'=>$this->user_info['userid']))->getField('point');
             $set_point = Integral_quantity($this->product_info['goods_price']);


            if ($data_type ==2) {
                if($this->product_info['goods_point'] > 0  && (int)$point >= $set_point){ 
                    $info['status'] = 2;
                    $info['is_vip_shi'] = 2;
                    $info['check_time'] = NOW_TIME;
                    
                }else{
                    $this->error = '当前积分不足';
                     return FALSE;
                }
            }

            if($this->product_info['goods_number'] - $this->product_info['already_num'] < 1){
            
                 $this->error = '没有库存了';
                 return FALSE;
               
            }

            $info['talk']      = trim($talk);
            $info['bind_id']   = $bind_id;
            $order_id = model('order')->update($info);
          
     

            if($order_id) {
                if($data_type == 2 && $this->product_info['goods_point'] > 0  && (int)$point >= $set_point){
               // $now_p = $point - $set_point;
            //    model('member')->where(array('userid'=>$this->user_info['userid']))->save(array('point'=>$now_p));
                //9-userid-goods_id-当前时间-1
                $sign = '9-'.$userid.$goods_id.NOW_TIME.'-1';
                action_finance_log($this->user_info['userid'], -$set_point,'point','获取免审试用资格,扣除'.$set_point.'积分',$sign,array('goods_id'=>$this->product_info['id'] ),TRUE) ;
                }
                $Factory = new \Product\Factory\order($order_id);
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
        $count = C_READ('buyer_good_buy_times','trial');
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

        /*防止重复抢购参与*/
        if ($this->product_info['goods_tips']['goods_order']['is_join'] ==1) {
            $o_map = array();
            $o_map['buyer_id'] = $this->user_info['userid'];
            $o_map['seller_id'] = $this->product_info['company_id'];
            $o_map['status'] = array('GT',0);
            $buyer_continuity_apply = model('order')->where($o_map)->count();
            if($buyer_continuity_apply > 0) {
                $this->error = '您正在参与该店铺其它活动商品,不能同时参与本次活动呃';
                return FALSE;
            }
        }
      
     /*重复ip检测*/
     $is_ip = $this->product_info['goods_tips']['goods_order']['is_ip'];
     if ($is_ip == 1) {
            $ip_order = model('order')->where(array('seller_id'=>$this->product_info['company_id'],'goods_id'=>$this->product_info['id'],'ip'=>get_client_ip(),'status'=>array('GT',0)))->count();
                if($ip_order > 0) {
                    $this->error = '当前ip已经申请过本次活动,请勿重复申请';
                    return FALSE;
                } 
         
     }
    
        $black_count = model('member_blacklist')->where(array('seller_id'=>$this->product_info['company_id'],'buyer_id'=>$this->user_info['userid']))->count();
        if ($black_count >0) {
            $this->error = '您已被该商家移入黑名单,请抢购其它商家的商品吧';
                return FALSE;
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


                 if (C('sso_is_open') == 1) {
                  unset($infos);
                  $infos = array();
                  $infos['userid'] = $this->product_info['company_id'];
                  $infos['type'] = 'money';
                  $infos['type_id'] = '1703';
                  $infos['num'] = $this->product_info['goods_deposit'];
                  $infos['goods_id'] = $this->product_info['id'];
                  $ret = _ps_send('money',$infos);
                  $data = php_data($ret);
                     if ($data['status'] == 1) {
                    action_finance_log($this->product_info['company_id'], $this->product_info['goods_deposit'], 'money', '活动审核失败，保证金退还',$sign, array('goods_id' => $this->product_info['id']));
                    model('member_merchant')->where(array('userid'=>$this->product_info['company_id']))->setDec('frozen_deposit', $this->product_info['goods_deposit']);                  
                       
                        }

                }else{

                     action_finance_log($this->product_info['company_id'], $this->product_info['goods_deposit'], 'money', '活动审核失败，保证金退还',$sign, array('goods_id' => $this->product_info['id']));
                      model('member_merchant')->where(array('userid'=>$this->product_info['company_id']))->setDec('frozen_deposit', $this->product_info['goods_deposit']);
                }


/*
                action_finance_log($this->product_info['company_id'], $this->product_info['goods_deposit'], 'money', '活动审核失败，保证金退还',$sign, array('goods_id' => $this->product_info['id']));
                model('member_merchant')->where(array('userid'=>$this->product_info['company_id']))->setDec('frozen_deposit', $this->product_info['goods_deposit']);*/
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
        $sqlmap['status'] = array('IN','2,3,4,5,6,8');
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
        
         //2015-11-08 优化 去除活动结算时候的待审核试用资格。在活动结算的时候全部设为失败
         //-----第一重防御--获取财务日志当中商家已缴纳的活动费用 进行核对确认--------//
         
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

         $result = $this->product_info;
         
        // 读取后台活动管理设置 [剩余推广费退还 0:不退还;1:退还]
        $seller_give_back = C_READ('seller_give_back');
            // 推广方式         $this->product_info['goods_charge_way'];
            // 商品推广费用     $this->product_info['goods_service'];        
        //---- 应返还商家总额 -------


         // 计算公式   （下单价格 +  平台推广费  + 红包 ）* (商品份数)    = 总保证金

        $result['total'] = ($this->product_info['goods_price'] + $this->product_info['goods_service'] + $this->product_info['goods_bonus']) * $this->product_info['goods_number'];
        
        // 已返还总额
        $result['goods_member_total'] = sprintf('%.2f',($this->product_info['goods_price'] + $this->product_info['goods_bonus']) * $state_order_mum);
     
        $goods_member_total = $result['goods_member_total'];
           

        // 平台总推广费  单份服务费 * 已完成订单数量
        
        $result['goods_service_total'] = sprintf('%.2f', $this->product_info['goods_service'] * $state_order_mum);


        if ($seller_give_back == 1 && $this->product_info['goods_charge_way'] == 0) {   //退还剩余推广费 0单份 1单场
            //  按单份 [应退还费用 = 总保证金 - (下单价 + 单笔推广费用 + 红包费) * 已售份数]
            
            // 平台总服务费
            if ($this->product_info['goods_charge_way'] == 1){  // 是单场收费的时候
                $pt_fwf = sprintf('%.2f',$this->product_info['goods_service']);
            }else{
                $pt_fwf = sprintf('%.2f',$this->product_info['goods_service'] * $this->product_info['goods_number']);
            }
            $result['goods_company_return'] = sprintf('%.2f',$this->product_info['goods_deposit'] - (($this->product_info['goods_price'] + $this->product_info['goods_service'] + $this->product_info['goods_bonus']) * $state_order_mum));
        }else{  //  不退还剩余推广费 [应退还费用 = (总份数 - 已售份数) * (下单价+红包费)]
            $result['goods_company_return'] = sprintf('%.2f',($this->product_info['goods_number'] - $state_order_mum) * ($this->product_info['goods_price']+$this->product_info['goods_bonus']));
        }

        //----------- 第三重防御 总保证金-已返还会员-平台推广费 是否返还商家 进行核对确认--------------//
        $yz  = round($this->product_info['goods_deposit']-$goods_member_total-$result['goods_service_total'],2);

        if($yz != $result['goods_company_return'] ){
           
         
          $this->error = '活动结算失败，请联系平台客服处理，错误码 -100 ';
           return false;
        }



           //------------2015-09-18升级三重防御加固-----------//


        return $result;   
    }

    /* 执行活动商品结算 */
    public function action_over_info() {
        $over_info = $this->get_over_info();
        if(!$over_info) return FALSE;
        $sign = '2-'.$this->product_info['mod'].'-'.$this->product_info['company_id'].'-'.$this->product_info['id'].'-2';
        $rs = model('member_finance_log')->where(array('only'=>$sign))->find();

          //活动结算后 批量将未审核试用资格的全部设为失败
          $map['goods_id'] = $this->product_info['id'];
          $map['status'] = 1;
          $map['act_mod'] = 'trial';
          model('order')->where($map)->setField('status',0);

        if(!$rs){

            if (C('sso_is_open') == 1) {
                  unset($infos);
                  $infos = array();
                  $infos['userid'] = $this->product_info['company_id'];
                  $infos['type'] = 'money';
                  $infos['type_id'] = '1702';
                  $infos['num'] = $over_info['goods_company_return'];
                  $infos['goods_id'] = $this->product_info['id'];
                  $ret = _ps_send('money',$infos);
                  $data = php_data($ret);
                 if ($data['status'] == 1) {
                    action_finance_log($this->product_info['company_id'], $over_info['goods_company_return'], 'money', '（商品id:'.$this->product_info['id'].'）活动结束，保证金结算并退还。',$sign, array('goods_id' => $this->product_info['id']));  

                    //扣除商家冻结中的保证金
                    model('member_merchant')->where(array('userid' => $this->product_info['company_id']))->setDec('frozen_deposit',$over_info['goods_company_return']);
                    $this->set_status(3, '活动结算完成');
                    return TRUE;                     
                       
                    }

                }else{
                 action_finance_log($this->product_info['company_id'], $over_info['goods_company_return'], 'money', '（商品id:'.$this->product_info['id'].'）活动结束，保证金结算并退还。',$sign, array('goods_id' => $this->product_info['id']));     

                 //扣除商家冻结中的保证金
                model('member_merchant')->where(array('userid' => $this->product_info['company_id']))->setDec('frozen_deposit',$over_info['goods_company_return']);
                $this->set_status(3, '活动结算完成');
                return TRUE;    
            }
           
            
        }else{
            $this->error = '活动结算失败，重复操作,错误代码 -104 ';
            return FALSE;
        }
    }

    /**
     * 平台确认付款 
     */
    public function admin_pay(){
        if (!defined('IN_ADMIN')) {
            $this->error = '您无权确认付款';
            return FALSE;
        }
    	$mod = $this->product_info['mod'];
    	$userid =  $this->product_info['company_id'];
    	//查出该商家的groupid  
    	$groupid = model('member')->getFieldByUserid($userid,'groupid');
    	$groups = getcache('merchant_group','member');
    	$config = unserialize($groups[$groupid]['config']);
    	//查出当前是按份收取或者按场收取

        if ($this->product_info['trial_type'] == 3) {
             $service = $this->red_reward($this->product_info['goods_price'],$this->product_info['company_id']);
        }elseif ($this->product_info['trial_type'] == 2) {
             $service = $this->ab_reward($this->product_info['goods_price'],$this->product_info['company_id']);

        }else{
             $seller_charge_money = C_READ('seller_charge_money','trial');
             $service = ($seller_charge_money == 0) ? $config[$mod]['cost']['product_cost'] : $config[$mod]['cost']['activity_cost'];
        }

      
    	// 单份 : 总金额 = (单价 +收费价格+红包)*数量    单场 ：总金额 = (单价+红包钱) * 数量 + 收费价格
        if($seller_charge_money == 0){//单份
    		$total = sprintf('%.2f',($this->product_info['goods_price'] + $service + $this->product_info['goods_bonus']) * $this->product_info['goods_number']);
    	}else{
    		$total = sprintf('%.2f',($this->product_info['goods_price']+$this->product_info['goods_bonus']) * $this->product_info['goods_number'] + $service);
    	}

        
        // 剩余推广费是否退还 [后台->活动设置 0:不退还；1:退还]
        $seller_give_back = C_READ('seller_give_back','trial');
        // 收费方式 $cost [后台->活动设置 0:按单份；1:按单场]  不退还推广费则不存推广费到商家冻结保证金中
        if ($seller_give_back == 0) {
            if ($seller_charge_money == 1){
                $total = $total - $service;
                action_finance_log($this->product_info['company_id'],-$service,'deposit',"扣除活动id:".$this->product_info['id']."推广服务费(不退还)",array('goods_id'=>$this->product_info['id']));
            }else{
                $total = $total - ($service * $this->product_info['goods_number']);
                action_finance_log($this->product_info['company_id'],-($service * $this->product_info['goods_number']),'deposit',"扣除活动id:".$this->product_info['id']."推广服务费(不退还)",array('goods_id'=>$this->product_info['id']));
            }
        }

        //扣除商家的余额（活动保证金）
        //用户当前余额
        $money = model('member')->getFieldByUserid($userid,'money');
        if ($money < $total){
            $this->error = '商家账户余额不足，请充值！当前余额'.$money.'元,<span style="color:red;">还需充值'.($total-$money).'元</span>';
            return FALSE;
        }
        $sign = '2-'.$this->product_info['mod'].'-'.$this->product_info['company_id'].'-'.$this->product_info['id'].'-3';
        $rs = model('member_finace_log')->where(array('only'=>$sign))->find();
        if(!$rs){

            if (C('sso_is_open') == 1) {
                unset($infos);
                $infos = array();
                $infos['userid'] = $this->product_info['company_id'];
                $infos['type'] = 'money';
                $infos['type_id'] = '1705';
                $infos['num'] = $total;
                $infos['goods_id'] = $this->product_info['id'];
                $ret = _ps_send('money',$infos);
                $data = php_data($ret);
                if ($data['status'] == 1) {
                 action_finance_log($this->product_info['company_id'],-$total,'money',"后台专员代扣除活动（".$this->product_info['title']."）保证金：".$total."。",$sign, array('goods_id'=>$this->product_info['id']));

                }else{
                    $this->error='操作失败';
                }
            }else{
               action_finance_log($this->product_info['company_id'],-$total,'money',"后台专员代扣除活动（".$this->product_info['title']."）保证金：".$total."。",$sign, array('goods_id'=>$this->product_info['id']));

            }

        }else{
            $this->error = '平台确认付款失败,重复操作';
            return FALSE;
        }

        $sqlMap = array();
        $sqlMap['goods_charge_way'] = $seller_charge_money;
        $sqlMap['goods_service'] = $service;
        $sqlMap['goods_deposit'] = $total;
        $sqlMap['seller_give_back'] = $seller_give_back;
        $result = model('product_'.$mod)->where(array('id'=>$this->product_info['id']))->save($sqlMap);
        if ($result === false) {
            $this->error = '平台确认付款失败';
            return FALSE;
        }
        // 将保证金写入商家保证金字段
        model('member_merchant')->where(array('userid'=>$userid))->setInc('frozen_deposit',$total);
        return TRUE;
    }


    /*获取拍a发b平台抽取服务费*/
    public function ab_reward($goods_price,$company_id){
       
        if ($goods_price <= 0) {
            $this->error ='参数错误';
            return false;

        }

        $groups = getcache('merchant_group','member');
        $groupid = model('member')->getFieldByUserid($company_id,'groupid');
        $config = unserialize($groups[$groupid]['config']);
        $trial = $config['trial'];
        if ($trial['a_b_cost'] == 0 ) {
            $service = '0.00';
        }else{
            $price_type = $trial['a_b'];
            array_multisort($price_type,'min','SORT_ASC');
            if(!empty($price_type) && is_array($price_type)){
                $service = 0;//缴纳的佣金
                $price_type = array_values($price_type);

                foreach ($price_type as $k=>$v){
                  
                    if($price_type[$k+1]['min'] >0 ){
                        
                        if( $goods_price >= $v['min'] && $goods_price < $price_type[$k+1]['min'] ){
                            
                            $service = sprintf('%.2f',$v['a_b_trial']);
                             break;
                        }


                    }else{
                        if( $goods_price >= $v['min']){
                        $service = sprintf('%.2f',$v['a_b_trial']);
                         break;
                        }

                    }
                    
                }
            }
        }

        return $service;

    }

    /*获取红包平台抽取服务费*/

    public function red_reward($goods_price,$company_id){
        if ($goods_price <= 0) {
            $this->error ='参数错误';
            return false;

        }
        
        $groups = getcache('merchant_group','member');
        $groupid = model('member')->getFieldByUserid($company_id,'groupid');
        $config = unserialize($groups[$groupid]['config']);
        $trial = $config['trial'];
        if ($trial['red_cost'] == 0 ) {
            $service = '0.00';
        }else{
            
            $price_type = $trial['red'];
            array_multisort($price_type,'min','SORT_ASC');
            if(!empty($price_type) && is_array($price_type)){
                $service = 0;//缴纳的佣金
                $price_type = array_values($price_type);

                foreach ($price_type as $k=>$v){
                    if($price_type[$k+1]['min'] >0 ){
                        if( $goods_price >= $v['min'] && $goods_price < $price_type[$k+1]['min'] ){
                         $service = sprintf('%.2f',$v['red_trial']);
                         break;
                        }


                    }else{
                        if( $goods_price >= $v['min']){
                         $service = sprintf('%.2f',$v['red_trial']);
                         break;
                        }

                    }
                    
                }
            }
        }

        return  $service;

    }


}
