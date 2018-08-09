<?php
namespace Member\Controller;
use \Common\Controller\SystemController;
/* 后台活动管理 */
class EnterActivityController extends SystemController{
	public function _initialize(){
		parent::_initialize();
		$this->db = model('activity_set');
		$this->pagecurr = max(1, I('page', 0, 'intval'));
		$this->pagesize = 10;
    }

    public function index(){
        $form = new \Common\Library\form();
        $act_name =  C('ACTIVITY_LISTS');
        $actvity = $this->db->field('key,value,activity_type')->select();
        $setting = $this->db->where(array('activity_type' => 'trial'))->getField('key,value');
        $arr = array();
        foreach($actvity as $k=>$v){
        	$arr[$v['activity_type']][] = $v;
        }

       foreach ($arr as $key=>$val){
       		foreach ($val as $_k=>$_v){
       			 $activityarr[$key][$_v['key']] = $_v['value'];
       		}
       }




       $level_1 = model('merchant_group')->limit(0,1)->select();
       $levels_1 = array();
       foreach ($level_1 as $key => $value) {
          $levels_1['name'] = $value['name'];
          $levels_1['image'] = $value['image'];
          $levels_1['pricetype'] =explode(',', $value['pricetype']);
          $levels_1['ordertype'] = explode(',', $value['ordertype']);
          $config = unserialize($value['config']);
          $levels_1['rebate'] = $config['rebate'];
          $levels_1['trial'] = $config['trial'];
          $levels_1['commission'] = $config['commission'];

          $levels_1['postal'] = $config['postal'];
       }

     

       $level_2 = model('merchant_group')->limit(1,1)->select();
       $levels_2 = array();
       foreach ($level_2 as $key => $value) {
          $levels_2['name'] = $value['name'];
          $levels_2['image'] = $value['image'];
          $levels_2['pricetype'] =explode(',', $value['pricetype']);
          $levels_2['ordertype'] = explode(',', $value['ordertype']);
          $config2 = unserialize($value['config']);
          $levels_2['rebate'] = $config2['rebate'];
          $levels_2['trial'] = $config2['trial'];
         $levels_2['commission'] = $config2['commission'];

          $levels_2['postal'] = $config2['postal'];
       }

       $level_3 = model('merchant_group')->limit(2,2)->select();
       $levels_3 = array();
       foreach ($level_3 as $key => $value) {
          $levels_3['name'] = $value['name'];
          $levels_3['image'] = $value['image'];
          $levels_3['pricetype'] =explode(',', $value['pricetype']);
          $levels_3['ordertype'] = explode(',', $value['ordertype']);
          $config3 = unserialize($value['config']);
          $levels_3['rebate'] = $config3['rebate'];
          $levels_3['commission'] = $config3['commission'];

          $levels_3['trial'] = $config3['trial'];
          $levels_3['postal'] = $config3['postal'];
       }

        include template('merchant/enter_activity');
    }

    /*立即报名内容页*/
    public function detail_activity(){
          $mod = I('mod','rebate');
          $userid = cookie('_userid');
          $actvity = $this->db->where(array('activity_type'=>$mod))->getField('key,value');
          //活动名称
          $name =  $actvity[$mod.'_name'];
          $actvitys = $this->db->field('key,value,activity_type')->select();
          $arr = array();
          foreach($actvitys as $k=>$v){
               $arr[$v['activity_type']][] = $v;
          }
          foreach ($arr as $key=>$val){
               foreach ($val as $_k=>$_v){
                   $activityarr[$key][$_v['key']] = $_v['value'];
               }
          }
          //该商家活动报名参与条件
          $seller_join_condition = string2array($actvity['seller_join_condition']);
         include template('merchant/detail_activity');
    }
	/*检测*/
    public function check($type='',$is_url='',$bonus=''){
    	//$type = I('type');
    	$type = (string) $type;
    	$userid = cookie('_userid');
    	$modelid = model('member')->getFieldByUserid($userid,'modelid');
    	if($userid < 1){
    		$this->error('您还没有登录，请先登录。>>去登录',U('Member/Index/login'));
    	}else{
    		//判断是否是商家会员
    		if($modelid == 1){
    			$this->error('您不是商家会员，请先注册商家会员。>>去注册',U('Member/Index/register'));
    		}
    	}
    	//判断商家信息是否填写
    	$models = getcache('model','commons');
    	$tablename = $models[$modelid]['tablename'];
    	$memberinfo = model($tablename)->where(array('userid'=>$userid))->find();
        $store_info = model('merchant_store')->where(array('userid'=>$userid))->find();

    	if($memberinfo['store_name'] == '' && !$store_info ){
    		$this->error('您的商家信息还没有完善，请先完善商家信息。>>去完善',U('Member/Merchant/complete'));
    	}
    	$groups = getcache('merchant_group','member');
    	$groupid = model('member')->getFieldByUserid($userid,'groupid');
    	$configs = $groups[$groupid]['config'];
    	$config = unserialize($configs);
    	$activity = model('activity_set')->where(array('activity_type'=>$type))->getField('key,value');
    	$sell_join_condition = string2array($activity['seller_join_condition']);
    	$types = array();
    	foreach ($sell_join_condition as $k=>$v){
    		$types[]= $k;
    	}
    	switch ($type) {
    		case 'rebate'://购物返利
    			$rebate = $config['rebate'];
    			//判断该活动是否开启
    			$isopen = $activity['rebate_isopen'];
    			if($isopen != 1){
    				$this->error('抱歉，该活动尚未开启');
    			}
    			//判断活动是否结束
    			$starttime = strtotime(C('SELLER_START_TIME'));
    			$endtime = strtotime(C('SELLER_END_TIME'));
    			if(NOW_TIME < $starttime){
    				$this->error('抱歉，该活动还没有开始');
    			}
    			if(NOW_TIME > $endtime){
    				$this->error('抱歉，该活动已结束');
    			}
    			//判断是否允许参与
    			if($rebate['isopen'] != 1){
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

    			/* if(in_array('shop',$types)){//店铺绑定
    				$shop = model('member_attesta')->where(array('userid'=>$userid,'type'=>'shop'))->find();
    				if(!$shop){
    					$this->error('您还没有进行店铺认证，请先认证。>>去验证',U('Member/Attesta/shop'));
    				}
    			}*/
    			if(in_array('brand',$types)){
    				$brand = model('member_attesta')->where(array('userid'=>$userid,'type'=>'brand'))->find();
	    			if(!$brand){
	    				$this->error('您还没有进行品牌认证，请先认证。>>去验证',U('Member/Attesta/brand'));
	    			}else{
	    				if($brand['status'] == 0){
	    					$this->error('您的品牌认证正在审核中，请耐心等待。',U('Member/Attesta/brand'));
	    				}elseif($brand['status'] == -1){
	    					$this->error('您的品牌认证审核失败，请重新上传资料。>>去验证',U('Member/Attesta/brand'));
	    				}
	    			}
    			}
    			if (in_array('email',$types)){//邮箱认证
    				$email_status = model('member')->getFieldByUserid($userid,'email_status');
    				if($email_status != 1){
    					$this->error('您还没有进行邮箱验证，请先验证。>>去验证',U('Member/Attesta/email_attesta'));
    				}
    			}
    			if(in_array('information',$types)){//完善身份信息
    				$infor = model('member_merchant')->where(array('userid'=>$userid))->find();
                    $store = model('merchant_store')->where(array('userid'=>$userid))->find();
    				if(empty($infor['store_name']) && empty($store)){
    					$this->error('您还没有完善资料？>>去完善',U('Member/Merchant/complete'));
    				}
    			}
    			//判断报名次数 apply
    			if($rebate['apply']['radio'] == 1){
    				$times = $rebate['apply']['times'];
    				//查出报名的次数
    				$applytimes = model('product')->where(array('userid'=>$userid,'mod'=>$type))->count();
    				if($applytimes > $times){
    					$this->error('抱歉，您报名的次数已满');
    				}
    			}
    			//判断商品报名间隔时间
    			$merchantLogic = D('Merchant','Logic');
    			$distime = $merchantLogic->distime($type,$groupid,$userid);
    			if(is_numeric($distime) === false && $distime != ''){
    				$this->error('请在'.$distime.'之后再添加');
    			} else if ($distime == ''){
    				if($is_url == 1){
    				    redirect(U('Member/MerchantProduct/add',array('mod'=>$type,'nojinlai'=>1,'bonus'=>$bonus),'',true));
    				}else{
    				    $this->success('验证通过');
    				    break;
    				}
    			}
    			
    			if($is_url == 1){
                    redirect(U('Member/MerchantProduct/add',array('mod'=>$type,'nojinlai'=>1,'bonus'=>$bonus),'',true));
                }else{
                    $this->success('验证通过');
                    break;
                }
    			break;
    		case 'trial'://试用
    			$trial = $config['trial'];
    			//判断活动是否结束
    			//判断该活动是否开启
    			$isopen = $activity['trial_isopen'];
    			if($isopen != 1){
    				$this->error('抱歉，该活动尚未开启');
    			}
    			extract($activity);
    			$seller_start_time = strtotime(C('SELLER_START_TIME'));
    			$seller_end_time = strtotime(C('SELLER_END_TIME'));
    			if(NOW_TIME < $seller_start_time){
    				$this->error('抱歉，该活动还没有开始');
    			}
    			if(NOW_TIME > $seller_end_time){
    				$this->error('抱歉，该活动已结束');
    			}
    			//判断是否允许参与
    			if($trial['isopen'] != 1){
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

    			/*if(in_array('shop',$types)){//店铺绑定
    				$shop = model('member_attesta')->where(array('userid'=>$userid,'type'=>'shop'))->find();
    				if(!$shop){
    					$this->error('您还没有进行店铺认证，请先认证。>>去验证',U('Member/Attesta/shop'));
    				}
    			}*/

/*    			if(in_array('brand',$types)){
    				$brand = model('member_attesta')->where(array('userid'=>$userid,'type'=>'brand'))->find();
    				if(!$brand){
    					$this->error('您还没有进行品牌认证，请先认证。>>去验证',U('Member/Attesta/brand'));
    				}else{
    					if($brand['status'] == 0){
    						$this->error('您的品牌认证正在审核中，请耐心等待。',U('Member/Attesta/brand'));
    					}elseif($brand['status'] == -1){
    						$this->error('您的品牌认证审核失败，请重新上传资料。>>去验证',U('Member/Attesta/brand'));
    					}
    				}
    	
    			}*/
    			if (in_array('email',$types)){//邮箱认证
    				$email_status = model('member')->getFieldByUserid($userid,'email_status');
    				if($email_status != 1){
    					$this->error('您还没有进行邮箱验证，请先验证。>>去验证',U('Member/Attesta/email_attesta'));
    				}
    			}

/*    			if(in_array('information',$types)){//完善身份信息
    				$infor = model('member_merchant')->where(array('userid'=>$userid))->find();
    				$store = model('merchant_store')->where(array('userid'=>$userid))->find();
                     if(empty($infor['store_name']) && empty($store)){
                        $this->error('您还没有完善资料？>>去完善',U('Member/Merchant/complete'));
                    }
    			}*/
    			
    			//判断报名次数
    			if($trial['apply']['radio'] == 1){
    				$times = $trial['apply']['times'];
    				//查出报名的次数
    				$applytimes = model('product')->where(array('userid'=>$userid,'mod'=>$type))->count();
    				if($applytimes > $times){
    					$this->error('抱歉，您报名的次数已满');
    				}
    			}
    			//判断报名间隔时间
    			$merchantLogic = D('Merchant','Logic');
    			$distime = $merchantLogic->distime($type,$groupid,$userid);
    			if(is_numeric($distime) === false && $distime != ''){
    				$this->error('请在'.$distime.'之后再添加');
    			} else if ($distime == ''){
    			    if($is_url == 1){
    			        redirect(U('Member/MerchantProduct/add',array('mod'=>$type,'nojinlai'=>1,'bonus'=>$bonus),'',true));
    			    }else{
    			        $this->success('验证通过');
    			        break;
    			    }
    			} 
    			if($is_url == 1){
                    redirect(U('Member/MerchantProduct/add',array('mod'=>$type,'nojinlai'=>1,'bonus'=>$bonus),'',true));
                }else{
                    $this->success('验证通过');
                    break;
                }
    			break;
    		case 'postal'://9.9包邮
    			$postal = $config['postal'];
    			//判断该活动是否开启
    			$isopen = $activity['postal_isopen'];
    			if($isopen != 1){
    				$this->error('抱歉，该活动尚未开启');
    			}
    			extract($trialinfo);
    			//判断活动是否结束
    			$seller_start_time = strtotime(C('SELLER_START_TIME'));
    			$seller_end_time = strtotime(C('SELLER_END_TIME'));
    			if(NOW_TIME < $seller_start_time){
    				$this->error('抱歉，该活动还没有开始');
    			}
    			if(NOW_TIME > $seller_end_time){
    				$this->error('抱歉，该活动已结束');
    			}
    			//判断是否允许参与
    			if($postal['isopen'] != 1){
    				$this->error('您没有权限参与该活动');
    			}
    			//判断商品报名间隔时间
    			$merchantLogic = D('Merchant','Logic');
    			$distime = $merchantLogic->distime($type,$groupid,$userid);
    			if(is_numeric($distime) === false && $distime != ''){
    				$this->error('请在'.$distime.'之后再添加');
    			} else if ($distime == ''){
    			    if($is_url == 1){
    				    redirect(U('Member/MerchantProduct/add',array('mod'=>$type,'nojinlai'=>1,'bonus'=>$bonus),'',true));
    				}else{
    				    $this->success('验证通过');
    				    break;
    				}
    			}
    			if($is_url == 1){
                    redirect(U('Member/MerchantProduct/add',array('mod'=>$type,'nojinlai'=>1,'bonus'=>$bonus),'',true));
                }else{
                    $this->success('验证通过');
                    break;
                }
    			break;
    		case 'special'://专场活动
    			$special = $config['special'];
    			//判断是否允许参与
    			if($special['isopen'] != 1){
    				$this->error('您没有权限参与该活动');
    			}
    			break;
    		default://品牌折扣
    			break;
    	}
    }
    
    public function check_activity(){
         $type  = I('mod');
        if ($type == "rebate") {
            $setting = $this->db->getField('key,value');
            $check = string2array($setting['seller_join_condition']);
          
            $userinfo = D('Member')->find($this->userid);
            $seller = D('MemberMerchant')->find($this->userid);
            $status = string2array($seller['attestation']);
            if ($check) {
             if ($check['phone'] == 1) {
                if ($userinfo['phone_status'] != 1) {
                    $this->error('请先验证手机号码！');
                }
             }

             if ($check['email'] == 2) {
                if ($userinfo['email_status'] != 1) {
                    $this->error('请先验证邮箱！');
                }
             }

              if ($check['realname'] == 3) {
                if ($status['name_status'] != 1) {
                    $this->error('请先实名认证在申请报名');
                }
             }


             /* if ($check['shop'] == 4) {
                if ($status['shop_status'] != 1) {
                    $this->error('请先店铺认证在申请报名');
                }
             }*/

              if ($check['brand'] == 5) {
               if ($status['brand_status'] != 1) {
                    $this->error('请先品牌认证在申请报名');
                }
             }
             $this->success('申请成功！',U('Member/MerchantProduct/add',array('mod'=>$type)));
          }else{
            $this->success('申请成功！',U('Member/MerchantProduct/add',array('mod'=>$type)));
          }
        }
    }

    public function contact(){
        $city = C('site_contact_city');
        $address = model('linkage')->where(array('linkageid'=>$city))->find();
        include template('merchant/contact');
    }


}