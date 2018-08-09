<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Admin\Controller;
/* URL规则管理 */
Class UrlruleController extends InitController {
    public function _initialize() {
    	parent::_initialize();
    	$this->urlrule_db = D('Urlrule');
        $this->modules = getcache('module', 'commons');
    }

    /**
     * 规则管理
     * @author   <jason>
     */
    public function init() {
        $big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('add').'\', title:\'添加URL规则\', width:\'750\', height:\'300\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', '添加URL规则');        
        /** 查询 **/
        $page = max(1, (int) I('page'));
        $count = $this->urlrule_db->count();
        $infos = $this->urlrule_db->page($page, 10)->select();
        $pages = page($count, 10);
        $show_dialog = TRUE;
        include $this->admin_tpl('urlrule_list');
    }

    /**
     * 规则添加
     * @author   <jason>
     */
    public function add() {
        if (submitcheck('dosubmit')) {
            $info = $_POST['info'];
            $result = $this->urlrule_db->update($info);
            if (!$result) {
                $this->error($this->urlrule_db->getError());
            }
            $this->public_cache();
            $this->success('操作成功', 'javascript:close_dialog();');
        } else {
            $show_header = FALSE;
            $form = new \Common\Library\form();
            $modules = getcache('module', 'commons');
            include $this->admin_tpl('urlrule_add');
        }
        
    }

    /**
     * 规则编辑
     * @author   <jason>
     */
    public function edit($urlruleid = 0) {
        $urlruleid = (int) $urlruleid;
        $data = $this->urlrule_db->detail($urlruleid);
        if ($data == FALSE) {
            $this->error($this->urlrule_db->getError());
        }
        if (submitcheck('dosubmit')) {
            $info = $_POST['info'];
            $info['urlruleid'] = $urlruleid;
            $result = $this->urlrule_db->update($info);
            if (!$result) {
                $this->error($this->urlrule_db->getError());
            }
            $this->public_cache();
            $this->success('操作成功', 'javascript:close_dialog();');
        } else {
            $show_header = FALSE;
            $form = new \Common\Library\form();
            $modules = getcache('module', 'commons');
            extract($data);
            include $this->admin_tpl('urlrule_edit');
        }
    }

    /**
     * 规则删除
     * @author   <jason>
     */
    public function delete($urlruleid = 0) {
        $urlruleid = (int) $urlruleid;
        if ($urlruleid < 1) $this->error('参数错误');
        $result = $this->urlrule_db->delete($urlruleid);
        if (!$result) {
            $this->error('数据删除');
        }
        $this->public_cache();
        $this->success('操作成功');
    }

    /**
     * 更新缓存
     * @author   <jason>
     */
    private function public_cache() {
        $this->urlrule_db->build_cache();
    }
}