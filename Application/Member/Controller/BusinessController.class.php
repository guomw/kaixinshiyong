<?php
namespace Member\Controller;
use Admin\Controller\InitController;
if (!defined('MODULE_CACHE')) define('MODULE_CACHE', DATA_PATH.'caches_model/');
class BusinessController extends InitController{
	public function _initialize(){
		parent::_initialize();
		$this->db = model('member');
		$this->models = getcache('model','commons');
		$this->modelid = I('modelid','2','intval');
		$this->tablename = $this->models[$this->modelid]['tablename'];
		$this->pagesize = 20;
	}
	
	public function manage(){
		$pagecurr = max(1,I('page',0,'intval'));
		//所属专员
		$r = model('admin_role')->field('roleid')->where(array('rolename'=>'招商专员'))->find();
		$attract_lists = model('admin')->field('userid,username')->where(array('roleid'=>$r['roleid']))->select();
		$roleid = session('roleid');
		$rolename = model('admin_role')->getFieldByRoleid($roleid,'rolename');
		
		$sqlMap = array();
		$sqlmap = array();
		if($rolename == '招商专员'){
			$attract = (session('userid')) ? session('userid') : -99;
			$sqlMap['agent_id'] = session('userid');
		}else{
			$attract = (isset($_GET['attract'])) ? $_GET['attract'] : -99;
		}
		$modelid = $sqlMap['modelid'] = $this->modelid;
		
		if (IS_GET) {
			$info = I('get.');
			$info['start_time'] = (!empty($info['start_time'])) ? strtotime($info['start_time']) : 0;
			$info['end_time'] = (!empty($info['end_time'])) ? strtotime($info['end_time']) : 0;
			/* 注册时间 */
			if ($info['start_time'] && $info['end_time']){
				$sqlMap['regdate'] = array("BETWEEN",array($info['start_time'],$info['end_time']));
			}else{
				if ($info['start_time'] > 0) {
					$sqlMap['regdate'] = array("EGT", $info['start_time']);
				}
				if ($info['end_time'] > 0) {
					$sqlMap['regdate'] = array("ELT", $info['end_time']);
				}
			}
			/* 当前状态 */
			$info['status'] = (int) $info['status'];
			if ($info['status'] > 0) {
				$sqlMap['islock'] = $info['status'];
			}
			//所属专员
			if($info['attract'] > -99){
				$sqlMap['agent_id'] = $info['attract'];
			}
			/* 会员组 */
			$info['groupid'] = (int) $info['groupid'];
			if ($info['groupid'] > 0) {
				$sqlMap['groupid'] = $info['groupid'];
			}
			/* 关键字搜索类型 */
			$info['type'] = (int) $info['type'];
			if (trim($info['keyword'])) {
				switch ($info['type']) {
					case '1': //用户名
					 $store  = model('merchant_store')->where(array('contact_want'=>array("LIKE", "%".$info['keyword']."%")))->getfield('userid',true);
					 if(!$store){
					 	$uids = model('member_merchant')->where(array('contact_want'=>array("LIKE", "%".$info['keyword']."%")))->getfield('userid',true);
					 }else{
					 	$uids = $store;

					 }
					
					$sqlMap['userid'] = array("IN", $uids);
						break;
					case '2':

					$contact = model('member_merchant')->where(array('contact_name'=>array("LIKE", "%".$info['keyword']."%")))->getfield('userid',true);
					$sqlMap['userid'] = array("IN", $contact);
						break;

					case '3': // 邮箱
						$sqlMap['email'] = array("LIKE", "%".$info['keyword']."%");
						break;

					case '4': // 手机
						$sqlMap['phone'] = array("LIKE", "%".$info['keyword']."%");
						break;
					case '5': // 手机
						$sqlMap['userid'] = array("EQ",$info['keyword']);
						break;
			
					default:
						break;
				}
			}
		}
		//会员组
		$grouplist = getcache('merchant_group','member');
		foreach ($grouplist as $k => $v) $grouplist[$k] = $v['name'];
		$count = $this->db->where($sqlMap)->count();
		$manage_lists = $this->db->where($sqlMap)->page($pagecurr,$this->pagesize)->order('userid DESC ,regdate DESC')->select();
// 		echo $this->db->getLastSql();
		foreach ($manage_lists as $k=>$v) {
			$sqlmap['userid'] = $v['userid'];

			$rs = M($this->tablename)->where($sqlmap)->find();
			foreach ($rs as $_k=>$_v) {
				$manage_lists[$k][$_k] = $_v;
			}
			$manage_lists[$k]['count'] = model('merchant_store')->where(array('userid'=>$v['userid']))->count();

			$manage_lists[$k]['new_store_name'] = model('merchant_store')->where(array('userid'=>$v['userid']))->order('is_default desc')->find();
			$manage_lists[$k]['new_contact_name'] = model('merchant_store')->where(array('userid'=>$v['userid']))->order('is_default desc')->find();;

			//活动商品
			$manage_lists[$k]['activity_count'] = model('product')->where(array('company_id'=>$v['userid']))->count();
			//所属专员
			$manage_lists[$k]['admin_name'] = model('admin')->getFieldByUserid($v['agent_id'],'username');
		}
		$pages = page($count,$this->pagesize);
		$big_menu = array('javascript:window.top.art.dialog({id:\'add\',iframe:\''.U('add').'\', title:\''.L('merchant_add').'\', width:\'700\', height:\'500\', lock:true}, function(){var d = window.top.art.dialog({id:\'add\'}).data.iframe;var form = d.document.getElementById(\'dosubmit\');form.click();return false;}, function(){window.top.art.dialog({id:\'add\'}).close()});void(0);', L('merchant_add'));
		$form = new \Common\Library\form();
 		include $this->admin_tpl('merchant_lists');
	}
	/*新增商家*/
	public function add(){
		//会员模型
		$models = getcache('model', 'commons');
		$groups = getcache('merchant_group','member');
		$modelid = I('modelid', '2', 'intval');
		$groupid = I('groupid', '1', 'intval');
		$setting = getcache('setting', 'member');
		if (IS_POST) {
			$info = $_POST['info'];
			$info['modelid'] = $modelid;
			$MemberLogic = D('Member', 'Logic');
			$result = $MemberLogic->register($info);
			if (!$result) {
				$this->error($MemberLogic->getError());
			}
			$this->success('操作成功');
		} else {
			$form =  new \Common\Library\form();
			$show_header = true;
			$show_validator = true;
			require MODULE_CACHE.'member_form.class.php';
			$member_form = new \member_form($modelid);
			//$forminfos = $member_form->get();
			include $this->admin_tpl('merchant_add');
		}
	}


	public function store(){

		$pagecurr = max(1,I('page',0,'intval'));
		//所属专员
		$r = model('admin_role')->field('roleid')->where(array('rolename'=>'招商专员'))->find();
		$attract_lists = model('admin')->field('userid,username')->where(array('roleid'=>$r['roleid']))->select();
		$roleid = session('roleid');
		$rolename = model('admin_role')->getFieldByRoleid($roleid,'rolename');
		
		$sqlMap = array();
		$sqlmap = array();
		if($rolename == '招商专员'){
			$attract = (session('userid')) ? session('userid') : -99;
			$sqlMap['agent_id'] = session('userid');
		}else{
			$attract = (isset($_GET['attract'])) ? $_GET['attract'] : -99;
		}
		$modelid = $sqlMap['modelid'] = $this->modelid;
		
		if (IS_GET) {
			$info = I('get.');
			$info['start_time'] = (!empty($info['start_time'])) ? strtotime($info['start_time']) : 0;
			$info['end_time'] = (!empty($info['end_time'])) ? strtotime($info['end_time']) : 0;
			/* 注册时间 */
			if ($info['start_time'] && $info['end_time']){
				$sqlMap['regdate'] = array("BETWEEN",array($info['start_time'],$info['end_time']));
			}else{
				if ($info['start_time'] > 0) {
					$sqlMap['regdate'] = array("EGT", $info['start_time']);
				}
				if ($info['end_time'] > 0) {
					$sqlMap['regdate'] = array("ELT", $info['end_time']);
				}
			}
			/* 当前状态 */
			$info['status'] = (int) $info['status'];
			if ($info['status'] > 0) {
				$sqlMap['islock'] = $info['status'];
			}
			//所属专员
			if($info['attract'] > -99){
				$sqlMap['agent_id'] = $info['attract'];
			}
			/* 会员组 */
			$info['groupid'] = (int) $info['groupid'];
			if ($info['groupid'] > 0) {
				$sqlMap['groupid'] = $info['groupid'];
			}
			/* 关键字搜索类型 */
			$info['type'] = (int) $info['type'];
			if (trim($info['keyword'])) {
				switch ($info['type']) {
					case '1': //用户名
					$uids = model('member_merchant')->where(array('store_name'=>array("LIKE", "%".$info['keyword']."%")))->getfield('userid',true);
					$sqlMap['userid'] = array("IN", $uids);
						break;
					case '2':

					$contact = model('member_merchant')->where(array('contact_name'=>array("LIKE", "%".$info['keyword']."%")))->getfield('userid',true);
					$sqlMap['userid'] = array("IN", $contact);
						break;

					case '3': // 邮箱
						$sqlMap['email'] = array("LIKE", "%".$info['keyword']."%");
						break;

					case '4': // 手机
						$sqlMap['phone'] = array("LIKE", "%".$info['keyword']."%");
						break;
			
					default:
						break;
				}
			}
		}
		//会员组
		$grouplist = getcache('merchant_group','member');
		foreach ($grouplist as $k => $v) $grouplist[$k] = $v['name'];
		$count = model('merchant_store')->where($sqlMap)->count();
		$manage_lists = model('merchant_store')->where($sqlMap)->page($pagecurr,$this->pagesize)->order('id DESC')->select();
// 		echo $this->db->getLastSql();
		foreach ($manage_lists as $k=>$v) {
			$sqlmap['userid'] = $v['userid'];
			$rs = M($this->tablename)->where($sqlmap)->find();
			foreach ($rs as $_k=>$_v) {
				$manage_lists[$k][$_k] = $_v;
			}
		  $manage_lists[$k]['type'] = model('product_category')->where(array('catid'=>$v['industry']))->getField('catname');
		}
		$pages = page($count,$this->pagesize);
		
		$form = new \Common\Library\form();
 		include $this->admin_tpl('store_lists');

	}
}