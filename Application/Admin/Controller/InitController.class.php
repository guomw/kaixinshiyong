<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Admin\Controller;
use Common\Controller\SystemController;
define('IN_ADMIN', TRUE);
class InitController extends SystemController {
    static public $admin;
    /**
     * 构造函数
     * @author   <jason>
     */
    public function _initialize() {
    	parent::_initialize();
        $this->roles = getcache('role', 'commons');
        $this->admin_db = model('admin');
        $this->node_db = model('node');
        define('ADMIN_LOGIN_STATUS',1);
        $this->is_login();
        if (getgpc('dialog')) $show_header = TRUE;
        define('MENUID', I('menuid', 0, 'intval'));
        define('FROMHASH', session('FROMHASH'));
        
    }

    /* 判断是否登录 */
    final public function is_login() {
        if(CONTROLLER_NAME == 'Index' && ACTION_NAME == 'login') {
            return true;
        } else {
            $userid = cookie('userid');
            if(session('?userid') === FALSE || session('?roleid') === FALSE || $userid != session('userid')) {
                $this->error('您尚未登录', U('Admin/Index/login'));
            }
        }
        self::$admin = self::get_userinfo($userid);
        $this->get_rolelist(1);
        $this->check_priv();
    }

    /**
     * 获取管理员信息
     * @author   <jason>
     */
    final public function get_userinfo($userid = 0) {
        return $this->admin_db->getByUserid($userid);
    }

    /**
     * 获取节点列表
     * @param  int $display 显示状态
     * @return array
     */
    final public function get_rolelist($display = -1) {
        $sqlMap = array();        
        if (self::$admin['roleid'] != 1) {
            $this->roleids = unserialize($this->roles[self::$admin['roleid']]['role_priv']);
            $sqlMap['id'] = array("IN", $this->roleids);
        }
        if ($display > -1) {
            $sqlMap['display'] = $display;
        }
        return $this->node_db->where($sqlMap)->order("listorder ASC, id ASC")->select();
    }

    /**
     * 权限判断
     * @author   <jason>
     */
    final public function check_priv() {
        if(MODULE_NAME == 'Admin' && CONTROLLER_NAME == 'Index' && in_array(ACTION_NAME, array('login', 'init'))) return true;        
        if(session('roleid') == 1) return true;
        $action = ACTION_NAME;
        if(preg_match('/^public_/', $action)) return true;
        if(preg_match('/^ajax_([a-z]+)_/', $action, $_match)) {
            $action = $_match[1];
        }
        $sqlMap = array();
        $sqlMap['m'] = MODULE_NAME;
        $sqlMap['c'] = CONTROLLER_NAME;
        $sqlMap['a'] = $action;
        $r = $this->node_db->where($sqlMap)->find();
        if(!$r || !in_array($r['id'], $this->roleids)) {
            $this->error('您没有权限操作该项！<br/><font color="red">错误代码：</font>'.MODULE_NAME.':'.CONTROLLER_NAME.'::'.ACTION_NAME);
        }
        return TRUE;
    }
}