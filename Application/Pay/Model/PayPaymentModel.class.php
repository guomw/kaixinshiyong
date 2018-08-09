<?php
namespace Pay\Model;
use Think\Model;
class PayPaymentModel extends Model{
	/*自动验证*/
	protected $_validate = array ();

	/* 自动完成 */
	protected $_auto = array ();

	public function build_cache($item = FALSE) {
		$rows = $this->where(array('enabled' => '1'))->select();
		if ($item === TRUE && $rows) {
			$item_info = array();
			foreach ($rows as $r) {
				$pay_code = strtolower($r['pay_code']);
				$pay_config = string2array($r['config']);
				foreach ($pay_config as $k => $v) {
					$item_info[$k] = $v['value'];
				}
				if (!empty($item_info)) 
					setcache($pay_code, $item_info, 'pay');
				else
					delcache($pay_code, 'pay'); 
			}
		}
		return setcache('payment', $rows, 'pay');
	}
}