<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Common\Api;
use Common\Api\Api;

class HookApi extends Api{
    /* 构造方法 */
    protected function init(){
        $this->modules = getcache('module', 'commons');
    }

    /* 嵌入点简易实现方法 */
    public function run($hookid, $param = '') {
        if (!$this->modules) return FALSE;
        /*修改*/
        $dirs =  $dirs_arr  = array();
        $dirs = glob(APP_PATH.'*');
        foreach ($dirs as $d) {
            if (is_dir($d)) {
                $d = basename($d);
                if ($d != 'Common' && $d != 'Install') {
                    $dirs_arr[] = $d;
                }               
            }
        }
        $modules = $dirs_arr;
        /*修改*/
        // $modules = array_keys($this->modules);

        foreach ($modules as $module) {
            $class_path = "\\".ucfirst($module)."\\Library\\hook";
            if(!class_exists($class_path)) continue;
            $Hook = new $class_path();
            if(!method_exists($Hook, $hookid)) continue;
            $Hook->$hookid($param);
        }
    }
}