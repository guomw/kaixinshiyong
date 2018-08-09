<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
class member_group
{
	public function __construct() {
		$this->db = D('Member/MemberGroup');
	}

	public function run() {
		$data = $this->db->select();
		$result = array();
		if (is_array($data)) {
			foreach ($data as $v) {
				$result[$v['groupid']] = $v;
			}
		}
		setcache('member_group', $result,'member');
		return $result;		
	}
}
?>