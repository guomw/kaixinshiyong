<?php
namespace Member\Controller;
use \Admin\Controller\InitController;
class MerchantGroupController extends InitController{
	public function _initialize(){
		parent::_initialize();
		$this->db = D('MerchantGroup');
	}
	public function init(){
		$pagecurr = max(1,I('page',0,'intval'));
		$pagesize = 20;
		$sqlMap = array();
		$merchant_count = $this->db->where($sqlMap)->count(); 
		$merchant_lists = $this->db->where($sqlMap)->page($pagecurr,$pagesize)->order('listorder ASC')->select();
		$pages = page($merchant_count,$pagesize);
		$form = new \Common\Library\form();
		include $this->admin_tpl('merchant_type');
	}
	
	public function add(){
		if(submitcheck('dosubmit')){
			$info = $_POST['info'];
			$info['pricetype'] = implode(',',$info['pricetype']);
			$info['ordertype'] = implode(',',$info['ordertype']);
			$info['config'] = serialize($info['config']);
			$result = $this->db->update($info);
			if (!$result) {
				$this->error($this->db->getError());
			}
			$this->db->build_cache();
			$this->success('操作成功');
		}else{
			$form = new \Common\Library\form();
			include $this->admin_tpl('merchant_type_add');
		}
	}
	public function edit($groupid = 0){
		$groupid = (int) $groupid;
		$typeinfo = $this->db->getByGroupid($groupid);
		extract($typeinfo);
		$pricetype = explode(',',$pricetype);//收费标准
		$ordertype = explode(',',$ordertype);//下单方式
		$config = unserialize($config);
		$rebate = $config['rebate'];//购物返利
		$trial = $config['trial'];//免费试用
		$commission = $config['commission'];//免费试用
		$bonus_count = count($commission['service_price']);
		$postal = $config['postal'];//9.9包邮
		$brand = $config['brand'];//品牌折扣
		$special =  $config['special'];
		$a_b_count = count($trial['a_b']);
		$red_count = count($trial['red']);

		if(IS_POST){
			$post = I('post.');
			$info = $post['info'];
			$info['pricetype'] = implode(',',$info['pricetype']);
			$info['ordertype'] = implode(',',$info['ordertype']);
			$info['config'] = serialize($info['config']);
			$info['groupid'] = $info['groupid'];
			$result = $this->db->update($info);
			if (!$result) {
				$this->error($this->db->getError());
			}
			$this->db->build_cache();
			$this->success('操作成功');
		}else{
			$form = new \Common\Library\form();
			include $this->admin_tpl('merchant_type_edit');
		}
	}
	/**
	 * 检测商家类型名称
	 */
	public function public_checkname_ajax(){
		$username = isset($_GET['name']) && trim($_GET['name']) ? trim($_GET['name']) : exit(0);
		if(CHARSET != 'utf-8') {
			$username = iconv('utf-8', CHARSET, $username);
			$username = addslashes($username);
		}
		$status = $this->db->where(array('name'=>$username))->count();
		if($status > 0) {
			exit('0');
		} else {
			exit('1');
		}
	}
	/**
	 * 删除
	 */
	public function delete($groupid = array()){
		$groupid = (array) $groupid;
		if(empty($groupid)){
			$this->error('参数错误');
		}
		foreach ($groupid as $k=>$v) {
			$v = (int) $v;
			$this->db->where(array('groupid'=>$v))->delete();
		}
		$this->success('删除成功');
	}
	/**
	 * 排序
	 */
public function public_sort($listorders = array()) {
		$listorders = (array) $listorders;
		if (submitcheck('dosubmit')) {
			if (empty($listorders)) {
				$this->error('参数错误');
			}
			foreach ($listorders as $id => $sort) {
				if(!is_numeric($id) || !is_numeric($sort)) continue;
				$this->db->where(array('groupid' => $id))->setField('listorder', $sort);
			}
			
			$this->success('操作成功');
		} else {
			$this->error('请勿非法访问'.ACTION_NAME);
		}
	}
}