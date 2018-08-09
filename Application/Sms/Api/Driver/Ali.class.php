<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Sms\Api\Driver;
class Ali {
	/* 发送短信 */
	public static function send($mobile,$extr = array()) {
        $setting = getcache('setting', 'sms');
		 if(empty($setting)){
            return '请配置短信接口';
        }
      

		$result = sms($mobile,$extr);
		
        if ($result['success'] == 'true') {
            return TRUE;
        } else {
            return $result['msg'];

        }
	}

	/* 查询余额 */
	public static function get_num() {
		/*self::$config['action'] = 'getsms';
		return _dfsockopen('http://220.194.54.161:8060/index.aspx?'.http_build_query(self::$config));*/
	}
}
?>