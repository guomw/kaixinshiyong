<?php
namespace Product\Factory;
use \Product\Library\OrderInterface;
class order {
    public $order_info = array();
    public $product_info = array();
    /**
     * 初始化
     * @param type $oid 订单编号
     * @param type $mod 活动类型
     */
	public function __construct($oid = 0) {
		$this->set_adapter($oid);
	}
    
	/**
	 * 构造适配器
	 * @param  $adapter_name 支付模块code
	 * @param  $adapter_config 支付模块配置
	 */
	public function set_adapter($oid) {
        $oid = (int) $oid;
        $this->order_info = model('order')->find($oid);
        if ($this->order_info['act_mod']=='trial') {
        	$this->order_info['trial_report'] = (model('trial_report')->where(array('order_id'=>$oid,'goods_id'=>$this->order_info['goods_id']))->find());
        }
        if(!$this->order_info) {

            if(!model('error')->where(array('order' =>$oid ))->getField('order')){
                    $sqlmap['title'] = '订单id'.$oid.'不存在';
                    $sqlmap['dateline'] = time();
                    $sqlmap['ip'] = get_client_ip();
                    $sqlmap['content'] = '订单id'.$oid.'不存在';
                    $sqlmap['order'] = $oid;
                    model('error')->where($sqlmap)->add();

            }


        	return false;

        }

        $this->product_info = model('product/product')->detail($this->order_info['goods_id']);
        if(!$this->product_info) {

            if(!model('error')->where(array('goods_id' => $this->order_info['goods_id'] ))->getField('goods_id')){
                        $sqlmap['title'] = '商品id'.$this->order_info['goods_id'].'不存在';
                        $sqlmap['dateline'] = time();
                        $sqlmap['ip'] = get_client_ip();
                        $sqlmap['content'] = '订单id'.$oid.'当中商品id'.$this->order_info['goods_id'].'不存在';
                        $sqlmap['goods_id'] = $this->order_info['goods_id'];

                        model('error')->where($sqlmap)->add();

            }

        	 return false;

        }
        $adapter_name = $this->product_info['mod'].'_'.'order';
        $class_path = "\\Product\\Library\\Driver\\".$adapter_name;
        if(!class_exists($class_path)) return FALSE;
        $this->adapter_instance = new $class_path($this->order_info, $this->product_info);
        return $this->adapter_instance;
	}
    
	public function __call($method_name, $method_args) {
		if (method_exists($this, $method_name))
			return call_user_func_array(array(& $this, $method_name), $method_args);
		elseif (
			!empty($this->adapter_instance)
			&& ($this->adapter_instance instanceof OrderInterface)
			&& method_exists($this->adapter_instance, $method_name)
		) 
		return call_user_func_array(array(& $this->adapter_instance, $method_name), $method_args);
	}    
    
}
