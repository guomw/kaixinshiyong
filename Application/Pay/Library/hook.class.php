<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Pay\Library;
class hook
{
	/**
	 * 支付成功
	 * @param  array $param [description]
	 * @param   trade_sn - 订单号
	 * @param   total_fee -支付金额
	 * @param   price - 价格
	 * @param   order_status - 订单状态（0 交易完成 1 支付成功
	 */
	// public function pay_success(&$param) {
	// 	F('lllllll2',&$param,CONF_PATH);
	// 	if ($param && $param['order_status'] == 0) {
	// 		$sqlmap = array();
	// 		$sqlmap['trade_sn'] = $param['trade_sn'];
	// 		$order_info = model("pay_order")->where($sqlmap)->find();
	// 		F('llllllll2',$order_info,CONF_PATH);
	// 		if ($order_info && $order_info['status'] != 1 && (int) $order_info['userid'] > 0) {
	// 			/* 更改订单状态 */
	// 			model("pay_order")->where($sqlmap)->setField(array('status'=>'1','buyer_email'=>$param['buyer_email']));
	// 			/* 写入账户明细 */
	// 			$total = $order_info['total_fee'];
	// 			$sign = '4-4-'.$order_info['userid'].'-'.$total.'-'.$order_info['trade_sn'];
	// 			$rs = model('member_finance_log')->where(array('only'=>$sign))->find();
	// 			if(!$rs){
	// 			    action_finance_log($order_info['userid'],$total,'money','用户：'.$order_info['userid'].'充值',$sign,array('order_id'=>$order_info['trade_sn']));
	// 			}
	// 		}
	// 	}
	// }
}


