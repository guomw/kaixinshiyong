<?php
namespace Task\Factory;
use \Task\Library\TaskInterface;
class task {
    public $task_info = array();
    /**
     * 初始化
     * @param type $pid 商品编号
     * @param type $mod 活动类型
     */
	public function __construct($id = 0) {
		$this->set_adapter($id);
	}
    
    /**
     * 构造适配器
     * @param int $pid 商品ID
     * @return boolean
     */
	public function set_adapter($id) {
        $id = (int) $id;
        $this->task_info = model('task_day')->detail($id);
        if(!$this->task_info) return false;
        $class_path = "\\Task\\Library\\Driver\\task";
        if(!class_exists($class_path)) return FALSE;
        $this->adapter_instance = new $class_path($this->task_info);
        return $this->adapter_instance;
	}
    
	public function __call($method_name, $method_args) {
		if (method_exists($this, $method_name))
			return call_user_func_array(array(& $this, $method_name), $method_args);
		elseif (
			!empty($this->adapter_instance)
			&& ($this->adapter_instance instanceof TaskInterface)
			&& method_exists($this->adapter_instance, $method_name)
		)
		return call_user_func_array(array(& $this->adapter_instance, $method_name), $method_args);
	}    
    
}
