<?php
// +----------------------------------------------------------------------
// | OneThink [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013 http://www.onethink.cn All rights reserved.
// +----------------------------------------------------------------------
// | Author: 麦当苗儿 <zuojiazi@vip.qq.com> <http://www.zjzit.cn>
// +----------------------------------------------------------------------

namespace Common\Api;
use Common\Api\Api;

class ModuleApi extends Api {
    /**
     * 构造方法，实例化操作模型
     */
    protected function init(){
        $this->db = D('Module');
        $this->error = '';
    }

    public function check($module = '') {
        if (empty($module)) {
            $this->error = '模块名不能为空';
            return FALSE;
        }
        $this->module = $module;
        define('MODULE_DIR', APP_PATH.$this->module);
        if (!file_exists(MODULE_PATH) || !is_dir(MODULE_PATH)) {
            $this->error = '模块不存在';
            return FALSE;
        }
        return TRUE;
    }

    /**
     * 安装模块
     * @author   <jason>
     */
    public function install() {
        if ($this->db->getByModule($this->module)) {
            $this->error = '该模块已安装';
            return FALSE;
        }        
        /* 第一步：执行插件SQL */
        $_module_table_file = MODULE_DIR.DIRECTORY_SEPARATOR.'install.sql';
        if (file_exists($_module_table_file)) {
            $_module_table_query = @file_get_contents($_module_table_file);
            sqlexecute($_module_table_query);
        }
        /* 第二步：加载附加程序 */
        $_module_install_file = MODULE_DIR.DIRECTORY_SEPARATOR.'install.php';
        if (file_exists($_module_install_file)) {
            include $_module_install_file;
            if ($sqlquery) {
                sqlexecute($sqlquery);
            }
        }
        /* 第三步：写入模块表 */
        $_module_config_file = MODULE_DIR.DIRECTORY_SEPARATOR.'config.inc.php';
        $config = include $_module_config_file;
        $moduleinfo = array('module' => $this->module,'name' => $modulename,'description' => $introduce,'installdate' => dgmdate(NOW_TIME, 'Y-m-d'),'updatedate' => dgmdate(NOW_TIME, 'Y-m-d'));
        $this->db->add($moduleinfo);

        /* 第四步：复制前台模板 */
        $_module_tpl_path = MODULE_DIR.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR;
        $_module_tpl_files = glob($_module_tpl_path.'*.html');
        if ($_module_tpl_files) {
            $view_tpl_path = C('VIEW_PATH').DEFAULT_THEME.DIRECTORY_SEPARATOR.strtolower($this->module);
            if (!file_exists($view_tpl_path)) {
                helpers('dir');
                dir_create($view_tpl_path);
            }
            foreach ($_module_tpl_files as $tpl_file) {
                @copy($tpl_file, $view_tpl_path.DIRECTORY_SEPARATOR.basename($tpl_file));
            }
        }
        api('Cache/module');
        return TRUE;
    }

    /**
     * 卸载模块
     * @author   <jason>
     */
    public function uninstall() {
        $moduleinfo = $this->db->getByModule($this->module);
        if (!$moduleinfo) {
            $this->error = '该模块未安装';
            return FALSE;
        }
        if ($moduleinfo['iscore'] == 1) {
            $this->error = '禁止卸载内置模块';
            return FALSE;
        }     
        /* 第一步：执行插件SQL */
        $_module_table_file = MODULE_DIR.DIRECTORY_SEPARATOR.'uninstall.sql';
        if (file_exists($_module_table_file)) {
            $_module_table_query = @file_get_contents($_module_table_file);
            sqlexecute($_module_table_query);
        }
        /* 第二步：加载附加程序 */
        $_module_uninstall_file = MODULE_DIR.DIRECTORY_SEPARATOR.'uninstall.php';
        if (file_exists($_module_uninstall_file)) {
            include $_module_uninstall_file;
            if ($sqlquery) {
               sqlexecute($sqlquery);
            }
        }
        /* 第三步：删除相关表 */
        D('Node')->where(array('m' => $this->module))->delete();
        $this->db->where(array('module' => $this->module))->delete();
        api('Cache/module');
        return TRUE;
    }
}