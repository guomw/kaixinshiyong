<?php
namespace Order\Controller;
use \Admin\Controller\InitController;
/**
 *	订单申诉列表
 */
class AppealController extends InitController{
	public function _initialize(){
		parent::_initialize();
		$this->db = model('appeal');
		$this->order = model('order');
		$this->pagecurr = max(1, I('page', 0, 'intval'));
		$this->pagesize = 10;
	
	}
	/* 获取申诉状态 状态(0:待商家补充凭证，1:待平台仲裁,2:平台仲裁完毕,3:已关闭申诉） */
	public static function getAppealstatus(){
		return array('999'=>'全部(状态)','0'=>'待商家补充凭证','1'=>'待平台仲裁','2'=>'平台仲裁完毕','3'=>'已关闭申诉');
	}
	/* 获取申诉类型 */
	public static function getAppealtype($mod = 'rebate'){
		if ($mod == 'rebate') {
			$arr = array('1'=>'修改订单号','2'=>'单号被审核有误','4'=>'其它');
		}elseif ($mod == 'trial'){
			$arr = array('1'=>'修改订单号','2'=>'单号被审核有误','3'=>'取消试用资格','4'=>'其它');
		}
		return $arr;
	}
	/* 申诉列表 */
	public function init(){
		$form =  new \Common\Library\form();
	 	$show_header = false;
	 	$sqlMap = array();
		$info = I('get.');
		$info['start_time'] = (!empty($info['start_time'])) ? strtotime($info['start_time']) : 0;
		$info['end_time'] = (!empty($info['end_time'])) ? strtotime($info['end_time']) : 0;
		/* 注册时间 */
		if ($info['start_time'] && $info['end_time']){
			$sqlMap['buyer_time'] = array("BETWEEN",array($info['start_time'],$info['end_time']));
		}else{
			if ($info['start_time'] > 0) {
			$sqlMap['buyer_time'] = array("EGT", $info['start_time']);
			}
			if ($info['end_time'] > 0) {
				$sqlMap['buyer_time'] = array("ELT", $info['end_time']);
			}
		}
		if($info['appeal_status'] != 999){
			$sqlMap['appeal_status'] = array('EQ',$info['appeal_status']);
		}

		if($info['appeal_type']>0){
			$sqlMap['appeal_type'] = array('EQ',$info['appeal_type']);
		}

		$type = $info['type'];
		if($type){
			switch ($type) {
				case '1':
					$sqlMap['order_sn'] = array("LIKE", "%".$info['keyword']."%");
					break;

				case '2':
					$uids = model('member')->where(array('nickname'=>array("LIKE", "%".$info['keyword']."%")))->getfield('userid',true);
					$sqlMap['buyer_id'] = array("IN", $uids);
					break;

				case '3':
					$gids = model('product')->where(array('title'=>array("LIKE", "%".$info['keyword']."%")))->getfield('id',true);
					$sqlMap['goods_id'] = array("IN", $gids);
					break;
			}
		}
		$aCounts = $this->db->where($sqlMap)->count();
		$appeals = $this->db->where($sqlMap)->page($this->pagecurr, $this->pagesize)->order('id DESC')->select();
		$pages = page($aCounts, $this->pagesize);
		// 相关信息
		foreach ($appeals as $k => $v) {
			$v['buyer']  = D('Member')->find($v['buyer_id']);
			$v['seller'] = D('Member')->find($v['seller_id']);
			$v['goods']  = D('Product')->find($v['goods_id']);
			$appeals[$k] = $v ;
		}
		$appealtype = self::getAppealtype();
		$appealstatus = self::getAppealstatus();
		include $this->admin_tpl('appeal_list');
	}

	/* 处理申诉 */
	public function appeal_do(){
		if (submitcheck('dosubmit','P')){
			$info = I('post.');
			if ((int)$info['order_id'] < 1) $this->error('该订单不存在');
			if ($info['order_status'] =='-99') $this->error('请选择审核结果');
			if (!$info['admin_cause']) $this->error('审核描述不能为空');
			$factory = new \Product\Factory\order($info['order_id']);
			if (!$factory) $this->error($factory->getError());
			$type = array('2'=>'重新填写订单号','4'=>'重新发起申述','7'=>'订单完成并返款','0'=>'直接关闭订单');
			if ($info['order_status'] == 7 ){	//返还金直接返还到会员账户，订单交易完成
				$result = $factory->pay();
			}else{
				$result = $factory->set_status($info['order_status'],'后台管理员：'.$info['admin_cause']);
			}
			if (!$result) $this->error($factory->getError());
			runhook('order_appeal_arbitration',array('title' => $factory->product_info['title'],'type'=>$type[$info['order_status']],'userid' => $factory->order_info['buyer_id'],'seller_id' => $factory->product_info['company_id']));

			$info['appeal_status'] = 2;
			$info['admin_time'] = NOW_TIME;
			$result = $this->db->update($info);
			if (!$result) $this->error('该申诉处理失败');
			$this->success('申诉处理操作成功','javascript:close_dialog();', 2);
		}else{
			$appeal_id = I('get.appeal_id');
			$appeal = $this->db->find($appeal_id);
			$appeal['buyer_imgs_url'] = string2array($appeal['buyer_imgs_url']);
			$appeal['seller_imgs_url'] = string2array($appeal['seller_imgs_url']);
			// 会员信息
			$appeal['buyer']  = model('member')->find($appeal['buyer_id']);
			// 商家信息
			$appeal['seller'] = model('member')->find($appeal['seller_id']);
			$appeal['store']  = model('member_merchant')->find($appeal['seller_id']);
			// 商品信息
			$appeal['goods']  = D('Product/Product')->detail($appeal['goods_id']);
			// 订单信息
			$appeal['order']  = $this->order->find($appeal['order_id']);
			$appealtype = self::getAppealtype();
			$appealstatus = self::getAppealstatus();
			include $this->admin_tpl('appeal_edit'); 
		}
	}

	/* 删除申诉 [Adalways试用系统] */
	public function delete(){
		$ids = I('param.ids');
		foreach ($ids as $id) {
			$this->db->delete($id);
		}
		$this->success('删除成功');
	}
}
?>