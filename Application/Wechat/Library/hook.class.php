<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Wechat\Library;
class hook
{
    protected $options = array();
    protected $notify = array();
    public function __construct() {
        $setting = getcache('setting', 'wechat');
        if($setting['enable'] != 1) return FALSE;
        $this->options = $setting['options'];
        $this->notify = $setting['notify'];
    }

    public function system_init() {
        $param = I('param.');

        if($param['_form'] == 'wechat' && !empty($param['openid'])) {
            $uid = model('member_oauth')->where(array('type' => 'wechat', 'openid' => $param['openid']))->getField('uid');
            if($uid > 0 && $uid !== (int) cookie('_userid')) {
                $MemberLogic = D('Member/Member', 'Logic');
                $MemberLogic->publogin($uid);
            }
        }
    }
    
    /* 微信帐号绑定 */
    public function wechat_account_bind(&$param) {
        if(!$this->notify['account_bind']['enabled'] || !$this->notify['account_bind']['template_id']) return FALSE;
        $wechat = new \Wechat\Library\Wechat($this->options);
        $userinfo = $wechat->getUserInfo($param['openid']);
        $message = array(
            'touser' => $param['openid'],
            'template_id' => $this->notify['account_bind']['template_id'],
            'url' => U('Member/Index/Index', '', '', TRUE),
            'topcolor' => '#FF0000',
            'data' => array(
                //头部信息
                'first' => array(
                    'value' => '微信账号绑定成功，欢迎您的到来~'
                ),
                //微信ID
                'keyword1' => array(
                    'value' => $userinfo['nickname']
                ),
                //绑定帐号
                'keyword2' => array(
                    'value' => $param['account']
                ),
                //开通功能
                'keyword3' => array(
                    'value' => '微信客户端所有功能'
                ),
                'remark' => array(
                    'value' => '点击查看我的个人中心'
                ),
            )
        );
        $wechat->sendTemplateMessage($message);
        //-----------写入绑定数据表-----------
        $info = array();
        $info['uid'] = cookie("_userid");
        $info['openid'] = $param['openid'];
        $info['type'] = 'wechat';
        if(!model('member_oauth')->where($info)->count()) {
            model('member_oauth')->add($info);
        }
    }
    /**
     * 提现审核处理结果
     * userid : 会员ID
     * money  : 提现金额    
     * result : 结果(成功/失败)
     * paypal : 提现方式(1:普通;2:快速)
     */
	public function pay_cash_check(&$param) {
        if(!$this->notify['pay_cash_check']['enabled'] || !$this->notify['pay_cash_check']['template_id']) return FALSE;
        $rs = model('cash_records')->find($param['id']);
        $openid = model('member_oauth')->where(array('uid' => $param['userid'], 'type' => 'wechat'))->getField('openid');
        if(!$openid || !$rs) return FALSE;
        $nickname = nickname($param['userid']);
        $message = array(
            'touser' => $openid,
            'url' => U('Member/Profile/Index', '', '', TRUE),
            'topcolor' => '#FF0000',
            'template_id' => $this->notify['pay_cash_check']['template_id']
        );        
        $first = $nickname."您好，您提交的".(($param['paypal'] == 1 ? '普通' : '快速'))."提现申请已审核".(($param['result'] == 1) ? '成功' : '失败');        
        if($param['result'] == 0) {
            $first .= '，资金已退回到您的账户余额';
        }
        $first .= '。';
        $remark = ($rs['cause']) ? $rs['cause'] : '有任何疑问，请致电客服。';
        $keyword2 = ($param['paypal'] == 1) ? '普通提现' : '快速提现';
        $keyword4 = ($param['result'] == 1) ? '通过审核' : '审核失败';

        $message['data'] = array(
            'first' => array('value' => $first),
            // 提现金额
            'keyword1' => array('value' => $param['money']),
            // 提现方式
            'keyword2' => array('value' => $keyword2),
            // 申请时间
            'keyword3' => array('value' => dgmdate($rs['inputtime'], 'Y/m/d H:i')),
            // 审核结果
            'keyword4' => array('value' => $keyword4),
            // 审核时间
            'keyword5' => array('value' => dgmdate(NOW_TIME, 'Y/m/d H:i')),
            'remark' => array('value' => $remark)
        );
        $wechat = new \Wechat\Library\Wechat($this->options);
        $wechat->sendTemplateMessage($message);
	}
    
    /*订单审核通知*/
    public function order_check_trade_no(&$param) {
        if(!$this->notify['order_check_trade_no']['enabled'] || !$this->notify['order_check_trade_no']['template_id']) return FALSE;
        $rs = model('order')->find($param['order_id']);
        $openid = model('member_oauth')->where(array('uid' => $rs['buyer_id'], 'type' => 'wechat'))->getField('openid');        
        if(!$openid || !$rs) return FALSE;
        $nickname = nickname($rs['buyer_id']);        
        $message = array(
            'touser' => $openid,
            'template_id' => $this->notify['order_check_trade_no']['template_id'],
            'url' => U('Member/Profile/index', '', '', TRUE),
            'topcolor' => '#FF0000',
        );
        
        $first = $nickname.",您好。";
        $success = $error = '';
        if($param['mod'] == 'rebate') {
            $success .= '您提交的订单号已通过商家审核。请等待商家确认付款！';
            $error .= '您提交的订单号因「'.$param['cause'].'」被商家审核失败。建议您仔细核对再次提交订单号或向平台发起申诉。';
        } else {
            $success .= '恭喜您的试用资格申请已获得商家确认通过，请尽快到商家店铺按活动要求下单,到期将自动取消资格!';
            $error .= '非常抱歉！您提交的试用申请,未通过审核。您可以尝试重新申请或关注其它试用活动。';
        }
        $first .= ($param['result'] == 1) ? $success: $error;
        
        $keyword2 = model('activity_set')->where(array('key' => $param['mod'].'_name'))->getField('value');
        if($param['result'] == 1) {
            $keyword3 = ($param['mod'] == 'rebate') ? '成功待付款' : '已确认资格待下单';
        } else {
            $keyword3 = ($param['mod'] == 'rebate') ? '失败待修改' : '未通过资格审核';
        }
        $keyword4 = model('member_merchant')->where(array('userid' => $rs['seller_id']))->getField('store_name');

        $message['data'] = array(
            'first' => array('value' => $first),
            //更新时间
            'keyword1' => array('value' => dgmdate(NOW_TIME, 'Y/m/d H:i')),
            //订单类型
            'keyword2' => array('value' => $keyword2),
            //订单状态
            'keyword3' => array('value' => $keyword3, 'color' => '#FF0000'),
            //订单来源(店铺名称)
            'keyword4' => array('value' => $keyword4),
            //订单详情
            'keyword5' => array('value' => $title),
            'remark'   => '如有疑问请联系在线客服。'
        );
        $wechat = new \Wechat\Library\Wechat($this->options);
        $wechat->sendTemplateMessage($message);
    }
    
    /* 订单结算通知 */
    public function order_balance(&$param) {
        if(!$this->notify['order_balance']['enabled'] || !$this->notify['order_balance']['template_id']) return FALSE;
        $rs = model('order')->find($param['order_id']);
        $openid = model('member_oauth')->where(array('uid' => $rs['buyer_id'], 'type' => 'wechat'))->getField('openid');
        if(!$openid || !$rs) return FALSE;
        $nickname = nickname($rs['buyer_id']);        
        $message = array(
            'touser' => $openid,
            'template_id' => $this->notify['order_balance']['template_id'],
            'url' => U('Member/Profile/index', '', '', TRUE),
            'topcolor' => '#FF0000',
        );
        $first = $nickname.",您好。";
        $success = '您参与的活动已结算完成！';
        $error = '非常抱歉,您参与的活动审核失败！';
        $act_name = model('activity_set')->where(array('key' => $param['mod'].'_name'))->getField('value');
        $result = $param['result'];
        $first .= ($result == 1) ? $success: $error;
        if($result == 1) {
            $keyword3 = '结算成功';
        } else {
            $keyword3 = '审核失败';
        }
        $message['data'] = array(
            'first' => array('value' => $first),
            //订单号
            'keyword1' => array('value' => $param['order_id']),
            //完成时间
            'keyword2' => array('value' => dgmdate(NOW_TIME, 'Y/m/d H:i')),
            'remark'   => '如有疑问，联系在线客服！'
        );
        $wechat = new \Wechat\Library\Wechat($this->options);
        $wechat->sendTemplateMessage($message);        
    }
    
    /* 申诉处理通知 */
    public function order_appeal_arbitration(&$param) {
        echo '111';
        if(!$this->notify['order_appeal_arbitration']['enabled'] || !$this->notify['order_appeal_arbitration']['template_id']) return FALSE;
        
        $openid = $this->getOpenidByUid($param['userid']);
        var_dump($openid."---");
        if(!$openid) return FALSE;
        $buyer_info = getUserInfo($param['userid']);
        $seller_info = getUserInfo($param['seller_id']);
        if(!$buyer_info && !$seller_info) return FALSE;
        var_dump($param['userid']);
        var_dump($param['seller_id']);

die();
        $message = array(
            'touser' => $openid,
            'template_id' => $this->notify['order_appeal_arbitration']['template_id'],
            'url' => U('Member/Profile/index', '', '', TRUE),
            'topcolor' => '#FF0000',
        );
        $message['data'] = array(
            'frist' => array('value' => '您好，您的申诉已处理完毕。'),
            'keyword1' => array('value' => $buyer_info['nickname']),
            'keyword2' => array('value' => $seller_info['nickname']),
            'keyword3' => array('value' => $param['type']),
            'remark' => array('value' => '如对处理结果有异议,请联系在线客服!')
        );
        $wechat = new \Wechat\Library\Wechat($this->options);
        $wechat->sendTemplateMessage($message);
    }
    
    private function getOpenidByUid($uid = 0) {
        return model('member_oauth')->where(array('uid' => $uid, 'type' => 'wechat'))->getField('openid');
    }
}