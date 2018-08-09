<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Common\Api;
use Common\Api\Api;

class ModelApi extends Api{

    /* 构造方法 */
    protected function init(){
        $this->db = M('Model');
        $this->model_filed_db = M('ModelField');
        $this->error = '';
    }

    /* 更新模型缓存 */
    public function build_cache($modelid = 0) {
        $modelid = (int) $modelid;
        $sqlMap = array();
        if ($modelid > 0) {
            $sqlMap['modelid'] = $modelid;
        }
        $lists = $this->db->where($sqlMap)->select();
        $models = $modules = array();
        foreach ($lists as $r) {
            $models[$r['modelid']] = $r;
            /* 写入模型对应的字段缓存 */
            $model_fields = $this->model_filed_db->where(array('modelid' => $r['modelid'], 'disabled' => '0'))->order('listorder ASC')->select();
            $fields = array();
            if ($model_fields) {
                foreach($model_fields as $field) {
                    $fields[$field['field']] = $field;
                }
                if (!empty($fields)) {
                    setcache('model_field_'.$r['modelid'], $fields, 'model');
                } else {
                    delcache('model_field_'.$r['modelid'], 'model');
                }                
            }
            $modules[] = $r['module'];
        }
        $modules = array_filter(array_unique($modules));
        if($modules) $this->module_cache($modules);
        return setcache('model', $models, 'commons');
    }

    /* 更新对应模型的自定义缓存 */
    private function module_cache($modules = array()) {
        if(empty($modules)) return FALSE;
        foreach ($modules as $module) {
            $module = ucwords($module);
            $module_path = APP_PATH.$module.'/Library';
            $cachefiles = glob($module_path.'/*.cache.class.php');
            if ($cachefiles) {
                foreach ($cachefiles as $cachefile) {
                    if(!is_file($cachefile)) continue;
                    require_once $cachefile;
                    $classname = basename($cachefile, '.cache.class.php');
                    $cache_class = new $classname;
                    if (method_exists($cache_class, 'run')) {
                        $cache_class->run();
                    }
                }
            }
        }
    }
    
}