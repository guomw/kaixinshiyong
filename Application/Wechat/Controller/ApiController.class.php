<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Wechat\Controller;
class ApiController extends \Common\Controller\BaseController
{
    protected $error = '';
    protected $menus = array();
    protected $replace = array();

    public function _initialize() {
        parent::_initialize();
        $setting = getcache('setting', 'wechat');
        if($setting['enable'] != 1) die;

        /* 优先读取移动端地址 */
        $wap_config = getcache('setting', 'wap');
        $site_url = ($wap_config['wap_enable'] == 1 && $wap_config['wap_domain']) ? $wap_config['wap_domain'] : (is_ssl() ? 'https://' : 'http://').$_SERVER['HTTP_HOST'].__ROOT__;

        $this->options = $setting['options'];
        $buttons = $setting['menu']['button'];

        if($buttons) {
            foreach ($buttons as $key => $value) {
                if($value['status'] == 0) unset($buttons[$key]);
                if($value['sub_button']) {
                    foreach ($value['sub_button'] as $k => $v) {
                        if($v['status'] == 0) unset($buttons[$key]['sub_button'][$k]);
                        unset($buttons[$key]['sub_button'][$k]['status']);
                    }
                }
                unset($buttons[$key]['status']);
            }
        }

        if($buttons) {
            $this->menus = array('button' => $buttons);
        }

        $this->replace = array(
            '{site_name}' => C('WEBNAME'),
            '{site_url}'  => $site_url,
            '{web_url}' => 'http://'.$_SERVER['HTTP_HOST'],
        );
    }

	public function index() {
		$wechat = new \Wechat\Library\Wechat($this->options);
		$wechat->valid();
		$type = $wechat->getRev()->getRevType();
        $access_token = $wechat->checkAuth();
        $openid = $wechat->getRevFrom();
        $this->replace['{openid}'] = $openid;
        if(!$openid) die;
        echo 'success';
		switch($type) {
			case $wechat::MSGTYPE_TEXT:				
                $factory = new \Wechat\Library\factory();
                $msg = $factory->wechat_product_search($wechat->getRevContent());
                $wechat->news($msg)->reply(); 
				break;
			case $wechat::MSGTYPE_EVENT:
                $RevEvent = $wechat->getRevEvent();
                $key = $RevEvent['key'];
                /* 需要登录授权 */
                if(preg_match('/^wechat_(order|account)_((?!bind).)*/', $key)) {
                    $uid = $this->getBindInfo($openid);
                    if(!$uid) {
                        $wechat->text($this->dreplace(C('WECHAT_ACCOUNT_NBIND_TPL')))->reply();
                        exit();
                    }
                }
                $userinfo = $this->getUserInfo($uid);                
                if(!$userinfo && $this->error) {
                    $wechat->text($this->error)->reply();
                }
                $this->replace['{nickname}'] = $userinfo['nickname'];
                $factory = new \Wechat\Library\factory();
                $factory->userid = (int) $uid;
                $factory->params = array('openid' => $openid, '_form' => 'wechat');
                if(method_exists($factory, $key)) {
                    $msg = $factory->$key();
                    if(empty($msg)) die;
                    if(is_array($msg)) {
                        $wechat->news($msg)->reply();
                    } else {
                        $wechat->text($this->dreplace($msg))->reply();
                    }
                }
                break;
			case $wechat::MSGTYPE_IMAGE:
				break;
			default:
                break;
		}
	}
    
    private function dreplace($subject, $array = array()) {
        $array = (empty($array)) ? $this->replace : array_merge($this->replace, $array);
        return str_replace(array_keys($array), $array, $subject);
    }


    /**
     * 检测绑定信息
     * @param type $openid
     */
    private function getBindInfo($openid = '') {
        if(!$openid) return FALSE;
        $sqlmap = array();
        $sqlmap['type'] = 'wechat';
        $sqlmap['openid'] = $openid;
        $uid = (int) model('member_oauth')->where($sqlmap)->getField('uid');
        return $uid;
    }


    /**
     * 获取用户信息
     * @param (int) $uid 会员ID
     * @return boolean
     */
    private function getUserInfo($uid = '') {
        $userinfo = getUserInfo($uid);
        if(!$userinfo) {
            $this->error = '';
            return FALSE;
        }
        if($userinfo['modelid'] != 1) {
            $this->error = '目前微信客户端仅对买家开放';
            return FALSE;
        }
        return $userinfo;
    }
}