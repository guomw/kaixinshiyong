<?php
namespace Member\Controller;
use \Admin\Controller\InitController;
class TrialReportController extends InitController{
	public function _initialize(){
		parent::_initialize();
		$this->db = model('report_member');
		$this->pagesize = 10;
		$this->pagecurr = max(1,I('page',0,'intval'));
	} 
	
	public function init(){
		$sqlMap = array();
		$count = $this->db->where($sqlMap)->count();
		$report_lists = $this->db->where($sqlMap)->order('inputtime DESC')->select();
		$form = new \Common\Library\form();
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('add',array('t'=>1)).'\', title:\'试用达人添加\', width:\'700\', height:\'500\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', '试用达人添加');
		include $this->admin_tpl('report_member_lists');
	}
	
	public function add(){
		if(IS_POST){
			$info = I('post.info');
			$info['inputtime'] = NOW_TIME;
			if(empty($info['name'])) $this->error('请输入达人名称');
			$result = $this->db->add($info);
			if(!$result){
				$this->error('添加失败');
			}
			$this->success('添加成功','javascript:close_dialog();');
		}else{		
			include $this->admin_tpl('report_member_add');
		}
	}
	
	/**
	* 删除
	*/
	public function delete($ids = array()){
		$ids = (array) $ids;
		if(empty($ids)) $this->error('参数错误');
		foreach ($ids as $k=>$id){
			$id = (int) $id;
			$this->db->where(array('id'=>$id))->delete();
		}
		$this->success('删除成功');
	}
	
	//修改
	public function edit($id = 0){
		$id = (int) $id;
		if($id < 1) $this->error('参数错误');
		$r = $this->db->getById($id);
		extract($r);
		if(IS_POST){
			$info = I('post.info');
			$info['updatetime'] = NOW_TIME;
			$result = $this->db->where(array('id'=>$id))->save($info);
			if(!$result){
				$this->error('修改错误');
			}
			$this->success('修改成功','javascript:close_dialog();');
		}else{
			include $this->admin_tpl('report_member_edit');
		}
	}
}