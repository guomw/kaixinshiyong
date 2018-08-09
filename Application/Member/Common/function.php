<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
use Common\Model\ModelModel;

function get_membernum_by_groupid($groupid = 0) {
	$groupid = (int) $groupid;
	return D('Member')->where(array('groupid' => $groupid))->count();
}
/**
 * 用户奖励添加
 *  @param  $type 奖励类型（0成长值 1积分  2 金钱）
 */
function add_member_grade($id,$num,$type){
	if($type == 0){
		$result = model('member')->where(array('userid'=>$id))->setInc('exp',$num);
	}else if($type == 1){
		$result = model('member')->where(array('userid'=>$id))->setInc('point',$num);
	}else{
		$result = model('member')->where(array('userid'=>$id))->setInc('money',$num);
	}
	return $result;
}

/*取得成为XXX商家所交的费用*/
function get_merchant_vip($modelid){
	$groups = getcache('merchant_group','member');
	$pricetype = $groups[$modelid]['pricetype'];
	$pricetype = explode(',',$pricetype);
	if($pricetype[0] == 1){
		return $pricetype[1].'/月';
	}elseif($pricetype[0] == 2){
		return $pricetype[1].'/季';
	}else{
		return $pricetype[1].'/年';
	}
}

/*取得成为XXX商家所交的费用*/
function get_member_vip($modelid){
    $groups = getcache('member_group','member');
    $pricetype = $groups[$modelid]['pricetype'];
    $pricetype = explode(',',$pricetype);
    if($pricetype[0] == 1){
        return $pricetype[1].'/月';
    }elseif($pricetype[0] == 2){
        return $pricetype[1].'/季';
    }else{
        return $pricetype[1].'/年';
    }
}

/**
 * 判断商家是否认证
 * @param $userid
 * @param $type
 */
function is_attesta($userid,$type){
	if ($userid < 1) return FALSE;
	if (empty($type)) return FALSE;
	if($type == 'phone'){
		$result = model('member')->getFieldByUserid($userid,'phone_status');
	}else if($type == 'email'){
		$result = model('member')->getFieldByUserid($userid,'email_status');
	}else if($type == 'information'){
		$member = model('member_merchant')->where(array('userid'=>$userid))->find();
		if($member['store_name'] || $member['store_logo']){
			return TRUE;
		}else{
			return FALSE;
		}
	}else{
		$infos = model('member_attesta')->where(array('userid'=>$userid,'type'=>$type))->find();
		$result = $infos['status'];
	}
	return $result;
}

/**
 * 商品统计
 * @param $status 商品状态
 * @param $userid 商家id
 * @param $mod 商品类型
 */
function product_stat($status,$userid,$mod=''){
    if($userid < 1) return false;
    $sqlMap = array();
    if(!empty($mod)){
        $sqlMap['mod'] = $mod;
    }else{
        $sqlMap['mod'] = array('in','rebate,trial');
    }
    $sqlMap['company_id'] = $userid;
    $sqlMap['status'] = $status;
    $count = D('Product/Product')->where($sqlMap)->count();
//     return D('Product/Product')->getLastSql();
    return $count;
}

/**
 * 订单统计
 * @param $status  订单状态
 * @param $mod 订单类型
 */
function order_state($userid,$status,$mod){
    if($userid < 1) return false;
    $sqlMap = array();
    $sqlMap['seller_id'] = $userid;
    $sqlMap['status'] = $status;
    $sqlMap['act_mod'] = $mod;
    $count = model('order')->where($sqlMap)->count();
    return $count;
}