<?php
namespace Link\Controller;
use \Admin\Controller\InitController;
/**
 * 友情链接的审核申请
 * @author Administrator
 *
 */
class LinkCheckController extends InitController{
	public function _initialize(){
		parent::_initialize();
		$this->db = D('Link');
	}
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
	/**
	 * 友情链接的审核
	 */
	public function link_check(){
		$link_checkids = (array)I('linkid');//获取需要审核的id值
		if(submitcheck('dosubmit')){
			if(empty($link_checkids)){
				$this->error('参数错误');
			}
			$sqlMap = array();
			foreach ($link_checkids as $link_checkid) {
				$sqlMap['linkid'] = (int) $link_checkid;
				$sqlMap['passed'] = 1;
				$this->db->update($sqlMap);
			}
			$this->success('操作成功');
		}else{
			$this->error('请勿非法提交');
		}
	}
	/**
	 * 批量删除未审核的链接
	 */
	public function delete_all(){
		$linkids = (array) I('linkid');
		if(submitcheck('dosubmit')){
			if(empty($linkids)){$this->error('参数错误');}
			foreach($linkids as $linkid){
				$linkid = (int) $linkid;
				$this->db->where(array('linkid'=>$linkid))->delete();
			}
			$this->success('操作成功');
		}else{
			$this->error('请勿非法提交');
		}
	}
}