<?php
/**
 * @version        更新缓存api
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Common\Api;
use Common\Api\Api;
class CacheApi extends Api {
	public function init() {}
	
	/* 自定义函数接口 */
	public function run() {
		list($classname, $module) = func_get_args();		
		$classname = (empty($classname)) ? '*' : $classname;
		$module = (empty($module)) ? MODULE_NAME : ucwords($module);
		$module_path = APP_PATH.$module.'/Library';
        $cachefiles = glob($module_path.'/'.$classname.'.cache.class.php');
        if ($cachefiles) {
            foreach ($cachefiles as $cachefile) {
                if(!is_file($cachefile)) continue;
                require_cache($cachefile);
                $classname = basename($cachefile, '.cache.class.php');
                $cache_class = new $classname;
                if (method_exists($cache_class, 'run')) {
                    $cache_class->run();
                    unset($cache_class);
                }
            }
        }
        return TRUE;		
	}
	
	/* 模块缓存 */
	public function module() {
		D('Module')->build_cache();
		return TRUE;
	}

	/* 模型缓存 */
	public function model($modelid = 0) {
		$ModelApi = new \Common\Api\ModelApi();
        $ModelApi->build_cache($modelid);
	}

	/* 栏目缓存 */
	public function category() {
		return D('Category')->build_cache();
	}

	/**
	 * 下载服务器缓存
	 * @author   <jason>
	 */
	public function downservers() {
		return TRUE;
	}

	/**
	 * 敏感词缓存
	 * @author   <jason>
	 */
	public function badword() {
		return TRUE;
	}

	/**
	 * IP黑名单
	 * @author   <jason>
	 */
	public function ipbanned() {
		return TRUE;
	}

	/**
	 * 关联链接
	 * @author   <jason>
	 */
	public function keylink() {
		return TRUE;		
	}

	/**
	 * 联动菜单
	 * @author   <jason>
	 */
	public function linkage() {
		return D('Linkage')->build_cache();
	}

	/**
	 * 推荐位缓存
	 * @author   <jason>
	 */
	public function position() {
		return D('Position')->build_cache();	
	}

	/**
	 * 角色缓存
	 * @author   <jason>
	 */
	public function admin_role() {
		return D('AdminRole')->build_cache();
	}

	/**
	 * URL规则缓存
	 * @author   <jason>
	 */
	public function urlrule() {
		return D('Urlrule')->build_cache();
	}

	/**
	 * 删除模板缓存
	 * @author   <jason>
	 */
	public function clear_cache() {
		$dir = new \Common\Library\dir();
		return $dir->delDir(CACHE_PATH);
	}

	/**
	 * 清空日志
	 * @author   <jason>
	 */
	public function clear_log() {
		$dir = new \Common\Library\dir();
		return $dir->del(LOG_PATH);
	}

	/**
	 * 来源缓存
	 * @author   <jason>
	 */
	public function copyfrom() {
		return TRUE;
	}

	public function setting() {
		return D('Setting')->build_cache();
	}

	/* 清理数据库字段缓存 */
	public function clear_fields() {
		$dir = new \Common\Library\dir();
		return $dir->del(DATA_PATH.'_fields');		
	}

	/* 标签缓存 */
	public function clear_tag() {
		$dir = new \Common\Library\dir();
		return $dir->del(RUNTIME_PATH.'Temp');
	}

	/* 模板缓存 */
	public function clear_tmp() {
		$dir = new \Common\Library\dir();
		return $dir->del(RUNTIME_PATH.'Cache');
	}

	/* 通知模板缓存 */
	public function notify_tmp() {
		$lists = model('notify_template')->getField('identifier, content, type');
		//缓存通知模板
		setcache('notify',$lists,'notify');
		return true;
	}

}