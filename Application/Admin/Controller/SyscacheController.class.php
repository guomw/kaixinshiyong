<?php
/**
 * @version        更新缓存
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Admin\Controller;
Class SyscacheController extends InitController {
    public function _initialize() {
    	parent::_initialize();
    }

    /* 清理缓存 */
    public function clear() {
    	$isdosubmit = 1;
    	$page = I('page', 0, intval);
    	if (submitcheck('dosubmit')) {
	    	$modules = array(
	    		array('name' => '模块', 'function' => 'module'),
	    		array('name' => '模型', 'function' => 'model'),
	    		array('name' => '栏目', 'function' => 'category'),
	    		array('name' => '敏感词', 'function' => 'badword'),
	    		array('name' => '联动菜单', 'function' => 'linkage'),
	    		array('name' => '角色', 'function' => 'admin_role'),
				array('name' => '缓存', 'function' => 'clear_cache'),
				array('name' => '日志', 'function' => 'clear_log'),
				array('name' => '字段缓存', 'function' => 'clear_fields'),
				array('name' => '模板缓存', 'function' => 'clear_tmp'),
				array('name' => '通知模板缓存', 'function' => 'notify_tmp'),
				array('name' => '标签缓存', 'function' => 'clear_tag'),	
	    	);
	    	$m = $modules[$page];
	    	$cache = new \Common\Api\CacheApi();
	    	if (method_exists($cache, $m['function'])) {
	    		$cache->$m['function']();
	    	}
	    	if (!empty($modules[$page])) {
	    		echo '<script type="text/javascript">window.parent.addtext("<li>更新'.$m['name'].'缓存完成..........</li>");</script>';
	    	} else {
	    		$isdosubmit = 0;    		
	    		echo '<script type="text/javascript">window.parent.addtext("<li><font color=red>全部缓存更新完毕</font></li>");</script>';
	    	}
	    	$page++; 		
    	}
    	include $this->admin_tpl('cache');
    }
}