<?php
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
defined('IN_TPCMS') or exit('No permission resources.');
/**
 * 获取管理员信息
 * @param  int $id [管理员ID]
 * @return array
 */
function getAdminInfoById($id) {
	return model('admin')->find($id);
}