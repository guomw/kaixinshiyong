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
    $modules[$i]['name']    = '微信支付';   
    $modules[$i]['desc']    = '微信支付商户平台，为商家提供全新的微信支付管理与服务';
    $modules[$i]['is_cod']  = '0';
    $modules[$i]['is_online']  = '1';
    $modules[$i]['author']  = 'TPCMS开发团队';
    $modules[$i]['website'] = 'http://www.dealswill.com';
    $modules[$i]['version'] = '1.0.0';
    $modules[$i]['logo'] = '/static/images/pay/weixin.jpg';
    $modules[$i]['config']  = array(
    	   array('name' => 'APPID','type' => 'text','value' => ''),
    	   array('name' => 'MCHID','type' => 'text','value' => ''),
    	   array('name' => 'KEY','type' => 'text','value' => ''),
    	   array('name' => 'APPSECRET','type' => 'text','value' => ''),
    );
    return;
}
class Weixin extends Pay  {
	public function __construct($config = array(),$product_info) {	

      ini_set('date.timezone','Asia/Shanghai');
      //模式二
      /**
       * 流程：
       * 1、调用统一下单，取得code_url，生成二维码
       * 2、用户扫描二维码，进行支付
       * 3、支付完成之后，微信服务器会通知支付成功
       * 4、在支付成功通知中需要查单确认是否真正支付成功（见：notify.php）
       */
      $input = new \Wechat\Pay\lib\WxPayUnifiedOrder();
      $input->SetBody($product_info['name']);
      $input->SetAttach($product_info['userid']);
      $input->SetOut_trade_no($product_info['trade_sn']);
      $input->SetTotal_fee($product_info['total_fee'] * 100  ); //美元转换为分
      $input->SetTime_start(date("YmdHis"));
      $input->SetTime_expire(date("YmdHis", time() + 600));
      $input->SetGoods_tag("");
      $input->SetNotify_url("http://".$_SERVER['HTTP_HOST']."/weixin_api.php");
      $input->SetTrade_type("NATIVE");
      $input->SetProduct_id("1");
      $result = $this->GetPayUrl($input);
      $url2 = $result["code_url"];
      if($result["code_url"]){
      	$data = array('status' => 1,'src' =>"http://paysdk.weixin.qq.com/example/qrcode.php?data=$url2"); 
      	echo json_encode($data);
      	return true;
      }else{
      	echo json_encode(array('status' => 0,'msg' =>'订单创建失败，请联系平台客服'));
      	return false;
      }
	}

    
    /*废弃以前的商品信息获取*/
    public function getpreparedata() {		
    	// 数字签名
    	$prepare_data['sign'] = build_mysign($prepare_data,$this->config['alipay_key'],'MD5');
    	return $prepare_data;
    }


    /**
	 * POST接收数据
	 * 状态码说明  （0 交易完成 1 待付款 2 待发货 3 待收货 4 交易关闭 5交易取消
	 */
    public function notify() {
    	$postStr = $GLOBALS["HTTP_RAW_POST_DATA"];
    	$param = (array)simplexml_load_string($postStr, 'SimpleXMLElement', LIBXML_NOCDATA);
    	$receive_sign = $param['sign'];
    	$receive_data = $this->filterParameter($param);
    	if ($receive_data) {
    		    //查询订单
    			$input = new \Wechat\Pay\lib\WxPayOrderQuery();
    			$input->SetTransaction_id($param['transaction_id']);
    			F('result3',$input,CONF_PATH);
    			$r = new \Wechat\Pay\lib\WxPayApi();
          $result = $r->orderQuery($input);
    			if(array_key_exists("return_code", $result)
    				&& array_key_exists("result_code", $result)
    				&& $result["return_code"] == "SUCCESS"
    				&& $result["result_code"] == "SUCCESS")
    			{
    				$this->pay_success($result);
    				return true;
    			}
    			return false;

		} else {
			return false;
		}   	
    }
    
    
    /**
     * 充值入账 废弃以前的不生效 移动过来的
     * @param $result
     */

    	public function pay_success($param) {
    		if (array_key_exists("return_code", $param)
    				&& array_key_exists("result_code", $param)
    				&& $param["return_code"] == "SUCCESS"
    				&& $param["result_code"] == "SUCCESS") {
    			$sqlmap = array();
    			$sqlmap['trade_sn'] = $param['out_trade_no'];
    			$order_info = model("pay_order")->where($sqlmap)->find();
    			if ($order_info && $order_info['status'] != 1 && (int) $order_info['userid'] > 0) {
    				/* 更改订单状态 */
    				model("pay_order")->where($sqlmap)->setField(array('status'=>'1','buyer_email'=>$param['buyer_email'],'notify_time'=>NOW_TIME));
    				/* 写入账户明细 */
    				$total = $order_info['total_fee'];
    				$sign = '4-4-'.$order_info['userid'].'-'.$total.'-'.$order_info['trade_sn'];
    				$rs = model('member_finance_log')->where(array('only'=>$sign))->find();
    				if(!$rs){
    				    action_finance_log($order_info['userid'],$total,'money','用户：'.$order_info['userid'].'微信支付在线快速充值',$sign,array('order_id'=>$order_info['trade_sn']));
    				    return true;
    				}else{
                       
                       return false;
    				}
    			}
    		}

    		return false;
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
	private function filterParameter($parameter){
		$para = array();
		foreach ($parameter as $key => $value)
		{
			if ('sign' == $key || 'sign_type' == $key || '' == $value || 'm' == $key  || 'a' == $key  || 'c' == $key   || 'code' == $key || 'method' == $key) continue;
			else $para[$key] = $value;
		}
		return $para;
	}

	/**
	 * 
	 * 生成直接支付url，支付url有效期为2小时,模式二
	 * @param UnifiedOrderInput $input
	 */
	public function GetPayUrl($input){
          
		if($input->GetTrade_type() == "NATIVE"){
		    $WxPayApi =	new \Wechat\Pay\lib\WxPayApi();
			$result = $WxPayApi->unifiedOrder($input);
			return $result;
		}
	}
}
?>