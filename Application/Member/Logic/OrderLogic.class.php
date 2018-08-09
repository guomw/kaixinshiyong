<?php
namespace Member\Logic;
use Think\Model;
class OrderLogic extends Model{
	public function __construct() {
		//初始化附件类
		$this->db = model('Order');
		$this->error = '';
	}
	
	/* 统计多个订单时的总价格 */
	public function order_counts($oids,$type='rebate'){
		if (!$oids) {
			$this->error = '订单不存在';
			return FALSE;
		}
		// 查出该订单的商品id
		$goods_ids = array();
		foreach ($oids as $key => $oid) {
			$factory = new \Product\Factory\order($oid);
			if($factory->order_info['status'] != 5) {
	            $this->error = '当前订单不是已审核通过状态';
	            return FALSE;
	            break;
	        }
			$goods_ids[$key] = $this->db->where(array('id'=>$oid))->getField("goods_id");
		}
		// 购物返利、免费试用、九块九包邮的算法不同
		$moneys = '';
		if ($type == 'rebate') {
			foreach ($goods_ids as $key => $goods_id) {
				$factory = new \Product\Factory\product($goods_id);
				$moneys += sprintf('%.2f', ($factory->product_info['goods_price'] - ($factory->product_info['goods_price'] * $factory->product_info['goods_discount'] / 10)));
			}
			return sprintf('%.2f',$moneys);
		}elseif ($type == 'trial'){
			foreach ($goods_ids as $key => $goods_id) {
				$moneys += model('product_'.$type)->where(array('id'=>$goods_id))->getField('goods_price');
			}
			return sprintf('%.2f',$moneys);
		}else{
			$this->error = '商品模型有误!';
			return FALSE;
		}
	}

	public function getError() {
        return $this->error;
    }
}
?>