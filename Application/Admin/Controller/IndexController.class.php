<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Admin\Controller;
use Common\Library\tree;
Class IndexController extends InitController {
    public function _initialize() {
    	parent::_initialize();
        $this->menu_db = model('node');
        $this->admin_panel_db = model('admin_panel');
    }

    public function init() {
        $rolelist = parent::get_rolelist(1);
        $menu = new tree();
        $menu->init($rolelist);
        $top_menu = $menu->get_child(0);
        if(session('roleid') == 1){
            if(C('system_auth_type') == 'professional') {
                $top_menu[99999] = array('id' => 99999, 'parentid' => 0, 'name' => '专业版', 'm' => 'Admin', 'c' => 'Professional', 'a' => 'config');
            }
        }


       // $top_menu[88888] = array('id' => 88888, 'parentid' => 0, 'name' => '旗舰版', 'm' => 'Admin', 'c' => 'Professional', 'a' => 'config');
        $adminpanel = $this->admin_panel_db->where(array('adminid' => session('userid')))->select();
        $roles = getcache('role', 'commons');
        $rolename = $roles[session('roleid')]['rolename'];
        $admin_username = cookie('admin_username');
        include $this->admin_tpl('index'); 
    }


    /**
     * 管理员登录
     * @author   <jason>
     */
    public function login() {
        $referer = (getgpc('referer')) ? getgpc('referer') : U('Admin/Index/init');
        if (IS_POST) {
            $info = I('post.');
            $username = addslashes(trim($info['username']));
            $password = addslashes(trim($info['password']));
            if (empty($username)) {
                $this->error('用户名不能为空');
            }
            if (empty($password)) {
                $this->error('密码不能为空');
            }

              /* 检验验证码 */
            /* 1、检查验证码开启状态 */
            if (!checkVerify(strtolower($info['verify']))) {
                $this->error('验证码不正确');
            }
            
            $this->admin_db = D('Admin');
            /* 校验数据开始 */
            $data = $this->admin_db->where(array('username' => $username))->find();
            if (!is_array($data) || $data['password'] != md5(md5($password.$data['encrypt']))) {
                $this->error('用户名或密码不正确');   
            }
            /* 写入登陆数据 */
            $updateAdminData = array (
                'userid'            => $data['userid'],
                'lastlogintime'     => NOW_TIME,
                'lastloginip'       => get_client_ip(),
                'logincount'        => $data['logincount'] + 1
            );
            if (!$this->admin_db->update($updateAdminData, FALSE)) {
                $this->error('登录异常，请稍候尝试.');
            }
            /* 标识登录状态 */
            // 写入SESSION
            session('userid', $data['userid']);
            session('roleid', $data['roleid']);
            session('FROMHASH', random(6)); //重新生成FROMHASH校验值
            session('lock_screen', 0);
            session('lock_screen_error', 0);
            cookie('admin_username', $data['username']);
            cookie('userid', $data['userid']);
            cookie('admin_email', $data['email']);
            $this->success('登陆成功', $referer);
        } else {
            $form = new \Common\Library\form();
            include $this->admin_tpl('login');
        }
    }

    /**
     * 注销登录
     * @author   <jason>
     */
    public function public_logout() {
        cookie('userid', 0);
        session('userid', null);
        session('admin_username', '');
        session('FROMHASH', random(6));
        $this->success('您已成功注销登录', U('login'));
    }

    // 调用左侧菜单
    public function public_menu_left($menuid = 1) {
        $rolelist = parent::get_rolelist(1);      
        $menu = new tree();
        $menu->init($rolelist);
        $datas = $menu->get_child($menuid);
        if (isset($_GET['parentid']) && $parentid = intval($_GET['parentid']) ? intval($_GET['parentid']) : 10) {
            foreach($datas as $_value) {
                if($parentid == $_value['id']) {
                    echo '<li id="_M'.$_value['id'].'" class="on top_menu"><a href="javascript:_M('.$_value['id'].',\''.U($_value['m'].'/'.$_value['c'].'/'.$_value['a'].$data).'\')" hidefocus="true" style="outline:none;">'.L($_value['name']).'</a></li>';
                    
                } else {
                    echo '<li id="_M'.$_value['id'].'" class="top_menu"><a href="javascript:_M('.$_value['id'].',\''.U($_value['m'].'/'.$_value['c'].'/'.$_value['a'].$data).'\')"  hidefocus="true" style="outline:none;">'.L($_value['name']).'</a></li>';
                }       
            }
        } else {
            include $this->admin_tpl('left');
        }        
    }

    /**
     * 首页右侧主体
     * @author   <jason>
     */
    public function public_main() {
        $userid = session('userid');
        $show_header = $show_pc_hash = 1;
        $pc_writeable = is_writable(RUNTIME_PATH);
        $roles = getcache('role', 'commons');
        $rolename = $roles[session('roleid')]['rolename'];
        $admin_username = cookie('admin_username');
        $r = D('Admin')->getByUserid($userid);
        $logintime = $r['lastlogintime'];
        $loginip = $r['lastloginip'];

        $logsize_warning = 1;
        $sysinfo = get_sysinfo();
        $sysinfo['mysqlv'] = mysql_get_server_info();
		include $this->admin_tpl('main');
    }

    /**
     * 维持 session 登陆状态
     */
    public function public_session_life() {
        $userid = session('userid');
        return true;
    }

    /**
     * 桌面锁定
     */
    public function public_lock_screen() {
        session('lock_screen', 1);
        exit();
    }

    /**
     * 锁屏解锁
     */
    public function public_login_screenlock($lock_password = '') {
        $r = $this->admin_db->getByUserid(session('userid'));
        $lock_screen_error = (session('?lock_screen_error') == FALSE) ? 0 : session('lock_screen_error');        
        if (empty($lock_password)) {
            $this->error('密码不能为空');
        }

        if ($lock_screen_error > 2) {
            $this->error('密码输入错误次数过多');
        }

        if (md5(md5($lock_password.$r['encrypt'])) != $r['password']) {
            session('lock_screen', 1);
            $lock_screen_error = $lock_screen_error + 1;
            session('lock_screen_error', $lock_screen_error);
            $this->error('密码输入错误，您还能再尝试' .(3-$lock_screen_error). '次');
        }
        session('lock_screen', 0);
        session('lock_screen_error', 0);
        $this->success('解锁成功');
    }

    /**
     * 当前位置
     * @author   <jason>
     */
    public function public_current_pos($menuid) {
        echo D('Node')->current_pos($menuid);
    }






    public function taobaoIP($clientIP){
        $taobaoIP = 'http://ip.taobao.com/service/getIpInfo.php?ip='.$clientIP;
        $IPinfo = json_decode(file_get_contents($taobaoIP));
        $province = $IPinfo->data->region;
        $city = $IPinfo->data->city;
        $data = $province.$city;
        return $data;
    }

    public  function test2(){



    }


}
/*    public function callback(){
        $time = time();
        $prev_time = getcache('tishi_time','admin');

        if($time-$prev_time >= 180){
            $check_goods_count =  goods_info_count(-2);
            $check_pay = deposite_count(2);
            $check_pay_money = deposite_count(3);

            $result['state'] = 1;
            $result['info']['num'] = $check_goods_count;
            $result['info']['cznum'] = $check_pay;
            $result['info']['txnum'] = $check_pay_money;

            setcache('tishi_time',$time,'admin');
        }else{
            $result['state'] = 0;
        }

        exit(json_encode($result));

    }
*/

