<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Oauth\Controller;
class IndexController extends \Common\Controller\BaseController {    
    public function _initialize() {
        parent::_initialize();
        $this->member_db = model('member/member');
    }
    
    /* 登陆接口 */
    public function login() {
        $factory = new \Oauth\Factory\oauth();
        $login_url = $factory->login();

        if(!$login_url) {
            $this->error($factory->getError());
        } else {
            redirect($login_url);
        }
    }
    
    /* 回调接口 */
    public function callback() {
        $param = I('param.');
        $factory = new \Oauth\Factory\oauth();
        $token = $factory->access_token($param['code']);
        $openid = $factory->get_openid($token['access_token']);
        $MemberLogic = D('Member/Member', 'Logic');
        $openid = $openid ? $openid:$param['openid'];
        /* 判断是否已绑定 */
        $oauth = model('member_oauth')->where(array('openid' => $openid))->find();

        if($oauth && $oauth['uid'] > 0) {
            $MemberLogic->publogin($oauth['uid']);
            $this->success('登录成功', "/index.php");
        } else {
            $userinfo = $factory->get_user_info($openid, $token['access_token']);
            $user = $factory->get_user_info($openid, $token['access_token']);
            $token['openid'] = $openid;
            $token_code = authcode(serialize($token), 'ENCODE');
            $SEO = seo(0, '帐号绑定');
            include template('bind');
        }
    }
    
    public function register() {
        if(IS_POST) {         
            $info = $_POST['register'];
            $info['modelid'] = 1;
            $info['encrypt'] = random(6);
            $token = unserialize(authcode($info['token']));
            $userid = $this->member_db->update($info);
            if(!$userid) {
                $this->error($this->member_db->getError());
            } else {
                $token['uid'] = $userid;
                model('member_oauth')->add($token);
                $MemberLogic = D('Member/Member', 'Logic');
                $MemberLogic->publogin($userid);
                include template('register_success', 'member');
            }
        } else {
            $this->error('请勿非法访问');
        }
    }
    
    /**
     * 绑定帐号
     */
    public function bind() {
        if(IS_POST) {
            $info = $_POST['login'];
            $sqlmap = array();
            $sqlmap['email|phone'] = $info['account'];
            $userinfo = $this->member_db->where($sqlmap)->find();            
            if ($userinfo['password'] == md5(md5($info['password'].$userinfo['encrypt'])) && $userinfo['status'] == 1) {
                $token = unserialize(authcode($info['token']));
                $token['uid'] = $userinfo['userid'];
                model('member_oauth')->add($token);
                $MemberLogic = D('Member/Member', 'Logic');
                $MemberLogic->publogin($userinfo['userid']);
                $this->success('账户绑定成功', __APP__);
            } else {
                $this->error('账户绑定失败');
            }
        } else {
            $this->error('请勿非法访问');
        }
    }
    
    /**
     * 授权令牌加密
     * @param type $token_code
     */
    public function quick($token_code = '') {
        $info['modelid'] = 1;
        $info['email'] = random(8).'@qq.com';
        $info['password'] = $info['pwdconfirm'] = random(6);
        $info['encrypt'] = random(6);
        $info['nickname'] = $_GET['nickname'];
        $userid = $this->member_db->update($info);
        if(!$userid) {
            $this->error($this->member_db->getError());
        } else {
            $token = unserialize(authcode($token_code));
            $token['uid'] = $userid;
            model('member_oauth')->add($token);
            $MemberLogic = D('Member/Member', 'Logic');
            $MemberLogic->publogin($userid);
            include template('register_success', 'member');
        }
    }
}
