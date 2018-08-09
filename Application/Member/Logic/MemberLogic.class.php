<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Member\Logic;
use Think\Model;
if (!defined('MODULE_CACHE')) define('MODULE_CACHE', DATA_PATH.'caches_model/');
/* 业务逻辑分层 */
class MemberLogic extends Model {
    public function __construct() {
        $this->db = model('member');
        $this->error = '';
        $this->models = getcache('model', 'commons');
        $this->settings = getcache('setting', 'member');
    }

    /* 注册一个新用户 */
    public function register($info) {
        $settings = $this->settings;        
        $info['modelid'] = (int) $info['modelid'];        
        if($info['modelid'] < 1 || !isset($this->models[$info['modelid']])) {
            $this->error = '参数错误，请勿非法提交';
            return FALSE;
        }
        $modelid = $info['modelid'];
        $model_name = $this->models[$info['modelid']]['name'];        
        /* 是否开启注册 */
        if(!in_array($info['modelid'], $settings['setting_register_enable']) && !defined('IN_ADMIN')) {
            $this->error = '当前未开启'.$model_name.'注册';
            return FALSE;
        }
        
        /* 1、检查验证码开启状态 */
      
        if(isset($info['verify'])){
        if (in_array($modelid, $settings['setting_register_verify_enable']) && !checkVerify(strtolower($info['verify'])) && !defined('IN_ADMIN')) {
            $this->error = '验证码不正确';
            return FALSE;            
        } 
        }
        
        /* 2、检测手机验证码 */
        if(in_array($modelid, $settings['setting_register_sms_enable'])) {
            if(!$this->register_check_sms($info['phone'], $info['sms'], TRUE) && !defined('IN_ADMIN')) {
                $this->error = $this->error;
                return FALSE;
            } else {
                /* 定义手机为已验证 */
                $info['phone_status'] = 1;
            }
        }        
        /* 注册默认值 */
        $info['encrypt'] = random(6);
        $info['point'] = (int) 0;
        $info['groupid'] = (isset($info['groupid']) && is_numeric($info['groupid']) && $info['groupid'] > 1) ? $info['groupid'] : 1;

        $userid = $this->db->update($info);
        if (!$userid) {
            $this->error = $this->db->getError();
            return FALSE;
        }
        $this->publogin($userid);
        return $userid;
    }
    
    /**
     * 检测手机验证码
     * @param string $mobile    手机号码
     * @param string $sms       短信验证码
     */
    public function register_check_sms($mobile = '', $sms = '', $isyes = TRUE) {
        if(empty($mobile) || !is_mobile($mobile)) {
            $this->error = '手机号码为空或格式错误';
            return FALSE;
        }
        if(empty($sms)) {
            $this->error = '手机短信验证码不能为空';
            return FALSE;
        }
        $sqlmap = array();
        $sqlmap['mobile'] = $mobile;
        $sqlmap['id_code'] = $sms;
        $sqlmap['status'] = 0;
        if(model('sms_report')->where($sqlmap)->count() < 1) {
            $this->error = '验证码输入错误';
            return FALSE;
        }
        if($isyes === TRUE) {
            model('sms_report')->where($sqlmap)->setField('status', 1);
        }
        return TRUE;
    }


    
    public function login($info) {
        $setting = getcache('setting', 'member');
//         if ($info['verify'] != strtolower(session('verify')) && $info['needverify'] != 1) {
//            $this->error = '验证码不正确';
//            return FALSE;
//         }
        $username = htmlspecialchars(trim($info['username']));
        $password = htmlspecialchars(trim($info['password']));
        $sqlmap = array();
        $sqlmap['email|phone'] = $username;
        $userinfo = $this->db->where($sqlmap)->find();

        $setting2 = getcache('setting', 'wap');
        $http_host = $_SERVER['HTTP_HOST'];
        $wap_domain = ltrim($setting2['wap_domain'], "'http://'");
        $detect = new \Wap\Library\Mobile_Detect();
        if ($detect->isMobile() || stripos($http_host, $wap_domain) !== FALSE || cookie('ismobile') == 1) {
          //  if(C('system_auth_type') != 'professional') die('该功能仅专业版使用');
            if($userinfo['modelid'] == 2){
                $this->error = '商家会员不能在手机端登录，请在电脑上操作';
                return FALSE;
            }
        }

        if ($userinfo) {
            if ($userinfo['status'] != 1  ) {
                $this->error = '您的账户尚未通过审核';
                return FALSE;
            } elseif ($userinfo['islock'] == 1  ) {
                $this->error = '您的账户被系统锁定，禁止登录';
                return FALSE;
            } elseif ($userinfo['password'] != md5(md5($password.$userinfo['encrypt']))) {
                $this->error = '用户名或密码错误';
                return FALSE;
            } else {
          
                $this->publogin($userinfo['userid']);
                return TRUE;
            }
        } else {
            $this->error = '用户名或密码错误';
            return FALSE;            
        }
    }
    
    public function publogin($uid = 0) {
        $userinfo = $this->db->find($uid);
        cookie('_userid', $userinfo['userid'], 86400);
        cookie('_groupid', $userinfo['groupid'], 86400);
        cookie('_modelid', $userinfo['modelid'], 86400);
        $this->db->update(array('userid' => $userinfo['userid'], 'lastdate' => NOW_TIME, 'lastip' => get_client_ip(),'loginnum' => $userinfo['loginnum']+ 1),false);
       
        return TRUE;
    }

    public function getError() {
        return $this->error;
    }


    /* 分步骤：注册一个新用户 */
    public function register_($info) {
        $settings = $this->settings;        
        $info['modelid'] = (int) $info['modelid'];        
        if($info['modelid'] < 1 || !isset($this->models[$info['modelid']])) {
            $this->error = '参数错误，请勿非法提交';
            return FALSE;
        }
        $modelid = $info['modelid'];
        $model_name = $this->models[$info['modelid']]['name'];        
        /* 是否开启注册 */
        if(!in_array($info['modelid'], $settings['setting_register_enable']) && !defined('IN_ADMIN')) {
            $this->error = '当前未开启'.$model_name.'注册';
            return FALSE;
        }
        
        /* 1、检查验证码开启状态 */
        if (in_array($modelid, $settings['setting_register_verify_enable']) && !checkVerify(strtolower($info['verify'])) && !defined('IN_ADMIN')) {
            $this->error = '验证码不正确';
            return FALSE;            
        } 
        
        /* 2、检测手机验证码 */
        // if(in_array($modelid, $settings['setting_register_sms_enable'])) {
        //     if(!$this->register_check_sms($info['phone'], $info['sms'], TRUE) && !defined('IN_ADMIN')) {
        //         $this->error = $this->error;
        //         return FALSE;
        //     } else {
        //         /* 定义手机为已验证 */
        //         $info['phone_status'] = 1;
        //     }
        // }
        /* 注册默认值 */
        $info['encrypt'] = random(6);
        $info['point'] = (int) 0;
        $info['groupid'] = (isset($info['groupid']) && is_numeric($info['groupid']) && $info['groupid'] > 1) ? $info['groupid'] : 1;

        $userid = $this->db->update($info);
        if (!$userid) {
            $this->error = $this->db->getError();
            return FALSE;
        }
        $this->publogin($userid);
        return $userid;
    }
}