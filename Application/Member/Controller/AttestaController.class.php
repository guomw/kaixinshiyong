<?php 
// +----------------------------------------------------------------------
// | 会员中心 资料认证管理
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2016 http://www.dealswill.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
//
namespace Member\Controller;
use \Member\Controller\InitController;
class AttestaController extends InitController {
	public function _initialize() {
		parent::_initialize();
		$this->db = model('member');
		$this->sms_db = model('sms_report');
		$this->module_db = model('module');
		$this->attesta_db = model('member_attesta');
		// $this->userinfo = getUserInfo($this->userid);
		$this->modelid = $this->userinfo['modelid'];
		$this->setting = D('Setting')->getField('key,value');
	}

	/**
	 * 魔力试用资料认证
	 * @author   <master@ .cn>
     * @copyright: adalways inc
     * @date：2016-10-06
     * @version：1.0
     */
	public function index() {
        if(DEFAULT_THEME == 'wap'){
            redirect(U('Member/Profile/infomation'));
        }

		//会员模型
		$models = getcache('model','commons');
		$userinfo = getUserInfo($this->userid);
		$groupid = $userinfo['groupid'];
		$groups = ($userinfo['modelid'] == 1) ? getcache('member_group','member') : getcache('merchant_group','member');
		$groupname = $groups[$groupid]['name'];
	    $tablename = $models[$this->modelid]['tablename'];
		$member_infos = M($tablename)->where(array('userid'=>$this->userid))->find();
		$SEO = seo(0,"资料认证");
		//查出已绑定的支付宝账号
		$alipay = $this->attesta_db->where(array('userid'=>$this->userid,'type'=>'alipay'))->find();

		$alipayinfos = string2array($alipay['infos']);
		//查出已绑定的银行卡
		$bank = $this->attesta_db->where(array('userid'=>$this->userid,'type'=>'bank'))->find();
		$bankinfos = string2array($bank['infos']);
		//查看是否绑定店铺、品牌
		$infos = $this->attesta_db->where(array('userid'=>$this->userid))->select();
		$arr = array();
		foreach ($infos as $key=>$val) {
			$arr[$val['type']] = $val;
		}


		$tpl = ($this->modelid == 1)? 'buyer/attestation':'merchant/attestation';

		include template($tpl);
	}
	/**
	 * 商家个人认证首页
	 * @author   <master@ .cn>
     * @copyright: adalways inc
     * @date：2016-10-06
     * @version：1.0
     */
	public function person(){
		//判断是否商家会员
		if($this->modelid != 2){
			$this->error('您无权访问该页面');
		}
		$infos = $this->attesta_db->where(array('userid'=>$this->userid))->select();
		$userinfo = getUserInfo($this->userid);
		$arr = array();
		foreach ($infos as $key=>$val) {
			$arr[$val['type']] = $val;
		}
		//查出已绑定的支付宝账号
		$alipay = $this->attesta_db->where(array('userid'=>$this->userid,'type'=>'alipay'))->find();
		$infos = string2array($alipay['infos']);
		//查出已绑定的银行卡
		$bank = $this->attesta_db->where(array('userid'=>$this->userid,'type'=>'bank'))->find();
		$bankinfos = string2array($bank['infos']);
		$SEO = seo(0,'商家个人认证');
		include template('merchant/person');
	}

	/**
	 * 获取邮箱验证码
	 * @author   <master@ .cn>
     * @copyright: adalways inc
     * @date：2016-10-06
     * @version：1.0
     */
	public function verify(){
		$info = $this->db->where(array('userid'=>$this->userid))->find();
		$userid = $info['userid'];
		$email=I('email');
		if ($email){
			if ((cookie($userid.'_email_time')+60) > NOW_TIME) $this->error('请等待60秒后再获取');
			//判断邮箱是否验证过
			$info = model('member')->where(array('email'=>$email,'email_status'=>1))->find();
			if($info) $this->error('该邮箱已经被认证过');
			helpers('mail');
			$code = random(6,1);
			$result = sendmail($email, '邮箱认证','您邮箱认证验证码为：' .$code);
			$arr = array();
			if($result){
				cookie($userid.'_email',$code);
				cookie($userid.'_email_time',NOW_TIME);
				$this->success('发送邮箱成功');
			}else{
				$this->error('发送邮箱失败');
			}
		}
	}
	
	/**
	 * 邮箱认证
	 * @author   <master@ .cn>
     * @copyright: adalways inc
     * @date：2016-10-06
     * @version：1.0
     */
	public function email_attesta(){
		if (IS_POST) {
			$info = I("post.");
			$config = getcache('setting', 'member');		
			$email_code = cookie($this->userid."_email");
			if ($info['n_code'] != $email_code) {
				$this->error("验证码有误");
			}
			$infos['email_status'] = 1;
			$infos['email'] = $info['email'];
			//判断邮箱是否可用
			$rs = $this->db->where(array('email'=>$info['email'],'userid'=>array('NEQ'=>$this->userid)))->find();
			if($rs){
				$this->error('该邮箱已经被占用，请更换邮箱');
			}
			//判断邮箱是否已经被认证
			if($this->userinfo['email_status'] == 1 && $this->userinfo['email'] == $info['email']){
				$this->error('该邮箱已经认证过');
			}
			$result = $this->db->where(array('userid'=>$this->userid))->save($infos);
			if ($result) {
				if($this->userinfo['email_status'] == 0) {
					runhook('member_attesta_email');
				}
				$url = ($this->modelid == 1) ? U('Member/Attesta/index') : U('Member/Attesta/person');
				$this->success("邮箱认证成功！",$url);
			}
		}else{
			$userinfo = getUserInfo($this->userid,'',1);
			$SEO = seo(0,"邮箱认证");
			$tpl = ($this->modelid == 1)? 'buyer/email_attesta':'merchant/email_attesta';
			include template($tpl);
		}
	}
	/**
	 * 邮箱检测
	 * @author   <master@ .cn>
     * @copyright: adalways inc
     * @date：2016-10-06
     * @version：1.0
     */
	public function check_email(){
		$email = $_GET['email'];
		$result = $this->db->where(array('email'=>$email))->find();
		if($result){
			//查看是否为当前会员
			if($result['userid'] == $this->userid){
				$this->success('可用');
			}else{
				$this->error('邮箱不可用');
			}
		}else{
			$this->success('该邮箱可用');
		}
	}

	/**
	 * 获取手机验证码【魔力试用】
	 * @author   <master@ .cn>
     * @copyright: adalways inc
     * @date：2016-10-06
     * @version：1.0
     */
	public function code(){
		$phone = I('phone');
		if(is_mobile($phone) != TRUE) $this->error('手机号格式不正确');
		//判断改用是否已经绑定过
		$info = model('member')->where(array('phone'=>$phone,'phone_status'=>1))->find();
		if($info > 0) $this->error('该手机已经绑定过，请更换手机');
		//判断用户发送短信的次数
		//判断该用户的次数
		$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		$endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
		$sqlmap = array();
		$sqlmap['userid'] = $this->userid;
		$sqlmap['posttime'] = array("BETWEEN",array($beginToday,$endToday));
		$sqlmap['enum'] = 'phone';

		// 查询最后一次的获取信息
		$lastSms = $this->sms_db->where($sqlmap)->order('id DESC')->find();
		if (($lastSms['posttime']+60) > NOW_TIME) $this->error('请等待60秒后再获取...');
		$count = $this->sms_db->where($sqlmap)->count();
		if (intval($count) > 3) {
			$this->error('今日发送短信次数已满');
		}
		 $_vcode = random(6, 1);
        $msg = '您的手机认证验证码为'.$_vcode;
		$SmsApi = new \Sms\Api\SmsApi();
		$arr = array();
        $arr['param'] = "{'code':'$_vcode'}";
        $arr['template_id'] = C('template_id_3');
        $result = $SmsApi->send($phone, $msg,$arr);
       // $result = $SmsApi->send($phone, $msg);
        if(!$result) {
            $this->error('手机短信发送失败，请重试。');
        } else {
            $info = array();
            $info['mobile'] = $phone;
            $info['posttime'] = NOW_TIME;
            $info['id_code'] = $_vcode;
            $info['msg'] = $msg;
            $info['userid'] = $this->userid;
            $info['ip'] = get_client_ip();
            $info['status'] = 1;
            $info['enum'] = 'phone';
            model('sms_report')->update($info);
            $this->success(M()->getdberror());
        }
	}
	
	/**
	 * 手机认证
	 * @author   <master@ .cn>
     * @copyright: adalways inc
     * @date：2016-10-06
     * @version：1.0
     */
	public function phone_attesta() {
		if (IS_POST) {
			$info = I('post.');
			$map['enum'] = 'phone';
			$map['mobile'] = $info['mobile'];
			$map['id_code'] = $info['id_code'];
			$map['userid'] = $this->userid;
			//判断该用户的手机是否可用
			$rs = $this->db->where(array('phone'=>$info['mobile'],'userid'=>array('NEQ'=>$this->userid)))->count();


			if($rs > 0){
				$this->error('该手机已被占用，请更换手机');
				return FALSE;
			}
			//判断是否认证过
			if($this->userinfo['phone_status'] && $this->userinfo['phone'] == $info['mobile']){
				$this->error('该手机已经认证，请勿重新认证');
				return FALSE;
			}


			//查询赠送积分
			$member_setting = $this->module_db->getFieldByModule('Member', 'setting');
			$member_setting = dstripslashes(unserialize($member_setting));
			$codes = $this->sms_db->where($map)->find();
			if ($codes) {
				if ($codes['id_code'] != $info['id_code']) {
					$this->error('验证码有误');
					return FALSE;
				}
				if (NOW_TIME - $codes['posttime'] >60*5) {
					$this->error('验证码已失效');
					return FALSE;
				}
				$result = $this->db->where(array('userid'=>$this->userid))->save(array('phone_status'=>1,'phone'=>$codes['mobile']));
				$url = ($this->modelid == 1) ? U('Member/Attesta/index') : U('Member/Attesta/person');
				if ($result && $this->userinfo['phone_status'] == 0) {
					runhook('member_attesta_phone',array('userid'=>$this->userid));
					$this->success('手机认证成功！',$url);
				}elseif($result && $this->userinfo['phone_status'] == 1){
					$this->success('手机认证成功！',$url);
				}
			}else{
				$this->error('该验证码不存在');
					return FALSE;
			}
		}else{
			$tpl = ($this->modelid == 1)? 'buyer/phone_attesta' : 'merchant/phone_attesta';
			$SEO = seo(0,"手机认证");
			include template($tpl);
		}
	}
	/**
	 * 证码判断
	 * @author   <master@ .cn>
     * @copyright: adalways inc
     * @date：2016-10-06
     * @version：1.0
     */
	public function check_verify(){
 		$phone = $_GET['phone'];
		$id_code =$_GET['verify'];
		$rs = $this->sms_db->where(array('mobile'=>$phone,'id_code'=>$id_code))->find();
		if(empty($rs)){
			$this->error('验证码错误');
		}else{
			//判断是否超过时间
			$time = NOW_TIME - $rs['posttime'];
			if($time > 300){
				$this->error('验证码过期，请重新发送');
			}else{
				$this->success('验证码输入成功');
			}
		}
	}
	/**
	 * 手机判断
	 * @author   <master@ .cn>
     * @copyright: adalways inc
     * @date：2016-10-06
     * @version：1.0
     */
	public function check_phone(){
		$phone = $_GET['phone'];
		if(empty($phone) || !is_mobile($phone)) {
			$this->error('手机为空或格式不正确');
		}
		//判断是否有该手机号码
		$rs = $this->db->where(array('phone'=>$phone))->count();		
		if($rs > 0){
			$this->error('该手机号码已经认证，请更换手机');
		}else{
			$this->success('输入正确');
		}
	}
	/**
	 * 是否已经身份认证
	 * @author   <master@ .cn>
     * @copyright: adalways inc
     * @date：2016-10-06
     * @version：1.0
     */
	public function isperson(){
		$result = model("member_attesta")->where(array('userid'=>$this->userid,'type'=>'identity'))->count();
		if($result){
			$this->success('成功');
		}else{
			$this->error('您未通过实名认证，请先认证');
		}
	}
	
	/**
	 * 身份认证
	 * @author   <master@ .cn>
     * @copyright: adalways inc
     * @date：2016-10-06
     * @version：1.0
     */
	public function name_attesta(){
		//查询已提交的信息
		$rs = $this->attesta_db->where(array('userid'=>$this->userid,'type'=>'identity'))->find();

		if($rs){
			if($rs['status'] == 0){
				$this->error('您的身份认证信息正在审核中，请勿重复操作',U('person'));
				return false;
			}elseif($rs['status'] == 1){
				$this->error('您已经认证，请勿重新认证');
				return false;
			}
		}
		$identity = string2array($rs['infos']);
		//查出当前用户的真是姓名
		$models = getcache('model','commons');
		$tablename = $models[$this->modelid]['tablename'];
		$memberinfo = model($tablename)->where(array('userid'=>$this->userid))->find();
		$name = ($this->modelid == 1)? $memberinfo['realname'] : $memberinfo['contact_name'];
		
 		if (IS_POST) {
			$info = I("post.");
			$info['infos'] = array2string($info);
			$info['userid'] = $this->userid;
			$info['dateline'] =  NOW_TIME;
			$info['type'] = 'identity';
			$info['status'] = 0;
			if (strlen($info['id_number']) < 15 || strlen($info['id_number']) > 18) {
					$this->error('请输入正确的身份证号码');
					return FALSE;
			}
			
			if($rs){
				$info['updatetime'] = NOW_TIME;
				$result = $this->attesta_db->where(array('id'=>$info['id']))->save($info);
			}else{
				$result = $this->attesta_db->add($info);
			}
			if ($result) {
				if($this->modelid == 1){
					model($tablename)->where(array('userid'=>$this->userid))->setField('realname',$info['name']);
				}else{
					model($tablename)->where(array('userid'=>$this->userid))->setField('contact_name',$info['name']);
				}
				$url = ($this->modelid == 1) ? U('Member/Attesta/index') : U('Member/Attesta/person');
				$this->success('信息提交成功，请等待审核',$url);
			}
		}else{
			$SEO = seo(0,"实名认证");
			$tpl = ($this->modelid == 1) ? 'buyer/name_attesta' : 'merchant/name_attesta';
			include template($tpl);
		}
	}
	/**
	 * 支付宝认证
	 * @author   <master@ .cn>
     * @copyright: adalways inc
     * @date：2016-10-06
     * @version：1.0
     */
	public function alipay_attesta(){
		$models = getcache('model','commons');
		$tablename = $models[$this->modelid]['tablename'];
		$memberinfo = model($tablename)->find($this->userid);

        $infos = model('member_attesta')->where(array('userid'=>$this->userid,'type'=>'identity'))->find();
        if(empty($infos) || $infos['status'] == 0){
            $this->error('您还未通过实名认证，实名认证通过后才可绑定',U('name_attesta'));
        }

		//取出用户身份认证的名称
		$identity = model('member_attesta')->where(array('userid'=>$this->userid,'type'=>'identity'))->find();
		$infos = string2array($identity['infos']);
		//判断该用户是否认证过
		$rs = $this->attesta_db->where(array('userid'=>$this->userid,'type'=>'alipay'))->find();
		if($rs){
			$this->error('您已经认证过，请勿再次认证');
		}
		if (IS_POST) {
			$info = I('post.');
			if (empty($info['account'])) {
				$this->error('请输入支付宝账号');
				return FALSE;
			}
			$email = isemail($info['account']); 
			$phone = is_mobile($info['account']);
			if (!$email && !$phone) {
				$this->error('格式错误');
				return FALSE;
			}
			//判断该支付宝账号是否绑定
			$con = array();
			$con['type'] = array('EQ','alipay');
			$con['infos'] = array('LIKE','%'.$info['account'].'%');
			$attesta_infos =  model('member_attesta')->where($con)->count();
			
			if($attesta_infos > 0){
				$this->error('该支付宝账号已经认证过');
			}
			//将认证信息存入数据库
			$sqlmap = array();
			$arr = array('username'=>$name,'alipay_account'=>$info['account'],'alipay_code'=>$info['alipay_code']);
			$sqlmap['infos'] = array2string($arr);
			$sqlmap['userid'] = $this->userid;
			$sqlmap['dateline'] = NOW_TIME;
			$sqlmap['status'] = 1;
			$sqlmap['type'] = 'alipay'; 
			$result = $this->attesta_db->add($sqlmap);
			if ($result) {
				model('member')->where(array('userid'=>$this->userid))->setField('alipay_status', 1);	
				$url = ($this->modelid == 1) ? U('Member/Attesta/index') : U('Member/Attesta/person');
				runhook('member_attesta_alipay');
				$this->success('支付宝绑定成功',$url);
			}else{
				$this->error('操作失误，请重试！');
				return FALSE;
			}
		}else{
			$SEO = seo(0,"支付宝绑定");
			$tpl = ($this->modelid == 1) ? 'buyer/alipay_attesta' : 'merchant/alipay_attesta';
			include template($tpl);
		}
	}
	/**
	 * 银行绑定
	 * @author   <master@ .cn>
     * @copyright: adalways inc
     * @date：2016-10-06
     * @version：1.0
     */
	public function bank_attesta(){
		$infos = model('member_attesta')->where(array('userid'=>$this->userid,'type'=>'identity'))->find();
		if(empty($infos) || $infos['status'] == 0){
			$this->error('您还未通过实名认证，实名认证通过后才可绑定',U('name_attesta'));
		}
		
		$account = model('member')->where(array('userid'=>$this->userid))->find();
		if ($account['bank_status'] == 1) {
			$this->error('已绑定，请勿重新绑定');
			return FALSE;
		}
		$region = model('linkage')->where(array('parentid'=>0,'keyid'=>1))->select();
		//判断用户是否绑定过
		$rs = $this->attesta_db->where(array('userid'=>$this->userid,'type'=>'bank'))->find();
		//查询银行
		$banks = model('linkage')->where(array('parentid'=>0,'keyid'=>3360))->select();
		if($rs){
			$this->error('您已经认证过，请勿重新认证');
			return false;
		}
		//查询绑定的身份
		$identity = model('member_attesta')->where(array('userid'=>$this->userid,'type'=>'identity'))->find();
		$infos = string2array($identity['infos']);
		if (IS_POST) {
			$info = I('post.');
			$map = array();
			$map['infos'] = array2string($info);
			$map['userid'] = $this->userid;
			$map['dateline'] = NOW_TIME;
			$map['status'] = 1;
			$map['type'] = 'bank';
			if (!is_numeric($info['account']) || strlen($info['account']) < 16 || strlen($info['account']) > 19) {
				$this->error('请输入正确的银行卡号');
				return FALSE;
			}
			//判断该银行账号是否绑定
			$con = array();
			$con['type'] = array('EQ','bank');
			$con['infos'] = array('LIKE','%'.$info['account'].'%');
			$attesta_infos =  model('member_attesta')->where($con)->count();
			if($attesta_infos > 0){
				$this->error('该支付宝账号已经认证过');
			}
			
			//记录该用户绑定的信息
			$result =  $this->attesta_db->add($map);
			if ($result) {
				 model('member')->where(array('userid'=>$this->userid))->setField('bank_status', 1);	
				 $url = ($this->modelid == 1) ? U('Member/Attesta/index') : U('Member/Attesta/person');
				 runhook('member_attesta_bank');
				$this->success('银行绑定成功',$url);
			}else{
				$this->error('操作失误，请重试！');
				return FALSE;
			}
		}else{
			$SEO = seo(0,"银行账户绑定");
			$tpl = ($this->modelid == 1) ? 'buyer/bank_attesta' : 'merchant/bank_attesta';
			include template($tpl);
		}
	}

	/**
	 * 店铺认证 [魔力试用]
	 * @author   <master@ .cn>
     * @copyright: adalways inc
     * @date：2016-10-06
     * @version：1.0
     */
	public function shop(){
		$rs = $this->attesta_db->where(array('userid'=>$this->userid,'type'=>'shop'))->find();
		$infos = $rs['infos'];
		$url = string2array($infos);
		//查出该商家的验证码
		$verify = M('MemberMerchant')->getFieldByUserid($this->userid,'verify');
		
		//判断是否为商家会员
		if($this->modelid != 2){
			$this->error('您不是商家会员，请先申请为商家会员',U('Member/Index/register'));
		}
		if(submitcheck('dosubmit')){
			$info = I('post.');
			$info['infos'] = array2string($info);
			$info['userid'] = $this->userid;
			$info['dateline'] = NOW_TIME;
			$info['status'] = 1;
			$info['type'] = 'shop';
			if($rs){
				$this->error('你已经认证过，请勿重新认证');
			}else{
				if(empty($_POST['url'])){
					$this->error('商品链接不能为空');
				}

				$result = go_tmall($_POST['url']);
				$result = strpos($result['title'],$verify);
				if($result > 0){
					$this->attesta_db->add($info);
					runhook('member_attesta_shop');
					$this->success('认证通过');
				}else{
					$this->error('认证失败');
				}
			}
		}else{
			$SEO = seo(0,"店铺认证");
			include template('merchant/shop');
		}
	}
	/**
	 * 给商家指定一个验证码
	 * @author   <master@ .cn>
     * @copyright: adalways inc
     * @date：2016-10-06
     * @version：1.0
     */
	public function check_shop_verify(){
		$userid = I('userid');
		$verify = random(6);
		$result = model('member_merchant')->where(array('userid'=>$userid))->setField('verify',$verify);
		echo $verify;
	}
	/*品牌认证【魔力试用】*/
	public function brand(){
		$rs = $this->attesta_db->where(array('userid'=>$this->userid,'type'=>'brand'))->find();
		$infos = string2array($rs['infos']);
		//判断是否为商家会员
		if($this->modelid != 2){
			$this->error('您不是商家会员，请先申请为商家会员',U('Member/Index/register'));
		}
		if(IS_POST){
			$info = I('post.');
			$info['infos'] = array2string($info);
			$info['userid'] = $this->userid;
			$info['status'] = 0;
			$info['dateline'] = NOW_TIME;
			$info['type'] = 'brand';
			if($rs){
				$result = $this->attesta_db->where(array('id'=>$info['id']))->save($info);
			}else{
				$result = $this->attesta_db->add($info);
			}
			if($result){
				runhook('member_attesta_brand');
				$this->success('操作成功',U('index'));
			}else{
				$this->error('操作失败');
			}
		}else{
			include template('merchant/brand');
		}
	}

	/**
	 * 下载授权书
	 * @author   <master@ .cn>
     * @copyright: adalways inc
     * @date：2016-10-06
     * @version：1.0
     */
	public function download(){
		header("Content-type:text/html;charset=utf-8");
		$file_name = '基本设置信息.doc';
		//用以解决中文不能显示出来的问题
		$file_name=iconv("utf-8","gb2312",$file_name);
		$file_sub_path=$_SERVER['DOCUMENT_ROOT'].'/uploadfile/downfile/';
		$file_path=$file_sub_path.$file_name;
		//首先要判断给定的文件存在与否
		if(!file_exists($file_path)){
			echo "没有该文件文件";
			return ;
		}
		$fp=fopen($file_path,"r");
		$file_size=filesize($file_path);
		//下载文件需要用到的头
		Header("Content-type: application/octet-stream");
		Header("Accept-Ranges: bytes");
		Header("Accept-Length:".$file_size);
		Header("Content-Disposition: attachment; filename=".$file_name);
		$buffer=1024;
		$file_count=0;
		//向浏览器返回数据
		while(!feof($fp) && $file_count<$file_size){
			$file_con=fread($fp,$buffer);
			$file_count+=$buffer;
			echo $file_con;
		}
		fclose($fp);
	}
	
	public function compare_email(){
		$email = $_GET['old_email'];
		$result = $this->db->where(array('email'=>$email,'userid'=>$this->userid))->find();
		if($result){
			$this->success('可用');
		}else{
			$this->error('邮箱输入错误');
		}
	}

	/**
	 * 绑定亚马逊账号
	 * @author   <master@ .cn>
     * @copyright: adalways inc
     * @date：2016-10-06
     * @version：1.0
     */
	public function bindtaobao(){
		$SEO = seo('','亚马逊账号绑定');
		if ($this->userinfo['modelid'] != 1) $this->error('请登录买家账号',U('member/index/login'));
		if(IS_POST){
			$info = I('post.data');
			$account = $info['account'];
			if (!$account) $this->error('请输入要绑定的亚马逊帐号');

			/* 统计该会员已绑定数量 */		
			$bind_tb_nums = C('bind_tb_nums');	//	后台允许绑定的个数
			$count = model('member_bind')->where(array('userid' => $this->userid,'status'=>array('NEQ',2)))->count();
			if ($count >= $bind_tb_nums) $this->error('您已绑定了'.$count.'个亚马逊帐号，已达到最高绑定数量了！');

			/* 该账号是否已经被绑定 */
			$account_count = model('member_bind')->where(array('account'=>$account))->count();
			if($account_count) $this->error('该亚马逊账号已经被绑定过，请更换亚马逊账号');

			$data                   = array();
			$data['userid']         = $this->userid;
			$data['account']        = $account;
			$data['account_level']  = $info['account_level'];
			$data['status']         = 1;
			$data['bLevel']         = $info['bLevel'];
			$data['taobao_img']     = $info['taobao_img'];
		    $data['inputtime']      = NOW_TIME;
            $data['is_default'] = 0;
            $result = model('member_bind')->add($data);
			if(!$result) $this->error('绑定亚马逊账号失败');
			$this->success('绑定成功');
		}else{
			/* 已绑定的账号记录 */
			$infos = array();
			$infos = model('member_bind')->where(array('userid' => $this->userid))->order('id DESC')->select();
			include template('buyer/bind');
		}
	}

	/**
	 * 解除亚马逊绑定
	 * @author   <master@ .cn>
     * @copyright: adalways inc
     * @date：2016-10-06
     * @version：1.0
     */
	public function unbind(){
		$id = (int)$_POST['id'];
		if ($id < 1) $this->error('该亚马逊帐号不存在');
		//判断该账号是否是该会员操作
		$rs = model('member_bind')->find($id);
		if ($rs['userid'] != $this->userid) $this->error('请登录您的会员帐号！',U('member/index/login/'));
		if ($rs['status'] > 1 ) $this->error('该账号已解绑或已删除');
		$data = array();
		$data['id'] = $id;
		$data['status'] = 2;
		$data['updatetime'] = NOW_TIME;
		$result = model('member_bind')->save($data);
		if (!$result) $this->error('该账号解绑失败');
		$this->success('该账号已解绑');
	}

	/**
	 * 删除亚马逊绑定
	 * @author   <master@ .cn>
     * @copyright: adalways inc
     * @date：2016-10-06
     * @version：1.0
     */
	public function bind_del(){
		$account = (int)$_POST['id'];
		$result = model('member_bind')->where(array('id'=>$account,'userid'=>$this->userid))->delete();
		if (!$result) $this->error('参数错误');
		$this->success('该账号已成功删除');
	}



	/**
	 * 支付宝二维码上传
	 * @author   <master@ .cn>
     * @copyright: adalways inc
     * @date：2016-10-06
     * @version：1.0
     */
	public function upload(){
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

	/**
	 * 设置为默认亚马逊帐号
	 * @author   <master@ .cn>
     * @copyright: adalways inc
     * @date：2016-10-06
     * @version：1.0
     */
	public function setdefault() {
		$id = (int)$_GET['id'];
		if ($id < 1) $this->error('该亚马逊帐号不存在');
		//判断该账号是否是该会员操作
		$rs = model('member_bind')->find($id);
		if ($rs['userid'] != $this->userid) $this->error('请登录您的会员帐号！',U('member/index/login/'));
		if ($rs['status'] > 1 ) $this->error('该账号已解绑或已删除');

		/* 把该会员的所有绑定信息设置为不是默认 */
		model('member_bind')->where(array('userid'=>$this->userid))->setField('is_default',0);

		/* 设置当前账号为默认账号 */
		$result = model('member_bind')->where(array('id'=>$id))->setField('is_default',1);
		if (!$result) $this->error('设为默认账号失败');
		$this->success('默认账号设置成功！');
	}

	/**
	 * 身份证号码联网认证管理 
	 * @author   <master@ .cn>
	 * @copyright: adalways inc
	 * @date：2016-10-12
	 * @return  0 认证失败 1.认证成功
	 * @version：1.0
	 */
	public function idservice($id_number = ''){
		if (empty($id_number)) $this->error('请输入身份证号码'); 
		echo 0;
		die();
		//临时取消实名认证
		$ch = curl_init();
	    $url = 'http://apis.baidu.com/apistore/idservice/id?id='.$id_number;
	    $header = array(
	        'apikey:c929640261374c68da4396fd138faa8f',
	    );
	    // 添加apikey到header
	    curl_setopt($ch, CURLOPT_HTTPHEADER  , $header);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    // 执行HTTP请求
	    curl_setopt($ch , CURLOPT_URL , $url);
	    echo $res = curl_exec($ch);
	    echo json_decode($res);

	   // $result =  json_decode($res);
	   // $num = $result->{'errNum'};
	   // echo json_encode($num);
	}

	/**
	 * 邮箱认证
	 * @author   <master@ .cn>
     * @copyright: adalways inc
     * @date：2016-10-06
     * @version：1.0
     */
	public function v2_email_attesta(){
		if (IS_POST) {
			$info = I("post.");
			$config = getcache('setting', 'member');		
			$email_code = cookie($this->userid."_email");
			if ($info['n_code'] != $email_code) {
				$this->error("验证码有误");
			}
			$infos['email_status'] = 1;
			$infos['email'] = $info['email'];
			//判断邮箱是否可用
			$rs = $this->db->where(array('email'=>$info['email'],'userid'=>array('NEQ'=>$this->userid)))->find();
			if($rs){
				$this->error('该邮箱已经被占用，请更换邮箱');
			}

			//判断邮箱是否已经被认证
			if($this->userinfo['email_status'] == 1 && $this->userinfo['email'] == $info['email']){
				$this->error('该邮箱已经认证过');
			}
			$result = $this->db->where(array('userid'=>$this->userid))->save($infos);
			if ($result) {
				if($this->userinfo['email_status'] == 0) {
					runhook('member_attesta_email');
				}
				$url = ($this->modelid == 1) ? U('Member/Attesta/index') : U('Member/Attesta/person');
				$this->success("邮箱认证成功！",$url);
			}
		}else{
			$SEO = seo(0,"邮箱认证");
			$tpl = 'buyer/v2_email_attesta';
			$userinfo = getUserInfo($this->userid);
			include template($tpl);
		}
	}


	/**
	 * 邮箱认证
	 * @author   <master@ .cn>
     * @copyright: adalways inc
     * @date：2016-10-06
     * @version：1.0
     */
	public function v2_phone_attesta(){
		if (IS_POST) {
			$info = I("post.");
			$config = getcache('setting', 'member');		
			$email_code = cookie($this->userid."_email");
			if ($info['n_code'] != $email_code) {
				$this->error("验证码有误");
			}
			$infos['email_status'] = 1;
			$infos['email'] = $info['email'];
			//判断邮箱是否可用
			$rs = $this->db->where(array('email'=>$info['email'],'userid'=>array('NEQ'=>$this->userid)))->find();
			if($rs){
				$this->error('该邮箱已经被占用，请更换邮箱');
			}
			
			//判断邮箱是否已经被认证
			if($this->userinfo['email_status'] == 1 && $this->userinfo['email'] == $info['email']){
				$this->error('该邮箱已经认证过');
			}
			$result = $this->db->where(array('userid'=>$this->userid))->save($infos);
			if ($result) {
				if($this->userinfo['email_status'] == 0) {
					runhook('member_attesta_email');
				}
				$url = ($this->modelid == 1) ? U('Member/Attesta/index') : U('Member/Attesta/person');
				$this->success("邮箱认证成功！",$url);
			}
		}else{
			$SEO = seo(0,"邮箱认证");
			$tpl = 'buyer/v2_phone_attesta';
			$userinfo = getUserInfo($this->userid);
			include template($tpl);
		}
	}

	public function v2_check(){
		$infos = model('member_attesta')->where(array('userid'=>$this->userid,'type'=>'identity'))->find();
        if(empty($infos)){
            $this->error('您还未实名认证，实名认证通过后才可绑定',U('name_attesta'));
        }

        if ($infos && $infos['status'] == 0) {
        	 $this->error('实名认证正在审核中，通过后才可绑定',U('index'));

        }

		//取出用户身份认证的名称
		$identity = model('member_attesta')->where(array('userid'=>$this->userid,'type'=>'identity'))->find();
		$infos = string2array($identity['infos']);
		//判断该用户是否认证过
		$rs = $this->attesta_db->where(array('userid'=>$this->userid,'type'=>'alipay'))->find();
		if($rs){
			$this->error('您已经认证过，请勿再次认证',U('index'));
		}

		$this->success('认证通过');

	}

	public function v2_check_bank(){
		$infos = model('member_attesta')->where(array('userid'=>$this->userid,'type'=>'identity'))->find();
        if(empty($infos)){
            $this->error('您还未实名认证，实名认证通过后才可绑定',U('name_attesta'));
        }

        if ($infos && $infos['status'] == 0) {
        	 $this->error('实名认证正在审核中，通过后才可绑定',U('index'));

        }
		
		//判断该用户是否认证过
		$rs = $this->attesta_db->where(array('userid'=>$this->userid,'type'=>'bank'))->find();
		if($rs){
			$this->error('您已经认证过，请勿再次认证',U('index'));
		}
		$this->success('认证通过');

	}

	public function v2_check_alipay(){
		$infos = model('member_attesta')->where(array('userid'=>$this->userid,'type'=>'alipay'))->find();
        if($infos['status'] == 1){
            $this->error('支付宝已成功绑定，请勿重复绑定',U('index'));
        }
	}

	public function v2_bank_status(){
		$infos = model('member_attesta')->where(array('userid'=>$this->userid,'type'=>'bank'))->find();
        if($infos['status'] == 1){
            $this->error('银行卡已成功绑定，请勿重复绑定',U('index'));
        }else{
        	$this->success('认证通过');
        }
	}

	public function v2_alipay_attesta(){
		$models = getcache('model','commons');
		$tablename = $models[$this->modelid]['tablename'];
		$memberinfo = model($tablename)->find($this->userid);

        $infos = model('member_attesta')->where(array('userid'=>$this->userid,'type'=>'identity'))->find();
        /*if(empty($infos) || $infos['status'] == 0){
            $this->error('您还未通过实名认证，实名认证通过后才可绑定',U('name_attesta'));
        }*/

		//取出用户身份认证的名称
		$identity = model('member_attesta')->where(array('userid'=>$this->userid,'type'=>'identity'))->find();
		$infos = string2array($identity['infos']);
		//判断该用户是否认证过
		$rs = $this->attesta_db->where(array('userid'=>$this->userid,'type'=>'alipay'))->find();
		/*if($rs){
			$this->error('您已经认证过，请勿再次认证');
		}*/
		if (IS_POST) {
			$info = I('post.');
			if (empty($info['account'])) {
				$this->error('请输入支付宝账号');
				return FALSE;
			}
			$email = isemail($info['account']); 
			$phone = is_mobile($info['account']);
			if (!$email && !$phone) {
				$this->error('格式错误');
				return FALSE;
			}
			//判断该支付宝账号是否绑定
			$con = array();
			$con['type'] = array('EQ','alipay');
			$con['infos'] = array('LIKE','%'.$info['account'].'%');
			$attesta_infos =  model('member_attesta')->where($con)->count();
			
			if($attesta_infos > 0){
				$this->error('该支付宝账号已经认证过');
			}
			//将认证信息存入数据库
			$sqlmap = array();
			$arr = array('username'=>$info['name_attesta'],'alipay_account'=>$info['account'],'alipay_code'=>$info['alipay_code']);
			$sqlmap['infos'] = array2string($arr);
			$sqlmap['userid'] = $this->userid;
			$sqlmap['dateline'] = NOW_TIME;
			$sqlmap['status'] = 1;
			$sqlmap['type'] = 'alipay'; 
			$result = $this->attesta_db->add($sqlmap);
			if ($result) {
				model('member')->where(array('userid'=>$this->userid))->setField('alipay_status', 1);	
				$url = ($this->modelid == 1) ? U('Member/Attesta/index') : U('Member/Attesta/person');
				runhook('member_attesta_alipay');
				$this->success('支付宝绑定成功',$url);
			}else{
				$this->error('操作失误，请重试！');
				return FALSE;
			}
		}else{
			$SEO = seo(0,"支付宝绑定");
			$tpl = 'buyer/v2_alipay_attesta';
			include template($tpl);
		}
	}


	public function v2_bank_attesta(){
		$infos = model('member_attesta')->where(array('userid'=>$this->userid,'type'=>'identity'))->find();
		/*if(empty($infos) || $infos['status'] == 0){
			$this->error('您还未通过实名认证，实名认证通过后才可绑定',U('name_attesta'));
		}*/
		
		$account = model('member')->where(array('userid'=>$this->userid))->find();
		/*if ($account['bank_status'] == 1) {
			$this->error('已绑定，请勿重新绑定');
			return FALSE;
		}*/
		$region = model('linkage')->where(array('parentid'=>0,'keyid'=>1))->select();
		//判断用户是否绑定过
		$rs = $this->attesta_db->where(array('userid'=>$this->userid,'type'=>'bank'))->find();
		//查询银行
		$banks = model('linkage')->where(array('parentid'=>0,'keyid'=>3360))->select();
		/*if($rs){
			$this->error('您已经认证过，请勿重新认证');
			return false;
		}*/
		//查询绑定的身份
		$identity = model('member_attesta')->where(array('userid'=>$this->userid,'type'=>'identity'))->find();
		$infos = string2array($identity['infos']);
		if (IS_POST) {
			$info = I('post.');
			$map = array();
			$map['infos'] = array2string($info);
			$map['userid'] = $this->userid;
			$map['dateline'] = NOW_TIME;
			$map['status'] = 1;
			$map['type'] = 'bank';
			if (!is_numeric($info['account']) || strlen($info['account']) < 16 || strlen($info['account']) > 19) {
				$this->error('请输入正确的银行卡号');
				return FALSE;
			}
			//判断该银行账号是否绑定
			$con = array();
			$con['type'] = array('EQ','bank');
			$con['infos'] = array('LIKE','%'.$info['account'].'%');
			$attesta_infos =  model('member_attesta')->where($con)->count();
			
			if($attesta_infos > 0){
				$this->error('该支付宝账号已经认证过');
			}
			//记录该用户绑定的信息
			$result =  $this->attesta_db->add($map);
			if ($result) {
				 model('member')->where(array('userid'=>$this->userid))->setField('bank_status', 1);	
				 $url = ($this->modelid == 1) ? U('Member/Attesta/index') : U('Member/Attesta/person');
				 runhook('member_attesta_bank');
				$this->success('银行绑定成功',$url);
			}else{
				$this->error('操作失误，请重试！');
				return FALSE;
			}
		}else{
			$SEO = seo(0,"银行账户绑定");
			$tpl = 'buyer/v2_bank_attesta';
			include template($tpl);
		}
	}

	public function v2_check_email(){
		$email = $_GET['email'];
		$result = $this->db->where(array('email'=>$email))->find();
		if($result){
			//查看是否为当前会员
			if($result['userid'] == $this->userid && $result['email_status'] == 1){
				$this->error('该邮箱已验证过,请勿重复验证');
			}elseif($result['userid'] == $this->userid && $result['email_status'] != 1){
				$this->success('该邮箱可用');

			}else{
				$this->error('该邮箱已存在');
			}
		}else{
			$this->success('该邮箱可用');
		}
	}
}
