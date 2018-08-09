<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Member\Service;
use Think\Model;
if (!defined('MODULE_FIELDS')) define('MODULE_FIELDS', MODULE_PATH.'Fields/');
if (!defined('MODULE_CACHE')) define('MODULE_CACHE', DATA_PATH.'caches_model/');
/* 数据接口分层 */
class MemberModelService extends Model {
    /* 构造方法 */
    protected function init(){
        $this->error = '';
        $this->models = getcache('model', 'commons');
    }
    
    public function build_cache() {
        include MODULE_FIELDS.'fields.inc.php';
        if(!file_exists(MODULE_CACHE))            
            @mkdir(MODULE_CACHE, 0755, TRUE);
        /* 更新内容模型类：表单生成、入库、更新、输出 */
        $classtypes = array('form','input','update','output');
        foreach($classtypes as $classtype) {
            $cache_data = file_get_contents(MODULE_FIELDS.'member_'.$classtype.'.class.php');
            $cache_data = str_replace('}?>','',$cache_data);
            foreach($fields as $field => $fieldvalue) {
                if(file_exists(MODULE_FIELDS.$field.DIRECTORY_SEPARATOR.$classtype.'.inc.php')) {
                    $cache_data .= "\r\n".file_get_contents(MODULE_FIELDS.$field.DIRECTORY_SEPARATOR.$classtype.'.inc.php');
                }
            }
            $cache_data .= "\r\n } \r\n?>";
            file_put_contents(MODULE_CACHE.'member_'.$classtype.'.class.php',$cache_data);
            @chmod(MODULE_CACHE.'member_'.$classtype.'.class.php',0777);
        }
        /* 删除字段缓存 */
        api('Cache/clear_fields');
        return TRUE;
    }
}