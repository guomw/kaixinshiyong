<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
 **/

namespace Member\Controller;

use \Common\Controller\BaseController;

if (!defined('MODULE_CACHE')) define('MODULE_CACHE', DATA_PATH . 'caches_model/');

class IndexController extends BaseController
{
    public function _initialize()
    {
        parent::_initialize();
        $this->db = model('member');
        $this->setting = D('Setting');
        $this->module_db = D('Module');
    }

    public function index()
    {


        redirect(U('Usercp/index'));
    }

    public function mini()
    {
    }

    /**
     * 会员注册
     * @author   <jason>
     */
    public function register()
    {
        if (I('agent_id') > 0) cookie('_agent_id', I('agent_id'), 86400);
        $models = getcache('model', 'commons');
        $settings = getcache('setting', 'member');
        $agent_id = cookie('_agent_id') > 0 ? cookie('_agent_id') : '';
        $modelid = I('modelid', '1', 'intval');
        if (!in_array($modelid, $settings['setting_register_enable'])) {
            $this->error('当前未开启' . $models[$modelid]['name'] . '注册');
        }
        $setting = getcache('setting', 'member');
        if (IS_POST) {
            $info = $_POST['info'];
            $MemberLogic = D('Member', 'Logic');
            $result = $MemberLogic->register($info);
            if (!$result) {
                $this->error($MemberLogic->getError());
            } else {
                $userid = $result;
                /*手机认证送积分*/
                if ($info['agent_id'] > 0 && in_array($modelid, $settings['setting_register_sms_enable'])) {
                    runhook('member_attesta_phone', array('userid' => $userid));
                }

                if (in_array($modelid, $settings['setting_register_email_enable'])) {
                    $data = array();
                    $data['userid'] = $userid;
                    $data['modelid'] = $modelid;
                    $data['email'] = $info['email'];
                    // 激活邮箱
                    runhook('member_email_activate', $data);
                }
                // 发送站内信
                runhook('member_email_webcome', array('userid' => $userid));
                // 奖励积分
                runhook('member_register_success', array('userid' => $userid));

                $SEO = seo(0, "账户注册成功", '用户注册成功', '用户注册成功');
                include template('register_success');
            }
        } else {
            if ($modelid == 1) {
                $msg = '用户注册';
            } else {
                $msg = '商家注册';
            }
            $SEO = seo(0, $msg, '', '');
            include template('register');
        }
    }

    /**
     * 会员登陆
     * @author   <jason>
     */
    public function login()
    {
        $refresh = I('refresh', '');

        if (IS_POST) {
            $refresh = $info['refresh'];
            if (!$refresh) $refresh = U('Member/Profile/index');
            $info = I('post.');

            //检测整合平台是否开启
            if (C('sso_is_open') == 1) {
                $ret = _ps_send('login', $info);

                $data = php_data($ret);
                if ($data['status'] == 1) {
                    cookie('_userid', $data['userid'], 86400);
                    cookie('_groupid', $data['groupid'], 86400);
                    cookie('_modelid', $data['modelid'], 86400);
                    $this->success('登录成功', urldecode($refresh));
                } else {
                    $this->error('登录失败 用户名或密码错误', urldecode($refresh));
                }
            } else {


                $MemberLogic = D('Member', 'Logic');
                $result = $MemberLogic->login($info);
                if (!$result) {
                    $this->error($MemberLogic->getError());
                }
                $userid = cookie('_userid');
                if ($info['remember'] == 1) {
                    cookie('_username', $info['username'], 86400);
                }


                $this->success('登录成功', urldecode($refresh));

            }


        } else {
            $SEO = seo(0, "用户登录");
            include template('login');
        }
    }

    /**
     * 会员登出
     * @author   <jason>
     */
    public function logout()
    {
        cookie('_userid', NULL);
        cookie('_groupid', 8);
        $this->success('操作成功', U('login'));
    }

    /**
     * 邮件认证
     * @author   <jason>
     */
    public function verify_email()
    {
        $code = I('code');
        if (empty($code)) {
            $this->error('操作失败');
        }
        $string = authcode($code, 'DECODE');
        list($uid, $code) = explode("|", $string);
        $uid = (int)$uid;
        if ($uid < 1 || $code != md5(C('AUTHKEY'))) {
            $this->error('认证编码错误');
        }
        $userinfo = getUserInfo($uid);
        $this->db->where(array('userid' => $uid))->setField('email_status', 1);
        if ($userinfo['email_status'] == 0) {
            runhook('member_attesta_email');
        }
        $this->success('您的邮箱认证成功', U('Member/Profile/index'));
    }

    /**
     * 找回密码
     * @author   <jason>
     */
    public function forget()
    {
        if (IS_POST) {
            $info = I('post.');
            if ($info) {
                $email = trim(remove_xss($info['username']));
                if (empty($email)) {
                    $this->error('账号不能为空');
                }
                $map['email|phone'] = $info['username'];
                $result = $this->db->where($map)->find();
                if (!$result) {
                    $this->error('没有找到此用户');
                }
                $data = array();
                $data['userid'] = $result['userid'];
                if ($result['nickname']) {
                    $data['account'] = $result['nickname'];
                } else {
                    $data['account'] = $result['email'];
                }
                $data['email'] = $result['email'];
                if ($result['email']) {
                    runhook('member_email_retpwd', $data);
                    cookie('forget_email', $result['email']);
                    redirect(U('success_email'));
                } elseif ($result['phone'] && !$result['email']) {
                    cookie('forget_phone', $result['phone']);
                    redirect(U('forget_phone'));

                }
                // 发送邮件

            }
        } else {
            $SEO = seo(0, "填写账户信息-忘记密码");
            include template('forget');
        }
    }

    public function forget_phone()
    {
        $phone = cookie('forget_phone');
        if (!$phone) redirect(U('forget'));
        if (IS_POST) {
            $info = I('post.');
            $MemberLogic = D('Member', 'Logic');
            if (!is_mobile($info['phone'])) {
                $this->error('手机格式错误！');
                return FALSE;
            }

            if (!$MemberLogic->register_check_sms($info['phone'], $info['sms'], TRUE)) {
                $this->error('验证码输入错误');
                return FALSE;
            }


            $count = model('sms_report')->where(array('mobile' => $info['phone'], 'id_code' => $info['sms'], 'status' => 1))->count();
            if ($count) {

                $this->success('验证通过', U('forget_set_phone'));
            } else {

                $this->success('验证失败', U('forget_phone'));


            }


        }
        $SEO = seo(0, "找回密码-验证身份");

        include template('forget_phone');
    }


    public function forget_set_phone()
    {
        $SEO = seo(0, "找回密码-重置密码");
        $sqlmap = array();
        $sqlmap['phone'] = cookie('forget_phone');
        $uid = model('member')->where($sqlmap)->getField('userid');
        if (!$uid) {
            redirect(U('forget'));
        }

        if (IS_POST) {
            $info = I('post.');
            $userinfo = $this->db->find($info['uid']);
            if (!$userinfo) {
                $this->error('用户信息错误');
            }
            if (empty($info['password'])) {
                $this->error('新密码不能为空');
            }
            if ($info['password'] != $info['pwdconfirm']) {
                $this->error('两次密码不一致！！');
            }
            $password = md5(md5($info['password'] . $userinfo['encrypt']));
            $result = $this->db->where(array('userid' => $userinfo['userid']))->setField('password', $password);
            if (!$result) {
                $this->error('密码修改失败');
            } else {
                cookie('forget_phone', NULL);
                $SEO = seo(0, "找回密码-已成功，请牢记");
                include template('reset_success');
            }
        }
        include template('set_phone_pwd');
    }

    /* 找回密码 再发一封 */
    public function success_email()
    {
        $email = cookie('forget_email');
        if (!$email) redirect(U('forget'));
        if (IS_POST) {
            $info = I('post.');
            if (!$info['username']) return false;
            $map['email|phone'] = $info['username'];
            $result = $this->db->where($map)->find();
            if (!$result) {
                $this->error('没有找到此用户');
            }
            $data = array();
            $data['userid'] = $result['userid'];
            if ($result['nickname']) {
                $data['account'] = $result['nickname'];
            } else {
                $data['account'] = $result['email'];
            }
            $data['email'] = $result['email'];
            // 发送邮件
            runhook('member_email_retpwd', $data);
            cookie('forget_email', $result['email']);
            $this->success('邮箱已成功发送');
        }
        $SEO = seo(0, "找回密码-验证身份");
        include template('email_success');
    }

    public function forget_step_two()
    {
        $email = cookie('forget_email');
        if (!$email) {
            $this->error('请勿非法访问');
        }
        include template('email_success');
    }

    /* 找回密码：验证身份 */
    public function forget_resetpwd()
    {
        if (IS_POST) {
            $info = I('post.');
            $userinfo = $this->db->find($info['uid']);
            if (empty($info['password'])) {
                $this->error('新密码不能为空');
            }
            if ($info['password'] != $info['pwdconfirm']) {
                $this->error('两次密码不一致！！');
            }
            $password = md5(md5($info['password'] . $userinfo['encrypt']));
            $result = $this->db->where(array('userid' => $userinfo['userid']))->setField('password', $password);
            if (!$result) {
                $this->error('密码修改失败');
            } else {
                cookie('forget_email', NULL);
                $SEO = seo(0, "找回密码-已成功，请牢记");
                include template('reset_success');
            }
        } else {
            $email = I('email');
            $code = I('key');
            $time = I('posttime');
            if (NOW_TIME - ($time + 86400) > 0) {
                $this->error('该链接已失效,请重新请求', U('forget'));
            }
            if (empty($code) && empty($email)) {
                $this->error('请勿非法访问', U('forget'));
            }
            $string = authcode($code, 'DECODE');
            list($uid, $code) = explode("|", $string);
            $uid = (int)$uid;
            if ($uid < 1 || $code != md5(C('AUTHKEY'))) {
                $this->error('重置编码错误');
            }
            $SEO = seo(0, "找回密码-重置密码");
            include template('reset_password');
        }
    }

    /**
     * 重新设置密码
     * @author   <jason>
     */
    public function resetpwd()
    {
        $userid = session('_forget_userid_');
        $userinfo = $this->db->getByUserid($userid);
        if (IS_POST) {
            $info = I('post.');
            if (empty($info['password'])) {
                $this->error('新密码不能为空');
            }
            if ($info['password'] != $info['2password']) {
                $this->error('两次密码不一致！！');
            }
            $password = md5(md5($info['password'] . $userinfo['encrypt']));
            $result = $this->db->where(array('userid' => $userid))->setField('password', $password);
            if (!$result) {
                $this->error('密码修改失败');
            } else {
                session('_forget_userid_', NULL);
                $this->success('操作成功', U('phone_success'));
            }
        } else {
            include template('forget/resetpwd');
        }
    }

    public function phone_success()
    {
        include template('forget/phone_success');
    }

    public function email_success()
    {
        $email = cookie('forget_email');
        if ($email) {
            cookie('forget_email', NULL);
            include template('forget/email_success');
        } else {
            $this->error('请勿非法访问');
        }
    }


    public function forget_send_sms($mobile = '')
    {
        if (is_mobile(trim($mobile)) != TRUE) {
            $this->error('手机号格式不正确');
            return false;
        }
        $beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $endToday = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
        $sqlmap = array();
        $sqlmap['posttime'] = array('between', array($beginToday, $endToday));
        $sqlmap['mobile'] = $mobile;
        $sqlmap['enum'] = 'password';
        $count = model('sms_report')->where($sqlmap)->count();
        $lastSms = model('sms_report')->where($sqlmap)->order('id DESC')->find();
        if (($lastSms['posttime'] + 60) > NOW_TIME) $this->error('请等待60秒后再获取...');
        $conditions = array();
        $conditions['posttime'] = array('between', array($beginToday, $endToday));
        $conditions['ip'] = get_client_ip();
        $ip_count = model('sms_report')->where($conditions)->count();
        if (intval($count) > 3) {
            $this->error('同一号码，每天只能发送3次，请明日再尝试');
            return false;
        }

        if (intval($ip_count) > 5) {
            $this->error('今日发送短信条数已用完,请明日再尝试');
        }


        /* 检测当前手机的发送日期 */
        $_vcode = random(6, 1);
        $msg = '亲，您的手机验证码为' . $_vcode;
        $SmsApi = new \Sms\Api\SmsApi();
        $arr = array();
        $arr['param'] = "{'code':'$_vcode'}";
        $arr['template_id'] = C('template_id_2');
        $result = $SmsApi->send($mobile, $msg, $arr);
        // $result = $SmsApi->send($mobile, $msg);
        // $result = TRUE;
        if (!$result) {
            $this->error('手机短信发送失败，请重试。');
        } else {
            $info = array();
            $info['mobile'] = $mobile;
            $info['posttime'] = NOW_TIME;
            $info['id_code'] = $_vcode;
            $info['msg'] = $msg;
            $info['ip'] = get_client_ip();
            $info['enum'] = 'password';
            model('sms_report')->update($info);
            $this->success(M()->getdberror());
        }
    }

    /**
     * 发放手机验证码
     * todo::对同时间重复发送进行校验
     * @param string $mobile 接收者手机号
     */
    public function public_send_sms($mobile = '', $verify = '', $modelid = '')
    {
        $setting = getcache('setting', 'member');
        if (in_array($modelid, $setting['setting_register_sms_enable']) == false) {
            return false;
        }

        $beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $endToday = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
        $sqlmap = array();
        $sqlmap['posttime'] = array('between', array($beginToday, $endToday));
        $sqlmap['mobile'] = $mobile;
        $sqlmap['enum'] = 'register';
        $count = model('sms_report')->where($sqlmap)->count();
        $lastSms = model('sms_report')->where($sqlmap)->order('id DESC')->find();
        if (($lastSms['posttime'] + 60) > NOW_TIME) $this->error('请等待60秒后再获取...');
        $conditions = array();
        $conditions['posttime'] = array('between', array($beginToday, $endToday));
        $conditions['ip'] = get_client_ip();
        $ip_count = model('sms_report')->where($conditions)->count();


        if (!in_array($modelid, $setting['setting_register_sms_enable'])) {

            $this->error('已关闭手机注册');
            return false;

        }

        if (intval($count) > 3) {
            $this->error('同一号码，每天只能发送3次，请明日再尝试');
            return false;
        }

        if (intval($ip_count) > 10) {
            $this->error('今日发送短信条数已用完,请明日再尝试');
        }
        if (is_mobile(trim($mobile)) != TRUE) {
            $this->error('手机号格式不正确');
            return false;
        }

        if (in_array($modelid, $setting['setting_register_verify_enable'])) {
            $verify = strtolower($verify);
            if (checkVerify($verify, FALSE) == FALSE) {
                $this->error('验证码输入错误,请重新输入');
                return false;
            }
        }


        /* 手机号码已被注册的不能发送短信 */
        if (model('member')->where(array('phone' => $mobile))->count() > 0) {
            $this->error('该手机号已被占用');
        }

        /* 检测当前手机的发送日期 */
        $_vcode = random(6, 1);
        $msg = '感谢您注册,您的验证码为' . $_vcode;
        $SmsApi = new \Sms\Api\SmsApi();
        $arr = array();
        $arr['param'] = "{'code':'$_vcode'}";
        $arr['template_id'] = C('template_id_1');
        $result = $SmsApi->send($mobile, $msg, $arr);
        // $result = $SmsApi->send($mobile, $msg);
        // $result = TRUE;
        if (!$result) {
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

    /**
     * 检测手机验证码
     */
    public function public_check_sms()
    {
        $param = I('param.');
        $MemberLogic = D('Member', 'Logic');
        $result = $MemberLogic->register_check_sms($param['mobile'], $param['sms'], FALSE);
        if (!$result) {
            $this->error($MemberLogic->getError());
        } else {
            $this->success('验证码输入正确');
        }
    }


    public function send_email()
    {
        $userid = cookie('_userid');
        helpers('mail');
        $setting = getcache('setting', 'member');
        $info = $this->db->find($userid);
        helpers('mail');
        $code = authcode($userid . '|' . md5(C('AUTHKEY')), 'ENCODE');
        $url = U('Member/Index/verify_email', array('code' => $code), '', TRUE);
        $message = $setting['registerverifymessage'];
        $arr = array('{webname}' => C('webname'), '{username}' => $info['username'], '{email}' => $info['email'], '{url}' => $url, '{siteurl}' => HTTP_HOST);
        $message = str_replace(array_keys($arr), $arr, $message);
        sendmail($info['email'], "邮箱认证", $message);
    }

    /* 检测邮箱是否被占用 */
    public function public_checkemail_ajax($email = '')
    {
        if (!isemail($email)) {
            $this->error('Email格式错误');
        }

        if (C('sso_is_open') == 1) {
            $ret = _get_send('check_email', array('email' => $email));
            if ($ret) {
                $this->success('Email可用');
            } else {
                $this->error('Email已被占用');
            }

        } else {
            $sqlmap = array();
            $sqlmap['email'] = $email;
            if (model('member')->where($sqlmap)->count() > 0) {
                $this->error('Email已被占用');
            } else {
                $this->success('Email可用');
            }
        }


    }

    /* 检测手机号是否可用 */
    public function public_checkphone_ajax()
    {
        $mobile = $_GET['phone'];
        if (!is_mobile($mobile)) {
            $this->error('手机号码格式错误');
        }

        if (C('sso_is_open') == 1) {
            $ret = _get_send('check_phone', array('phone' => $mobile));
            if ($ret) {
                $this->success('手机号可用');
            } else {
                $this->error('手机号已被占用 请更换！');
            }

        } else {
            $sqlmap = array();
            $sqlmap['phone'] = $mobile;
            if (model('member')->where($sqlmap)->count() > 0) {
                $this->error('该手机号已被占用');
            } else {
                $this->success('该手机号可用');
            }

        }


    }

    /* 检测验证码是否正确 */
    public function public_checkverify_ajax($verify = '')
    {
        $verify = strtolower($verify);
        if (checkVerify($verify, FALSE) == TRUE) {
            $this->success('验证码输入正确');
        } else {
            $this->error('验证码输入错误');
        }
    }

    public function check_exist_name()
    {
        $username = $_GET['username'];
        if (!$username) return false;
        $sqlmap = array();
        $sqlmap['phone|email'] = $username;
        if (model('member')->where($sqlmap)->count() > 0) {
            $this->success('输入正确');
        } else {
            $this->error('该用户不存在');
        }

    }

    /*使用达人人气增加*/
    public function addpopu($id = 0)
    {
        $id = (int)$id;
        if ($id < 1) $this->error('参数错误');
        $userid = cookie('_userid');
        if ($userid < 1) $this->error('您没有登录，请先登录', U('Member/Index/login'));
        //判断是否点击过
        $isclick = cookie('addp_' . $userid . 'id_' . $id);
        if (isset($isclick)) $this->error('亲，您已经点击过了，明天再来吧');
        $result = model('report_member')->where(array('id' => $id))->setInc('popularity', 1);
        if (!$result) {
            $this->error('新增人气失败');
        } else {
            cookie('addp_' . $userid . 'id_' . $id, 1, 60 * 60 * 24);
            $this->success('新增成功');
        }
    }


// ----------------------------------- 分步骤注册 ---------------------------------------

    /**
     * 注册首页
     * @author   <jason>
     */
    public function register_index()
    {
        $SEO = seo(0, "注册首页");
        include template('register_index');
    }

    /* 服务协议 */
    public function agreement()
    {
        $SEO = seo(0, "服务协议");
        include template('agreement');
    }

    /**
     * 会员注册
     * @author   <jason>
     */
    public function register_()
    {
        $models = getcache('model', 'commons');
        $settings = getcache('setting', 'member');
        $agent_id = cookie('_agent_id') > 0 ? cookie('_agent_id') : '';
        $modelid = I('modelid', '1', 'intval');
        if (!in_array($modelid, $settings['setting_register_enable'])) {
            $this->error('当前未开启' . $models[$modelid]['name'] . '注册');
        }
        $setting = getcache('setting', 'member');
        if (IS_POST) {
            $info = $_POST['info'];
            if ($info['password'] !== $info['pwdconfirm']) $this->error('两次密码不一致！！');
            $MemberLogic = D('Member', 'Logic');
            $result = $MemberLogic->register_($info);
            if (!$result) {
                $this->error($MemberLogic->getError());
            } else {
                $userid = $result;
                /* 开启邮箱激活 */
                if (in_array($modelid, $settings['setting_register_email_enable'])) {
                    $data = array();
                    $data['userid'] = $userid;
                    $data['modelid'] = $modelid;
                    $data['email'] = $info['email'];
                    // 激活邮箱
                    runhook('member_email_activate', $data);
                }
                /* 开启手机认证 */
                if (in_array($modelid, $setting['setting_register_sms_enable'])) {
                    $this->register_phone($userid);
                } else {
                    // 发送站内信
                    runhook('member_email_webcome', array('userid' => $userid));
                    // 奖励积分
                    runhook('member_register_success', array('userid' => $userid));
                    $SEO = seo(0, "账户注册成功", '用户注册成功', '用户注册成功');
                    $memberInfo = member_info($userid);
                    include template('register_success');
                }
            }
        } else {
            if ($modelid == 1) {
                $msg = '用户注册';
            } else {
                $msg = '商家注册';
            }
            $SEO = seo(0, $msg);
            include template('register');
        }
    }

    /* 注册第二步：手机认证 */
    public function register_phone($userid = '')
    {
        $SEO = seo(0, '注册-手机认证');
        if (cookie('_userid') != trim($userid) || !trim($userid)) $this->error('请注册会员！', U('register_index'));
        include template('register2');
    }

    /* 注册第三步：手机认证并注册成功 */
    public function register_success()
    {
        if (IS_POST) {
            $info = $_POST['info'];
            $MemberLogic = D('Member', 'Logic');
            $result = $MemberLogic->register_check_sms($info['phone'], $info['sms'], FALSE);
            if (!$result) $this->error($MemberLogic->getError());
            // 把手机号码写入表里并更改手机认证状态
            $data = array();
            $data['phone'] = $info['phone'];
            $data['phone_status'] = 1;
            $this->db->where(array('userid' => $info['userid']))->save($data);
            $memberInfo = member_info($info['userid']);
            $SEO = seo(0, '注册成功');
            include template('register_success');
        } else {
            $this->error('请注册会员！', U('register_index'));
        }
    }


    public function userregister()
    {

        if (I('agent_id') > 0) cookie('_agent_id', I('agent_id'), 86400);

        /*手机打开邀请好友链接*/
        $setting2 = getcache('setting', 'wap2');
        $http_host = $_SERVER['HTTP_HOST'];
        $wap_domain = ltrim($setting2['wap_domain'], "'http://'");
        $detect = new \Wap\Library\Mobile_Detect();
        if ($detect->isMobile() && C('system_auth_type') == 'professional') {
            redirect('http://' . $_SERVER['HTTP_HOST'] . '/yq/' . I('agent_id'));
            return false;
        }


        $models = getcache('model', 'commons');
        $settings = getcache('setting', 'member');
        $agent_id = cookie('_agent_id') > 0 ? cookie('_agent_id') : '';
        $modelid = I('modelid', '1', 'intval');
        /*if (!in_array($modelid, $settings['setting_register_v2_email']) && in_array($modelid, $settings['setting_register_v2_phone'])) {
         	$this->error('已关闭邮箱注册，请使用手机注册',U('v2_register_phone',array('modelid'=>$modelid,'agent_id'=>$agent_id)));

         }elseif (!in_array($modelid, $settings['setting_register_v2_email']) && !in_array($modelid, $settings['setting_register_v2_phone'])) {
         	$this->error('抱歉，平台已关闭注册',__APP__);
         	
         }*/


        if (IS_POST) {
            $info = I('post.');
            $MemberLogic = D('Member', 'Logic');
            $modelid = (int)$info['modelid'];
            if (!isemail($info['email'])) {
                $this->error('邮箱格式错误！');
                return FALSE;
            }

            if (!checkVerify(strtolower($info['verify']))) {
                $this->error('验证码不正确');
                return false;
            }


            $info['encrypt'] = random(6);
            $info['point'] = (int)0;
            $info['groupid'] = (isset($info['groupid']) && is_numeric($info['groupid']) && $info['groupid'] > 1) ? $info['groupid'] : 1;

            if (c('sso_is_open') == 1) {

                $ret = _ps_send('register', $info);

                $data = php_data($ret);

                switch ($data['status']) {

                    case '-2':
                        # code...
                        $this->error('email格式错误');
                        break;
                    case '-3':
                        # code...
                        $this->error('密码未填写');
                        break;
                    case '-4':
                        # code...
                        $this->error('手机格式错误');
                        break;

                    case '-5':
                        # code...
                        $this->error('email 被占用');
                        break;
                    case '1':
                        /*	云平台注册成功*/
                        $info['userid'] = $data['userid'];
                        break;
                    default:
                        $this->error('注册失败 请重试！ ' . $ret);
                        break;
                }

            }


            $userid = model('member')->update($info);
            if (!$userid) {
                $this->error('服务器繁忙，请重新注册');
                return FALSE;
            } else {
                $MemberLogic->publogin($userid);
                /*手机认证送积分*/
                if ($info['agent_id'] > 0 && in_array($modelid, $settings['setting_register_sms_enable'])) {
                    runhook('member_attesta_phone', array('userid' => $userid));

                }

                if ($info['agent_id'] > 0) {
                    $arr = $this->level($agent_id);
                    model('member')->where(array('userid' => $userid))->save(array('levels' => array2string($arr)));
                }


                // 发送站内信
                runhook('member_email_webcome', array('userid' => $userid));
                // 新人注册奖励任务
                runhook('member_register_success', array('userid' => $userid));

                $this->success('注册成功，请继续完善验证资料', U('check_email'));

            }
        }
        include template('v2_register');
    }


    //推荐我的人
    public function level($agent_id)
    {

        $arr = array();
        array_push($arr, $agent_id);//33
        $agent_2 = model('member')->field('agent_id')->where(array('userid' => $agent_id))->find();//30
        if ($agent_2['agent_id'] > 0) {
            array_push($arr, $agent_2['agent_id']);
            $agent_3 = model('member')->field('agent_id')->where(array('userid' => $agent_2['agent_id']))->find();//4
            if ($agent_3['agent_id'] > 0) {
                array_push($arr, $agent_3['agent_id']);
            }

            $agent_4 = model('member')->field('agent_id')->where(array('userid' => $agent_3['agent_id']))->find();//4
            if ($agent_4['agent_id'] > 0) {
                array_push($arr, $agent_4['agent_id']);
            }

        }
        return $arr;

    }


    public function v2_register_phone()
    {
        $models = getcache('model', 'commons');
        $settings = getcache('setting', 'member');
        $agent_id = cookie('_agent_id') > 0 ? cookie('_agent_id') : '';
        $modelid = I('modelid', '1', 'intval');
        /*if (!in_array($modelid, $settings['setting_register_v2_phone']) && in_array($modelid, $settings['setting_register_v2_email']) ) {
             $this->error('已关闭手机注册，请使用邮箱注册',U('userregister',array('modelid'=>$modelid,'agent_id'=>$agent_id)));

         }elseif(!in_array($modelid, $settings['setting_register_v2_phone']) && !in_array($modelid, $settings['setting_register_v2_email']) ){
             $this->error('抱歉，平台已关闭注册',__APP__);
         }*/
        if (IS_POST) {
            $info = I('post.');
            $MemberLogic = D('Member', 'Logic');
            $modelid = (int)$info['modelid'];
            if (!is_mobile($info['phone'])) {
                $this->error('手机格式错误！');
                return FALSE;
            }

            /**
             * begin
             * NEW-ADD-GMW
             * 2018-08-09 17:00
             * 因滑动验证码不能用，所以此处添加图形验证码判断，降低短信被刷的几率
             */
            if (!checkVerify(strtolower($info['verify']))) {
                $this->error('发送失败 请重新获取！');
                return false;
            }
            /**end*/


            if (!$MemberLogic->register_check_sms($info['phone'], $info['sms'], TRUE)) {
                $this->error('验证码输入错误');
                return FALSE;
            } else {
                /* 定义手机为已验证 */
                $info['phone_status'] = 1;
            }


            $info['encrypt'] = random(6);
            $info['point'] = (int)0;
            $info['groupid'] = (isset($info['groupid']) && is_numeric($info['groupid']) && $info['groupid'] > 1) ? $info['groupid'] : 1;

            if (c('sso_is_open') == 1) {

                $ret = _ps_send('register', $info);

                $data = php_data($ret);


                switch ($data['status']) {

                    case '-2':
                        # code...
                        $this->error('email格式错误');
                        break;
                    case '-3':
                        # code...
                        $this->error('密码未填写');
                        break;
                    case '-4':
                        # code...
                        $this->error('手机格式错误');
                        break;

                    case '-5':
                        # code...
                        $this->error('email 被占用');
                        break;
                    case '1':
                        /*	云平台注册成功*/
                        $info['userid'] = $data['userid'];
                        break;
                    default:
                        $this->error('注册失败 请重试！ ' . $ret);
                        break;
                }

            }

            $userid = model('member')->update($info);
            if (!$userid) {
                $this->error('服务器繁忙，请稍后在试');
                return FALSE;
            } else {
                $MemberLogic->publogin($userid);
                /*手机认证送积分*/
                if ($info['agent_id'] > 0 && in_array($modelid, $settings['setting_register_sms_enable'])) {
                    runhook('member_attesta_phone', array('userid' => $userid));
                }

                if ($info['agent_id'] > 0) {
                    $arr = $this->level($agent_id);
                    model('member')->where(array('userid' => $userid))->save(array('levels' => array2string($arr)));
                }

                $phone_status = model('member')->where(array('userid' => $userid))->getField('phone_status');
                if ($phone_status == 1) {
                    runhook('member_attesta_phone');
                }

                // 发送站内信
                runhook('member_email_webcome', array('userid' => $userid));
                // 奖励积分
                runhook('member_register_success', array('userid' => $userid));

                $this->success('注册成功', U('v2_register_suc'));

                include template('v2_check_email');
            }


        }


        include template('v2_register_phone');
    }

    public function v2_register_suc()
    {
        $userid = cookie('_userid');
        $userinfo = getUserInfo($userid);
        include template('v2_success');

    }

    public function check_email()
    {
        $userid = cookie('_userid');
        $modelid = getUserInfo($userid, 'modelid');
        $new_email = getUserInfo($userid, 'email');

        if (IS_POST) {
            $info = I('post.');
            extract($info);
            $email_log = model('email_log')->where(array('email' => $email, 'code' => $code))->order('id DESC')->find();
            if (!$email_log) {
                $this->error('该验证码不存在，请重新获取');
                exit();
            }
            if (NOW_TIME > ($email_log['posttime'] + 5 * 60)) {
                $this->error('验证码已失效，请重新获取过');
                exit();
            }

            $rs = model('member')->where(array('userid' => $userid))->setField('email_status', 1);
            if ($rs) {
                runhook('member_attesta_email');
                $this->success('验证通过', U('v2_register_suc'));
            } else {
                $this->error('系统繁忙，请稍后再试');
            }

        }
        include template('v2_check_email');

    }


    public function v2_send_sms($mobile = '', $modelid = 1, $key = "")
    {
        $setting = getcache('setting', 'member');
        $beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $endToday = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
        $sqlmap = array();
        $sqlmap['posttime'] = array('between', array($beginToday, $endToday));
        $sqlmap['mobile'] = $mobile;
        $sqlmap['enum'] = 'register';
        $count = model('sms_report')->where($sqlmap)->count();
        $lastSms = model('sms_report')->where($sqlmap)->order('id DESC')->find();
        if (($lastSms['posttime'] + 60) > NOW_TIME) $this->error('请等待60秒后再获取...');
        $conditions = array();
        $conditions['posttime'] = array('between', array($beginToday, $endToday));
        $conditions['ip'] = get_client_ip();
        $ip_count = model('sms_report')->where($conditions)->count();


        if (!in_array($modelid, $setting['setting_register_v2_phone'])) {
            $this->error('已关闭手机注册');
            return false;

        }

        if (intval($count) > 5) {
            $this->error('同一号码，每天只能发送5次，请明日再尝试');
            return false;
        }

        if (intval($ip_count) > 10) {
            $this->error('今日发送短信条数已用完,请明日再尝试');
        }
        if (is_mobile(trim($mobile)) != TRUE) {
            $this->error('手机号格式不正确');
            return false;
        }


        //获取缓存key
        $key1 = S($key);
        if ($key == "" || $key1 != $key) {
            $this->error('发送失败 请重新获取key！');
        }
        /**
         * begin
         * NEW-ADD-GMW
         * 2018-08-09 17:00
         * 因滑动验证码不能用，所以此处添加图形验证码判断，降低短信被刷的几率
         */
        if (!checkVerify(strtolower($key1), false)) {
            $this->error('发送失败 请重新获取！');
            return false;
        }
        /**end*/


        /* 手机号码已被注册的不能发送短信 */
        if (model('member')->where(array('phone' => $mobile))->count() > 0) {
            $this->error('该手机号已被占用');
        }

        /* 检测当前手机的发送日期 */
        $_vcode = random(6, 1);
        $msg = '验证码' . $_vcode . ',您正在注册成为' . C('webname') . '用户，感谢您的支持';
        $SmsApi = new \Sms\Api\SmsApi();
        $arr = array();
        $product = C('webname');
        $arr['param'] = "{'code':'$_vcode','product':'$product'}";
        $arr['template_id'] = C('template_id_1');
        $result = $SmsApi->send($mobile, $msg, $arr);
        // $result = $SmsApi->send($mobile, $msg);
        // $result = TRUE;
        if (!$result) {
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

    public function v2_send_email($email = '', $userid = '')
    {
        $beginToday = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
        $endToday = mktime(0, 0, 0, date('m'), date('d') + 1, date('Y')) - 1;
        $userid =cookie('_userid');
        $sqlmap = array();
        if($userid&&$userid>0)
        $email = getUserInfo($userid, 'email');

        $sqlmap['posttime'] = array('between', array($beginToday, $endToday));
        $sqlmap['email'] = $email;

        if ($userid) {
            $sqlmap['userid'] = $userid;
        }
        $lastSms = model('email_log')->where($sqlmap)->order('id DESC')->find();
        if (($lastSms['posttime'] + 60) > NOW_TIME) {
            $this->error('请等待60秒后再获取');
            exit();
        }


        $count = model('email_log')->where($sqlmap)->count();
        if ($count > 3) {
            $this->error('今日发送条数已用完');
            exit();
        }


        $code = random(6, 1);
        $message = '您的验证码为' . $code . "。该验证码有效期为5分钟，请勿向任何人提供那您接收到的验证码信息";
        helpers('mail');
        $result = sendmail($email, "邮箱验证", $message);
        if ($result) {
            $info = array();
            $info['email'] = $email;
            $info['posttime'] = NOW_TIME;
            $info['code'] = $code;
            $info['msg'] = $message;
            $info['status'] = 1;
            $info['userid'] = $userid;
            model('email_log')->add($info);
            $this->success('发送成功,请留意查收,在收件箱或者垃圾箱');
        } else {
            $this->error('发送失败,邮箱不存在,请仔细检查');
        }
    }


    public function sms_appkey($appkey = "")
    {

        if (IS_POST) {
            $info = I('post.');
            $appkey = $info['key'];
        }
        if (!$appkey) return false;
        S($appkey, $appkey, 300);

        $this->success('成功');
    }

    public function public_ajax_check()
    {
        $settings = getcache('setting', 'member');
        $modelid = I('modelid', '1', 'intval');

        if (in_array($modelid, $settings['setting_register_v2_phone']) && !in_array($modelid, $settings['setting_register_v2_email'])) {
            $this->success('已关闭邮箱注册，请使用手机注册', U('v2_register_phone', array('modelid' => $modelid)));


        } elseif (in_array(!$modelid, $settings['setting_register_v2_phone']) && in_array($modelid, $settings['setting_register_v2_email'])) {
            $this->success('已关闭手机注册，请使用邮箱注册', U('userregister', array('modelid' => $modelid)));


        } elseif (!in_array($modelid, $settings['setting_register_v2_phone']) && !in_array($modelid, $settings['setting_register_v2_email'])) {
            $this->error('抱歉，平台已关闭注册', __APP__);
        } else {
            $this->success('验证通过', U('userregister', array('modelid' => $modelid, 'agent_id' => $agent_id)));
        }

    }


}
