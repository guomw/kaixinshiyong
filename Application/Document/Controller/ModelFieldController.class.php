<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Document\Controller;
use \Admin\Controller\InitController;
use Think\Db;
/**
 * 内容模型管理
 * @author   <jason>
 */
class ModelFieldController extends InitController {
    public function _initialize() {
    	parent::_initialize();
    	$this->model_db = D('Model');
        $this->model_field_db = D('ModelField');
        $this->field_dir = MODULE_PATH.'Fields/';
    }

    /**
     * 模型字段管理
     * @param int $modelid 所属模型ID
     * @author   <jason>
     */
    public function init($modelid) {
        $modelid = (int) $modelid;
        $r = $this->model_db->getByModelid($modelid);
        $map = array();
        $map['modelid'] = $modelid;
        $datas = $this->model_field_db->where($map)->order("listorder ASC")->select();
        include $this->field_dir.'fields.inc.php';
        $show_header = 1;
        include $this->admin_tpl('model_field_manage');
    }

    /**
     * 模型字段添加
     * @author   <jason>
     */
    public function add($modelid = null) {
        $modelid = (int) $modelid;
        if ($modelid < 1) {
            $this->error('模型参数错误');
        }
        $model_info = $this->model_db->detail($modelid);
        if (!$model_info) {
            $this->error('模型不存在');
        }
        if (submitcheck('dosubmit')) {
            $info = $_POST['info'];
            $info['setting'] = $_POST['setting'];
            $info['modelid'] = $modelid;
            $info['unsetgroupids'] = isset($_POST['unsetgroupids']) ? implode(',',$_POST['unsetgroupids']) : '';
            $info['unsetroleids'] = isset($_POST['unsetroleids']) ? implode(',',$_POST['unsetroleids']) : '';
            $model_table = $model_info['tablename'];
            $tablename = $_POST['issystem'] ? C('DB_PREFIX').$model_table : C('DB_PREFIX').$model_table.'_data';
            $field = $_POST['info']['field'];
            $minlength = $_POST['info']['minlength'] ? $_POST['info']['minlength'] : 0;
            $maxlength = $_POST['info']['maxlength'] ? $_POST['info']['maxlength'] : 0;
            $field_type = $_POST['info']['formtype'];
            require $this->field_dir.$field_type.DIRECTORY_SEPARATOR.'config.inc.php';
            if(isset($_POST['setting']['fieldtype'])) {
                $field_type = $_POST['setting']['fieldtype'];
            }
            require $this->field_dir.'add.sql.php';
            // 删除字段缓存
            $fieldid = $this->model_field_db->update($info);
            if (!$fieldid) {
                $this->error($this->model_field_db->getError());
            }
            $this->public_field_cache($modelid);
            $this->success('操作成功');
        } else {
            $show_header = $show_validator = $show_dialog = '';         
            $_roles = getcache('role', 'commons');
            $roles = array();
            foreach ($_roles as $key => $value) {
                $roles[$value['roleid']] = $value['rolename'];
            }
            $_grouplist = getcache('member_group','member');
            $grouplist = array();
            foreach ($_grouplist as $key => $value) {
                $grouplist[$value['groupid']] = $value['name'];
            }            
            include $this->field_dir.'fields.inc.php';
            $all_field = array();
            foreach($fields as $_k=>$_v) {
                if(in_array($_k,$not_allow_fields) || in_array($_k,$exists_field) && in_array($_k,$unique_fields)) continue;
                $all_field[$_k] = $_v;
            }
            $form = new \Common\Library\form();
            include $this->admin_tpl('model_field_add');
        }
    }

    /**
     * 模型字段编辑
     * @author   <jason>
     */
    public function edit($fieldid = 0) {
        $data = $this->model_field_db->detail($fieldid);
        if ($data == FALSE) {
            $this->error($this->model_field_db->getError());
        }
        if (submitcheck('dosubmit')) {
            $info = $_POST['info'];
            $info['fieldid'] = $fieldid;
            $info['setting'] = $_POST['setting'];
            $info['unsetgroupids'] = isset($_POST['unsetgroupids']) ? implode(',',$_POST['unsetgroupids']) : '';
            $info['unsetroleids'] = isset($_POST['unsetroleids']) ? implode(',',$_POST['unsetroleids']) : '';
            // 加载字段配置
            $model_info = $this->model_db->detail($data['modelid']);
            $model_table = $model_info['tablename'];
            $tablename = $_POST['issystem'] ? C('DB_PREFIX').$model_table : C('DB_PREFIX').$model_table.'_data';
            $field = $_POST['info']['field'];
            $minlength = $_POST['info']['minlength'] ? $_POST['info']['minlength'] : 0;
            $maxlength = $_POST['info']['maxlength'] ? $_POST['info']['maxlength'] : 0;
            $field_type = $_POST['info']['formtype'];
            require $this->field_dir.$field_type.DIRECTORY_SEPARATOR.'config.inc.php';
            if(isset($_POST['setting']['fieldtype'])) {
                $field_type = $_POST['setting']['fieldtype'];
            }
            $oldfield = $_POST['oldfield'];
            require $this->field_dir.'edit.sql.php';

            $fieldid = $this->model_field_db->update($info);
            if (!$fieldid) {
                $this->error($this->model_field_db->getError());
            }
            $this->public_field_cache($info['modelid']);
            $this->success('操作成功');
        } else {
            $show_header = $show_validator = $show_dialog = '';
            $form = new \Common\Library\form();
            include $this->field_dir.'fields.inc.php';
            $modelid = intval($_GET['modelid']);
            $fieldid = intval($_GET['fieldid']);
            extract($data);
            require $this->field_dir.$formtype.DIRECTORY_SEPARATOR.'config.inc.php';
            
            $setting = string2array($setting);
            ob_start();
            include $this->field_dir.$formtype.DIRECTORY_SEPARATOR.'field_edit_form.inc.php';
            $form_data = ob_get_contents();
            ob_end_clean();

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
            header("Cache-control: private");
            include $this->admin_tpl('model_field_edit');
            exit();
        }
    }

    /**
     * 模型字段删除
     * @author   <jason>
     */
    public function delete($fieldid = 0) {
        if (getgpc('fromhash') != session('FROMHASH')) {
            $this->error('请勿非法访问');
        }
        ((int) $fieldid > 0) || $this->error('参数错误');
        $field_data = $this->model_field_db->detail($fieldid);
        if (!$field_data) {
            $this->error($this->model_field_db->getError());
        }
        // 删除字段开始
        $model_info = $this->model_db->detail($field_data['modelid']);
        if (!$model_info) {
            $this->error('数据异常');
        }

        /* 禁止删除内部字段 */
        if ($data['iscore'] == 1) {
            $this->error('禁止删除内部字段');
        }

        /* 禁止删除的字段 */
        if (!empty($this->config['not_allow_fields']) && in_array($field_data['field'], $this->config['not_allow_fields'])) {
            $this->error('系统设定不允许删除此字段');
        }        

        /* 删除字段实际存在位置 */
        $field_delete_sql = $this->field_dir.'delete.sql.php';
        if (file_exists($field_delete_sql)) {
            $tablename = C('DB_PREFIX').(($field_data['issystem'] == 1) ? $model_info['tablename'] : $model_info['tablename'].'_data');
            $field = $field_data['field'];
            include $field_delete_sql;
        }
        /* 删除模型字段的记录 */
        $result = $this->model_field_db->delete($fieldid);
        if (!$result) {
            $this->error($this->model_field_db->getError());
        }
        $this->public_field_cache($modelid);
        $this->success('操作成功');
    }

    /**
     * 模型字段禁用
     * @author   <jason>
     */
    public function disabled($fieldid = 0) {
        $fileid = (int) $fieldid;
        $data = $this->model_field_db->detail($fieldid);
        if (!$data) {
            $this->error($this->model_field_db->getError());
        }
        if (in_array($data['field'], $this->config['forbid_fields'])) {
            $this->error('该字段不允许被禁用');
        }
        $disabled = ($data['disabled'] == 1) ? 0 : 1;
        $this->model_field_db->save(array('fieldid' => $fieldid, 'disabled' => $disabled));
        $this->success('操作成功');
    }

    /**
     * 模型字段排序
     * @author   <jason>
     */
    public function public_listorder() {
        if (submitcheck('dosubmit')) {
            $listorders = $_POST['listorders'];
            if (empty($listorders)) {
                $this->error('数据非法');
            }
            foreach ($listorders as $k => $v) {
                if (!is_numeric($k) || !is_numeric($v)) continue;
                $this->model_field_db->save(array('fieldid' => $k, 'listorder' => $v));
            }
            $this->public_field_cache();
            $this->success('操作成功');
        } else {
            $this->error('请勿非法访问');
        }
    }


    /**
     * 更新字段缓存
     * @param  int $modelid 指定模型
     * @return [type]           [description]
     */
    public function public_field_cache($modelid = 0) {
        $modelid = (int) $modelid;
        $ModelApi = new \Common\Api\ModelApi();
        $ModelApi->build_cache($modelid);
        return TRUE;
    }

    /**
     * 检查字段是否存在
     */
    public function public_checkfield() {
        exit('1');
        $field = strtolower($_GET['field']);
        $oldfield = strtolower($_GET['oldfield']);
        if($field==$oldfield) exit('1');
        $modelid = intval($_GET['modelid']);
        $model_cache = getcache('model','commons');
        $tablename = $model_cache[$modelid]['tablename'];
        $issystem = intval($_GET['issystem']);
        
        if($issystem) {
            $this->db->table_name = $this->db->db_tablepre.$tablename;
        } else {
            $this->db->table_name = $this->db->db_tablepre.$tablename.'_data';
        }
        $fields = $this->db->get_fields();
        
        if(array_key_exists($field,$fields)) {
            exit('0');
        } else {
            exit('1');
        }
    }

    /**
     * 模型字段检测(用于添加或编辑的效验)
     * @param  int $modelid 模型ID
     * @param  string $field   字段名
     * @author   <jason>
     */
    public function public_check_model_field($modelid = 0, $field = 0) {
        $modelid = (int) $modelid;
        $field = trim($field);
        $map = array();
        $map['modelid'] = $modelid;
        $map['field'] = $field;
        $count = $this->model_field_db->where($map)->count();
        if ($count) {
            $this->error('字段名已经存在');
        }
        $this->success('字段名可用');
    }

    /**
     * 获取字段相关参数
     * @author   <jason>
     */
    public function public_field_setting() {
        $fieldtype = $_GET['fieldtype'];
        require $this->field_dir.$fieldtype.DIRECTORY_SEPARATOR.'config.inc.php';
        ob_start();
        include $this->field_dir.$fieldtype.DIRECTORY_SEPARATOR.'field_add_form.inc.php';
        $data_setting = ob_get_contents();
        ob_end_clean();
        $settings = array('field_basic_table'=>$field_basic_table,'field_minlength'=>$field_minlength,'field_maxlength'=>$field_maxlength,'field_allow_search'=>$field_allow_search,'field_allow_fulltext'=>$field_allow_fulltext,'field_allow_isunique'=>$field_allow_isunique,'setting'=>$data_setting);
        echo json_encode($settings);
        return true;
    }
}