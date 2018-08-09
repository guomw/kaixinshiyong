<?php 
class order
{
	public function buyer_list($attr) {
		$sqlmap = array();
        if(isset($attr['goods_id']) && !empty($attr['goods_id'])) {
            $sqlmap['goods_id'] = $attr['goods_id'];
        }
        if(isset($attr['mod']) && !empty($attr['mod'])) {
        	$sqlmap['act_mod'] = $attr['mod'];
        }
        $sqlmap['status'] = array("GT", 1);
        $buyer_ids = model('order')->where($sqlmap)->order('id DESC')->limit($attr['limit'])->getField('buyer_id', TRUE);
        return $buyer_ids;
	}
}