<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Admin\Controller;
Class ModuleController extends InitController {
    public function _initialize() {
    	parent::_initialize();
        $this->db = D('Module');
    }

   /**
    * 模块管理
    * @author   <jason>
    */
    public function init() {
        $dirs = $module = $dirs_arr = $directory = array();
        $dirs = glob(APP_PATH.'*');
        foreach ($dirs as $d) {
            if (is_dir($d)) {
                $d = basename($d);
                if ($d != 'Common' && $d != 'Install') {
                    $dirs_arr[] = $d;
                }               
            }
        }
        define('INSTALL', true);
        $modules = $this->db->getField('module, iscore, name,installdate,updatedate');
        $total = count($dirs_arr);
        $dirs_arr = array_chunk($dirs_arr, 20, true);
        $page = max(1, (int) I('page'));
        $pages = page($total, 20);
        $directory = $dirs_arr[intval($page-1)];
        include $this->admin_tpl('module_list');
    }

    /**
     * 模块安装
     * @author   <jason>
     */
    public function install($module = null) {
        $ModuleApi = new \Common\Api\ModuleApi();
        if (!$ModuleApi->check($module)) {
            $this->error($ModuleApi->error, 'javascript:close_dialog();');
        }
        if (submitcheck('dosubmit')) {
            if ($ModuleApi->install()) {
                $this->success('模块安装成功', 'javascript:close_dialog();');
            } else {
               $this->error($ModuleApi->error, 'javascript:close_dialog();'); 
            }
        } else {
            include APP_PATH.$module.DIRECTORY_SEPARATOR.'config.inc.php';
            include $this->admin_tpl('module_config');
        }
    }

    /**
     * 模块卸载
     * @author   <jason>
     */
    public function uninstall($module = null) {
        $ModuleApi = new \Common\Api\ModuleApi();
        if (!$ModuleApi->check($module)) {
            $this->error($ModuleApi->error, 'javascript:close_dialog();');
        }
        if ($ModuleApi->uninstall()) {
            $this->success('模块卸载成功', 'javascript:close_dialog();');
        } else {
           $this->error($ModuleApi->error, 'javascript:close_dialog();'); 
        }
    }

}