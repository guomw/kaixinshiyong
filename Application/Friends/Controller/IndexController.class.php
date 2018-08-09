<?php
namespace Friends\Controller;
class IndexController extends \Common\Controller\BaseController
{
    
    public function _initialize() {
        parent::_initialize();

    }

    public function index($userid = '') {

        if(I('userid') > 0) cookie('_agent_id', I('userid'), 86400);
        $detect = new \Wap\Library\Mobile_Detect();
        if (!$detect->isMobile()) {
            redirect('/register');
        }
        if (!$userid) {
            return false;
        }
        $infos = model('member')->find($userid);
        if (!$infos) {
            return false;
        }
        $name = nickname($userid);
        include template('index');
    }



    public function  submit(){
        if (IS_POST) {
            $phone = I('phone');
            $userid = I('userid');

            if(!is_mobile($phone)) {
                $this->error('手机号码格式错误');
            }

            if(!$userid) {
                $this->error('用户信息错误');
            }
            $data = array();
            $data['phone'] = $phone;
            $data['userid'] = $userid;
            cookie('phone', $phone);
            $this->success('验证成功',U('set_pwd',array('userid'=>$userid)));

        }


    }


    public function set_pwd($userid){
        $phone = cookie('phone');
        $userid = I('userid');
        if(!$phone || !$userid){
            return false;
        }
        include template('set_pwd');
    }


    /* 注册完成发送app下载短信*/
     public function app_sms(){
        $msg = '恭喜您！注册成功！现在可以下载APP体验免费试用啦，<a href="#">去下载</a>';
        $SmsApi = new \Sms\Api\SmsApi();
        $result = $SmsApi->send($info['phone'],$msg);

     }


    public function friend_success(){
        include template('success');

    }

    /* 检测手机号是否可用 */
    public function public_checkphone_ajax(){
        $mobile = I('phone');
        if(!is_mobile($mobile)) {
            $this->error('手机号码格式错误');
        }
        $sqlmap = array();
        $sqlmap['phone'] = $mobile;
        if(model('member')->where($sqlmap)->count() > 0) {
            $this->error('该手机号已被占用');
        } else {
            $this->success('该手机号可用');
        }        
    }

    public function  send_sms($mobile = '',$modelid = 1){
        $setting = getcache('setting', 'member');
        $beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
        $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
        $sqlmap = array();
        $sqlmap['posttime'] = array('between',array($beginToday,$endToday));
        $sqlmap['mobile'] = $mobile;
        $sqlmap['enum'] = 'register';
        $count = model('sms_report')->where($sqlmap)->count();
        $lastSms = model('sms_report')->where($sqlmap)->order('id DESC')->find();
        if (($lastSms['posttime']+60) > NOW_TIME) $this->error('请等待60秒后再获取...');
        $conditions = array();
        $conditions['posttime'] = array('between',array($beginToday,$endToday));
        $conditions['ip'] = get_client_ip();
        $ip_count = model('sms_report')->where($conditions )->count();

        if (intval($count) > 5) {
            $this->error('同一号码，每天只能发送5次，请明日再尝试');
            return false;
        }

        if (intval($ip_count) > 10) {
            $this->error('今日发送短信条数已用完,请明日再尝试');
        }
        if(is_mobile(trim($mobile)) != TRUE) {
            $this->error('手机号格式不正确');
            return false;
        }
        
        /* 手机号码已被注册的不能发送短信 */
        if(model('member')->where(array('phone' => $mobile))->count() > 0) {
             $this->error('该手机号已被占用');

        }

        /* 检测当前手机的发送日期 */
        $_vcode = random(6, 1);
        $msg =  '验证码'.$_vcode.',您正在注册成为'.C('webname').'用户，感谢您的支持';

        $SmsApi = new \Sms\Api\SmsApi();
        
        $arr = array();
        $product = C('webname');
        $arr['param'] = "{'code':'$_vcode','product':'$product'}";
        $arr['template_id'] = C('template_id_1');
        $result = $SmsApi->send($mobile, $msg,$arr);
        if(!$result) {
            $this->error('手机短信发送失败，请重试。');
        } else {
            $info = array();
            $info['mobile'] = $mobile;
            $info['posttime'] = NOW_TIME;
            $info['id_code'] = $_vcode;
            $info['msg'] = $msg;
            $info['ip'] = get_client_ip();
            $info['enum'] = 'register';
            model('sms_report')->update($info);
            $this->success(M()->getdberror());
        }
    }

    public function download(){
        include template('download');
    }
}