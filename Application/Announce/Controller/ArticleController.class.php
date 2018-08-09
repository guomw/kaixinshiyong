<?php
namespace Announce\Controller;
use \Admin\Controller\InitController;
class ArticleController extends InitController{
	public function _initialize(){
		parent::_initialize();
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
		
			include $this->admin_tpl('article_add');
		}
	}
	
}