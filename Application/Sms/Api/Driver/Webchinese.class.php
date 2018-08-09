<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Sms\Api\Driver;
class Webchinese {
	private static $config    =   '';
    private static $setting   = '';
	public static function getInstance() {
        self::$setting = getcache('setting', 'sms');
		self::$config = array(
			'Uid' 	=> self::$setting['username'],
			'Key'	=> self::$setting['password']
		);
	}

	/* 发送短信 */
	public static function send($mobile, $content) {
        if(empty(self::$config)){
            self::getInstance();
        }
        self::$config['smsMob'] = $mobile;
        self::$config['smsText'] = $content;
		$result = _dfsockopen('http://utf8.sms.webchinese.cn/?'.http_build_query(self::$config));
        if ($result > 0) {
            return TRUE;
        } else {
            switch ($result) {
                case '-1':
                    return '没有该用户账户';
                    break;
                case '-2':
                    return '接口密钥不正确';
                    break;
                case '-3':
                    return '短信数量不足';
                    break;
                case '-4':
                    return '手机号格式不正确';
                    break;
                case '-6':
                    return 'IP限制';
                    break;
                case '-11':
                    return '该用户被禁用';
                    break;
                case '-14':
                    return '短信内容出现非法字符';
                    break;
                case '-21':
                    return 'MD5接口密钥加密不正确';
                    break;
                case '-41':
                    return '手机号码为空';
                    break;
                case '-42':
                    return '短信内容为空';
                    break;
                case '-51':
                    return '短信签名格式不正确';
                    break;
                default:
                    return '短信发送失败';
                    break;
            }
            return FALSE;
        }
	}

	/* 查询余额 */
	public static function get_num() {
        if(empty(self::$config)){
            self::getInstance();
        }
		self::$config['Action'] = 'SMS_Num';
		return _dfsockopen('http://sms.webchinese.cn/web_api/SMS/?'.http_build_query(self::$config));
	}
}
?>