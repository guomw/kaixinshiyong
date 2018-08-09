<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Member\Controller;
use \Member\Controller\InitController;
defined('MODULE_CACHE') or define('MODULE_CACHE', DATA_PATH.'caches_model/');
class DocumentController extends InitController {
	public $userid = '';
	public $userinfo = '';
	public function _initialize() {
		parent::_initialize();
		$this->models = getcache('model', 'commons');
		$this->categorys = getcache('category', 'commons');
		$this->document_db = D('Document/Document');
        if (!file_exists(MODULE_CACHE.'content_form.class.php')) {
            api('Cache/run', 'document_model');
        }
	}

	private function set_model($modelid) {
		$this->model = $this->models[$modelid];
		$this->tableName = $this->model['tablename'];
	}
	
	/**
	 * 商家设置
	 * @author   <jason>
	 */
	public function _empty() {	
		$this->company();
	}

	/**
	 * 发布文档
	 * @author   <jason>
	 */
	public function add() {
		$catid = (int) I('catid');
		$category = $this->categorys[$catid];
		$modelid = $category['modelid'];
		if ($catid < 1 || empty($category)) $this->error('参数错误');
		$this->set_model($modelid);
		if (IS_POST) {
			$info = $_POST['info'];
			$info['status'] = 0;
			$result = $this->document_db->add_content($info);
			if (!$result) {
				$this->error($this->document_db->getError());
			}
			$this->success('操作成功', U('mangage', array('catid' => $catid)));
		} else {       
	        require MODULE_CACHE.'content_form.class.php';
	        $content_form = new \content_form($modelid, $catid, $category);
	        $data = array();
	        $forminfos = $content_form->get();
	        $show_dialog = $show_validator = 1;
			include template('document_add');
		}
	}

	/**
	 * 修改文档
	 * @author   <jason>
	 */
	public function edit() {
		$id = (int) I('id');
		$catid = (int) I('catid');
		$category = $this->categorys[$catid];
		$modelid = $category['modelid'];
		if ($id < 1 || $catid < 1 || empty($category)) $this->error('参数错误');
		$this->set_model($modelid);
		$systeminfo = M($this->tableName)->getById($id);
		$modelinfo = M($this->tableName.'_data')->getById($id);
		if(!$systeminfo || !$modelinfo) $this->error('数据异常');
		if (IS_POST) {
			$info = $_POST['info'];
            $result = $this->document_db->edit_content($info, $id);
            if (!$result) {
                $this->error($this->document_db->getError());
            } else {
            	$this->error('操作成功', U('mangage', array('catid' => $catid)));
            }
		} else {
			$data = array_merge($systeminfo, $modelinfo);
			$data = array_map('dhtmlspecialchars',$data);
            require MODULE_CACHE.'content_form.class.php';
            $content_form = new \content_form($modelid, $catid, $category);
            $forminfos = $content_form->get($data);			
			include template('document_edit');
		}
	}

	/**
	 * 信息管理
	 * @author   <jason>
	 */
	public function mangage($catid = 0) {
		$catid = (int) $catid;
		include template('document_add');
	}
}