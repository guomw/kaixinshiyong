<?php
/**
 * 购物返利订单驱动
 * @version        $Id$
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Product\Library\Driver;
class trial_order extends \Product\Library\OrderInterface {
    /* 填写订单号 */
    public function fill_trade_no($trade_sn, $cause = '',$userid='') {
        if(!in_array($this->order_info['status'], array(2,4))) {
            $this->error = '您无权填写订单号';
            return FALSE;
        }
        if(empty($trade_sn)) {
            $this->error = '订单号不能为空';
            return FALSE;
        }
        model('order')->where(array('id'=>$this->order_info['id']))->setField('order_sn', $trade_sn);
        $cause = ($cause) ? $cause : '填写订单号';
        return $this->set_status(2, $cause,$userid);
    }
    
    /* 关闭抢购 */
    public function close() {
        if(!defined('IN_ADMIN') && $this->order_info['status'] != 2 && $this->order_info['status'] != 1) {
            $this->error = '您无权关闭该订单';
            return FALSE;
        }
        $this->set_status(0, '关闭抢购资格');
        return TRUE;
    }

    /* 试用流程更新:审核订单号 */
    public function check_ordersn($ispass = 2) {
        if($this->order_info['status'] != 2 && !$this->order_info['trial_report']) {
            $this->error = '您无权审核该订单号';
            return FALSE;
        }
        if ( (int)$ispass == 1 ) {
            $this->set_status(8, '审核通过订单号');
        }else{
            $this->set_status(4, '审核未通过订单号');
        }
        return TRUE;
    }

    /* 订单通过 */
    public function pass($cause='') {
        if($this->order_info['status'] != 3) {
            $this->error = '当前订单不是待审核状态';
            return FALSE;
        }
        $result = $this->set_status(5);
        if(!$result) {
            $this->error = '订单审核失败';
            return FALSE;
        }
        if ($cause) {
            $this->write_log($cause);
        }else{
            $this->write_log('商家订单审核通过');
        }
        return TRUE;
    }

    /* 拒绝通过 */
    public function refuse($cause='') {
        if($this->order_info['status'] != 3) {
            $this->error = '当前订单不是待审核状态';
            return FALSE;
        }
        $result = $this->set_status(4);
        if(!$result) {
            $this->error = '订单审核拒绝操作失败';
            return FALSE;
        }
        if ($cause) {
            $this->write_log($cause);
        }else{
            $this->write_log('商家订单拒绝通过');
        }
        return TRUE;
    }

    /* 撤销通过 */
    public function cancel() {
        if($this->order_info['status'] != 5) {
            $this->error = '当前订单不是已审核通过状态';
            return FALSE;
        }
       return $this->set_status(4, '商家订单撤销通过');
    }

    /* 确认付款 
     *  $price : (该参数已废)
     *  $appraised :  付款评优
     *  $val : 评优赠送的 数值
     *  $is_hook : 是否由钩子执行
    */
    public function pay($price,$appraised,$val,$is_hook='FALSE') {

        if($this->order_info['status'] != 3 && $this->order_info['order_sn'] && !defined('IN_ADMIN') && $is_hook=='FALSE') {
            $this->error = '当前订单不是待审核状态';
            return FALSE;
        }

        $price = sprintf('%.2f',$this->product_info['goods_price']);     
                 
        if ((int)$appraised == 2) {    //  商品评优并支付给会员
            // 检测商家账户余额
            $seller_money = model('member')->where(array('userid'=>$this->product_info['company_id']))->getField("money");
            if ($seller_money < $val) {
                $this->error = '当前余额不足，不能评优并支付！';
                return FALSE;
            }
            // 扣除商家付款的钱
            $sign = '1-'.$this->product_info['mod'].'-'.$this->order_info['seller_id'].'-'.$this->order_info['goods_id'].'-'.$this->order_info['id'].'-'.$val.'-3';
            $rs = model('member_finance_log')->where(array('only'=>$sign))->find();
            if(!$rs){
                /*评优奖励会员明细*/
                action_finance_log($this->order_info['seller_id'], -$val, 'money', '订单结算(并赠送会员'.$val.'美元 试用报告优秀奖励)', $sign, array('goods_id' => $this->order_info['goods_id'], 'order_id' => $this->order_info['id']));
            }else{
                $this->error = '商家订单结算，重复操作';
                return FALSE;
            }

            // 增加会员的余额
            $sign1 = '1-'.($this->product_info['mod']).'-'.($this->order_info['buyer_id']).'-'.($this->order_info['goods_id']).'-'.($this->order_info['id']).'-'.($price+$val).'-3';
            $rs1 = model('member_finance_log')->where(array('only'=>$sign1))->find();
            if(!$rs1){
                 if (C('sso_is_open') == 1) {
                    $infos = array();
                    $infos['userid'] = $this->order_info['buyer_id'];
                    $infos['type'] = 'money';
                    $infos['seller_id'] = $this->order_info['seller_id'];
                    $infos['type_id'] = '2101';
                    $infos['num'] = $price+$val;
                    $infos['order_id'] = $this->order_info['id'];
                    $ret = _ps_send('money',$infos);
                    $data = php_data($ret);
                    if ($data['status'] == 1) {
                       action_finance_log($this->order_info['buyer_id'], $price+$val, 'money', '订单结算(并获得商家'.$val.'美元 试用报告优秀奖励)', $sign1, array('goods_id' => $this->order_info['goods_id'], 'order_id' => $this->order_info['id']));
                    }else{
                        $this->error = '试用订单结算失败';
                    }
                    # code...
                 }else{

                     action_finance_log($this->order_info['buyer_id'], $price+$val, 'money', '订单结算(并获得商家'.$val.'美元 试用报告优秀奖励)', $sign1, array('goods_id' => $this->order_info['goods_id'], 'order_id' => $this->order_info['id']));
                 }

            }else{
                $this->error = '会员订单结算，重复操作';
                return FALSE;
            }
            // 设置该订单状态(字段：appraised)为已评优
            model('order')->where(array('id'=>$this->order_info['id']))->setField('appraised','1');
            $this->write_log('商家确认订单并付款(并赠送会员'.$val.'美元试用报告优秀奖励)');
        }else{
            // 增加会员的余额
            if ($this->product_info['mod'] == 'trial') {
                $mod_name = C_READ('TRIAL_NAME','trial').'活动';
            }elseif ($this->product_info['mod'] == 'rebate') {
                 $mod_name = C('REBATE_NAME').'活动';
            }elseif ($this->product_info['mod'] == 'postal') {
                $mod_name = C_READ('POSTAL_NAME','trial').'活动';
            }else{
                $mod_name = '';
            }
            $sign = '1-'.$this->product_info['mod'].'-'.$this->order_info['buyer_id'].'-'.$this->order_info['goods_id'].'-'.$this->order_info['id'].'-'.$price.'-1';
            $rs = model('member_finance_log')->where(array('only'=>$sign))->find();
            if(!$rs){

                 if (C('sso_is_open') == 1) {
                    $infos = array();
                    $infos['userid'] = $this->order_info['buyer_id'];
                    $infos['type'] = 'money';
                    $infos['seller_id'] = $this->order_info['seller_id'];
                    $infos['type_id'] = '2101';
                    $infos['num'] = $price;
                    $infos['order_id'] = $this->order_info['id'];
                    $ret = _ps_send('money',$infos);
                    $data = php_data($ret);
                    if ($data['status'] == 1) {
                       action_finance_log($this->order_info['buyer_id'], $price, 'money', $mod_name.'('.$this->product_info['title'].')已完成，资金已实时充入您的账户，感谢参与！', $sign, array('goods_id' => $this->order_info['goods_id'], 'order_id' => $this->order_info['id']));
                    }else{
                        $this->error = '试用订单结算失败';
                    }
                    # code...
                }else{

                     action_finance_log($this->order_info['buyer_id'], $price, 'money', $mod_name.'('.$this->product_info['title'].')已完成，资金已实时充入您的账户，感谢参与！', $sign, array('goods_id' => $this->order_info['goods_id'], 'order_id' => $this->order_info['id']));
                }




               
            }else{
                $this->error = '会员订单完成，重复操作';
                return FALSE;
            }
            if (defined('IN_ADMIN') || $is_hook=='true') {
                $this->write_log('审核试用报告超时，系统自动审核通过并付款');
            }else{
                $this->write_log('商家确认订单并付款');
            }
        } 
        
        // 剩余推广费是否退还 [后台->活动设置 0:不退还；1:退还]
        $seller_give_back = C_READ('seller_give_back','trial');
        // 收费方式 [后台->活动设置 0:按单份；1:按单场]
        $goods_charge_way = $this->product_info['goods_charge_way'];
        if ($seller_give_back == 1 && $goods_charge_way == 0 ) { 
            $minus_money = $price + $this->product_info['goods_service'];
        }else{
            $minus_money = $price;
        }
        $minus_money = sprintf('%.2f',$minus_money);
        
        // 扣除商家冻结保证金 和平台服务费
        $msign = '1-'.$this->product_info['mod'].'-'.$this->order_info['seller_id'].'-'.$this->order_info['goods_id'].'-'.$this->order_info['id'].'-'.$minus_money.'-1';
        $m = model('member_finance_log')->where(array('only'=>$msign))->find();
        if(!$m){
            action_finance_log($this->order_info['seller_id'], -$minus_money, 'deposit', '('.$this->product_info['title'].')订单[ID:'.$this->order_info['id'].']完成，扣除'.$minus_money.'美元保证金', $msign, array('goods_id' => $this->order_info['goods_id'], 'order_id' => $this->order_info['id']),FALSE);
        }else{
            $this->error = '扣除商家保证金，重复操作';
            return FALSE;
        }

        /*加入试用活动活动记录 8-活动名称-用户id-商品id-订单id-金额*/
        $new_sign = '8-'.$this->order_info['act_mod'].'-'.$this->order_info['seller_id'].'-'.$this->order_info['goods_id'].'-'.$this->order_info['id'].'-'.$this->product_info['goods_service'];
         $new_sign_1 = model('member_finance_log')->where(array('only'=>$new_sign))->find();
        if (!$new_sign_1) {
           action_finance_log($this->order_info['seller_id'], -$this->product_info['goods_service'], 'service', '订单[ID:'.$this->order_info['id'].']完成，将在活动结算收取单笔服务费'.$this->product_info['goods_service'].'服务费', $new_sign, array('goods_id' => $this->order_info['goods_id'], 'order_id' => $this->order_info['id']),FALSE);
        }

        /*平台补贴9-活动名称-用户id-商品id-订单id-下单金额*/

        $subsidy_sign = $new_sign = '9-'.$this->order_info['act_mod'].'-'.$this->order_info['buyer_id'].'-'.$this->order_info['goods_id'].'-'.$this->order_info['id'].'-'.$this->product_info['goods_price'];
        $subsidy_sign_1 = model('member_finance_log')->where(array('only'=>$subsidy_sign))->find();
        if (!$subsidy_sign_1) {
            if ($this->product_info['subsidy_type'] == 1 && $this->product_info['subsidy'] > 0) {

                 action_finance_log($this->order_info['buyer_id'], $this->product_info['subsidy'], 'point', '('.$this->product_info['title'].')订单[ID:'.$this->order_info['id'].']完成，平台补贴'.$this->product_info['subsidy'].'积分', $subsidy_sign, array('goods_id' => $this->order_info['goods_id'], 'order_id' => $this->order_info['id']),FALSE);
             }elseif($this->product_info['subsidy_type'] == 2 && $this->product_info['subsidy'] > 0){


                 if (C('sso_is_open') == 1) {
                    $infos = array();
                    $infos['userid'] = $this->order_info['buyer_id'];
                    $infos['type'] = 'money';
                    $infos['seller_id'] = $this->order_info['seller_id'];
                    $infos['type_id'] = '2103';
                    $infos['num'] = $this->product_info['subsidy'];
                    $infos['order_id'] = $this->order_info['id'];
                    $ret = _ps_send('money',$infos);
                    $data = php_data($ret);
                    if ($data['status'] == 1) {
                      action_finance_log($this->order_info['buyer_id'], $this->product_info['subsidy'], 'money', '('.$this->product_info['title'].')订单[ID:'.$this->order_info['id'].']完成，平台补贴'.$this->product_info['subsidy'].'美元', $subsidy_sign, array('goods_id' => $this->order_info['goods_id'], 'order_id' => $this->order_info['id']));
                    }else{
                        $this->error = '平台补贴失败';
                    }
                    # code...
                }else{

                    action_finance_log($this->order_info['buyer_id'], $this->product_info['subsidy'], 'money', '('.$this->product_info['title'].')订单[ID:'.$this->order_info['id'].']完成，平台补贴'.$this->product_info['subsidy'].'美元', $subsidy_sign, array('goods_id' => $this->order_info['goods_id'], 'order_id' => $this->order_info['id']));
                }

             }
            
        }

        


        model('member_merchant')->where(array('userid' => $this->order_info['seller_id']))->setDec('frozen_deposit',$minus_money);
        // 冻结保证金扣除红包费用
        if ($this->product_info['goods_bonus'] > 0) {
            $hsign = '1-'.$this->product_info['mod'].'-'.$this->order_info['seller_id'].'-'.$this->order_info['goods_id'].'-'.$this->order_info['id'].'-'.$this->product_info['goods_bonus'].'-2';
            $h = model('member_finance_log')->where(array('only'=>$hsign))->find();
            if(!$h){
                action_finance_log($this->order_info['seller_id'], -$this->product_info['goods_bonus'], 'deposit', '('.$this->product_info['title'].')订单[ID:'.$this->order_info['id'].']完成，扣除'.$this->product_info['goods_bonus'].'美元红包', $hsign, array('goods_id' => $this->order_info['goods_id'], 'order_id' => $this->order_info['id']),FALSE);
            }else{
                $this->error = '扣除商家红包，重复操作';
                return FALSE;
            }
            model('member_merchant')->where(array('userid' => $this->order_info['seller_id']))->setDec('frozen_deposit',$this->product_info['goods_bonus']);
        }
        // 如果有红包，则增加会员余额
        if ($this->product_info['goods_bonus'] > 0) {
            $bsign = '1-'.$this->product_info['mod'].'-'.$this->order_info['buyer_id'].'-'.$this->order_info['goods_id'].'-'.$this->order_info['id'].'-'.$this->product_info['goods_bonus'].'-2';;
            $b = model('member_finance_log')->where(array('only'=>$bsign))->find();
            if(!$b){


                if (C('sso_is_open') == 1) {
                    $infos = array();
                    $infos['userid'] = $this->order_info['buyer_id'];
                    $infos['type'] = 'money';
                    $infos['seller_id'] = $this->order_info['seller_id'];
                    $infos['type_id'] = '2102';
                    $infos['num'] = $this->product_info['goods_bonus'];
                    $infos['order_id'] = $this->order_info['id'];
                    $ret = _ps_send('money',$infos);
                    $data = php_data($ret);
                    if ($data['status'] == 1) {
                       action_finance_log($this->order_info['buyer_id'], $this->product_info['goods_bonus'], 'money', '('.$this->product_info['title'].')活动完成后奖励'.$this->product_info['goods_bonus'].'美元红包', $bsign, array('goods_id' => $this->order_info['goods_id'], 'order_id' => $this->order_info['id']));
                    }else{
                        $this->error = '试用订单结算失败';
                    }
                    # code...
                }else{

                      action_finance_log($this->order_info['buyer_id'], $this->product_info['goods_bonus'], 'money', '('.$this->product_info['title'].')活动完成后奖励'.$this->product_info['goods_bonus'].'美元红包', $bsign, array('goods_id' => $this->order_info['goods_id'], 'order_id' => $this->order_info['id']));
                }


               
            }else{
                $this->error = '会员红包增加，重复操作';
                return FALSE;
            }
            $this->write_log('试用完成后奖励'.$this->product_info['goods_bonus'].'美元红包');
        }

        /*收取会员缴纳的平台服务费*/
        $buyer_price_open = C_READ('buyer_order_fee','trial');
        if (intval($buyer_price_open) == 1) {
            $fee_price = $this->service_fee($this->product_info['goods_price']);
            /*12-userid-order_id*/
            $_fee_sign = '12-'.$this->order_info['buyer_id'].'-'.$this->order_info['id'];
            $fee_count = model('member_finance_log')->where(array('only'=>$_fee_sign))->count();

            if ($fee_count < 1) {
                 action_finance_log($this->order_info['buyer_id'], -$fee_price, 'money', '('.$this->product_info['title'].')活动完成,缴纳'.$fee_price.'美元平台服务费', $_fee_sign, array('goods_id' => $this->order_info['goods_id'], 'order_id' => $this->order_info['id']));
            }

        }
        $this->set_status(7);
        // 设置试用报告状态为审核通过
        $data = array();
        $data['status'] = 1;
        $data['updatetime'] = NOW_TIME;
        $a = model('trial_report')->where(array('order_id'=>$this->order_info['id']))->save($data);

        /*招商专员--start*/
        $companys = model('member')->field('agent_id')->find($this->order_info['seller_id']);
        if ($companys['agent_id'] > 0) {
            $agent = model('admin')->where(array('userid'=>$companys['agent_id']))->field('fee_type,service_fee,roleid')->find();
            if ($agent) {
                if ($agent['roleid'] == 6) {
                    //按照下单价提成
                    if($agent['fee_type'] == 2){
                        $money =sprintf("%.2f",$agent['service_fee']/100 * $this->product_info['goods_price']);
                        if($money > 0){
                            $msg = "买家(id:".$this->order_info['buyer_id']."),完成试用活动(id:".$this->order_info['goods_id']."),订单(id:".$this->order_info['id'].")，您获得单笔下单价提成".$money."美元";
                                $infos = array();
                                $infos['time'] = NOW_TIME;
                                $infos['type'] = 2;
                                $infos['money'] = $money;
                                $infos['agent_id']= $companys['agent_id'];
                                $infos['body'] = $msg;
                                model('company_log')->add($infos);
                        }

                    }

                    //按照下单服务费提成
                    if($agent['fee_type'] == 3){
                        $money =sprintf("%.2f", $agent['service_fee'] / 100 * $this->product_info['goods_service']);
                        if($money > 0){
                            $msg = "买家(id:".$this->order_info['buyer_id']."),完成试用活动(id:".$this->order_info['goods_id']."),订单(id:".$this->order_info['id'].")，您获得单笔服务费的 ".$agent['service_fee']."% 提成".$money."美元";
                            $infos = array();
                            $infos['time'] = NOW_TIME;
                            $infos['type'] = 5;
                            $infos['money'] = $money;
                            $infos['agent_id']= $companys['agent_id'];
                            $infos['body'] = $msg;
                            model('company_log')->add($infos);
                        }
                    }
                }
            }
        }
        /*招商专员--end*/
        $this->write_log('订单已完成');
        return TRUE;
    }
    
    /* 填写试用报告 */
    public function fill_trial_report($data,$userid=''){
        if (!$data) {
            $this->error = '试用报告内容为空，请重新填写试用报告';
            return FALSE;
        }
        $result =  model('trial_report')->update($data);
        if($result) $this->set_status(3,'填写试用报告成功，请等待商家审核哦~',$userid);
        return TRUE;
    }

    /**
     * 设置订单状态
     * @param int $status
     * @param string $cause
     * @return bool
     */
    public function set_status($status = 0, $cause = '',$userid='') {
        if ((int)$status === 0 && $this->order_info['status'] != 1) { // 关闭订单时，对已购买的字段 -1
            model('product')->where(array('id' => $this->product_info['id']))->setDec('already_num');
        }
        $info = array();
        $info['id'] = $this->order_info['id'];
        $info['status'] = $status;
        $info['check_time'] = NOW_TIME;
        $info['complete_time'] = NOW_TIME;
        $info['check_ip'] = get_client_ip();
        $result =  model('order')->update($info);
        if($cause && $result) $this->write_log($cause,$userid);
        return TRUE;
    }
    
    /* 删除订单 */
    public function delete() {
        if ($this->order_info['status'] != 0) {
            $this->error = '该订单不是关闭状态，不能删除';
            return FALSE;
        }
        model('order')->where(array('id'=>$this->order_info['id']))->delete();
    }
    
    /* 获取活动配置 */
    private function getActConfig() {
        return model('activity_set')->where(array('activity_type' => $this->product_info['mod']))->getField('key,value', TRUE);
    }

    /*会员缴纳平台服务费*/
    public function service_fee($goods_price){
        if (!$goods_price) {
            $this->error = '参数错误';
            return false;
        }

        $trial_price = C_READ('trial_fee_price','trial');
        $price = string2array($trial_price);
        $trial = $price['trial'];
        $price_type  = $trial['trial_fee_price'];
        array_multisort($price_type,'min','SORT_ASC');
            if(!empty($price_type) && is_array($price_type)){
                $service = 0;//缴纳的佣金
                $price_type = array_values($price_type);

                foreach ($price_type as $k=>$v){
                  
                    if($price_type[$k+1]['min'] >0 ){
                        
                        if( $goods_price >= $v['min'] && $goods_price < $price_type[$k+1]['min'] ){
                            
                            $service = sprintf('%.2f',$v['trial']);
                             break;
                        }


                    }else{
                        if( $goods_price >= $v['min']){
                        $service = sprintf('%.2f',$v['trial']);
                         break;
                        }

                    }
                    
                }
            }

            return $service;

    }



}