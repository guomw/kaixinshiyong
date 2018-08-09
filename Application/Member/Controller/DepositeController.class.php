<?php
namespace Member\Controller;
use \Admin\Controller\InitController;
class DepositeController extends InitController{
	public function _initialize(){
		parent::_initialize();
		$this->db = model('cash_records');
	}
	public function init(){
		$pagecurr = max(1,I('page',0,'intval'));
		$pagesize = 20;
		$sqlMap = array();
		$info = I('param.');
		$status = (isset($info['status']))	 ? (int) $info['status']: -2;
		$type = (isset($info['type'])) ? (int) $info['type'] : -99;
		if(IS_GET){
			if(isset($info['paypal'])){
				$sqlMap['paypal'] = $info['paypal'];
			}
			if($status > -2){
				$sqlMap['status'] = $status;
			}
			if($type > -99){
				$sqlMap['type'] = $type;
			}
			$keyword = $info['keyword'];
			$info['start_time'] = (!empty($info['start_time'])) ? strtotime($info['start_time']) : 0;
			$info['end_time'] = (!empty($info['end_time'])) ? strtotime($info['end_time']) : 0;
			if ($info['start_time'] && $info['end_time']){
				$sqlMap['inputtime'] = array("BETWEEN",array($info['start_time'],$info['end_time']));
			}else{
				if ($info['start_time'] > 0) {
					$sqlMap['inputtime'] = array("EGT", $info['start_time']);
				}
				if ($info['end_time'] > 0) {
					$sqlMap['inputtime'] = array("ELT", $info['end_time']);
				}
			}
			
			if(isset($info['p_type']) && isset($keyword)){
				if($info['p_type'] == 1){//昵称 
					//查出输入昵称相似的会员
					$rs = model('member')->where(array('nickname'=>array("LIKE","%$keyword%")))->select();
					foreach($rs as $k=>$v){
						$ids[] = $v['userid'];
					}
					$sqlMap['userid'] = array("in",$ids);
				}else if($info['p_type'] == 0){
					$sqlMap['name'] = array("LIKE","%$keyword%");
				}else{
					$sqlMap['userid'] = $keyword;
				}				
			}
		}
		$count = $this->db->where($sqlMap)->count();
		$lists = $this->db->where($sqlMap)->page($pagecurr,$pagesize)->order('inputtime DESC')->select();
		foreach ($lists as $k=>$v){
			//查出用户名、用户组
			$lists[$k]['nickname'] = model('member')->getFieldByUserid($v['userid'],'nickname');
			$lists[$k]['modelid'] =  model('member')->getFieldByUserid($v['userid'],'modelid');
		}
		$pages = page($count,$pagesize);
		$form = new \Common\Library\form();
		include $this->admin_tpl('deposite_lists');
	}
	
	/*审核通过*/
	public function check($id = 0,$success_order=NULL){
		$id = (int) $id;
		if($id < 1) $this->error('参数错误');
		//判断该信息是否已经通过
		$rs = $this->db->where(array('cashid'=> $id))->find();
		if(IS_POST){
			if($rs['status'] != 0) $this->error('该信息已经审核，请勿重新审核','javascript:close_dialog();');
				$sqlmap['status'] =1;
				$sqlmap['check_time'] =time();
			if(isset($success_order)){
			    $sqlmap['success_order'] =$success_order;
			}
			$result = $this->db->where(array('cashid'=>$id))->setField($sqlmap);
			if(!$result) $this->error('审核失败','javascript:close_dialog();');
			runhook('pay_cash_check',array('id' => $id, 'userid' => $rs['userid'],'money' => $rs['money'],'result' => 1,'paypal' => $rs['paypal']));
			$this->success('审核成功','javascript:close_dialog();');
		}
        include $this->admin_tpl('deposite_chek');
	}
	
	/*审核失败*/
	public function uncheck($id = 0){
		$id = (int) $id;
		if($id < 1) $this->error('参数错误');
		//判断该信息是否审核
		$info =  $this->db->where(array('cashid'=>$id))->find();
		if($info['status'] != 0) $this->error('该信息已经审核，请勿重新审核');
		if(IS_POST){
			$cause = trim($_POST['cause']);
			if (!$cause)	$this->error('请填写审核失败原因');
			$name = cookie('admin_username');
			$result = $this->db->where(array('cashid'=>$id))->setField(array('status'=>-1,'cause'=>$cause,'operator'=>$name,'check_time'=>NOW_TIME));
			if(!$result) $this->error('操作失败');
			//退还金额
			$info = $this->db->getByCashid($id);
			
			$sign = '4-2-'.$info['userid'].'-'.$info['money'].'-'.dgmdate($info['inputtime'],'Y-m-d H:i:s');
			$sqlmap = array();
			$sqlmap['only'] = $sign;
			$result = model('member_finance_log')->where($sqlmap)->find();
			if(!$result){
			    action_finance_log($info['userid'],$info['money'],'money','提现未通过返回',$sign,array());
			    runhook('pay_cash_check',array('id' => $id, 'userid' => $info['userid'],'money' => $info['money'],'result' => 0,'cause' => $cause));
			    $this->success('操作成功','javascript:close_dialog();');
			}else{
			    $this->error('操作失败,重复操作');
			}
		}else{
			include $this->admin_tpl('deposite_uncheck');
		}
	}

    /*重新审核处理微信支付*/
    public function check_weixin_deposite($id = 0){
    	$id = (int) $id;
    	if($id < 1) $this->error('参数错误');
    	//判断该信息是否审核
    	$info =  $this->db->where(array('cashid'=>$id))->find();

    	if($info['status'] != -2) $this->error('当前付款状态不对，请勿重新审核');
    	if($info['type'] != 3) $this->error('当前会员不是申请的微信提现');

    	$openid = model('member_oauth')->where(array('uid' =>$info['userid']))->getField('openid');
    	if(!$openid) $this->error('当前会员没有关注平台微信公众号,并且绑定帐号');

    	//发起快速提现申请
    	$deposite = new \Wechat\Pay\lib\WxPayMpaymkttransfers();
    	$wxrs = $deposite->wxpay_deposite($info['cashid'],$openid,$info['totalmoney'],$info['name']);
    	//print_r($wxrs);
    	if($wxrs['result_code'] == 'SUCCESS'){
    	    //标记已成功
    	    model('cash_records')->where(array('cashid' =>$id))->setField(array('status' =>1,'success_order' =>$wxrs['payment_no'],'err_cause'=>'','check_time' =>strtotime($wxrs['payment_time'])));
    	    //写入交易成功订单号
    	    runhook('pay_cash_check',array('id' => $id, 'userid' => $info['userid'],'money' => $info['money'],'result' => 1,'paypal' => $info['paypal']));
    	    $this->success('提现处理成功,资金已实时到提现会员的微信钱包');
    	}else{
    	    //没有实时提现成功
    	    //标记原因
    	    model('cash_records')->where(array('cashid' =>$id))->setField(array('status' =>-2,'err_cause'=>$wxrs['return_msg']));
    	    $this->error('微信支付返回提示：'.$wxrs['return_msg']);
        }
    }

	/*提现详细信息*/
	public function deposite_info($id = 0){
		$id = (int) $id;
		if($id < 1) $this->error('参数错误');
		$info = $this->db->getByCashid($id);
		extract($info);
		$rs = model('member_attesta')->where(array('userid'=>$userid,'type'=>'bank'))->find();
		$infos = string2array($rs['infos']);
		$privince = model('linkage')->getFieldByLinkageid($infos['province'],'name');
		$city = model('linkage')->getFieldByLinkageid($infos['city'],'name');
		$bankname =  model('linkage')->getFieldByLinkageid($infos['bank_name'],'name');
		include $this->admin_tpl('deposite_info');
	}
}