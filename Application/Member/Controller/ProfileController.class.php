<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Member\Controller;
use \Member\Controller\InitController;
if (!defined('MODULE_CACHE')) define('MODULE_CACHE', DATA_PATH.'caches_model/');
class ProfileController extends InitController {
	public function _initialize() {
		parent::_initialize();
		$this->favorite_db = D('MemberFavorite');
		$this->product_db = D('Product/Product');
		$this->activity_status = C('ACTIVITY_STATUS');
        $this->groupid =  $this->userinfo['groupid'];
	}

	public function _empty() {
		$this->index();
	}

	/* 基础资料修改 */
	public function index() {
		$models = getcache('model', 'commons');
		$modelid = $this->userinfo['modelid'];
		$tablename = $models[$modelid]['tablename'];
		$modelinfo = M($tablename)->where(array('userid' => $this->userid))->find();
		$alipay_name = model('member_detail')->where(array('userid'=>$this->userid))->getField("alipay_name");
		extract($modelinfo);
		$groupid = $this->userinfo['groupid'];
		$groups = ($modelid == 1) ? getcache('member_group','member') : getcache('merchant_group','member');
		$groupname = $groups[$groupid]['name'];
		//用户地址
		$address = string2array($this->userinfo['receives']);
		if ($modelid == 1) {
			/*已审核试用资格，待填写订单号*/
			$trial_pass_count = model('order')->where(array('status'=>2,'buyer_id'=>$this->userid,'order_sn'=>'','act_mod'=>'trial'))->count();
			/*已下单待交试用报告*/
		    $write_report_count = model('order')->where(array('status'=>8,'buyer_id'=>$this->userid,'order_sn'=>array('NEQ',''),'act_mod'=>'trial'))->count();
		    /*待修改订单号/报告*/
		   $update_count = model('order')->where(array('status'=>4,'buyer_id'=>$this->userid,'order_sn'=>array('NEQ',''),'act_mod'=>'trial'))->count();

		   /*申诉中*/
		   $appeal_count = model('order')->where(array('status'=>6,'buyer_id'=>$this->userid,'order_sn'=>array('NEQ',''),'act_mod'=>'trial'))->count();





		}
		if($modelid == 2){
            /*待审核试用资格*/
            $z_zg = model('order')->where(array('status' =>1,'seller_id' =>$this->userid,'act_mod' =>'trial'))->count();

            /*待审核订单号*/
            $z_order = model('order')->where(array('status' =>2,'seller_id' =>$this->userid,'act_mod' =>'trial'))->count();

            /*待审核试用报告*/
            $z_bg = model('order')->where(array('status' =>3,'act_mod' =>'trial','seller_id' =>$this->userid))->count();

            /*闪电试用 待审核订单*/
            $s_order = model('order')->where(array('status' =>3,'act_mod' =>'commission','seller_id' =>$this->userid,'order_sn' =>array('GT',0)))->count();

            /*闪电试用 待返款订单*/
            $s_pay_order = model('order')->where(array('status' =>5,'act_mod' =>'commission','seller_id' =>$this->userid,'order_sn' =>array('GT',0)))->count();


			//待审核未付款活动总条数
			$check_count = product_stat(-3,$this->userid,'');
			$check_rebate_count = product_stat(-3,$this->userid,'rebate');
			$check_trial_count = product_stat(-3,$this->userid,'trial');
			//待审核已付款活动总条数
			$check_count_pay = product_stat(-2,$this->userid,'');
			$check_rebate_count_pay = product_stat(-2,$this->userid,'rebate');
			$check_trial_count_pay = product_stat(-2,$this->userid,'trial');
			
			//已通过活动总条数
			$pass_count = product_stat(-1,$this->userid,'');
			$rebate_pass_count = product_stat(-1,$this->userid,'rebate');
			$trial_pass_count = product_stat(-1,$this->userid,'trial');
			//进行中的活动 已上架
			$going_count = product_stat(1,$this->userid,'');
			$rebate_going_count = product_stat(1,$this->userid,'rebate');
			$trial_going_count = product_stat(1,$this->userid,'trial');
			//未通过的活动
			$uncount = product_stat(0,$this->userid,'');
			$rebate_uncount = product_stat(0,$this->userid,'rebate');
			$trial_uncount = product_stat(0,$this->userid,'trial');
			//结算中的活动 已下架
			$colse_count = product_stat(2,$this->userid,'');
			$colse_count_rebate = product_stat(2,$this->userid,'rebate');
			$colse_count_trial = product_stat(2,$this->userid,'trial');
			//待审核的订单
			$order_check_count = model('order')->where(array('status'=>3,'seller_id'=>$this->userid))->count();
			//申诉
			$appeal_count = model('appeal')->where(array('seller_id'=>$this->userid))->count();
			//客服专员
			$agent_id = model('member')->getFieldByUserid($this->userid,'agent_id');
			$admin_info  = model('admin')->getByUserid($agent_id);
		}

		//收藏商品
		$sqlmap['userid'] = $this->userid;
        $collect_list = model('member_favorite')->where($sqlmap)->order('id DESC,dateline DESC')->limit(5)->select();
        foreach ($collect_list as $k => $v) {
        		$goods_list = model('product')->where('id='.$v['goods_id'])->select() ;
        		foreach ($goods_list as $key => $value) {
        			if ($v['goods_id'] == $value['id']) {
        				$collect_list[$k]['title'] = $value['title'];
			        	$collect_list[$k]['thumb'] = $value['thumb'];
			        	$collect_list[$k]['collect_id'] = $value['id'];
			            $collect_list[$k]['status'] = $this->activity_status[$value['status']];
        			}
      		  }
        }
        //认证通过
        $attestas = model('member_attesta')->where(array('userid'=>$this->userid))->select();
        foreach ($attestas as $_k=>$_v) {
        	$arr[$_v['type']] = $_v;
        }
        $SEO['title'] = '个人中心-'.C('WEBNAME');
        $SEO['keyword'] = '个人中心-'.C('WEBNAME');
        $SEO['title'] = '个人中心-'.C('WEBNAME');
        $tpl = ($modelid == 1) ? 'member_index' : 'merchant_index';
		include template($tpl);
	}

	/*修改头像*/
	public function avatar(){
		if(!empty($_FILES)){
			$suid = sprintf("%09d", $this->userid);
			$dir1 = substr($suid, 0, 3);
			$dir2 = substr($suid, 3, 2);
			$dir3 = substr($suid, 5, 2);
			$upload = new \Think\Upload();// 实例化上传类
			$upload->maxSize  =     3145728 ;
			$upload->exts     =     array('jpg', 'gif', 'png');
			$upload->rootPath = './uploadfile/avatar/';
			$upload->savePath = $dir1.'/'.$dir2.'/'.$dir3.'/';
			$upload->replace  = TRUE;
			$upload->saveName = $this->userid.'_avatar';
			$upload->autoSub = FALSE;
			$upload->saveExt  = 'jpg';
			$result = $upload->upload($_FILES);
			$modelid = $this->db->getFieldByUserid($this->userid,'modelid');
			if($result){
				if($modelid == 1){
					$this->redirect(U('Member/Profile/index'));
				}else{
					$this->redirect(U('Member/MerchantMember/index'));
				}
			}else{
				$this->error('修改头像失败');
			}
		}
	}

	public function infomation(){
		$userinfo = $this->db->find($this->userid);

		$region = model('linkage')->where(array('parentid'=>0,'keyid'=>1))->select();
		//查询所在地
		if($userinfo['address']){
			$address = string2array($userinfo['address']);
		}
		extract($address);
		//收货地址
		if($userinfo['receives']){
			$receives = string2array($userinfo['receives']);
		}
		extract($receives);
		extract($userinfo);
		$models = getcache('model','commons');
		$modelid = $this->userinfo['modelid'];
		$tablename = $models[$modelid]['tablename'];
		$member_infos = D($tablename)->find($this->userid);
		if($member_infos['birthday']){
			$birthday = string2array($member_infos['birthday']);
		}
		$year = $birthday['year'];
		$month = $birthday['month'];
		$day = $birthday['day'];
		extract($member_infos);
		if (IS_POST) {
			$info = I('post.');	
			$addr = array();
			$addr['provice'] = $info['province'];
			$addr['city'] = $info['city'];
			$addr['area'] = $info['area'];
			$map = array();
			$map['year'] = $info['year'];
			$map['month'] = $info['month'];
			$map['day'] = $info['day'];
			$sqlmap = array();
			$map_info = array();
			$sqlmap['address'] = array2string($addr);//所在地
			$sqlmap['nickname'] = $info['nickname'];
			$sqlmap['qq'] = $info['qq'];
			$sqlmap['receives'] = array2string($info['receives']);
			$map_info['birthday'] = dstripslashes(array2string($map));//出生日期
			$map_info['sex'] = $info['sex'];
			$map_info['userid'] = $this->userid;

			//把上传的文件移入文件
			$avatar = $info['avatar'];
			//需移入的文件夹目录
			$suid = sprintf("%09d", $this->userid);
			$dir1 = substr($suid, 0, 3);
			$dir2 = substr($suid, 3, 2);
			$dir3 = substr($suid, 5, 2);

			$root_url =  $_SERVER['DOCUMENT_ROOT'];

			$rootDir = $root_url.'/uploadfile/avatar/';
			$userDir = $dir1.'/'.$dir2.'/'.$dir3.'/';
			//头像新文件名
			$filename = $rootDir.$userDir.$this->userid.'_avatar.jpg';

			if(!is_dir($rootDir.$userDir)){
			        mkdir($rootDir.$userDir);
			        chmod($rootDir.$userDir,0777);
			}
			$upload = copy($root_url.$avatar,$filename);
			$result = $this->db->where(array('userid'=>$this->userid))->save($sqlmap);
			if($tablename){
				//查询是否有userid
				$count = model($tablename)->where(array('userid'=>$this->userid))->count();
				if($count < 1){
					$rs = model($tablename)->add($map_info);
				}else{
					$rs = model($tablename)->where(array('userid'=>$this->userid))->save($map_info);
				}
			}
			
			if( false !== $result ){
				$this->success('操作成功',U('infomation'));

			}else{
				$this->error('修改失败');
			}
		}else{
            $SEO['title'] = '用户设置-'.C('WEBNAME');
            $SEO['keyword'] = '用户设置-'.C('WEBNAME');
            $SEO['description'] = '用户设置-'.C('WEBNAME');

            //查看是否绑定店铺、品牌
            $infos = model('member_attesta')->where(array('userid'=>$this->userid))->select();
            $arr = array();
            foreach ($infos as $key=>$val) {
                $arr[$val['type']] = $val;
            }
			//是否绑定亚马逊账号
			$is_bind = model('member_bind')->where(array('userid'=>$this->userid))->count();
			include template('buyer/infomation');
		}
	}
	
	/* 模型字段资料 */
	public function full() {
		$models = getcache('model', 'commons');
		$modelid = $this->userinfo['modelid'];
		$tablename = $models[$modelid]['tablename'];
		$modelinfo = M($tablename)->where(array('userid' => $this->userid))->find();
		if (IS_POST) {
			$info = $_POST['info'];
	        require MODULE_CACHE.'member_input.class.php';
	        $member_input = new \member_input($modelid);
	        $info = $member_input->get($info);
	        if (!$info) {
	        	$this->error($member_input->getError());
	        }
	        $result = M($tablename)->where(array('userid' => $this->userid))->save($info);
	        $this->success('操作成功');
		} else {
			require MODULE_CACHE.'member_form.class.php';
			$member_form = new \member_form($modelid);
			$forminfos = $member_form->get($modelinfo);
			include template('profile');
		}		
	}

	/* 个人和企业通用修改密码 */
	public function pwd() {
		$modelid = $this->userinfo['modelid'];
		if(IS_POST){
			$info = I('post.');
			$oldpass = md5(md5($info['oldpass'].$this->userinfo['encrypt']));
			$rs = $this->db->where(array('userid'=>$this->userid))->find();
			if($oldpass!=$rs['password']){
				$this->error('原密码错误');
			}
			if($rs){
				if($info['password'] != $info['pwdconfirm']){
					$this->error('两次密码不一致');
				}
				$info['password'] = md5(md5($info['password'].$this->userinfo['encrypt']));
				$result = $this->db->where(array('userid'=>$this->userid))->save($info);
				if(!$result){
					$this->error('修改密码失败');
				}
				$this->success('修改成功',U('Member/index/login'));
			}else{
				$this->error('该用户不存在');
			}
		}else{
			$SEO = seo('','密码修改');
			$tpl = ($modelid == 1) ? 'buyer/password' : 'merchant/password';
			include template($tpl);
		}
	}
	
	/*成为vip*/
	public function becomevip(){
        $groups = getcache('member_group','member');
        $level = $groups[2];
        $tablename = $this->models[$this->modelid]['tablename'];
        $modelinfo = M($tablename)->where(array('userid' => $this->userid))->find();
        extract($modelinfo);
        //到期时间
        $grouptype =  $this->userinfo['grouptype'];
        $starttime = $this->userinfo['grouptime'];
        $group_endtime = $this->userinfo['group_endtime'];

        $endtime = $group_endtime;

		include template('buyer/becomevip');
	}

    /*查看该商家是否有足够的余额支付*/
    public function check_money(){
        if($this->userid < 1){
            $this->error('参数错误');
        }
        $money = (int) I('money');
        $status = substr(I('money'),strpos(I('money'),'/')+1);//年季月
        $type = (int) I('type');
        if ($type < 1) {
        	 $this->error('请勿非法访问');
        }

        if ($money < 1) {
        	$this->error('请勿非法访问');
        }
        $moneys = $this->db->getFieldByUserid($this->userid,'money');

        //判断用户是否可以再次点击
        if($this->groupid != $type && $this->groupid != 1){
            $groups = getcache('member_group','member');
            $name = $groups[$this->groupid]['name'];//商家级别
            $name2 = $groups[$type]['name'];//商家级别
            $this->error('您已经是'.$name.'了,未到期之前不能成为'.$name2);
        }

        //判断有没有足够的余额
        if($moneys < $money){//余额不足
            $this->error('您的余额不足，请先充值。马上充值？',U('Pay/Index/pay',array('money'=>$money)));
        }else{
            if($type == 2){
                $msg = "成为Vip用户支付费用";
                $str = "恭喜，您购买VIP用户成功！";
            }

            if($status == '年'){
                if($this->userinfo['group_endtime'] != 0){
                    $group_endtime = strtotime("+1 year ",$this->userinfo['group_endtime']);
                }else{
                    $group_endtime = strtotime("+1 year ",NOW_TIME);
                }
            }else if($status == '季'){
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

            //扣除会员的余额并且将此记录存入数据表
            $sign = '5-1-'.$this->userid.'-'.$money.'-'.dgmdate($group_endtime,'Y-m-d H:i:s');
            $rs = model('member_finance_log')->where(array('only'=>$sign))->find();
            if(!$rs){
            	if (C('sso_is_open') == 1) {
						  unset($infos);
						  $infos = array();
						  $infos['userid'] = $this->userid;
				          $infos['type'] = 'money';
				          $infos['type_id'] = '1603';
				          $infos['num'] = -$money;
				          $infos['end_time'] = $group_endtime;

				          $ret = _ps_send('money',$infos);
				          $data = php_data($ret);
				          if ($data['status'] == 1) {
               					    action_finance_log($this->userid,-$money,'money',$msg,$sign,array());
				          }
					}else{
                					action_finance_log($this->userid,-$money,'money',$msg,$sign,array());

					}


                action_finance_log($this->userid,-$money,'money',$msg,$sign,array());
                $result = $this->db->where(array('userid'=>$this->userid))->save(array('groupid'=>$type,'grouptime'=>NOW_TIME,'group_endtime'=>$group_endtime));
                //到期后自动转为普通会员
                if($result){
                    $arr = array('userid'=>$this->userid);
                    runhook('member_login_success_change_membergroup',$arr);
                    $this->success($str);
                }else{
                    $this->error('操作失败');
                }
            }else{
                $this->error('操作失败，重复操作');
            }
        }
    }

	 /* 获取省市 [Adalways试用系统] */
    public function get_area(){
        $id = I('id');
        $area = model('linkage')->where(array('parentid'=>$id,'keyid'=>1))->select();
        echo json_encode($area);
    }

    public function check_exist_pwd(){
    	$pwd = $_GET['oldpass'];
		if (!$pwd) return false;
		$result = $this->db->find($this->userid);
		$pwds = md5(md5($pwd.$result['encrypt']));
		if ($pwds == $result['password']) {
			 $this->success('输入正确');
		}else {
            $this->error('密码输入有误');
        }        
    }

    public function update_avatar(){
        //把上传的文件移入文件
        $avatar = I('avatar');
        $suid = sprintf("%09d", $this->userid);
        $dir1 = substr($suid, 0, 3);
        $dir2 = substr($suid, 3, 2);
        $dir3 = substr($suid, 5, 2);
        $rootDir = SITE_PATH.'/uploadfile/avatar/';
        $userDir = $dir1.'/'.$dir2.'/'.$dir3.'/';

        //头像新文件名
        $filename = $rootDir.$userDir.$this->userid.'_avatar.jpg';
        // 调取150*150的缩略图
        $list = explode('.', $avatar);
        $avatar = $list['0'].'_150.'.$list['1'];
        if (file_exists(SITE_PATH.$avatar)) {
            $result = moveFile(SITE_PATH.$avatar,$filename);
        }
    }



    public function profile(){
    	$userinfo = $this->db->find($this->userid);
    	$region = model('linkage')->where(array('parentid'=>0,'keyid'=>1))->select();
    	//查询所在地
		if($userinfo['address']){
			$address = string2array($userinfo['address']);
		}
		extract($address);
		//收货地址
		if($userinfo['receives']){
			$receives = string2array($userinfo['receives']);
		}
		extract($receives);
		extract($userinfo);
		$models = getcache('model','commons');
		$modelid = $this->userinfo['modelid'];
		$tablename = $models[$modelid]['tablename'];
		$member_infos = D($tablename)->find($this->userid);
		if($member_infos['birthday']){
			$birthday = string2array($member_infos['birthday']);
		}
		$year = $birthday['year'];
		$month = $birthday['month'];
		$day = $birthday['day'];
		extract($member_infos);
		$SEO = seo(0,'修改个人资料');
    	include template('buyer/profile');
    }


}