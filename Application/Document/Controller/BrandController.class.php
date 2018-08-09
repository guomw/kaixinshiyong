<?php
namespace Document\Controller;
use \Admin\Controller\InitController;
class BrandController extends InitController{
	public function _initialize(){
		parent::_initialize();
		$this->pagecurr = max(1,I('page',0,'intval'));
		$this->pagesize = 10;
		$this->db = model('brand');
	}
	
	public function init(){
		$sqlMap = array();
		$brand_count =   $this->db->where($sqlMap)->count();
		$brand_lists = $this->db->where($sqlMap)->page($this->pagecurr,$this->pagesize)->order('listorder ASC')->select();
		$pages = page($brand_count,$this->pagesize);
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('add').'\', title:\''.L('brand_add').'\', width:\'700\', height:\'500\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('brand_add'));
		include $this->admin_tpl('brand_lists');
	}
	/*添加*/
	public function add(){
		if(submitcheck('dosubmit')){
			$info = I('brand');
			$info['dateline'] = NOW_TIME;
			$result = $this->db->add($info);
			if(!$result){
				$this->error('添加品牌失败');
			}
			$this->success('添加品牌成功','javascript:close_dialog();');
		}else{
			$form = new \Common\Library\form();
			include $this->admin_tpl('brand_add');
		}
	}
	/*修改*/
	public function edit($id=0){
		$id = (int) $id;
		$info = $this->db->where(array('id'=>$id))->find();
		extract($info);
		if(submitcheck('dosubmit')){
			$param = I('brand');
			$param['updatetime'] = NOW_TIME;
			$result = $this->db->where(array('id'=>$id))->save($param);
			if(!$result){
				$this->error('操作失败','javascript:close_dialog();');
			}
			$this->success('修改成功','javascript:close_dialog();');
		}else{
			$form = new \Common\Library\form();
			include $this->admin_tpl('brand_edit');
		}
	}
	/*排序*/
	public function listorder($listorder = array()){
		$listorder = (array) $listorder;
		if(empty($listorder))  $this->error('参数错误');
		foreach ($listorder as $k=>$id) {
			$this->db->where(array('id'=>$k))->setField('listorder',$id);
		}
		$this->success('操作成功');
	}
	/*删除*/
	public function delete($id=array()){
		$id = (array) $id;
		if(empty($id)) $this->error('参数错误');
		foreach ($id as $k=>$v){
			$v = (int) $v;
			$this->db->where(array('id'=>$v))->delete();
		}
		$this->success('删除成功');
	}
}