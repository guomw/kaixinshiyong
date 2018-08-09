<?php
namespace Announce\Controller;
use \Admin\Controller\InitController;
class ArticleTypeController extends InitController{
	public function _initialize(){
		parent::_initialize();
		$this->db = D('Category');
	}
	public function init(){
		$pagecurr = max(1,I('page','0','intval'));
		$pagesize = 10;
		$sqlMap = array();
		$type = I('type');
		if(isset($type)){
			if($type == 1){//审核公告
				$sqlMap['passed'] = 0;
			}else if($type == 2){//会员公告
				$sqlMap['type'] = 0;
			}else if($type == 3){//商家公告
				$sqlMap['type'] = 1;
			}else{
				$sqlMap['passed'] = 1;
			}
		}
		$announce_count = $this->db->where($sqlMap)->count();
		$announce_lists = $this->db->where($sqlMap)->page($pagecurr,$pagesize)->select();
		$pages = page($announce_count,$pagesize);
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('add').'\', title:\'添加文章分类\', width:\'500\', height:\'300\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', 添加文章分类);
		include $this->admin_tpl('artcle_category_list');
	}
	/**
	 * 文章分类添加
	 */
	public function add(){
		if(submitcheck('dosubmit')){
			$info = I('announce');
			$info['starttime'] = strtotime($info['starttime']);
			$info['endtime'] = strtotime($info['endtime']);
			$info['username'] = cookie('admin_username');
			$result = $this->db->update($info);
			if(!$result){
				$this->error('操作失败');
			}
			$this->success('操作成功','javascript:close_dialog();');
		}else{
			$show_dialog = $show_header = $show_validator = TRUE;
			$form = new \Common\Library\form();
			include $this->admin_tpl('category_add');
		}
	}
	/**
	 * 修改公告
	 */
	public function edit(){
		$aid = I('aid');
		$info = $this->db->where(array('announceid'=>$aid))->find();
		extract($info);
		if(submitcheck('dosubmit')){
			$info = I('announce');
			$info['username'] = cookie('admin_username');
			$info['starttime'] = strtotime($info['starttime']);
			$info['endtime'] = strtotime($info['endtime']);
			$result = $this->db->update($info);
			if(!$result){
				$this->error('操作失败');
			}
			$this->success('操作成功','javascript:close_dialog();');
		}else{
			$show_dialog = $show_header = $show_validator = TRUE;
			$form = new \Common\Library\form();
			include $this->admin_tpl('announce_edit');
		}
	}
	/**
	 * 删除选定公告
	 */
	public function delete($aid=array()){
		$aid = (array) $aid;
		if(empty($aid)){$this->error('参数错误');}
		foreach($aid as $id){
			$id = (int) $id;
			$this->db->where(array('announceid'=>$id))->delete();						
		}
		$this->success('删除成功');
	}
	/**
	 * 通过选定公告
	 */
	public function public_approval($aid = array()){
		$aid = (array) $aid;
		if(empty($aid)){
			$this->error('参数错误');
		}
		foreach ($aid as $id) {
			$id = (int) $id;
			$this->db->where(array('announceid'=>$id))->setField('passed',1);
		}
		$this->success('操作成功');
	}
	/**
	 * 排序
	 */
	public function listorder(){
		if(submitcheck('dosubmit')){
			$listorders = (array) I('listorders');
			if(empty($listorders)){
				$this->error('参数错误');
			}
			foreach ($listorders as $k=>$v){
				 if(!is_numeric($k) || !is_numeric($v)) continue;
				 $this->db->where(array('announceid'=>$k))->setField('listorder',$v);
			}
			$this->success('排序操作成功');
		}else{
			$this->error('请勿非法提交');
		}
	}
}