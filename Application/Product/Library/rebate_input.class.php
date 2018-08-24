<?php
class rebate_input {
	var $data;
	var $fields = array(
		'system' => '',
		'model' => '',
	);

    function __construct() {
		$this->error = '';
		//初始化附件类
		$this->groups = getcache('merchant_group', 'member');
		$this->groupid = cookie('_groupid');
		$this->group_info = $this->groups[$this->groupid];
		$this->config_infos = unserialize($this->group_info['config']);   
		$this->rebate = $this->config_infos['rebate'];
    }

	function get($data, $isimport = 0) {
		$this->data = $data = trim_script($data);
		$info = array();
		foreach($data as $field=>$value) {
			$func = $field;
			if(method_exists($this, $func)) $value = $this->$func($field, $value);
			if($this->fields[$field]['issystem']) {
				$info[$field] = $value;
			} else {
				$info[$field] = $value;
			}
			if(!defined('IN_ADMIN')){
				if($field == 'goods_albums'){
					$value = array_shift(string2array($value));
					$info['thumb'] = $value['url'];
				}
			}
		}
		return $info;
	}

	/* 店铺来源 */
	public function source($field, $value) {
		$value = (int) $value;
		if($value < 1) {
			$this->error = '店铺来源选择不正确';
			return FALSE;
		}
		return (int) $value;
	}

	/* 下单方式 */
	public function type($field, $value) {
		if(empty($value)) {
			$this->error=  '下单方式不能为空';
			return FALSE;
		}
		return trim($value);
	}
	
	/* 是否参与亚马逊 */
	public function taobaoke($field, $value){
		$value = (int) $value;
		return ($this->data['source'] < 2) ? $value : 0;
	}

	/* 商品图册 */
	public function goods_albums($field, $value){
		$value = array();
		if($_POST[$field.'_url']) {
			foreach ($_POST[$field.'_url'] as $k => $v) {
				if($v != ''){
					$r['url'] = $v;
					$r['alt'] = $_POST[$field.'_alt'][$k];
					$value[] = $r;
				}
			}
		}
		
		if(empty($value)){
			//$r['url'] = '/static/images/loading.gif';
			$r['url'] = $_POST['info']['thumb'];

			$value[] = $r;
		}
		
		return array2string($value);
	}

	/* 商品份数 */
	public function goods_number($field, $value){
		$value = (int) $value;
		if($value < 1) {
			$this->error = '请输入商品份数';
			return FALSE;
		}
		$group_info = $this->groups[$this->groupid];
		/* 前台发布 */
		if(!defined('IN_ADMIN')) {
			if(empty($group_info)) {
				$this->error = '商家权限获取失败';
				return FALSE;
			}
			//判断前台发布商品分数
			$goods_min = $this->rebate['goods_number']['min'];
			$goods_max = $this->rebate['goods_number']['max'];
			if($this->rebate['goods_number']['radio'] == 1){//设置了商品分数限制
				if($goods_min > $value || $value > $goods_max){
					$this->error = '商品份数应为'.$goods_min.'~'.$goods_max.'之间';
					return FALSE;
				}
			}
		}
		return $value;
	}

	/* 下单规则 */
	public function goods_rule($field, $value) {
		return (is_array($value)) ? array2string($value) : $value;
	}

	/* 下单价格 */
	public function goods_price($field, $value){
		$price_min = $this->rebate['price_range']['min'];
		$price_max = $this->rebate['price_range']['max'];
		if(!defined('IN_ADMIN') && $this->rebate['price_range']['radio'] == 1){
			if($value < $price_min || $value > $price_max){
				$this->error = '下单价格范围是'.$price_min.'~'.$price_max;
				return FALSE;
			}
		}
		return sprintf('%.2f', $value);
	}

	/* 商品折扣 */
	public function goods_discount($field, $value){
		$discount = string2array(C('SELLER_DISCOUNT_RANGE'));
		if(!defined('IN_ADMIN') && ($value < $discount['min'] || $value > $discount['max'])){
			$this->error = '商品的折扣范围是'.$discount['min'].'~'.$discount['max'];
			return FALSE;
		}
		return $value;
	}

	/* 活动时间 */
	public function activity_days($field, $value){
		$value = (int) $value;
		/*前台添加*/
		if(!defined('IN_ADMIN')){
			$activitydays = $this->rebate['activitydays'];
			if($value > $activitydays){
				$this->error = '活动时间不操过'.$activitydays.'天';
				return FALSE;
			}
		}else{
			if($value < 1) {
				$this->error = '活动时间不能小于1天';
				return FALSE;
			}
		}
		return $value;
	}

	/* 商品介绍 */
	public function goods_content($field, $value){
		if(empty($value)){
			$this->error = '商品介绍不能为空';			
		}
		return remove_xss(dstripslashes($value));
	}

	/* 商品地址 */
	public function goods_url($field, $value) {
		$rule = '/^http(s?):\/\/(?:[A-za-z0-9-]+\.)+[A-za-z]{2,4}(?:[\/\?#][\/=\?%\-&~`@[\]\':+!\.#\w]*)?$/';
		if (preg_match($rule, $value) !== 1) {
			$this->error = '下单地址格式不合法';
			return FALSE;
		}
		return $value;
	}
	
	/* 温馨提醒 */
	public function goods_tips($field, $value){
		if(is_array($value)) {
//			$value['order_tip'] = implode(',',$value['order_tip']);
			$value = array2string($value);
		}
		return $value;
	}

	public function service_type($field, $value){
		$value = (int) $value;
		
		return $value;
	}
	

	public function getError() {
		return $this->error;
	}
}