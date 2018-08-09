<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Pay\Library\Driver;
use Pay\Library\Pay;
if (isset($set_modules) && $set_modules == TRUE) {
    $i = isset($modules) ? count($modules) : 0;
    $modules[$i]['code']    = basename(__FILE__, '.class.php');
    $modules[$i]['name']    = '环迅支付';   
    $modules[$i]['desc']    = '环迅支付总部位于上海，在长春、天津、北京、南京、杭州、西安、成都、重庆、武汉、福州、广州及深圳均拥有分支机构';
    $modules[$i]['is_cod']  = '0';
    $modules[$i]['is_online']  = '1';
    $modules[$i]['author']  = 'TPCMS开发团队';
    $modules[$i]['website'] = 'http://www.dealswill.com';
    $modules[$i]['version'] = '1.0.0';
    $modules[$i]['logo'] = '/static/images/pay/ips.jpg';
    $modules[$i]['config']  = array(
     	array('name' => '商户帐号','type' => 'text','value' => ''),
        array('name' => 'MD5密钥','type' => 'text','value' => ''),
        array('name' => '商户号','type' => 'text','value' => ''),
    );
    return;
}
class Ipspay extends Pay {
	public function __construct($config = array()) {	
		if (!empty($config)) $this->set_config($config);
	    if ($this->config['service_type']==1) $this->config['service'] = 'trade_create_by_buyer';
		elseif($this->config['service_type']==2) $this->config['service'] = 'create_direct_pay_by_user';
        else $this->config['service'] = 'create_partner_trade_by_buyer';	
		$this->config['gateway_url'] = 'http://bankbackuat.ips.com.cn/psfp-entry/gateway/payment.html';
		$this->config['gateway_method'] = 'POST';
		$this->config['return_url'] = return_url('ipspay');
	}

	public function getpreparedata() {		
		$prepare_data['service'] = $this->config['service'];
		$prepare_data['payment_type'] = '1';
		$prepare_data['seller_email'] = $this->config['alipay_account'];
		$prepare_data['partner'] = $this->config['alipay_partner'];
		$prepare_data['_input_charset'] = CHARSET;		
		$prepare_data['notify_url'] = $this->config['notify_url'];
		$prepare_data['return_url'] = $this->config['return_url'];
		
		// 商品信息
		$prepare_data['subject'] = $this->product_info['name'];
		$prepare_data['body'] = $this->product_info['body'];
		if (array_key_exists('url', $this->product_info)) $prepare_data['show_url'] = $this->product_info['url'];
		if (!array_key_exists('total_fee', $this->product_info)) {
			$prepare_data['price'] = $this->product_info['price'];
			$prepare_data['quantity'] = $this->product_info['quantity'];
		} else {
			$prepare_data['total_fee'] = $this->product_info['total_fee'];
		}		
		//订单信息
		$prepare_data['out_trade_no'] = $this->product_info['trade_sn'];

		// 物流信息
		if($this->config['service'] == 'create_partner_trade_by_buyer' || $this->config['service'] == 'trade_create_by_buyer') {
			$prepare_data['logistics_type'] = 'EXPRESS';
			$prepare_data['logistics_fee'] = '0.00';
			$prepare_data['logistics_payment'] = 'SELLER_PAY';
		}
		//买家信息
		$prepare_data['buyer_email'] = $this->product_info['buyer_email'];
		
		$prepare_data = arg_sort($prepare_data);
		// 数字签名
		$prepare_data['sign'] = build_mysign($prepare_data,$this->config['alipay_key'],'MD5');
		return $prepare_data;
	}

    /**
	 * POST接收数据
	 * 状态码说明  （0 交易完成 1 待付款 2 待发货 3 待收货 4 交易关闭 5交易取消
	 */
    public function notify() {
    	$param = I('param.');
    	$receive_sign = $param['sign'];
    	$receive_data = $this->filterParameter($param);
    	$receive_data = arg_sort($receive_data);
    	if ($receive_data) {
			$verify_result = $this->get_verify('http://notify.alipay.com/trade/notify_query.do?service=notify_verify&partner=' . $this->config['alipay_partner'] . '&notify_id=' . $receive_data['notify_id']);
			if (preg_match('/true$/i', $verify_result)){
				$sign = '';
				$sign = build_mysign($receive_data,$this->config['alipay_key'],'MD5');
				if ($sign != $receive_sign) {
					return false;
				} else {
					$return_data['trade_sn'] = $receive_data['out_trade_no'];
					$return_data['total_fee'] = $receive_data['total_fee'];
					$return_data['buyer_email'] = $receive_data['buyer_email'];
					switch ($receive_data['trade_status']) {
						case 'WAIT_BUYER_PAY': $return_data['order_status'] = 1; break;
						case 'WAIT_SELLER_SEND_GOODS': $return_data['order_status'] = 2; break;
						case 'WAIT_BUYER_CONFIRM_GOODS': $return_data['order_status'] = 3; break;
						case 'TRADE_CLOSED': $return_data['order_status'] = 4; break;						
						case 'TRADE_FINISHED': $return_data['order_status'] = 0; break;
						case 'TRADE_SUCCESS': $return_data['order_status'] = 0; break;
						default:
							 $return_data['order_status'] = 5;
					}
					/* 埋钩子 */
					//runhook('pay_success', $return_data);
					$this->pay_success($return_data);
					return $return_data;
				}
			} else {
				return false;
			}
		} else {
			return false;
		}   	
    }
    
    
    /**
     * 充值入账 废弃以前的不生效 移动过来的
     * @param $result
     */

    	public function pay_success(&$param) {
    		if ($param && $param['order_status'] == 0) {
    			$sqlmap = array();
    			$sqlmap['trade_sn'] = $param['trade_sn'];
    			$order_info = model("pay_order")->where($sqlmap)->find();
    			if ($order_info && $order_info['status'] != 1 && (int) $order_info['userid'] > 0) {
    				/* 更改订单状态 */
    				model("pay_order")->where($sqlmap)->setField(array('status'=>'1','buyer_email'=>$param['buyer_email']));
    				/* 写入账户明细 */
    				$total = $order_info['total_fee'];
    				$sign = '4-4-'.$order_info['userid'].'-'.$total.'-'.$order_info['trade_sn'];
    				$rs = model('member_finance_log')->where(array('only'=>$sign))->find();
    				if(!$rs){
    				    action_finance_log($order_info['userid'],$total,'money','用户：'.$order_info['userid'].'在线快速充值',$sign,array('order_id'=>$order_info['trade_sn']));
    				}
    			}
    		}
    	}
   

    /**
     * 相应服务器应答状态
     * @param $result
     */
    public function response($result) {
    	if (FALSE == $result) echo 'fail';
		else echo 'success';
    }
    
    /**
     * 返回字符过滤
     * @param $parameter
     */
	private function filterParameter($parameter)
	{
		$para = array();
		foreach ($parameter as $key => $value)
		{
			if ('sign' == $key || 'sign_type' == $key || '' == $value || 'm' == $key  || 'a' == $key  || 'c' == $key   || 'code' == $key || 'method' == $key) continue;
			else $para[$key] = $value;
		}
		return $para;
	}
}
?>