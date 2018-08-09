<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Template\Controller;
class AdminController extends \Admin\Controller\InitController {
	public function _initialize() {
		$this->filepath = TPL_PATH;
		parent::_initialize();
	}

	/**
	 * 风格列表
	 * @author   <jason>
	 */
	public function manage() {		
		$dirs = glob($this->filepath.'*');
		$lists = array();
		foreach ($dirs as $dir) {
			if(is_dir($dir)) {
				$key = str_replace(TPL_PATH, '', $dir);
				$pic = (file_exists($dir.'/preview.jpg')) ? $dir.'/preview.jpg' : IMG_PATH.'preview.jpg';
				if($key == 'wap') continue;
				$lists[$key] = str_replace(SITE_PATH, __ROOT__, $pic);
			}
		}
		include $this->admin_tpl('theme_manage');
	}

	public function setting($skin = '') {
		if(empty($skin)) {
			$this->error('参数错误');
		}
		model('setting')->where(array('key' => 'default_style'))->setField('value', $skin);
		model('setting')->build_cache();
		$cache = new \Common\Api\CacheApi();
		$cache->clear_cache();
		$this->success('模板主题设置成功', U('manage'));
	}
}