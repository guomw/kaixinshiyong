<?php
class commission_input {
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
		$this->commission = $this->config_infos['commission'];
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

	
	
	

	/* 商品图册 */
	/*public function goods_albums($field, $value){
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
	}*/

	/* 商品份数 */
	public function goods_number($field, $value){
		$value = (int) $value;
		if($value < 1) {
			$this->error = '请输入商品份数';
			return FALSE;
		}
		/* 前台发布 */
		
		return $value;
	}

	/*搜索流程图*/
	public function goods_search_albums_url($field, $value){

		return array2string($value);
	}

	public function ctype($field, $value){
		return array2string($value);
	}

	public function thumb($field, $value){
		return $value;
	}
	
	



	/* 下单价格 */
	public function goods_price($field, $value){
		return sprintf('%.2f', $value);
	}

	/* 掌柜旺旺 */
	public function goods_wangwang($field, $value){
		if(empty($value)){
			$this->error = '掌柜旺旺不能为空';
		}
		return $value;
	}

	public function goods_address($field, $value){
		if(empty($value)){
			$this->error = '商品所在位置不能为空';
		}
		return $value;
	}

	
	
	
	

	/*每单赠送用户红包*/
	public function bonus_price($field, $value){
		$value = (int) $value;
		if($value == 1) {
			$value = $this->data['bonus_price'];
			if(empty($value)) {
				$this->error = '请输入佣金';
				return false;
			}
		}
		
		return $value;
	}
	
	/* 活动时间 */
	public function activity_days($field, $value){
		
		if($value < 1) {
			$this->error = '活动时间不能小于1天';
			return FALSE;
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
	
	
	

	public function getError() {
		return $this->error;
	}
}