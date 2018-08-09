<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Product\Library;
class hook {
	public function system_init() {
		/* 活动定时上线 */
		$sqlmap = array();
		$sqlmap['status'] = -1;
		$sqlmap['start_time'] = array("LT", NOW_TIME);
		$online_ids = model('product')->where($sqlmap)->limit(100)->order("start_time ASC")->getField('id', TRUE);
        if($online_ids) {
			foreach ($online_ids as $id) {
				$factory = new \Product\Factory\product($id);
                $factory->set_status(1, '活动正式上线');
			}
		}
        /* 活动自动下线 */
        $sqlmap = array();
        $sqlmap['status'] = 1;
        $sqlmap['end_time'] = array("LT", NOW_TIME);
        $offline_ids = model('product')->where($sqlmap)->limit(100)->order("end_time ASC")->getField('id', TRUE);
        if($offline_ids) {
			foreach ($offline_ids as $id) {
				$factory = new \Product\Factory\product($id);
                $factory->set_status(2, '活动已结束（到期自动下线）');
			}            
        }


        return FALSE;
	}

	/* 更新绑定淘宝账号信息 */
	public function get_bind_taobao(&$param) {
		$id = (int)$param['id'];
		if ($id < 1) return FALSE;
		$info = model('member_bind')->find($id);
		// 15天内不用再更新
		if ( ($info['updatetime']+86400*15) > NOW_TIME ) return FALSE;
		if (!$info) return FALSE;
		$get_info = json_decode(_dfsockopen('http://cha.yunhuasuan.net/ajax/api/?username='.$info['account'].'&_='.NOW_TIME.random(5)),TRUE);
		if ($get_info['status'] != 1) return FALSE;
		$data                   = array();
		$data['userid']         = $info['userid'];
		$data['account']        = $info['account'];
		$data['reg_time']       = $get_info['info']['regTime'];
		$data['safe_grade']     = $get_info['info']['safeType'];
		$data['is_real_name']   = $get_info['info']['utype'];
		$data['mid_comment']    = $get_info['info']['brate1'];
		$data['cha_comment']    = $get_info['info']['brate2'];
		$data['favorable_rate'] = $get_info['info']['bok_p'];
		$data['account_level']  = $get_info['info']['bLevelIco'];
		$data['bLevel']         = $get_info['info']['bLevel'];
		$data['bscore']         = $get_info['info']['bscore'];		
		$data['status']         = ($get_info['status'] == 1) ? 1 : 0 ;
		$data['updatetime'] = NOW_TIME;
		model('member_bind')->where(array('id'=>$id))->save($data);
	}
}