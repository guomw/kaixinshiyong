<?php
namespace Pay\Controller;
use \Common\Controller\BaseController;
class IndexController extends BaseController
{
	public function _initialize(){
		header("Content-type:text/html;charset=utf-8");//保证字符编码为utf8
		parent::_initialize();
		$this->db = model('pay_order');
		$this->member_db = model('member');
        $this->userinfo = is_login();
	}

    /* 发起支付请求 */
    public function index() {
        $payment_handler = new \Pay\Factory\Factory('alipay');
        $payment_handler->set_productinfo($product_info);
        $result = $payment_handler->get_code();
        echo "<a href='".$result."' target='_blank'>go...</a>";
    }

    /* 处理支付宝支付结果 */
    public function callback() {
        $info = I('param.');
        $payment_handler = new \Pay\Factory\Factory('alipay');
        $result = $payment_handler->notify();

        $url =str_replace('api.php', 'index.php',U('Member/Profile/index'));

        if ($result !== FALSE) {
            $recharge_info = $this->db->where(array("trade_sn"=>$result['trade_sn']))->find();
            $this->success('支付成功',$url);
        } else {
            $this->error('支付失败，请联系管理员',$url);
        }
    }

    /* 处理财付通回调结果 */
    public function tenpay_callback() {
        $info = I('param.');
        $payment_handler = new \Pay\Factory\Factory('Tenpay');
        $result = $payment_handler->notify();
        $url =str_replace('Tenpay_api.php', 'index.php',U('Member/Profile/index'));
        $status = $this->db->where(array("trade_sn"=>$info['out_trade_no']))->getField('status');
        if ($status) {
            $this->success('支付成功',$url);
        } else {
            $this->error('支付失败，请联系管理员',$url);
        }
    }

    /* 处理微信支付结果 */
    public function weixin_callback() {
        $payment_handler = new \Pay\Factory\Factory('Weixin');
        $result = $payment_handler->notify();
        if ($result !== FALSE) {
            $recharge_info = $this->db->where(array("trade_sn"=>$result['trade_sn']))->find();
            $this->success('支付成功',U('Member/Profile/index'));
        } else {
            $this->error('支付失败，请联系管理员', U('Product/Index/index'));
        }
    }

   /* 微信扫码支付*/
   public function weixin_pay(){
        $userid = cookie('_userid'); 
        $userinfo = member_info($userid);
        if($userid < 1) $this->error('您还没有登录，请先登录',U('Member/Index/login'));
        //判断会员的账号是否可用
        $minfo = $this->member_db->getByUserid($userid);
        $islock = $minfo['islock'];
        if($islock == 1) $this->error('您的账号已被锁定');
        $modelid = $minfo['modelid'];
        //充值账号
        $alipay = getcache('weixin',pay);

       if(IS_GET){
            $info = I('get.');
            $money = $info['money'];
            if($money < 0) $this->error('请输入金额');
            if($info['pay_code'] == 'Bank') $this->error('请手动转账汇款到指定帐号！');
            //判断手续费
            $pay_setting = getcache('payment','pay');
            foreach ($pay_setting as $k=>$v){
                //微信支付
                if($v['pay_code'] == $info['pay_code']){
                    $pay_method = $v['pay_method'];
                    $pay_fee = $v['pay_fee'];
                    if($pay_method == 0){//按比例收费
                        //总价格
                        $total = $money + round($money * $pay_fee / 100 , 2);
                        $pay = round($money * $pay_fee / 100 , 2);
                    }else{
                        $total = $money + $pay_fee;
                        $pay = $pay_fee;
                    }
                }
            }
            $sqlMap = array();
            $sqlMap['userid'] = $userid;
            $sqlMap['code'] = $info['pay_code'];
            $sqlMap['trade_sn'] = date('YmdHis').NOW_TIME.(rand()%90 +10);
            $sqlMap['total_fee'] = $money;
            $sqlMap['status'] = '';
            $sqlMap['subject'] = '用户充值';
            $sqlMap['dateline'] = NOW_TIME;
            $sqlMap['fee'] = $pay;
            $rs = $this->db->add($sqlMap);
            if($rs){
                $product_info = array(
                        'name'      => '用户充值',//订单名称
                        'total_fee' => $total,//订单价格
                        'trade_sn'  => $sqlMap['trade_sn'],//订单号唯一
                        'userid' =>$userid,
                );
                $payment_handler = new \Pay\Factory\Factory($info['pay_code'],'',$product_info);
                $payment_handler->set_productinfo($product_info);
                

            }
        }


   }



    /*用户充值页面*/    
    public function pay(){
    	$userid = cookie('_userid'); 
        $userinfo = member_info($userid);
    	if($userid < 1) $this->error('您还没有登录，请先登录',U('Member/Index/login'));
    	//判断会员的账号是否可用
    	$minfo = $this->member_db->getByUserid($userid);
    	$islock = $minfo['islock'];
    	if($islock == 1) $this->error('您的账号已被锁定');
    	$modelid = $minfo['modelid'];

    	//充值账号
    	$alipay = getcache('alipay',pay);
    	if(IS_POST){
    		$info = I('post.');
    		$money = $info['money'];
    		if($money < 0) $this->error('请输入金额');

            if($info['pay_code'] == 'Bank') $this->error('请手动转账汇款到指定帐号！');
    		//判断手续费
    		$pay_setting = getcache('payment','pay');
    		foreach ($pay_setting as $k=>$v){
    			//支付宝
    			if($v['pay_code'] == $info['pay_code']){
    				$pay_method = $v['pay_method'];
    				$pay_fee = $v['pay_fee'];
    				if($pay_method == 0){//按比例收费
    					//总价格
    					$total = $money + round($money * $pay_fee / 100 , 2);
    					$pay = round($money * $pay_fee / 100 , 2);
    				}else{
    					$total = $money + $pay_fee;
    					$pay = $pay_fee;
    				}
    			}
    		}
    		$sqlMap = array();
    		$sqlMap['userid'] = $userid;
    		$sqlMap['code'] = $info['pay_code'];
    		$sqlMap['trade_sn'] = date('YmdHis').NOW_TIME.(rand()%90 +10);
    		$sqlMap['total_fee'] = $money;
    		$sqlMap['status'] = '';
    		$sqlMap['subject'] = '用户充值';
    		$sqlMap['dateline'] = NOW_TIME;
    		$sqlMap['fee'] = $pay;
    		$rs = $this->db->add($sqlMap);
    		if($rs){
    			$product_info = array(
                        'name'      => '用户充值',//订单名称
                        'total_fee' => $total,//订单价格
                        'trade_sn'  => $sqlMap['trade_sn'],//订单号唯一
    			);
    			$payment_handler = new \Pay\Factory\Factory($info['pay_code']);
    			$payment_handler->set_productinfo($product_info);
    			$result = $payment_handler->get_code();
    			Header("Location: $result");
    		}
    	}else{

            $SEO = seo(0,'账户充值');
            /*获取已开启的支付平台*/
            $pay_list = model('pay_payment')->field('name,logo,pay_code')->where(array('is_open' => 1))->select();
    		include template('pay');
    	}
    } 
    
    /*获取充值手续费*/
    public function check_money(){
    	$money = I('money');
    	$pay_setting = getcache('payment','pay');
    	//判断按什么收费 0 比例 1 固定
    	foreach ($pay_setting as $k=>$v){
    		//支付宝
    		if($v['pay_code'] == I('code')){
    			$pay_method = $v['pay_method'];
    			$pay_fee = $v['pay_fee'];
    			if($pay_method == 0){//按比例收费
    				//总价格
    				$total = $money + round($money * $pay_fee / 100 , 2);
    				$pay = round($money * $pay_fee / 100 , 2);
    			}else{
    				$total = $money + $pay_fee;
    				$pay = $pay_fee;
    			}
    		}
    	}
    	echo json_encode(array('total'=>$total,'fee'=>$pay_fee,'pay_fee'=>$pay));
    }


    /*普通充值*/
    public  function ordinary(){
		$userid = cookie('_userid'); 
    	if($userid < 1) $this->error('您还没有登录，请先登录',U('Member/Index/login'));
		//判断会员的账号是否可用
    	$minfo = $this->member_db->getByUserid($userid);
    	$islock = $minfo['islock'];
        if($islock == 1) $this->error('您的账号已被锁定');
        if(IS_POST){
    		$info = I('info');
			$tran_number = $info['tran_number'];
			if(strlen($tran_number) != 28 && strlen($tran_number) != 32){
				$this->error('交易号输入错误');
			}
			$money = $info['money'];
			if($money < 0){
				$this->error('金额不能小于0');
			}

			$paymode = $info['paymode'];

    		$info['inputtime'] = NOW_TIME;
    		$info['status'] = 0;
            $info['userid'] = $userid;
    		$result = Model('pay_check')->add($info);
    		if(!$result){
    			$this->error('操作失败');
    		}else{
    			$this->success('操作成功，请耐心等待管理员审核');
    		}
    	}
    }
    public function total_money(){
        
    	$pay_setting = getcache('deposite_setting','pay');
    	$quick = $pay_setting['quick'];
    	//取出当前用户的id
    	$userid = (int) cookie('_userid');
        $userinfo = member_info($userid);
    	//判断该会员是商家还是用户
    	$modelid = model('member')->getFieldByUserid($userid,'modelid');
    	//提现手续费  判断是否为商家
    	//$fee = ($modelid == 1) ? $quick['service']['common'] : $quick['service']['vip'];
    	if($modelid == 1){
    		$fee = $quick['service']['common'];
    	}else{
    		//判断该商家是否为普通商家
    		$groupid = model('member')->getFieldByUserid($userid,'groupid');
    		if($groupid == 1){//普通商家
    			$fee = $quick['service']['common'];
    		}else{
    			$fee = $quick['service']['vip'];
    		}
    	}
    	$money = I('money');
    	$total_money = $money - $money * $fee /100;
    	echo $total_money;
    }
    /*提现*/
    public function deposite(){

        

        $userid = (int) cookie('_userid');
        $userinfo = member_info($userid);

    	//提现设置
    	$pay_setting = getcache('deposite_setting','pay');
        extract($pay_setting);
    	if(empty($type)){
    		$this->error('管理员没有设置提现方式，请联系管理员');
    	}

        //查出已绑定的支付宝账号
        $alipay = model('member_attesta')->where(array('userid'=>$userid,'type'=>'alipay'))->find();

        $alipays = string2array($alipay['infos']);

        $alipayinfos = string2array($alipay['infos']);
        //查出已绑定的银行卡
        $bank = model('member_attesta')->where(array('userid'=>$userid,'type'=>'bank'))->find();
        $bankinfos = string2array($bank['infos']);
        $infos = model('member_attesta')->where(array('userid'=>$userid))->select();
    //   echo model('member_attesta')->getLastSql();

        $arr = array();
        foreach ($infos as $key=>$val) {
            $arr[$val['type']] = $val;
        }

       // var_dump($infos);




    	//取出当前用户的id
    	//判断该会员是商家还是用户
    	$modelid = model('member')->getFieldByUserid($userid,'modelid');
    	//提现手续费
    	if($modelid == 1){
    		$fee = $quick['service']['common'];
    	}else{
    		//判断该商家是否为普通商家
    		$groupid = model('member')->getFieldByUserid($userid,'groupid');
    		if($groupid == 1){//普通商家
    			$fee = $quick['service']['common'];
    		}else{
    			$fee = $quick['service']['vip'];
    		}
    	}
    	//判断会员是否登录
    	if($userid < 1) {
    		$this->error('您还没有登录，请先登录',U('Member/Index/login'));
    	}else{
    		//判断用户的账号是否通过
    		$status = model('member')->getFieldByUserid($userid,'status');
    		if($status != 1) {
    			$this->error('您的账号还没有审核通过');
    		}
    	}
    	$money = model('member')->getFieldByUserid($userid,'money');
    	if(IS_POST){
    	    $info = I('post.');
    	    $info['inputtime'] = NOW_TIME;
    	    $info['status'] = 0;
    	    $info['userid'] = $userid;
    	    $info['ip'] = get_client_ip();
    	    $info['fee'] = $fee;
    	    $info['totalmoney'] = $info['money'] - $info['money'] * $fee / 100;//实际提现金额
    	    //判断是否有申请提现
    	    $sign = '4-1-'.$userid.'-'.$info['money'].'-'.dgmdate($info['inputtime'],'Y-m-d H:i');
    	    $rs = model('member_finance_log')->where(array('only'=>$sign))->find();
    	    if(!$rs){
    	        //判断是否选择提现方式
    	        if(!isset($info['type'])){
    	            $this->error('请选择提现方式');
    	        }
    	        //判断当前金额是否符合条件
    	        $min_money = $pay_setting['min_money'];
    	        if($info['money'] < 0){
    	            $this->error('金额不能小于0 ');
    	        }
    	        if($info['money'] < $min_money && $info['money'] > 0){
    	            $this->error('金额不能小于'.$min_money);
    	        }
    	        if($info['money'] % $pay_setting['multiple_money'] != 0){
    	            $this->error('金额必须为'.$pay_setting['multiple_money'].'倍数');
    	        }
    	        if($info['paypal'] == 1){
    	            $info['fee'] = 0;
    	            $info['totalmoney'] = $info['money'];
    	        }else{
    	            $info['fee'] = $info['money'] * $fee / 100;
    	            $info['totalmoney'] = $info['money'] - $info['money'] * $fee / 100;
    	        }
    	        //检测该用户的账户余额
    	        if($money < $info['money']){
    	            $this->error('您的账户余额不足');
    	        }
    	        $rs = model('member_attesta')->where(array('userid'=>$userid,'type'=>'identity'))->find();
    	        $identify = string2array($rs['infos']);
    	        $name = $identify['name'];
    	        //查出该用户绑定的账号
    	        if($info['type'] == 1){//提到银行卡
    	            $bank = model('member_attesta')->where(array('userid'=>$userid,'type'=>'bank'))->find();
    	            if(!$bank){
        				$this->error('您还没有绑定银行卡账号，请先绑定',U('Member/Attesta/bank_attesta'));
    	            }
    	            $bankinfos = string2array($bank['infos']);
    	            $info['bank'] = model('linkage')->getFieldByLinkageid($bankinfos['bank_name'],'name');
    	            $info['name'] = $name;
    	            $info['cash_alipay_username'] = $bankinfos['account'];
    	        }else{//提现到支付宝
    	            $alipay = model('member_attesta')->where(array('userid'=>$userid,'type'=>'alipay'))->find();
    	            if(!$alipay){
        				$this->error('你还没有绑定支付宝账号，请先绑定',U('Member/Attesta/alipay_attesta'));
    	            }
    	            $alipayinfos = string2array($alipay['infos']);
    	            $info['name'] = $name;
    	            $info['cash_alipay_username'] = $alipayinfos['alipay_account'];
    	        }


    	        $result = model('cash_records')->add($info);
    	        if($result){
	                action_finance_log($userid,-$info['money'],'money','userid'.$userid.':申请提现',$sign,array());
	                $this->success('申请成功，请耐心等待');
    	        }else{
    	            $this->error('申请失败');
    	        }
    	    }else{
    	        $this->error('申请失败,一分钟只能申请一次');
    	    }
    	}else{
            $SEO = seo(0,'余额提现');
    		include template('deposite');
    	}
    }
    /*检测是否绑定银行、支付宝*/
    public function check(){
    	$userid = (int) cookie('_userid');
    	if($userid < 1)$this->error('您还没有登录，请先登录',U('Member/Index/login'));
    	//判断当前后台设置
    	$pay_setting = getcache('deposite_setting','pay');
    	extract($pay_setting);
    	if(in_array('alipay',$type) && in_array('bank',$type)){
    		$info = model('member_attesta')->where(array('userid'=>$userid))->getField('type',true);
    		if(in_array('alipay',$info) && !in_array('bank',$info)){
    			$this->error('bank',U('Member/Attesta/bank_attesta'));
    		}elseif(!in_array('alipay',$info) && in_array('bank',$info)){
    			$this->error('alipay',U('Member/Attesta/Alipay_attesta'));
    		}elseif(!in_array('alipay',$info) && !in_array('bank',$info)){
    			$this->error('all');
    		}
    	}elseif(in_array('bank',$type)){
    		//判断是否绑定银行卡
    		$bank = model('member_attesta')->where(array('userid'=>$userid,'type'=>'bank'))->find();
    		if(!$bank){
    			$this->error('bank',U('Member/Attesta/bank_attesta'));
    		}else{
    			$this->success('银行卡已绑定');
    		}
    	}else{
    		//判断是否绑定支付宝
    		$alipay = model('member_attesta')->where(array('userid'=>$userid,'type'=>'alipay'))->find();
    		if(!$alipay){
    			$this->error('alipay',U('Member/Attesta/Alipay_attesta'));
    		}else{
    			$this->success('支付宝已绑定');
    		}
    	}
    } 


    /*判断交易号是否重复*/
    public function check_trade(){
        $trade_sn = $_GET['tran_number'];
        if (!$trade_sn) return false;
        $sqlmap = array();
        $sqlmap['tran_number'] = $trade_sn;
        $sqlmap['status']= array('IN','0,1');
        $result = model('pay_check')->where($sqlmap)->select();
        if($result){
            $this->error('支付宝交易号已存在'); 
        } else {
          $this->success('输入正确');
        }        

    }
}