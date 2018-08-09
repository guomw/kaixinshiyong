<?php
/**
 * 9.9包邮产品驱动
 */
namespace Product\Library\Driver;
class postal_product extends \Product\Library\ProductInterface {
	/**
	 * 获取活动设置
	 *  @return array
	 */
	public function getConfig(){
		$result =  model('activity_set')->where(array('activity_type' => $this->product_info['mod']))->getField('key,value', TRUE);
		$result['postal_name'] = (string) $result['postal_name'];
		$result['postal_price_name'] = (string) $result['postal_price_name'];//活动价名称
		$result['seller_charge_name'] = (string) $result['seller_charge_name'];//推广费
		$result['seller_charge_money'] = (int) $result['seller_charge_money'];//收费价格
		$result['seller_start_time'] = (string) $result['seller_start_time'];
		$result['seller_end_time'] = (string) $result['seller_end_time'];
		return $result;
	}
	/**
	 * 9.9包邮检测
	 */
	public function pay_check() {
		$config = $this->getConfig();
		/*---- 限制买家参与 ----*/
		if(empty($this->user_info)) {
			$this->error = '尚未登录';
			return FALSE;
		}
		if($this->user_info['modelid'] != 1) {
			$this->error = '暂时只限买家参与';
			return FALSE;
		}
	
		/*---- 检测商品状态 ----*/
		/* 上架状态 */
		if($this->product_info['status'] != 1) {
			$this->error = '该商品尚未上架';
			return FALSE;
		}
		if($this->product_info['start_time'] > NOW_TIME) {
			$this->error = '该活动尚未开始';
			return FALSE;
		}
		if($this->product_info['end_time'] < NOW_TIME) {
			$this->error = '该活动已经结束';
			return FALSE;
		}
		/* 检测商品库存 */
		if($this->product_info['goods_number'] - $this->product_info['already_num'] < 1) {
			$this->error = '该商品已售罄';
			return FALSE;
		}
		return TRUE;
	}
	/**
	 * 更新库存
	 * @param int       $num    库存数量
	 * @param string    $msg    操作理由
	 */
	public function quantity_update($num = 0, $msg = '') {
		$num = (int)$num;
		if(empty($num)) return FALSE;
		$model = model('product_'.$this->product_info['mod'])->where(array('id' => $this->product_info['id']));
		if($num > 0) {
			$result = $model->setInc('goods_number', $num);
		} else {
			$result = $model->setDec('goods_number', abs($num));
		}
		if($msg) $this->write_log ($this->product_info['status'], $msg);
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
		$info['id'] = $this->product_info['id']; 
		$info['status'] = -1;
		$info['start_time'] = $start_time;
		$info['end_time'] = $start_time + ($this->product_info['activity_days'] * 86400);
		$info['updatetime'] = NOW_TIME;
		$result = model('product')->save($info);
		if(!$result) {
			$this->error = '产品处理失败，请稍后重试';
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
		if($this->product_info['status'] != -2) {
			$this->error = '该商品非待审核状态';
			return FALSE;
		}
		$result = $this->set_status(0, '后台管理员审核失败');
		if(!$result) {
			$this->error = '处理失败，请稍后重试';
			return FALSE;
		}
		// 退还保证金给商家
		if($this->product_info['goods_deposit']) {
		    $sign = '2-'.$this->product_info['mod'].'-'.$this->product_info['company_id'].'-'.$this->product_info['id'].'-6';
		    $rs = model('member_finance_log')->where(array('only'=>$sign))->find();
		    if(!$rs){
		        action_finance_log($this->product_info['company_id'], $this->product_info['goods_deposit'], 'money', '活动审核失败，保证金退还',$sign, array('goods_id' => $this->product_info['id']));
		    }else{
		        return FALSE;
		    }
		}
		return TRUE;
	}
}