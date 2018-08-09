<?php
namespace Admin\Controller;
use \Admin\Controller\InitController;
class KeywordsController extends InitController{
	public function _initialize(){
		parent::_initialize();
		$this->db = model('keyword_hot');
	}
	public function init($catid = 0){
		$pagecurr = max(1,I('page',0,'intval'));
		$pagesize = 30;
		$sqlMap = array();
		$count = $this->db->where($sqlMap)->count();
		$infos = $this->db->where($sqlMap)->page($pagecurr,$pagesize)->order('listorder ASC')->select();
		$pages = page($count,$pagesize);
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('add').'\', title:\''.L('search_word_add').'\', width:\'450\', height:\'200\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('search_word_add'));
		include $this->admin_tpl('keywords_list');
	}
	/**
	 * 添加关键字 
	 */
	public function add(){
		if(submitcheck('dosubmit','GP')){
			$info = I('info');
			$info['inputtime'] = NOW_TIME;
			$info['updatetime'] = NOW_TIME;
			$result = $this->db->add($info);
			if(!$result){
				$this->error($this->db->getError());
			}
			$this->success('操作成功','javascript:close_dialog();');
		}else{
			$show_validator = $show_scroll = $show_header = true;
			include $this->admin_tpl('keywords_add');
		}
	}
	/**
	 * 修改关键字
	 */
	public function edit(){
		$keywordid = (int) I('keywordid');
		$rs = $this->db->where(array('keywordid'=>$keywordid))->find();
		extract($rs);
		if(submitcheck('dosubmit')){
			$info = I('info');
			$info['updatetime'] = NOW_TIME;
			$result = $this->db->where(array('keywordid'=>$keywordid))->save($info);
			if(!$result){
				$this->error($this->db->getError());
			}
			$this->success('操作成功','javascript:close_dialog();');
		}else{
			$show_validator = $show_scroll = $show_header = true;
			include $this->admin_tpl('keywords_edit');
		}
	}
	/**
	 * 删除关键字
	 */
	public function delete($keywordid = array()){
		$keywordid = (array) $keywordid;
		foreach ($keywordid as $id) {
			$id = (int) $id;
			$this->db->where(array('keywordid'=>$id))->delete();
		}
		$this->success('操作成功','javascript:close_dialog();');
	}
	/**
	 * 推荐
	 */
	public function recommend($keywordid = array()){
		$keywordid = (array) $keywordid;
		$sqlMap = array();
		$sqlMap['status'] = 1;
		$result = $this->db->where(array('keywordid'=>array('in',$keywordid)))->save($sqlMap);
		if(!$result){
			$this->error($this->db->getError());
		}
		$this->success('操作成功','javascript:close_dialog();');
	}
	/**
	 * 取消推荐
	 */
	public function unrecommend($keywordid = array()){
		$keywordid = (array) $keywordid;
		$sqlMap = array();
		$sqlMap['status'] = 0;
		$result = $this->db->where(array('keywordid'=>array('in',$keywordid)))->save($sqlMap);
		if(!$result){
			$this->error($this->db->getError());
		}
		$this->success('操作成功','javascript:close_dialog();');
	}
	/**
	 * 排序
	 */
	public function listorder(){
	if(submitcheck('dosubmit')){
			$sort = $_POST['listorder'];
			if(is_array($sort)){
				foreach($sort as $key=>$val){
					$key = (int) $key;$val = (int) $val;
					if($key < 1) continue;
					$this->db->where(array('keywordid'=>$key))->setField('listorder',$val);  
				}
			}
			$this->success('操作成功','javascript:close_dialog();');
		}else{
			$this->error('请勿非法提交');
		}
	}
	
}