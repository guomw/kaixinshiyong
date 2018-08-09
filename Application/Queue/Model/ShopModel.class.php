<?php 
/**
 * @version        $Id$
 * @author         jason
 * @copyright      Copyright (c) 2007 - 2014, Adalways Technology Co., Ltd.
 * @link           http://www.dealswill.com
**/
namespace Shop\Model;
use Think\Model;
Class ShopModel extends Model
{
	protected $_auto = array (
		array('dateline', NOW_TIME, self::MODEL_BOTH, 'string'),
	);
}