<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Member\Controller;
use \Member\Controller\InitController;
class SmsController extends InitController {
	
	public function _initialize() {
		parent::_initialize();
		$this->sms_db=D('Sms_report');
		$this->db = D('Member');
	}
	
	public function get_code($phone,$msg,$type){
		$userinfo=$this->db->where(array('userid'=>$this->userid))->find();
		if (!is_mobile($phone)) $this->error('请填写正确的手机号码');
		$code=random(6,1);
		//发送手机短信验证码
		$sms = new \Sms\Api\SmsApi();
		$pay=$sms->send($phone,$msg.$code);
		if ($pay) {
			$info['id_code']  = $code;
			$info['userid']   = $this->userid;
			$info['msg']      = $msg.$code;
			$info['posttime'] = NOW_TIME;
			$info['status']   = '1';
			$info['mobile']   = $phone;
			$info['enum']     = $type;
			$this->sms_db->add($info);
			$this->success($code);
		}else{
			$this->error($pay);
		}
	}
}