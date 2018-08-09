<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Template\Controller;
use \Admin\Controller\InitController;
class FileController extends InitController {
	//模板文件夹
	private $filepath;
	//风格名
	private $style;
	//风格属性
	private $style_info;
	//是否允许在线编辑模板
	private $tpl_edit;

	public function _initialize() {
		parent::_initialize();

		$this->style = isset($_GET['style']) && trim($_GET['style']) ? str_replace(array('..\\', '../', './', '.\\', '/', '\\'), '', trim($_GET['style'])) : showmessage(L('illegal_operation'), HTTP_REFERER);
		if (empty($this->style)) {
			showmessage(L('illegal_operation'), HTTP_REFERER);
		}
		$this->filepath = TPL_PATH.$this->style.DIRECTORY_SEPARATOR;
		if (file_exists($this->filepath.'config.php')) {
			$this->style_info = include $this->filepath.'config.php';
			if (!isset($this->style_info['name'])) $this->style_info['name'] = $this->style;
		}
		$this->tpl_edit = C('tpl_edit');
	}

	/**
	 * 文件详情
	 * @author   <jason>
	 */
	public function init() {
		$dir = isset($_GET['dir']) && trim($_GET['dir']) ? str_replace(array('..\\', '../', './', '.\\', '/', '\\'), '', trim($_GET['dir'])) : '';
		$filepath = $this->filepath.$dir;
		$list = glob($filepath.DIRECTORY_SEPARATOR.'*');
		if(!empty($list)) ksort($list);
		$local = str_replace(array(SITE_PATH.'/', DIRECTORY_SEPARATOR.DIRECTORY_SEPARATOR), array('', DIRECTORY_SEPARATOR), $filepath);
		if (substr($local, -1, 1) == '.') {
			$local = substr($local, 0, (strlen($local)-1));
		}
		$encode_local = str_replace(array('/', '\\'), '|', $local);
		$file_explan = $this->style_info['file_explan'];
		$show_header = true;
		$tpl_edit = $this->tpl_edit;
		include $this->admin_tpl('file_list');
	}

	/**
	 * 更新文件命名
	 * @author   <jason>
	 */
	public function updatefilename() {
		$file_explan = isset($_POST['file_explan']) ? $_POST['file_explan'] : '';
		if (!isset($this->style_info['file_explan'])) $this->style_info['file_explan'] = array();
		$this->style_info['file_explan'] = array_merge($this->style_info['file_explan'], $file_explan);
		@file_put_contents($this->filepath.'config.php', '<?php return '.var_export($this->style_info, true).';?>');
		$this->success('操作成功');
	}
}