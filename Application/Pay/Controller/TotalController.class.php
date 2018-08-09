<?php
namespace Pay\Controller;
use Admin\Controller\InitController;
use Pay\Library\Method;
if (!defined('PAY_CONF')) define('PAY_CONF', APP_PATH.'Pay/conf/');
class TotalController extends InitController{
	public function _initialize(){
		parent::_initialize();
		
	}

	public function init(){
		$form = new \Common\Library\form();
		$form = new \Common\Library\form();
		/*商家总保证金 : 商品id大于0  and 订单id等于0 and 金额 小于0*/
		$sqlmap = array();
		$sqlmap['p.goods_id'] = array('GT','0');
		$sqlmap['p.order_id'] = array('EQ','0');
		$sqlmap['p.num'] = array('LT','0');
		$sqlmap['t.modelid'] = array('EQ','2');
		$total_deposit = model('member_finance_log')->where()->select();
		$ids = model('member_finance_log')->alias('p')->join(C('DB_PREFIX').'member AS t ON p.userid = t.userid')->where($sqlmap)->SUM('p.num');
		$total_deposit = explode('-', $ids);
		$total_deposit = $total_deposit[1];
		/*总收取了商家vip费用 : 商品di等于0 订单id等于0 `only` LIKE  '%5-2%'*/

		$vipmap = array();
		$vipmap['p.goods_id'] = array('EQ','0');
		$vipmap['p.order_id'] = array('EQ','0');
		$vipmap['p.num'] = array('LT','0');
		$vipmap['t.modelid'] = array('EQ','2');
		$vipmap['p.only'] = array('like','%5-2%');
		$seller_vips = model('member_finance_log')->alias('p')->join(C('DB_PREFIX').'member AS t ON p.userid = t.userid')->where($vipmap)->SUM('p.num');
		$seller_vips = explode('-', $seller_vips);
		$seller_vips = $seller_vips[1];

		/*总收取买家vip费用: 商品di等于0 订单id等于0 `only` LIKE  '%5-1%'*/

		$buyermap = array();
		$buyermap['p.goods_id'] = array('EQ','0');
		$buyermap['p.order_id'] = array('EQ','0');
		$buyermap['p.num'] = array('LT','0');
		$buyermap['t.modelid'] = array('EQ','1');
		$buyermap['p.only'] = array('like','%5-1%');
		$buyer_vips = model('member_finance_log')->alias('p')->join(C('DB_PREFIX').'member AS t ON p.userid = t.userid')->where($buyermap)->SUM('p.num');
		$buyer_vips = explode('-', $buyer_vips);
		$buyer_vips = $buyer_vips[1];


		/*总返还给会员费用:商品id大于0 and 订单id大于0 and 金额大于0 */
		$buyermoney = array();
		$buyermoney['p.goods_id'] = array('GT','0');
		$buyermoney['p.order_id'] = array('GT','0');
		$buyermoney['p.num'] = array('GT','0');
		$buyermoney['t.modelid'] = array('EQ','1');
		$buyer_money = model('member_finance_log')->alias('p')->join(C('DB_PREFIX').'member AS t ON p.userid = t.userid')->where($buyermoney)->SUM('p.num');


		/*总退还商家保证金 商品id大于0  and 订单id等于0 and 金额 大于*/
		$sellermoney = array();
		$sellermoney['p.goods_id'] = array('GT','0');
		$sellermoney['p.order_id'] = 0;
		$sellermoney['p.num'] = array('GT','0');
		$sellermoney['t.modelid'] = array('EQ','2');
		$seller_money = model('member_finance_log')->alias('p')->join(C('DB_PREFIX').'member AS t ON p.userid = t.userid')->where($sellermoney)->SUM('p.num');

		/*总成功处理提现多少元: 将提现记录里面已经成功的提现累计相加*/
		$cash_total = model('cash_records')->where(array('status'=>1))->SUM('totalmoney');

		/*待处理提现多少元:将提现中的待审核提现的累计相加*/
		$have_cash = model('cash_records')->where(array('status'=>0))->SUM('totalmoney');

		/*会员账户余额累计*/
		$member_toatl = model('member')->SUM('money');
		include $this->admin_tpl('total_detail');
	}

	
}