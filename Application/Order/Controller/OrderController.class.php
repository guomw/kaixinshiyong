<?php
namespace Order\Controller;
use \Admin\Controller\InitController;
/**
 *	后台订单列表
 */
class OrderController extends InitController{
	public function _initialize(){
		parent::_initialize();
		$this->db = model("order");
		$this->member = model('member');
		$this->pagecurr = max(1, I('page', 0, 'intval'));
		$this->pagesize = 10;
		$this->status = array('已关闭','已抢购', '已确认','待审核', '审核失败', '审核通过', '申诉中', '已完成');	
	}




	
	/**
	 * 订单列表
	 */
	public function init(){
	    $info = I('param.');
	    $state = $this->status;
	    $sqlMap = array();
	    /* 类型 */
	    if ($info['act_mod'] != -99) {
	    	$sqlMap['act_mod'] = $info['act_mod'];
	    }

	    if (isset($_GET['goods_id'])) {
	    	$sqlMap['goods_id'] = $_GET['goods_id'];
	    }
	    if ($info['search']) {
			$info['start_time'] = (!empty($info['start_time'])) ? strtotime($info['start_time']) : 0;
			$info['end_time'] = (!empty($info['end_time'])) ? strtotime($info['end_time']) : 0;
			/* 下单时间 */
			if ($info['start_time'] && $info['end_time']){
				$sqlMap['create_time'] = array("BETWEEN",array($info['start_time'],$info['end_time']));
			}else{
				if ($info['start_time'] > 0) {
				$sqlMap['create_time'] = array("EGT", $info['start_time']);
				}
				if ($info['end_time'] > 0) {
					$sqlMap['create_time'] = array("ELT", $info['end_time']);
				}
			}
			/* 当前状态 */
			$info['status'] =  $info['status'];
			if ($info['status'] != -99) {
				if ($info['status'] == 8) {	//	(活动设置开启后)待平台审核的订单
					$sqlMap['status'] = 3;
					$string = '(create_time + '.(C('seller_check_time')*86400).') < '.NOW_TIME ;
					$sqlMap['_string'] = $string;
				}else{
					$sqlMap['status'] = $info['status'];
				}
			}
			$info['type'] = (int) $info['type'];
			$info['keyword'] = trim($info['keyword']);
			if ($info['keyword']) {
				switch ($info['type']) {
					case '5': //订单ID
						$sqlMap['id'] = $info['keyword'];
						continue;
					case '1': //订单号
						$sqlMap['order_sn'] = array("LIKE", "%".$info['keyword']."%");
						continue;
					case '2': //会员昵称
						$uids = $this->member->where(array('nickname'=>array("LIKE", "%".$info['keyword']."%")))->getfield('userid',true);
						$sqlMap['buyer_id'] = array("IN", $uids);
						continue;
					case '3': //商家昵称
						$uids = $this->member->where(array('nickname'=>array("LIKE", "%".$info['keyword']."%")))->getfield('userid',true);
						$sqlMap['seller_id'] = array("IN", $uids);
						continue;
					case '4': //商品标题
						$gids = model('product')->where(array('title'=>array("LIKE", "%".$info['keyword']."%")))->getfield('id',true);
						$sqlMap['goods_id'] = array("IN", $gids);
						continue;
					case '6': //商品id
					
						$sqlMap['goods_id'] = array("eq", $info['keyword']);
						continue;

					case '7': //商家id
						$sqlMap['seller_id'] = array("eq", $info['keyword']);
						continue;
				}
			}
		}

		$count = $this->db->where($sqlMap)->count();	
		$orders= $this->db->where($sqlMap)->page($this->pagecurr,$this->pagesize)->order('id DESC')->select();
		
		$pages = page($count, $this->pagesize);
		// 查出相关信息
		foreach ($orders as $k => $v) {
			// 买家信息
			// $orders[$k]['ip'] = $this->member->find($v['buyer_id']);
			
			$orders[$k]['buyer'] = model('member')->where(array('userid'=>$v['buyer_id']))->find();
			  
			
			// 商家信息
			$orders[$k]['seller'] = $this->member->find($v['seller_id']);
			// 商家店铺名称
			$orders[$k]['store_name'] = store_name($v['seller_id']);
			//用户的真实姓名
			$get_personal = get_personal($v['buyer_id'],'identity');
			$orders[$k]['realname'] = $get_personal['name'];
			
			
            if (!empty($v['bind_id'])) {
            	 //绑定的亚马逊账号
               $orders[$k]['taobao'] = model('member_bind')->where(array('id'=>$v['bind_id']))->getField('account');
            }else{
            	//用户的亚马逊账号
				$taobao = get_personal($v['buyer_id'],'taobao');
				$orders[$k]['taobao'] = $taobao['account'];
			}
			// 商品信息
			$factory = new \Product\Factory\product($v['goods_id']);
			$orders[$k]['product'] = $factory->product_info;
		}
		
		$form =  new \Common\Library\form();
		include $this->admin_tpl('order_list'); 
	}
	/* 查看订单详情 */
	public function read(){
		$id = I('id');
		if ((int)$id < 1 )	$this->error('该订单不存在');
		$state = $this->status;
		$factory = new \Product\Factory\order($id);
		$order = $factory->order_info;
		$product = $factory->product_info;
		// 会员信息
		$buyer = $this->member->find($order['buyer_id']);
		// 商家信息
		$seller = $this->member->find($order['seller_id']);
		$show_header = false;
		include $this->admin_tpl('order_read');
	}
	/* 操作订单 */
	public function operate(){
		$id = (array)I('id');
		$ids = array_filter($id);
		foreach ($ids as $id) {
			$factory = new \Product\Factory\order($id);
			$status = $factory->order_info['status'];
			if ($status > 0 && $status < 7) { //	只能关闭当前状态不为0的订单
				$factory->set_status(0,'后台管理员关闭订单');
			}
		}
		$this->success('关闭成功',U('init'));
	}
	/* 删除订单 */
	public function delete(){
		$ids = (array) I('id');
        if(empty($ids)) {
            $this->error('请指定要删除的订单ID');
        }
		foreach ($ids as $id) {
			$factory = new \Product\Factory\order($id);
            $factory->delete();
		}
		$this->success('订单删除成功',U('init'));
	}

	/* 订单日志 */
	public function log($order_id = 0){
		if ((int)$order_id < 1)	$this->error('订单号有误');
		$factory = new \Product\Factory\order($order_id);
		$logs = $factory->get_log();
		if($logs) {
            foreach ($logs as $k => $v) {
                $v['inputtime'] = dgmdate($v['inputtime'], 'Y/m/d H:i');
                if($v['issystem'] == 1) {
                    $v['nickname'] = 'admin';
                } else {
                    $v['nickname'] = model('member')->where(array('userid' => $v['userid']))->getField('nickname');
                }
                $logs[$k] = $v;
            }
            $result['status'] = 1;
            $result['data'] = $logs;
        }
        echo json_encode($result);
	}


     /*恢复免费试用订单 状态为待填写试用报告状态 用于用户未填写试用报告，被系统自动关闭*/
    public function order_state8($order_id = 0){
	   	if ((int) $order_id < 1) $this->error('订单号有误');
        
        /*获取订单信息*/
	    $ret =  model('order')->where(array('id' => $order_id))->find();

	    if(!$ret) $this->error('此订单不存在');

	    if ($ret['status'] != 0) {
	    	$this->error('当前订单不是已关闭状态,不能恢复');
	    }

	    /*获取参与的活动商品 已获得资格人数 和总活动数量*/ 
	    $goods_number = (int) model('product_trial')->where(array('id' =>$ret['goods_id']))->getField('goods_number');
	    $goods_status = model('product')->where(array('id' =>$ret['goods_id']))->getField('status');

	    if($goods_status == 1 || $goods_status == 2 ){
    	   $already_num = (int) model('product')->where(array('id' =>$ret['goods_id']))->getField('already_num');
    	   if($already_num >=$goods_number ){
    	   	$this->error('当前活动商品没有库存了，没有办法为此订单恢复');
    	   }

    	   $ret1 = model('product')->where(array('id' =>$ret['goods_id']))->setInc('already_num');
    	   if(!$ret1){
    	   	 $this->error('减少商家库存失败，无法继续恢复 请人工手动');
    	   };
    	   

    	   $data['status'] = 8;
    	   $data['check_time'] = NOW_TIME;
    	   if(!model('order')->where(array('id' =>$order_id))->setField($data)){
    	   	$this->error('更新订单状态失败，无法继续恢复 请人工手动');
           }

	         /*为订单写入恢复日志*/
		     $sqlmap['order_id'] = $order_id;
		     $sqlmap['buyer_id'] = $ret['buyer_id'];
		     $sqlmap['seller_id'] = $ret['seller_id'];
		     $sqlmap['cause'] = '后台管理员人工恢复订单！如有疑问请联系在线客服！';
		     $sqlmap['inputtime'] = NOW_TIME;
		     if(model('order_log')->add($sqlmap)){
		     	$this->success('当前订单已成功恢复！');
		     }

	    }else{
	    	$this->error('商品的活动状态, 不能恢复了！');

	    }

   }

	/*后台订单代审核通过*/
	public function pass($oid = 0){
		$oid = (int)$oid;
		if($oid < 1) $this->error('该订单不存在');
		$factory = new \Product\Factory\order($oid);
		$result = $factory->pass();
		if(!$result) $this->error($factory->getError());
		runhook('order_check_trade_no',array('order_id' => $oid, 'userid' => $factory->order_info['buyer_id'],'result' => 1,'title' => $factory->product_info['title'],'mod' => $factory->product_info['mod']));
		$this->success('订单审核成功',U('order/order/init',array('act_mod'=>$factory->product_info['mod'],'status'=>$factory->order_info['status'])));
	}
	
	/*拒绝通过*/
	public function refuse($oid = 0){
		$oid = (int)$oid;
		if($oid < 1) $this->error('该订单不存在');
		$info = $_POST;
		$info['content'] = trim($info['content']);
		if ($info['content'] == '') $this->error('请填写拒绝通过原因');
		$factory = new \Product\Factory\order($info['oid']);
		$info['content'] = '审核未通过：'.$info['content'];
		$result = $factory->refuse($info['content']);
		if(!$result) $this->error($factory->getError());
		runhook('order_check_trade_no',array('order_id' => $oid, 'userid' => $factory->order_info['buyer_id'],'result' => 0,'title' => $factory->product_info['title'],'mod' => $factory->product_info['mod'],'cause' => $info['content']));
		$this->success('订单撤销审核成功');
	}

	public  function refuse_list(){

		$info = I('param.');
	    $state = $this->status;
	    $sqlMap = array();
	    /* 类型 */
	    if ($info['act_mod'] != -99) {
	    	$sqlMap['act_mod'] = $info['act_mod'];
	    }

	    if (isset($_GET['goods_id'])) {
	    	$sqlMap['goods_id'] = $_GET['goods_id'];
	    }
	    if ($info['search']) {
			$info['start_time'] = (!empty($info['start_time'])) ? strtotime($info['start_time']) : 0;
			$info['end_time'] = (!empty($info['end_time'])) ? strtotime($info['end_time']) : 0;
			/* 下单时间 */
			if ($info['start_time'] && $info['end_time']){
				$sqlMap['create_time'] = array("BETWEEN",array($info['start_time'],$info['end_time']));
			}else{
				if ($info['start_time'] > 0) {
				$sqlMap['create_time'] = array("EGT", $info['start_time']);
				}
				if ($info['end_time'] > 0) {
					$sqlMap['create_time'] = array("ELT", $info['end_time']);
				}
			}
			/* 当前状态 */
			/*$info['status'] =  $info['status'];
			if ($info['status'] != -99) {
				if ($info['status'] == 8) {	//	(活动设置开启后)待平台审核的订单
					$sqlMap['status'] = 3;
					$string = '(create_time + '.(C('seller_check_time')*86400).') < '.NOW_TIME ;
					$sqlMap['_string'] = $string;
				}else{
					$sqlMap['status'] = $info['status'];
				}
			}*/




			$info['type'] = (int) $info['type'];
			$info['keyword'] = trim($info['keyword']);
			if ($info['keyword']) {
				switch ($info['type']) {
					case '5': //订单ID
						$sqlMap['id'] = $info['keyword'];
						continue;
					case '1': //订单号
						$sqlMap['order_sn'] = array("LIKE", "%".$info['keyword']."%");
						continue;
					case '2': //会员昵称
						$uids = $this->member->where(array('nickname'=>array("LIKE", "%".$info['keyword']."%")))->getfield('userid',true);
						$sqlMap['buyer_id'] = array("IN", $uids);
						continue;
					case '3': //商家昵称
						$uids = $this->member->where(array('nickname'=>array("LIKE", "%".$info['keyword']."%")))->getfield('userid',true);
						$sqlMap['seller_id'] = array("IN", $uids);
						continue;
					case '4': //商品标题
						$gids = model('product')->where(array('title'=>array("LIKE", "%".$info['keyword']."%")))->getfield('id',true);
						$sqlMap['goods_id'] = array("IN", $gids);
						continue;
				}
			}
		}

		$sqlMap['status'] = 4;

		$count = $this->db->where($sqlMap)->count();	
		$orders= $this->db->where($sqlMap)->page($this->pagecurr,$this->pagesize)->order('id DESC')->select();
		
		$pages = page($count, $this->pagesize);
		// 查出相关信息
		foreach ($orders as $k => $v) {
			// 买家信息
			// $orders[$k]['ip'] = $this->member->find($v['buyer_id']);
			
			$orders[$k]['buyer'] = model('member')->where(array('userid'=>$v['buyer_id']))->find();
			$orders[$k]['close_cause'] = model('order_log')->where(array('order_id'=>$v['id'],'seller_id'=>$v['seller_id']))->order('id desc')->getField('cause');
			  
			
			// 商家信息
			$orders[$k]['seller'] = $this->member->find($v['seller_id']);
			// 商家店铺名称
			$orders[$k]['store_name'] = store_name($v['seller_id']);
			//用户的真实姓名
			$get_personal = get_personal($v['buyer_id'],'identity');
			$orders[$k]['realname'] = $get_personal['name'];
			
			
            if (!empty($v['bind_id'])) {
            	 //绑定的亚马逊账号
               $orders[$k]['taobao'] = model('member_bind')->where(array('id'=>$v['bind_id']))->getField('account');
            }else{
            	//用户的亚马逊账号
				$taobao = get_personal($v['buyer_id'],'taobao');
				$orders[$k]['taobao'] = $taobao['account'];
			}
			// 商品信息
			$factory = new \Product\Factory\product($v['goods_id']);
			$orders[$k]['product'] = $factory->product_info;
		}
		
		$form =  new \Common\Library\form();
		include $this->admin_tpl('refuse_list');

	}
	/*付款*/
	public function pay(){
		$oid = (int) I('oid');
		$mod = I('mod', 'rebate');
		if($oid < 1) $this->error('您没有权限进行此操作');
		$factory = new \Product\Factory\order($oid);
		$sql = "select order_id,userid,num,dateline,Count(*) from ".C('DB_PREFIX')."member_finance_log where order_id = ".$factory->order_info['id']." and num>0 group by order_id,userid,num having Count(*)>=1 order by id desc";
		$r = model()->query($sql);
		if(!$r){
		    $result = $factory->pay();
		}
		if (!$result) {
			$this->error($factory->getError());
		}
		runhook('order_balance',array('order_id' => $oid, 'userid' => $factory->order_info['buyer_id'],'title' => $factory->product_info['title'],'result' => 1));
		$this->success('订单付款成功');
	}
	/*撤销付款*/
	public function cancel($oid = 0){
		$oid = (int) $oid;
		$factory = new \Product\Factory\order($oid);
		$result = $factory->cancel();
		if(!$result) $this->error($factory->getError());
		runhook('order_balance',array('order_id' => $oid, 'userid' => $factory->order_info['buyer_id'],'title' => $factory->product_info['title'],'result' => 0));
		$this->success('订单撤销审核成功');
	}
}
?>