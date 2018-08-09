<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Admin\Controller;
Class AdminPanelController extends InitController {
    public function _initialize() {
    	parent::_initialize();
        $this->admin_panel_db = model('admin_panel');
    	$this->menu_db = model('node');
    }

    /* 菜单管理 */
    public function init($style = 'init') {
        $menuids = array();
        $tmp = (array) $this->admin_panel_db->field('menuid, datetime')->limit(10)->select();
        if (!empty($tmp)) {
            foreach ($tmp as $key => $value) {
                $menuids[] = $value['menuid'];
            }
        }
        $lists = array();
        if (count($menuids) > 0) {
            $where = array();
            $where['id'] = array('IN', $menuids);
            $lists = (array) $this->menu_db->where($where)->select();
            foreach ($lists as $key => $value) {            
                $value['url'] = U($value['m'].'/'.$value['c'].'/'.$value['a'].$value['data']);
                $value['datetime'] = $tmp[$key]['datetime'];
                $value['menuid'] = $tmp[$key]['menuid'];
                $value['id'] = $tmp[$key]['menuid'];
                $lists[$key] = $value;
            }
        }
        if ($style != 'init') {
            return $this->fetch('AdminPanel:'.$style);
        } else {
            $this->display($style);
        }      
    }

    /* 添加菜单 */
    public function add($menuid = '') {
        $menuinfo = $this->menu_db->detail($menuid);
        if (!$menuinfo) {
            $this->error($this->menu_db->getError());
        }
        $info = array();
        $info['userid'] = session("userid");
        $info['name'] = $menuinfo['name'];
        $info['url'] = U($menuinfo['m'].'/'.$menuinfo['c'].'/'.$menuinfo['a'].$menuinfo['data']);
        $result = $this->admin_panel_db->update($info);
        if (!$result) {
            $this->error($this->admin_panel_db->getError());
        } else {
            $info['menuid'] = $result;
            $this->success($info);
        }
    }

    /* 菜单删除 */
    public function delete($menuid = array()) {
        if (!submitcheck('dosubmit', 'GP')) {
            $this->error('请勿非法提交');            
        }
        $menuid = (array) $menuid;
        if (count($menuid) < 1) $this->error('参数错误');
        if (!$this->admin_panel_db->_delete($menuid)) {
            $this->error('菜单删除失败');
        }
        $this->success('菜单删除成功');
    }
}