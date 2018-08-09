<?php
namespace Product\Factory;
use \Product\Library\ProductInterface;
class product {
    public $product_info = array();
    /**
     * 初始化
     * @param type $pid 商品编号
     * @param type $mod 活动类型
     */
	public function __construct($pid = 0) {
		$this->set_adapter($pid);
	}
    
    /**
     * 构造适配器
     * @param int $pid 商品ID
     * @return boolean
     */
	public function set_adapter($pid) {
        $pid = (int) $pid;
        $this->product_info = model('product/product')->detail($pid);
        if(!$this->product_info) return false;
        $adapter_name = $this->product_info['mod'].'_product';
        $class_path = "\\Product\\Library\\Driver\\".$adapter_name;
        if(!class_exists($class_path)) return FALSE;
        $this->adapter_instance = new $class_path($this->product_info);
        return $this->adapter_instance;
	}
    
	public function __call($method_name, $method_args) {
		if (method_exists($this, $method_name))
			return call_user_func_array(array(& $this, $method_name), $method_args);
		elseif (
			!empty($this->adapter_instance)
			&& ($this->adapter_instance instanceof ProductInterface)
			&& method_exists($this->adapter_instance, $method_name)
		)
		return call_user_func_array(array(& $this->adapter_instance, $method_name), $method_args);
	}    
    
}
