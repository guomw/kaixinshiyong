<?php
/**
 * 支付模块调用工厂
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Pay\Factory;
use Pay\Library\Pay;
class Factory
{
	public function __construct($adapter_name = '', $adapter_config = array(),$product_info=array()) {
		if (MODULE_NAME != 'Pay') load('Pay.function');
		$this->set_adapter($adapter_name, $adapter_config,$product_info);
	}
	/**
	 * 构造适配器
	 * @param  $adapter_name 支付模块code
	 * @param  $adapter_config 支付模块配置
	 */
	public function set_adapter($adapter_name, $adapter_config = array(),$product_info=array()) {
		if (!is_string($adapter_name)) return false;
		else {
			if (empty($adapter_config)) $adapter_config = getcache(strtolower($adapter_name), 'pay');
			$class_name = ucwords($adapter_name);
			$class_path = "\\Pay\\Library\\Driver\\".$class_name;
			$this->adapter_instance = new $class_path($adapter_config,$product_info);
		}
		return $this->adapter_instance;
	}
	
	public function __call($method_name, $method_args) {
		if (method_exists($this, $method_name))
			return call_user_func_array(array(& $this, $method_name), $method_args);
		elseif (
			!empty($this->adapter_instance)
			&& ($this->adapter_instance instanceof Pay)
			&& method_exists($this->adapter_instance, $method_name)
		) 
		return call_user_func_array(array(& $this->adapter_instance, $method_name), $method_args);
	}	
}
?>