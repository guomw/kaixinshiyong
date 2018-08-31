<?php
namespace Member\Controller;
use Member\Controller\InitController;
class MerchantProductController extends InitController{
	public function _initialize(){
		parent::_initialize();
		//商品来源
		$this->source = model('shop_set')->getField('id, name', TRUE);
		//活动状态
		$this->activity_state = C('ACTIVITY_STATUS'); 
		//活动商品折扣范围
		$this->discount = C('SELLER_DISCOUNT_RANGE');
		$this->product_db = D('Product/Product');
		$this->product_log = model('product_log');
		$this->modelid = model('member')->getFieldByUserid($this->userid,'modelid');
		if($this->modelid != 2){
			$this->error('您不是商家会员，无权访问该页面');
		}
	}
	/*活动管理*/
	public function activity(){
		$mod = (I('mod')) ? I('mod') : '';
		$pagecurr = max(1,I('page',0,'intval'));
		$pagesize = 5;
		$sqlMap = array();
		if($mod != ''){
			$sqlMap['mod'] = $mod;
		}
		$userid = $this->userid;
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
		$count = $this->product_db->where($sqlMap)->count();
		$lists = $this->product_db->where($sqlMap)->page($pagecurr,$pagesize)->order('updatetime DESC')->select();
		foreach ($lists as $k=>$v) {
			$v = $this->product_db->detail($v['id']);
			$lists[$k] = $v;
		}
		
		$pages = showPage($count,$pagecurr,$pagesize);
	    $v2_pages = v2_page_3($count,$pagesize);

		$form = new \Common\Library\form();
		$tpl = 'merchant/activity';
		include template($tpl); 
	}

	public function select_trial(){
		$source = model('shop_set')->getField('id,name');
		$mod = I('mod','trial');  
		$activity_set = model('activity_set')->where(array('activity_type'=>$mod))->getField('key,value');
		$activity = string2array($activity_set['single_mode']);
		if($activity_set['seller_bonus_isopen'] == 1) {
			//每单赠送红包范围
			$bonus_price = string2array($activity_set['bonus_price']);
		}
		//商品发布件数
		$models = getcache('merchant_group','member');
		$groupid = model('member')->getFieldByUserid($this->userid,'groupid');
		$config = unserialize($models[$groupid]['config']);
		$rebate = $config[$mod];
		$a_b = $rebate['a_b'];
		$red = $rebate['red'];
		//该商家的下单类型
		$ordertype = $models[$groupid]['ordertype'];
		$ordertype = explode(',',$ordertype);
		$trial = $config['trial'];
		$merchant_name = $models[$groupid]['name'];
		include template('merchant/select_trial');
	}
	/*发布产品*/
	public function add(){
		$mod = I('mod','rebate');  

        /*检测试用活动是否开启*/
        if($mod == 'trial' && C('TRIAL_ISOPEN') != 1){
        	$this->error('亲,抱歉试用活动尚未开启！');
        }

        /*检测返利活动是否开启*/
        if($mod == 'rebate' && C('REBATE_ISOPEN') != 1){
        	$this->error('亲,抱歉返利活动尚未开启！');
        }

        /*检测闪电试用活动是否开启*/
        if($mod == 'commission' && C('COMMISSION_ISOPEN') != 1){
        	$this->error('亲,抱歉闪电试用活动尚未开启！');
        }
        

		//发布购物返利活动
		$bonus = I('bonus');
		$new_ordertype = I('ordertype','search');
		$new_postal = I('postal',0);
		$new_trial_type = I('trial_type','a');
		$new_address = I('address',0);
		$new_source = I('source',1);
		$new_red = I('trial_type','a');

		if ($new_red == 'a') {
			$trial_type = 1;
		}elseif($new_red == 'b'){
			$trial_type = 2;
		}elseif($new_red == 'red'){
			$trial_type = 3;
		}
		$nojinlai = I('nojinlai');
		if($nojinlai != 1){
		    //判断是否满足报名条件
		    $check = A('Member/EnterActivity');
		    $check->check($mod,1,$bonus);
		}

		//该商家所属的专员
		$agent_id = model('member')->getFieldByUserid($this->userid,'agent_id');
		$attach = model('admin')->where(array('userid'=>$agent_id))->field('username,qq')->find();
		//活动类型
		$activity_set = model('activity_set')->where(array('activity_type'=>$mod))->getField('key,value');
		$activity = string2array($activity_set['single_mode']);
		if($activity_set['seller_bonus_isopen'] == 1) {
			//每单赠送红包范围
			$bonus_price = string2array($activity_set['bonus_price']);
		}

		$store_infos = model('merchant_store')->where(array('userid'=>$this->userid))->order('is_default DESC')->select();
		if (!$store_infos) {
			$store_info = model('member_merchant')->where(array('userid'=>$this->userid))->select();
		}else{
			$store_info = $store_infos;
		}

		$merchant = model('member_merchant')->where(array('userid'=>$this->userid))->find();
		//商品发布件数
		$models = getcache('merchant_group','member');
		$groupid = model('member')->getFieldByUserid($this->userid,'groupid');
		$config = unserialize($models[$groupid]['config']);
		$merchant_name = $models[$groupid]['name'];
		$rebate = $config[$mod];
		//该商家的下单类型
		$ordertype = $models[$groupid]['ordertype'];
		$ordertype = explode(',',$ordertype);
		//发布商品件数
		$goods_number = $rebate['goods_number'];
		//商品价格
		$price_range = $rebate['price_range'];
		$activitydays = $rebate['activitydays'];
		//判断该活动结束时间
		$starttime = strtotime(C('SELLER_START_TIME'));
		$endtime = strtotime(C('SELLER_END_TIME'));
		if(NOW_TIME < $starttime){
			$this->error('抱歉，该活动还没有开始');
		}
		if(NOW_TIME > $endtime){
			$this->error('抱歉，该活动已结束');
		}
		//判断该活动是否开启
 		$merchantLogic = D('Merchant','Logic');
		if(!$merchantLogic->isjoin($mod,$groupid)){
			$this->error('抱歉，您不能参与该活动');
		}
		//间隔时间
		$distime = $merchantLogic->distime($mod,$groupid,$this->userid);
		if(is_numeric($distime) === false && $distime != '') {
			$this->error('请在'.$distime.'之后再添加');
		}
		//报名次数判断
		$applytimes = $this->product_db->where(array('userid'=>$this->userid,'mod'=>$mod))->count();
		if($applytimes > $merchantLogic->apply($mod,$groupid)){
			$this->error('您报名次数已达上限');
		}

		
		if(IS_POST){
			$info = $_POST['info']; 
			/*if ($info['ctype']) {
				$info['ctype'] = json_encode($info['ctype']);
			}
			*/
            //更新该商家招商专员
            if($info['attract'] != 0){
                Model('member')->where(array('userid'=>$this->userid))->save(array('agent_id'=>$info['attract']));
            }
             
             if($mod=='rebate' ){
               $service_type = C_READ('service_type','rebate');
     			if($service_type == 2){  
     	           $info['service_type'] = 2;
     			}
             }

             if ($mod == 'commission') {
             	 $info['allow_groupid'] = array2string($info['allow_groupid']);

             }

			$info['mod'] = $mod;
			$info['company_id'] = $this->userid;
 			$product = D('Product/Product');
			$result = $product->update($info);
			/*echo $product->getLastSql();
			die();*/
			if(!$result) {
				$this->error($product->getError());
			} else {
				//加入产品日志
				product_log($result,'-3','0',$this->userid,'加入产品');
				if($mod == 'postal'){
					$this->success('产品发布成功',U('activity',array('mod'=>$mod)));
				}else{
					$this->success('产品发布成功', U('bailbond',array('id'=>$result,'mod'=>$mod)));
				}
			}
		}else{
			$form = new \Common\Library\form();
			include template('merchant/add_'.$mod);
		}
	}

	public  function  select_trial_edit(){
		$mod = I('mod','trial');
		$id = (int) I('id');
		$sources = model('shop_set')->getField('id,name');
		$activity_set = model('activity_set')->where(array('activity_type'=>$mod))->getField('key,value');
		$activity = string2array($activity_set['single_mode']);
		if($activity_set['seller_bonus_isopen'] == 1) {
			//每单赠送红包范围
			$bonus_price = string2array($activity_set['bonus_price']);
		}
		//商品发布件数
		$models = getcache('merchant_group','member');
		$groupid = model('member')->getFieldByUserid($this->userid,'groupid');
		$config = unserialize($models[$groupid]['config']);
		$rebate = $config[$mod];

		//该商家的下单类型
		$ordertype = $models[$groupid]['ordertype'];
		$ordertype = explode(',',$ordertype);

		$infos =  $this->product_db->detail($id);
		if(empty($infos)){
			$this->error('参数错误');
		}
		if($mod != $infos['mod']){
			$this->error('没有该商品');
		}
		extract($infos);
		if (IS_POST) {
			$info['id'] = $id;
			$info['mod'] = $mod;
			$info['company_id'] = $this->userid;
			$result = $this->product_db->update($info);
			if(!$result) {
				$this->error($this->product_db->getError());
			} else {
				//加入产品日志
				$this->success('产品编辑成功', U('edit',array('mod'=>$mod,'id'=>$id)));
			}
		}

		include template('merchant/edit_select_trial');

	}
	/*编辑活动*/
	public function edit(){
		$mod = I('mod','rebate');
		$id = (int) I('id');
		/*$new_ordertype = I('ordertype');
		$new_postal = I('postal');
		$new_trial_type = I('trial_type');
		$new_address = I('address');
		$new_source = I('source');*/
		//该商家所属的专员
		$agent_id = model('member')->getFieldByUserid($this->userid,'agent_id');
		$attach = model('admin')->where(array('userid'=>$agent_id))->field('username,qq')->find();
		
		//活动类型
		$activity_set = model('activity_set')->where(array('activity_type'=>$mod))->getField('key,value');
		$activity = string2array($activity_set['single_mode']);
		if($activity_set['seller_bonus_isopen'] == 1) {
			//每单赠送红包范围
			$bonus_price = string2array($activity_set['bonus_price']);
		}
		$infos =  $this->product_db->detail($id);
		if(empty($infos)){
			$this->error('参数错误111');
		}
		if($mod != $infos['mod']){
			$this->error('没有该商品');
		}
		$store_infos = model('merchant_store')->where(array('userid'=>$this->userid))->order('is_default DESC')->select();
		if (!$store_infos) {
			$store_info = model('member_merchant')->where(array('userid'=>$this->userid))->select();
		}else{
			$store_info = $store_infos;
		}

		extract($infos);
		if($company_id != $this->userid || $status != -3) $this->error('您没有权限修改该信息');
		$models = getcache('merchant_group','member');
		$groupid = $this->db->getFieldByUserid($this->userid,'groupid');
		$merchant_name = $models[$groupid]['name'];
		$config = unserialize($models[$groupid]['config']);
		//该商家的下单类型
		$ordertype = $models[$groupid]['ordertype'];
		$ordertype = explode(',',$ordertype);
		$rebate = $config[$mod];
		//发布商品件数
		$goods_number_config = $rebate['goods_number'];
		//商品价格
		$price_range = $rebate['price_range'];
		$activitydays = $rebate['activitydays'];
		if(IS_POST){
			$info = $_POST['info'];
			$info['id'] = $id;
			$info['mod'] = $mod;
			if ($mod == 'commission') {
             	 $info['allow_groupid'] = array2string($info['allow_groupid']);

             }
			$info['company_id'] = $this->userid;
			$result = $this->product_db->update($info);
			if(!$result) {
				$this->error($this->product_db->getError());
			} else {
				//加入产品日志
				product_log($id,'-3','0',$this->userid,'编辑产品');
				$this->success('产品编辑成功', U('activity',array('mod'=>$mod)));
			}
		}else{
			$form = new \Common\Library\form();
			include template('merchant/edit_'.$mod);                  
		}
	}
	/**
	 * 计算商品总保证金
	 */
	public function product_total($id=0){
		$id = (int) $id;
		if($id < 1) $this->error('参数错误');
		$info = $this->product_db->detail($id);
		extract($info);
		//判断该商家的类型
		$groups = getcache('merchant_group','member');
		$groupid = $this->db->getFieldByUserid($this->userid,'groupid');
		$config = unserialize($groups[$groupid]['config']);
		if($mod == 'rebate'){//购物返利
			$service = $config[$mod]['service'];//平台服务费
		}else if($mod == 'trial'){//推广费用
			//判断是按单份或者单场

			if ($trial_type == 3) {
				$service = $this->red_reward($goods_price);

			}elseif($trial_type == 2){
				$service = $this->ab_reward($goods_price);

			}else{

				$cost = C_READ('seller_charge_money',$mod);
				$service = ($cost == 0) ? $config[$mod]['cost']['product_cost'] : $config[$mod]['cost']['activity_cost'];

			}
			
		}
		// 计算总保证金
		if ($mod == 'rebate'){
			//保证金  = (下单价+技术服务费) * 商品数量	/////  技术服务费  = 下单价 * 平台服务费率 / 100
			
		    // 如果为全额缴纳方式
		    if($service_type == 1){
		       $total = sprintf("%.2f",($goods_price + ($goods_price * $service / 100)) * $goods_number);
		    }

			// 如果为部分缴纳方式 2015- 11-25 增加
	       if($service_type == 2){
	       	$total = sprintf("%.2f",(($goods_price-($goods_price / 10 * $goods_discount ) )+ ($goods_price * $service / 100)) * $goods_number);
	       }   


		}else{//免费试用
			if ($cost == 0) {
				//总价 = (单价 +收费价格)*数量  [单份] + 红包钱
				$total = sprintf('%.2f',($goods_price + $service + $goods_bonus) * $goods_number);
			}else{
				//总价 = 单价 * 数量 + 收费价格 [单场] + 红包钱
				$total = sprintf('%.2f',($goods_price * $goods_number) + $service + $goods_number * $goods_bonus);
			}
		}
		echo $total;
	}
	/*
	 * 活动担保金
	 */	
	public function bailbond($id = 0){
		$id = (int) $id;//商品的id
		if ($id < 1) $this->error('该商品不存在！');
		$info = $this->product_db->detail($id);

		if(empty($info)) $this->error('参数错误');
		extract($info);

		if($this->userid != $company_id || $status != -3) $this->error('您没有权限编辑该商品');
		//判断该商家的类型
		$groups = getcache('merchant_group','member');
		$groupid = $this->db->getFieldByUserid($this->userid,'groupid');
		$config = unserialize($groups[$groupid]['config']);
		if($mod == 'rebate'){//购物返利
			$service = $config[$mod]['service'];//平台服务费
		}else if($mod == 'trial'){//推广费用
			if ($trial_type == 3) {
				 $service = $this->red_reward($goods_price);
				 $_type = 3;

			}elseif($trial_type == 2){
				$service = $this->ab_reward($goods_price);
				$_type = 2;

			}else{
				//判断是按单份或者单场
				$cost = C_READ('seller_charge_money',$mod);
				// product_cost:单份推广费;activity_cost:单场推广费
				$service = ($cost == 0) ? $config[$mod]['cost']['product_cost'] : $config[$mod]['cost']['activity_cost'];
				$_type = 1;

			}


			


		}else if($mod == 'commission'){
			//判断该商家的类型
		
	    	$cost = $config['commission'];

			if ($cost['cost'] == 0) {
				$service = '0.00';
			}else{
			    $service = $this->service($goods_price);



			}
		}
		if(IS_POST){
			
			// 计算总保证金
			if ($mod == 'rebate'){
				//保证金  = (下单价+技术服务费) * 商品数量	/////  技术服务费  = 下单价 * 平台服务费率 / 100 
			    
			    // 如果为全额缴纳方式
			    if($service_type == 1){
			       $total = sprintf("%.2f",($goods_price + ($goods_price * $service / 100)) * $goods_number);
			    }

				// 如果为部分缴纳方式
		       if($service_type == 2){
		       	$total = sprintf("%.2f",(($goods_price-($goods_price / 10 * $goods_discount ) )+ ($goods_price * $service / 100)) * $goods_number);
		       }                                     //下单价100  折扣1折 需返还给会员90美元

			}elseif($mod == 'commission'){
				if ($cost['cost'] == 0) {
					//总价 = (下单价 +试客佣金)*数量  
					$total = sprintf('%.2f',($goods_price +  $bonus_price) * $goods_number);
				}else{
					//总价 = (下单价 +试客佣金+平台佣金)*数量  
					$total = sprintf('%.2f',($goods_price  + $service +  $bonus_price)*$goods_number);
				}

			}else{//免费试用




				if ($cost == 0) {
					//总价 = (单价 +收费价格)*数量  [单份] + 红包钱
					$total = sprintf('%.2f',($goods_price + $service + $goods_bonus) * $goods_number);
				}else{
					//总价 = 单价 * 数量 + 收费价格 [单场] + 红包钱
					$total = sprintf('%.2f',($goods_price * $goods_number) + $service + $goods_number * $goods_bonus);
				}
			}


			$ids = (int) I('id');//商品id
			//用户当前余额
			$money = $this->db->getFieldByUserid($this->userid,'money');
			if ($money < $total) $this->error('您的余额不足，请充值！当前余额'.$money.'美元,<span style="color:red;">还需充值'.($total-$money).'美元</span>');


			//将该商品的状态改为未审核已付款(待审核)
			$rs = $this->product_db->where(array('id'=>$ids))->setField('status',-2);
			if(!$rs) $this->error('交付失败');	
			//将此信息写入记录表中
			$msg = 'id:'.$ids.','.$info['title'].'交付担保金';
			$sign = '2-'.$mod.'-'.$this->userid.'-'.$id.'-1';
			$rs = model('member_finance_log')->where(array('only'=>$sign))->find();
			if(!$rs){
				
				action_finance_log($this->userid,-$total,'money',$msg,$sign,array('goods_id'=>$id));
					    
			}
			if($mod == 'trial'){				
				// 剩余推广费是否退还 [后台->活动设置 0:不退还；1:退还]
		        $seller_give_back = C_READ('seller_give_back','trial');
		        // 收费方式 $cost [后台->活动设置 0:按单份；1:按单场]  不退还推广费则不存推广费到商家冻结保证金中
		        if ($seller_give_back == 0) {
		        	if ($cost == 1){
		        		$total = $total - $service;
		        		action_finance_log($this->userid,-$service,'deposit',"扣除活动id:".$id."推广服务费(不退还)",array('goods_id'=>$id));
		        	}else{
		        		$total = $total - ($service * $goods_number);
		        		action_finance_log($this->userid,-($service * $goods_number),'deposit',"扣除活动id:".$id."推广服务费(不退还)",array('goods_id'=>$id));		        		
		        	}
		        }
		        // 将保证金、平台服务费、是否退还保证金 存入数据库中
				model('product_'.$mod)->where(array('id'=>$ids))->save(array('goods_deposit'=>$total,'goods_service'=>$service,'goods_charge_way'=>$cost,'seller_give_back'=>$seller_give_back));
				// 将保证金写入商家保证金字段				
				model('member_merchant')->where(array('userid'=>$this->userid))->setInc('frozen_deposit',$total);
			}elseif($mod == 'commission'){
				model('product_'.$mod)->where(array('id'=>$ids))->save(array('goods_deposit'=>$total,'goods_service'=>$service));
				model('member_merchant')->where(array('userid'=>$this->userid))->setInc('frozen_deposit',$total);

			}else{
				model('product_'.$mod)->where(array('id'=>$ids))->save(array('goods_deposit'=>$total,'goods_service'=>$service));
				model('member_merchant')->where(array('userid'=>$this->userid))->setInc('frozen_deposit',$total);
			}			
			//加入产品日志
			product_log($ids,'-2','0',$this->userid,'交付担保金');
			$this->success('交付成功');
		}else{
			include template('merchant/bailbond_'.$mod);
		}
	}
	/**
	 * 待审核页面
	 */
	public function check(){
		if ($this->userinfo['modelid'] != 2) $this->error('请登录商家账号',U('member/index/login'));
		include template('merchant/check');
	}
	/*二维码上传*/
	public function code(){
		if(!empty($_FILES)){
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize  =     3145728 ;
			$upload->exts     =     array('jpg', 'gif', 'png');
			$upload->rootPath = SITE_PATH.'/uploadfile/code/';
			if(!file_exists($upload->rootPath)){//不存在，则创建
               mkdir($upload->rootPath, 0777);
            }
			$upload->savePath = '';
			$upload->replace  = TRUE;
			$upload->saveName = NOW_TIME.random(5,1).'_code';
			$upload->autoSub = FALSE;
			$upload->saveExt  = 'jpg';
			$result = $upload->upload($_FILES);
			$name = __ROOT__.'/uploadfile/code/'.$result['Filedata']['savename'];
			if($result){
				echo $name;exit();
			}else{
				exit('0');
			}
			
		}
	}
	/*撤销活动*/
	public function revocation(){
		$id = (int)I('id');
		$mod = I('mod');
		$info = $this->product_db->detail($id);
		if(empty($info)) $this->error('参数错误');
		if($info['company_id'] != $this->userid || $info['status'] != -3){
			$this->error('您没有权限访问该页面');
		}
		//修改活动的订单状态
		$r = $this->product_db->where(array('id'=>$id))->setField('status',4);
		//加入产品日志
		product_log($id,'-3','0',$this->userid,'撤销活动');
		if($r){
			//退还相应的保证金
			$goods_deposit = $info['goods_deposit'];
			$sign = '2-'.$mod.'-'.$this->userid.'-'.$id.'-5';
			$rs = model('member_finance_log')->where(array('only'=>$sign))->find();
			if(!$rs){
			    action_finance_log($this->userid,$goods_deposit,'money','userid:'.$this->userid.'撤销活动-退还保证金',$sign,array('goods_id'=>$id));
			    $this->success('撤销成功');
			}else{
			    $this->error('撤销失败,重复操作');
			}
		}else{
			$this->error('撤销失败');
		}
	}
	
	/**
	 * 产品日志查看
	 */
	public function product_log($id = 0){
		$id = (int) $id;
		if($id < 1) $this->error('参数错误');
		$sqlMap = array();
		$sqlMap['p_id'] = $id;
		$sqlMap['uid'] = $this->userid; 
		$result = $this->product_log->where($sqlMap)->select();
		foreach ($result as $k=>$v) {
			$result[$k]['nickname'] = $this->db->getFieldByUserid($v['uid'],'nickname');      
		}
		include template('merchant/product_log');
	}
	/*删除产品*/
	public function product_delete($id = 0){
		$id = (int) $id;
		if($id < 0){echo $this->error('参数错误');}
		$result = $this->product_db->where(array('id'=>$id))->delete();  
		if(!$result){
			$this->error('删除失败');
		}else{
			$this->success('删除成功');
		}
	}
	/*产品分类选择*/
	public function check_category(){
		$linkageid = I('linkage_catid');
		//根据目前的栏目产品分了id查出他的子id
		$infos = model('product_category')->where(array('parentid'=>$linkageid))->select();
		foreach($infos as $k=>$v){
			$ids[] = $v['catid'];
		}
		echo json_encode($ids);
	}
	/**
	 * 推荐产品
	 */
	public function recommend(){
		$id = I('gid');
		$info = $this->product_db->detail($id);
		if(empty($info)) $this->error('没有找到相关商品');
		//查看是否为该商家发布商品
		if(($this->userid != $info['userid']) && $info['status'] != 1){
			$this->error('你没有权限编辑改产品');
		}
		//查询该商品是否已经推荐
		$rs = model('product')->where(array('id'=>$id,'isrecommend'=>1))->find();
		if($rs > 0){
			$this->error('该产品已经被推荐');
		}
		$result = model('product')->where(array('id'=>$id))->setField('isrecommend',1);
		if($result){
			$this->success('推荐产品成功');
		}else{
			$this->error('推荐产品失败');
		}
	}
	/**
	 * 取消推荐
	 */
	public function unrecommend(){
		$id = I('gid');
		$info = $this->product_db->detail($id);
		if(empty($info)) $this->error('没有找到相关商品');
		//查看是否为该商家发布商品
		if(($this->userid != $info['userid']) && $info['status'] != 1){
			$this->error('你没有权限编辑改产品');
		}
		$result = model('product')->where(array('id'=>$id))->setField('isrecommend',0);
		if($result){
			$this->success('取消该产品推荐成功');
		}else{
			$this->error('取消该产品推荐失败');
		}
	}

	/* 活动结算 */
	public function activity_over() {
		if (IS_POST) {
            $goods_id = (int)$_POST['goods_id'];
            if ($goods_id < 1) $this->error('该商品不存在！');
            $factory = new \Product\Factory\product($goods_id);            
            if(!$factory) $this->error($factory->getError());
        	$factory->action_over_info();
        	runhook('product_balance',array('userid' => $factory->product_info['company_id'],'title' => $factory->product_info['title']));
            $this->success('活动商品结算完成', 'javascript:close_dialog();');
        }else{
        	$goods_id = (int)$_GET['goods_id'];
			if ($goods_id < 1)	$this->error('该商品有误！');
			$factory = new \Product\Factory\product($goods_id);
			if ($factory->product_info['company_id'] != $this->userid) $this->error('请登录商家账号');
			$info = $factory->get_over_info();
			if (!$info)	$this->error($factory->getError());
			$this->activity_status = C('ACTIVITY_STATUS');
			include template('merchant/activity_over');
        }		
	}

	/* 追加商品 */
	public function push_number() {
		$param = I('param.');
		$pid = (int)$param['pid'];
		if ($pid < 1) $this->error('该商品不存在！');
		$factory = new \Product\Factory\product($pid);
		$proInfo = $factory->product_info;
		if ($proInfo['status'] != 1) $this->error('必须是正在进行中的商品才能追加库存！');
		if ($this->userid != $proInfo['company_id']) $this->error('您无权追加此商品！');
		$proInfo['com_day'] = C_READ('seller_push_days',"$proInfo[mod]");
		$proInfo['com_number'] = C_READ('seller_push_nums',"$proInfo[mod]");
		unset($proInfo['goods_content']);
		$records = model('product_complement')->where(array('product_id' => $proInfo['id'],'status' => 0))->find();
		if (IS_POST) {
			if ($param['com_day'] > $proInfo['com_day']) $this->error('追加天数必须小于'.$proInfo['com_day'].'天');
			if ($param['com_number'] < $proInfo['com_number']) $this->error('追加份数必须大于'.$proInfo['com_number'].'份');			
			// 计算总保证金
			if ($proInfo['mod'] == 'rebate'){
				//保证金 = (下单价+技术服务费) * 商品数量 ///// 技术服务费 = 下单价 * 平台服务费率 / 100 
	    	    if($proInfo['service_type'] == 1){
				   $total = sprintf("%.2f",($proInfo['goods_price'] + ($proInfo['goods_price'] * $proInfo['goods_service'] / 100)) * $param['com_number']);
	    	    }
	    		// 如果为部分缴纳方式
	    	    if($proInfo['service_type'] == 2){
	    	       $total ="9999";
	           	   $total = sprintf("%.2f",(($proInfo['goods_price']-($proInfo['goods_price'] / 10 *  $proInfo['goods_discount'] ) )+ ($proInfo['goods_price'] * $proInfo['goods_service'] / 100)) * $param['com_number']);
	           } 

			}else{//免费试用
				if ($proInfo['goods_charge_way'] == 0) {
					//总价 = (下单价 + 平台推广费[单份] + 红包[单份]) * 数量   
					$total = sprintf('%.2f',($proInfo['goods_price'] + $proInfo['goods_service'] + $proInfo['goods_bonus']) * $param['com_number']);
				}else{
					//总价 = (下单价 + 红包[单份]) * 数量 + 收费价格 [单场] 
					$total = sprintf('%.2f',($proInfo['goods_price'] + $proInfo['goods_bonus']) * $param['com_number'] + $proInfo['goods_service']);
				}
			}

			//用户当前余额
			$money = $this->db->getFieldByUserid($this->userid,'money');
			if ($money < $total) $this->error('您的余额不足，请先充值！');

			$data = array();
			$data['id'] = (int)$records['id'];
			$data['product_id'] = $pid;
			$data['company_id'] = $this->userid;
			$data['com_number'] = $param['com_number'];
			$data['com_day'] = $param['com_day'];
			$data['com_total_fee'] = $total;
			$data['dateline'] = NOW_TIME;
			$data['status'] = 0;
			$result = model('product_complement')->update($data);
			if (!$result)	$this->error('追加记录失败！');
			$this->success('追加商品库存成功！',U('push_bailbond',array('pid'=>$pid)));
		}else{
			include template('merchant/alert_push_number');
		}
	}
	/* 追加保证金 */
	public function push_bailbond() {
		$param = I('param.');
		$pid = (int)$param['pid'];
		if ($pid < 1)	$this->error('该商品不存在！');
		$factory = new \Product\Factory\product($pid);
		$proInfo = $factory->product_info;
		if ($proInfo['status'] != 1) $this->error('必须是正在进行中的商品才能追加库存！');
		if ($proInfo['company_id'] != $this->userid) $this->error('您没有权限访问该页面！');
		unset($proInfo['goods_content']);
		$records = model('product_complement')->where(array('product_id' => $proInfo['id'],'status' => 0))->find();
		$this->activity_status = C('ACTIVITY_STATUS');
		if (!$records) $this->error('该请求不存在，请重新追加商品库存');
		if ($records['company_id'] != $this->userid) $this->error('该请求不存在，请重新追加商品库存');
		if (IS_POST) {
			// 计算总保证金
			if ($proInfo['mod'] == 'rebate'){
				//保证金 = (下单价+技术服务费) * 商品数量 ///// 技术服务费 = 下单价 * 平台服务费率 / 100 
				//$total = sprintf("%.2f",($proInfo['goods_price'] + ($proInfo['goods_price'] * $proInfo['goods_service'] / 100)) * $records['com_number']);
	       	    // 如果为全额缴纳方式
	       	    if($proInfo['service_type'] == 1){
				   $total = sprintf("%.2f",($proInfo['goods_price'] + ($proInfo['goods_price'] * $proInfo['goods_service'] / 100)) * $records['com_number']);
	       	    }
	       		// 如果为部分缴纳方式
	       	    if($proInfo['service_type'] == 2){
	              	$total = sprintf("%.2f",(($proInfo['goods_price']-($proInfo['goods_price'] / 10 *  $proInfo['goods_discount'] ) )+ ($proInfo['goods_price'] * $proInfo['goods_service'] / 100)) * $records['com_number']);
	              } 

			}else{//免费试用
				if ($proInfo['goods_charge_way'] == 0) {
					//总价 = (下单价 + 平台推广费[单份] + 红包[单份]) * 数量   
					$total = sprintf('%.2f',($proInfo['goods_price'] + $proInfo['goods_service'] + $proInfo['goods_bonus']) * $records['com_number']);
				}else{
					//总价 = (下单价 + 红包[单份]) * 数量 + 收费价格 [单场] 
					$total = sprintf('%.2f',($proInfo['goods_price'] + $proInfo['goods_bonus']) * $records['com_number'] + $proInfo['goods_service']);
				}
			}

			if ($total != $records['com_total_fee']) $this->error('价格有误 请重新追加库存或联系管理员!');
			//用户当前余额
			$money = $this->db->getFieldByUserid($this->userid,'money');
			if ($money < $total) $this->error('您的余额不足，请充值！');
			
			$map['product_id'] = $pid;
            $map['status'] = 2;
			$complement1 = model('product_complement')->where($map)->select(); 
			if($complement1 != null) $this->error('您之前还有未审核的追加记录,请等待审核之后再进行追加！');     
            //数据库查询是否有追加记录 如果存在则不允许继续追加

			$msg = 'id:'.$proInfo['id'].','.$proInfo['title'].'交付追加库存担保金';
			$sign = '2-'.$proInfo['mod'].'-'.$this->userid.'-'.$proInfo['id'].'-4-'.$records['id'];
			$rs = model('member_finance_log')->where(array('only'=>$sign))->find();
			if(!$rs){
			   	action_finance_log($this->userid,-$total,'money',$msg,$sign,array('goods_id'=>$proInfo['id']));
			    // 将追加的总保证金追加到商品总保证金中
				model('product_'.$proInfo['mod'])->where(array('id'=>$proInfo['id']))->setInc('goods_deposit',$records['com_total_fee']);
				// 将保证金写入商家保证金字段
				model('member_merchant')->where(array('userid'=>$this->userid))->setInc('frozen_deposit',$total);
				// 将追加表的状态更新为已支付
				model('product_complement')->where(array('id' => $records['id']))->setField('status',2);
			    //加入产品日志
			    product_log($proInfo['id'],'1','0',$this->userid,'交付追加库存担保金');
			    $this->success('交付成功,请等待管理员审核');
			}else{
			    $this->error('交付失败，一天只能追加一次');
			}
		}else{
			include template('merchant/push_bailbond');
		}
	}


	public function push_check(){
		if ($this->userinfo['modelid'] != 2) $this->error('请登录商家账号',U('member/index/login'));
		include template('merchant/push_check');
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


	public  function add_time($id = ''){
		$id = I('id');
		if ($id < 1)	$this->error('该商品不存在！');
		$factory = new \Product\Factory\product($id);
		$proInfo = $factory->product_info;
		if ($this->userid != $proInfo['company_id']) $this->error('您无权追加此商品！');
		if ($proInfo['status'] != 2) $this->error('必须是正在结算中的商品才能追加时间！');

		if (IS_POST) {
			$param = I('param.');
			$id = $param['id'];
			$day = $param['com_day'];
			$factorys = new \Product\Factory\product($id);
			$proInfos = $factorys->product_info;
			if ($proInfos['status'] != 2) $this->error('必须是正在结算中的商品才能追加时间！');
			if (!is_numeric($day)) {
				$this->error('请输入追加天数');
			}

			$new_day = NOW_TIME + ($day*86400);
			if (NOW_TIME > $new_day) {
                $this->error('追加时间已过，温馨提示只能在活动结束7天内进行延期！');
			}
			$result = model('product')->where(array('id' => $id))->setField('end_time',$new_day);
			$now_day = $proInfos['activity_days']+$day;
		    $results = model('product_'.$proInfos['mod'])->where(array('id' => $id))->save(array('activity_days'=>$now_day));
			if ($result && $results) {
				
				model('product')->where(array('id' => $id))->setField('status',1);
				

			}



			$this->success('追加成功');

		}
		

		include template('merchant/alert_add_time');




	}

	public function ab_reward($goods_price){
		if ($goods_price <= 0) {
			$this->error('参数错误');
			return false;

		}

		$groups = getcache('merchant_group','member');
		$groupid = $this->db->getFieldByUserid($this->userid,'groupid');
		$config = unserialize($groups[$groupid]['config']);
	    $trial = $config['trial'];
		if ($trial['a_b_cost'] == 0 ) {
			$service = '0.00';
		}else{
			
		    $price_type = $trial['a_b'];
			array_multisort($price_type,'min','SORT_ASC');
			if(!empty($price_type) && is_array($price_type)){
				$service = 0;//缴纳的佣金
				$price_type = array_values($price_type);

				foreach ($price_type as $k=>$v){
					if($price_type[$k+1]['min'] >0 ){
						
						if( $goods_price >= $v['min'] && $goods_price < $price_type[$k+1]['min'] ){
						 $service = sprintf('%.2f',$v['a_b_trial']);
						 break;
						}


					}else{
						if( $goods_price >= $v['min']){
						 $service = sprintf('%.2f',$v['a_b_trial']);
						 break;
						}

					}
					
				}
			}
		}
		

		return $service;

	}

	public function red_reward($goods_price){
		if ($goods_price <= 0) {
			$this->error('参数错误');
			return false;

		}
		$groups = getcache('merchant_group','member');
		$groupid = $this->db->getFieldByUserid($this->userid,'groupid');
		$config = unserialize($groups[$groupid]['config']);
	    $trial = $config['trial'];
		if ($trial['red_cost'] == 0 ) {
			$service = '0.00';
		}else{
			
		    $price_type = $trial['red'];
			array_multisort($price_type,'min','SORT_ASC');
			if(!empty($price_type) && is_array($price_type)){
				$service = 0;//缴纳的佣金
				
				$price_type = array_values($price_type);
				

				foreach ($price_type as $k=>$v){
					if($price_type[$k+1]['min'] >0 ){
						
						if($goods_price >= $v['min'] && $goods_price < $price_type[$k+1]['min'] ){
						  $service = sprintf('%.2f',$v['red_trial']);
						 break;
						}


					}else{
						if($goods_price >= $v['min']){
							$service = sprintf('%.2f',$v['red_trial']);
						 break;
						}

					}
					
				}
			}
		}
		
		return $service;

	}




}