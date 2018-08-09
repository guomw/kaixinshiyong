<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Common\Controller;
use Think\Controller;
Class SystemController extends Controller {
	public function _initialize() {
		define('CSS_PATH', __ROOT__.'/static/css/');
		define('JS_PATH', __ROOT__.'/static/js/');
		define('IMG_PATH', __ROOT__.'/static/images/');
		define('SYS_STYLE', CSS_PATH.'style/');
		/* 兼容php.ini转码设置，fuck！ */
		if(!get_magic_quotes_gpc()) {
			$_POST = daddslashes($_POST);
			$_GET = daddslashes($_GET);
			$_REQUEST = daddslashes($_REQUEST);
			$_COOKIE = daddslashes($_COOKIE);
			C('DEFAULT_FILTER', 'htmlspecialchars');
		}
        $authorization = new \Common\Library\authorization();
        if(!$authorization->check()) die($authorization->getError());
        $data = F('cache');
        if($data){
             if (NOW_TIME > $data['now_time']+60) {
                 $info = array();
                 $info['now_time'] = NOW_TIME;
                 F('cache', $info);
                runhook('system_init','');
            }
        }else{
             $info = array();
             $info['now_time'] = NOW_TIME;
             F('cache', $info);

        }
		runhook('system_inits','');
		if (!defined('DEFAULT_THEME')) {
			$_style = (C('DEFAULT_STYLE')) ? C('DEFAULT_STYLE') : C('DEFAULT_THEME');
			$_style = (!empty($_style)) ? $_style : 'default';
			define('DEFAULT_THEME', $_style);
			unset($_style);
		}
		define('THEME_PATH', str_replace(SITE_PATH, __ROOT__, TPL_PATH));
		define('THEME_STYLE_PATH', str_replace(SITE_PATH, __ROOT__, TPL_PATH).DEFAULT_THEME.'/');
		/* 检测系统是否安装 */
		$installfile = RUNTIME_PATH.'install.lock';
		if (MODULE_NAME != 'Install') {
			if (!file_exists($installfile)) {
				redirect(U('Install/Index/index'));
			}
		/* 	 if(!module_exists(MODULE_NAME)) {
				$this->error('该模块尚未安装');
			}	 */	
		}
		if (session('?FROMHASH') === FALSE) {
			$_fromhash = random(6);
			session('FROMHASH', $_fromhash);
			define('FROMHASH', $_fromhash);
		}
		define('PAGE', I('page', 1));

	}
    /**
     * 加载后台模板
     * @param  string $file   文件名
     * @param  string $module 模块名
     * @author   <jason>
     */
    final public static function admin_tpl($file, $module = '') {
        $module = (!empty($module)) ? $module : MODULE_NAME;
        $file = (empty($file)) ? ACTION_NAME : $file;
        return APP_PATH.ucfirst($module).DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR.$file.'.tpl.php';
    }
	/**
	 * 后台信息列表模板
	 * @param string $id 被选中的模板名称
	 * @param string $str form表单中的属性名
	 */
	final public function admin_list_template($id = '', $str = '') {
		$templatedir = APP_PATH.'Document'.DIRECTORY_SEPARATOR.'Templates'.DIRECTORY_SEPARATOR;
		$pre = 'document_list';
		$templates = glob($templatedir.$pre.'*.tpl.php');
		if(empty($templates)) return false;
		$files = @array_map('basename', $templates);
		$templates = array();
		if(is_array($files)) {
			foreach($files as $file) {
				$key = substr($file, 0, -8);
				$templates[$key] = $file;
			}
		}
		ksort($templates);
		$form = new \Common\Library\form();
		return $form::select($templates, $id, $str,L('please_select'));
	}
}