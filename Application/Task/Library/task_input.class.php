<?php
class task_input {
	var $data;
    function __construct() {
		$this->error = '';
		//初始化附件类
		$this->config = model('activity_set')->where(array('activity_type'=>'task'))->getField('key,value');
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
		}
		return $info;
	}
	
	/* 店铺来源 */
	public function source($field, $value) {
		$value = (int) $value;
		if($value < 1) {
			$this->error = '商品来源选择不正确';
			return FALSE;
		}
		return (int) $value;
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
			//判断前台发布商品分数
			$goods_min = $this->config['task_num'];
			if($goods_min){//设置了商品分数限制
				if($goods_min > $value){
					$this->error = '商品份数至少为'.$goods_min;
					return FALSE;
				}
			}
		}
		return $value;
	}

	/* 价格 */
	public function goods_price($field, $value){
		$price_min = $this->config['task_price'];
		if(!defined('IN_ADMIN') && $price_min){
			if($value < $price_min){
				$this->error = '价格至少为'.$price_min;
				return FALSE;
			}
		}
		return sprintf('%.2f', $value);
	}
		
	public function getError() {
		return $this->error;
	}
}