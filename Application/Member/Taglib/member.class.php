<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
class member{ 
    function __construct() {
        $this->categorys = getcache('category','commons');
        $this->models = getcache('model','commons');
    }

    public function lists($data) {
    	$sqlMap = array();
        $sqlMap['nickname'] = array('NEQ','');
    	if (isset($data['where'])) {
    		$sqlMap['_string'] = $data['where'];
    	}
    	if (isset($data['uids'])) {
    		$uids = (!is_array($data['uids'])) ? explode(",", $data['uids']) : $data['uids'];
    		$sqlMap['userid'] = array("IN", $uids);
    	}
    	if($data['modelid'] == 2){
    		$lists = model('member_merchant')->where($sqlMap)->order($data['order'])->limit($data['limit'])->select();
    	}else{
            $sqlMap['modelid'] = $data['modelid'];
    		$lists = model('member')->where($sqlMap)->order($data['order'])->limit($data['limit'])->select();
    		foreach ($lists as $k=>$v) {
    			if($v['modelid'] == 2){
    				$rs = model('member_merchant')->where(array('userid'=>$v['userid']))->find();
    				foreach($rs as $_k=>$_v){
    					$lists[$k][$_k] = $_v;
    				}
    			}
    		}
    	}
    	return $lists;
    }
	/*试用达人*/
    public function trial_repost($data) {
        $sqlMap = array();
		if (isset($data['where'])) {
    		$sqlMap['_string'] = $data['where'];
    	}
		$lists = model('report_member')->where($sqlMap)->order($data['order'])->limit($data['limit'])->select();

		return $lists;
    }
}