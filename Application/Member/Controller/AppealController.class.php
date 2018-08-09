<?php 
namespace Member\Controller;
use Member\Controller\InitController;
/* 会员中心 申诉控制器 */
class AppealController extends InitController{
	public function _initialize() {		
		parent::_initialize();
		$this->models = getcache('model', 'commons');
		$this->model = $this->models[4];
		$this->db = model('appeal');
		$this->order = model('order');
		$this->log_db = model('order_log');
		$this->member = model('member');
		$this->product = model("product");
		$this->msgdb = model("message");
		$this->pagesize = 10;
		$this->pagecurr = max(1,I('page',1,'intval'));
	}
	/* 获取申诉状态 状态(0:待商家补充凭证，1:待平台仲裁,2:平台仲裁完毕,3:已关闭申诉） */
	public static function getAppealstatus(){
		return array('999'=>'全部(状态)','0'=>'待商家补充凭证','1'=>'待平台仲裁','2'=>'平台仲裁完毕','3'=>'已关闭申诉');
	}
	/* 获取申诉类型 */
	public static function getAppealtype($mod = 'rebate'){
		if ($mod == 'rebate' || $mod == 'commission') {
			$arr = array('1'=>'修改订单号','2'=>'单号被审核有误','4'=>'其它');
		}elseif ($mod == 'trial'){
			$arr = array('1'=>'修改订单号','2'=>'单号被审核有误','3'=>'取消试用资格','4'=>'其它');
		}
		return $arr;
	}
	/* 我要申诉 [Adalways试用系统] */
	public function add() {
		$SEO = seo(0, '订单申诉 - 买家中心');
		$oid = (int) I('get.id');
		if ($oid < 1) $this->error('参数错误');
		$order_map = array();
		$order_map['id'] = $oid;
		$order_map['user_id'] = $this->userid;
		//订单是否存在
		$order_info = $this->order->where($order_map)->find();
		if ($order_info['buyer_id'] != $this->userid)	$this->error('该订单不存在');
		if ($order_info['status'] != '4') $this->error('该订单无需申诉');
		// 商品信息
		$order_info['pro'] = D('Product/Product')->detail($order_info['goods_id']);
		// 店铺信息
		$order_info['store_name'] = store_name($order_info['seller_id']);
		$form = new \Common\Library\form();
		$appeal_type = self::getAppealtype($order_info['pro']['mod']);
		include template('buyer/appeal');

	}
	/* 会员提交申诉详情 [Adalways试用系统]*/
	function add_post(){
		$data=I('post.');
		if ($data['appeal_type'] <  1   )	$this->error('请选择申诉类型');
		if ($data['buyer_cause'] == null)	$this->error('申诉理由不能为空');
		if ($data['buyer_phone'] == null)	$this->error('手机号码不能为空');
		if ($data['buyer_qq']    == null)	$this->error('QQ号码不能为空');
		if (!$data['buyer_imgs_url'])	$this->error('请上传申诉的图片');
		$data["order_sn"]      = $data["order_sn"];
		$data["appeal_status"] = '0';
		$data["buyer_id"]      = $this->userid;
		$data["buyer_time"]    = NOW_TIME;
		if ($data['buyer_imgs_url']) $data['buyer_imgs_url'] = array2string($data['buyer_imgs_url']);
		$result = $this->db->add($data);
		if (!$result)  $this->error("申诉失败");
		// 写入操作日志、并设置该订单状态为申述中(6)
		$factory = new \Product\Factory\order($data['order_id']);
		if ($factory->order_info['buyer_id'] != $this->userid)	$this->error('请登录会员帐号');
		$result = $factory->set_status(6,'会员(本人)申诉已成功');
		if (!$result)	$this->error('申诉提交失败，请稍后再试！');
		runhook('order_appeal',array('userid' => $factory->product_info['company_id'],'title' => $factory->product_info['title']));
		if ($data['v2'] == 1) {
			$url = U('Member/Appeal/appeal_manage');
		}else{
			$url = U("Order/manage",array('state'=>'6','mod'=>$factory->product_info['mod']));
		}
		$this->success('申诉成功',$url,1);
	}
	/* 会员关闭申诉 */
	function close(){
		$info = I('post.');
		if ((int)$info['aid'] < 1 )	$this->error('该申诉不存在');
		if ((int)$info['userid'] < 1 )	$this->error('请登录会员账号');
		if ((int)$info['oid'] < 1 )	$this->error('该订单不存在');
		$result = $this->db->where(array('id'=>$info['aid'],'buyer_id'=>$info['userid']))->setfield('appeal_status',3);
		if (!$result)	$this->error('关闭申诉失败，请稍后再试!');
		// 写入操作日志、并设置该订单状态为失败(4)
		$factory = new \Product\Factory\order($info['oid']);
		if ($factory->order_info['buyer_id'] != $this->userid)	$this->error('请登录会员帐号');
		$result = $factory->set_status(4,'会员(本人)关闭该订单申诉');
		if (!$result) {
			$this->db->where(array('id'=>$info['aid'],'buyer_id'=>$info['userid']))->setfield('appeal_status',0);
			$this->error('关闭申诉失败，请稍后再试!');
		}
		$this->success('关闭申诉成功', U('order/manage',array('state'=>6)));

	}
	/* 商家提交申诉 */
	function appeal_seller(){
		$appeal_id = I('param.appeal_id');
		// 查出该申诉记录信息
		$appeal = $this->db->where(array("id" => $appeal_id))->find();
		if ($appeal['seller_id'] != $this->userid)	$this->error('当前记录不存在');
		if ($appeal['appeal_status'] > 1) $this->error('该申诉已完成，禁止一切操作',U('member/appeal/appeal_manage'));
		if (submitcheck('dosubmit','p')){
			if ($this->userinfo['modelid'] != 2 )	$this->error('请登录商家账号');
			$info = I('post.');
			if (!$info['seller_cause'])	$this->error('请填写处理理由！');
			if (!$info['seller_imgs_url'])	$this->error('请上传证据图片！');
			$data = array();
			$data['appeal_status'] = 1;
			$data['seller_cause'] = $info['seller_cause'];
			$data['seller_imgs_url'] = array2string($info['seller_imgs_url']);
			$data['seller_time'] = NOW_TIME;
			$result = $this->db->where(array('id' => $appeal_id))->save($data);
			if (!$result)	$this->error('提交申诉失败，请重新再试！');
			$this->success('操作成功，待管理员审核',U('appeal_manage'));
		}else{
			if ($this->userinfo['modelid'] != 2 )	$this->error('请登录商家账号');
			$appeal['buyer_imgs_url'] = string2array($appeal['buyer_imgs_url']);
			$appeal['seller_imgs_url'] = string2array($appeal['seller_imgs_url']);
			$appealtypes  = self::getAppealtype();	//	申诉类型
			$appealstatus = self::getAppealstatus();	//	申诉状态	
			// 买家信息 
			$userInfo1 = $this->member->where(array("userid" => $appeal['buyer_id']))->find();
			$userInfo2 = model('member_detail')->where(array("userid" => $userInfo1['userid']))->find();
			$userInfo = array_merge($userInfo1,$userInfo2);
			// 商家信息
			$sellerInfo = $this->member->where(array("userid" => $appeal['seller_id']))->find();
			// 商品信息
			$proInfo = D('Product/Product')->detail($appeal['goods_id']);
			include template('merchant/appeal_seller');
		}
	}
	/* 商家 买家申诉管理 */
	public function appeal_manage() {
		$SEO = seo(0, '订单申诉 - 个人中心');
		if($this->userid  < 1) $this->error('您还没有登录,请先登录',U('Member/Index/login'));
		$modelid = $this->userinfo['modelid'];
		$sqlMap = array();
		$state = I('get.state');
		if ($state > -1) $sqlMap['appeal_status'] = $state;
		if ($modelid == 2) {
			$sqlMap['seller_id'] = $this->userid;
		}else{
			$sqlMap['buyer_id'] = $this->userid;
		}
		//申诉记录
		$aCounts = $this->db->where($sqlMap)->count();
		$appeals = $this->db->where($sqlMap)->page($this->pagecurr, $this->pagesize)->order("buyer_time DESC")->select();
		foreach($appeals as $key=> $appeal){
			//商品信息
			$appeal['goods'] = getGoodsInfo($appeal['goods_id']);
			//买家人信息
			$appeal['username'] = $this->member->where(array("userid"=>$appeal['buyer_id']))->getfield("nickname");
			$appeals[$key] = $appeal;
			// 订单信息(用于传入js)
			$orders[$key] = model('order')->find($appeal['order_id']);
		}
		$appealstatus = self::getAppealstatus();
		$appealtypes = self::getAppealtype();
		$pages = showPage($aCounts, $this->pagecurr,$this->pagesize);
		$v2_pages = v2_page_3($aCounts,$this->pagesize);

		$tpl = ($modelid == 1) ? 'buyer/appeal_manage' : 'merchant/appeal_manage';
        include template($tpl);
	}
	
	/* 查看申诉 */
	public function read(){
		if (IS_POST){
			$appeal_id = I('post.appeal_id');
			// 查出该申诉记录信息
			$appeal = $this->db->where(array("id" => $appeal_id))->find();
			$appeal['buyer_imgs_url'] = string2array($appeal['buyer_imgs_url']);
			$appeal['seller_imgs_url'] = string2array($appeal['seller_imgs_url']);
			$appealtypes  = self::getAppealtype();	//	申诉类型
			$appealstatus = self::getAppealstatus();	//	申诉状态	
			// 买家信息 
			$userInfo1 = $this->member->where(array("userid" => $appeal['buyer_id']))->find();
			$userInfo2 = model('member_detail')->where(array("userid" => $userInfo1['userid']))->find();
			$userInfo = array_merge($userInfo1,$userInfo2);
			// 商家信息
			$sellerInfo = $this->member->where(array("userid" => $appeal['seller_id']))->find();
			// 商品信息
			$proInfo = D('Product/Product')->detail($appeal['goods_id']);
			include template('buyer/alert_appeal');
		}else{
			$this->error('请勿非法访问！');
		}
	}
	/* 撤销申诉 [Adalways试用系统] */
	public function appeal_cancel(){
		$order_id = I("get.order_id");
		$appeal_id = I("get.appeal_id");
		// 更改申诉记录状态为 已撤销(3)
		$result = $this->db->where(array("id" => $appeal_id))->setField("appeal_status","3");
		// 更改订单表记录状态为 审核失败(4)
		$result2 = $this->order->where(array("id" => $order_id))->setField("status","4");
		if (!$result || !$result2) $this->error('撤销失败');
		$this->success('撤销成功');
	}
}
?>
