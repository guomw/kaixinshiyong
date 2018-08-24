<?php
class trial_input {
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
		$this->trial = $this->config_infos['trial'];
    }

	function get($data, $isimport = 0) {
		$this->data = $data = trim_script($data);
		$info = array();
		foreach($data as $field=>$value) {
			$func = $field;
			//echo 'field---'.$field."<br>";
			//echo 'value---'.$value."<br>";
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
		//die();
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
		/* 前台发布 */
		if(!defined('IN_ADMIN')) {
			if(empty($this->group_info)) {
				$this->error = '商家权限获取失败';
				return FALSE;
			}
			//判断前台发布商品分数
			$goods_min = $this->trial['goods_number']['min'];
			$goods_max = $this->trial['goods_number']['max'];
			if($this->trial['goods_number']['radio'] == 1){//设置了商品分数限制
				if($goods_min > $value || $value > $goods_max){
					$this->error = '商品份数应为'.$goods_min.'~'.$goods_max.'之间';
					return FALSE;
				}
			}
		}
		return $value;
	}

	/*搜索流程图*/
	public function goods_search_albums($field, $value){
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
		return array2string($value);
	}
	/* 下单规则 */
	public function goods_rule($field, $value) {
		return (is_array($value)) ? array2string($value) : $value;
	}

	/* 下单价格 */
	public function goods_price($field, $value){
		return sprintf('%.2f', $value);
	}

	/* 预存邮费 */
	public function goods_postage($field, $value){
		$value = (int) $value;
		return $value;
	}
	
	/*最终试用商品*/
/*	public function goods_tryproduct($field, $value){
		if(empty($value)) {
				$this->error = '请输入试用商品';
				return false;
		}
		return $value;
	}*/
	
	/*vip免审*/
	public function goods_vipfree($field, $value){
		$value = (int) $value;
		/* if($value == 1) {
			$value = $this->data['field_goods_tryproduct'];
			if(empty($value)) {
				$this->error = '请输入试用商品';
				return false;
			}
		} */
		return $value;
	}
	
	/*授权待审*/
	public function goods_impower($field,$value){
		$value = (int) $value;
		return $value;
	}
	
	/*每单赠送用户红包*/
	public function goods_bonus($field, $value){
		$value = (int) $value;
		if($value == 1) {
			$value = $this->data['goods_bonus'];
			if(empty($value)) {
				$this->error = '请输入红包金额';
				return false;
			}
		}
		/*前台添加*/
		if(!defined('IN_ADMIN')){
			if(!empty($value)){
				$bonus_price = string2array(C_READ('bonus_price'));
				if($value < $bonus_price['min'] || $value > $bonus_price['max']){
					$this->error = '红包的金额范围是'.$bonus_price['min'].'~'.$bonus_price['max'];
				}
			}
		}
		return $value;
	}
	
	/* 活动时间 */
	public function activity_days($field, $value){
		$value = (int) $value;
		/*前台添加*/
		if(!defined('IN_ADMIN')){
			$activitydays = $this->trial['activitydays'];
			if($activitydays && $value > $activitydays){
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
			return false;
		}
		return $value;
	}
	
	/* 温馨提醒 */
	public function goods_tips($field, $value){
		if(is_array($value)) {
			$value = array2string($value);
		}
		return $value;
	}
	

	public function getError() {
		return $this->error;
	}
}