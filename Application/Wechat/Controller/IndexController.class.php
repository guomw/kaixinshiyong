<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Wechat\Controller;
class IndexController extends \Common\Controller\BaseController
{
	public function _initialize() {
		parent::_initialize();
	}
    
	public function bind() {
        $param = I('param.');
        if(!isset($param['openid']) || empty($param['openid'])) {
            $this->error('微信用户验证失败');
        }
        $sqlmap = array();
        $sqlmap['type'] = 'wechat';
        $sqlmap['openid'] = $param['openid'];
        $uid = model('member_oauth')->where($sqlmap)->getField('uid');
        if($uid > 0) {
            $this->error('您已绑定该微信帐号，不能重复绑定。');
        }
        if(IS_POST) {
            if(!$param['account']) {
                $this->error('帐号不能为空');
            }
            if(!$param['password']) {
                $this->error('密码不能为空');
            }
            $param['username'] = $param['account'];
            $MemberLogic = D('Member/Member', 'Logic');
            $result = $MemberLogic->login($param);
            if(!$result) {
                $this->error($MemberLogic->getError());
            } else {
                if(cookie('_modelid') != 1) {
                    $this->error('您是商家用户，暂不支持微信绑定');
                }
//                cookie('_userid', NULL);
//                cookie('_groupid', NULL);
//                cookie('_modelid', NULL);
                runhook('wechat_account_bind', $param);
                $this->success('微信帐号绑定成功', __APP__);
            }
        } else {
            $SEO = seo(0, '绑定微信帐号');
            include template('bind');
        }
	}
}