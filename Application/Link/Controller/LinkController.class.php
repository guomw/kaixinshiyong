<?php
namespace Link\Controller;
use \Admin\Controller\InitController;
/**
 *	后台友情链接
 */
class LinkController extends InitController{
	public function _initialize(){
		parent::_initialize();
		$this->db = D('Link');//表名称
		$this->link_type_db = D('LinkType');
	}
	/**
	 * 添加友情链接
	 */
	public function add(){
		if(submitcheck('dosubmit')){
			$link = $_POST['link'];
			$link['inputtime'] = time();
			$result = $this->db->update($link);
			if(!$result){
				$this->error($this->db->getError());
			}
			$this->success('添加成功','javascript:close_dialog();');
		}else{
			//友情链接分类
			$types = $this->link_type_db->select();
			$form = new \Common\Library\form();
			$show_header = $show_validator = TRUE;
			include $this->admin_tpl('link_add');
		}	
	}
	/**
	 * 友情链接列表
	 */
	public function init(){
		$pagecurr = max(1,I('page',0,'intval')); 
		$pagesize = 20;
		$sqlMap = array();
		$typeid = I('typeid');
 		if(!empty($typeid)){
 			$sqlMap['typeid'] = $typeid;
 		}
		$link_count = $this->db->where($sqlMap)->count();
		$link_list = $this->db->order('listorder asc')->where($sqlMap)->page($pagecurr,$pagesize)->select();
		$pages = page($link_count,$pagesize);//分页显示
		foreach($link_list as $key=>$val){
			$tid = (int) $val['typeid'];
			$link_list[$key]['typename'] = $this->link_type_db->getFieldByTypeid($tid,'typename');
		}
		$type_arr = $this->link_type_db->select();//查出所有的分类信息
		/* 拓展菜单  array数组中 0为链接地址  1为链接名称*/
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('add').'\', title:\''.L('add_link').'\', width:\'700\', height:\'500\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('add_link'));
		include $this->admin_tpl('link_list');
	}
	/**
	 * 修改友情链接
	 */
	public function edit($linkid = 0){
		$linkid = (int) $linkid;
		if($linkid < 1 ){$this->error('参数错误');}
		$linkinfo = $this->db->where(array('linkid'=>$linkid))->find();
		if(empty($linkinfo)){$this->error('修改的链接不存在');}
		if(submitcheck('dosubmit')){
			$link = $_POST['link'];
			$link['updatetime'] = NOW_TIME;
			$link['linkid'] = $linkid;
			$result = $this->db->update($link);
			if(!$result){
				$this->error($this->db->getError());
			}
			$this->success('修改成功', 'javascript:close_dialog();');
		}else{
			//友情链接分类
			$types = $this->link_type_db->select();
			$form = new \Common\Library\form();
			$show_header = $show_validator = TRUE;
			include $this->admin_tpl('link_edit');	
		}
	}
	/**
	 * 删除友情链接(删除单个)
	 */
	public function delete($linkid = 0){
		$linkid = (int) $linkid;
		if($linkid < 1){$this->error('参数错误');}
		$rs = $this->db->where(array('linkid'=>$linkid))->delete();
		if($rs){
			$this->success('删除成功');
		}else{
			$this->error('请勿非法提交');
		}
	}
	/**
	 * 批量删除
	 */
	public function all_delete(){
		$linkids = (array) I('linkid');
		if(submitcheck('dosubmit')){
			if(empty($linkids)){
				$this->error('参数错误');
			}
			foreach ($linkids as $linkid) {
				$linkid = (int) $linkid;
				$this->db->where(array('linkid'=>$linkid))->delete();
			}
			$this->success('操作成功');
		}else{
			$this->error('请勿非法提交');
		}
	}
	/**
	 * 排序
	 */
	public function sort(){
		if(submitcheck('dosubmit')){
			$sort = $_POST['listorders'];
			if(is_array($sort)){
				foreach($sort as $key=>$val){
					$key = (int) $key;$val = (int) $val;
					if($key < 1) continue;
					$this->db->where(array('linkid'=>$key))->setField('listorder',$val);  
				}
			}
			$this->success('操作成功');
		}else{
			$this->error('请勿非法提交');
		}
	}
	/**
	 * 友情链接审核
	 */
	public function check(){
		$checkid = (int) I('linkid');
		if($checkid < 1){$this->error('参数错误');}
		$sqlMap = array();
		$sqlMap['passed'] = 1;
		$sqlMap['linkid'] = $checkid;
		$result = $this->db->update($sqlMap);
		if(!$result){
			$this->error('请勿非法提交');
		}
		$this->success('操作成功');
	}

	/**
	 * 判断网站名称是否存在
	 */
	public function public_check_webname($link_name=''){
		$sqlMap = array();
		$sqlMap['webname'] = I('link_name');
		$count = model('link')->where($sqlMap)->count();
		if($count > 0){
			echo "0";exit();
		}else{
			echo "1";exit(); 
		}
	}
	/**
	 * 友情链接申请页面
	 */
	public function check_list(){
		$pagecurr = max(1,I('page',0,'intval'));
		$pagesize = 20;
		$sqlMap = array();
		$sqlMap['passed'] = 0;
		$link_check_count = $this->db->where($sqlMap)->count();
		$link_check_list = $this->db->where($sqlMap)->page($pagecurr,$pagesize)->order('listorder asc')->select();
		$pages = page($link_check_count,$pagesize);
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('Link/add').'\', title:\''.L('add_link').'\', width:\'700\', height:\'500\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('add_link'));
		include $this->admin_tpl('link_check_list');
	}
}
?>