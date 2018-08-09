<?php
/**
 * 购物返利订单驱动
 * @version        $Id$
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Commission\Library\Driver;
class order extends \Commission\Library\OrderInterface {
    /* 填写订单号 */
    public function fill_trade_no($trade_sn, $cause = '') {
        if(!in_array($this->order_info['status'], array(2,3,4))) {
            $this->error = '您无权填写订单号';
            return FALSE;
        }


        if ($this->order_info['status'] == 3 && C_READ('buyer_update_order_type','commission') > 0) {
             $buyer_time = C_READ('buyer_update_order_type','commission');
             $times = $this->order_info['check_time']+$buyer_time*3600;
             if (NOW_TIME > $times) {
                    $this->error = '限定修改订单号时间已过！';
                    return FALSE;
                   
                }
        }

        if(empty($trade_sn)) {
            $this->error = '订单号不能为空';
            return FALSE;
        }
        model('order')->where(array('id'=>$this->order_info['id']))->setField('order_sn', $trade_sn);
        $cause = ($cause) ? $cause : '填写订单号';
        return $this->set_status(3, $cause);
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
        $price = sprintf('%.2f',($this->product_info['goods_price'] + $this->product_info['goods_bonus'] ));
        $money = model('member')->getFieldByUserid($this->product_info['company_id'],'money');
        if ($money < $price){
            $this->error = '商家账户余额不足，请充值！当前余额'.$money.'元,<span style="color:red;">还需充值'.($price-$money).'元</span>';
            return FALSE;
        }

         $sign = '2-commission-'.$this->order_info['seller_id'].'-'.$this->order_info['goods_id'].'-'.$this->order_info['id'].'-3';
       
        $rs = model('member_finance_log')->where(array('only'=>$sign))->find();
        if(!$rs){

          
           // 增加会员的余额
           /* action_finance_log($this->order_info['buyer_id'], $price, 'money', '订单结算', array('goods_id' => $this->order_info['goods_id'], 'order_id' => $this->order_info['id']));*/

             // 增加商家的余额下单价+红包
           action_finance_log($this->order_info['seller_id'], $price, 'money', '订单结算，平台退还给商家的资金为:'.$price."元", array('goods_id' => $this->order_info['goods_id'], 'order_id' => $this->order_info['id']));
            // 扣除商家冻结保证金
            $seller_price = sprintf('%.2f',($this->product_info['goods_service']+$price));
            
            action_finance_log($this->order_info['seller_id'], -$seller_price, 'deposit', '订单[ID:'.$this->order_info['id'].']完成，扣除'.$seller_price.'元保证金', array('goods_id' => $this->order_info['goods_id'], 'order_id' => $this->order_info['id']),FALSE);
            /*保证金*/

            model('member_merchant')->where(array('userid' => $this->order_info['seller_id']))->setDec('frozen_deposit',$seller_price);
             $this->set_status(7);
            if (!defined('IN_ADMIN')) {
                $this->write_log('商家确认付款');
            }else{
                $this->write_log('平台已处理并确认付款');
            }
             $this->write_log('订单已完成');
            return TRUE;
        }else{
            $this->error = '平台确认付款失败,重复操作';
            return FALSE;
        }



    }

        
    /**
     * 设置订单状态
     * @param int $status
     * @param string $cause
     * @return bool
     */
    public function set_status($status = 0, $cause = '') {
        if ((int)$status === 0 && $this->product_info['already_num'] > 0) { // 关闭订单时，对已购买的字段 -1
            model('product')->where(array('id' => $this->product_info['id']))->setDec('already_num');
        }
        $info = array();
        $info['id'] = $this->order_info['id'];
        $info['status'] = $status;
        $info['check_time'] = NOW_TIME;
        if ($status == 7) {
            $info['complete_time'] = NOW_TIME;
        }
        $result =  model('order')->update($info);
        if($cause && $result) $this->write_log($cause);
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