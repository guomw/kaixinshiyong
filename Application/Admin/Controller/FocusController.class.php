<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Admin\Controller;
use \Admin\Controller\InitController;
class FocusController extends InitController{
	public function _initialize(){
		parent::_initialize();
		$this->pagecurr = max(1,I('page',0,'intval'));
		$this->pagesize = 10;
		$this->db = model('focus');
	}
	public function index(){
		$sqlMap = array();
		$t = (int) I('t');//轮播图片类型
		if($t){
			$sqlMap['type'] = $t;
		}
		$count =   $this->db->where($sqlMap)->count();
		$lists = $this->db->where($sqlMap)->page($this->pagecurr,$this->pagesize)->order('listorder ASC')->select();
		$pages = page($count, $this->pagesize);
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('add').'\', title:\'添加新幻灯\', width:\'500\', height:\'350\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', '添加新幻灯');
		include $this->admin_tpl('focus_lists');
	}
	public function add(){
		if(submitcheck('dosubmit')){
			$info = I('image');
			$info['starttime'] = strtotime($info['starttime']);
			$info['endtime'] = strtotime($info['endtime']);
			$info['color'] =$info['color'];

			$info['updatetime'] = NOW_TIME;
			$result = $this->db->add($info);
			if(!$result){
				$this->error('操作失败','javascript:close_dialog();');
			}
			$this->success('操作成功','javascript:close_dialog();');
		}else{
			$form = new \Common\Library\form();
			include $this->admin_tpl('focus_add');
		}
	}
	public function edit($id = 0){
		$id = (int) $id;
		$info = $this->db->where(array('id'=>$id))->find();
		extract($info);
		if(submitcheck('dosubmit')){
			$param = I('image');
			$param['starttime'] = strtotime($param['starttime']);
			$param['endtime'] = strtotime($param['endtime']);
			$param['color'] =$param['color'];

			$param['updatetime'] = NOW_TIME;
			$result = $this->db->where(array('id'=>$id))->save($param);
			if(!$result){
				$this->error('操作失败','javascript:close_dialog();');
			}
			$this->success('修改成功','javascript:close_dialog();');
		}else{
			$form = new \Common\Library\form();
			include $this->admin_tpl('focus_edit');
		}
	}

	public function delete($id = array()){
		$id = (array) $id;
		if(empty($id)) $this->error('参数错误');
		foreach ($id as $k=>$v){
			$v = (int) $v;
			$this->db->where(array('id'=>$v))->delete();
		}
		$this->success('删除成功');
	}
	/*排序*/
	public function public_listorder($listorder = array()){
		$listorder = (array) $listorder;
		if(empty($listorder))  $this->error('参数错误');
		foreach ($listorder as $k=>$id) {
			$this->db->where(array('id'=>$k))->setField('listorder',$id);
		}
		$this->success('排序成功');
	}
}