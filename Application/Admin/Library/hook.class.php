<?php
namespace Admin\Library;
helpers('mail');
class hook {
    protected $configs = array();
    protected $replace = array();
    
    public function __construct() {
        $this->configs = getcache('notify','notify');
        $this->replace['{site_name}'] = C('webname');
        $this->replace['{site_url}'] = 'http://'.$_SERVER['HTTP_HOST'];
        $this->replace['{ip}'] = get_client_ip();
    }
    
    /**
     * 注册激活
     * @param type $param
     * userid  : 会员ID
     * modelid : 会员模型ID
     * email   : 邮箱地址
     */
    public function member_email_activate(&$param) {
        if(empty($this->configs['member_email_activate']['content'])) return FALSE;
        $email_enable = getcache('setting', 'member');
        if (in_array($param['modelid'], $email_enable['setting_register_email_enable'])) {
            $code = authcode($param['userid'].'|'.md5(C('AUTHKEY')), 'ENCODE');
            $this->replace['{url}'] = U('Member/Index/verify_email', array('code' => $code), '', TRUE);
            $this->replace['{email}'] = $t_email;
            $message = str_replace(array_keys($this->replace), $this->replace, dstripslashes($this->configs['member_email_activate']['content']));
            sendmail($param['email'], "邮箱认证激活", $message);
        }
    }
    
    /**
     * 注册欢迎邮件
     * @param type $param
     * userid : 会员ID
     */
    public function member_email_webcome(&$param) {
        if(empty($this->configs['member_email_webcome']['content'])) return FALSE;
        $data = array();
        $userid = (int) $param['userid'];
        if ($userid > 0) {    
            $this->replace['{site_name}'] = C('webname'); 
            $content = str_replace(array_keys($this->replace), $this->replace, dstripslashes($this->configs['member_email_webcome']['content']));
            $message = array();
            $message['send_from_id'] = 1;
            $message['send_to_id'] = $userid;
            $message['subject'] = '注册欢迎邮件';
            $message['content'] = $content;
            $api = new \Message\Library\api();
            $api->send_mess($message);
            // push($userid,$message_id,'注册欢迎邮件',$content,'','',$m_time='0');
        }
    }
    
    /**
     * 找回密码
     * @param type $param
     * userid  : 会员ID
     * email   : 邮箱地址
     * account : 用户名
     */
    public function member_email_retpwd(&$param) {
        if(empty($this->configs['member_email_retpwd']['content'])) return FALSE;
        $userid = (int) $param['userid'];
        if ($userid > 0) {
            $code = authcode($userid.'|'.md5(C('AUTHKEY')), 'ENCODE');            
            $this->replace['{url}'] = U('Member/Index/forget_resetpwd', array('email'=>$param['email'],'posttime'=>NOW_TIME,'key'=>$code),'',TRUE, TRUE);
            $content = dstripslashes($this->configs['member_email_retpwd']['content']);
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
    public function pay_recharge_check(&$param) {
        $type = explode(',', $this->configs['pay_recharge_check']['type']);
        $userid = (int) $param['userid'];
        $result = $param['result'];
        if(empty($type)) return FALSE;        
      if($userid < 1) return FALSE;        
        $user_info = getUserInfo($userid);
        $result = $param['result'];
        if(!$user_info) return FALSE;
        $this->replace['{nickname}'] = $user_info['nickname'];  
        $this->replace['{money}'] = $param['money'];
        $subject = '充值审核处理';
        $content = "普通充值审核处理：\r\n{nickname}您好：\r\n您提交的普通充值申请,审核{state}。充值金额：{money}元。";
        if($result == 1) {
            $content .= "\r\n资金已实时充入您的网站账户,请登录网站查看,祝您生意兴隆!";
            $_msg = '资金已实时充入您的网站账户,请登录网站查看,祝您生意兴隆!';
        } else {
            $content .= "\r\n请检查重新提交,或联系相关客服专员。";
            $_msg = '请检查重新提交,或联系相关客服专员';

        }
        $this->replace['{state}'] = ($result == 1) ? '成功' : '失败';
        if ($result == 1) {
           $_result = '成功';
        }else{
             $_result = '失败';
        }
        $content = str_replace(array_keys($this->replace), $this->replace, $content);        
        if(in_array('message', $type)) {
            $message = array();
            $message['send_from_id'] = 1;
            $message['send_to_id'] = $userid;
            $message['subject'] = $subject;
            $message['content'] = str_replace('<br/>','\r\n',$content);
            $api = new \Message\Library\api();
            $message_id = $api->send_mess($message); 
            $app = model('member_app')->where(array('alias'=>$userid))->find();
            if ($app && in_array('push', $type)) {
               $res =  push($userid,$subject,$content,'','',$m_time='0',$userid); 
               
            }    
        }

       

        if (in_array('email', $type) && $user_info['email_status'] == 1){
            sendmail($user_info['email'], $subject, str_replace('<br/>','\r\n',$content));
        }
        if (in_array('sms', $type) && $user_info['phone_status'] == 1) {
            $SmsApi = new \Sms\Api\SmsApi();
            $arr = array();
            $_nickname = $user_info['nickname'];
            $_money = $param['money'];
            $arr['param'] = "{'nickname':'$_nickname','state':'$_result','money':'$_money','msg':'$_msg'}";
            $arr['template_id'] = C('template_id_4');
            $result = $SmsApi->send($user_info['phone'], $content,$arr);
            if ($result) {
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
    public function pay_cash_check(&$param) {
        $type = explode(',', $this->configs['pay_cash_check']['type']);
        $userid = (int) $param['userid'];
        if(empty($type)) return FALSE;        
        if($userid < 1) return FALSE;        
        $user_info = getUserInfo($userid);
        if(!$user_info) return FALSE;    
        $pay_setting = getcache('deposite_setting', 'pay');        
        $pay_type = ($param['paypal'] == 2) ? 'quick' : 'common';
        $this->replace['{nickname}'] = $user_info['nickname'];
        if($param['paypal'] == 2) {
            $_paypal = '快速';
        }elseif($param['paypal'] == 1){
            $_paypal = '普通';
        }elseif($param['paypal'] == 3){
            $_paypal = '微信实时';
        }

         if ($param['result'] == 1) {
           $_result = '成功';
        }else{
           $_result = '失败';
        }
        $this->replace['{paypal}'] = ($param['paypal'] == 2) ? '快速' : '普通';
        $this->replace['{time}'] = $pay_setting[$pay_type]['time'];
        $this->replace['{state}'] = ($param['result'] == 1) ? '成功' : '失败';
        $this->replace['{money}'] = $param['money'];
        $this->replace['{cause}'] = $param['cause'];
        $subject = '提现审核处理';
        $content .= $subject;
        $content .= "\r\n{nickname}您好：\r\n您提交的{paypal}提现申请审核{state}！\r\n提现金额：{money}元\r\n";
        if($param['result'] == 1) {
            $content .= "资金将在{time}小时到达您的提现账户，请注意查收。";
            $_msg ='资金将在'.$pay_setting[$pay_type]['time'].'小时到达您的提现账户，请注意查收';
        } else {
            $content .= "失败原因：{cause}\r\n资金已退回您网站账户,请重新提交申请。";
            $_msg = "失败原因：".$param['cause']."资金已退回您网站账户,请重新提交申请。";
        }
        $content = str_replace(array_keys($this->replace), $this->replace, $content);
       
        if (in_array('message', $type)){
            $data['send_from_id'] = 1;
            $data['send_to_id'] = $userid;
            $data['subject'] = $subject;
            $data['content'] = str_replace('<br/>','\r\n',$content);
            $api = new \Message\Library\api();
            $message_id = $api->send_mess($data);

            $app = model('member_app')->where(array('alias'=>$userid))->find();
            if ($app && in_array('push', $type)) {
                push($userid,$subject,$content,'','',$m_time='0',$userid);      
            }
        }
        if (in_array('email', $type) && $user_info['email_status'] == 1){
            sendmail($user_info['email'], $subject, str_replace('<br/>','\r\n',$content));
        }
        if (in_array('sms', $type) && $user_info['phone_status'] == 1){
            $SmsApi = new \Sms\Api\SmsApi();
            $arr = array();
            $_time = $pay_setting[$pay_type]['time'];
            $_nickname = $user_info['nickname'];
            $_money = $param['money'];
            $arr['param'] = "{'nickname':'$_nickname','paypal':'$_paypal','state':'$_result','money':'$_money','msg':'$_msg'}";
            $arr['template_id'] = C('template_id_5');
            $result =  $SmsApi->send($user_info['phone'], $content,$arr);
           if ($result) {
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
    public function product_check(&$param) {
        $type = explode(',', $this->configs['product_check']['type']);
        $userid = (int) $param['userid'];
        if(empty($type)) return FALSE;        
        if($userid < 1) return FALSE;        
        $user_info = getUserInfo($userid);
        if(!$user_info) return FALSE;
        
        $subject = $content = '商品审核处理';
        $content .= "\r\n{nickname}您好：\r\n您报名的活动商品审核{state}，\r\n商品名称：{title}\r\n";
        if($param['result'] == 1) {
            $content .= "\r\n上线时间：{online_time}\r\n到期后将自动上线，请做好相关准备工作。";
            $_msg = "上线时间:".dgmdate($param['start_time'])."到期后将自动上线，请做好相关准备工作。";
        } else {
            $content .= "如已支付保证金，将自动退回到您网站账户！";
             $_msg = "如已支付保证金，将自动退回到您网站账户！";
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
            $arr = array();
            $_nickname = $user_info['nickname'];
            $_title = $param['title'];
            $arr['param'] = "{'nickname':'$_nickname','state':'$_result','title':'$_title','msg':'$_msg'}";
            $arr['template_id'] = C('template_id_6');
            $SmsApi->send($user_info['phone'], $content,$arr);
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
     * 商品屏蔽通知
     * @param type $param
     * userid : 商家ID
     * title  : 商品名称
     * cause  : 屏蔽原因
     */
    public function product_lock(&$param) {
        $type = explode(',', $this->configs['product_lock']['type']);
        $userid = (int) $param['userid'];
        if(empty($type)) return FALSE;        
        if($userid < 1) return FALSE;        
        $user_info = getUserInfo($userid);
        if(!$user_info) return FALSE;
        
        $subject = $content = '商品屏蔽通知';
        $content .= "\r\n{nickname}您好：\r\n您的活动商品已被管理员屏蔽，商品名称：{title}\r\n屏蔽原理：{cause}\r\n\r\n如有疑问请联系相关客服专员处理";
        
        $this->replace['{nickname}'] = $user_info['nickname'];
        $this->replace['{title}'] = $param['title'];
        $this->replace['{cause}'] = $param['cause'];
        
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
            sendmail($user_info['email'], $subject, str_replace('<br/>','\r\n',$content));
        }
        if (in_array('sms', $type) && $user_info['phone_status'] == 1){
            $SmsApi = new \Sms\Api\SmsApi();
            $arr = array();
            $_nickname = $user_info['nickname'];
            $_title = $param['title'];
            $_cause = $param['cause'];

            $arr['param'] = "{'nickname':'$_nickname','title':'$_title','cause':'$_cause'}";
            $arr['template_id'] = C('template_id_7');
           $result =  $SmsApi->send($user_info['phone'], $content,$arr);


           // $SmsApi->send($user_info['phone'], $content);
           if ($result) {
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
    }
    
    /**
     * 商品结算通知
     * @param type $param
     * userid : 商家ID
     * title  : 商品名称
     */
    public function product_balance(&$param) {
        $type = explode(',', $this->configs['product_lock']['type']);
        $userid = (int) $param['userid'];
        if(empty($type)) return FALSE;        
        if($userid < 1) return FALSE;        
        $user_info = getUserInfo($userid);
        if(!$user_info) return FALSE;
        
        $subject = $content = "商品结算通知";
        $content .= "\r\n{nickname}您好：\r\n您报名的活动商品,已经结算完成\r\n商品名称：{title}\r\n资金已实时返还到您的网站账户!";
        
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
            $arr = array();
            $_nickname = $user_info['nickname'];
            $_title = $param['title'];
            $arr['param'] = "{'nickname':'$_nickname','title':'$_title'}";
            $arr['template_id'] = C('template_id_8');
            $result = $SmsApi->send($user_info['phone'], $content,$arr);
            if ($result) {
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
    }
    
    /**
     * 用户填写订单号
     * @param type $param
     * userid : 商家ID
     * title  : 商品名称
     * order_sn : 订单号
     * mod : 活动类型
     */
    public function order_fill_trade_no(&$param) {
        $type = explode(',', $this->configs['order_fill_trade_no']['type']);
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
            $arr = array();
            $_nickname = $user_info['nickname'];
            $_title = $param['title'];
            $arr['param'] = "{'nickname':'$_nickname','title':'$_title'}";
            $arr['template_id'] = C('template_id_9');
            $SmsApi->send($user_info['phone'], $content,$arr);
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
     * 试用资格审核结果
     * @param type $param
     * userid : 会员ID
     * title  : 商品名称
     * mod    : 活动类型
     * result : 结果(成功/失败)
     * cause  : 失败原因
     * 
     */
    public function order_check_zige(&$param){
        $type = explode(',', $this->configs['order_check_zige']['type']);
        $userid = (int) $param['userid'];
        if(empty($type)) return FALSE;        
        if($userid < 1) return FALSE;        
        $user_info = getUserInfo($userid);
        if(!$user_info) return FALSE;
        
        $subject = $content = "试用资格结果";

        if ($param['result']) {
             $_status = '成功';
        }else{
            $_status = '失败';
        }
        $subject = $content = "申请试用品审核结果";
        $content .= "{nickname}您好：\r\n您本次提交的试用申请{state}！\r\n试用品：{title}";
        $_content = "申请试用品审核结果".$user_info['nickname']."您好：您本次提交的试用申请".$_status.",试用品：".$param['title']."";
        if($param['result'] == 1) {
           $content .= "\r\n请尽快按活动要求下单,到期将自动取消资格！！";
           $_msg = '请尽快按活动要求下单,到期将自动取消资格！！';
        }else{
            //不通过 返回
            return false;
        }

        $this->replace['{nickname}'] = $user_info['nickname'];
        $this->replace['{state}'] = ($param['result'] == 1) ? '成功' : '失败';
        $this->replace['{title}'] = $param['title'];
        $this->replace['{cause}'] = $param['{cause}'];
        $content = str_replace(array_keys($this->replace), $this->replace, $content);
        if($param['result'] == 1 ){
            if (in_array('message', $type)){
                $data['send_from_id'] = 1;
                $data['send_to_id'] = $userid;
                $data['subject'] = $subject;
                $data['content'] = str_replace('<br/>','\r\n',$content);
                $api = new \Message\Library\api();
                $message_id = $api->send_mess($data);
                $app = model('member_app')->where(array('alias'=>$userid))->find();
                if ($app && in_array('push', $type)) {
                    //推送下发
                    push($userid,$subject,$content,'','',$m_time='0',$userid);      
                }
            }

            if (in_array('email', $type) && $user_info['email_status'] == 1){
                sendmail($user_info['email'],$subject, str_replace('<br/>','\r\n',$content));
            }

            if (in_array('sms', $type) && $user_info['phone_status'] == 1){
                $SmsApi = new \Sms\Api\SmsApi();
                $arr = array();
                $_nickname = $user_info['nickname'];
                $_title = $param['title'];
                $arr['param'] = "{'nickname':'$_nickname','content':'$_content','msg':'$_msg'}";
                $arr['template_id'] = C('template_id_11');
                $SmsApi->send($user_info['phone'], $content,$arr);
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
    public function order_check_trade_no(&$param) {
        $type = explode(',', $this->configs['order_check_trade_no']['type']);
        $userid = (int) $param['userid'];
        if(empty($type)) return FALSE;        
        if($userid < 1) return FALSE;        
        $user_info = getUserInfo($userid);
        if(!$user_info) return FALSE;
        
        $subject = $content = "订单审核结果";

        if ($param['result']) {
             $_status = '成功';
        }else{
            $_status = '失败';
        }

        if($param['mod'] == 'rebate') {
            $_content = "".$user_info['nickname']."您好：您提交的订单号由商家审核为".$_status."！商品名称：".$param['title']."";
            $content .= "{nickname}您好：\r\n您提交的订单号由商家审核为{state}！\r\n商品名称：{title}";
            if($param['result'] == 1) {
                $_msg = '请等待商家确认付款！';
                $content .= "\r\n请等待商家确认付款！";
            } else {
                $content .= "审核理由：{cause}\r\n请修改订单号,重新提交或发起申诉,提交平台仲裁！";
                  $_msg = '审核理由：'.$param['cause'].'请修改订单号,重新提交或发起申诉,提交平台仲裁！';
            }
        } else {
            $subject = $content = "申请试用品审核结果";
            if ($param['order_sn']) {
                $content .= "{nickname}您好：\r\n您提交的订单号由商家审核为{state}！\r\n商品名称：{title}";
                $_content = "".$user_info['nickname']."您好：您提交的订单号由商家审核为".$_status."！商品名称：".$param['title']."";
                 if($param['result'] == 1) {
                        $content .= "\r\n请耐心等待确认收货后填写试用报告！";
                        $_msg = '请请耐心等待确认收货后填写试用报告！';
                    } else {
                      $content .= "审核理由：{cause}\r\n请修改订单号,重新提交或发起申诉,提交平台仲裁！";
                      $_msg = '审核理由：'.$param['cause'].'请修改订单号,重新提交或发起申诉,提交平台仲裁！';
                    }
               
            }else{
                $content .= "{nickname}您好：\r\n您本次提交的试用申请{state}！\r\n试用品：{title}";
                $_content = "申请试用品审核结果".$user_info['nickname']."您好：您本次提交的试用申请".$_status.",试用品：".$param['title']."";

                   if($param['result'] == 1) {     

                     $this->order_check_zige($param);

                   } else {
                       return false;
                   }
            }

        }
        
        $this->replace['{nickname}'] = $user_info['nickname'];
        $this->replace['{state}'] = ($param['result'] == 1) ? '成功' : '失败';
        $this->replace['{title}'] = $param['title'];
        $this->replace['{cause}'] = $param['{cause}'];
        $content = str_replace(array_keys($this->replace), $this->replace, $content);
        
        if ($param['result'] == 1 ) {
            if (in_array('message', $type)){
                $data['send_from_id'] = 1;
                $data['send_to_id'] = $userid;
                $data['subject'] = $subject;
                $data['content'] = str_replace('<br/>','\r\n',$content);
                $api = new \Message\Library\api();
                $message_id = $api->send_mess($data);
                $app = model('member_app')->where(array('alias'=>$userid))->find();
                if ($app && in_array('push', $type)) {
                    push($userid,$subject,$content,'','',$m_time='0',$userid);      
                }
            }

            if (in_array('email', $type) && $user_info['email_status'] == 1){
                sendmail($user_info['email'],$subject, str_replace('<br/>','\r\n',$content));
            }

            if (in_array('sms', $type) && $user_info['phone_status'] == 1){
                $SmsApi = new \Sms\Api\SmsApi();
                $arr = array();
                $_nickname = $user_info['nickname'];
                $_title = $param['title'];
                $arr['param'] = "{'nickname':'$_nickname','content':'$_content','msg':'$_msg'}";
                $arr['template_id'] = C('template_id_11');
                $SmsApi->send($user_info['phone'], $content,$arr);
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
    public function order_fill_report(&$param) {
        $type = explode(',', $this->configs['order_fill_report']['type']);
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
            $arr = array();
            $_nickname = $user_info['nickname'];
            $_title = $param['title'];
            $arr['param'] = "{'nickname':'$_nickname','title':'$_title'}";
            $arr['template_id'] = C('template_id_10');
            $SmsApi->send($user_info['phone'], $content,$arr);
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
     * 订单结算通知 [蒲哥说的是付款这步才执行]
     * @param array $param
     * userid : 会员ID
     * title  : 商品名称
     * cause  : 失败原因
     * result : 结果(成功/失败)
     */
    public function order_balance(&$param) {
        $type = explode(',', $this->configs['order_balance']['type']);
        $userid = (int) $param['userid'];
        if(empty($type)) return FALSE;        
        if($userid < 1) return FALSE;        
        $user_info = getUserInfo($userid);
        if(!$user_info) return FALSE;
        
        $subject = $content = "订单结算通知";
        $content = "\r\n{nickname}您好：\r\n您参与的活动{state}\r\n商品名称：{title}";
        if($param['result'] == 1) {
            $_msg = '已实时充入您的网站账户';
            $content .= "\r\n已实时充入您的网站账户";
        } else {
            $content .= "\r\n请及时修改订单号重新提交,到期后将自动关闭订单。";
            $_msg = "请及时修改订单号重新提交,到期后将自动关闭订单。";
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
            $message_id = $api->send_mess($data);
            $app = model('member_app')->where(array('alias'=>$userid))->find();
            if ($app && in_array('push', $type)) {
                push($userid,$subject,$content,'','',$m_time='0',$userid);      
            }
        }
        
        if (in_array('email', $type) && $user_info['email_status'] == 1){
            sendmail($user_info['email'],$subject, str_replace('<br/>','\r\n',$content));
        }
        if (in_array('sms', $type) && $user_info['phone_status'] == 1){
            $SmsApi = new \Sms\Api\SmsApi();
            $arr = array();
            $_nickname = $user_info['nickname'];
            $_title = $param['title'];
            if ($param['result'] == 1) {
                $_result = '结算完成';
            }else{
                $_result = '审核失败';

            }
            $arr['param'] = "{'nickname':'$_nickname','state':'$_result','title':'$_title','msg':'$_msg'}";
            $arr['template_id'] = C('template_id_12');
            $SmsApi->send($user_info['phone'], $content,$arr);
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
    public function order_appeal(&$param) {
        $type = explode(',', $this->configs['order_appeal']['type']);
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
            $arr = array();
            $_nickname = $user_info['nickname'];
            $_title = $param['title'];
            $arr['param'] = "{'nickname':'$_nickname','title':'$_title'}";
            $arr['template_id'] = C('template_id_13');
            $SmsApi->send($user_info['phone'], $content,$arr);
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
    public function order_appeal_arbitration(&$param) {
        $type = explode(',', $this->configs['order_appeal_arbitration']['type']);
        $userid = (int) $param['userid'];
        $seller_id = (int) $param['seller_id'];
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
            $message_id = $api->send_mess($data);
            $app = model('member_app')->where(array('alias'=>$userid))->find();
            if ($app && in_array('push', $type)) {
                push($userid,$subject,$content,'','',$m_time='0',$userid);      
            }
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

                $arr = array();
                $_nickname = $buyer_info['nickname'];
                $_title = $param['title'];
                $_type = $param['type'];
                $arr['param'] = "{'nickname':'$_nickname','title':'$_title','type':'$_type'}";
                $arr['template_id'] = C('template_id_14');
                $SmsApi->send($buyer_info['phone'], $buyer_msg,$arr);
/*                $SmsApi->send($buyer_info['phone'], $buyer_msg);
*/
                model('sms_report')->update($info);
            }
            if ($seller_info['phone'] && $seller_info['phone_status'] == 1) {
                $info['mobile'] = $seller_info['phone'];
                $info['msg'] = $seller_msg;
                $info['userid'] = $seller_id;
                $info['status'] = 1;

                 $arr = array();
                $_nickname = $seller_info['nickname'];
                $_title = $param['title'];
                $_type = $param['type'];
                $arr['param'] = "{'nickname':'$_nickname','title':'$_title','type':'$_type'}";
                $arr['template_id'] = C('template_id_14');
                $SmsApi->send($seller_info['phone'], $seller_msg,$arr);
                //$SmsApi->send($seller_info['phone'], $seller_msg);
                model('sms_report')->update($info);
            }
        }
    }

    /**
     * 赠送vip会员
     * @param array $param
     * userid : 会员ID
     * name  : 会员类型
     * type   : 处理结果
     */
    public function zeng_vip_message(&$param) {
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
           // $SmsApi->send($user_info['phone'], $content);
            $arr = array();
            $_nickname = $user_info['nickname'];
            $_name = $param['name'];
            $arr['param'] = "{'nickname':'$_nickname','name':'$_name'}";
            $arr['template_id'] = C('template_id_16');
            $SmsApi->send($user_info['phone'], $content,$arr);
            $info = array();
            $info['mobile'] = $user_info['phone'];
            $info['posttime'] = NOW_TIME;
            $info['msg'] = $content;
            $info['userid'] = $userid;
            $info['status'] = 1;
            $info['enum'] = 'notice';
            model('sms_report')->update($info);
        }

    }

     public function goods_number_notify(&$param) {
        $type = explode(',', $this->configs['goods_notify']['type']);
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

            $arr = array();
            $_nickname = $user_info['nickname'];
            $_title = $param['title'];
            $arr['param'] = "{'nickname':'$_nickname','title':'$_title'}";
            $arr['template_id'] = C('template_id_15');
            $SmsApi->send($param['phone'], $content,$arr);


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
