<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Oauth\Factory;
use \Oauth\Library\OauthInterface;
class oauth {
    public $config = array();
    
    public function __construct($type = 'qqconnect') {
        $configs = getcache('setting', 'oauth');
        $this->config = $configs[$type];
        if(!$this->config) return false;
        $this->set_adapter($type);
    }
    
    /**
     * 构造适配器
     * @param string $type 类型
     */
    public function set_adapter($type) {
        $class_path = "\\Oauth\\Library\\Driver\\".$type;
        if(!class_exists($class_path)) return FALSE;
        $this->adapter_instance = new $class_path($this->config);
        return $this->adapter_instance;
    }
    
    /**
     * 魔术方法
     * @param string $method_name
     * @param mixed $method_args
     */
	public function __call($method_name, $method_args) {
		if (method_exists($this, $method_name))
			return call_user_func_array(array(& $this, $method_name), $method_args);
		elseif (
			!empty($this->adapter_instance)
			&& ($this->adapter_instance instanceof OauthInterface)
			&& method_exists($this->adapter_instance, $method_name)
		)
		return call_user_func_array(array(& $this->adapter_instance, $method_name), $method_args);
	}
    
}
