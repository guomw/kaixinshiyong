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
    $modules[$i]['name']    = '财付通';   
    $modules[$i]['desc']    = '财付通（Tenpay）是腾讯公司于2005年9月正式推出专业在线支付平台，其核心业务是帮助在互联网上进行交易的双方完成支付和收款。致力于为互联网用户和企业提供安全、便捷、专业的在线支付服务。';
    $modules[$i]['is_cod']  = '0';
    $modules[$i]['is_online']  = '1';
    $modules[$i]['author']  = 'TPCMS开发团队';
    $modules[$i]['website'] = 'http://www.dealswill.com';
    $modules[$i]['version'] = '1.0.0';
    $modules[$i]['logo'] = '/static/images/pay/cft.jpg';
    $modules[$i]['config']  = array(
    	   array('name' => 'partner','type' => 'text','value' => ''),
    	   array('name' => 'key','type' => 'text','value' => ''),
    );
    return;
}
class Tenpay extends Pay  {
	public function __construct($config = array(),$product_info) {	
    if (!empty($config)) $this->set_config($config);
      if ($this->config['service_type']==1) $this->config['service'] = 'trade_create_by_buyer';
    elseif($this->config['service_type']==2) $this->config['service'] = 'create_direct_pay_by_user';
        else $this->config['service'] = 'create_partner_trade_by_buyer';  
        
    $this->config['gateway_url'] = 'https://gw.tenpay.com/gateway/pay.htm?';
    $this->config['gateway_method'] = 'POST';
    $this->config['notify_url'] = return_url('alipay',1);
    $this->config['return_url'] = return_url('alipay');
	}

    
    /*获取付款订单详细信息*/
    public function getpreparedata() {		
      $partner = $this->config['partner'];                  //财付通商户号
      $key =  $this->config['key']; //财付通密钥
      $return_url = "http://".$_SERVER['HTTP_HOST']."/Tenpay_api.php";      //显示支付结果页面,*替换成payReturnUrl.php所在路径
      $notify_url = "http://".$_SERVER['HTTP_HOST']."/Tenpay_api.php";;      //支付完成后的回调处理页面,*替换成payNotifyUrl.php所在路径

    	// 数字签名
      //---------------------------------------------------------
      //财付通即时到帐支付请求示例，商户按照此文档进行开发即可
      //---------------------------------------------------------
      /* 获取提交的订单号 */
      $out_trade_no = $this->product_info['trade_sn'];
      /* 获取提交的商品名称 */
      $product_name = $this->product_info['name'];
      /* 获取提交的商品价格 */
      $order_price = $this->product_info['total_fee'];
      /* 获取提交的备注信息 */
      $remarkexplain = $this->product_info['name'];
      /* 支付方式 */
      $trade_mode=$_REQUEST["trade_mode"];
      $strDate = date("Ymd");
      $strTime = date("His");
      /* 商品价格（包含运费），以分为单位 */
      $total_fee = $this->product_info['total_fee'] * 100;
      /* 商品名称 */
      $desc = $this->product_info['name'];
      /* 创建支付请求对象 */
      $reqHandler = new \Pay\Api\Tenpay\RequestHandler();
      $reqHandler->init();
      $reqHandler->setKey($key);
      $reqHandler->setGateUrl("https://gw.tenpay.com/gateway/pay.htm");

      //----------------------------------------
      //设置支付参数 
      //----------------------------------------
      $reqHandler->setParameter("partner", $partner);
      $reqHandler->setParameter("out_trade_no", $out_trade_no);
      $reqHandler->setParameter("total_fee", $total_fee);  //总金额
      $reqHandler->setParameter("return_url", $return_url);
      $reqHandler->setParameter("notify_url", $notify_url);
      $reqHandler->setParameter("body", $desc);
      $reqHandler->setParameter("bank_type", "DEFAULT");      //银行类型，默认为财付通
      //用户ip
      $reqHandler->setParameter("spbill_create_ip", $_SERVER['REMOTE_ADDR']);//客户端IP
      $reqHandler->setParameter("fee_type", "1");               //币种
      $reqHandler->setParameter("subject",$desc);          //商品名称，（中介交易时必填）

      //系统可选参数
      $reqHandler->setParameter("sign_type", "MD5");        //签名方式，默认为MD5，可选RSA
      $reqHandler->setParameter("service_version", "1.0");    //接口版本号
      $reqHandler->setParameter("input_charset", "utf-8");      //字符集
      $reqHandler->setParameter("sign_key_index", "1");       //密钥序号
      //业务可选参数
      $reqHandler->setParameter("attach", "");                //附件数据，原样返回就可以了
      $reqHandler->setParameter("product_fee", "");           //商品费用
      $reqHandler->setParameter("transport_fee", "0");          //物流费用
      $reqHandler->setParameter("time_start", date("YmdHis"));  //订单生成时间
      $reqHandler->setParameter("time_expire", "");             //订单失效时间
      $reqHandler->setParameter("buyer_id", "");                //买方财付通帐号
      $reqHandler->setParameter("goods_tag", "");               //商品标记
      $reqHandler->setParameter("trade_mode",$trade_mode);              //交易模式（1.即时到帐模式，2.中介担保模式，3.后台选择（卖家进入支付中心列表选择））
      $reqHandler->setParameter("transport_desc","");              //物流说明
      $reqHandler->setParameter("trans_type","1");              //交易类型
      $reqHandler->setParameter("agentid","");                  //平台ID
      $reqHandler->setParameter("agent_type","");               //代理模式（0.无代理，1.表示卡易售模式，2.表示网店模式）
      $reqHandler->setParameter("seller_id","");                //卖家的商户号

      //请求的URL
      $reqUrl = $reqHandler->getRequestURL();    	
      return $reqHandler->getAllParameters();
    }


    /**
	 * POST接收数据
	 * 状态码说明  （0 交易完成 1 待付款 2 待发货 3 待收货 4 交易关闭 5交易取消
	 */
    public function notify() {
          $partner = $this->config['partner'];                  //财付通商户号
          $key =  $this->config['key'];
          /* 创建支付应答对象 */
          $resHandler = new \Pay\Api\Tenpay\ResponseHandler();
          $resHandler->setKey($key);

          //判断签名
          if($resHandler->isTenpaySign()) {
          //通知id
          $notify_id = $resHandler->getParameter("notify_id");
        
          //通过通知ID查询，确保通知来至财付通
          //创建查询请求
          $queryReq = new \Pay\Api\Tenpay\RequestHandler();
          $queryReq->init();
          $queryReq->setKey($key);
          $queryReq->setGateUrl("https://gw.tenpay.com/gateway/simpleverifynotifyid.xml");
          $queryReq->setParameter("partner", $partner);
          $queryReq->setParameter("notify_id", $notify_id);
          
          //通信对象
          $httpClient = new \Pay\Api\Tenpay\client\TenpayHttpClient();
          $httpClient->setTimeOut(5);
          //设置请求内容
          $httpClient->setReqContent($queryReq->getRequestURL());
        
          //后台调用
          if($httpClient->call()) {
          //设置结果参数
            $queryRes = new \Pay\Api\Tenpay\client\ClientResponseHandler();
            $queryRes->setContent($httpClient->getResContent());
            $queryRes->setKey($key);
          
          if($resHandler->getParameter("trade_mode") == "1"){
          //判断签名及结果（即时到帐）
          //只有签名正确,retcode为0，trade_state为0才是支付成功
          if($queryRes->isTenpaySign() && $queryRes->getParameter("retcode") == "0" && $resHandler->getParameter("trade_state") == "0") {
              //取结果参数做业务处理
              $out_trade_no = $resHandler->getParameter("out_trade_no");
            //财付通订单号
              $transaction_id = $resHandler->getParameter("transaction_id");
            //金额,以分为单位
              $total_fee = $resHandler->getParameter("total_fee");
             //如果有使用折扣券，discount有值，total_fee+discount=原请求的total_fee
              $discount = $resHandler->getParameter("discount");
              
              //------------------------------
              //处理业务开始
              //------------------------------

              $info = $resHandler->getAllParameters();

              F('info',$info,CONF_PATH);
              if($this->pay_success($info)){
                echo "success";
                return true;
              }else{
                echo "fail";
                return false;
              }

              //处理数据库逻辑
              //注意交易单不要重复处理
              //注意判断返回金额
              
              //------------------------------
              //处理业务完毕
              //------------------------------
              
              
            } else {
               echo "fail";
               return false;
            }
          }else{
          //通信失败
          echo "fail";
          return false;
         } 
    
         } else {
          echo "<br/>" . "认证签名失败" . "<br/>";
          echo $resHandler->getDebugInfo() . "<br>";
          return false;
      }  	
    }
    
 }   
    /**
     * 充值入账 废弃以前的不生效 移动过来的
     * @param $result
     */
    	public function pay_success($param) {
    		if ($param['trade_state'] == 0) {
    			$sqlmap = array();
    			$sqlmap['trade_sn'] = $param['out_trade_no'];
    			$order_info = model("pay_order")->where($sqlmap)->find();
          F('order1',$order_info,CONF_PATH);
    			if ($order_info && $order_info['status'] != 1 && (int) $order_info['userid'] > 0) {
    				/* 更改订单状态 */
    				model("pay_order")->where($sqlmap)->setField(array('status'=>'1','buyer_email'=>$param['buyer_email'],'notify_time'=>NOW_TIME));
    				/* 写入账户明细 */
    				$total = $order_info['total_fee'];
    				$sign = '4-4-'.$order_info['userid'].'-'.$total.'-'.$order_info['trade_sn'];
    				$rs = model('member_finance_log')->where(array('only'=>$sign))->find();
    				if(!$rs){
    				    action_finance_log($order_info['userid'],$total,'money','用户：'.$order_info['userid'].'财付通在线快速充值',$sign,array('order_id'=>$order_info['trade_sn']));
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