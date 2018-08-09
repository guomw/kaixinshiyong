<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Template\Controller;
use \Admin\Controller\InitController;
class StyleController extends InitController {

	public function _initialize() {
		$this->filepath = TPL_PATH;
		parent::_initialize();
	}

	/**
	 * 风格列表
	 * @author   <jason>
	 */
	public function init() {
		$list = template_list();
		include $this->admin_tpl('style_list');
	}

	private function getStyleInfo($style = '') {
		return TRUE;
	}

	/**
	 * 更新风格名称
	 * @author   <jason>
	 */
	public function updatename() {
		$info = I('post.');
		$styles = (array) $info['name'];
		if(!empty($styles)) {
			foreach ($styles as $key => $name) {
				$filepath = $this->filepath.$key.DIRECTORY_SEPARATOR.'config.php';
				if (file_exists($filepath)) {
					$arr = include $filepath;
					$arr['name'] = $name;
					@file_put_contents($filepath, '<?php return '.var_export($arr, true).';?>');
				}
			}
			$this->success('操作成功');
		} else {
			$this->error('参数错误');
		}
	}

	/**
	 * 风格导出
	 * @author   <jason>
	 */
	public function export($style = 'default') {
		$this->error('模板导出功能开发中');
	}

	/**
	 * 启用/禁用风格
	 * @author   <jason>
	 */
	public function disable($style = '') {		
		if(empty($style)) $this->error('参数不正确');
		$filepath = $this->filepath.$style.DIRECTORY_SEPARATOR.'config.php';
		if (file_exists($filepath)) {
			$arr = include $filepath;
			$arr['disable'] = ($arr['disable'] == 1) ? 0 : 1;
			@file_put_contents($filepath, '<?php return '.var_export($arr, true).';?>');
			$this->success('操作成功');
		} else {
			$this->error('风格不存在');
		}
	}
}