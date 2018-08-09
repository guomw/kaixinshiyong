<?php
/**
 * 
 * 微信支付API异常类
 * @author widyhu
 *
 */
namespace Wechat\Pay\lib;
use \Think\Exception;
class WxPayException extends \Think\Exception {
	public function errorMessage()
	{
		return $this->getMessage();
	}
}
