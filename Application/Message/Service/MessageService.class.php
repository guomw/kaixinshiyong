<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Message\Service;
use Think\Model;
/* 短消息数据接口 */
class MessageService extends Model 
{
	function __construct() {
		$this->db = D('Message');
		$this->message_data_db = D('MessageData');
		$this->message_group_db = D('MessageGroup');
	}
	/**
	 * 获取指定用户的新消息
	 * @param  int  $uid      用户UID
	 * @param  bool $issystem 是否系统消息
	 * @return array()
	 */
	public function getNewMessage($uid, $groupid, $issystem = TRUE) {
		$uid = (int) $uid;
		if ($uid < 1) return FALSE;
		/* 系统消息 */
		$result = $stsyem = array();
		/* 私信列表 */
		$sqlMap = array();
		$sqlMap['send_to_id'] = $uid;
		$sqlMap['status'] = 0;
		$result = (array) $this->db->where($sqlMap)->select();
		if ($issystem === TRUE) {
			$group_message_ids = $this->message_data_db->where(array('userid' => $uid))->getField('group_message_id', TRUE);
			$sqlMap = array();
			$sqlMap['groupid'] = $groupid;
			$sqlMap['status'] = 1;
			if (!empty($group_message_ids)) {
				$sqlMap['id'] = array("NOT IN", $group_message_ids);
			}
			$stsyem = (array) $this->message_group_db->where($sqlMap)->select();
			$result = ($result) ? array_merge($stsyem, $result) : $stsyem;	
		}
		return $result;
	}
}
?>