<?php
/**
 * 购物返利订单驱动
 * @version        $Id$
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Product\Library\Driver;
class commission_order extends \Product\Library\OrderInterface {
    /* 填写订单号 */
    public function fill_trade_no($arr, $cause = '',$userid='') {
        if(!in_array($this->order_info['status'], array(2,3,4))) {
            $this->error = '您无权填写订单号';
            return FALSE;
        }
     
        if(empty($arr['order_sn'])) {
            $this->error = '订单号不能为空';
            return FALSE;
        }
        model('order')->where(array('id'=>$this->order_info['id']))->setField('order_sn', $arr['order_sn']);
        model('order')->where(array('id'=>$this->order_info['id']))->setField('order_img', $arr['order_img']);

        $cause = ($cause) ? $cause : '填写订单号';
        return $this->set_status(3, $cause,$userid);
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
            $this->write_log('商家订单审核通过、待商家付款');
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

    /* 确认付款 */
    public function pay() {
        if($this->order_info['status'] != 5 && !defined('IN_ADMIN')) {
            $this->error = '当前订单不是已审核通过状态';
            return FALSE;
        }
        $price = sprintf('%.2f', ($this->product_info['goods_price']+ $this->product_info['bonus_price'] ));
        if($price > 0) {
            // 增加会员的余额
            $sign = '1-'.$this->product_info['mod'].'-'.$this->order_info['buyer_id'].'-'.$this->order_info['goods_id'].'-'.$this->order_info['id'].'-'.$price;
            $rs = model('member_finance_log')->where(array('only'=>$sign))->find();
            if(!$rs){
                if ($this->product_info['mod'] == 'trial') {
                    $mod_name = C_READ('TRIAL_NAME','trial').'活动';
                }elseif ($this->product_info['mod'] == 'rebate') {
                    $mod_name = C('REBATE_NAME').'活动';
                }elseif ($this->product_info['mod'] == 'postal') {
                    $mod_name = C_READ('POSTAL_NAME','trial').'活动';
                }elseif ($this->product_info['mod'] == 'commission') {
                    $mod_name = C_READ('COMMISSION_NAME','commission').'活动';
                }else{
                    $mod_name = '';
                }
                action_finance_log($this->order_info['buyer_id'], $price, 'money', $mod_name.'('.$this->product_info['title'].')已完成，资金已实时充入您的账户，感谢参与！', $sign, array('goods_id' => $this->order_info['goods_id'], 'order_id' => $this->order_info['id']));
                 // 添加成功日志
                 if (!defined('IN_ADMIN')) {
                     $this->write_log('商家确认付款');
                 }else{
                     $this->write_log('平台已处理并确认付款');
                 }

            }else{
               $this->error = '重复操作'; 
               return FALSE;
            }
            
            $this->set_status(7);
            // 扣除商家冻结保证金
            $sign1 = '1-'.$this->product_info['mod'].'-'.$this->order_info['seller_id'].'-'.$this->order_info['goods_id'].'-'.$this->order_info['id'].'-'.sprintf('%.2f',($price+$this->product_info['goods_service']));
            $rs1 = model('member_finance_log')->where(array('only'=>$sign1))->find();
            if(!$rs1){
                action_finance_log($this->order_info['seller_id'], -(sprintf('%.2f',($price+$this->product_info['goods_service']))), 'deposit', '订单[ID:'.$this->order_info['id'].']完成，扣除'.sprintf('%.2f',($price+$this->product_info['goods_service'])).'元保证金', $sign1, array('goods_id' => $this->order_info['goods_id'], 'order_id' => $this->order_info['id']),FALSE);
            }else{
                $this->error = '重复操作'; 
                return FALSE;
            }

         $new_sign = '8-'.$this->order_info['act_mod'].'-'.$this->order_info['seller_id'].'-'.$this->order_info['goods_id'].'-'.$this->order_info['id'].'-'.$this->product_info['goods_service'];
         $new_sign_1 = model('member_finance_log')->where(array('only'=>$new_sign))->find();

        if (!$new_sign_1) {
           action_finance_log($this->order_info['seller_id'], -$this->product_info['goods_service'], 'service', '订单[ID:'.$this->order_info['id'].']完成，将在活动结算收取单笔服务费'.$this->product_info['goods_service'].'服务费', $new_sign, array('goods_id' => $this->order_info['goods_id'], 'order_id' => $this->order_info['id']),FALSE);
        }


        $subsidy_sign = $new_sign = '9-'.$this->order_info['act_mod'].'-'.$this->order_info['buyer_id'].'-'.$this->order_info['goods_id'].'-'.$this->order_info['id'].'-'.$this->product_info['goods_price'];
        $subsidy_sign_1 = model('member_finance_log')->where(array('only'=>$subsidy_sign))->find();
        if (!$subsidy_sign_1) {
            if ($this->product_info['subsidy_type'] == 1 && $this->product_info['subsidy'] > 0) {

                 action_finance_log($this->order_info['buyer_id'], $this->product_info['subsidy'], 'point', '('.$this->product_info['title'].')订单[ID:'.$this->order_info['id'].']完成，平台补贴'.$this->product_info['subsidy'].'积分', $subsidy_sign, array('goods_id' => $this->order_info['goods_id'], 'order_id' => $this->order_info['id']),FALSE);
             }elseif($this->product_info['subsidy_type'] == 2 && $this->product_info['subsidy'] > 0){
                    action_finance_log($this->order_info['buyer_id'], $this->product_info['subsidy'], 'money', '('.$this->product_info['title'].')订单[ID:'.$this->order_info['id'].']完成，平台补贴'.$this->product_info['subsidy'].'元', $subsidy_sign, array('goods_id' => $this->order_info['goods_id'], 'order_id' => $this->order_info['id']));

             }
            
        }
            model('member_merchant')->where(array('userid' => $this->order_info['seller_id']))->setDec('frozen_deposit',sprintf('%.2f',($price+$this->product_info['goods_service'])));

            $this->write_log('订单完成');
        }
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
}