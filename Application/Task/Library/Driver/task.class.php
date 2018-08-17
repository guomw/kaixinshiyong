<?php
/**
 * 日赚任务驱动
 */
namespace Task\Library\Driver;
class task extends \Task\Library\TaskInterface {
	/**
	 * 获取活动设置
	 *  @return array
	 */
	public function getConfig(){
		$result =  model('activity_set')->where(array('activity_type' => 'task'))->getField('key,value', TRUE);
		$result['task_price'] =  $result['task_price'];
		$result['task_num'] = (int) $result['task_num'];
		return $result;
	}
	
	/**
	 * 更新库存
	 * @param int       $num    库存数量
	 * @param string    $msg    操作理由
	 */
	public function quantity_update($num = 0, $msg = '') {
		$num = (int)$num;
		if(empty($num)) return FALSE;
		$model = model('task_day')->where(array('id' => $this->task_info['id']));
		if($num > 0) {
			$result = $model->setInc('goods_number', $num);
		} else {
			$result = $model->setDec('goods_number', abs($num));
		}
		if($msg) $this->write_log ($this->task_info['status'], $msg);
		return TRUE;
	}
	/**
	 * 审核通过
	 * @param int       $start_time 商品上线时间
	 * @param string    $msg        操作理由
	 * @return boolean
	 */
	public function pass($start_time = 0, $msg = '商品审核成功') {
		$start_time = (!is_numeric($start_time)) ? strtotime($start_time) : (int) $start_time;
		if($start_time < NOW_TIME) {
			$this->error = '上线时间只能在当前时间之后';
			return FALSE;
		}
		$info = array();
		$info['id'] = $this->task_info['id']; 
		$info['status'] = -1;
		$info['start_time'] = $start_time;
		$info['updatetime'] = NOW_TIME;
		$result = model('task_day')->save($info);
		if(!$result) {
			$this->error = '任务处理失败，请稍后重试';
			return FALSE;
		}
		$this->write_log(-1, $msg);
		return TRUE;
	}
	
	/**
	 * 审核失败
	 * @param string $msg 操作理由
	 * @return boolean
	 */
	public function refuse($msg = '') {
		if($this->task_info['status'] != -2) {
			$this->error = '该商品非待审核状态';
			return FALSE;
		}
		$result = $this->set_status(0, '后台管理员审核失败');
		if(!$result) {
			$this->error = '处理失败，请稍后重试';
			return FALSE;
		}
		// 退还佣金给商家
		if($this->task_info['totalmoney']) {
		    $sign = '2-3-'.$this->task_info['company_id'].'-'.$this->task_info['id'].'-3';
		    $rs = model('member_finance_log')->where(array('only'=>$sign))->find();
		    if(!$rs){
		        action_finance_log($this->task_info['company_id'], $this->task_info['totalmoney'], 'money', '审核失败，任务佣金退还',$sign, array('goods_id' => $this->task_info['id']));
		    }else{
		        $this->error = '审核失败，重复操作';
		        return FALSE;
		    }
		}
		$this->write_log(0, '审核失败'.$msg);
		return TRUE;
	}
	
	/**
	 * 平台确认付款
	 */
	public function admin_pay(){
		if (!defined('IN_ADMIN')) {
			$this->error = '您无权确认付款';
			return FALSE;
		}
		//判断佣金是否正确
		$money = $this->task_info['goods_price'] * $this->task_info['goods_number'];
		$money = sprintf('%.2f', $money);
		if($money != $this->task_info['totalmoney']){
			$this->error = '金额不正确';
			return false;
		}
		//扣除商家的余额（活动保证金）
		$userid = $this->task_info['company_id'];
		//用户当前余额
		$balance = model('member')->getFieldByUserid($userid,'money');
		if($balance < $money){
		    $this->error = '商家账户余额不足，请充值！当前余额'.$balance.'美元,<span style="color:red;">还需充值'.($money-$balance).'美元</span>';
		    return FALSE;
		}
		
		$sign = '2-3-'.$userid.'-'.$this->task_info['id'].'-2';
		$rs = model('member_finance_log')->where(array('only'=>$sign))->find();
		if(!$rs){
		    action_finance_log($userid,-$money,'money',"后台专员代扣除活动（".$this->task_info['title']."）保证金：".$money."。",$sign);
		}else{
		    $this->error = '平台确认付款失败，重复操作';
		    return FALSE;
		}
		
		//将保证金写入商家保证金字段
		model('member_merchant')->where(array('userid'=>$this->task_info['company_id']))->setInc('frozen_deposit',$money);
		$this->write_log(-1, '平台确认付款');
		return TRUE;
	}
}