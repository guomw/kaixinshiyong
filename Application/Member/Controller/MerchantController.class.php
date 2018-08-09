<?php
namespace Member\Controller;
use Member\Controller\InitController;
if (!defined('MODULE_CACHE')) define('MODULE_CACHE', DATA_PATH.'caches_model/');
class MerchantController extends InitController{
	public function _initialize(){
		parent::_initialize();
		$this->models = getcache('model','commons');
		//查出该会员的modelid
		$this->modelid = $this->db->getFieldByUserid($this->userid,'modelid');
		$this->groupid =  $this->userinfo['groupid'];
		if($this->modelid != 2){
			$this->error('您不是商家会员，无权访问该页面');
		}
	}
	/*成为vip*/
	public function becomevip(){
		$groups = getcache('merchant_group','member');
		$name = $groups[$this->groupid]['name'];//商家级别

		$tablename = $this->models[$this->modelid]['tablename'];
		$modelinfo = M($tablename)->where(array('userid' => $this->userid))->find();
		extract($modelinfo);
		//到期时间
		$grouptype =  $this->userinfo['grouptype'];
		$starttime = $this->userinfo['grouptime'];
        $group_endtime = $this->userinfo['group_endtime'];
        $endtime = $group_endtime;

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
          $levels_3['trial'] = $config3['trial'];
          $levels_3['postal'] = $config3['postal'];
       }

		include template('merchant/becomevip');
	}

		/*查看该商家是否有足够的余额支付*/
		public function check_money(){

			$type = (int) I('type');
	        if($type == 2){
	            $level_2 = model('merchant_group')->where(array('groupid' => 2))->getField('pricetype');
	            $level_2 =explode(",",$level_2);
	            $money=$level_2[1];
	            $status =$level_2[0];
	        }elseif($type == 3){
	        	$level_2 = model('merchant_group')->where(array('groupid' => 3))->getField('pricetype');
	        	$level_2 =explode(",",$level_2);
	        	$money=$level_2[1];
	        	$status =$level_2[0];

	        }else{
	             $this->error('参数错误');

	        }


			if($this->userid < 1){
				$this->error('参数错误');
			}


			if ($type < 1) {
				$this->error('请勿非法访问');
			}
			if ($money < 1) {
				$this->error('请勿非法访问');
			}
			$moneys = $this->db->getFieldByUserid($this->userid,'money');
			
			//判断商家是否可以再次点击
			if($this->groupid != $type && $this->groupid != 1){
	            $groups = getcache('merchant_group','member');
	            $name = $groups[$this->groupid]['name'];//商家级别
	            $name2 = $groups[$type]['name'];//商家级别
				$this->error('您已经是'.$name.'了,未到期之前不能成为'.$name2);
			}

			//判断有没有足够的余额
			if($moneys < $money){//余额不足
				$this->error('您的余额不足，请先充值。马上充值？',U('Pay/Index/pay'));
			}else{

				if($type == 3){
					$msg = "成为皇冠商家支付费用";
					$str = "恭喜，您升级/续费皇冠商家成功！";
				}
				if($type == 2){
					$msg = "成为钻石商家支付费用";
					$str = "恭喜，您升级/续费钻石商家成功！";
				}

	            if($status == 3){
	                if($this->userinfo['group_endtime'] != 0){
	                    $group_endtime = strtotime("+1 year ",$this->userinfo['group_endtime']);
	                }else{
	                    $group_endtime = strtotime("+1 year ",NOW_TIME);
	                }
	            }else if($status == 2){
	                if($this->userinfo['group_endtime'] != 0){
	                    $group_endtime = strtotime("+3 month ",$this->userinfo['group_endtime']);
	                }else{
	                    $group_endtime = strtotime("+3 month ",NOW_TIME);
	                }
	            }else{
	                if($this->userinfo['group_endtime'] != 0){
	                    $group_endtime = strtotime("+1 month ",$this->userinfo['group_endtime']);
	                }else{
	                    $group_endtime = strtotime("+1 month ",NOW_TIME);
	                }
	            }

				//扣除商家的余额并且将此记录存入数据表
				$sign = '5-2-'.$this->userid.'-'.$money.'-'.dgmdate($group_endtime,'Y-m-d H:i:s');
				$sqlmap = array();
				$sqlmap['only'] = $sign;
				$rs = model('member_finance_log')->where($sqlmap)->find();
				if(!$rs){

					if (C('sso_is_open') == 1) {
						  unset($infos);
						  $infos = array();
						  $infos['userid'] = $info['userid'];
				          $infos['type'] = 'money';
				          $infos['type_id'] = ($type == 2)?'1601':'1602';
				          $infos['num'] = -$money;
				          $infos['end_time'] = $group_endtime;

				          $ret = _ps_send('money',$infos);
				          $data = php_data($ret);
				          if ($data['status'] == 1) {
				          	action_finance_log($this->userid,-$money,'money',$msg, $sign, array());
				          }
					}else{
						action_finance_log($this->userid,-$money,'money',$msg, $sign, array());

					}



				}
				
	            $info = array();
	            $info['num'] = array('exp','num + 1');
	            $info['viptime'] = NOW_TIME;
	            $info['vipmoney'] = array('exp','vipmoney +'.$money);
	            model('member')->where(array('userid'=>$this->userid))->save($info);
	            
	            $result = $this->db->where(array('userid'=>$this->userid))->save(array('groupid'=>$type,'grouptime'=>NOW_TIME,'group_endtime'=>$group_endtime));
				//到期后自动转为普通商家
				if($result){
                      //如果是招商专员名下商家 写入招商log日志
					$companys = model('member')->field('agent_id')->find($this->userid);
		            if ($companys['agent_id'] > 0) {
		                $agent = model('admin')->where(array('userid'=>$companys['agent_id']))->field('fee_type,company_config,roleid')->find();
		                $group_fee = string2array($agent['company_config']);
		                if ($agent && $agent['roleid'] == 6) {
		                	if ($type == 2) {
		                		$name = '钻石商家';
		                        $money =sprintf("%.2f",$group_fee['service_zuan_fee']);
		                        $_levels = 3;

		                	}else{
		                		$money =sprintf("%.2f",$group_fee['service_huang_fee']);
		                		$name="皇冠商家";
		                		$_levels = 4;

		                	}
		                    
		                    $msg = "商家(id:".$this->userid."),升级/续费".$name.")，提成".$money."元";
		                    $infos = array();
		                    $infos['time'] = NOW_TIME;
		                    $infos['type'] = $_levels;
		                    $infos['money'] = $money;
		                    $infos['agent_id']= $companys['agent_id'];
		                    $infos['body'] = $msg;
		                    model('company_log')->add($infos);
		                }
		            }
		            $arr = array('userid'=>$this->userid);
					runhook('member_login_success_change_membergroup',$arr);
					$this->success($str,U('becomevip'));
				}else{
					$this->error('操作失败');
				}
			}
	}

	/*是否为皇冠商家*/
	public function isvip(){
		$groupid = $this->db->getFieldByUserid($this->userid,'groupid');
		if($groupid == 3){
			$this->error("您是已经皇冠商家了");
		}else{
			$this->success('ok');
		}
	}
	/*是否为砖石商家*/
	public function ismond(){
		if($this->groupid == 2){
			$this->error("您是已经钻石商家了");
		}else{
			$this->success('ok');
		}
	}
	
	/*商家完善资料*/
	public function complete(){
		$tableName = $this->models[$this->modelid]['tablename'];
		//查出当前会员信息
		if($this->userid < 0 ){$this->error('参数错误');}
		//主表信息
		$member_info = $this->db->where(array('userid'=>$this->userid))->find();
		//附表信息
		$member_infos = M($tableName)->where(array('userid'=>$this->userid))->find();
		if(empty($member_info)){
			$memberinfo = $member_infos;
		}else if(empty($member_infos)){
			$memberinfo = $member_info;
		}else{
			$memberinfo = array_merge($member_info,$member_infos);
		}
		extract($memberinfo);
		/*店铺信息*/
		$category = model('product_category')->where(array('parentid'=>0))->limit($limit)->order("listorder ASC")->select();
		$storeinfos = model('merchant_store')->where(array('userid'=>$this->userid))->order('id desc')->select();
		foreach ($storeinfos as $key => $value) {
			$storeinfos[$key]['type'] = model('product_category')->where(array('catid'=>$value['industry']))->getField('catname');
		}
		$ids = I('id');
		if ($ids) {
			$store = model('merchant_store')->find($ids);

		}

		$num =  model('merchant_group')->where(array('groupid'=>$member_info['groupid']))->getField('store_num');


		/*店铺信息*/                 

		//获取会员模型表单
		require MODULE_CACHE.'member_form.class.php';
		$member_form =  new \member_form($this->modelid);
		$forminfos = $forminfos_arr = $member_form->get($memberinfo);
		//万能字段过滤
		foreach($forminfos as $field=>$info) {
			if($info['isomnipotent']) {
				unset($forminfos[$field]);
			} else {
				if($info['formtype']=='omnipotent') {
					foreach($forminfos_arr as $_fm=>$_fm_value) {
						if($_fm_value['isomnipotent']) {
							$info['form'] = str_replace('{'.$_fm.'}',$_fm_value['form'], $info['form']);
						}
					}
					$forminfos[$field]['form'] = $info['form'];
				}
			}
		}
		
		if(IS_POST){
			$info = I('post.');	
			$info_group = $info['info'];
			$info['nickname'] = $info['info']['contact_name'];
			if($tableName){
				//判断是否有该用户
				$count = model($tableName)->where(array('userid'=>$this->userid))->count();
				if($count < 1){
					$info_group['userid'] = $this->userid;
					$rs = model($tableName)->add($info_group);
				}else{
					$rs = model($tableName)->where(array('userid'=>$this->userid))->save($info_group);
				}
			}
			$result = $this->db->where(array('userid'=>$this->userid))->save($info);
			if($result === false && $rs === false){
				$this->error('操作失败');
			}
			$this->success('操作成功');
		}else{
			$form = new \Common\Library\form();
			include template('merchant/complete');
		}
	}


	public function  store_info(){
		if (IS_POST) {
			$info = I('post.');	
			$info_group = $info['info'];
			$info_group['inputtime'] = NOW_TIME;
			$info['nickname'] = $info['info']['contact_name'];
			$groupid = model('member')->where(array('userid'=>$this->userid))->getField('groupid');
			if(!$info_group['id']){
					$count = model('merchant_store')->where(array('store_name'=>$info_group['store_name']))->count();
					$add_count = model('merchant_store')->where(array('store_address'=>$info_group['store_address']))->count();
					$total_count = model('merchant_store')->where(array('userid'=>$this->userid))->count();
					$num =  model('merchant_group')->where(array('groupid'=>$groupid))->getField('store_num');
					$name =  model('merchant_group')->where(array('groupid'=>$groupid))->getField('name');


					if ($total_count >= $num) {
							$this->error('您目前是'.$name.',只可以绑定'.$num.'个店铺！');
					}

					if ($count > 0 || $add_count > 0) {
						$this->error('该店铺已绑定，请勿重复绑定！');
					}

						$info_group['userid'] = $this->userid;
						$rs = model('merchant_store')->add($info_group);
				}else{
					$count = model('merchant_store')->where(array('store_name'=>$info_group['store_name'],'userid'=>array('NEQ',$this->userid)))->count();
					$add_count = model('merchant_store')->where(array('store_address'=>$info_group['store_address'],'userid'=>array('NEQ',$this->userid)))->count();

					if ($count > 0 || $add_count > 0) {
						$this->error('该店铺已绑定，请勿重复绑定！');
					}

						$rs = model('merchant_store')->where(array('userid'=>$this->userid,'id'=>$info_group['id']))->save($info_group);
				}
				
				$result = $this->db->where(array('userid'=>$this->userid))->save($info);
				if($result === false && $rs === false){
					$this->error('操作失败');
				}
			$this->success('操作成功');
		}else{
			$this->error('请勿非法访问');
		}
	}

	public function del(){
		$id = I('id');
		if(!$id) $this->error('请勿非法访问');
		$result = model('merchant_store')->where(array('id'=>$id,'userid'=>$this->userid))->delete();
		if ($result) {
			$this->success('操作成功');
		}else{
			$this->error('服务器繁忙，请稍后再试');
		}
	}

	public function set_default(){
		$id = I('id');
		if(!$id) $this->error('请勿非法访问');
		model('merchant_store')->where(array('userid'=>$this->userid))->setField('is_default',0);
		/* 设置当前账号为默认账号 */
		$result = model('merchant_store')->where(array('id'=>$id))->setField('is_default',1);
		if ($result) {
			$this->success('设置成功');
		}else{
			$this->error('服务器繁忙，请稍后再试');
		}

	}
	
}