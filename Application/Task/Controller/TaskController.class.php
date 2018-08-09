<?php
namespace Task\Controller;
use \Admin\Controller\InitController;
class TaskController extends InitController{
	public function _initialize(){
		parent::_initialize();
		 $this->source = model('shop_set')->getField('id, name', TRUE);
		 $this->task_status = C('TASK_STATUS');
		 $this->db = model('task_day');
		 $this->pagecurr = max(1,I('page'));
		 $this->pagesize = 10;
	}
	/*日赚任务列表*/
	public function init(){
		$sqlMap = array();
		$param = I('param.');
		$status = (isset($param['status'])) ? (int) $param['status'] : -99;
		if($status > -99) {
			$sqlMap['status'] = $param['status'];
		}
		$count = $this->db->where($sqlMap)->count();
		$lists = $this->db->where($sqlMap)->page($this->pagecurr,$this->pagesize)->order('id DESC,updatetime DESC')->select();
		foreach ($lists as $k=>$v){
			$factory = new \Task\Factory\task($v['id']);
			$v = $factory->task_info;
			$v['store_name'] = model('member_merchant')->where(array('userid' => $v['company_id']))->getField('store_name');
			$lists[$k] = $v;
		}
		$pages = page($count,$this->pagesize);
		$show_dialog = $show_validator = 1;
		$form = new \Common\Library\form();
		include $this->admin_tpl('task_list');
	}
	
	/*日赚任务添加*/
	public function add(){
		if(IS_POST){
			$info = I('info');
			$result = $this->db->update($info);
			if(!$result){
				$this->error($this->db->getError());
			}else{
				$this->success('任务添加成功', U('init'));
			}
		}else{
			$show_header = $show_dialog = $show_validator = 1;
			$form = new \Common\Library\form();
			include $this->admin_tpl('task_add');
		}
	}
	
	/*修改*/
	public function edit($id = 0){
		$rs = $this->db->getById($id);
		if(!$rs) $this->error($this->db->getError());
		$rs['goods_albums'] = string2array($rs['goods_albums']);
		if(submitcheck('dosubmit')) {
			$info = $_POST['info'];
			$info['id'] = $id;
			$result = $this->db->update($info);
			if(!$result) {
				$this->error($this->db->getError());
			} else {
				$this->success('编辑成功', U('init'));
			}
		} else {
			$show_header = $show_dialog = $show_validator = 1;
			$form = new \Common\Library\form();
			include $this->admin_tpl('task_edit');
		}
	}
	
	/*确认付款*/
	public function pay($task_id = 0){
		$task_id = (int) $task_id;
		if($task_id < 1) $this->error('该条信息不存在');		
		$factory = new \Task\Factory\task($task_id);
		//将该产品的佣金及服务费存入数据库
		$rs = $factory->admin_pay();
		if(!$rs) $this->error($factory->getError());
		//设置订单状态
		$factory->set_status('-2', '后台管理员确认付款');
		$this->success('确认付款成功');
	}
	/*审核通过*/
	public function pass($task_id){
		$factory = new \Task\Factory\task($task_id);
		$rs = $factory->task_info;
		if(!$rs) $this->error('参数错误');
		if(IS_POST) {
			extract($_POST);
			if (empty($start_time)) {
				$this->error('请设置上线时间');
			}
			if(is_array($start_time)) {
				$start_time = intval($start_time['Y']).'-'.intval($start_time['m']).'-'.intval($start_time['d']).' '.intval($start_time['H']).':'.intval($start_time['i']);
				$start_time = strtotime($start_time);
			}
			$result = $factory->pass($start_time, $msg);
			if(!$result) {
				$message = ($factory->getError()) ? $factory->getError() : '操作失败';
				$this->error($message);
			} else {
				$this->success('操作成功', 'javascript:close_dialog();');
			}
		} else {
			$form = new \Common\Library\form();
			$show_dialog = FALSE;
			include $this->admin_tpl('task_pass');
		}
	}
	/*拒绝*/
	public function refuse($task_id){
		$factory = new \Task\Factory\task($task_id);
		$result = $factory->refuse('审核失败并退还保证金');
		if(!$result) {
			$this->error($factory->getError());
		} else {
			$this->success('审核失败操作成功');
		}
	}
	/*任务记录*/
	public function record($task_id = 0){
		$factory = new \Task\Factory\task($task_id);
		$record = $factory->get_record();
		if($record) {
			foreach ($record as $k => $v) {
				$v['start_time'] = dgmdate($v['start_time'], 'Y/m/d H:i');
				$v['username'] = model('member')->where(array('userid' => $v['userid']))->getField('nickname');
				$v['phone'] = model('member')->where(array('userid' => $v['userid']))->getField('phone');
				$record[$k] = $v;
			}
			$result['status'] = 1;
			$result['data'] = $record;
		}else{
			$result = array('status' => 0);
		}
		echo json_encode($result);
	}
	/*删除商品*/
	public function delete($ids = array()){
		if(submitcheck('dosubmit', 'GP')) {
			$ids = (array) $ids;
			if(empty($ids)) $this->error('参数错误');
			foreach ($ids as $k=>$v) {
				$v = (int) $v;
				$this->db->where(array('id'=>$v))->delete();
			}
			$this->success('删除成功');
		}else{
			$this->error('请勿非法访问');
		}
	}
}