<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Sms\Api;
class SmsApi {
    private static $setting    =   '';
    private static $handler    =   '';
    private static $type    =   '';


    /* 构造方法 */
    public static function init($type){
        self::$setting = self::getSetting();
        extract(self::$setting);
        $type = (empty($type)) ? 'Webchinese' : self::$setting['type'];  
        $class  =   strpos($type,'\\') ? $type : 'Sms\\Api\\Driver\\'. ucwords(strtolower($type));
        self::$handler  =    $class;
        self::$type  =    $type;
        if (!class_exists(self::$handler)) {
            return '接口不存在';
        }
    }

    /**
     * 发送短消息
     * @author   <jason>
     */
    public static function send($mobile,$content,$param = array()) {
        $arr = $param;
        if(empty(self::$handler)){
            self::init();
        }



        if (empty($mobile) || empty($content)) {
            return '参数错误';
        }
        if (is_array($mobile) && !empty($mobile)) {
            $mobile = implode(",", $mobile);
        }

       
        $class  =   self::$handler;
        $type = self::$type;

        if ($type == 'Webchinese') {
          
           return $class::send($mobile,$content);  
        }else{
            return $class::send($mobile,$arr);  
        }
        

              
    }

    /* 短信检测接口 */
    public static function check_sms($mobile, $id_code, $type, $isupdate = TRUE) {
    }

    public static function get_num() {
        if(empty(self::$handler)){
           self::init();
        }
        $class  =   self::$handler;
        return $class::get_num();
    }

    public static function getError() {
        return $this->error;
    }

    private static function getSetting() {
        self::$setting = getcache('setting', 'sms');
        return self::$setting;
    }

}