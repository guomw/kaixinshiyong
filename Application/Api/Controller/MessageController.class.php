<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Api\Controller;
use \Common\Controller\BaseController;
class MessageController extends BaseController {

    /**
     * 注册激活
     * @param type $param
     * userid  : 会员ID
     * modelid : 会员模型ID
     * email   : 邮箱地址
     */
    public function app_member_email_activate($param) {
        if(empty($this->configs['app_member_email_activate']['content'])) return FALSE;
        $email_enable = getcache('setting', 'member');
        if (in_array($param['modelid'], $email_enable['setting_register_email_enable'])) {
            $code = authcode($param['userid'].'|'.md5(C('AUTHKEY')), 'ENCODE');
            $this->replace['{url}'] = U('Member/Index/verify_email', array('code' => $code), '', TRUE);
            $this->replace['{email}'] = $t_email;
            $message = str_replace(array_keys($this->replace), $this->replace, dstripslashes($this->configs['app_member_email_activate']['content']));
            sendmail($param['email'], "邮箱认证激活", $message);
        }
    }

    public function test(){
       /* $account = 2851105929;
       $get_info = 'https://report.jpush.cn/v3/messages?msg_ids=1 -u "7d431e42dfa6a6d693ac2d04:5e987ac6d2e04d95a9d8f0d1"');
        var_dump($get_info);
       die();*/
        test2();
      // $result = 'https://report.jpush.cn/v3/messages?msg_ids=269978303 ';
      // var_dump($result);
    }


    /**
     * 注册欢迎邮件
     * @param type $param
     * userid : 会员ID
     */
    public function app_member_email_webcome(&$param) {
        if(empty($this->configs['app_member_email_webcome']['content'])) return FALSE;
        $data = array();
        $userid = (int) $param['userid'];
        if ($userid > 0) {         
            $content = str_replace(array_keys($this->replace), $this->replace, dstripslashes($this->configs['app_member_email_webcome']['content']));
            $message = array();
            $message['send_from_id'] = 1;
            $message['send_to_id'] = $userid;
            $message['subject'] = '注册欢迎邮件';
            $message['content'] = $message;
            $api = new \Message\Library\api();
            $api->send_mess($message);
        }
    }

    /**
     * 找回密码
     * @param type $param
     * userid  : 会员ID
     * email   : 邮箱地址
     * account : 用户名
     */
    public function app_member_email_retpwd($param) {
        if(empty($this->configs['app_member_email_retpwd']['content'])) return FALSE;
        $userid = (int) $param['userid'];
        if ($userid > 0) {
            $code = authcode($userid.'|'.md5(C('AUTHKEY')), 'ENCODE');            
            $this->replace['{url}'] = U('Member/Index/forget_resetpwd', array('email'=>$param['email'],'posttime'=>NOW_TIME,'key'=>$code),'',TRUE, TRUE);
            $content = dstripslashes($this->configs['app_member_email_retpwd']['content']);
            $this->replace['{account}'] = $param['account'];
            $this->replace['{email}'] = $param['email'];
            $message = str_replace(array_keys($this->replace), $this->replace, $content);
            sendmail($param['email'], '密码找回邮件', $message);
        }
    }


      /**
     * 普通充值审核处理结果
     * @param type $param
     * userid : 会员id
     * money  : 充值金额
     * cause  : 原因
     * result : 结果(成功/失败)
     */
    public function app_pay_recharge_check($param) {
        $type = explode(',', $this->configs['app_pay_recharge_check']['type']);
        $userid = (int) $param['userid'];
        if(empty($type)) return FALSE;        
        if($userid < 1) return FALSE;        
        $user_info = getUserInfo($userid);
        if(!$user_info) return FALSE;
        $this->replace['{nickname}'] = $user_info['nickname'];  
        $this->replace['{money}'] = $param['money'];                  
        $subject = '充值审核处理';
        $content = "普通充值审核处理：\r\n{nickname}您好：\r\n您提交的普通充值申请,审核{state}。充值金额：{money}元。";
        if($result == 1) {
            $content .= "\r\n资金已实时充入您的网站账户,请登录网站查看,祝您生意兴隆!";
        } else {
            $content .= "\r\n请检查重新提交,或联系相关客服专员。";
        }
        $this->replace['{state}'] = ($result == 1) ? '成功' : '失败';
        $content = str_replace(array_keys($this->replace), $this->replace, $content);        
        if(in_array('message', $type)) {
            $message = array();
            $message['send_from_id'] = 1;
            $message['send_to_id'] = $userid;
            $message['subject'] = $subject;
            $message['content'] = str_replace('<br/>','\r\n',$content);
            $api = new \Message\Library\api();
            $api->send_mess($data);            
        }
        if (in_array('email', $type) && $user_info['email_status'] == 1){
            sendmail($user_info['email'], $subject, str_replace('<br/>','\r\n',$content));
        }
        if (in_array('sms', $list) && $user_info['phone_status'] == 1) {
            $SmsApi = new \Sms\Api\SmsApi();
            $SmsApi->send($user_info['phone'], $content);
            $info = array();
            $info['mobile'] = $user_info['phone'];
            $info['posttime'] = NOW_TIME;
            $info['msg'] = $content;
            $info['userid'] = $userid;
            $info['enum'] = 'notice';
            $info['status'] = 1;
            model('sms_report')->update($info);
        }
        return FALSE;
    }


    /**
     * 提现审核处理结果
     * @param type $param
     * userid : 会员ID
     * money  : 提现金额    
     * result : 结果(成功/失败)
     * paypal : 提现方式(1:普通;2:快速)
     */
    public function app_pay_cash_check(&$param) {
        $type = explode(',', $this->configs['app_pay_cash_check']['type']);
        $userid = (int) $param['userid'];
        if(empty($type)) return FALSE;        
        if($userid < 1) return FALSE;        
        $user_info = getUserInfo($userid);
        if(!$user_info) return FALSE;    
        $pay_setting = getcache('deposite_setting', 'pay');        
        $pay_type = ($param['paypal'] == 2) ? 'quick' : 'common';
        $this->replace['{nickname}'] = $user_info['nickname'];
        $this->replace['{paypal}'] = ($param['paypal'] == 2) ? '快速' : '普通';
        $this->replace['{time}'] = $pay_setting[$pay_type]['time'];
        $this->replace['{state}'] = ($param['result'] == 1) ? '成功' : '失败';
        $this->replace['{money}'] = $param['money'];
        $this->replace['{cause}'] = $param['cause'];
        $subject = '提现审核处理';
        $content .= $subject;
        $content .= "\r\n{nickname}您好：\r\n您提交的{paypal}提现申请审核{state}！\r\n提现金额：{money}元\r\n";
        if($param['result'] == 1) {
            $content .= "资金将在{time}到达您的提现账户，请注意查收。";
        } else {
            $content .= "失败原因：{cause}\r\n资金已退回您网站账户,请重新提交申请。";
        }
        $content = str_replace(array_keys($this->replace), $this->replace, $content);
        if (in_array('message', $type)){
            $data['send_from_id'] = 1;
            $data['send_to_id'] = $userid;
            $data['subject'] = $subject;
            $data['content'] = str_replace('<br/>','\r\n',$content);
            $api = new \Message\Library\api();
            $api->send_mess($data);
        }
        if (in_array('email', $type) && $user_info['email_status'] == 1){
            sendmail($user_info['email'], $subject, str_replace('<br/>','\r\n',$content));
        }
        if (in_array('sms', $type) && $user_info['phone_status'] == 1){
            $SmsApi = new \Sms\Api\SmsApi();
            $SmsApi->send($user_info['phone'], $content);
            $info = array();
            $info['mobile'] = $user_info['phone'];
            $info['posttime'] = NOW_TIME;
            $info['msg'] = $content;
            $info['userid'] = $userid;
            $info['enum'] = 'notice';
            $info['status'] = 1;
            model('sms_report')->update($info);
        }
    }


    /**
     * 商品审核结果
     * @param type $param
     * userid : 商家ID
     * start_time : 上线时间
     * title : 商品名称
     * cause : 失败原因[预留]
     * result: 结果(成功/失败)
     */
    public function app_product_check($param) {
        $type = explode(',', $this->configs['app_product_check']['type']);
        $userid = (int) $param['userid'];
        if(empty($type)) return FALSE;        
        if($userid < 1) return FALSE;        
        $user_info = getUserInfo($userid);
        if(!$user_info) return FALSE;
        
        $subject = $content = '商品审核处理';
        $content .= "\r\n{nickname}您好：\r\n您报名的活动商品审核{state}，\r\n商品名称：{title}\r\n";
        if($param['result'] == 1) {
            $content .= "\r\n上线时间：{online_time}\r\n到期后将自动上线，请做好相关准备工作。";
        } else {
            $content .= "如已支付保证金，将自动退回到您网站账户！";
        }
        
        $this->replace['{nickname}'] = $user_info['nickname'];
        $this->replace['{state}'] = ($param['result'] == 1) ? '成功' : '失败';
        $this->replace['{title}'] = $param['title'];
        $this->replace['{online_time}'] = dgmdate($param['start_time']);
        
        $content = str_replace(array_keys($this->replace), $this->replace, $content);
        if (in_array('message', $type)){
            $message = array();
            $message['send_from_id'] = 1;
            $message['send_to_id'] = $userid;
            $message['subject'] = $subject;
            $message['content'] = str_replace('<br/>','\r\n', $content);
            $api = new \Message\Library\api();
            $api->send_mess($message);
        }
        if (in_array('email', $type) && $user_info['email_status'] == 1){
            sendmail($user_info['email'],$subject, str_replace('<br/>','\r\n',$content));
        }
        if (in_array('sms', $type) && $user_info['phone_status'] == 1){
            $SmsApi = new \Sms\Api\SmsApi();
            $SmsApi->send($user_info['phone'], $content);
            $info = array();
            $info['mobile'] = $user_info['phone'];
            $info['posttime'] = NOW_TIME;
            $info['msg'] = $content;
            $info['userid'] = $userid;
            $info['enum'] = 'notice';
            $info['status'] = 1;
            model('sms_report')->update($info);
        }
        return TRUE;
    }


     /**
     * 用户填写订单号
     * @param type $param
     * userid : 商家ID
     * title  : 商品名称
     * order_sn : 订单号
     * mod : 活动类型
     */
    public function app_order_fill_trade_no($param) {
        $type = explode(',', $this->configs['app_order_fill_trade_no']['type']);
        $userid = (int) $param['userid'];
        if(empty($type)) return FALSE;        
        if($userid < 1) return FALSE;        
        $user_info = getUserInfo($userid);
        if(!$user_info) return FALSE;
        
        $subject = $content = "新增订单号";
        $content .= "\r\n{nickname}您好：\r\n商品名称：{title}\r\n请及时审核,\r\n如未审核,到期后将自动审核";
        $this->replace['{nickname}'] = $user_info['nickname'];
        $this->replace['{title}'] = $param['title'];
        $content = str_replace(array_keys($this->replace), $this->replace, $content);
        if (in_array('message', $type)){
            $data['send_from_id'] = 1;
            $data['send_to_id'] = $userid;
            $data['subject'] = $subject;
            $data['content'] = str_replace('<br/>','\r\n',$content);
            $api = new \Message\Library\api();
            $api->send_mess($data);
        }
        if (in_array('email', $type) && $user_info['email_status'] == 1){
            sendmail($user_info['email'],$subject, str_replace('<br/>','\r\n',$content));
        }
        if (in_array('sms', $type) && $user_info['phone_status'] == 1){
            $SmsApi = new \Sms\Api\SmsApi();
            $SmsApi->send($user_info['phone'], $content);
            $info = array();
            $info['mobile'] = $user_info['phone'];
            $info['posttime'] = NOW_TIME;
            $info['msg'] = $content;
            $info['userid'] = $userid;
            $info['enum'] = 'notice';
            $info['status'] = 1;
            model('sms_report')->update($info);
        }
    }


    /**
     * 订单审核结果[购物返利/免费试用]
     * @param type $param
     * userid : 会员ID
     * title  : 商品名称
     * mod    : 活动类型
     * result : 结果(成功/失败)
     * cause  : 失败原因
     * 
     */
    public function app_order_check_trade_no($param) {
        $type = explode(',', $this->configs['app_order_check_trade_no']['type']);
        $userid = (int) $param['userid'];
        if(empty($type)) return FALSE;        
        if($userid < 1) return FALSE;        
        $user_info = getUserInfo($userid);
        if(!$user_info) return FALSE;
        
        $subject = $content = "订单审核结果";
        if($param['mod'] == 'rebate') {
            $content .= "{nickname}您好：\r\n您提交的订单号由商家审核为{state}！\r\n商品名称：{title}";
            if($param['result'] == 1) {
                $content .= "\r\n请等待商家确认付款！";
            } else {
                $content .= "审核理由：{cause}\r\n请修改订单号,重新提交或发起申诉,提交平台仲裁！";
            }
        } else {
            $content .= "{nickname}您好：\r\n您本次提交的试用申请{state}！\r\n试用品：{title}";
            if($param['result'] == 1) {
                $content .= "\r\n请尽快按活动要求下单,到期将自动取消资格！";
            } else {
                $content .= "\r\n建议您重新尝试申请!\r\n友情提示:多参加活动有助于提高通过率！";
            }
        }
        
        $this->replace['{nickname}'] = $user_info['nickname'];
        $this->replace['{state}'] = ($param['result'] == 1) ? '成功' : '失败';
        $this->replace['{title}'] = $param['title'];
        $this->replace['{cause}'] = $param['{cause}'];
        $content = str_replace(array_keys($this->replace), $this->replace, $content);
       
        if ($param['result'] == 1) {
            if (in_array('message', $type)){
                $data['send_from_id'] = 1;
                $data['send_to_id'] = $userid;
                $data['subject'] = $subject;
                $data['content'] = str_replace('<br/>','\r\n',$content);
                $api = new \Message\Library\api();
                $api->send_mess($data);
            }


            if (in_array('email', $type) && $user_info['email_status'] == 1){
                sendmail($user_info['email'],$subject, str_replace('<br/>','\r\n',$content));
            }


            if (in_array('sms', $type) && $user_info['phone_status'] == 1){
                $SmsApi = new \Sms\Api\SmsApi();
                $SmsApi->send($user_info['phone'], $content);
                $info = array();
                $info['mobile'] = $user_info['phone'];
                $info['posttime'] = NOW_TIME;
                $info['msg'] = $content;
                $info['userid'] = $userid;
                $info['enum'] = 'notice';
                $info['status'] = 1;
                model('sms_report')->update($info);
            }

        }

        return TRUE;
    }


    /**
     * 填写试用报告通知
     * userid : 商家ID
     * title  : 商品名称
     */
    public function app_order_fill_report($param) {
        $type = explode(',', $this->configs['app_order_fill_report']['type']);
        $userid = (int) $param['userid'];
        if(empty($type)) return FALSE;        
        if($userid < 1) return FALSE;        
        $user_info = getUserInfo($userid);
        if(!$user_info) return FALSE;
        
        $subject = $content = "新增试用报告";
        $content .= "\r\n{nickname}您好：\r\n商品名称:{title}新增试用报告:\r\n请及时审核,如未审核,到期后将自动审核";
        $this->replace['{nickname}'] = $user_info['nickname'];
        $this->replace['{title}'] = $param['title'];
        $content = str_replace(array_keys($this->replace), $this->replace, $content);
        if (in_array('message', $type)){
            $data['send_from_id'] = 1;
            $data['send_to_id'] = $userid;
            $data['subject'] = $subject;
            $data['content'] = str_replace('<br/>','\r\n',$content);
            $api = new \Message\Library\api();
            $api->send_mess($data);
        }
        if (in_array('email', $type) && $user_info['email_status'] == 1){
            sendmail($user_info['email'],$subject, str_replace('<br/>','\r\n',$content));
        }
        if (in_array('sms', $type) && $user_info['phone_status'] == 1){
            $SmsApi = new \Sms\Api\SmsApi();
            $SmsApi->send($user_info['phone'], $content);
            $info = array();
            $info['mobile'] = $user_info['phone'];
            $info['posttime'] = NOW_TIME;
            $info['msg'] = $content;
            $info['userid'] = $userid;
            $info['enum'] = 'notice';
            $info['status'] = 1;
            model('sms_report')->update($info);
        }

        return TRUE;
    }
    

      /**
     * 订单结算通知 [蒲哥说的是付款这步才执行]
     * @param array $param
     * userid : 会员ID
     * title  : 商品名称
     * cause  : 失败原因
     * result : 结果(成功/失败)
     */
    public function app_order_balance($param) {
        $type = explode(',', $this->configs['app_order_balance']['type']);
        $userid = (int) $param['userid'];
        if(empty($type)) return FALSE;        
        if($userid < 1) return FALSE;        
        $user_info = getUserInfo($userid);
        if(!$user_info) return FALSE;
        
        $subject = $content = "订单结算通知";
        $content = "\r\n{nickname}您好：\r\n您参与的活动{state}\r\n商品名称：{title}";
        if($param['result'] == 1) {
            $content .= "\r\n已实时充入您的网站账户";
        } else {
            $content .= "\r\n请及时修改订单号重新提交,到期后将自动关闭订单。";
        }
        $this->replace['{nickname}'] = $user_info['nickname'];
        $this->replace['{state}'] = ($param['result'] == 1) ? '结算完成' : '审核失败';
        $this->replace['{title}'] = $param['title'];
        $content = str_replace(array_keys($this->replace), $this->replace, $content);
        if (in_array('message', $type)){
            $data['send_from_id'] = 1;
            $data['send_to_id'] = $userid;
            $data['subject'] = $subject;
            $data['content'] = str_replace('<br/>','\r\n', $content);
            $api = new \Message\Library\api();
            $api->send_mess($data);
        }
        if (in_array('email', $type) && $user_info['email_status'] == 1){
            sendmail($user_info['email'],$subject, str_replace('<br/>','\r\n',$content));
        }
        if (in_array('sms', $type) && $user_info['phone_status'] == 1){
            $SmsApi = new \Sms\Api\SmsApi();
            $SmsApi->send($user_info['phone'], $content);
            $info = array();
            $info['mobile'] = $user_info['phone'];
            $info['posttime'] = NOW_TIME;
            $info['msg'] = $content;
            $info['userid'] = $userid;
            $info['enum'] = 'notice';
            $info['status'] = 1;
            model('sms_report')->update($info);
        }
    }

    /**
     * 会员发起申诉
     * @param array $param
     * userid : 商家ID
     * title  : 商品名称
     */
    public function app_order_appeal($param) {
        $type = explode(',', $this->configs['app_order_appeal']['type']);
        $userid = (int) $param['userid'];
        if(empty($type)) return FALSE;        
        if($userid < 1) return FALSE;        
        $user_info = getUserInfo($userid);
        if(!$user_info) return FALSE;
        $subject = $content = '会员发起申诉';
        $content .= "\r\n{nickname}您好：\r\n您收到一笔来自买家的申诉！\r\n商品名称：{title}\r\n请您及时登录网站处理，到期后将自动处理~";
        $this->replace['{nickname}'] = $user_info['nickname'];
        $this->replace['{title}'] = $param['title'];
        $content = str_replace(array_keys($this->replace), $this->replace, $content);
        if (in_array('message', $type)){
            $message = array();
            $message['send_from_id'] = 1;
            $message['send_to_id'] = $userid;
            $message['subject'] = $subject;
            $message['content'] = str_replace('<br/>','\r\n',$content);
            $api = new \Message\Library\api();
            $api->send_mess($message);
        }
        if (in_array('email', $type) && $user_info['email_status'] == 1){
            sendmail($user_info['email'],$subject, str_replace('<br/>','\r\n',$content));
        }
        if (in_array('sms', $type) && $user_info['phone_status'] == 1){
            $SmsApi = new \Sms\Api\SmsApi();
            $SmsApi->send($user_info['phone'], $content);
            $info = array();
            $info['mobile'] = $user_info['phone'];
            $info['posttime'] = NOW_TIME;
            $info['msg'] = $content;
            $info['userid'] = $userid;
            $info['status'] = 1;
            $info['enum'] = 'notice';
            model('sms_report')->update($info);
        }
        return TRUE;
    }



     /**
     * 仲裁申诉结果
     * @param array $param
     * userid : 会员ID
     * seller_id : 商家ID
     * title  : 商品名称
     * type   : 处理结果
     */
    public function app_order_appeal_arbitration($param) {
        $type = explode(',', $this->configs['app_order_appeal_arbitration']['type']);
        $userid = (int) $param['userid'];
        if(empty($type)) return FALSE;        
        if($userid < 1) return FALSE;        
        $buyer_info = getUserInfo($userid);
        $seller_info = getUserInfo($seller_id);
        if(!$buyer_info && !$seller_info) return FALSE;
        
        $subject = $content = "仲裁申诉结果";
        $content .= "\r\n{nickname}您好：\r\n您提交的申诉,平台已处理！\r\n商品名称：{title}\r\n处理结果：{type}\r\n如对处理结果有异议,请联系在线客服!";
        $this->replace['{title}'] = $param['title'];
        $this->replace['{type}'] = $param['type'];
        
        $this->replace['{nickname}'] = $buyer_info['nickname'];
        $buyer_msg = str_replace(array_keys($this->replace), $this->replace, $content);
        
        $this->replace['{nickname}'] = $seller_info['nickname'];
        $seller_msg = str_replace(array_keys($this->replace), $this->replace, $content);
        
        if (in_array('message', $type)){
            $api = new \Message\Library\api();
            $data['send_from_id'] = 1;                
            $data['subject'] = $subject;
            //发给会员
            $data['send_to_id'] = $userid;            
            $data['content'] = str_replace('<br/>','\r\n', $buyer_msg);
            $api->send_mess($data);
            //发给商家
            $data['send_to_id'] = $seller_id;
            $data['content'] = str_replace('<br/>','\r\n', $seller_msg);
            $api->send_mess($data);
        }
            
        if (in_array('email', $type)) {
            if ($buyer_info['email'] && $buyer_info['email_status'] == 1) {
                sendmail($buyer_info['email'], $subject, str_replace('<br/>','\r\n', $buyer_msg));
            }
            if ($seller_info['email'] && $seller_Info['email_status'] == 1) {
                sendmail($seller_info['email'],$subject, str_replace('<br/>','\r\n', $seller_info));
            }
        }
            
        if (in_array('sms', $type)){
            $SmsApi = new \Sms\Api\SmsApi();                
            $info = array();                
            $info['posttime'] = NOW_TIME;                
            $info['enum'] = 'notice';
            if ($buyer_info['phone'] && $buyer_info['phone_status'] == 1) {
                $info['mobile'] = $buyer_info['phone'];
                $info['msg'] = $buyer_msg;
                $info['status'] = 1;
                $info['userid'] = $userid;
                $SmsApi->send($buyer_info['phone'], $buyer_msg);
                model('sms_report')->update($info);
            }
            if ($seller_info['phone'] && $seller_info['phone_status'] == 1) {
                $info['mobile'] = $seller_info['phone'];
                $info['msg'] = $seller_msg;
                $info['userid'] = $seller_id;
                $info['status'] = 1;
                $SmsApi->send($seller_info['phone'], $seller_msg);
                model('sms_report')->update($info);
            }
        }

        return TRUE;
    }
    
   
	 /**
     * 赠送vip会员
     * @param array $param
     * userid : 会员ID
     * name  : 会员类型
     * type   : 处理结果
     */
    public function app_zeng_vip_message($param) {
        $type = explode(',', $param['type']);
        $userid = (int) $param['userid'];

        if(empty($type)) return FALSE;
        if($userid < 1) return FALSE;
        $user_info = getUserInfo($userid);
        if(!$user_info) return FALSE;

        $subject = $content = "VIP会员通知";
        $content = "\r\n{nickname}您好：赠送的{name}vip会员，请尽情体验vip服务。如有疑问，请咨询您的客服专员。祝您生意兴隆";

        $this->replace['{nickname}'] = $user_info['nickname'];
        $this->replace['{name}'] = $param['name'];
        $content = str_replace(array_keys($this->replace), $this->replace, $content);

        if (in_array('message', $type)){
            $data['send_from_id'] = 1;
            $data['send_to_id'] = $userid;
            $data['subject'] = $subject;
            $data['content'] = str_replace('<br/>','\r\n', $content);
            $api = new \Message\Library\api();
            $api->send_mess($data);
        }
        if (in_array('email', $type) && $user_info['email_status'] == 1){
            sendmail($user_info['email'],$subject, str_replace('<br/>','\r\n',$content));
        }
        if (in_array('sms', $type) && $user_info['phone_status'] == 1){
            $SmsApi = new \Sms\Api\SmsApi();
            $SmsApi->send($user_info['phone'], $content);
            $info = array();
            $info['mobile'] = $user_info['phone'];
            $info['posttime'] = NOW_TIME;
            $info['msg'] = $content;
            $info['userid'] = $userid;
            $info['status'] = 1;
            $info['enum'] = 'notice';
            model('sms_report')->update($info);
        }
        return TRUE;

    }


    public function app_goods_number_notify($param) {
        $type = explode(',', $this->configs['app_goods_notify']['type']);
        $userid = (int) $param['userid'];
        if(empty($type)) return FALSE;        
        if($userid < 1) return FALSE;        
        $user_info = getUserInfo($userid);
        if(!$user_info) return FALSE;
        
        $subject  = "商品补仓提醒";
        $content  = "\r\n{nickname}您好：您关注的\r\n活动商品:{title}，商家已追加了库存,\r\n 请尽快前往活动页面参与活动。";
        $this->replace['{nickname}'] = $user_info['nickname'];
        $this->replace['{title}'] = $param['title'];
        $content = str_replace(array_keys($this->replace), $this->replace, $content);
        if (in_array('message', $type)){
            $data['send_from_id'] = 1;
            $data['send_to_id'] = $userid;
            $data['subject'] = $subject;
            $data['content'] = str_replace('<br/>','\r\n',$content);
            $api = new \Message\Library\api();
            $api->send_mess($data);
        }
        if (in_array('email', $type)){
            sendmail($param['email'],$subject, str_replace('<br/>','\r\n',$content));
        }
        if (in_array('sms', $type)){
            $SmsApi = new \Sms\Api\SmsApi();
            $SmsApi->send($param['phone'], $content);
            $info = array();
            $info['mobile'] = $param['phone'];
            $info['posttime'] = NOW_TIME;
            $info['msg'] = $content;
            $info['userid'] = $userid;
            $info['enum'] = 'notice';
            $info['status'] = 1;
            model('sms_report')->update($info);
        }
    }




}