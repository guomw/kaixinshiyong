<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Member\Controller;
use \Admin\Controller\InitController;
use Think\Db;
/**
 * 后台会员模型管理
 */
class MemberModelController extends InitController {
	public function _initialize() {
		parent::_initialize();
		$this->db = D('Model');
		$this->model_field_db = D('ModelField');
	}

	/**
	 * 会员模型管理
	 * @author   <jason>
	 */
	public function manage() {
		$member_model_list = $sqlMap = array();
		$sqlMap['module'] = 'member';
		$member_model_list = $this->db->where($sqlMap)->select();
		/* 拓展菜单 */
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('add').'\', title:\''.L('add_model').'\', width:\'700\', height:\'500\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('member_model_add'));
		include $this->admin_tpl('member_model_list');
	}

	/**
	 * 添加会员模型
	 * @author   <jason>
	 */
	public function add() {
		if (submitcheck('dosubmit')) {
			$info = $_POST['info'];
			$info['name'] = $info['modelname'];			
			$info['module'] = strtolower(MODULE_NAME);
			$info['inputtime'] = NOW_TIME;
			$is_exists = $this->db->table_exists(C('DB_PREFIX').'member_'.$info['tablename']);
			if ($is_exists) {
				$this->error('数据表已存在');
			}
			$info['tablename'] = 'member_'.$info['tablename'];
			$modelid = $this->db->add($info);
			if ($modelid) {
				$model_sql = file_get_contents(MODULE_PATH.'Fields/model.sql');
				$tablepre = C('DB_PREFIX');
				$tablename = $info['tablename'];
				$model_sql = str_replace('$tablename', $tablepre.$tablename, $model_sql);
				sqlexecute($model_sql);
			}
			$this->success('操作成功', 'javascript:close_dialog();');
		} else {
			$show_dialog = TRUE;
			include $this->admin_tpl('member_model_add');
		}
	}

	/**
	 * 编辑会员模型
	 * @author   <jason>
	 */
	public function edit($modelid = 0) {
		$modelid = (int) $modelid;
		if ($modelid < 1) $this->error('参数错误');
		$modelinfo = $this->db->where(array('modelid' => $modelid, 'module' => 'member'))->find();
		if (empty($modelinfo)) $this->error('模型不存在');
		if (submitcheck('dosubmit')) {
			$info = $_POST['info'];
			$info['modelid'] = $modelid;
			$info['name'] = $info['modelname'];
			$info['description'] = $info['description'];
			$info['disabled'] = (int) $info['disabled'];
			$info = array_merge($modelinfo, $info);
			$result = $this->db->update($info);
			if (!$result) {
				$this->error($this->db->getError());
			}
			$this->success('操作成功', 'javascript:close_dialog();');
		} else {
			$show_dialog = $show_header = TRUE;
			include $this->admin_tpl('member_model_edit');
		}
	}

	/**
	 * 删除会员模型
	 * @author   <jason>
	 */
	public function delete() {
		if (submitcheck('dosubmit')) {
			$modelids = (array) $_POST['modelid'];
			foreach ($modelids as $modelid) {
				if (!is_numeric($modelid)) continue;
				$modelinfo = $this->db->getByModelid($modelid);
				$tablename = C('DB_PREFIX').$modelinfo['tablename'];
				sqlexecute("DROP TABLE IF EXISTS `".$tablename."`;");
				$this->model_field_db->where(array('modelid' => $modelid))->delete();
				$this->db->delete($modelid);
			}
			$this->success('操作成功');
		} else {
			$this->error('请勿非法提交');
		}
	}

	/**
	 * 模型导出
	 * @author   <jason>
	 */
	public function export() {
		$this->success('操作成功');
	}

	/**
	 * 模型数据移动
	 * @author   <jason>
	 */
	public function move() {
		$this->success('操作成功', 'javascript:close_dialog();');
	}

	/**
	 * 模型排序
	 * @author   <jason>
	 */
	public function public_listorder() {
		if (IS_POST) {
			$sort = $_POST['sort'];
			if (is_array($sort)) {
				foreach ($sort as $key => $value) {
					$this->db->where(array('modelid' => $key))->setField('sort',$value);
				}
			}
			$this->success('操作成功');
		} else {
			$this->error('请勿非法提交');
		}
	}
	/**
	 * 检测模型名称
	 * @author   <jason>
	 */
	public function public_checkmodelname($modelname = '') {
		$oldmodelname = I('oldmodelname');
		if($modelname == $oldmodelname) exit('0');
		$sqlMap = array();
		$result = $this->db->getByName($modelname);
		if (is_array($result) && !empty($result)) exit("1");
		exit("0");
	}

	/**
	 * 检测模型表名
	 * @author   <jason>
	 */
	public function public_checktablename($tablename = '') {
		if(empty($tablename)) echo "1";
		$tablename = C('DB_PREFIX').'member_'.$tablename;
		echo D('Model')->table_exists($tablename);
	}
}