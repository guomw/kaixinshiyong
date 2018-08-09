<?php
namespace Pay\Controller;
use Admin\Controller\InitController;
class PayCheckController extends InitController{
	public function _initialize(){
		parent::_initialize();
		$this->db = model('pay_check');
	}
	
	public function init(){
		$pagecurr = max(1,I('page',0,'intval'));
		$pagesize = 20;
		$sqlMap = array();
		$param = I('param.');
		$info = $param['info'];
		$status = (isset($info['status'])) ? (int)$info['status'] : $param['status'];
		if(IS_GET){
			$start_addtime = $info['start_addtime'];
			$end_addtime = $info['end_addtime'];
			$type = $info['type'];
			$keyword = $info['keyword'];
			if(!empty($type) && !empty($keyword)){
				if($type == 1){//交易号 
					$sqlMap['tran_number'] = array("LIKE","%$keyword%");
				}else{
					//根据输入的昵称查找用户
					$rs = model('member')->where(array('nickname'=>array("LIKE","%$keyword%")))->select();
					foreach ($rs as $k=>$v) {
						$ids[] = $v['userid'];
					}
					$sqlMap['userid'] = array("in",$ids);
				}
			}
		 	if($status > -2) {
                $sqlMap['status'] = $param['status'];
            }
			if(!empty($start_addtime) && !empty($end_addtime)) {
				$start = strtotime($start_addtime.' 00:00:00');
				$end = strtotime($end_addtime.' 23:59:59');
				$sqlMap[]= "AND `inputtime` >= '$start' AND  `inputtime` <= '$end'";
			}
		}
		$pay_count = $this->db->where($sqlMap)->count();
		$pay_lists = $this->db->where($sqlMap)->page($pagecurr,$pagesize)->order('inputtime DESC')->select();
		foreach ($pay_lists as $key=>$val) {
			$infos = model('member_merchant')->getByUserid($val['userid']);
			$pay_lists[$key]['store_name'] = $infos['store_name'];
			$pay_lists[$key]['contact_name'] = $infos['contact_name'];
		}
		$pages = page($pay_count,$pagesize);
		$form = new \Common\Library\form();
		include $this->admin_tpl('pay_check_lists');
	}
	/*删除*/
	public function delete($id = array()){
		if (IS_POST && I('fromhash') == session('FROMHASH')) {
			$ids = (array) $id;
			if(empty($ids)) $this->error('参数错误');
			foreach ($ids as $k=>$v) {
				$payid = (int) $v;
				$this->db->where(array('id'=>$v))->delete();
			}
			$this->success('删除成功');	
		}else{
			$this->error('请勿非法操作');
		}
	}
	
	/*审核通过*/
	public function passed($id = 0){
		$id = (int) $id;
		if(empty($id)) $this->error('参数错误');
		//判断该条信息是否审核
		$info = $this->db->where(array('id' => $id))->find();
		if($info['status'] != 0){
			$this->error('该条信息已经审核，请勿重新审核');
		}
		//写入用户的账户记录
		//根据id查出当前记录的用户以及充值金额
		$r = $this->db->where(array('id'=>$id))->find();
		$userid = $r['userid'];
		$money = $r['money'];
		$nickname = M('Member')->getFieldByUserid($userid,'nickname');
		$msg = $nickname.'普通充值,审核通过';
		$sign = '4-3-'.$userid.'-'.$money.'-'.$r['tran_number'];
		$rs = model('member_finance_log')->where(array('only'=>$sign))->find();
		if(!$rs){
			$this->db->where(array('id'=>$id))->save(array('status'=>1,'check_time'=>NOW_TIME));
		    action_finance_log($userid,$money,'money',$msg,$sign,array('order_id'=>$r['tran_number']));
		    runhook('pay_recharge_check',array('userid' => $userid,'result' => '1','money' => $info['money']));
		    $this->success('审核成功');
		}else{
		    $this->error('审核失败，重复操作');
		}
	}

	/*审核失败*/
	public function unpassed($id = 0){
		$id = (int) $id;
		if($id < 1) $this->error('参数错误');
		//判断该信息是否审核
		$info =  $this->db->where(array('id' => $id))->find();
		if($info['status'] != 0) $this->error('该条信息已经审核，请勿重复审核','javascript:close_dialog();');
		if (IS_POST) {
			$cause = trim($_POST['cause']);
			if (!$cause)	$this->error('请填写审核失败原因');
			$result = $this->db->where(array('id'=>$id))->save(array('status'=>-1,'check_time'=>NOW_TIME,'cause'=>$cause));
			runhook('pay_recharge_check',array('userid' => $info['userid'],'result' => '0','money' => $info['money'],'cause' => $cause));
			if(!$result){
				$this->error('操作失败');
			}
			$this->success('操作成功','javascript:close_dialog();');
		}else{
			include $this->admin_tpl('pay_check_unpass');
		}
	}
	
	/*审核成功判断状态*/
	public function check_pass(){
		$id = (int) I('id');
		//判断当前状态
		$count = $this->db->where(array('id'=>$id))->find();
		if($count['status'] == 1) {
			$this->error("该条信息已经审核过，请勿重新审核");
			return false;
		}elseif($count['status'] == -1){
			$this->error("该条信息当前状态为审核未通过");
			return false;
		}else{
			$this->success('ok',U('passed',array('id'=>$id)));
		}
	}	
	
	/*商家详情*/
	public function detail(){
		$userid = (int) I('userid');
		if($userid < 1){
			$this->error("参数错误");
		}
		$info = model('member_merchant')->getByUserid($userid);
		include $this->admin_tpl('pay_detail_check');
	}
	/*充值详情*/
	public function payinfo($id = 0){
		$id = (int) $id;
		if($id < 1) $this->error('参数错误');
		$info = $this->db->getById($id);
		extract($info);
		include $this->admin_tpl('pay_detail_info');
 	}
}