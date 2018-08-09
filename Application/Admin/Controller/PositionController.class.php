<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Admin\Controller;
Class PositionController extends InitController {
    public function _initialize() {
    	parent::_initialize();
    	$this->db = D('Position');
        $this->position_data_db = D('PositionData');
    }

    /**
     * 推荐位管理
     * @author   <jason>
     */
    public function init() {
        $models = getcache('model','commons');      
        //推荐栏位
        $categorys = getcache('category','commons');
        $page = I(C('VAR_PAGE'), '0', 'int');
        $count = $this->db->count();
        $infos = $this->db->order('listorder ASC')->limit(10)->page($page)->select();
        $pages = page($count, 10);
        $big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('add').'\', title:\''.L('posid_add').'\', width:\'500\', height:\'360\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('posid_add'));
        include $this->admin_tpl('position_list');
    }

    /* 角色添加 */
    public function add() {
        $models = getcache('model','commons');
        if (submitcheck('dosubmit')) {
            $info = $_POST['info'];
            $result = $this->db->update($info);
            if (!$result) {
                $this->error($this->db->getError());
            }
            $this->db->build_cache();
            $this->success('操作成功', 'javascript:close_dialog();');
        } else {
            $modelinfo = array();
            foreach ($models as $key => $value) {
                if ($value['module'] != 'document') continue;
                $modelinfo[$key] = $value['name'];
            }
            $form = new \Common\Library\form();
            $show_header = 1;
            include $this->admin_tpl('position_add');
        }
    }

    /**
     * 推荐位编辑
     * @author   <jason>
     */
    public function edit($posid = 0) {
        $posid = (int) $posid;
        $data = $this->db->detail($posid);
        if (!$data) $this->error($this->db->getError());
        if (submitcheck('dosubmit')) {
            $info = $_POST['info'];
            $info['posid'] = $posid;
            $result = $this->db->update($info);
            if (!$result) $this->db->getError();
            $this->db->build_cache();
            $this->success('操作成功', 'javascript:close_dialog();');
        } else {
            extract($data);
            $models = getcache('model','commons');
            $modelinfo = array();
            foreach ($models as $key => $value) {
                if ($value['module'] != 'document') continue;
                $modelinfo[$key] = $value['name'];
            }
            $form = new \Common\Library\form();
            $show_header = 1;
            include $this->admin_tpl('position_edit');
        }
    }

    /**
     * 推荐位删除
     * @author   <jason>
     */
    public function delete($posid = 0) {
        $posid = (int) $posid;
        if (getgpc('fromhash') != session('FROMHASH') || $posid < 1) {
            $this->error('参数错误，请勿非法提交');
        }
        $map = array('posid' => $posid);
        $this->db->where($map)->delete();
        $this->position_data_db->where($map)->delete();
        $this->db->build_cache();
        $this->success('推荐位删除成功');
    }

    /**
     * 推荐位排序
     * @author   <jason>
     */
    public function listorder() {
        if (!submitcheck('dosubmit')) {
            $this->error('请勿非法请求');
        }
        $listorders = (array) $_POST['listorders'];
        if (!empty($listorders)) {
            foreach ($listorders as $posid => $listorder) {
                if (!is_numeric($posid) || !is_numeric($listorder)) continue;
                $this->db->update(array('posid' => $posid, 'listorder' => $listorder));
            }
            $this->db->build_cache();
        }
        $this->success('操作成功');
    }

    /**
     * 推荐位信息管理
     * @author   <jason>
     */
    public function public_item($posid = 0) {
        $categorys = getcache('category','commons');
        $posid = (int) $posid;
        $infos = $this->position_data_db->where(array('posid' => $posid))->select();
        if (is_array($infos) &&!empty($infos)) {
            foreach ($infos as $key => $value) {
                $value['catname'] = $categorys[$value['catid']]['catname'];
                $value['data'] = string2array($value['data']);
                $infos[$key] = $value;
            }
        }
        include $this->admin_tpl('position_items');
    }

    /**
     * 信息排序
     * @author   <jason>
     */
    public function public_item_listorder() {
        $posid = (int) $_POST['posid'];
        $listorders = $_POST['listorders'];
        if (submitcheck('dosubmit')) {
            foreach ($listorders as $items => $listorder) {
                $item = explode("-", $items);
                $data = array();
                $data['posid'] = $posid;
                $data['catid'] = (int) $item['0'];
                $data['id'] = (int) $item['1'];
                dump($data);
                $this->position_data_db->where($data)->setField('listorder', $listorder);
            }
            $this->success('排序更新成功');
        } else {
            $this->error('请勿非法请求');
            exit();
        }
    }

    /**
     * 移除单项
     * @return [type] [description]
     */
    public function public_item_remove($posid = 0) {
        $items = (array) $_POST['items'];
        if (submitcheck('dosubmit')) {
            if (empty($items)) $this->error('请选择要移除的数据');
            foreach ($items as $item) {
                $item = explode("-", $item);
                $map = array();
                $map['posid'] = $posid;
                $map['modelid'] = (int) $item['1'];
                $map['id'] = (int) $item['0'];
                $this->position_data_db->where($map)->delete();
            }
            $this->success('指定数据移除成功');
        } else {
            $this->error('请勿非法请求');
        }
    }

    /**
     * 信息管理
     * @author   <jason>
     */
    public function public_item_manage() {
        $id = I('id', 0, 'intval');
        $posid = I('posid', 0, 'intval');
        $modelid = I('modelid', 0, 'intval');
        $map = array('id' => $id, 'modelid' => $modelid, 'posid' => $posid);
        $rs = $this->position_data_db->where($map)->find();
        $data = string2array($rs['data']);
        if (submitcheck('dosubmit')) {
            $info = $_POST;
            $_POST['info']['inputtime'] = strtotime($_POST['info']['inputtime']);
            $info['synedit'] = (int) $_POST['synedit'];
            $info['data'] = array2string($_POST['info']);
            $result = $this->position_data_db->where($map)->save($info);
            if (!$result) {
                $this->error('操作失败', -1);
            }
            $this->success('操作成功', 'javascript:close_dialog();');
        } else {
            $form = new \Common\Library\form();
            $rs = array_merge($rs, $data);
            $show_header = FALSE;
            include $this->admin_tpl('position_item_manage');
        }
    }

    /*视频预览*/
    public function preview(){
    	$thumb = $_GET['thumb'];
    	include $this->admin_tpl('position_priview');
    }
}