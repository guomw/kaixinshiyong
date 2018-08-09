<?php
namespace Member\Controller;
use Member\Controller\InitController;
class MerchantCommissionController extends InitController{
	public function _initialize(){
		parent::_initialize();
		//商品来源
		$this->source = model('shop_set')->getField('id, name', TRUE);
		//活动状态
		$this->activity_state = C('ACTIVITY_STATUS'); 
		//活动商品折扣范围
		$this->discount = C('SELLER_DISCOUNT_RANGE');
		$this->commission = D('commission');
		$this->commission_log = model('commission_log');
		$this->modelid = model('member')->getFieldByUserid($this->userid,'modelid');
		if($this->modelid != 2){
			$this->error('您不是商家会员，无权访问该页面');
		}
	}
	/*活动管理*/
	public function activity(){
		
		$pagecurr = max(1,I('page',0,'intval'));
		$pagesize = 5;
		$sqlMap = array();
		$sqlMap['company_id'] = $this->userid;
		$param = I('param.');
		$activity_state = (isset($param['activity_state'])) ? $param['activity_state'] : -99;
		$keyword = $param['keyword'];
		if(IS_GET){
			if($activity_state > -99){
				$sqlMap['status'] = $activity_state;
			}
			if(!empty($keyword)){
				$sqlMap['keyword|title'] = array("LIKE","%$keyword%");
			}
		}		
		$count = $this->commission->where($sqlMap)->count();
		$lists = $this->commission->where($sqlMap)->page($pagecurr,$pagesize)->order('id DESC')->select();
		$pages = showPage($count,$pagecurr,$pagesize);
		$form = new \Common\Library\form();
		include template('merchant/activity'); 
	}
	/*发布产品*/
	public function add(){
		$models = getcache('merchant_group','member');
		$groupid = model('member')->getFieldByUserid($this->userid,'groupid');
		$config = unserialize($models[$groupid]['config']);

		$commission = $config['commission'];
		$activitydays = $commission['activitydays'];

		if(IS_POST){
			$info = $_POST['info']; 
			$info['company_id'] = $this->userid;
			$info['mod'] = 'commission';
			$info['status'] = -3;
			$info['service'] = $this->service($info['goods_price']);
			$info['c_type'] = implode(',',$info['c_type']);
			$info['goods_albums'] =array2string($info['goods_albums']);
			$info['inputtime'] = NOW_TIME;
			$info['goods_service'] = ($info['goods_price'] + $info['bonus_price']+$this->service($info['goods_price']))*$info['goods_number'];
			
			$result = $this->commission->add($info);
			if(!$result) {
				$this->error('新增数据失败');
			} else {
				//加入产品日志
				commission_log($result,'-3','0',$this->userid,'加入产品');
				$this->success('产品发布成功', U('bailbond',array('id'=>$result,'mod'=>$mod)));
				
			}
		}else{
			$form = new \Common\Library\form();
			include template('merchant/add_commission');
		}
	}


	/*
	 * 活动担保金
	 */	
	public function bailbond($id = 0){
		$id = (int) $id;//商品的id
		if ($id < 1) $this->error('该商品不存在！');
		$info = $this->commission->find($id);
		if(empty($info)) $this->error('参数错误');
		extract($info);
		if($this->userid != $company_id || $status != -3) $this->error('您没有权限编辑该商品');
		//判断该商家的类型
		$groups = getcache('merchant_group','member');
		$groupid = $this->db->getFieldByUserid($this->userid,'groupid');
		$config = unserialize($groups[$groupid]['config']);
	    $cost = $config['commission'];
		if ($cost['cost'] == 0) {
			$services = '0.00';
		}else{
		    $price_type = $cost['service_price'];
			array_multisort($price_type,'min','SORT_ASC');
			if(!empty($price_type) && is_array($price_type)){
				$services = 0;//缴纳的佣金
				foreach ($price_type as $k=>$v){
					//判断下单在那个范围，应缴纳多少佣金  $max<$price_type<$min
					if( ($goods_price >= $v['min'] && $goods_price <$v['max'])){
						 $services = sprintf('%.2f',$v['commission']);
						 break;
						
					}
				}
			}


		}
		
		if(IS_POST){
			// 计算总保证金
			
				if ($cost['cost'] == 0) {
					//总价 = (下单价 +试客佣金)*数量  
					$total = sprintf('%.2f',($goods_price +  $bonus_price) * $goods_number);
				}else{
					//总价 = (下单价 +试客佣金+平台佣金)*数量  
					$total = sprintf('%.2f',($goods_price  + $service +  $bonus_price)*$goods_number);
				}


			
			$ids = (int) I('id');//商品id
			
			//用户当前余额
			$money = $this->db->getFieldByUserid($this->userid,'money');
			if ($money < $total) $this->error('您的余额不足，请充值！当前余额'.$money.'元,<span style="color:red;">还需充值'.($total-$money).'元</span>');
			//将该商品的状态改为未审核已付款(待审核)
			
			//将此信息写入记录表中
			$msg = 'id:'.$ids.','.$title.'交付担保金';
		    $sign = '2-'.$mod.'-'.$this->userid.'-'.$ids.'-1';
			$rs = model('member_finance_log')->where(array('only'=>$sign))->find();
			//echo model('member_finance_log')->getLastSql();
		
			if(!$rs){
				   $rss =  action_finance_log($this->userid,-$total,'money',$msg,$sign,array('commission_id'=>$id));
				  if(!$rss){
					  	$this->error('交付失败,请勿重复操作');
					  }else{
						$this->commission->where(array('id'=>$ids))->setField('status',-2);
						model('member_merchant')->where(array('userid'=>$this->userid))->setInc('frozen_deposit',$total);
						//加入产品日志
						commission_log($ids,'-2','0',$this->userid,'交付担保金');
						$this->success('交付成功');
					}
				}
			
		}else{
			include template('merchant/bailbond_commission');
		}
	}
	/*编辑活动*/
	public function edit(){
		$id = (int) I('id');
		if(empty($id)){
			$this->error('参数错误');
		}
		$infos =  $this->commission->find($id);
		$c_types = explode(',', $infos['c_type']);
		$goods_albumss = string2array($infos['goods_albums']);

		if(empty($infos)){
			$this->error('参数错误');
		}
		extract($infos);

		//活动类型
		$setting = model('activity_set')->where(array('activity_type' =>'commission'))->getField('key,value');
		//$price_type = $config['seller_price'];
		$bonus_prices = string2array($setting['commission_price']);
	    $bonus = $bonus_prices['commission'];
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

		if($company_id != $this->userid || $status != -3) $this->error('您没有权限修改该信息');
		$models = getcache('merchant_group','member');
		$groupid = $this->db->getFieldByUserid($this->userid,'groupid');
		$config = unserialize($models[$groupid]['config']);
		$commission = $config['commission'];
		$activitydays = $commission['activitydays'];
	
	
	
		if(IS_POST){
			$info = $_POST['info'];
			$info['company_id'] = $this->userid;
			$info['mod'] = 'commission';
			$info['status'] = -3;
			$info['service'] = $this->service($info['goods_price']);
			$info['c_type'] = implode(',',$info['c_type']);

			//$info['goods_albums'] = implode(',',$info['goods_albums']);
			//var_dump($info['goods_albums']);
			$info['goods_albums'] =array2string($info['goods_albums']);
			$info['inputtime'] = NOW_TIME;
			$info['goods_service'] = ($info['goods_price'] + $info['bonus_price']+$this->service($info['goods_price']))*$info['goods_number'];

		//	var_dump($info);
		//	die();

			$result = $this->commission->where(array('id'=>$id))->save($info);
			if(!$result) {
				$this->error('编辑失败,请重试！');
			} else {
				//加入产品日志
				commission_log($id,'-3','0',$this->userid,'编辑产品');
				$this->success('产品编辑成功', U('activity'));
			}
		}else{
			$form = new \Common\Library\form();
			include template('merchant/edit_commission');                  
		}
	}
	

	/**
	 * 待审核页面
	 */
	public function check(){
		if ($this->userinfo['modelid'] != 2) $this->error('请登录商家账号',U('member/index/login'));
		include template('merchant/check');
	}
	
	
	/**
	 * 产品日志查看
	 */
	public function commission_log($id = 0){
		$id = (int) $id;
		if($id < 1) $this->error('参数错误');
		$sqlMap = array();
		$sqlMap['p_id'] = $id;
		$sqlMap['userid'] = $this->userid; 
		$result = $this->commission_log->where($sqlMap)->select();
		foreach ($result as $k=>$v) {
			if($v['is_sys'] == 1) {
	               $result[$k]['nickname'] = '管理员'/*usemodel('admin')->where(array('rid' => $v['uid']))->getField('username')*/;
	            } else {
	               $result[$k]['nickname'] = model('member')->where(array('userid' => $v['uid']))->getField('nickname');
	            }

		}
		include template('merchant/commission_log');
	}

	/*检测商家参与闪电佣金权限*/
	public function check_authority(){
		$userid = $this->userid;
		$modelid = model('member')->getFieldByUserid($userid,'modelid');
    	if($userid < 1){
    		$this->error('您还没有登录，请先登录。>>去登录',U('Member/Index/login'));
    	}else{
    		//判断是否是商家会员
    		if($modelid == 1){
    			$this->error('您不是商家会员，请先注册商家会员。>>去注册',U('Member/Index/register'));
    		}
    	}

    	//判断该活动是否开启
    	$groupids = $this->db->getFieldByUserid($this->userid,'groupid');
 		$merchantLogic = D('Merchant','Logic');
		if(!$merchantLogic->isjoin('commission',$groupids)){
			$this->error('抱歉，您不能参与该活动');
		}
		

    	//判断商家信息是否填写
    	$models = getcache('model','commons');
    	$tablename = $models[$modelid]['tablename'];
    	$memberinfo = model($tablename)->where(array('userid'=>$userid))->find();
    	$store_info = model('merchant_store')->where(array('userid'=>$userid))->find();

/*    	if($memberinfo['store_name'] == '' && $store_info == ''){
    		$this->error('您的商家信息还没有完善，请先完善商家信息。>>去完善',U('Member/Merchant/complete'));
    	}*/
    	$groups = getcache('merchant_group','member');
    	if (!$groups) {
    		$groups = model('merchant_group')->select();
    	}
    	$groupid = model('member')->getFieldByUserid($userid,'groupid');
    	$configs = $groups[$groupid]['config'];
    	$config = unserialize($configs);
    	$activity = model('activity_set')->where(array('activity_type'=>'commission'))->getField('key,value');
    	$sell_join_condition = string2array($activity['seller_join_condition']);
    	$types = array();
    	foreach ($sell_join_condition as $k=>$v){
    		$types[]= $k;
    	}
    	$commission = $config['commission'];
        //判断该活动是否开启
        $isopen = $activity['commission_isopen'];
        if($isopen != 1){
            $this->error('抱歉，该活动尚未开启');
        }
       
        //判断是否允许参与
        if($commission['isopen'] != 1){
            $this->error('您没有权限参与该活动');
        }
        //判断参与条件
        if(in_array('realname',$types)){//实名认证
            $identity = model('member_attesta')->where(array('userid'=>$userid,'type'=>'identity'))->find();
            if(!$identity){
                $this->error('您还没有进行身份验证，请先验证。 >>去验证',U('Member/Attesta/name_attesta'));
            }else{
                if($identity['status'] == 0){
                    $this->error('您的身份验证正在审核中，请耐心等待。',U('Member/Attesta/peoson'));
                }elseif($identity['status'] == -1){
                    $this->error('您的身份验证审核失败，请重新上传资料。',U('Member/Attesta/name_attesta'));
                }
            }
        }
         if(in_array('phone',$types)){//手机绑定
            $phone = model('member')->getFieldByUserid($userid,'phone_status');
            if($phone != 1){
                $this->error('您还没有进行手机验证，请先验证。>>去验证',U('Member/Attesta/phone_attesta'));
            }
        }


        if (in_array('email',$types)){//邮箱认证
            $email_status = model('member')->getFieldByUserid($userid,'email_status');
            if($email_status != 1){
                $this->error('您还没有进行邮箱验证，请先验证。>>去验证',U('Member/Attesta/email_attesta'));
            }
        }
       
/*        if(in_array('information',$types)){//完善身份信息
            $infor = model('member_merchant')->where(array('userid'=>$userid))->find();
            $store = model('merchant_store')->where(array('userid'=>$userid))->find();

            if(empty($infor['store_name']) && empty($store)){
                $this->error('您还没有完善资料？>>去完善',U('Member/Merchant/complete'));
            }
        }
*/
        $this->success('审核通过');


	}



	/**
	 * 佣金验证
	 */
	public function reward(){
		$company_id = $this->userid;
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
		$company_id = $this->userid;
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


public function multi_array_sort($multi_array, $sort_key, $sort = 'SORT_DESC') {
	if (is_array($multi_array)) {
		$key_array = array();
		foreach ($multi_array as $row_array) {
			if (is_array($row_array)) {
				$key_array[] = $row_array[$sort_key];
			} else {
				return FALSE;
			}
		}
	} else {
		return FALSE;
	}
	array_multisort($key_array, $sort, $multi_array);
	return $multi_array;
}

	/*获取平台佣金*/
	public function service($goods_price){
		$groups = getcache('merchant_group','member');
		$groupid = $this->db->getFieldByUserid($this->userid,'groupid');
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
					if( ($goods_price >= $v['min'] && $goods_price <= $v['max'])){
						 $service = sprintf('%.2f',$v['commission']);
						 break;
						
					}
				}
			}
		}

		return $service;
		
	}




	/*撤销活动*/
	public function revocation(){
		$id = (int)I('id');
		$info = $this->commission->find($id);
		if(empty($info)) $this->error('参数错误');
		if($info['company_id'] != $this->userid || $info['status'] != -3){
			$this->error('您没有权限访问该页面');
		}
		  
		//修改活动的订单状态
		$rs = $this->commission->where(array('id'=>$id))->setField('status',4);
		if(!$rs){
			//加入产品日志
			commission_log($id,'-3','0',$this->userid,'撤销活动');
		    $this->success('撤销成功');
		}else{
		    $this->error('撤销失败,重复操作');
		}
		
	}
	


	
	
	

	
	


}