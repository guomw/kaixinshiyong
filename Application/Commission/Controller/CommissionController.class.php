<?php
namespace Commission\Controller;
use \Admin\Controller\InitController;
class CommissionController extends InitController{
	public function _initialize(){
		parent::_initialize();
		$this->status = C('COMMISSION_STATUS');
		$this->activity_lists = C('ACTIVITY_LISTS');
		$this->source = model('shop_set')->getField('id, name', TRUE);

		$this->db = model('commission');
	}
	/**
	 * 管理商品
	 */
	public function manage(){
		$form = new \Common\Library\form();
        $sqlmap = array();
        $param = I('param.');
        if (IS_GET) {
        	$search = $param['search'];
       		$status = (isset($search['status'])) ?$sqlmap['status'] =(int)$search['status'] : -99;
	    	 if(isset($search['start_time']) && !empty($search['start_time'])) {
	            $start_time = strtotime($search['start_time']. '00:00:00');
	            $sqlmap['start_time'] = array("GT", $start_time);
	        }
	        
	        if(isset($search['end_time']) && !empty($search['end_time'])) { 
	            $end_time = strtotime($search['end_time'].' 23:59:59');
	            $sqlmap['end_time'] = array("LT", $end_time);
	        }
	        
	        if(isset($search['keyword']) && !empty($search['keyword'])) {
	            if($search['type'] == 'product') {
	                $sqlmap['title|keyword'] = array("LIKE", "%".$search['keyword']."%");
	            } elseif($search['type'] == 'company') {
	                $company_map = array();
	                $company_map['store_name'] = array("LIKE", "%".$search['keyword']."%");
	                $company_ids = model('member_merchant')->where($company_map)->getField('userid', 'TRUE');
	                $company_ids = (empty($company_ids)) ? array('-1') : $company_ids;
	                $sqlmap['company_id'] = array("IN", $company_ids);
	            }
	        }
        }
       
        $count = $this->db->where($sqlmap)->count();
        $lists = $this->db->where($sqlmap)->page(PAGE, 10)->order('updatetime DESC,id DESC')->select();
        foreach ($lists as $k=>$v){
        	$lists[$k]['store_name'] = model('member_merchant')->where(array('userid' => $v['company_id']))->getField('store_name');
        }
        $pages = page($count, 10);
        $show_header = FALSE;
        $show_dialog = FALSE;
		include $this->admin_tpl('manage_list');
	}
	
	/**
	 * 发布商品
	 */
	public function add(){
		if(submitcheck('dosubmit')) {
			$info = $_POST['info'];
			$info['mod'] = 'commission';
			$info['status'] = -3;
			$info['service'] = $this->service($info['company_id'],$info['goods_price']);
			$info['c_type'] = implode(',',$info['c_type']);
			$info['goods_albums'] =array2string($_POST['goods_search_albums_url']);
			$info['inputtime'] = NOW_TIME;
			$info['goods_service'] = ($info['goods_price'] + $info['bonus_price']+$this->service($info['company_id'],$info['goods_price']))*$info['goods_number'];
			$result = model('commission')->add($info);
			if(!$result) {
				$this->error('产品发布失败，请重新添加');
			} else {
				//加入产品日志
				commission_log($result,-3,1,$this->userid,'加入产品');
				$this->success('产品发布成功', U('manage'));
				
			}
			
		} else {
			$show_header = $show_dialog = $show_validator = 1;
			$form = new \Common\Library\form();
			include $this->admin_tpl('product_add');
		}
	}
	
	
/**
	 * 佣金验证
	 */
	public function reward(){
		//$company_id = $this->userid;
		$goods_price = I('goods_price');//下单价
		//$goods_price = 12;
		//判断该商家是那种类型的类型 
		$setting = model('activity_set')->where(array('activity_type' =>'commission'))->getField('key,value');
		//$price_type = $config['seller_price'];
		$bonus_price = string2array($setting['commission_price']);
	    $bonus = $bonus_price['commission'];
	    $price_type = $bonus['commission_price'];
		array_multisort($price_type,'min','SORT_ASC');
		if(!empty($price_type) && is_array($price_type)){
			$price = 0;//缴纳的佣金
			foreach ($price_type as $k=>$v){
				//判断下单在那个范围，应缴纳多少佣金  $max<$price_type<$min
				if( ($goods_price >= $v['min'] && $goods_price <= $v['max'])){
					 $price = sprintf('%.2f',$v['commission']);
					 break;
					
				}
			}
		}
	
	  echo $price;
		
	}

	/*校验试客佣金是否通过*/
	public function check_bouns(){
		//$company_id = $this->userid;
		$goods_price = I('goods_price');//下单价
	    $goods_bonus = I('bonus_price');//下单价
		//判断该商家是那种类型的类型 
		$setting = model('activity_set')->where(array('activity_type' =>'commission'))->getField('key,value');
		//$price_type = $config['seller_price'];
		$bonus_price = string2array($setting['commission_price']);
	    $bonus = $bonus_price['commission'];
	    $price_type = $bonus['commission_price'];
       // $bonus_count = count($bonus['bonus_price']);
		//数组排序
		$this->multi_array_sort($price_type,'min','SORT_ASC');
		if(!empty($price_type) && is_array($price_type)){
			$price = 0;//缴纳的佣金
			foreach ($price_type as $k=>$v){
				
				//判断下单在那个范围，应缴纳多少佣金  $max<$price_type<$min
				if( ($goods_price >= $v['min'] && $goods_price <= $v['max'])){
					 $price = sprintf('%.2f',$v['commission']);
					 break;
				}
			}
		}
	
		if ((int)$goods_bonus >= (int)$price) {
			$this->success("校验通过");
		}else{
			$this->error("校验失败",$price);
		}

	}
	/**
	 * 编辑商品
	 * @param int $id 商品ID
	 */
	public function edit($id = 0){

		if (!$id) $this->error('该商品id不存在！');
		$rs = $this->db->detail($id);
		if(!$rs) $this->error('该商品不存在！');
		$c_types = explode(',', $rs['c_type']);
		$goods_albumss = string2array($rs['goods_albums']);
		if(submitcheck('dosubmit')) {
			$info = $_POST['info'];
			$info['id'] = $id;
			$info['service'] = $this->service($info['company_id'],$info['goods_price']);
			$info['c_type'] = implode(',',$info['c_type']);
			$info['goods_albums'] =array2string($_POST['goods_search_albums_url']);
			$info['inputtime'] = NOW_TIME;
			$info['goods_service'] = ($info['goods_price'] + $info['bonus_price']+$this->service($info['company_id'],$info['goods_price']))*$info['goods_number'];
			$result = model('commission')->where(array('id'=>$id))->save($info);
			if(!$result) {
				$this->error('产品编辑失败，请重试！');
			} else {
				$this->success('产品编辑成功', U('manage'));
			}
		} else {
			$show_header = $show_dialog = $show_validator = 1;
			$form = new \Common\Library\form();
			include $this->admin_tpl('product_edit');
		}
	}


	/*获取平台佣金*/
	public function service($userid,$goods_price){
		$groups = getcache('merchant_group','member');
		$groupid = model('member')->getFieldByUserid($userid,'groupid');
		$config = unserialize($groups[$groupid]['config']);
	    $cost = $config['commission'];
		if ($cost['cost'] == 0) {
			$service = '0.00';
		}else{
			
		    $price_type = $cost['service_price'];
			array_multisort($price_type,'min','SORT_ASC');
			if(!empty($price_type) && is_array($price_type)){
				$service = 0;//缴纳的佣金
				foreach ($price_type as $k=>$v){
					//判断下单在那个范围，应缴纳多少佣金  $max<$price_type<$min
					if( ($goods_price >= $v['min'] && $goods_price < $v['max'])){
						 $service = sprintf('%.2f',$v['commission']);
						 break;
						
					}
				}
			}
		}
		return $service;
		
	}
	
	
	/* 删除商品 */
	public function delete(){
		if(submitcheck('dosubmit', 'GP')) {
			$ids = (array) I('ids');
			if(empty($ids)) $this->error('参数错误');
			foreach($ids as $id) {
				$this->db->pro_delete($id);
			}
			$this->success('产品删除成功');
		} else {
			$this->error('请勿非法访问');
		}
	}
	/* 审核报名商品 */
	public function check() {
	    $status = I('status', -2);
	    $form = new \Common\Library\form();
	    $sqlmap = array();
	    $param = I('param.');
	    $search = $param['search'];
	    if($status < 6) {
	        $sqlmap['status'] = $status;
	    }
	    if(submitcheck('searchbtn', 'GP')) {
	        if(isset($search['status']) && !empty($search['status'])) {
	            $sqlmap['status'] = $search['status'];
	        }
	        
	        if(isset($search['start_time']) && !empty($search['start_time'])) {
	            $start_time = strtotime($search['start_time']. '00:00:00');
	            $sqlmap['start_time'] = array("GT", $start_time);
	        }
	        if(isset($search['end_time']) && !empty($search['end_time'])) {
	            $end_time = strtotime($search['end_time'].' 23:59:59');
	            $sqlmap['end_time'] = array("LT", $end_time);
	        }
	        if(isset($search['keyword']) && !empty($search['keyword'])) {
	            if($search['type'] == 'product') {
	                $sqlmap['title|keyword'] = array("LIKE", "%".$search['keyword']."%");
	            } elseif($search['type'] == 'company') {
	                $company_map = array();
	                $company_map['store_name'] = array("LIKE", "%".$search['keyword']."%");
	                $company_ids = model('member_merchant')->where($company_map)->getField('userid', 'TRUE');
	                $company_ids = (empty($company_ids)) ? array('-1') : $company_ids;
	                $sqlmap['company_id'] = array("IN", $company_ids);
	            }
	        }
	    }
	     
	    $count = $this->db->where($sqlmap)->count();
	    $lists = $this->db->where($sqlmap)->page(PAGE, 10)->order('id DESC')->select();
	    foreach ($lists as $k=>$r){
	       
	        $lists[$k]['store_name'] = store_name($r['company_id']);
	        
	    }
	    $pages = page($count, 10);
	    $show_header = FALSE;
	    $show_dialog = FALSE;

	    include $this->admin_tpl('product_check');
	}

	/**
	 * 确认付款
	 * @param int $product_id  商品ID
	 */
	public function pay($product_id = 0) {
		$factory = new \Commission\Factory\commission($product_id);
	    if ((int)$product_id < 1)   $this->error('该商品不存在！');
	    //将该产品的保证金及服务费存入数据库
	    $rs = $this->admin_pay($product_id);
	    if(!$rs) $this->error('操作失败，请稍后重试');
	    //设置订单状态
	     $factory->set_status(-2, '后台管理员确认付款');
	    $this->success('确认付款成功');
	}
	
	/**
	 * 审核通过
	 */
	public function pass($product_id){
	    $product_id = (int) $product_id;
	    if ($product_id < 1) $this->error('商品ID有误');
	    $rs = $this->db->find($product_id);
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
	        $result = $this->commission_pass($rs['id'],$start_time, $msg);
	        if(!$result) {
	            $message = '操作失败';
	            $this->error($message);
	        } else {
	            runhook('product_check',array('userid' =>$rs['company_id'],'start_time' => $start_time,'title' => $rs['title'],'result' => 1));
	            $this->success('操作成功', 'javascript:close_dialog();');
	        }
	    } else {
	        $form = new \Common\Library\form();
	        $show_dialog = FALSE;
	        include $this->admin_tpl('product_pass');
	    }
	}


	public function commission_pass($id,$start_time = 0, $msg = '商品审核成功') {
		if (!$id) $this->error('参数错误');
		$goods_info = $this->db->find($id);
		$start_time = (!is_numeric($start_time)) ? strtotime($start_time) : (int) $start_time;
		if($start_time < NOW_TIME) {
			$this->error = '上线时间只能在当前时间之后';
			return FALSE;
		}
		$info = array();
		$info['id'] = $id;
		$info['status'] = -1;
		$info['start_time'] = $start_time;
		$info['end_time'] = $start_time+($goods_info['activity_days']*86400);
		$info['updatetime'] = NOW_TIME;
		$result = model('commission')->save($info);
		if(!$result) {
			$this->error = '产品处理失败，请稍后重试';
			return FALSE;
		}
		commission_log($id,-1,1,$goods_info['company_id'],$msg);

		return TRUE;
	}
	
	/**
	 * 审核拒绝
	 * @param int $product_id 商品ID
	 */
	public function refuse($product_id = 0,$cause='审核拒绝并退还保证金') {
		$product_id = (int)$product_id;
		$goods_info = $this->db->find($product_id);
	    if (!$goods_info) {
	    	 $this->error("参数错误！");
	    }
	    if($goods_info['status'] != -2) {
			$this->error = '该商品非待审核状态';
			return FALSE;
		}
		$result_refuse = $this->db->where(array('id'=>$goods_info['id']))->setField('status',0);
	    if(!$result_refuse) {
	        $this->error("审核拒绝操作失败");
	    } else {
			commission_log($product_id,-1,1,$goods_info['company_id'],$cause);
			$sign = '2-commission-'.$goods_info['company_id'].'-'.$product_id.'-2';
			action_finance_log($goods_info['company_id'], $goods_info['goods_service'], 'money', '活动审核失败，保证金退还', array('commission_id' =>$product_id));
			model('member_merchant')->where(array('userid'=>$goods_info['company_id']))->setDec('frozen_deposit',$goods_info['goods_service']);

	       //发送短息、邮件
	        runhook('product_check',array('userid' =>$goods_info['company_id'],'title' => $goods_info['title'],'result' => 0));
	        $this->success('审核拒绝操作成功');
	    }


	}


	



    /*平台确认付款*/
    public function admin_pay($id){
    	if (!$id) $this->error('参数错误！');
	    if(!defined('IN_ADMIN')) {
	        $this->error = '您无权确认付款';
	        return FALSE;
	    }
	    $product_info = $this->db->find($id);
	    if (!$product_info) $this->error('该商品不存在！');
	    $userid =  $product_info['company_id'];
	    //总保证金 = （下单价 +试客佣金 + 平台佣金（自动算）） * 商品数量
	    $total = sprintf('%.2f',($product_info['goods_price'] + $product_info['bonus_price'] + $product_info['service']) * $product_info['goods_number']);
	    /*如果计算出来的总保证金不等于goods_service  则从新保存*/
	    if ($total != $product_info['goods_service']) {
	    	$sqlMap = array();
		    $sqlMap['goods_service'] = $total;
		    $result = model('commission')->where(array('id'=>$product_info['id']))->save($sqlMap);
	    }
	    
        //扣除商家的余额（活动保证金）
	    //用户当前余额
	    $money = model('member')->getFieldByUserid($userid,'money');
	    if ($money < $total){
	        $this->error = '商家账户余额不足，请充值！当前余额'.$money.'元,<span style="color:red;">还需充值'.($total-$money).'元</span>';
	        return FALSE;
	    }
	    $sign = '2-commission-'.$product_info['company_id'].'-'.$product_info['id'].'-1';
	    $rs = model('member_finance_log')->where(array('only'=>$sign))->find();
	    if(!$rs){
	        action_finance_log($product_info['company_id'],-$total,'money',"平台代扣除活动（".$product_info['title']."）保证金：".$total."。", $sign, array('commission_id'=>$product_info['id']));
	        // 将保证金写入商家保证金字段
	   	    model('member_merchant')->where(array('userid'=>$userid))->setInc('frozen_deposit',$total);

	   	    return TRUE;

	    }else{
	        $this->error = '平台确认付款失败,重复操作';
	        return FALSE;
	    }
	    
	}   

	
	
	/**
	 * 屏蔽（暂停）商品
	 * @param int $product_id 商品ID
	 */
	public function blocked($product_id = 0) {
	    $msg = I('msg');
	    $msg = $msg ? $msg : '后台管理员屏蔽商品';
	    $factory = new \Commission\Factory\commission($product_id);
	    $result = $factory->set_status(5, $msg);
	    if(!$result) $this->error('屏蔽商品失败');
	    runhook('product_lock',array('userid' =>$factory->product_info['company_id'],'title' => $factory->product_info['title'],'cause' => $msg));
	    $this->success('屏蔽商品成功');
	}
	
	/**
	 * 查看商品操作日志
	 * @param int $product_id
	 */
	public function log($product_id = 0) {
	    $result = array('status' => 0);
	    $factory = $this->db->find($product_id);
	    $sqlmap = array();
        $sqlmap['p_id'] = $product_id;
        $log = model('commission_log')->where($sqlmap)->order("id DESC")->select();
	    if($log) {
	        foreach ($log as $k => $v) {
	            $v['dateline'] = dgmdate($v['dateline'], 'Y/m/d H:i');
	            if($v['is_sys'] == 1) {
	                $v['username'] = model('admin')->where(array('userid' => $v['uid']))->getField('username');
	            } else {
	                $v['username'] = model('member')->where(array('userid' => $v['uid']))->getField('nickname');
	            }
	            $log[$k] = $v;
	        }
	        $result['status'] = 1;
	        $result['data'] = $log;
	    }

	    
	    echo json_encode($result);
	}
	/**
     * 恢复已暂停的商品
     */
     public function recover($product_id = 0){
        $product_id = (int)$product_id;
        //检测该商品是否已经结束
        $factory = new \Commission\Factory\commission($product_id);
        $info = $factory->product_info;
        if($info['status'] != 5){
            $this->error('商品状态不是已暂停的状态');
        }
        if($info['end_time'] < NOW_TIME){
            $this->error('商品已结束，不能执行该操作');  
        }
        //将商品的状态改为活动进行中
        $result = $factory->set_status(1, '后台管理员恢复已暂停商品');
        if(!$result) {
			$this->error = '处理失败，请稍后重试';
		}
        $this->success('处理成功');
     }
}

