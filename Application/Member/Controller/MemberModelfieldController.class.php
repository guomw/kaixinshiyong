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
define('MODULE_CACHE', DATA_PATH.'caches_model/');
/**
 * 后台会员模型字段管理
 */
class MemberModelfieldController extends InitController {
	public function _initialize() {
		parent::_initialize();
		$this->db = D('ModelField');
		$this->model_db = D('Model');
		$this->model_api = D('MemberModel', 'Service');
		if (!file_exists(MODULE_CACHE.'member_form.class.php')) {
			$this->model_api->build_cache();
		}
	}

	/**
	 * 模型字段管理
	 * @author   <jason>
	 */
	public function manage() {
		$modelid = I('modelid', 'intval', 0);
		$datas = $this->cache_field($modelid);
		$modelinfo = $this->model_db->getByModelid($modelid);
		if(!is_array($modelinfo)) {
			$this->error('模型不存在');
		}
		/* 拓展菜单 */
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('add', array('modelid' => $modelinfo['modelid'])).'\', title:\''.L('member_modelfield_add').' '.L('model_name').'：'.$modelinfo['name'].'\', width:\'700\', height:\'500\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('member_modelfield_add'));
		include $this->admin_tpl('member_modelfield_list');
	}

	/**
	 * 添加会员模型字段
	 * @author   <jason>
	 */
	public function add() {
		if (submitcheck('dosubmit')) {
			$info = $_POST['info'];
			$modelinfo = $this->model_db->getByModelid($info['modelid']);
			if (!is_array($modelinfo)) {
				$this->error('模型不存在');
			}			
			$tablename = C('DB_PREFIX').$modelinfo['tablename'];			
			$field = $info['field'];
			$minlength = $info['minlength'] ? $info['minlength'] : 0;
			$maxlength = $info['maxlength'] ? $info['maxlength'] : 0;
			$field_type = $info['formtype'];
			require MODULE_PATH.'Fields'.DIRECTORY_SEPARATOR.$field_type.DIRECTORY_SEPARATOR.'config.inc.php';
			if(isset($_POST['setting']['fieldtype'])) {
				$field_type = $_POST['setting']['fieldtype'];
			}
			require MODULE_PATH.'Fields'.DIRECTORY_SEPARATOR.'add.sql.php';
			/* 附加字段 */
			// $info['setting'] = array2string($_POST['setting']);
			$info['setting'] = $_POST['setting'];
			$info['unsetgroupids'] = isset($_POST['unsetgroupids']) ? implode(',',$_POST['unsetgroupids']) : '';
			$info['unsetroleids'] = isset($_POST['unsetroleids']) ? implode(',',$_POST['unsetroleids']) : '';
			$result = $this->db->update($info);
			if (!$result) {
				$this->error($this->db->getError());
			}
			$this->success('操作成功', 'javascript:close_dialog();');
		} else {
			$modelid = I('modelid');
			$show_header = $show_validator= $show_dialog = TRUE;
			require MODULE_PATH.'Fields/fields.inc.php'; 
			$form =  new \Common\Library\form();
            $_roles = getcache('role');
            $roles = array();
            foreach ($_roles as $key => $value) {
                $roles[$value['roleid']] = $value['rolename'];
            }
            $_grouplist = getcache('member_group','member');
            $grouplist = array();
            foreach ($_grouplist as $key => $value) {
                $grouplist[$value['groupid']] = $value['name'];
            }
			include $this->admin_tpl('member_modelfield_add');
		}
	}

	/**
	 * 编辑会员模型字段
	 * @author   <jason>
	 */
	public function edit($fieldid = 0) {
		$fieldid = (int) $fieldid;
		if ($fieldid < 1) $this->error('参数错误');
		$fieldinfo = $this->db->getByFieldid($fieldid);
		$modelinfo = $this->model_db->where(array('modelid' => $fieldinfo['modelid'], 'module' => 'member'))->find();
		if (empty($modelinfo)) $this->error('模型不存在');
		if (submitcheck('dosubmit')) {
			$info = $_POST['info'];
			$info['fieldid'] = $fieldid;			
			$tablename = C('DB_PREFIX').$modelinfo['tablename'];
			$field = $info['field'];
			$minlength = $info['minlength'] ? $info['minlength'] : 0;
			$maxlength = $info['maxlength'] ? $info['maxlength'] : 0;
			$field_type = $info['formtype'];
			require MODULE_PATH.'Fields'.DIRECTORY_SEPARATOR.$field_type.DIRECTORY_SEPARATOR.'config.inc.php';
			if(isset($_POST['setting']['fieldtype'])) {
				$field_type = $_POST['setting']['fieldtype'];
			}
			$oldfield = $_POST['oldfield'];
			require MODULE_PATH.'Fields'.DIRECTORY_SEPARATOR.'edit.sql.php';
			/* 附加字段 */
			// $info['setting'] = array2string($_POST['setting']);
			$info['setting'] = $_POST['setting'];
			$info['unsetgroupids'] = isset($_POST['unsetgroupids']) ? implode(',',$_POST['unsetgroupids']) : '';
			$info['unsetroleids'] = isset($_POST['unsetroleids']) ? implode(',',$_POST['unsetroleids']) : '';
			$result = $this->db->update($info);
			if (!$result) {
				$this->error($this->db->getError());
			}
			$this->success('操作成功', 'javascript:close_dialog();');
		} else {
			$modelid = $modelinfo['modelid'];
			$show_header = $show_validator= $show_dialog = TRUE;
			require MODULE_PATH.'Fields/fields.inc.php'; 
			$form =  new \Common\Library\form();
            $_roles = getcache('role');
            $roles = array();
            foreach ($_roles as $key => $value) {
                $roles[$value['roleid']] = $value['rolename'];
            }
            $_grouplist = getcache('member_group','member');
            $grouplist = array();
            foreach ($_grouplist as $key => $value) {
                $grouplist[$value['groupid']] = $value['name'];
            }

            extract($fieldinfo);
            // print_r(stripslashes($setting));
            $setting = string2array($setting);
            /* 增加获取字段相关参数 */
			ob_start();
			include MODULE_PATH.'Fields'.DIRECTORY_SEPARATOR.$formtype.DIRECTORY_SEPARATOR.'field_edit_form.inc.php';
			$form_data = ob_get_contents();
			ob_end_clean();
			
			include $this->admin_tpl('member_modelfield_edit');
		}
	}

	/**
	 * 删除会员模型
	 * @author   <jason>
	 */
	public function delete($fieldid = 0) {
		$fieldid = (int) $fieldid;		
		$fieldinfo = $this->db->getByFieldid($fieldid);
		if (empty($fieldinfo)) $this->error('参数错误');
		$tablename = $this->model_db->getFieldByModelid($fieldinfo['modelid'], 'tablename');
		$tablename = C('DB_PREFIX').$modelinfo['tablename'];		
		$field = $fieldinfo['field'];
		$r = $this->db->delete($fieldid);
		require MODULE_PATH.'Fields'.DIRECTORY_SEPARATOR.'delete.sql.php';
		$this->success('操作成功');
	}

	/**
	 * 模型字段状态
	 * @author   <jason>
	 */
	public function disabled($fieldid = 0) {
		$fieldid = (int) $fieldid;
		if($fieldid < 1) $this->error('参数错误');
		$disabled = $this->db->getFieldByFieldid($fieldid, 'disabled');
		$disabled = (!$disabled) ? 1 : 0;
		$result = $this->db->where(array('fieldid' => $fieldid))->setField('disabled', $disabled);
		if (!$result) {
			$this->error('字段状态更新失败');
		}
		$this->success('字段状态更新成功');
	}

	/**
	 * 字段排序
	 * @author   <jason>
	 */
	public function sort() {
		$listorders = $_POST['listorders'];
		if (empty($listorders)) {
			$this->error('参数错误');
		}
		foreach ($listorders as $fieldid => $listorder) {
			if(!is_numeric($fieldid) || !is_numeric($listorder)) continue;
			$this->db->where(array('fieldid' => $fieldid))->setField('listorder', $listorder);
		}
		$this->success('操作成功');
	}

	/**
	 * 检测字段是否存在
	 * @author   <jason>
	 */
	public function public_checkfield() {
		$param = I('param.');
		$sqlMap = array();
		$sqlMap['module'] = 'member';
		$sqlMap['field'] = trim($param['field']);
		$sqlMap['modelid'] = (int) $param['modelid'];
		$count = $this->db->where($sqlMap)->count();
		if ($count > 0) {
			echo "0";exit();
		} else {
			echo "1";exit();
		}
	}

	/**
	 * 模型字段设置
	 * @author   <jason>
	 */
	public function public_field_setting() {
		$fieldtype = $_GET['fieldtype'];
		require MODULE_PATH.'Fields'.DIRECTORY_SEPARATOR.$fieldtype.DIRECTORY_SEPARATOR.'config.inc.php';
		ob_start();
		include MODULE_PATH.'Fields'.DIRECTORY_SEPARATOR.$fieldtype.DIRECTORY_SEPARATOR.'field_add_form.inc.php';
		$data_setting = ob_get_contents();
		ob_end_clean();
		$settings = array('field_basic_table'=>$field_basic_table,'field_minlength'=>$field_minlength,'field_maxlength'=>$field_maxlength,'field_allow_search'=>$field_allow_search,'field_allow_fulltext'=>$field_allow_fulltext,'field_allow_isunique'=>$field_allow_isunique,'setting'=>$data_setting);
		echo json_encode($settings);
		return true;
	}

	/**
	 * 更新指定模型字段缓存
	 * 
	 * @param $modelid 模型id
	 */
	private function cache_field($modelid = 0) {
		$field_array = array();
		$fields = $this->db->where(array('modelid'=>$modelid))->order('listorder ASC')->select();
		foreach($fields as $_value) {
			$_value['setting'] = string2array($_value['setting']);
			$field_array[$_value['field']] = $_value;
		}
		F('model_field_'.$modelid, $field_array);
		$this->model_api->build_cache();
		return $field_array;
	}
}