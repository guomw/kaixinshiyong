<?php
namespace Commission\Factory;
use \Commission\Library\CommissionInterface;
class commission {
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
        $this->product_info = model('commission/commission')->detail($pid);
        if(!$this->product_info) return false;
        $class_path = "\\Commission\\Library\\Driver\\commission";
        if(!class_exists($class_path)) return FALSE;
        $this->adapter_instance = new $class_path($this->product_info);
        return $this->adapter_instance;
	}
    
	public function __call($method_name, $method_args) {
		if (method_exists($this, $method_name))
			return call_user_func_array(array(& $this, $method_name), $method_args);
		elseif (
			!empty($this->adapter_instance)
			&& ($this->adapter_instance instanceof CommissionInterface)
			&& method_exists($this->adapter_instance, $method_name)
		)
		return call_user_func_array(array(& $this->adapter_instance, $method_name), $method_args);
	}    
    
}
