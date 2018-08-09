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
    $modules[$i]['name']    = '商银信';
    $modules[$i]['desc']    = '第三方支付行业较早实践者商银信支付服务有限责任公司，致力于预付费卡及互联网支付的O2O并行发展；为企业、机构、行业客户及投资者提供专业的综合支付服务。';
    $modules[$i]['is_cod']  = '0';
    $modules[$i]['is_online']  = '1';
    $modules[$i]['author']  = 'TPCMS开发团队';
    $modules[$i]['website'] = 'http://www.dealswill.com';
    $modules[$i]['version'] = '1.0.0';
    $modules[$i]['config']  = array(
        'merchantid'=>array('name' => 'merchantid','type' => 'text','value' => ''),
        'key'=>array('name' => 'key','type' => 'text','value' => '')
    );
    return;
}
class Allscore extends Pay {
	public function __construct($config = array()) {
        if (!empty($config)) $this->set_config($config);
        $this->config['gateway_url']= 'http://211.157.145.8:8090/olgateway/serviceDirect.htm?';

        //字符编码格式 目前支持 utf-8
        $this->config['input_charset']= 'UTF-8';

        //访问模式,根据自己的服务器是否支持ssl访问，若支持请选择https；若不支持请选择http
        $this->config['transport']    = 'http';

        $this->config['AllscorePublicKey']=APP_PATH.'Pay/Library/Driver/key/allscore_public_key.pem';
        $this->config['MerchantPrivateKey']=APP_PATH.'Pay/Library/Driver/key/rsa_private_key.pem';

		$this->config['notifyUrl'] = return_url('allscore',1);
	}

	public function getpreparedata() {		
		$prepare_data['merchantId'] = $this->config['merchantid'];
		$prepare_data['inputCharset'] = trim($this->config['input_charset']);;
		$prepare_data['notifyUrl'] = $this->config['notifyUrl'];
        $prepare_data['payMethod'] = "fastPay";
        $prepare_data['service'] = "directPay";

		// 商品信息
		$prepare_data['subject'] = $this->product_info['name'];
		$prepare_data['body'] = $this->product_info['body'] ? $this->product_info['body'] : $this->product_info['name'];
		if (!array_key_exists('total_fee', $this->product_info)) {
			$prepare_data['transAmt'] = $this->product_info['price'];
		} else {
			$prepare_data['transAmt'] = $this->product_info['total_fee'];
		}

		//订单信息
		$prepare_data['outOrderId'] = $this->product_info['trade_sn'];

        $prepare_data['outAcctId'] = $this->product_info['userid'];
        $prepare_data['cardType'] = $this->product_info['cardType'];

		$prepare_data = arg_sort($prepare_data);
		// 数字签名
        $prepare_data['sign'] = build_mysign($prepare_data,$this->config['key'],'MD5');
        $prepare_data['signType'] = 'MD5';


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
			$verify_result = $this->get_verify2('http://211.157.145.8/olgateway/noticeQuery.htm?merchantId='. $this->config['merchantid'].'&notifyId='.$receive_data['notifyId']);
			if (preg_match('/true$/i', $verify_result)){
				$sign = '';
				$sign = build_mysign($receive_data,$this->config['key'],'MD5');
				if ($sign != $receive_sign) {
					return false;
				} else {
//                    \Think\Log::write('进来了就可以了！');
					$return_data['trade_sn'] = $receive_data['outOrderId'];
					$return_data['total_fee'] = $receive_data['transAmt'];
//					$return_data['buyer_email'] = $receive_data['buyer_email'];
					switch ($receive_data['tradeStatus']) {
						case '1': $return_data['order_status'] = 1; break;
						case '2': $return_data['order_status'] = 0; break;
						default:
							 $return_data['order_status'] = 5;
					}
					/* 埋钩子 */
					runhook('pay_success', $return_data);
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