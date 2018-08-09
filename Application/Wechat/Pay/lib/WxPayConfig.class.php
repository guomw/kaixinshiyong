<?php
/**
* 	配置账号信息
*/
namespace Wechat\Pay\lib;
use \Common\Controller\BaseController;
$alipay = getcache('weixin',pay);
define('APPID1',$alipay['APPID']);
define('MCHID1',$alipay['MCHID']);
define('KEY1',$alipay['KEY']);
define('APPSECRET1',$alipay['APPSECRET']);
class WxPayConfig extends BaseController
{
    const APPID = APPID1;
	const MCHID = MCHID1;
	const KEY = KEY1;
	const APPSECRET = APPSECRET1;
	const SSLCERT_PATH = '../cert/apiclient_cert.pem';
	const SSLKEY_PATH = '../cert/apiclient_key.pem';
	const CURL_PROXY_HOST = "0.0.0.0";//"10.152.18.220";
	const CURL_PROXY_PORT = 0;//8080;
	const REPORT_LEVENL = 1;
	const NOTIFY_URL = 'http://fanli. .cn/api.php';

}
