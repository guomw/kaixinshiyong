<?php
namespace Document\Controller;
use \Admin\Controller\InitController;
class NavigateController extends InitController{
	public function _initialize(){
		parent::_initialize();
		$this->db = model('navigate');
	}
	
	public function init(){
		$pagecurr = max(1,I('page','0','intval'));
		$pagesize = 10;
		$sqlMap = array();
		$nav_count = $this->db->where($sqlMap)->count();
		$nav_lists = $this->db->where($sqlMap)->page($pagecurr,$pagesize)->order('listorder ASC')->select();
		$pages = page($nav_count,$pagesize);
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('add').'\', title:\''.L('nav_add').'\', width:\'450\', height:\'300\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('nav_add'));
		include $this->admin_tpl('nav_lists');
	}
	public function add(){
		if(submitcheck('dosubmit')){
			$info = I('info');
			$info['dateline'] = NOW_TIME;
			$result = $this->db->add($info);
			if(!$result){
				$this->error('添加失败');
			}
			$this->success('添加成功','javascript:close_dialog();');
		}else{
			include $this->admin_tpl('nav_add');
		}
	}
	public function edit($navid = 0){
		$navid = (int) $navid;
		if($navid < 1) $this->error('参数错误');
		$info = $this->db->where(array('navid'=>$navid))->find();
		extract($info);
		if(submitcheck('dosubmit')){
			$param = I('info');
			$param['navid'] = (int) I('navid');
			$param['updatetime'] = NOW_TIME;
			$result = $this->db->where(array('navid'=>$param['navid']))->save($param);
			if(!$result){
				$this->error('操作失败');
			}
			$this->success('操作成功','javascript:close_dialog();');
		}else{
			include $this->admin_tpl('nav_edit');
		}
	}
	/*删除*/
	public function delete($navid = array()){
		$navid = (array) $navid;
		if(empty($navid)) $this->error('参数错误');
		foreach ($navid as $k=>$id){
			$id = (int) $id;
			$this->db->where(array('navid'=>$id))->delete();
		}
		$this->success('删除成功');
	}
	/*排序*/
	public function listorder($listorder = array()){
		$listorder = (array) $listorder;
		if(empty($listorder)) $this->error('参数错误');
		foreach ($listorder as $k=>$v){
			$this->db->where(array('navid'=>$k))->setField('listorder',$v);
		}
		$this->success('操作成功');
	}
}