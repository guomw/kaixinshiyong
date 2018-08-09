<?php
namespace Link\Controller;
use \Admin\Controller\InitController;
class LinkTypeController extends InitController{
	public function _initialize(){
		parent::_initialize();
		$this->link_type_db = D('LinkType');
	}
	/**
	 * 添加分类
	 */
	public function add(){
		/* 拓展菜单 即添加友情链接的按钮 */
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('Link/add').'\', title:\''.L('add_link').'\', width:\'700\', height:\'500\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('add_link'));
		if(submitcheck('dosubmit')){
			$type = $_POST['type'];
			$type['time'] = time();
			$result = $this->link_type_db->add($type);
			if(!$result){
				$this->error($this->link_type_db->getError());
			}
			$this->success('添加成功');
		}else{
			$show_validator = TRUE;
			include $this->admin_tpl('link_type_add');
		}
	}
	/**
	 * 判断分类名称是否存在
	 */
	public function public_check_name(){
		$sqlMap = array();
		$sqlMap['typename'] = I('get.typename');
		$count = $this->link_type_db->where($sqlMap)->count();
		if($count > 0){
			echo "0";exit();
		}else{
			echo "1";exit(); 
		}
	}
	/**
	 * 分类管理页面
	 */
	public function manage(){
		/* 拓展菜单 即添加友情链接的按钮 */
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('Link/add').'\', title:\''.L('add_link').'\', width:\'700\', height:\'500\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('add_link'));
		$pagecurr = max(1,I('page',0,'intval'));
		$pagesize = 10;
		$sqlMap = array();
		$link_count = $this->link_type_db->where($sqlMap)->count();
		$link_list = $this->link_type_db->order('listorder asc')->where($sqlMap)->page($pagecurr,$pagesize)->select();
		$pages = page($link_count,$pagesize);
		include $this->admin_tpl('link_type_manage');
	}
	/**
	 *  分类管理修改
	 */
	public function edit($typeid = 0){
		$typeid = (int) $typeid;
		if($typeid < 1){
			$this->error('参数错误');
		}
		$typeinfo = $this->link_type_db->where(array('typeid'=>$typeid))->find();
		if(empty($typeinfo)){
			$this->error('没有该链接分类');
		}
		if(submitcheck('dosubmit')){
			$type = $_POST['type'];
			$type['typeid'] = $typeid;
			$type['time'] = NOW_TIME;
			$result = $this->link_type_db->save($type);
			if(!$result){
				$this->error($this->link_type_db->getError());
			}
			$this->success('修改成功', 'javascript:close_dialog();');
		}else{
			$show_validator = TRUE;
			include $this->admin_tpl('link_type_edit');
		}
	}
	/**
	 * 分类管理删除
	 */
	public function delete($typeid = array()){
		$typeid = (array) $typeid;
		if($typeid < 1){
			$this->error('参数错误');
		}
		$result = $this->link_type_db->where(array('typeid'=> array("IN", $typeid)))->delete();
		if($result){
			$this->success('操作成功');
		}else{
			$this->error('操作失败');
		}
	}
}
?>