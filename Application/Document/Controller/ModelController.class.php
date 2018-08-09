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
class ModelController extends InitController {
    public function _initialize() {
    	parent::_initialize();
    	$this->model_db = D('Model');
        $this->model_field_db = D('ModelField');
        $this->field_dir = MODULE_PATH.'Fields/';
    }
    /**
     * 模型管理
     * @author   <jason>
     */
    public function init() {
        $page = I('page', '0');
        $sqlMap = array();
        $sqlMap['module'] = 'document';
    	$count = $this->model_db->where($sqlMap)->count();
        $datas = $this->model_db->where($sqlMap)->page($page, 10)->select();
        $pages = page($count, 10);
        $big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('add').'\', title:\'添加模型\', width:\'580\', height:\'420\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', '添加模型');
        include $this->admin_tpl('model_manage');
    }


    /**
     * 检测表名是否存在的
     * @return [type] [description]
     */
    public function public_check_tablename($tablename) {
        $oldtablename = I('oldtablename');
        if ($tablename == $oldtablename) {
            echo "1";
            exit();
        }
        $map = array();
        $map['tablename'] = $tablename;
        $count = $this->model_db->where($map)->count();
        if ($count) {
            echo "0";
            exit();
        }
        echo "1";
        exit();
    }
    /**
     * 添加模型
     * @author   <jason>
     */
    public function add() {
        if (submitcheck('dosubmit')) {
            $sqlfile = $this->field_dir.'model.sql';
            if (!file_exists($sqlfile)) {
                $this->error('缺少模型核心文件');
            }
            $info = $_POST['info'];
            $info['module'] = 'document';
            $info['category_template'] = $_POST['setting']['category_template'];
            $info['list_template'] = $_POST['setting']['list_template'];
            $info['show_template'] = $_POST['setting']['show_template'];
            if (isset($_POST['other']) && $_POST['other']) {
                $info['admin_list_template'] = $_POST['setting']['admin_list_template'];
                $info['member_add_template'] = $_POST['setting']['member_add_template'];
                $info['member_list_template'] = $_POST['setting']['member_list_template'];
            }
            $tablename = $info['tablename'];
            $modelid = $this->model_db->update($info);
            if ($modelid == FALSE) {
                $this->error($this->model_db->getError());
            }
            /* 入库模型信息 */
            $tablepre = C('DB_PREFIX');
            $model_sql = file_get_contents($sqlfile);
            $model_sql = str_replace('$basic_table', $tablepre.$tablename, $model_sql);
            $model_sql = str_replace('$table_data', $tablepre.$tablename.'_data', $model_sql);
            $model_sql = str_replace('$table_model_field',$tablepre.'model_field', $model_sql);
            $model_sql = str_replace('$modelid',$modelid,$model_sql);
            sqlexecute($model_sql);
            $this->public_cache();
            $this->success('模型创建成功', 'javascript:close_dialog();');
        } else {
            $form = new \Common\Library\form();
            $style_list = template_list(0);
            foreach ($style_list as $k=>$v) {
                $style_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
                unset($style_list[$k]);
            }
            $show_header = $show_validator = FALSE;
            $admin_list_template = $this->admin_list_template('document_list', 'name="setting[admin_list_template]"');
            include $this->admin_tpl('model_add');
        }
    }

    /**
     * 编辑模型
     * @author   <jason>
     */
    public function edit($modelid = 0) {
        $data = $this->model_db->detail($modelid);
        if (!$data) $this->error($this->model_db->getError());
        if (submitcheck('dosubmit')) {
            $info = $_POST['info'];
            $info['modelid'] = $modelid;
            $info['tablename'] = $data['tablename'];
            $info['setting'] = $_POST['setting'];
            $info['category_template'] = $_POST['setting']['category_template'];
            $info['list_template'] = $_POST['setting']['list_template'];
            $info['show_template'] = $_POST['setting']['show_template'];
            if (isset($_POST['other']) && $_POST['other']) {
                $info['admin_list_template'] = $_POST['setting']['admin_list_template'];
                $info['member_add_template'] = $_POST['setting']['member_add_template'];
                $info['member_list_template'] = $_POST['setting']['member_list_template'];
            }
            $result = $this->model_db->update($info);
            if ($result === FALSE) {
                $this->error($this->model_db->getError());
            }
            $this->public_cache();
            $this->success('操作成功', 'javascript:close_dialog();');
        } else {
            extract($data);
            $form = new \Common\Library\form();
            $style_list = template_list(0);
            foreach ($style_list as $k=>$v) {
                $style_list[$v['dirname']] = $v['name'] ? $v['name'] : $v['dirname'];
                unset($style_list[$k]);
            }
            $admin_list_template_f = $this->admin_list_template($admin_list_template, 'name="setting[admin_list_template]"');
            $show_header = $show_validator = FALSE;
            include $this->admin_tpl('model_edit');
        }
    }

    /**
     * 更新缓存
     * @author   <jason>
     */
    public function public_cache() {
        $this->model_db->_cache();
        $this->model_db->build_class();
        return FALSE;
    }

    /**
     * 删除模型
     * @author   <jason>
     * @return [type] [description]
     */
    public function delete($modelid = 0) {
        $modelid = (int) $modelid;
        if ($modelid < 1) {
            $this->error('参数错误');
        }
        $model_cache = getcache('model','commons');
        $model_table = $model_cache[$modelid]['tablename'];

        $this->model_db->drop_table($model_table);
        $this->model_db->drop_table($model_table.'_data');

        $result = $this->model_db->delete($modelid);
        if(!$result) $this->error($this->model_db->getError());
        $map = array();
        $map['modelid'] = $modelid;
        $this->model_field_db->where($map)->delete();
        $this->public_field_cache($modelid);
        $this->public_cache();
        $this->success('操作成功');
    }

    /**
     * 导入模型
     * @author   <jason>
     */
    public function import() {
        $this->error('模型导入功能暂不可用');
    }

    /**
     * 导出模型
     * @param  int $modelid 模型ID
     * @author   <jason>
     */
    public function export() {
        $this->error('模型导出功能暂不可用');
    }

    /**
     * 启用/禁用模型
     * @author   <jason>
     */
    public function disabled($modelid = 0) {
        $modelid = (int) $modelid;
        $data = $this->model_db->detail($modelid);
        if (!$data) {
            $this->error($this->model_db->getError());
        }
        $disabled = ($data['disabled'] == 1) ? 0 : 1;
        $result = $this->model_db->save(array('modelid' => $modelid, 'disabled' => $disabled));
        if ($result === FALSE) {
            $this->error($this->model_db->getError());
        }
        $this->public_cache();
        $this->success('操作成功');
    }
}