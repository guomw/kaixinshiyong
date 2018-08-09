<?php
namespace Pay\Controller;
use Admin\Controller\InitController;
use Pay\Library\Method;
if (!defined('PAY_CONF')) define('PAY_CONF', APP_PATH.'Pay/conf/');
class CountController extends InitController{
	public function _initialize(){
		parent::_initialize();
		
	}


	public function init(){  

	}

	public function order_apply(){
		$sqlmap = array();
		$param = I('param.');
       	$search = $param['search'];
		if (isset($search)) {
            $start_time = strtotime($search['start_time']);
            $end_time = strtotime($search['end_time']);
	        $sqlmap['time'] = array('between',array($start_time,$end_time));

			
       		/*if(isset($search['start_time']) && !empty($search['start_time'])) {
                $start_time = strtotime($search['start_time']. '00:00:00');
                $sqlmap['time'] = array("EGT", $start_time);
            }
            
            if(isset($search['end_time']) && !empty($search['end_time'])) { 
                $end_time = strtotime($search['end_time'].' 23:59:59');
                $sqlmap['time'] = array("ELT", $end_time);
            }*/

		}else{
			$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		    $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;

			$search['start_time'] = dgmdate($beginToday,'Y-m-d');
			$search['end_time'] = dgmdate($endToday,'Y-m-d');

	        $sqlmap['time'] = array('between',array($beginToday,$endToday));

		}


		$apply_trial_count = model('day_count')->sum('day_trial_Apply');
		$apply_phone_count = model('day_count')->sum('day_trial_Apply_phone');
		$apply_rebate_count = model('day_count')->sum('day_rebate_Qualifications');
		$apply_commission_count = model('day_count')->sum('day_commission_Apply_phone');
		$apply_rebate_phone = model('day_count')->sum('day_rebate_phone_Qualifications');

		
	    $lists = model('day_count')->where($sqlmap)->select();
        $form = new \Common\Library\form();
		include $this->admin_tpl('order_apply');
	}

	public function order_complete(){
		$sqlmap = array();
		$param = I('param.');
       	$search = $param['search'];
		if (isset($search)) {
			$start_time = strtotime($search['start_time']);
            $end_time = strtotime($search['end_time']);
	        $sqlmap['time'] = array('between',array($start_time,$end_time));

			/*
       		if(isset($search['start_time']) && !empty($search['start_time'])) {
                $start_time = strtotime($search['start_time']. '00:00:00');
                $sqlmap['time'] = array("GT", $start_time);
            }
            
            if(isset($search['end_time']) && !empty($search['end_time'])) { 
                $end_time = strtotime($search['end_time'].' 23:59:59');
                $sqlmap['time'] = array("LT", $end_time);
            }
*/
		}else{
			$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		    $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;

			$search['start_time'] = dgmdate($beginToday,'Y-m-d');
			$search['end_time'] = dgmdate($endToday,'Y-m-d');

	        $sqlmap['time'] = array('between',array($beginToday,$endToday));

		}
		
		$compelte_trial_count = model('day_count')->sum('day_trial_Complete');
		$compelte_rebate_count = model('day_count')->sum('day_rebate_Complete');
		$compelte_task_count = model('day_count')->sum('day_task_Complete');
		$compelte_commisison_count = model('day_count')->sum('day_commission_Complete');



	    $lists = model('day_count')->where($sqlmap)->select();
        $form = new \Common\Library\form();
		include $this->admin_tpl('order_complete');
	}


	public function order_register(){
		$sqlmap = array();
		$param = I('param.');
       	$search = $param['search'];
		if (isset($search)) {
			$start_time = strtotime($search['start_time']);
            $end_time = strtotime($search['end_time']);
	        $sqlmap['time'] = array('between',array($start_time,$end_time));

		}else{
			$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		    $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;

			$search['start_time'] = dgmdate($beginToday,'Y-m-d');
			$search['end_time'] = dgmdate($endToday,'Y-m-d');

	        $sqlmap['time'] = array('between',array($beginToday,$endToday));

		}

		$register_count = model('day_count')->sum('day_reg');
		
	    $lists = model('day_count')->where($sqlmap)->select();
        $form = new \Common\Library\form();
		include $this->admin_tpl('order_register');
	}


	public function new_add(){
		$sqlmap = array();
		$param = I('param.');
       	$search = $param['search'];
		if (isset($search)) {
			$start_time = strtotime($search['start_time']);
            $end_time = strtotime($search['end_time']);
	        $sqlmap['time'] = array('between',array($start_time,$end_time));

		}else{
			$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		    $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;

			$search['start_time'] = dgmdate($beginToday,'Y-m-d');
			$search['end_time'] = dgmdate($endToday,'Y-m-d');

	        $sqlmap['time'] = array('between',array($beginToday,$endToday));

		}

	    $new_trial_count = model('day_count')->sum('day_trial_new_mum');
		$new_rebate_count = model('day_count')->sum('day_rebate_new_mum');
/*		$new_task_count = model('day_count')->sum('day_rebate_new_mum');
*/		$new_commisison_count = model('day_count')->sum('day_commission_new_mum');

		
	    $lists = model('day_count')->where($sqlmap)->select();
        $form = new \Common\Library\form();
		include $this->admin_tpl('new_add');
	}


	public function wait_ing(){
		$sqlmap = array();
		$param = I('param.');
       	$search = $param['search'];
		if (isset($search)) {
			$start_time = strtotime($search['start_time']);
            $end_time = strtotime($search['end_time']);
	        $sqlmap['time'] = array('between',array($start_time,$end_time));

		}else{
			$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		    $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;

			$search['start_time'] = dgmdate($beginToday,'Y-m-d');
			$search['end_time'] = dgmdate($endToday,'Y-m-d');

	        $sqlmap['time'] = array('between',array($beginToday,$endToday));

		}

		$wait_trial_count = model('day_count')->sum('day_trial_Stay_mum');
		$wait_rebate_count = model('day_count')->sum('day_rebate_Stay_mum');
		$wait_task_count = model('day_count')->sum('day_task_Stay_mum');
		$wait_commisison_count = model('day_count')->sum('day_commission_Stay_mum');
		
	    $lists = model('day_count')->where($sqlmap)->select();
        $form = new \Common\Library\form();
		include $this->admin_tpl('wait_ing');
	}

    /*今日进行中的活动*/
	public function activity_ing(){
		$sqlmap = array();
		$param = I('param.');
       	$search = $param['search'];
		if (isset($search)) {
			$start_time = strtotime($search['start_time']);
            $end_time = strtotime($search['end_time']);
	        $sqlmap['time'] = array('between',array($start_time,$end_time));

		}else{
			$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		    $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
			$search['start_time'] = dgmdate($beginToday,'Y-m-d');
			$search['end_time'] = dgmdate($endToday,'Y-m-d');
	        $sqlmap['time'] = array('between',array($beginToday,$endToday));
		}

		$ing_trial_count = model('product')->where(array('mod' =>'trial','status' =>1))->count();
		$ing_rebate_count = model('product')->where(array('mod' =>'rebate','status' =>1))->count();
		$ing_task_count = model('task_day')->where(array('status' =>1))->count();
		$ing_commisison_count = model('product')->where(array('mod' =>'commisison','status' =>1))->count();
        $form = new \Common\Library\form();
		include $this->admin_tpl('activity_ing');
	}

    /*今日下架活动*/
	public function end_ing(){
		$sqlmap = array();
		$param = I('param.');
       	$search = $param['search'];
		if (isset($search)) {
			$start_time = strtotime($search['start_time']);
            $end_time = strtotime($search['end_time']);
	        $sqlmap['time'] = array('between',array($start_time,$end_time));
		}else{
			$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		    $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
			$search['start_time'] = dgmdate($beginToday,'Y-m-d');
			$search['end_time'] = dgmdate($endToday,'Y-m-d');
	        $sqlmap['time'] = array('between',array($beginToday,$endToday));
		}

		$end_trial_count = model('day_count')->sum('day_trial_end_mum');
		$end_rebate_count = model('day_count')->sum('day_rebate_end_mum');
		$end_task_count = model('day_count')->sum('day_task_end_mum');
		$end_commisison_count = model('day_count')->sum('day_commission_end_mum');
		
	    $lists = model('day_count')->where($sqlmap)->select();
        $form = new \Common\Library\form();
		include $this->admin_tpl('end_ing');
	}

	public function add_money(){
		$sqlmap = array();
		$param = I('param.');
       	$search = $param['search'];
		if (isset($search)) {
			$start_time = strtotime($search['start_time']);
            $end_time = strtotime($search['end_time']);
	        $sqlmap['time'] = array('between',array($start_time,$end_time));

		}else{
			$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		    $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;

			$search['start_time'] = dgmdate($beginToday,'Y-m-d');
			$search['end_time'] = dgmdate($endToday,'Y-m-d');

	        $sqlmap['time'] = array('between',array($beginToday,$endToday));

		}

		$money_trial_count = sprintf('%.2f',model('day_count')->sum('day_trial_Pay_deposit'));
		$money_rebate_count = sprintf('%.2f',model('day_count')->sum('day_rebate_Pay_deposit'));
		$money_task_count = sprintf('%.2f',model('day_count')->sum('day_task_Pay_deposit'));
		$money_commisison_count = sprintf('%.2f',model('day_count')->sum('day_commission_Pay_deposit'));
		
	    $lists = model('day_count')->where($sqlmap)->select();
        $form = new \Common\Library\form();
		include $this->admin_tpl('add_money');
	}

	public function back_money(){
		$sqlmap = array();
		$param = I('param.');
       	$search = $param['search'];
		if (isset($search)) {
			$start_time = strtotime($search['start_time']);
            $end_time = strtotime($search['end_time']);
	        $sqlmap['time'] = array('between',array($start_time,$end_time));

		}else{
			$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		    $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;

			$search['start_time'] = dgmdate($beginToday,'Y-m-d');
			$search['end_time'] = dgmdate($endToday,'Y-m-d');

	        $sqlmap['time'] = array('between',array($beginToday,$endToday));

		}

		$back_trial_count = sprintf('%.2f',model('day_count')->sum('day_trial_refund_deposit'));
		$back_rebate_count = sprintf('%.2f',model('day_count')->sum('day_rebate_refund_deposit'));
		$back_task_count = sprintf('%.2f',model('day_count')->sum('day_task_refund_deposit'));
		$back_commisison_count = sprintf('%.2f',model('day_count')->sum('day_commission_refund_deposit'));
		
		
	    $lists = model('day_count')->where($sqlmap)->select();  
        $form = new \Common\Library\form();
		include $this->admin_tpl('back_money');
	} 

	public function payoff(){
		$sqlmap = array();
		$param = I('param.');
       	$search = $param['search'];
		if (isset($search)) {
			$start_time = strtotime($search['start_time']);
            $end_time = strtotime($search['end_time']);
	        $sqlmap['time'] = array('between',array($start_time,$end_time));

		}else{
			$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		    $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;

			$search['start_time'] = dgmdate($beginToday,'Y-m-d');
			$search['end_time'] = dgmdate($endToday,'Y-m-d');

	        $sqlmap['time'] = array('between',array($beginToday,$endToday));

		}

		$buyer_count = sprintf('%.2f',model('day_count')->sum('day_Buyers_vip'));
		$seller_count = sprintf('%.2f',model('day_count')->sum('day_seller_vip'));
		$trial_count = sprintf('%.2f',model('day_count')->sum('day_trial_Complete_Counter_Fee'));
		$rebate_count =sprintf('%.2f', model('day_count')->sum('day_rebate_Complete_Counter_Fee'));
		$commission_count = sprintf('%.2f',model('day_count')->sum('day_commission_Complete_Counter_Fee'));

		
	    $lists = model('day_count')->where($sqlmap)->select();
        $form = new \Common\Library\form();
		include $this->admin_tpl('payoff');
	} 

	public function end_money(){
		$sqlmap = array();
		$param = I('param.');
       	$search = $param['search'];
		if (isset($search)) {
			$start_time = strtotime($search['start_time']);
            $end_time = strtotime($search['end_time']);
	        $sqlmap['time'] = array('between',array($start_time,$end_time));

		}else{
			$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		    $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;

			$search['start_time'] = dgmdate($beginToday,'Y-m-d');
			$search['end_time'] = dgmdate($endToday,'Y-m-d');

	        $sqlmap['time'] = array('between',array($beginToday,$endToday));

		}


		$end_money_trial = sprintf('%.2f',model('day_count')->sum('day_trial_Complete_money'));
		$end_money_rebate = sprintf('%.2f',model('day_count')->sum('day_rebate_Complete_money'));
		$end_money_task = sprintf('%.2f',model('day_count')->sum('day_commission_Complete_money'));
		$end_money_bonus = sprintf('%.2f',model('day_count')->sum('day_trial_Red'));


		
	    $lists = model('day_count')->where($sqlmap)->select();
        $form = new \Common\Library\form();
		include $this->admin_tpl('end_money');
	} 


	public function cash(){
		$sqlmap = array();
		$param = I('param.');
       	$search = $param['search'];
		if (isset($search)) {
			$start_time = strtotime($search['start_time']);
            $end_time = strtotime($search['end_time']);
	        $sqlmap['time'] = array('between',array($start_time,$end_time));

		}else{

			$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		    $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
			$search['start_time'] = dgmdate($beginToday,'Y-m-d');
			$search['end_time'] = dgmdate($endToday,'Y-m-d');
	        $sqlmap['time'] = array('between',array($beginToday,$endToday));

		}


	

		$cash_apply = sprintf('%.2f',model('day_count')->sum('day_cash_Apply'));
		$cash_success = sprintf('%.2f',model('day_count')->sum('day_cash_Success'));
		$cash_failure = sprintf('%.2f',model('day_count')->sum('day_cash_Failure'));

	    $lists = model('day_count')->where($sqlmap)->select();
        $form = new \Common\Library\form();
		include $this->admin_tpl('cash');

	}

	public function recharge(){
		$sqlmap = array();
		$param = I('param.');
       	$search = $param['search'];
		if (isset($search)) {
			$start_time = strtotime($search['start_time']);
            $end_time = strtotime($search['end_time']);
	        $sqlmap['time'] = array('between',array($start_time,$end_time));

		}else{
			$beginToday=mktime(0,0,0,date('m'),date('d'),date('Y'));
		    $endToday=mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1;
			$search['start_time'] = dgmdate($beginToday,'Y-m-d');
			$search['end_time'] = dgmdate($endToday,'Y-m-d');
	        $sqlmap['time'] = array('between',array($beginToday,$endToday));
		}
		$on_line = sprintf('%.2f',model('day_count')->sum('day_On-line_pay'));
		$admin_pay = sprintf('%.2f',model('day_count')->sum('day_admin_pay'));
		$putong = sprintf('%.2f',model('day_count')->sum('day_Ordinary_pay'));
	    $lists = model('day_count')->where($sqlmap)->select();
        $form = new \Common\Library\form();
		include $this->admin_tpl('recharge');
	}




	
}