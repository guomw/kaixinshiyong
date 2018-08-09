<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2013, Adalways Co. Ltd.
 * @link           http://www.dealswill.com
**/
namespace Member\Model;
Class MemberSignModel extends \Think\Model {
	/*自动验证*/
	protected $_validate = array (
	);

	protected $_auto = array (
        array('dateline', NOW_TIME, self::MODEL_INSERT, 'string'),     
        array('ip', 'get_client_ip', self::MODEL_INSERT, 'function'), 
	);
}