<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Oauth\Library\Driver;
class qqconnect extends \Oauth\Library\OauthInterface {
	public $api_url      = 'https://graph.qq.com/';
	public $appid        = '';
	public $appkey       = '';
	public $access_token = '';
	public $callback_url = '';
    
    public function getInstance() {
        $this->appid = $this->config['appid'];
        $this->appkey = $this->config['appkey'];

        //U('Oauth/Index/callback', array('type' => 'qqconnect'), TRUE, TRUE);
        $this->callback_url = "http://".$_SERVER['HTTP_HOST']."/qqlogin/";
        $this->access_token = '';
    }

    /**
     * 登陆跳转
     * @param string $scope 允许接口
     */
    public function login($scope = '') {
		$params=array(
			'client_id'     => $this->appid,
			'redirect_uri'  => $this->callback_url,
			'response_type' => 'code',
			'scope'         => $scope
		);
		return 'https://graph.qq.com/oauth2.0/authorize?'.http_build_query($params);
    }
    
    /**
     * 获取授权令牌
     * @param string $code
     * @param string $callback_url
     * @return array
     */
	public function access_token($code = '', $callback_url = '') {
		$params=array(
			'grant_type'    => 'authorization_code',
			'client_id'     => $this->appid,
			'client_secret' => $this->appkey,
			'code'          => $code,
			'state'         => '',
			'redirect_uri'  => $this->callback_url
		);
		$url='https://graph.qq.com/oauth2.0/token?'.http_build_query($params);
		$result = $this->_http($url);
		$json = array();
		if($result != '') parse_str($result, $json);
        if(!$json['access_token']) {
            $this->error = '获取授权令牌失败';
            return FALSE;
        } else {
            $json['type'] = 'qqconnect';
            $json['access_time'] = NOW_TIME;
            $json['expires_time'] = $json['access_time'] + $json['expires_in'];
            return $json;
        }
	}
    
	/**
     * 获取当前令牌的openid
     * @param type $access_token
     * @return type
     */
	public function get_openid($access_token = ''){
		$params=array(
			'access_token' => $access_token
		);
		$url='https://graph.qq.com/oauth2.0/me?'.http_build_query($params);
		$result_str = $this->_http($url);
		$json_r = array();
		if($result_str != ''){
			preg_match('/callback\(\s+(.*?)\s+\)/i', $result_str, $result_a);
			$json_r=json_decode($result_a[1], true);
		}
        if(!$json_r['openid']) {
            $this->error = 'OpenID 获取失败，请重试';
            return FALSE;
        } else {
            return $json_r['openid'];
        }
	}
    
    /**
     * 获取用户信息
     * @param string $openid       openid
     * @param string $access_token 授权令牌
     * @return array
     */
	public function get_user_info($openid = '', $access_token = ''){
		$params = array(
			'openid'=>$openid
		);
		$result =  $this->api('user/get_user_info', $access_token, $params);
        if($result['ret'] != 0) {
            $this->error = '用户信息获取失败，请重试';
            return FALSE;
        } else {
            return $result;
        }
	}
    
    /**
     * 调用接口
     * @param string    $url            地址
     * @param string    $access_token   令牌
     * @param array     $params         参数
     * @param string    $method         类型
     * 示例：$result = $this->api('user/get_user_info', array('openid'=>$openid), 'GET');
     */
	public function api($url = '', $access_token = '', $params=array(), $method='GET'){
		$url = $this->api_url.$url;
		$params['access_token'] = $access_token;
		$params['oauth_consumer_key']=$this->appid;
		$params['format']='json';
		if($method=='GET'){
			$result_str = $this->_http($url.'?'.http_build_query($params));
		}else{
			$result_str = $this->_http($url, http_build_query($params), 'POST');
		}
		$result=array();
		if($result_str!='') $result=json_decode($result_str, true);
		return $result;
	}    
    
	private function _http($url, $postfields='', $method='GET', $headers=array()){
		$ci=curl_init();
		curl_setopt($ci, CURLOPT_SSL_VERIFYPEER, FALSE); 
		curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ci, CURLOPT_CONNECTTIMEOUT, 30);
		curl_setopt($ci, CURLOPT_TIMEOUT, 30);
		if($method=='POST'){
			curl_setopt($ci, CURLOPT_POST, TRUE);
			if($postfields!='')curl_setopt($ci, CURLOPT_POSTFIELDS, $postfields);
		}
		$headers[]='User-Agent: QQ.PHP(piscdong.com)';
		curl_setopt($ci, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ci, CURLOPT_URL, $url);
		$response=curl_exec($ci);
		curl_close($ci);
		return $response;
	}    
    
}
