<?php
namespace Announce\Controller;
use \Common\Controller\BaseController;
class IndexController extends BaseController{
	public function _initialize(){
		parent::_initialize();
		$this->db = D('Announce');
	}

	/* 列表页 */
    public function lists($id = 0) {
    	$pagecurr = max(1,I('page','0','intval'));
		$pagesize = 10;
        $id = I('id');
        $type = I('type','all');
        if ($type == 'all') {
        	 $count = $this->db->count();
     		 $announce = $this->db->page($pagecurr,$pagesize)->order('announceid desc')->select();
        }else{
        	 $count = $this->db->where(array('type'=>$type))->count();
        	 $announce = $this->db->where(array('type'=>$type))->page($pagecurr,$pagesize)->order('announceid desc')->select();
        }
        $pages = showPage($count,$pagecurr,$pagesize);
        $SEO=seo(0,"公告信息");
        include template('list_notice','document'); 
    }

    /* 详情页 */
    public function show($id = '') {
        $id = (int) $id;
        $type = I('type','all');
        $rs = $this->db->find($id);
        if($id < 1 || !$rs)
        showmessage('参数错误');
        extract($rs);

        /* 获取上/下一篇 */
        $sqlmap = array();
        $sqlmap = array('status' => 1);
        $sqlmap['announceid'] = array('LT', $id);
        $prenext['pre'] = $this->db->where($sqlmap)->order("`announceid` DESC")->find();
        $sqlmap['announceid'] = array('GT', $id);
        $prenext['next'] = $this->db->where($sqlmap)->order("`announceid` ASC")->find();
        $SEO=seo(0,"公告详情信息");
        include template('show_notice','document');
    }   
	
}