<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Admin\Controller;
Class AdminController extends InitController {
    public function _initialize() {
    	parent::_initialize();
        $this->roles = getcache('role', 'commons');
        $this->admin_db = model('admin');
    }

    public function _empty() {
        $this->error(L('_METHOD_NOT_EXIST_'));
    }

   /**
    * 管理员列表
    * @author   <jason>
    */
    public function init() {
        $param = I('param.');
        $map = array();
        if (isset($param['roleid']) && $param['roleid'] > 0) {
            $map['roleid'] = (int) $param['roleid'];
        }
        $infos = $this->admin_db->where($map)->select();
        include $this->admin_tpl('admin_list');
    }

    /**
     * 添加管理员
     * @author   <jason>
     */
    public function add() {
        if (IS_POST) {
            $info = $_POST['info'];
            $info['encrypt'] = random(6);
            $info['password'] = md5(md5($info['password'].$info['encrypt']));
            $result = $this->admin_db->update($info);
            if (!$result) {
                $this->error($this->admin_db->getError());
            }
            $this->success('管理员添加成功');
        } else {
        	$roles = getcache('role','commons');
            $show_header = FALSE;
            include $this->admin_tpl('admin_add');
        }
    }

    /**
     * 编辑管理员
     * @author   <jason>
     */
    public function edit($userid) {
        $userid = (int) $userid;
        $data = $this->admin_db->detail($userid);
        if ($data === false) {
            $this->error($this->db->getError());
        }
        if (submitcheck('dosubmit')) {          
            $info = $_POST['info'];
            $info['userid'] = $userid;
            if (empty($info['password'])) {
                unset($info['password']);
            } else {
                $info['encrypt'] = random(6);//强制更新随机因子
                if ($info['password'] != $info['pwdconfirm']) {
                    $this->error('两次密码不一致');
                }                
                $info['password'] = md5(md5($info['password'].$info['encrypt']));
            }

            if ($info['config']) {
                 $info['company_config'] = array2string($info['config']);
            }
            $result = $this->admin_db->update($info);
            if(!$result) {
                $this->error($this->admin_db->getError());
            }
            $this->success('操作成功', 'javascript:close_dialog();');
        } else {
            extract($data);
            $show_header = FALSE;
            $fee = string2array($company_config);
            $roles = getcache('role','commons');
            include $this->admin_tpl('admin_edit');
        }
    }

    /**
     * 删除管理员
     * @author xuew <jason>
     */
    public function delete($userid) {
        $userid = (int) $userid;
        if($userid < 1) $this->error('删除错误');
        $result = $this->admin_db->where(array('userid' => $userid))->delete();
        if (!$result) {
            $this->error('删除管理员失败');
        }
        $this->success('删除管理员成功');
    }

    /* 修改个人资料 */
    public function edit_info() {
        if (submitcheck('dosubmit')) {
           $info = $_POST['info'];
           $result = $this->admin_db->update($info, FALSE);
           if (!$result) {
               $this->error($this->admin_db->getError());
           }
           $this->success('操作成功');
        } else {
        	$admininfo = parent::$admin;
            extract(parent::$admin);
            $rolename = model('admin_role')->getFieldByRoleid($roleid,'rolename');
            include $this->admin_tpl('admin_edit_info');
        }
    }

    /* 编辑密码 */
    public function edit_pwd() {      
        if (submitcheck('dosubmit')) {
            $info = $_POST;
            if (empty($info['old_password'])) {
                $this->error('旧密码不能为空');
            }
            if (empty($info['new_password'])) {
                $this->error('新密码不能为空');
            }
            if ($info['new_password'] != $info['new_pwdconfirm']) {
                $this->error('两次密码输入不一致');
            }
            if (md5(md5($info['old_password'].parent::$admin['encrypt'])) != parent::$admin['password']) {
                $this->error('旧密码输入不正确');
            }
            $info['password'] = md5(md5($info['new_password'].parent::$admin['encrypt']));
            $result = $this->admin_db->update($info);
            if (!$result) {
                $this->error($this->admin_db->getError());
            }
            $this->success('密码修改成功');
        } else {
            extract(parent::$admin);
            include $this->admin_tpl('admin_edit_pwd');            
        }
    }

    /* 检测是否存在 */
    public function public_already_exists($clientid = 'email') {
        $sqlMap = array();
        if ($clientid == 'email') {
            $email = I('email');
            if (empty($email) || !isemail($email)) {
                $this->error('邮箱错误');
            }
            $sqlMap['email'] = $email;  
            $sqlMap['userid'] = array("NEQ", parent::$admin['userid']);
            $result = $this->admin_db->where()->find($sqlMap);         
        } elseif ($clientid == 'old_password') {
            $old_password = I('old_password');
            if (empty($old_password)) {
                $this->error('邮箱错误');
            }
            $result = md5(md5($old_password.parent::$admin['encrypt'])) == parent::$admin['password'];
        }

        if ($result == false) {
            $this->error('不可用');
        }
        $this->success('可用');
    }


    /**
     * 检测管理员用户名
     * @author   <jason>
     */
    public function ajax_checkusername($username = '') {
        if (empty($username)) {
            echo "0";
            exit();
        }
        $userid = $this->admin_db->getFieldByUsername($username);
        if ($userid) {
            exit('0');
        } else {
            exit('1');
        }
    }


    /*招商专员*/
     public function company_add() {
        if (IS_POST) {
            $info = $_POST['info'];
            $info['encrypt'] = random(6);
            $info['password'] = md5(md5($info['password'].$info['encrypt']));
            $info['company_config'] = array2string($info['config']);
            $result = $this->admin_db->update($info);
            if (!$result) {
                $this->error($this->admin_db->getError());
            }
            $this->success('招商管理员添加成功');
        } else {
            $roles = getcache('role','commons');
            $show_header = FALSE;
            include $this->admin_tpl('company_add');
        }
    }

    /*招商记录*/
    public function company_log($userid){
        if (!(int)$userid) {
              return false;
        }

        $sqlMap = array();

        if (IS_GET) {
            $info = I('get.');
            $info['start_time'] = (!empty($info['start_time'])) ? strtotime($info['start_time']) : 0;
            $info['end_time'] = (!empty($info['end_time'])) ? strtotime($info['end_time']) : 0;
            /* 注册时间 */
            if ($info['start_time'] && $info['end_time']){
                $sqlMap['time'] = array("BETWEEN",array($info['start_time'],$info['end_time']));
            }else{
                if ($info['start_time'] > 0) {
                $sqlMap['time'] = array("EGT", $info['start_time']);
                }
                if ($info['end_time'] > 0) {
                    $sqlMap['time'] = array("ELT", $info['end_time']);
                }
            }
          
           
            /* 关键字搜索类型 */
           $type = (int) $info['type'];
           if($type > 0){
            $sqlMap['type'] = $type;
           }
            
        }

        $sqlMap['agent_id'] = $userid;
        $count = model('company_log')->where($sqlMap)->count();
        $infos = model('company_log')->where($sqlMap)->page(PAGE,10)->order('id desc')->select();
        $pages = page($count,10);
        foreach ($infos as $k => $v) {
            $infos[$k]['username'] = model('admin')->where(array('userid'=>$userid))->getField('username');
        }
        $form =  new \Common\Library\form();

        include $this->admin_tpl('company_log');

    }  

}